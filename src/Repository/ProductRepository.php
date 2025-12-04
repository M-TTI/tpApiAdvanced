<?php

namespace App\Repository;

use App\Entity\Product;
use App\Model\ProductFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findAllFiltered(?ProductFilter $filter): array
    {
        $result = $this->getQb();

        if ($filter instanceof ProductFilter) {
            if (!empty($filter->getCursor())) {
                $result
                    ->andWhere('p.id >= :cursor')
                    ->setParameter('cursor', $filter->getCursor())
                ;
            }

            if (!empty($filter->getLimit())) {
                $result->setMaxResults($filter->getLimit());
            }

            if (!empty($filter->getSort())) {
                foreach ($filter->getSort() as $field => $direction) {
                    if (!in_array($field, ProductFilter::SORTABLE_FIELDS)) {
                        throw new \InvalidArgumentException('Invalid sort field: ' . $field);
                    }

                    $result->addOrderBy('p.' . $field, $direction);
                }
            }

            if (!empty($filter->getFilters())) {
                foreach ($filter->getFilters() as $field => $value) {
                    switch ($field) {
                        case 'category':
                            $result
                                ->andWhere('c.label = :category')
                                ->setParameter('category', $value)
                            ;
                            break;
                        case 'price-lte':
                            $result
                                ->andWhere('p.price <= :price')
                                ->setParameter('price', $value)
                            ;
                            break;
                        case 'price-gte':
                            $result
                                ->andWhere('p.price >= :price')
                                ->setParameter('price', $value)
                            ;
                            break;
                        case 'price-lt':
                            $result
                                ->andWhere('p.price < :price')
                                ->setParameter('price', $value)
                            ;
                            break;
                        case 'price-gt':
                            $result
                                ->andWhere('p.price > :price')
                                ->setParameter('price', $value)
                            ;
                            break;
                    }
                }
            }
        }

        return $result
            ->getQuery()
            ->getResult()
        ;
    }

    private function getQb(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->addSelect('c')
            ->leftJoin('p.category', 'c')
        ;
    }
}
