<?php

namespace App\Form;

use App\Entity\Employee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Prenom',
            ])
            ->add('email', TextType::class, [
                'label' => 'Email'
            ])
            ->add('arrival_at', DateType::class, [
                'input' => 'datetime',
                'widget' => 'single_text',
                'label' => 'Date d\'entrée',
            ])
            ->add('contract', TextType::class, [
                'label' => 'Statut'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
