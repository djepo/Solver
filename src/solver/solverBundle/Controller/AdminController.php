<?php

namespace solver\solverBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;

use solver\solverBundle\Form\entitesType;

/**
 * @Route("/Admin") 
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_home")
     * @Template()
     */
    public function indexAction()
    {              
        return $this->render('solversolverBundle:Admin:layout.html.twig');
    }
    
    /**
      * @Route("/Ajax/Entites", name="admin_ajax_entites")
     */
    public function admin_ajax_entitesAction() {
        $em = $this->getDoctrine()->getEntityManager();
        
        $entites=$em->getRepository('solversolverBundle:entites')->findBy(array(), array('libelle'=>'ASC'));
        
        for($i=0;$i<count($entites);$i++)
        {
            $forms[$i]= $this->createForm(new entitesType(), $entites[$i])->createView();
        }
        
        return $this->render('solversolverBundle:Admin/Ajax:entites.html.twig', array('entites'=>$entites,
                                                                                      'forms'=>$forms,
        ));
    }
    
    
    
    /**
     * @Route("/Ajax/Entite/{id}/update", name="admin_ajax_entite_update")
     * @Method("post")
     */
    public function admin_ajax_entitesUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('solversolverBundle:entites')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entites entity.');
        }

        $editForm   = $this->createForm(new entitesType(), $entity);        

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            //return $this->redirect($this->generateUrl('admin_home'));
            return new response('');
        }
        else {
            throw $this->createNotFoundException('Erreur de validation');
        }         
    }
    
}
