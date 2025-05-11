<?php

namespace App\Factory;

use App\Entity\Status;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use App\Repository\ProjectRepository;

/**
 * @extends PersistentProxyObjectFactory<Status>
 */
final class StatusFactory extends PersistentProxyObjectFactory
{
    private $projectRepository;

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public static function class(): string
    {
        return Status::class;
    }


    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'libelle' => self::faker()->text(15),
            'project' => $this->projectRepository->getRandomProject()
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Status $status): void {})
        ;
    }
}
