<?php

namespace App\Factory;

use App\Entity\Slot;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use App\Repository\TaskRepository;
use App\Repository\EmployeeRepository;
use DateTime;

/**
 * @extends PersistentProxyObjectFactory<Slot>
 */
final class SlotFactory extends PersistentProxyObjectFactory
{
    private $taskRepository;
    private $employeeRepository;

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(TaskRepository $taskRepository, EmployeeRepository $employeeRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public static function class(): string
    {
        return Slot::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $randomTask = $this->taskRepository->getRandomTask();
        $relatedProject = $randomTask->getProject();

        // là il faut que la datte de débutt soit plus récente que le début du projet auquel est la tâche est liée
        $starting_at = self::faker()->dateTimeBetween($relatedProject->getStartingAt(), $randomTask->getDeadlineAt());

        $ending_at = self::faker()->dateTimeBetween($starting_at, $randomTask->getDeadlineAt());

        $randomEmployee = $this->employeeRepository->getRandomEmployee($starting_at);

        return [
            'employee' => $randomEmployee,
            'ending_at' => $ending_at,
            'starting_at' => $starting_at,
            'task' => $randomTask,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Slot $slot): void {})
        ;
    }
}
