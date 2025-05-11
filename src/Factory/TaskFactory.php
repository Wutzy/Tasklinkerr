<?php

namespace App\Factory;

use App\Entity\Task;
use App\Repository\ProjectRepository;
use App\Repository\StatusRepository;
use App\Repository\EmployeeRepository;
use DateTime;
use DateTimeImmutable;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Symfony\Component\Config\Definition\Exception\Exception;


/**
 * @extends PersistentProxyObjectFactory<Task>
 */
final class TaskFactory extends PersistentProxyObjectFactory
{
    private $projectRepository;
    private $statusRepository;
    private $employeeRepository;

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(ProjectRepository $projectRepository, StatusRepository $statusRepository, EmployeeRepository $employeeRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->statusRepository = $statusRepository;
        $this->employeeRepository = $employeeRepository;
    }

    public static function class(): string
    {
        return Task::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $randomProject = $this->projectRepository->getRandomProject();

        // date de début du projet selecitonné, la tâche doit commencer après
        $project_starting_at = $randomProject->getStartingAt();
        //$project_starting_at = new DateTime($randomProject->getStartingAt()->format('Y-m-d'));

        // date de fin du projet selectionné, la tâche doit finir avant
        $project_deadline_at = $randomProject->getDeadlineAt();
        //$project_deadline_at = new DateTime($randomProject->getDeadlineAt()->format('Y-m-d'));

        $deadline_at = self::faker()->dateTimeBetween($project_starting_at, $project_deadline_at);
        //throw new Exception('date début : ' . $project_starting_at->format('Y-m-d')  . 'datet fin: ' . $project_deadline_at->format('Y-m-d') . 'date fin de ttache: ' . $deadline_at->format('Y-m-d'));
        //('-' . $interval->days . ' days', '+1 years')

        // Vérifier si la date générée est dans l'intervalle souhaité
        if ($deadline_at < $project_starting_at || $deadline_at > $project_deadline_at) {
            throw new Exception('La date générée n\'est pas dans l\'intervalle prévu.');
        }

        return [
            'deadline_at' => $deadline_at,
            'description' => self::faker()->text(255),
            'employee' => $this->employeeRepository->getRandomEmployee($project_starting_at),
            // la date de fin de tâche doit être après la date de début du projet correspondant
            'project' => $randomProject,
            'status' => $this->statusRepository->getRandomStatus(),
            'title' => self::faker()->text(15),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Task $task): void {})
        ;
    }
}
