<?php

namespace App\Model;

class ProductFilter
{
    public const SORTABLE_FIELDS = ['id', 'label', 'price', 'stock', 'createdAt'];
    public const PROJECTABLE_FIELDS = ['id', 'label', 'price', 'stock', 'createdAt'];
    public const FILTER_LABELS = ['category', 'price-lte', 'price-gte', 'price-lt', 'price-gt'];
    private ?int $cursor = null;
    private ?int $limit = null;
    private array $sort = [];
    private array $filters = [];
    private array $projectedFields = [];
    private bool $categoryRelation = false;

    public function getCursor(): ?int
    {
        return $this->cursor;
    }

    public function setCursor(?int $cursor): self
    {
        $this->cursor = $cursor;

        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getSort(): array
    {
        return $this->sort;
    }

    public function setSort(array $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function getProjectedFields(): array
    {
        return $this->projectedFields;
    }

    public function setProjectedFields(array $projectedFields): self
    {
        $this->projectedFields = $projectedFields;

        return $this;
    }

    public function getCategoryRelation(): bool
    {
        return $this->categoryRelation;
    }

    public function setCategoryRelation(bool $categoryRelation): self
    {
        $this->categoryRelation = $categoryRelation;

        return $this;
    }
}
