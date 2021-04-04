<?php

namespace App\Form;

use App\Validator\Constraints\EmailUsed;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class PlanUnauthenticatedType extends AbstractType
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
                'required' => true,
                'label' => 'date',
                'format' => 'yyyy-MM-dd'
            ))
            ->add('description', TextareaType::class, array(
                'attr'  => array('class' => $classes),
                'required' => true,
                'label' => 'description'
            ))
            ->add('email', EmailType::class, array(
                'attr'  => array('class' => $classes),
                'required' => true,
                'label' => 'email_label',
                'mapped' => false,
                'constraints' => array(
                    new EmailUsed(array('groups' => array('new_from_template'))),
                    New Email()
                )
            ))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => $classes)),
                'required' => true,
                'first_options'  => array('label' => 'password'),
                'second_options' => array('label' => 'repeat_password'),
                'mapped' => false,
                'constraints' => array(
                    new Length(array(
                        'groups' => array('new_from_template'),
                        'min' => 8,
                        'max' => 255
                    ))
                )
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
            'data_class' => 'App\Entity\Plan',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_plan';
    }


}
