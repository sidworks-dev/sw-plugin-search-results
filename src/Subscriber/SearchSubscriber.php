<?php declare(strict_types=1);

namespace Sidworks\SearchResults\Subscriber;

use Shopware\Storefront\Page\Search\SearchPageLoadedEvent;
use Shopware\Storefront\Page\Suggest\SuggestPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SuggestPageLoadedEvent::class => 'onSearchSuggestPageLoaded',
            SearchPageLoadedEvent::class => 'onSearchPageLoaded'
        ];
    }

    public function onSearchSuggestPageLoaded(SuggestPageLoadedEvent $event): void
    {
        die('onSearchSuggestPageLoaded');
    }

    public function onSearchPageLoaded(SearchPageLoadedEvent $event): void
    {
        die('onSearchPageLoaded');
    }
}
