<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TaskRepository;
use App\Repository\ProjectRepository;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Task;


class TaskController extends AbstractController
{
    #[Route('/task/edit/{id}', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(TaskRepository $taskRepository, $id, Request $request, EntityManagerInterface $manager): Response
    {
        $task = $taskRepository->find($id);
        $statuses = $task->getProject()->getStatuses();
        $employees = $task->getProject()->getEmployees();
        
        // Vérification si l'employé existe
        if (!$task) {
            throw $this->createNotFoundException('La tâche n\'a pas été trouvé');
        }

        // Créer le formulaire
        $form = $this->createForm(TaskType::class, $task, [
            'project' => $task->getProject()
        ]);

        // Traiter le formulaire 
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            try {
                // Edition de la tâche
                $manager->persist($task);
                $manager->flush();
        
                // Si la suppression réussit, un message de succès est ajouté
                $this->addFlash('success', 'Les informations ont été mis à jour avec succès.');
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $this->addFlash('error', 'Une erreur est survenue : ' . $errorMessage);
            }

            return $this->redirectToRoute('app_task_edit', ['id' => $id]);
        }
        return $this->render('task/edit.html.twig', [
            'form' => $form,
            'task' => $task,
            'statuses' => $statuses,
            'employees' => $employees
        ]);
    }

    #[Route('project/{id}/task/add', name: 'app_task_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $manager, ProjectRepository $projectRepository, $id)
    {
        $task = new Task();
        $project = $projectRepository->find($id);

        $task->setProject($project);
        // Créer le formulaire
        $form = $this->createForm(TaskType::class, $task, [
            'project' => $project,
        ]);

        // Traiter le formulaire 
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            try {
                // Edition de la tâche
                $manager->persist($task);
                $manager->flush();
        
                // Si la suppression réussit, un message de succès est ajouté
                $this->addFlash('success', 'La tâche a été créé avec succès.');
            } catch (\Exception $e) {
                // En cas d'erreur, capturer le message de l'exception
                $errorMessage = $e->getMessage();  // Récupère le message d'erreur spécifique
        
                // Ajouter un message flash avec le message d'erreur
                $this->addFlash('error', 'Une erreur est survenue : ' . $errorMessage);
            }


            return $this->redirectToRoute('app_project', ['id' => $id]);
        }

        return $this->render('task/add.html.twig', [
            //'starting_at' => $starting_at,
            //'deadline_at' => $deadline_at,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/task/delete/{id}', name: 'app_task_delete', methods: ['GET', 'POST'])]
    public function delete(Task $task, EntityManagerInterface $manager): Response 
    {
            $project= $task->getProject();

            try {
                // Tentative de suppression de la tâche
                $manager->remove($task);
                $manager->flush();
        
                // Si la suppression réussit, un message de succès est ajouté
                $this->addFlash('success', 'La tâche "' . $task->getTitle() . '" a été supprimée avec succès.');
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                // En cas d'erreur (échec de la suppression)
                $this->addFlash('error', 'Une erreur est survenue : ' . $errorMessage);
            }

        return $this->redirectToRoute('app_project', [
            'id' => $project->getId()
        ]);
    }
}
