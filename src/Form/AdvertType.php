<?php

namespace App\Form;

use App\Entity\Advert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, array(
              'label' => "Information suplementaire",
              'attr' => ['class' => 'content']
            ))
            ->add('lat', HiddenType::class)
            ->add('lng', HiddenType::class)
            // ->add('time', RangeType::class, array(
            //   'attr' => array(
            //       'min' => 5,
            //       'max' => 50
            //   )))
            ->add('time', TimeType::class, array(
              'widget' => 'choice',
              'minutes' => [0,10,20,30,40,50],
              // 'mapped' => false,
              'label' => "Durée de la Session"
            ))
            ->add('date', DateType::class, array(
              'widget' => 'single_text',
              'format' => 'dd/MM/yyyy',
              'mapped' => false,
              'html5' => false,
              'attr' => ['class' => 'd-none']

            ))
              ->add('level', ChoiceType::class, array(
           'choices' => array('Sport' => 'Sport', 'Balade' => 'Balade'),
           'expanded' => true,
           'multiple' => false,
           'label' => "Choisisez le type de sortie :"
            ))
            ->add('hour', TimeType::class, array(
              'widget' => 'choice',
              'minutes' => [0,10,20,30,40,50],
              'mapped' => false,
              'label' => "Heure du rendez vous:"
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}
