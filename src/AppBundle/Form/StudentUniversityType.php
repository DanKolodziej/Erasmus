<?php

namespace AppBundle\Form;


use AppBundle\Entity\Faculty;
use AppBundle\Entity\Student;
use AppBundle\Entity\University;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Valid;

class StudentUniversityType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('user', UserExtensionType::class, array('label' => false, 'constraints' => array(new Valid())))
//            ->add('university', EntityType::class, array('expanded' => false, 'multiple' => false, 'class' => 'AppBundle:University',
//                'choice_label' => 'name', 'attr' => array('style' => 'margin:15px')))
//            ->add('externalCoordinator', HiddenType::class)
//            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//
//                $form = $event->getForm();
//                $university = $event->getData()->getUniversity();
//
//                $formOptions = array(
//                    'expanded' => false,
//                    'multiple' => false,
//                    'class'         => 'AppBundle:ExternalCoordinator',
//                    'choice_label'  => function($externalCoordinator){
//
//                        return $externalCoordinator->getUser()->getName().' '.$externalCoordinator->getUser()->getSurname();
//                    },
////                    'query_builder' => function (EntityRepository $er) use ($form) {
////                        return $er->createQueryBuilder('c')
////                            ->where('c.university = ?1')
////                            ->setParameter(1,$form->get("university")->getData());
////                    },
//                    'placeholder' => 'Choose university first',
//                    'attr' => array('style' => 'margin:15px')
//                );
//
//                $form->add('externalCoordinator', EntityType::class, $formOptions);
//            })
            ->add('externalCoordinator', EntityType::class, array('expanded' => false, 'multiple' => false, 'class' => 'AppBundle:ExternalCoordinator',
                'choice_label'  => function($externalCoordinator){

                    return $externalCoordinator->getUser()->getName().' '.$externalCoordinator->getUser()->getSurname();
                }, 'attr' => array('style' => 'margin:15px')
            ))
            ->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px; margin-left: 20px')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => Student::class, 'externalCoordinator' => null));
    }
}