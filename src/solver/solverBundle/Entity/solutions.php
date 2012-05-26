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
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var text $libelle
     *
     * @ORM\Column(name="libelle", type="text")
     */
    private $libelle;

    /**
     * @var boolean $existe
     *
     * @ORM\Column(name="existe", type="boolean")
     */
    private $existe;
    
    /**
     * @ORM\OneToOne(targetEntity="problemes")     
     */
    private $probleme;
    

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
    public function setLibelle($libelle)
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
}