<?php

namespace solver\solverBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller;

/**
 * entitésRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class entitesRepository extends EntityRepository
{
    /**
     *  Renvoie toutes les entites
     * 
     * @param bool $existe par défaut: vrai. Renvoie les entités existantes, ou inexistantes
     * @return solver\solverBundle\Entity\entite
     */
    public function getAll($only_existe=false){
        $em=$this->_em;
        
        if ($only_existe){
            $entites = $em->getRepository('solversolverBundle:entites')->findBy(array('existe'=>true));
        }
        else {
            $entites = $em->getRepository('solversolverBundle:entites')->findAll();
        }
        //$em = $this->getDoctrine()->getEntityManager();  
        
        
        
        return $entites;
    }            
}