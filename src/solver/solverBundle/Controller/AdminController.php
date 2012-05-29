<?php

namespace solver\solverBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;

use solver\solverBundle\Form\entitesType;
use solver\solverBundle\Form\problemesType;
use solver\solverBundle\Form\solutionsType;


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

//*************************************************************************************************************

    /**
    * @Route("/Ajax/Entites", name="admin_ajax_entites")
    */
    public function admin_ajax_entitesAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $entites=$em->getRepository('solversolverBundle:entites')->findBy(array(), array('libelle'=>'ASC'));
        $nouvelle_entite=new \solver\solverBundle\Entity\entites();
        $form_nouvelle_entite=$this->createForm(new entitesType(),$nouvelle_entite);

        for($i=0;$i<count($entites);$i++)
        {
            $forms[$i]= $this->createForm(new entitesType(), $entites[$i])->createView();
        }

        return $this->render('solversolverBundle:Admin/Ajax:entites.html.twig', array('entites'=>$entites,
                                                                                      'forms'=>$forms,
                                                                                      'nouvelle_entite'=>$nouvelle_entite,
                                                                                      'form_nouvelle_entite'=>$form_nouvelle_entite->createView(),
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

            return new response('');
        }
        else {
            throw $this->createNotFoundException('Erreur de validation');
        }
    }

    /**
     * @Route("/Entite/Create", name="admin_entite_create")
     * @Method("post")
     */
    public function admin_entite_createAction()
    {
        $entite  = new \solver\solverBundle\Entity\entites();
        $request = $this->getRequest();
        $form    = $this->createForm(new entitesType(), $entite);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entite);
            $em->flush();

            $this->get('session')->setFlash('success','L\'entité a été correctement ajoutée.');
            //return $this->redirect($this->generateUrl('problemes_show', array('id' => $probleme->getId())));
            return $this->redirect($this->generateUrl('admin_home'));

        }

        throw new \Exception('Erreur de validation');
    }

//*************************************************************************************************************

    /**
    * @Route("/Ajax/Problemes", name="admin_ajax_problemes")
    */
    public function admin_ajax_problemesAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $problemes=$em->getRepository('solversolverBundle:problemes')->findBy(array(), array('libelle'=>'ASC'));
        $nouveau_probleme=new \solver\solverBundle\Entity\problemes();
        $form_nouveau_probleme=$this->createForm(new problemesType(), $nouveau_probleme);

        for($i=0;$i<count($problemes);$i++)
        {
            $forms[$i]= $this->createForm(new problemesType(), $problemes[$i])->createView();
        }

        return $this->render('solversolverBundle:Admin/Ajax:problemes.html.twig', array('problemes'=>$problemes,
                                                                                        'forms'=>$forms,
                                                                                        'nouveau_probleme'=>$nouveau_probleme,
                                                                                        'form_nouveau_probleme'=>$form_nouveau_probleme->createView(),
        ));
    }

    /**
     * @Route("/Ajax/Probleme/{id}/update", name="admin_ajax_probleme_update")
     * @Method("post")
     */
    public function admin_ajax_problemesUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $problemes = $em->getRepository('solversolverBundle:problemes')->find($id);

        if (!$problemes) {
            throw $this->createNotFoundException('Unable to find problemes entity.');
        }

        $editForm   = $this->createForm(new problemesType(), $problemes);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($problemes);
            $em->flush();

            return new response('');
        }
        else {
            throw $this->createNotFoundException('Erreur de validation');
        }
    }

    /**
     * @Route("/Probleme/Create", name="admin_probleme_create")
     * @Method("post")
     */
    public function admin_probleme_createAction()
    {
        $probleme  = new \solver\solverBundle\Entity\problemes();
        $request = $this->getRequest();
        $form    = $this->createForm(new problemesType(), $probleme);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($probleme);
            $em->flush();

            $this->get('session')->setFlash('success','Le problème a été correctement ajouté.');
            //return $this->redirect($this->generateUrl('problemes_show', array('id' => $probleme->getId())));
            return $this->redirect($this->generateUrl('admin_home'));

        }

        throw new \Exception('Erreur de validation');
    }

//*************************************************************************************************************


    /**
    * @Route("/Ajax/Solutions", name="admin_ajax_solutions")
    */
    public function admin_ajax_solutionsAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $solutions=$em->getRepository('solversolverBundle:solutions')->findBy(array(), array('libelle'=>'ASC'));
        $nouvelle_solution=new \solver\solverBundle\Entity\solutions();
        $form_nouvelle_solution=$this->createForm(new solutionsType(), $nouvelle_solution);

        for($i=0;$i<count($solutions);$i++)
        {
            $forms[$i]= $this->createForm(new solutionsType(), $solutions[$i])->createView();
        }

        return $this->render('solversolverBundle:Admin/Ajax:solutions.html.twig', array('solutions'=>$solutions,
                                                                                        'forms'=>$forms,
                                                                                        'nouvelle_solution'=>$nouvelle_solution,
                                                                                        'form_nouvelle_solution'=>$form_nouvelle_solution->createView(),
        ));
    }

    /**
     * @Route("/Ajax/Solution/{id}/update", name="admin_ajax_solution_update")
     * @Method("post")
     */
    public function admin_ajax_solutionUpdateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $solution = $em->getRepository('solversolverBundle:solutions')->find($id);

        if (!$solution) {
            throw $this->createNotFoundException('Unable to find solutions entity.');
        }

        $editForm   = $this->createForm(new solutionsType(), $solution);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($solution);
            $em->flush();

            return new response('');
        }
        else {
            throw $this->createNotFoundException('Erreur de validation');
        }
    }

    /**
     * @Route("/Solution/Create", name="admin_solution_create")
     * @Method("post")
     */
    public function admin_solution_createAction()
    {
        $solution  = new \solver\solverBundle\Entity\solutions();
        $request = $this->getRequest();
        $form    = $this->createForm(new solutionsType(), $solution);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($solution);
            $em->flush();

            $this->get('session')->setFlash('success','La solution a été correctement ajouté.');
            return $this->redirect($this->generateUrl('admin_home'));

        }

        throw new \Exception('Erreur de validation');
    }

