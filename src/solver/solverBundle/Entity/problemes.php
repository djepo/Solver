<?php

namespace solver\solverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * solver\solverBundle\Entity\problemes
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="solver\solverBundle\Repository\problemesRepository")
 */
class problemes
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $libelle
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;

    /**
     *     
     * @ORM\ManyToOne(targetEntity="entites", inversedBy="problemes")
     * @ORM\JoinColumn(name="entite_id", referencedColumnName="id", nullable="true")
     */
    private $entite;
    
    /**
     *    
     * @ORM\OneToMany(targetEntity="probleme_probleme", mappedBy="probleme_aval")   
     * @ORM\OrderBy({"comptageUtilisationAmont" = "DESC"})  
     */
    private $problemesamont;
    
     /**
     *    
     * @ORM\OneToMany(targetEntity="probleme_probleme", mappedBy="probleme_amont")     
     */
    private $problemesaval;
    
     /**
     * Indique si le problème existe encore (ne pas effacer, afin de pouvoir garder des statistiques)
     * @var boolean
     * @ORM\Column(name="existe", type="boolean", nullable=false)
     */
    private $existe; 
    
    /**
     * @ORM\OneToMany(targetEntity="solving_log", mappedBy="problemeaval")
     */
    private $solving_log_probleme_aval;
    
    /**
     * @ORM\OneToMany(targetEntity="solving_log", mappedBy="problemeamont")
     */
    private $solving_log_probleme_amont;
    
    /**
     * @ORM\OneToMany(targetEntity="solutions", mappedBy="probleme", cascade={"remove", "persist"})     
     */
    private $solutions;
    
    /**
     *  Permet à l'utilisateur d'afficher ou non l'entité
     *  @ORM\Column(name="affiche", type="boolean", nullable=false)     
     */
    private $affiche; 

    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
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
     * Add problemesamont
     *
     * @param solver\solverBundle\Entity\probleme_probleme $problemesamont
     */
    public function addprobleme_probleme(\solver\solverBundle\Entity\probleme_probleme $problemesamont)
    {
        $this->problemesamont[] = $problemesamont;
    }

    /**
     * Get problemesamont
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProblemesamont()
    {           
        return $this->problemesamont;
    }

    /**
     * Get problemesaval
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProblemesaval()
    {
        return $this->problemesaval;
    }

    /**
     * Add solving_log_probleme_aval
     *
     * @param solver\solverBundle\Entity\solving_log $solvingLogProblemeAval
     */
    public function addsolving_log(\solver\solverBundle\Entity\solving_log $solvingLogProblemeAval)
    {
        $this->solving_log_probleme_aval[] = $solvingLogProblemeAval;
    }

    /**
     * Get solving_log_probleme_aval
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSolvingLogProblemeAval()
    {
        return $this->solving_log_probleme_aval;
    }

    /**
     * Get solving_log_probleme_amont
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSolvingLogProblemeAmont()
    {
        return $this->solving_log_probleme_amont;
    }
    
    /**
     * Set entite
     *
     * @param solver\solverBundle\Entity\entites $entite
     */
    public function setEntite(\solver\solverBundle\Entity\entites $entite=null)
    {
        $this->entite = $entite;
    }

    /**
     * Get entite
     *
     * @return solver\solverBundle\Entity\entites 
     */
    public function getEntite()
    {
        return $this->entite;
    }
    public function __construct()
    {
        $this->problemesamont = new \Doctrine\Common\Collections\ArrayCollection();
        $this->problemesaval = new \Doctrine\Common\Collections\ArrayCollection();
        $this->solving_log_probleme_aval = new \Doctrine\Common\Collections\ArrayCollection();
        $this->solving_log_probleme_amont = new \Doctrine\Common\Collections\ArrayCollection();
        $this->solutions = new \Doctrine\Common\Collections\ArrayCollection();
        
        $this->existe=true;
        $this->affiche=true;
    }
    
    /**
     * Add solutions
     *
     * @param solver\solverBundle\Entity\solutions $solutions
     */
    public function addsolutions(\solver\solverBundle\Entity\solutions $solutions)
    {
        $this->solutions[] = $solutions;
    }

    /**
     * Get solutions
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSolutions()
    {
        return $this->solutions;
    }

    /**
     * Set affiche
     *
     * @param boolean $affiche
     */
    public function setAffiche($affiche)
    {
        $this->affiche = $affiche;
    }

    /**
     * Get affiche
     *
     * @return boolean 
     */
    public function getAffiche()
    {
        return $this->affiche;
    }
}