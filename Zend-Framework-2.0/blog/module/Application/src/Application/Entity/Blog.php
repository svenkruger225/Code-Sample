<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Entity\Entity;
use Zend\Form\Annotation;

/**
 * Blog
 *
 * @ORM\Table(name="blog")
 * @ORM\Entity
 */
class Blog extends Entity
{
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
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     * 
     * @Annotation\Options({"label":"Title:"})
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Attributes({"class":"form-control"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", length=65535, nullable=false)
     * 
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Options({"label":"Content:"})
     * @Annotation\Attributes({"id": "content", "class":"form-control js-example-basic-fontstyle","style":"height:170px"})
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="sef", type="string", length=255, nullable=false)
     * 
     * @Annotation\Options({"label":"Sef:"})
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Attributes({"class":"form-control"})
     */
    private $sef;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text", length=65535, nullable=false)
     * 
     * @Annotation\Options({"label":"Meta Description:"})
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Attributes({"class":"form-control"})
     */
    private $metaDescription;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Application\Entity\Category", inversedBy="blog")
     * @ORM\JoinTable(name="blog_category",
     *   joinColumns={
     *     @ORM\JoinColumn(name="blog_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *   }
     * )
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Options({"label":"Catgeory:"})
     * @Annotation\Attributes({"class":"form-control"})
     * @Annotation\Attributes({"multiple":"multiple"})
     * @Annotation\Required({"required":"true"})
     */
    private $category;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->category = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Blog
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Blog
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set sef
     *
     * @param string $sef
     *
     * @return Blog
     */
    public function setSef($sef)
    {
        $this->sef = $sef;

        return $this;
    }

    /**
     * Get sef
     *
     * @return string
     */
    public function getSef()
    {
        return $this->sef;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     *
     * @return Blog
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }
     /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }
    public function addCategory($category)
    {
      $this->category[] = $category;
    }
    
    public function removeAllCategory()
    {
      $this->category = new \Doctrine\Common\Collections\ArrayCollection();
    }
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    public function exchangeArray($data)
    {
        $this->title = isset($data['title'])?$data['title']:null;
        $this->content = isset($data['content'])?$data['content']:null;
        $this->sef = isset($data['sef'])?$data['sef']:null;
        $this->metaDescription= isset($data['metaDescription'])?$data['metaDescription']:null;
    }
}
