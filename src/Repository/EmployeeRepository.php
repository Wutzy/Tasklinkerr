<?php

namespace App\Repository;

use App\Entity\Employee;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;


/**
 * @extends ServiceEntityRepository<Employee>
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    //    /**
    //     * @return Employee[] Returns an array of Employee objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Employee
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Récupérer un employé aléatoire arrivé avant dateCompare parmi ceux présents dans la base de données.
     * 
     *
     * @return Employee|null
     */
    public function getRandomEmployee(?\DateTimeInterface $dateCompare): ?Employee
    {   
        if(!$dateCompare) {
            $currentDateTime = new DateTime();
            $dateCompare = $currentDateTime;
        }
        
        $qb = $this->createQueryBuilder('e')
        ->where('e.active = :active')
        ->andWhere('e.arrival_at < :dateCompare')
        ->setParameter('active', 1)
        ->setParameter('dateCompare', $dateCompare->format('Y-m-d H:i:s'))
        ->getQuery();

        $employees = $qb->getResult();
        // $employees = $this->findBy([
        //     'active' => 1,
        //     'arrival_at' => ['> ' . $dateCompare->format('Y-m-d H:i:s')]
        // ]);

        // Si il n'y a pas d'employé, on retourne null
        if (empty($employees)) {
            throw new Exception('Pas d\'employés');
        }

        // Sélectionner un employé aléatoire
        $randomIndex = array_rand($employees); // Récupère une clé aléatoire du tableau
        return $employees[$randomIndex];
    }

}
