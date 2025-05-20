<?php declare(strict_types=1);

namespace Sidworks\SearchResults\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Storefront\Page\Search\SearchPageLoadedEvent;
use Shopware\Storefront\Page\Suggest\SuggestPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityRepository $searchResultsRepository
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

        if ($term === '' || strlen($term) > 255) {
            return;
        }

        $term = strip_tags($term);

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
