<?php declare(strict_types=1);

namespace Sidworks\SearchResults\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
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
        $term = trim((string)$request->query->get('search', ''));

        if ($term === '' || strlen($term) > 255) {
            return;
        }

        // Simple sanitization
        $term = strip_tags($term);

        $context = Context::createDefaultContext();

        // Try updating existing record
        $criteria = new \Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria();
        $criteria->addFilter(new \Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter('searchTerm', $term));
        $existing = $this->searchResultsRepository->search($criteria, $context)->first();

        if ($existing) {
            $this->searchResultsRepository->update([[
                'id' => $existing->getId(),
                'timesSearched' => $existing->getTimesSearched() + 1,
            ]], $context);
        } else {
            $this->searchResultsRepository->create([[
                'id' => \Shopware\Core\Framework\Uuid\Uuid::randomHex(),
                'searchTerm' => $term,
                'timesSearched' => 1,
            ]], $context);
        }
    }
}
