<?php declare(strict_types=1);

namespace Sidworks\SearchResults\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1747932130AddResultsCount extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1747932130;
    }


    public function update(Connection $connection): void
    {
        if ($this->columnExists($connection, 'sidworks_search_results', 'results_count')) {
            return;
        }

        $connection->executeStatement("
            ALTER TABLE `sidworks_search_results`
                ADD `results_count` INT NOT NULL DEFAULT 0
        ");
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
