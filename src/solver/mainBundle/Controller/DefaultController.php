<?php

namespace solver\mainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entites = $em->getRepository('solversolverBundle:entites')->getAll(true);

        /*test des requetes*/
        //$test=$em->getRepository('solversolverBundle:solving_log')->compteOccurencesAssociationProblemes(4,1);
        //var_dump($test);
        
        return array('entites' => $entites);        
    }
}
