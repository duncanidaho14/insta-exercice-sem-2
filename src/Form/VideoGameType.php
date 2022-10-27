<?php

namespace App\Form;

use App\Entity\VideoGames;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('introduction')
            ->add('description')
            ->add('image')
            ->add('price')
            ->add('releaseDate')
            ->add('categories')
            ->add('platforms')
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VideoGames::class,
        ]);
    }
}
