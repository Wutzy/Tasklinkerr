<?php

namespace App\Factory;

use App\Entity\Project;
use DateTime;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;


/**
 * @extends PersistentProxyObjectFactory<Project>
 */
final class ProjectFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Project::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $currentDateTime = new DateTime();

        $starting_at = self::faker()->dateTimeBetween('-6 years', '+1 years');
        // Calculer la date limite pour la date de fin (2 ans après la date de début)
        $endLimit = (clone $starting_at)->modify('+2 years'); // Cloner la date et ajouter 2 ans
        $deadline_at = self::faker()->dateTimeBetween($starting_at, $endLimit);

        // Si la date limite du projet est passée, il est alors directement archivé
        $archived = $currentDateTime > $deadline_at ? 1 : 0;
        
        return [
            'archived' => $archived,
            'deadline_at' => $deadline_at,
            'name' => self::faker()->text(15),
            'starting_at' => $starting_at,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Project $project): void {})
        ;
    }

    // Méthode pour récupérer tous les projets
    // public static function findAllProjects(): ?Project
    // {
    //     // Crée un QueryBuilder pour l'entité Project
    //     $qb = $this->createQueryBuilder('p')
    //         ->select('p'); // Sélectionner toutes les colonnes de l'entité Project

    //     // Exécuter la requête et retourner les résultats
    //     $qb->getQuery()->getResult();

    //     $projects = $qb->getQuery()->getResult();

    //     // Si il n'y a pas de projets, on retourne null
    //     if (empty($projects)) {
    //         throw new Exception('Pas de projets');
    //     }
    //     // Sélectionner un projet aléatoire
    //     $randomIndex = array_rand($projects); // Récupère une clé aléatoire du tableau
    //     return $projects[$randomIndex];
    // }
}
