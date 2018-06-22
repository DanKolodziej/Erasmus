<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Course;

class CourseType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('form', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('ects', IntegerType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('syllabus', FileType::class, array('required' => false, 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('level', ChoiceType::class, array('expanded' => true, 'multiple' => false,
                'choices' => array(
                'I - inż' => 'I - inż',
                'II - mgr' => 'II - mgr', 'III - dr' => 'III - dr'),
                'attr' => array('class' => 'btn btn-default', 'style' => 'margin:15px'), 'choice_attr' => array(
                'I - inż' => array('style' => 'margin-right: 15px'),
                'II - mgr' => array('style' => 'margin-left: 15px; margin-right: 15px'),
                'III - dr' => array('style' => 'margin-left: 15px; margin-right: 15px')
            )))
            ->add('type', ChoiceType::class, array('expanded' => false, 'multiple' => false, 'choices' => array(
                'Compulsory' => 'Compulsory', 'Selective' => 'Selective'), 'attr' => array('class' => 'btn btn-default', 'style' => 'margin:15px')))
            ->add('semesters', EntityType::class, array('expanded' => true, 'multiple' => true, 'class' => 'AppBundle:Semester',
                'choice_label' => function($semester){
                    return $semester->getSeason().' '.$semester->getYear();
                }, 'attr' => array('class' => 'btn btn-default', 'style' => 'margin:15px')));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => Course::class));
    }

}