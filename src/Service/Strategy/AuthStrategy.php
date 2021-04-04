<?php

namespace App\Service\Strategy;

use App\Entity\Plan;
use App\Service\Strategy\FormStrategyInterface;
use App\Service\UserService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Ramsey\Uuid\Uuid;

/**
 * Class AuthStrategy
 */
class AuthStrategy implements FormStrategyInterface {

    /**
     * The user object
     * @var $user
     */
    private $user;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage, UserService $userService)
    {
        $this->tokenStorage = $tokenStorage;
        $this->user = $userService->getUser();

    }

    /**
     * @return string
     */
    public function getFormType() {
        return 'App\Form\PlanType';
    }

    /**
     * @return string
     */
    public function getTwigTemplate(){
        return 'plan/new.html.twig';
    }

    /**
     * @param $form
     * @return mixed
     */
    public function createPlan($form)
    {
        $plan = $form->getData();
        $plan->setUser($this->user);

        return $plan;
    }

    /**
     * @return string
     */
    public function getByTemplateFormType() {
        return 'App\Form\ByTemplate\PlanByTemplateType';
    }

    /**
     * @return string
     */
    public function getByTemplateTwigTemplate() {
        return 'plan/by-template/new-by-template.html.twig';
    }

    /**
     * @param $form
     * @return Plan
     */
    public function handleSpecificFieldsByTemplate($form)
    {
        $orgPlan = $form->get('templates')->getData();
        $formPlan = $form->getData();
        $clone = clone $orgPlan;
        $clone->setId(Uuid::uuid4()->toString());
        $clone->setIsPublic(false);
        $clone->setIsTemplate(false);
        $clone->setTitle($formPlan->getTitle());
        $clone->setDate($formPlan->getDate());
        $clone->setDescription($formPlan->getDescription());
        $clone->setUser($this->user);

        return $clone;
    }
}
