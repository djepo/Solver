<?php

namespace solver\solverBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use solver\solverBundle\Entity\problemes;
use solver\solverBundle\Form\problemesType;

/**
 * problemes controller.
 *
 * @Route("/problemes")
 */
class problemesController extends Controller
{
    /**
     * Lists all problemes entities.
     *
     * @Route("/", name="problemes")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('solversolverBundle:problemes')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a problemes entity.
     *
     * @Route("/{id}/show", name="problemes_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('solversolverBundle:problemes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find problemes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new problemes entity.
     *
     * @Route("/new", name="problemes_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new problemes();
        $form   = $this->createForm(new problemesType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new problemes entity.
     *
     * @Route("/create", name="problemes_create")
     * @Method("post")
     * @Template("solversolverBundle:problemes:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new problemes();
        $request = $this->getRequest();
        $form    = $this->createForm(new problemesType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('problemes_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing problemes entity.
     *
     * @Route("/{id}/edit", name="problemes_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('solversolverBundle:problemes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find problemes entity.');
        }

        $editForm = $this->createForm(new problemesType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing problemes entity.
     *
     * @Route("/{id}/update", name="problemes_update")
     * @Method("post")
     * @Template("solversolverBundle:problemes:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('solversolverBundle:problemes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find problemes entity.');
        }

        $editForm   = $this->createForm(new problemesType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('problemes_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a problemes entity.
     *
     * @Route("/{id}/delete", name="problemes_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('solversolverBundle:problemes')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find problemes entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('problemes'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
