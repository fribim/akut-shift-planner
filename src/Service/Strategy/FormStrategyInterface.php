<?php

namespace App\Service\Strategy;

/**
 * Interface StrategyInterface
 * @package App\Form\Strategy
 */
interface FormStrategyInterface {

    /**
     * @return string
     */
    public function getFormType();

    /**
     * @return string
     */
    public function getTwigTemplate();

    /**
     * @param $formData
     * @return $plan
     */
    public function createPlan($formData);

    /**
     * @return string
     */
    public function getByTemplateFormType();

    /**
     * @return string
     */
    public function getByTemplateTwigTemplate();

    /**
     * @param $formData
     * @return Plan
     */
    public function handleSpecificFieldsByTemplate($formData);

}