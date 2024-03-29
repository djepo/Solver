<?php

namespace solver\solverBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use solver\solverBundle\Entity\entites;
use solver\solverBundle\Form\entitesType;

/**
 * entites controller.
 *
 * @Route("/entites")
 */
class entitesController extends Controller
{
    /**
     * Lists all entites entities.
     *
     * @Route("/", name="entites")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('solversolverBundle:entites')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a entites entity.
     *
     * @Route("/{id}/show", name="entites_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('solversolverBundle:entites')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entites entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new entites entity.
     *
     * @Route("/new", name="entites_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new entites();
        $form   = $this->createForm(new entitesType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new entites entity.
     *
     * @Route("/create", name="entites_create")
     * @Method("post")
     * @Template("solversolverBundle:entites:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new entites();
        $request = $this->getRequest();
        $form    = $this->createForm(new entitesType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('entites_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing entites entity.
     *
     * @Route("/{id}/edit", name="entites_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('solversolverBundle:entites')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entites entity.');
        }

        $editForm = $this->createForm(new entitesType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing entites entity.
     *
     * @Route("/{id}/update", name="entites_update")
     * @Method("post")
     * @Template("solversolverBundle:entites:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('solversolverBundle:entites')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entites entity.');
        }

        $editForm   = $this->createForm(new entitesType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('entites_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a entites entity.
     *
     * @Route("/{id}/delete", name="entites_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('solversolverBundle:entites')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find entites entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('entites'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
