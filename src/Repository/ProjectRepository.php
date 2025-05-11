<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }


    //    /**
    //     * @return Project[] Returns an array of Project objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Project
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getAllProjects() {

        $allProjects = $this->findAll();

        // Si il n'y a pas de projets, on retourne null
        if (empty($allProjects)) {
            throw new Exception('Pas de projets');
        }

        return $allProjects;
    }

    /**
     * Récupérer un projet aléatoire parmi ceux présents dans la base de données.
     *
     * @return Project|null
     */
    public function getRandomProject(): ?Project
    {
        $projects = $this->findAll();

        // Si il n'y a pas de projets, on retourne null
        if (empty($projects)) {
            throw new Exception('Pas de projets');
        }

        // Sélectionner un projet aléatoire
        $randomIndex = array_rand($projects); // Récupère une clé aléatoire du tableau
        return $projects[$randomIndex];
    }


}
