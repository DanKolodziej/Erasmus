<?php

namespace AppBundle\Form;

use AppBundle\Entity\Semester;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SemesterType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('season', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'width: 90%; margin: auto; margin-bottom:15px;'), 'label_attr' => array('style' => 'margin-left: 5%')))
            ->add('year', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'width: 90%; margin: auto; margin-bottom:15px;'), 'label_attr' => array('style' => 'margin-left: 5%')))
            ->add('status', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'width: 90%; margin: auto; margin-bottom:15px;'), 'label_attr' => array('style' => 'margin-left: 5%')))
            ->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px; margin-left: 5%')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => Semester::class));
    }

}