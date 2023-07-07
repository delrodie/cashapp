<?php

namespace App\Form\Archive;

use App\Entity\Archive\Domaine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomaineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('libelle')
            ->add('statut')
            ->add('slug')
            ->add('publiePar')
            ->add('modifiePar')
            ->add('publieLe')
            ->add('modifieLe')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Domaine::class,
        ]);
    }
}
