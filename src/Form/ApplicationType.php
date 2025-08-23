<?php
// src/Form/ApplicationType.php
namespace App\Form;

use App\Entity\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomApplication', TextType::class, [
                'label' => 'Nom de l\'application',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('scriptPath', TextType::class, [
                'label' => 'Nom du script (.ps1)',
            ])
            ->add('commandeExecution', TextType::class, [
                'label' => 'Commande à exécuter',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Application::class,
        ]);
    }
}
