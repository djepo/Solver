<?php

namespace solver\solverBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/{entite_id}", name="select_entite")
     * @Template()
     */
    public function indexAction($entite_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entites = $em->getRepository('solversolverBundle:entites')->getAll(true);
        $entite = $em->getRepository('solversolverBundle:entites')->find($entite_id);
        
        $symptomes = $em->getRepository('solversolverBundle:problemes')->getByEntite($entite_id);
        
        if ($entite){
            return $this->render('solvermainBundle::layout.html.twig', array('entites' => $entites,
                                                                         'entite'=>$entite,
                                                                         'symptomes'=>$symptomes));
        }
        else {
            throw $this->createNotFoundException('Entité (id='.$entite_id.') introuvable');
        }

    }


     /**
     * @Route("/{entite_id}/{symptome_id}/", name="select_entite_pb")
     * @Template()
     */
    public function problemLevelAction($entite_id, $symptome_id)
    {
        //récupération des éventuels paramètres get
        $request = $this->getRequest();
        $params=$request->query->get('chain');
        $chain=explode("&",$params);//arborescence passée en paramètres
        $level=sizeof($chain);      //niveau d'arborescence passé en paramètres
        if ($chain[0]=='') {
            $level=0;               //si aucun parametre en chain, le level est à 0, sinon, on a le niveau d'arborescence (par défaut, un array vide, a l'indice 0= à '', ca foutait la merde)
        }
        //var_dump($level);

        $em = $this->getDoctrine()->getEntityManager();

        $entites = $em->getRepository('solversolverBundle:entites')->getAll(true);
        $entite = $em->getRepository('solversolverBundle:entites')->find($entite_id);
        if (!$entite){
            throw $this->createNotFoundException('Entité (id='.$entite_id.') introuvable');
        }        
        
        $symptome = $em->getRepository('solversolverBundle:problemes')->find($symptome_id);
        if (!$symptome){
            throw $this->createNotFoundException('Symptome (id='.$symptome_id.') introuvable');
        }
        
        $chaine_problemes=null; //init à null, au cas ou on n'aie rien de passé en paramètres
        $probleme_selectionné=null;
        for ($i=0;$i<$level;$i++) {
            $chaine_problemes[$i]=$em->getRepository('solversolverBundle:problemes')->find($chain[$i]);
        }
        if (count($chaine_problemes)>0){
            $probleme_selectionné=$chaine_problemes[count($chaine_problemes)-1];
        }

        //var_dump($symptome->getproblemesamont());
        
        if ($request->isXmlHttpRequest()){
            //si on arrive ici par une requête ajax
            return $this->container->get('templating')->renderResponse('solversolverBundle:problemes:index.html.twig', array('entites' => $entites,
                                                                         'entite'=>$entite,
                                                                         'symptome'=>$symptome,
                                                                         'chaine_problemes'=>$chaine_problemes,
                                                                         'problème_sélectionné'=>$probleme_selectionné,
                                                                         'xhr_incoming'=>true,
                                                                        ));
        }
        else {
            //si on n'est pas sur une requête ajax (surement une requête get)
            return $this->render('solvermainBundle::layout.html.twig', array('entites' => $entites,
                                                                         'entite'=>$entite,
                                                                         'symptome'=>$symptome,
                                                                         'chaine_problemes'=>$chaine_problemes,
                                                                         'problème_sélectionné'=>$probleme_selectionné
                                                                        )
                            );
        }

    }
    
    /**
     * @Route("/ajax/probleme_solution/{probleme_id}/", name="ajax_probleme_solution_show")
     */
    public function ajax_solutionAction($probleme_id){
        $request = $this->getRequest();
        
        if ($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getEntityManager();
            $probleme = $em->getRepository('solversolverBundle:problemes')->find($probleme_id);
            $solution=$probleme->getSolution();
            
            return $this->container->get('templating')->renderResponse('solversolverBundle:blocs:ajax_solution.html.twig', array('solution' => $solution));
            
        }
        else {
            throw $this->createNotFoundException('Cette page est uniquement ajax, impossible de l\'appeler directement.');
        }
    }

}
