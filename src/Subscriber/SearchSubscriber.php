<?php declare(strict_types=1);

namespace Sidworks\SearchResults\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Storefront\Page\Search\SearchPageLoadedEvent;
use Shopware\Storefront\Page\Suggest\SuggestPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class SearchSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityRepository $searchResultsRepository,
        private readonly SystemConfigService $systemConfigService
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SuggestPageLoadedEvent::class => 'trackSearch',
            SearchPageLoadedEvent::class => 'trackSearch'
        ];
    }

    public function trackSearch($event): void
    {
        $request = $event->getRequest();
        $term = trim((string) $request->query->get('search', ''));

        $term = strip_tags($term);

        $minLength = (int) $this->systemConfigService->get('SidworksSearchResults.config.minStringLength') ?? 2;
        $maxLength = (int) $this->systemConfigService->get('SidworksSearchResults.config.maxStringLength') ?? 255;

        if ($term === '' || strlen($term) < $minLength || strlen($term) > $maxLength) {
            return;
        }

        $context = $event->getContext();
        $salesChannelId = $event->getSalesChannelContext()->getSalesChannel()->getId();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('searchTerm', $term));
        $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));

        $existing = $this->searchResultsRepository->search($criteria, $context)->first();

        if ($existing) {
            $this->searchResultsRepository->update([[
                'id' => $existing->getId(),
                'timesSearched' => $existing->getTimesSearched() + 1,
            ]], $context);
        } else {
            $this->searchResultsRepository->create([[
                'id' => Uuid::randomHex(),
                'searchTerm' => $term,
                'timesSearched' => 1,
                'salesChannelId' => $salesChannelId,
            ]], $context);
        }
    }
}
