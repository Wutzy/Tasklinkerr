<?php

namespace App\Repository;

use App\Entity\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Status>
 */
class StatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Status::class);
    }

    //    /**
    //     * @return Status[] Returns an array of Status objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Status
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Récupérer un statut aléatoire parmi ceux présents dans la base de données.
     *
     * @return Status|null
     */
    public function getRandomStatus(): ?Status
    {
        $status = $this->findAll();

        // Si il n'y a pas de projets, on retourne null
        if (empty($status)) {
            throw new Exception('Pas de status');
        }

        // Sélectionner un statut aléatoire
        $randomIndex = array_rand($status); // Récupère une clé aléatoire du tableau
        return $status[$randomIndex];
    }
}
