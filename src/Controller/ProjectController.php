<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProjectRepository;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Project;
use App\Entity\Status;

class ProjectController extends AbstractController
{
    #[Route('/project/detail/{id}', name: 'app_project')]
    public function detail(ProjectRepository $projectRepository, $id): Response
    {
        $project = $projectRepository->find($id);
        
        // Vérification si le projet existe, sinon tu peux rediriger ou afficher une erreur
        if (!$project) {
            throw $this->createNotFoundException('Le projet n\'a pas été trouvé');
        }

        //Récuperer les utilisateurs liés au projet
        $employees = $project->getEmployees()->toArray();

        //Recuperer les tâches liées au projet
        $tasks = $project->getTasks()->toArray();

        // Trier les tâches en fonction de leur status:
        $tasksByStatus = [];

        foreach ($tasks as $task) {
            $status = $task->getStatus();
            
            //Si le status n'a pas encore été ajouté au tableau, on l'ajoute
            if (!isset($tasksByStatus[$status->getLibelle()])) {
                $tasksByStatus[$status->getLibelle()] = [];
            }
            // On ajoute la tâche dans le tableau correspondant au statut
            $tasksByStatus[$status->getLibelle()][] = $task;
        }
        //dd($tasksByStatus);
        return $this->render('project/detail.html.twig', [
            'project' => $project,
            'tasksByStatus' => $tasksByStatus,
            'employees' => $employees,
            //'deadline_at' => $deadline_at,

        ]);
    }

    #[Route('/project/edit/{id}', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(ProjectRepository $repository, $id, Request $request, EntityManagerInterface $manager): Response
    {
        $project = $repository->find($id);
        
        // Vérification si le projet existe, sinon tu peux rediriger ou afficher une erreur
        if (!$project) {
            throw $this->createNotFoundException('Le projet n\'a pas été trouvéé');
        }

        // Créer le formulaire
        $form = $this->createForm(ProjectType::class, $project);

        // Traiter le formulaire 
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            try {
                // Edition du projet
                $manager->persist($project);
                $manager->flush();
        
                // Si la suppression réussit, un message de succès est ajouté
                $this->addFlash('success', 'Les informations ont été mis à jour avec succès.');
            } catch (\Exception $e) {
                // En cas d'erreur, capturer le message de l'exception
                $errorMessage = $e->getMessage();  // Récupère le message d'erreur spécifique
        
                // Ajouter un message flash avec le message d'erreur
                $this->addFlash('error', 'Une erreur est survenue : ' . $errorMessage);
            }

            return $this->redirectToRoute('app_project_edit', ['id' => $id]);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/project/add', name: 'app_project_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $manager)
    {
        $project = new Project();
        $now = new \DateTime();

        $project->setStartingAt($now);
        $project->setDeadlineAt((clone $now)->modify('+3 months'));

        $project->setArchived(false);

        // Créer le formulaire
        $form = $this->createForm(ProjectType::class, $project);

        // Traiter le formulaire 
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()){
            try {
                // Edition de la tâche
                $manager->persist($project);
                $manager->flush();
        
                // Créer un statut "Todo"
                $statusTodo = new Status();
                $statusTodo->setLibelle('Todo');
                $statusTodo->setProject($project);  // Associer le statut au projet

                // Persister le statut
                $manager->persist($statusTodo);
                $manager->flush();  // Sauvegarder le statut "Todo" en base de données

                // Si l'edition réussit, un message de succès est ajouté
                $this->addFlash('success', 'Le projet "' . $project->getName() . '" a été créé avec succès.');
            } catch (\Exception $e) {
                // En cas d'erreur, capturer le message de l'exception
                $errorMessage = $e->getMessage();  // Récupère le message d'erreur spécifique
        
                // Ajouter un message flash avec le message d'erreur
                $this->addFlash('error', 'Une erreur est survenue : ' . $errorMessage);
            }

            return $this->redirectToRoute('app_home');
        }

        return $this->render('project/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/project/archive/{id}', name: 'app_project_archive', methods: ['GET'])]
    public function archive(Project $project, EntityManagerInterface $manager): Response
    {


        try {
            // Tentative d'archivage de la tâche
            $project->setArchived(true);
            $manager->flush();
    
            // Si la suppression réussit, un message de succès est ajouté
            $this->addFlash('success', 'Le projet "' . $project->getName() . '" a été archivé avec succès.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            // En cas d'erreur (échec de la suppression)
            $this->addFlash('error', 'Une erreur est survenue : ' . $errorMessage);
        }

        return $this->redirectToRoute('app_home');
    }
}
