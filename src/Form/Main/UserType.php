<?php

namespace App\Form\Main;

use App\Entity\Main\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => ['class' => 'form-control', 'autocomplete'=>"off"],
                'label' => 'Nom utilisateur'
            ])
            ->add('roles', ChoiceType::class,[
                'choices'=>[
                    'Administrateur' => 'ROLE_ADMIN',
                    'Gerant' => 'ROLE_GERANT',
                    'Caisse' => 'ROLE_CAISSE',
//                    'Utilisateur' => 'ROLE_USER'
                ],
                'multiple'=> true,
                'expanded'=>true,
            ])
            ->add('password', PasswordType::class,[
                'attr'=>['class'=>'form-control'],
                'label' => "Mot de passe"
            ])
//            ->add('connexion')
//            ->add('lastConnectedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
