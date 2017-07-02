<?php

namespace Application\Entity;

use Application\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * Group
 *
 * @ORM\Table(name="`group`")
 * @ORM\Entity
 * @Annotation\Attributes({"class":"form-horizontal form-row-seperated" ,"enctype":"multipart/form-data"})
 */
class Group extends Entity 
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
     * @Annotation\Attributes({"type":"hidden"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
      * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Options({"label":"Name:"})
     * @Annotation\Required({"required":"true"})
     * @Annotation\Attributes({"class":"form-control"})
     */
    private $name;



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
     * @return Group
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
     * Get an array copy of object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $result = get_object_vars($this);
        return $result;
    }
    public function exchangeArray($data = array())
    {
        $this->name = (isset($data['name'])) ? $data['name'] : null;
    }
}
