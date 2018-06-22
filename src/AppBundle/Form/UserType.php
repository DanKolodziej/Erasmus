<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('surname', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('username', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('email', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('plainPassword', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('faculty', EntityType::class, array('expanded' => false, 'multiple' => false, 'class' => 'AppBundle:faculty', 'choice_label' => 'shortName', 'attr' => array('style' => 'margin:15px')))
            ->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px; margin-left: 20px')));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => User::class));
    }

    public function getParent()
    {

        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {

        return 'new_erasmus_user';
    }
}