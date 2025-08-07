<?php declare(strict_types=1);

namespace Sidworks\SearchResults\Subscriber;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Search\SearchPage;
use Shopware\Storefront\Page\Search\SearchPageLoadedEvent;
use Shopware\Storefront\Page\Suggest\SuggestPage;
use Shopware\Storefront\Page\Suggest\SuggestPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

class SearchSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityRepository $searchResultsRepository,
        private readonly SystemConfigService $systemConfigService
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SuggestPageLoadedEvent::class => 'onSuggestPageLoaded',
            SearchPageLoadedEvent::class => 'onSearchPageLoaded',
        ];
    }

    public function onSuggestPageLoaded(SuggestPageLoadedEvent $event): void
    {
        if (!$this->systemConfigService->get('SidworksSearchResults.config.suggestSearchEnabled')) {
            return;
        }

        $this->trackSearch($event);
    }

    public function onSearchPageLoaded(SearchPageLoadedEvent $event): void
    {
        $this->trackSearch($event);
    }

    private function trackSearch(SuggestPageLoadedEvent|SearchPageLoadedEvent $event): void
    {
        $request = $event->getRequest();
        $term = $this->getSearchTerm($request);

        if (!$this->isValidTerm($term) || $this->hasFiltersApplied($request)) {
            return;
        }

        $context = $event->getContext();
        $salesChannelId = $event->getSalesChannelContext()->getSalesChannel()->getId();
        $resultsCount = $this->getSearchResultCount($event);

        $this->storeOrUpdateSearchResult($term, $salesChannelId, $resultsCount, $context);
    }

    private function getSearchTerm(Request $request): string
    {
        return trim(strip_tags((string) $request->query->get('search', '')));
    }

    private function isValidTerm(string $term): bool
    {
        if ($term === '') {
            return false;
        }

        $minLength = (int) ($this->systemConfigService->get('SidworksSearchResults.config.minStringLength') ?? 2);
        $maxLength = (int) ($this->systemConfigService->get('SidworksSearchResults.config.maxStringLength') ?? 255);

        return strlen($term) >= $minLength && strlen($term) <= $maxLength;
    }

    private function hasFiltersApplied(Request $request): bool
    {
        $queryParams = $request->query->all();
        return count(array_diff(array_keys($queryParams), ['search'])) > 0;
    }

    private function getSearchResultCount(SuggestPageLoadedEvent|SearchPageLoadedEvent $event): int
    {
        $productListingResult = match (true) {
            $event->getPage() instanceof SuggestPage => $event->getPage()->getSearchResult(),
            $event->getPage() instanceof SearchPage => $event->getPage()->getListing(),
            default => null,
        };

        return $productListingResult?->getTotal() ?? 0;
    }

    private function storeOrUpdateSearchResult(string $term, string $salesChannelId, int $resultsCount, Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('searchTerm', $term));
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));

        $existing = $this->searchResultsRepository->search($criteria, $context)->first();

        if ($existing) {
            $this->searchResultsRepository->update([[
                'id' => $existing->getId(),
                'timesSearched' => $existing->getTimesSearched() + 1,
                'resultsCount' => $resultsCount,
            ]], $context);
        } else {
            $this->searchResultsRepository->create([[
                'id' => Uuid::randomHex(),
                'searchTerm' => $term,
                'timesSearched' => 1,
                'resultsCount' => $resultsCount,
                'salesChannelId' => $salesChannelId,
            ]], $context);
        }
    }
}
