<?php

namespace solver\solverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * solver\solverBundle\Entity\problemeprobleme
 *
 * @ORM\Table() 
 * @ORM\Entity(repositoryClass="solver\solverBundle\Repository\probleme_problemeRepository") 
 * @ORM\HasLifecycleCallbacks()
 */
class probleme_probleme 
{
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="problemes")     
     */
    private $probleme_amont;
    
     /**     
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="problemes")     
     */
    private $probleme_aval;
    
    /**
     * Compteur sur le nombre de problèmes aval résolus grâce au problème amont.
     * Théoriquement, ce compteur doit correspondre au nombre d'association identique que l'on va trouver dans la table solving_log
     * @ORM\Column(name="comptageUtilisationAmont", type="integer")
     * @var type 
     */
    private $comptageUtilisationAmont;
    
    /**
     *Compteur total de résolution d'un problème par u nproblème
     *@ORM\Column(name="comptageUtilisationAmontTotal", type="integer")
     */
    private $comptageUtilisationAmontTotal;
    
    
    
    /**
     * Indique si l'association existe encore (ne pas effacer, afin de pouvoir garder des statistiques)
     * @var boolean
     * @ORM\Column(name="existe", type="boolean", nullable=false)
     */
    private $existe;    

    
    public function __construct()
    {
        //une association existe par défaut, lors de sa création
        $this->comptageUtilisationAmont=0;
        $this->comptageUtilisationAmontTotal=0;
        $this->existe=true;
    }
    
    /**
     * Set probleme_amont
     *
     * @param solver\solverBundle\Entity\problemes $problemeAmont
     */
    public function setProblemeAmont(\solver\solverBundle\Entity\problemes $problemeAmont)
    {
        $this->probleme_amont = $problemeAmont;
    }

    /**
     * Get probleme_amont
     *
     * @return solver\solverBundle\Entity\problemes 
     */
    public function getProblemeAmont()
    {
        return $this->probleme_amont;
    }

    /**
     * Set probleme_aval
     *
     * @param solver\solverBundle\Entity\problemes $problemeAval
     */
    public function setProblemeAval(\solver\solverBundle\Entity\problemes $problemeAval)
    {
        $this->probleme_aval = $problemeAval;
    }

    /**
     * Get probleme_aval
     *
     * @return solver\solverBundle\Entity\problemes 
     */
    public function getProblemeAval()
    {
        return $this->probleme_aval;
    }

    /**
     * Set existe
     *
     * @param boolean $existe
     */
    public function setExiste($existe)
    {
        $this->existe = $existe;
    }

    /**
     * Get existe
     *
     * @return boolean 
     */
    public function getExiste()
    {
        return $this->existe;
    }

    /**
     * Set comptageUtilisationAmont
     *
     * @param integer $comptageUtilisationAmont
     */
    public function setComptageUtilisationAmont($comptageUtilisationAmont)
    {
        $this->comptageUtilisationAmont = $comptageUtilisationAmont;
    }

    /**
     * Get comptageUtilisationAmont
     *
     * @return integer 
     */
    public function getComptageUtilisationAmont()
    {
        //retourne directement le nombre d'occurences calculées à partir de la table solving_logs.
        //pour l'ordonner en DESC, il faut encore le préciser dans la table problèmes
        //$test=$this->getProblemeAmont()->getSolvingLogProblemeAmont()->count();
        return $this->comptageUtilisationAmont;
        //return $test;
    }
    
    /**
     * Cette fonction, appelée a la suite du chargement de l'entité en mémoire,
     * permet d'intégrer le comptage du nombre de fois ou un probleme aval a été identifié à un problème amont.
     * Le comptage s'effectue dans la table solving_logs, là ou on enregistre tous ces évènements.
     * 
     * @ORM\postLoad
     */
    public function doStuffOnPostLoad()
    {    
        $this->setComptageUtilisationAmont($this->getProblemeAmont()->getSolvingLogProblemeAmont()->count());
        $this->setComptageUtilisationAmontTotal($this->getProblemeAval()->getSolvingLogProblemeAval()->count());
        //$this->getProblemeAmont()->getSolvingLogProblemeAmont()->count();
        //$this->value = 'changed from postLoad callback!';
    }

    /**
     * Set comptageUtilisationAmontTotal
     *
     * @param integer $comptageUtilisationAmontTotal
     */
    public function setComptageUtilisationAmontTotal($comptageUtilisationAmontTotal)
    {
        $this->comptageUtilisationAmontTotal = $comptageUtilisationAmontTotal;
    }

    /**
     * Get comptageUtilisationAmontTotal
     *
     * @return integer 
     */
    public function getComptageUtilisationAmontTotal()
    {
        return $this->comptageUtilisationAmontTotal;
    }
}