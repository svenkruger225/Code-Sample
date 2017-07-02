<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Entity\Entity;
use Zend\Form\Annotation;

/**
 * City
 *
 * @ORM\Table(name="city", indexes={@ORM\Index(name="country_id", columns={"country_id"})})
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\CityRepository")
 */
class City extends Entity
{
    /*     * ******** PROPERTIES ********* */

    /**
     *
     * @var ServiceLocatorInterface
     *
     * @Annotation\Exclude()
     */
    protected $serviceLocator;
    /**
     *
     * @var Form
     *
     * @Annotation\Exclude()
     */
    protected $form = null;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, nullable=true)
     */
    private $status = 'inactive';

    /**
     * @var \Application\Entity\Country
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    private $country;



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
     * Set name
     *
     * @param string $name
     * @return City
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return City
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set country
     *
     * @param \Application\Entity\Country $country
     * @return City
     */
    public function setCountry(\Application\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \Application\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }
    public function getArrayCopy()
    {
        $result = get_object_vars($this);
        return $result;        
    }
    
}
