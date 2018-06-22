<?php

namespace AppBundle\Form;

use AppBundle\Entity\CourseStudent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseStudentGradeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('grade', ChoiceType::class, array('expanded' => false, 'multiple' => false, 'choices' => array(
                '2.0' => 2, '3.0' => 3, '3.5' => 3.5, '4.0' => 4, '4.5' => 4.5, '5.0' => 5, '5.5' => 5.5),
                'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px; margin-left: 20px')));


    }
}