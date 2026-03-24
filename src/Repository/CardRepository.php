<?php

namespace App\Repository;

use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Card>
 *
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    public function getAllUuids(): array
    {
        $result =  $this->createQueryBuilder('c')
            ->select('c.uuid')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY)
        ;
        return array_column($result, 'uuid');
    }

    public function getDistinctSetCodes(): array
    {
        $result = $this->createQueryBuilder('c')
            ->select('c.setCode')
            ->distinct()
            ->where('c.setCode IS NOT NULL')
            ->orderBy('c.setCode', 'ASC')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);

        return array_column($result, 'setCode');
    }

    public function getAllCards(?string $setCode = null, int $page = 1, int $limit = 100): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($setCode) {
            $qb->andWhere('c.setCode = :setCode')
               ->setParameter('setCode', $setCode);
        }

        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        $totalQb = $this->createQueryBuilder('c')->select('count(c.id)');
        if ($setCode) {
            $totalQb->andWhere('c.setCode = :setCode')
                    ->setParameter('setCode', $setCode);
        }
        $total = (int) $totalQb->getQuery()->getSingleScalarResult();

        return [
            'data' => $qb->getQuery()->getResult(),
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ];
    }

    public function getCardByName(string $name, ?string $setCode = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.name LIKE :name')
            ->setParameter('name', '%' . $name . '%');

        if ($setCode) {
            $qb->andWhere('c.setCode = :setCode')
               ->setParameter('setCode', $setCode);
        }

        return $qb->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }
}
