<?php

namespace App\Form\Main;

use App\Entity\Main\Categorie;
use App\Entity\Main\Domaine;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('code')
            ->add('libelle', TextType::class,['attr'=>['class'=>'form-control', 'autocomplete'=>"off"]])
//            ->add('slug')
            ->add('domaine', EntityType::class,[
                'class'=> Domaine::class,
                'choice_label' => 'libelle',
                'attr' => ['class'=>'form-select']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
