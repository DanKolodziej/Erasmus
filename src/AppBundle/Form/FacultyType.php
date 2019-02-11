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
use AppBundle\Entity\faculty;

class FacultyType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'width: 90%; margin: auto; margin-bottom:15px;'), 'label_attr' => array('style' => 'margin-left: 5%')))
            ->add('shortName', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'width: 90%; margin: auto; margin-bottom:15px;'), 'label_attr' => array('style' => 'margin-left: 5%')))
            ->add('upperFaculty', EntityType::class, array('expanded' => false, 'multiple' => false, 'class' => 'AppBundle:faculty',
                'choice_label' => function($upperFaculty){
                    return $upperFaculty->getShortName().' - '.$upperFaculty->getName();
                }, 'required' => false, 'attr' => array('style' => 'margin:15px'), 'label_attr' => array('style' => 'margin-left: 5%')))
            ->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px; margin-left: 5%')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => faculty::class));
    }

}