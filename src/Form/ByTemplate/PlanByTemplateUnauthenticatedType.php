<?php

namespace App\Form\ByTemplate;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Service\PlanService;
use App\Validator\Constraints\EmailUsed;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;


class PlanByTemplateUnauthenticatedType extends AbstractType
{
    /**
     * @var PlanService
     */
    private $planService;

    /**
     * PlanByTemplateType constructor.
     * @param PlanService $planService
     */
    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $classes = 'form-control';

        $builder
            ->add('templates', ChoiceType::class, array(
                'choices' => $this->planService->getTemplatePlans(),
                'choice_label' => function($plan, $key, $index) {
                    return $plan->getTitle();
                },
                'attr'  => array('class' => $classes),
                'label' => 'template_to_be_used',
                'mapped' => false
            ))
            ->add('title', null, array(
                    'attr'  => array('class' => $classes),
                    'label' => 'new_title'
                )
            )->add('date', DateType::class, array(
                'attr'  => array('class' => $classes . ' datepicker'),
                'html5' => false,
                'widget' => 'single_text',
                'label' => 'date',
                'format' => 'yyyy-MM-dd'
            ))
            ->add('email', EmailType::class, array(
                'attr'  => array('class' => $classes),
                'required' => false,
                'label' => 'email_label',
                'mapped' => false,
                'constraints' => array(
                    new EmailUsed(array('groups' => array('new_from_template'))),
                    new Email(array('groups' => array('new_from_template')))
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
            ->add('description', TextareaType::class, array(
                'attr'  => array('class' => $classes),
                'label' => 'description',
                'required' => true
            ))
            ->getForm();
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Plan',
            'validation_groups' => array('new_from_template')
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
