<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PlanCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Plancollection controller.
 *
 * @Route("plancollection")
 */
class PlanCollectionController extends Controller
{
    /**
     * Lists all planCollection entities.
     *
     * @Route("/", name="plancollection_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $planCollections = $em->getRepository('AppBundle:PlanCollection')->findAll();

        return $this->render('plancollection/index.html.twig', array(
            'planCollections' => $planCollections,
        ));
    }

    /**
     * Creates a new planCollection entity.
     *
     * @Route("/new", name="plancollection_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $planCollection = new Plancollection();
        $form = $this->createForm('AppBundle\Form\PlanCollectionType', $planCollection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($planCollection);
            $em->flush();

            return $this->redirectToRoute('plancollection_show', array('id' => $planCollection->getId()));
        }

        return $this->render('plancollection/new.html.twig', array(
            'planCollection' => $planCollection,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a planCollection entity.
     *
     * @Route("/{id}", name="plancollection_show")
     * @Method("GET")
     */
    public function showAction(PlanCollection $planCollection)
    {
        $deleteForm = $this->createDeleteForm($planCollection);

        return $this->render('plancollection/show.html.twig', array(
            'planCollection' => $planCollection,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing planCollection entity.
     *
     * @Route("/{id}/edit", name="plancollection_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PlanCollection $planCollection)
    {
        $deleteForm = $this->createDeleteForm($planCollection);
        $editForm = $this->createForm('AppBundle\Form\PlanCollectionType', $planCollection);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('plancollection_edit', array('id' => $planCollection->getId()));
        }

        return $this->render('plancollection/edit.html.twig', array(
            'planCollection' => $planCollection,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a planCollection entity.
     *
     * @Route("/{id}", name="plancollection_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PlanCollection $planCollection)
    {
        $form = $this->createDeleteForm($planCollection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($planCollection);
            $em->flush();
        }

        return $this->redirectToRoute('plancollection_index');
    }

    /**
     * Creates a form to delete a planCollection entity.
     *
     * @param PlanCollection $planCollection The planCollection entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PlanCollection $planCollection)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('plancollection_delete', array('id' => $planCollection->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}