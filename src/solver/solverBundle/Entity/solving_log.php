<?php

namespace solver\solverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * solver\solverBundle\Entity\solving_log
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="solver\solverBundle\Repository\solving_logRepository")
 */
class solving_log
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
     * @var datetime $horodatage
     *
     * @ORM\Column(name="horodatage", type="datetime")
     */
    private $horodatage;

    /**
     * @var integer $problemeaval
     *     
     * @ORM\ManyToOne(targetEntity="problemes")
     */
    private $problemeaval;

    /**
     * @var integer $problemeamont
     *     
     * @ORM\ManyToOne(targetEntity="problemes")
     */
    private $problemeamont;

    /**
     * @var string $adresseip
     *
     * @ORM\Column(name="adresseip", type="string", length=15)
     */
    private $adresseip;

    public function _construct() {
        $this->horodatage=new \DateTime;        
        $this->adresseip=$_SERVER['REMOTE_ADDR'];
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
     * Set horodatage
     *
     * @param datetime $horodatage
     */
    public function setHorodatage($horodatage)
    {
        $this->horodatage = $horodatage;
    }

    /**
     * Get horodatage
     *
     * @return datetime 
     */
    public function getHorodatage()
    {
        return $this->horodatage;
    }

    /**
     * Set problemeaval
     *
     * @param integer $problemeaval
     */
    public function setProblemeaval($problemeaval)
    {
        $this->problemeaval = $problemeaval;
    }

    /**
     * Get problemeaval
     *
     * @return integer 
     */
    public function getProblemeaval()
    {
        return $this->problemeaval;
    }

    /**
     * Set problemeamont
     *
     * @param integer $problemeamont
     */
    public function setProblemeamont($problemeamont)
    {
        $this->problemeamont = $problemeamont;
    }

    /**
     * Get problemeamont
     *
     * @return integer 
     */
    public function getProblemeamont()
    {
        return $this->problemeamont;
    }

    /**
     * Set adresseip
     *
     * @param string $adresseip
     */
    public function setAdresseip($adresseip)
    {
        $this->adresseip = $adresseip;
    }

    /**
     * Get adresseip
     *
     * @return string 
     */
    public function getAdresseip()
    {
        return $this->adresseip;
    }
}