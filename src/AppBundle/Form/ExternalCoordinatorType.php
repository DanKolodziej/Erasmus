<?php


namespace AppBundle\Form;

use AppBundle\Entity\ExternalCoordinator;
use FOS\UserBundle\Form\Type\UsernameFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class ExternalCoordinatorType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', UserNoFacultyExtensionType::class, array('label' => false, 'constraints' => array(new Valid())))
            ->add('university', EntityType::class, array('expanded' => false, 'multiple' => false, 'class' => 'AppBundle:University',
                'choice_label' => 'name', 'attr' => array('style' => 'margin:15px')))
            ->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px; margin-left: 20px')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => ExternalCoordinator::class));
    }

}