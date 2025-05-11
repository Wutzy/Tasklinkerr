<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;


class EmployeeController extends AbstractController
{
    #[Route('/employee', name: 'app_employee')]
    public function index(EmployeeRepository $repository): Response
    {
        $allEmployees = $repository->findAll();
        
        return $this->render('employee/index.html.twig', [
            'all_employees' => $allEmployees,
        ]);
    }    
    
    #[Route('/employee/edit/{id}', name: 'app_employee_edit')]
    public function edit(EmployeeRepository $repository, $id, Request $request, EntityManagerInterface $manager): Response
    {
        $employee = $repository->find($id);

                // $employees = $this->findBy([
        //     'active' => 1,
        //     'arrival_at' => ['> ' . $dateCompare->format('Y-m-d H:i:s')]
        // ]);
        
            // Vérification si l'employé existe, sinon tu peux rediriger ou afficher une erreur
        if (!$employee) {
            throw $this->createNotFoundException('L\'employé n\'a pas été trouvé');
        }

        $arrival_at = $employee->getArrivalAt()->format('m/d/Y');

        // Créer le formulaire
        $form = $this->createForm(EmployeeType::class, $employee);

        // Traiter le formulaire 
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            try {
                // Tentative de suppression de la tâche
                $manager->persist($employee);
                $manager->flush();
        
                // Si la suppression réussit, un message de succès est ajouté
                $this->addFlash('success', 'Les informations ont été mis à jour avec succès.');
            } catch (\Exception $e) {
                // En cas d'erreur, capturer le message de l'exception
                $errorMessage = $e->getMessage();  // Récupère le message d'erreur spécifique
        
                // Ajouter un message flash avec le message d'erreur
                $this->addFlash('error', 'Une erreur est survenue : ' . $errorMessage);
            }

            return $this->redirectToRoute('app_employee_edit', ['id' => $id]);
        }

        return $this->render('employee/edit.html.twig', [
            'form' => $form,
            'employee' => $employee,
            'arrival_at' => $arrival_at,
        ]);
    }

    #[Route('/employee/delete/{id}', name: 'app_employee_delete', methods: ['GET', 'POST'])]
    public function delete(Employee $employee, EntityManagerInterface $manager): Response 
    {
        try {
            // Tentative de suppression de l'employé
            $manager->remove($employee);
            $manager->flush();
    
            // Si la suppression réussit, un message de succès est ajouté
            $this->addFlash('success', 'L\' employée ' . $employee->getFirstName() .  ' ' . $employee->getLastName() . ' a été supprimé avec succès.');
        } catch (\Exception $e) {
            // En cas d'erreur, capturer le message de l'exception
            $errorMessage = $e->getMessage();  // Récupère le message d'erreur spécifique
    
            // Ajouter un message flash avec le message d'erreur
            $this->addFlash('error', 'Une erreur est survenue : ' . $errorMessage);
        }

        return $this->redirectToRoute('app_employee');
    }

}