//*************************************************************************************************************

    /**
    * @Route("/Ajax/Liaisons", name="admin_ajax_liaisons")
    */
    public function admin_ajax_liaisonsAction() {
        //$em = $this->getDoctrine()->getEntityManager();
        //$entites=$em->getRepository('solversolverBundle:entites')->findBy(array(), array('libelle'=>'ASC'));
        return $this->render('solversolverBundle:Admin/Ajax:liaisons.html.twig');
    }

    /**
     * @Route("/Ajax/Liaisons/Treeview/Entites", name="admin_ajax_liaisonstreeviewentitesaction")
     */
    public function admin_ajax_liaisonsTreeviewEntitesAction()
    {
        $request = $this->getRequest();
        $params=$request->query->get('id');
        $em = $this->getDoctrine()->getEntityManager();

        if ($params)
        {
            $chain=explode("&",$params);//arborescence passée en paramètres
            $level=sizeof($chain)-1;      //niveau d'arborescence passé en paramètres (le niveau 0 est celui des entités)

            if (substr($chain[$level],0,1)=='e')
            {
                //level 0, on va renvoyer les problèmes liés à l'entité
                $entite_id=substr($chain[$level],1,strlen($chain[$level])-1);
                $problemes=$em->getRepository('solversolverBundle:problemes')->findBy(array('entite'=>$entite_id), array('libelle'=>'ASC'));
                return $this->render('solversolverBundle:Admin/Ajax/Treeview:problemesParEntite.html.twig', array('problemes'=>$problemes,));
            }
            else {
                //level supérieur à 0, on va renvoyer les problèmes liés à un problème
                $probleme_id=substr($chain[$level],1,strlen($chain[$level])-1);
                $problemes=$em->getRepository('solversolverBundle:probleme_probleme')->findBy(array('probleme_aval'=>$probleme_id), array());
                //var_dump($problemes);
                return $this->render('solversolverBundle:Admin/Ajax/Treeview:problemesParProbleme.html.twig', array('problemes'=>$problemes,));
            }

        }
        else
        {
            //pas d'id définie, on va donc renvoyer la liste des entités
            $entites=$em->getRepository('solversolverBundle:entites')->findBy(array(), array('libelle'=>'ASC'));
            return $this->render('solversolverBundle:Admin/Ajax/Treeview:entites.html.twig', array('entites'=>$entites,));
        }
    }

     /**
     * @Route("/Ajax/Liaisons/Treeview/RenameThing", name="admin_ajax_liaisonstreeview_RenameThing")
     */
    public function admin_ajax_liaisonsTreeviewRenameThingAction()
    {
        $request = $this->getRequest();
        $param_id=$request->request->get('id');  
        $param_value=$request->get('libelle');
        
        if(!$param_id) {
            throw $this->createNotFoundException('Erreur: paramètre "id" introuvable.');
        }        
        if(!$param_value) {
            throw $this->createNotFoundException('Erreur: paramètre "value" introuvable.');
        }

        $em = $this->getDoctrine()->getEntityManager();
        
        if (substr($param_id,0,1)=='e') {
            //l'id commence par "e", c'est donc une entité
            $entite_id=substr($param_id,1,strlen($param_id)-1);
            $entite=$em->getRepository('solversolverBundle:entites')->find($entite_id);
            if (!$entite){
                throw $this->createNotFoundException('Erreur: entité introuvable.');
            }
            $entite->setLibelle($param_value);
            
            $validator = $this->get('validator');
            $errors = $validator->validate($entite);
                                   
            if (count($errors)==0)
            {
               $em->persist($entite);
               $em->flush();
               return new Response('ok');
            }
            else
            {
              //throw new \Exception('Erreur de validation');  
                return new Response(print_r($errors, true));
            }
        }
        else {
            //l'id ne commence pas par e, c'est donc un problème
            $probleme_id=substr($param_id,1,strlen($param_id)-1);
            $probleme=$em->getRepository('solversolverBundle:problemes')->find($probleme_id);
            if (!$probleme){
                throw $this->createNotFoundException('Erreur: probleme introuvable.');
            }
            $probleme->setLibelle($param_value);
            
            $validator = $this->get('validator');
            $errors = $validator->validate($probleme);
                                   
            if (count($errors)==0)
            {
               $em->persist($probleme);
               $em->flush();
               return new Response('ok');
            }
            else
            {
              //throw new \Exception('Erreur de validation');  
                return new Response(print_r($errors, true));
            }
        }
    }

}
