<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Status;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la tâche',
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('deadline_at', DateType::class, [
                'input' => 'datetime',
                'widget' => 'single_text',
                'label' => 'Date',
                'required' => false
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'libelle',
                'required' => true,
                'label' => 'Statut',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('s')
                        ->where('s.project = :project')
                        ->setParameter('project', $options['project']);
                },
            ])
            ->add('employee', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => function (Employee $employee) {
                    return $employee->getFirstName() . ' ' . $employee->getLastName();
                },
                'label' => 'Membre',
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('e')
                        ->join('e.projects', 'p')
                        ->where('p = :project')
                        ->setParameter('project', $options['project']);
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'project' => null,
        ]);
    }
}
