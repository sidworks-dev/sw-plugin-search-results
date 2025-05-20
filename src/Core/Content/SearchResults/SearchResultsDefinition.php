<?php declare(strict_types=1);

namespace Sidworks\SearchResults\Core\Content\SearchResults;

use Shopware\Core\System\SalesChannel\SalesChannelDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class SearchResultsDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'sidworks_search_results';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return SearchResultsEntity::class;
    }

    public function getCollectionClass(): string
    {
        return SearchResultsCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('search_term', 'searchTerm'))->addFlags(new Required(), new ApiAware()),
            (new IntField('times_searched', 'timesSearched'))->addFlags(new Required(), new ApiAware()),

            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class))->addFlags(new Required(), new ApiAware()),

            new ManyToOneAssociationField('salesChannel', 'sales_channel_id', SalesChannelDefinition::class, 'id', false),

            (new DateTimeField('created_at', 'createdAt'))->addFlags(new Required()),
            new DateTimeField('updated_at', 'updatedAt'),
        ]);
    }
}
