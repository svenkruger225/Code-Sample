<?php
namespace Application\Entity;

use DoctrineExtensions\NestedSet\MultipleRootNode;
use Application\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Category
 *
 * @ORM\Table(name="category", indexes={@ORM\Index(name="parent", columns={"parent_id"})})
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\CategoryRepository")
 *  
 */
class Category extends Entity implements MultipleRootNode
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
     * 
     * @Annotation\Attributes({"type":"hidden"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     * 
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Options({"label":"Name:"})
     * @Annotation\Required({"required":"true"})
     * @Annotation\Attributes({"class":"form-control"})
     * @Annotation\AllowEmpty
     */
    private $name;

    /**
     * @ORM\Column(name="root", type="integer", nullable=true)
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Options({"label":"Parent Category"})
     * @Annotation\Attributes({"class":"form-control"})
     */
    private $root;
        
    
    //its important to use lft and rgt as left and right are sql reserved words
    
    /**
     * @ORM\Column(type="integer")
     * @Annotation\Exclude()
     */
    private $lft;
    
    /**
     * @ORM\Column(type="integer")
     * @Annotation\Exclude()
     */
    private $rgt;

    

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
     * @return Category
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
        $this->root = (isset($data['parentId'])) ? $data['parentId'] : null;
        
    }

    public function __toString()
    {
        
    }

    public function getLeftValue()
    {
        return $this->lft;
    }

    public function getRightValue()
    {
        return $this->rgt;
    }

    public function getRootValue()
    {
        return $this->root;        
    }

    public function setLeftValue($left)
    {
        $this->lft = $left;
    }

    public function setRightValue($right)
    {
        $this->rgt = $right;
    }

    public function setRootValue($root)
    {
        $this->root = $root;        
    }

}
