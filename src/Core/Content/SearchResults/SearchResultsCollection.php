<?php declare(strict_types=1);

namespace Sidworks\SearchResults\Core\Content\SearchResults;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class SearchResultsCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return SearchResultsEntity::class;
    }
}
