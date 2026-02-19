<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('originalTitle', TextType::class)
            ->add('overview')
            ->add('releaseDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('posterPath', TextType::class, ['required' => false])
            ->add('backdropPath', TextType::class, ['required' => false])
            ->add('popularity', NumberType::class, ['required' => false])
            ->add('voteAverage', NumberType::class, ['required' => false])
            ->add('voteCount', NumberType::class, ['required' => false])
            ->add('adult', CheckboxType::class, ['required' => false])
            ->add('video', CheckboxType::class, ['required' => false])
            ->add('originalLanguage', TextType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
