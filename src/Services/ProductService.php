<?php

namespace App\Services;

use App\Entity\Product;
use App\Model\ProductFilter;

class ProductService
{
    public function transformParamArrayToObject(array $params): ?ProductFilter
    {
        if (empty($params)) {
            return null;
        }

        $productFilter = new ProductFilter();

        $cursor = $params['cursor'] ?? null;
        $limit = $params['limit'] ?? null;
        $sort = $params['sort'] ?? null;
        $filters = [];
        $fields = $params['fields'] ?? null;
        $relations = $params['include'] ?? null;

        foreach (array_keys($params) as $key) {
            if (in_array($key, ProductFilter::FILTER_LABELS)) {
                $filters[$key] = $params[$key];
            }
        }

        if (!empty($cursor)) {
            if (is_numeric($cursor)) {
                $productFilter->setCursor($cursor);
            } else {
                throw new \InvalidArgumentException("Cursor param must be a number");
            }
        }

        if (!empty($limit)) {
            if (is_numeric($limit)) {
                $productFilter->setLimit($limit);
            } else {
                throw new \InvalidArgumentException("Limit param must be a number");
            }
        }

        if (!empty($sort)) {
            $sort = explode(',', trim($sort));
            $sortParams = [];
            foreach ($sort as $sortItem) {
                if (str_starts_with($sortItem, '-')) {
                    $sortParams[substr($sortItem, 1)] = 'DESC';
                } else {
                    $sortParams[$sortItem] = 'ASC';
                }
            }

            $productFilter->setSort($sortParams);
        }

        if (!empty($filters)) {
            $productFilter->setFilters($filters);
        }

        if (!empty($fields)) {
            $fields = explode(',', strtolower(trim($fields)));
            foreach ($fields as $field) {
                if (!in_array($field, ProductFilter::PROJECTABLE_FIELDS)) {
                    throw new \InvalidArgumentException("This field is invalid");
                }
            }

            $productFilter->setProjectedFields($fields);
        }

        if (!empty($relations)) {
            $relations = explode(',', strtolower(trim($relations)));
            if (!in_array('category', $relations)) {
                throw new \InvalidArgumentException("This relation is invalid");
            }

            $productFilter->setCategoryRelation(in_array('category', $relations));
        }

        return $productFilter;
    }

    public function transformProductToArray(Product $product, ?ProductFilter $productFilter = null): array
    {
        $productArray = [];

        if (empty($productFilter)) {
            $productArray = [
                'id' => $product->getId(),
                'label' => $product->getLabel(),
                'price' => $product->getPrice(),
                'stock' => $product->getStock(),
                'createdAt' => $product->getCreatedAt(),
            ];
        } else {
            $fields = $productFilter->getProjectedFields();
            if (in_array('id', $fields)) {
                $productArray['id'] = $product->getId();
            }
            if (in_array('label', $fields)) {
                $productArray['label'] = $product->getLabel();
            }
            if (in_array('price', $fields)) {
                $productArray['price'] = $product->getPrice();
            }
            if (in_array('stock', $fields)) {
                $productArray['stock'] = $product->getStock();
            }
            if (in_array('createdAt', $fields)) {
                $productArray['createdAt'] = $product->getCreatedAt();
            }
            if ($productFilter->getCategoryRelation()) {
                $productArray['category'] = [
                    'label' => $product->getCategory()->getLabel(),
                    'description' => $product->getCategory()->getDescription(),
                ];
            }
        }

        return $productArray;
    }

    public function transformProductsToArray(array $products, ?ProductFilter $productFilter = null): array
    {
        return array_map(function (Product $product) use ($productFilter) {
            return $this->transformProductToArray($product, $productFilter);
        }, array_values($products));
    }
}
