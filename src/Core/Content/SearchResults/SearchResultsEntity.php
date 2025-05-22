<?php declare(strict_types=1);

namespace Sidworks\SearchResults\Core\Content\SearchResults;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class SearchResultsEntity extends Entity
{
    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }

    public function setSearchTerm(string $searchTerm): void
    {
        $this->searchTerm = $searchTerm;
    }

    public function getTimesSearched(): int
    {
        return $this->timesSearched;
    }

    public function setTimesSearched(int $timesSearched): void
    {
        $this->timesSearched = $timesSearched;
    }

    public function getResultsCount(): int
    {
        return $this->resultsCount;
    }

    public function setResultsCount(int $resultsCount): void
    {
        $this->resultsCount = $resultsCount;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
