<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Entity\Entity;
use Zend\Form\Annotation;

/**
 * Country
 *
 * @ORM\Table(name="country")
 * @ORM\Entity
 */
class Country extends Entity
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
     * @ORM\Column(name="code", type="string", length=25, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, nullable=true)
     */
    private $status = 'inactive';

    /**
     * @var string
     *
     * @ORM\Column(name="currency_code", type="string", length=25, nullable=false)
     */
    private $currencyCode;



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
     * @return Country
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
     * Set code
     *
     * @param string $code
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Country
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
     * Set currencyCode
     *
     * @param string $currencyCode
     * @return Country
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * Get currencyCode
     *
     * @return string 
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }
    
     public function getArrayCopy()
    {
        $result = get_object_vars($this);
        return $result;
    }
    public function exchangeArray($data = array())
    {
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->code = (isset($data['code'])) ? $data['code'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->currencyCode = (isset($data['currencyCode'])) ? $data['currencyCode'] : null;
        
    }   
}
