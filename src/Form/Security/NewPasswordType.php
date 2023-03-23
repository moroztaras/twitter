<?php

namespace App\Form\Security;

use App\Form\Security\Models\NewPasswordModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => [
                    'label' => 'password',
                    'label_attr' => [
                        'class' => 'hide',
                    ],
                    'attr' => ['placeholder' => 'password'],
                ],
                'second_options' => [
                    'label' => 'repeat_password',
                    'label_attr' => [
                        'class' => 'hide',
                    ],
                    'attr' => ['placeholder' => 'repeat_password'],
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'change_password',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NewPasswordModel::class,
        ]);
    }
}
