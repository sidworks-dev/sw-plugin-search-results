<?php declare(strict_types=1);

namespace Sidworks\SearchResults;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class SidworksSearchResults extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);

        $connection->executeStatement("
        CREATE TABLE IF NOT EXISTS `sidworks_search_results` (
            `id` BINARY(16) NOT NULL,
            `search_term` VARCHAR(255) NOT NULL,
            `sales_channel_id` BINARY(16) NOT NULL,
            `times_searched` INT NOT NULL DEFAULT 1,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3),
            PRIMARY KEY (`id`),
            UNIQUE KEY `uniq_search_term_sales_channel` (`search_term`, `sales_channel_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if (!$uninstallContext->keepUserData()) {
            /** @var Connection $connection */
            $connection = $this->container->get(Connection::class);
            $connection->executeStatement("DROP TABLE IF EXISTS `sidworks_search_results`");
        }

        parent::uninstall($uninstallContext);
    }
}
