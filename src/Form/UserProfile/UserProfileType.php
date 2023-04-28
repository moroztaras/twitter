<?php

namespace App\Form\UserProfile;

use App\Form\UserProfile\Model\UserProfileModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'first_name',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'last_name',
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'gender',
                'choices' => [
                   'male' => 'm',
                  'female' => 'w',
                ],
                'attr' => [
                    'class' => 'uk-flex uk-flex-middle kuk-child-margin-small-left',
                ],
                'multiple' => false,
                'expanded' => true,
                ])
            ->add('birthday', BirthdayType::class, [
                'label' => 'birthday',
                'attr' => [
                    'class' => 'uk-flex kuk-child-margin-small-left',
                ],
            ])
            ->add('country', CountryType::class, [
                'label' => 'country',
            ])
            ->add('avatar', FileType::class, [
                'label' => 'avatar',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file.',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'save',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserProfileModel::class,
        ]);
    }
}
