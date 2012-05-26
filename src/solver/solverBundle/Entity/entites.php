<?php

namespace solver\solverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * solver\solverBundle\Entity\entites
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="solver\solverBundle\Repository\entitesRepository")
 */
class entites
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
     * @var int $problemes;
     * 
     * @ORM\OneToMany(targetEntity="problemes", mappedBy="entite", cascade={"remove", "persist"})
     */
    private $problemes;
    
     /**
     * Indique si lentité existe encore (ne pas effacer, afin de pouvoir garder des statistiques)
     * @var boolean
     * @ORM\Column(name="existe", type="boolean", nullable=false)
     */
    private $existe; 

    
    public function __construct()
    {
        $this->problemes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add problemes
     *
     * @param solver\solverBundle\Entity\problemes $problemes
     */
    public function addproblemes(\solver\solverBundle\Entity\problemes $problemes)
    {
        $this->problemes[] = $problemes;
    }

    /**
     * Get problemes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProblemes()
    {
        return $this->problemes;
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
}