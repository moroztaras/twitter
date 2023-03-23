<?php

namespace App\Form\Security;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'first_name',
                'label_attr' => [
                    'class' => 'uk-hidden',
                ],
                'compound' => false,
                'attr' => [
                    'placeholder' => 'first_name',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'last_name',
                'label_attr' => [
                    'class' => 'uk-hidden',
                ],
                'compound' => false,
                'attr' => [
                    'placeholder' => 'last_name',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'email',
                'label_attr' => [
                    'class' => 'uk-hidden',
                ],
                'attr' => ['placeholder' => 'email'],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'password_must_match',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options' => [
                    'label' => 'password',
                    'label_attr' => [
                        'class' => 'uk-hidden uk-form-label',
                    ],
                    'attr' => ['placeholder' => 'password'],
                ],
                'second_options' => [
                    'label' => 'repeat_password',
                    'label_attr' => [
                        'class' => 'uk-hidden uk-form-label',
                    ],
                    'attr' => ['placeholder' => 'repeat_password'],
                ],
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'gender',
                'choices' => [
                    'male' => 'male',
                    'female' => 'female',
                ],
                'attr' => [
                    'class' => 'uk-flex uk-flex-middle kuk-child-margin-small-left',
                ],
                'label_attr' => [
                    'class' => 'uk-hidden',
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('birthday', BirthdayType::class, [
                'label' => 'birthday',
                'label_attr' => [
                    'class' => 'uk-hidden',
                ],
                'attr' => [
                    'class' => 'uk-flex kuk-child-margin-small-left',
                ],
            ])
            ->add('country', CountryType::class, [
                'label' => 'country',
                'label_attr' => [
                    'class' => 'uk-hidden',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'sign_up',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
