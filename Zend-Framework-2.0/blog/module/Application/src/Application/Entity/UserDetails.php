<?php

namespace Application\Entity;
use Application\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
/**
 * UserDetails
 *
 * @ORM\Table(name="user_details", indexes={@ORM\Index(name="city_id", columns={"city_id"}), @ORM\Index(name="group_id", columns={"group_id"}), @ORM\Index(name="category_id", columns={"category_id"}), @ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class UserDetails
{
   


    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=100, nullable=false)
     * 
     * @Annotation\Options({"label":"Address:"})
     *  @Annotation\Attributes({"class":"form-control"})
     * @Annotation\Required({"required":"true"})
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=25, nullable=true)
     * @Annotation\Options({"label":"Location:"})
     *  @Annotation\Attributes({"class":"form-control"})
     */
    private $location;

    /**
     * @var integer
     *
     * @ORM\Column(name="phone_no", type="integer", nullable=false)
     * @Annotation\Options({"label":"Phone No:"})
     *  @Annotation\Attributes({"class":"form-control"})
     * @Annotation\Required({"required":"true"})
     */
    private $phoneNo;

  
    /**
     * @var \Application\Entity\User
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Application\Entity\User", inversedBy="userDetails")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     * @Annotation\ComposedObject("Application\Entity\User")
     * @Annotation\Attributes({"type":"hidden"})
     */
    private $user;

    /**
     * @var \Application\Entity\City
     *@Annotation\Type("Zend\Form\Element\Select")
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\City")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * })
     * @Annotation\Options({"label":"City:"})
     *  @Annotation\Attributes({"class":"form-control"})
     * @Annotation\Required({"required":"true"})
     */
    private $city;

    /**
     * @var \Application\Entity\Group
     *@Annotation\Type("Zend\Form\Element\Select")
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Group")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     * })
     * @Annotation\Options({"label":"Group:"})
     *  @Annotation\Attributes({"class":"form-control"})
     * @Annotation\Required({"required":"true"})
     */
    private $group;

  

    /**
     * Set address
     *
     * @param string $address
     * @return UserDetails
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return UserDetails
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set phoneNo
     *
     * @param integer $phoneNo
     * @return UserDetails
     */
    public function setPhoneNo($phoneNo)
    {
        $this->phoneNo = $phoneNo;

        return $this;
    }

    /**
     * Get phoneNo
     *
     * @return integer 
     */
    public function getPhoneNo()
    {
        return $this->phoneNo;
    }


    /**
     * Set user
     *
     * @param \Application\Entity\User $user
     * @return UserDetails
     */
    public function setUser(\Application\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set city
     *
     * @param \Application\Entity\City $city
     * @return UserDetails
     */
    public function setCity(\Application\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \Application\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set group
     *
     * @param \Application\Entity\Group $group
     * @return UserDetails
     */
    public function setGroup(\Application\Entity\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Application\Entity\Group 
     */
    public function getGroup()
    {
        return $this->group;
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
      
        $this->address = (isset($data['address'])) ? $data['address'] : null;
        $this->location = (isset($data['location'])) ? $data['location'] : null;
        $this->phoneNo = (isset($data['phoneNo'])) ? $data['phoneNo'] : null;
        $this->city = (isset($data['city'])) ? $data['city'] : null;
        $this->group = (isset($data['group'])) ? $data['group'] : null;
        $this->user = (isset($data['user'])) ? $data['user'] : null;
    }
}
