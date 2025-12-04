<?php

namespace App\Repository;

use App\Entity\RefreshToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RefreshToken>
 */
class RefreshTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    public function save(RefreshToken $refreshToken): RefreshToken
    {
        $em = $this->getEntityManager();
        $em->persist($refreshToken);
        $em->flush();

        return $refreshToken;
    }

    public function delete(RefreshToken $refreshToken): RefreshToken
    {
        $em = $this->getEntityManager();
        $em->remove($refreshToken);
        $em->flush();

        return $refreshToken;
    }

    public function findOneByToken(string $token): ?RefreshToken
    {
        return $this->getQb()
            ->andWhere('t.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByJti(string $jti): ?RefreshToken
    {
        return $this->getQb()
            ->andWhere('t.jti = :jti')
            ->setParameter('jti', $jti)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function deleteExpired(): int
    {
        return $this->getQb()
            ->delete()
            ->andWhere('t.expiresAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute()
        ;
    }

    public function deleteAllForUser(User $user): int
    {
        return $this->getQb()
            ->delete()
            ->andWhere('t.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute()
        ;
    }

    private function getQb(): QueryBuilder
    {
        return $this->createQueryBuilder('t');
    }
}
