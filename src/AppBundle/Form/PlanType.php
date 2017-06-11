<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PlanType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $classes = 'form-control';

        $builder
            ->add('title', TextType::class, array(
                'attr'  => array('class' => $classes),
                'label' => 'title'
            ))
            ->add('date', DateType::class, array(
                'attr'  => array('class' => $classes . ' datepicker'),
                'html5' => false,
                'widget' => 'single_text',
                'required' => false,
                'label' => 'date'
            ))
            ->add('description', TextareaType::class, array(
                'attr'  => array('class' => $classes),
                'required' => false,
                'label' => 'description'
            ))
            ->add('email', EmailType::class, array(
                'attr'  => array('class' => $classes),
                'required' => false,
                'label' => 'email_label'
            ))
            ->add('isTemplate', CheckboxType::class, array(
                'attr'  => array('class' => $classes),
                'label' => 'is_template',
                'required' => false
            ))
            ->add('isPublic', CheckboxType::class, array(
                'attr'  => array('class' => $classes),
                'label' => 'is_public',
                'required' => true
            ))
            ->add('shifts', CollectionType::class, array(
                'entry_type' => ShiftType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'required' => true
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Plan'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_plan';
    }


}
