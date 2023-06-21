<?php

namespace App\Form\Main;

use App\Entity\Main\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class,['attr'=>['class'=>'form-control', 'readonly'=>true]])
            ->add('contact', TextType::class,['attr'=>['class'=>'form-control', 'readonly'=>true]])
            ->add('identite', TextType::class,['attr'=>['class' => 'form-control', 'autocomplete'=>"off"]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
