<?php

namespace solver\solverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * solver\solverBundle\Entity\solutions
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="solver\solverBundle\Repository\solutionsRepository")
 */
class solutions
{
    /**     
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     *   @ORM\Column("name=titre", type="string")
     *   
     */
    private $titre;

    /**
     * @var text $libelle
     *
     * @ORM\Column(name="libelle", type="text", nullable=true)
     */
    private $libelle;

    /**
     * @var boolean $existe
     *
     * @ORM\Column(name="existe", type="boolean")
     */
    private $existe;
    
    /**
     * @ORM\ManyToOne(targetEntity="problemes")     
     */
    private $probleme;
    
    /**
     * Priorité d'affichage des solutions par rapport aux autres, dans un problème donné
     * @ORM\Column(name="priorite", type="integer", nullable=true)
     */
    private $priorite;
    
    public function __construct()
    {
        $this->existe=true;
    }
    
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
     * @param text $libelle
     */
    public function setLibelle($libelle=null)
    {
        $this->libelle = $libelle;
    }

    /**
     * Get libelle
     *
     * @return text 
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
     * Set titre
     *
     * @param string $titre
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set probleme
     *
     * @param solver\solverBundle\Entity\problemes $probleme
     */
    public function setProbleme(\solver\solverBundle\Entity\problemes $probleme)
    {
        $this->probleme = $probleme;
    }

    /**
     * Get probleme
     *
     * @return solver\solverBundle\Entity\problemes 
     */
    public function getProbleme()
    {
        return $this->probleme;
    }

    /**
     * Set priorite
     *
     * @param integer $priorite
     */
    public function setPriorite($priorite=null)
    {
        $this->priorite = $priorite;
    }

    /**
     * Get priorite
     *
     * @return integer 
     */
    public function getPriorite()
    {
        return $this->priorite;
    }
}