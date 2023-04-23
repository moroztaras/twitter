<?php

namespace App\Form\Twitter;

use App\Form\Twitter\Model\TwitterModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TwitterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextareaType::class, [
                'label' => 'text',
                'required' => true,
                'attr' => [
                    'maxlength' => 1000,
                    ],
            ])
            ->add('video', TextareaType::class, [
                'label' => 'video',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TwitterModel::class,
        ]);
    }
}
