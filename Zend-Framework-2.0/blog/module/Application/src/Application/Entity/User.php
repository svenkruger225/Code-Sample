<?php
namespace Application\Entity;

use Application\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use ZfcUser\Entity\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"username"}), @ORM\UniqueConstraint(name="email", columns={"email"})})
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Attributes({"type":"hidden"})
     */
    private $userId;
    /**
     * @param \Doctrine\Common\Collections\Collection $property
     * @ORM\OneToOne(targetEntity="Application\Entity\UserDetails", mappedBy="user")
     * @Annotation\Exclude()
     */
    private $userDetail;
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=true)
     * 
     * @Annotation\Options({"label":"User Name:"})
     *  @Annotation\Attributes({"class":"form-control"})
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true , unique=true)
     * @Annotation\Options({"label":"Email:"})
     *  @Annotation\Attributes({"class":"form-control"})
     * @Annotation\Required({"required":"true"})
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Validator({"name":"EmailAddress"})
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=50, nullable=true)
     * @Annotation\Options({"label":"Name:"})
     *  @Annotation\Attributes({"class":"form-control"})
     */
    private $displayName;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=128, nullable=false)
     * @Annotation\Type("Zend\Form\Element\Password")
     * @Annotation\Options({"label":"Password:"})
     *  @Annotation\Attributes({"class":"form-control"})
     * @Annotation\Validator({"name":"StringLength", "options": {"min":"2"}})
     * @Annotation\Required({"required":"true"})
     */
    private $password;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="smallint", nullable=true)
     * 
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"State:"})
     *  @Annotation\Attributes({"class":"form-control"})
     */
    private $state='0';
  
    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }
    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * Set status
     *
     * @param boolean $status
     * @return User
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getState()
    {
        return $this->state;
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
        unset($this->userDetail);
        $this->userId = (isset($data['userId'])) ? $data['userId'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->password = (isset($data['password'])) ? md5($data['password']) : null;
        $this->displayName = (isset($data['displayName'])) ? $data['displayName'] : null;
        $this->state = (isset($data['state'])) ? $data['state'] : null;
    }

    public function setId($id)
    {
        $this->userId = $id;
        return $this;
    }
    public function getId()
    {
        return $this->userId;
        
    }
    /**
     * Set user
     *
     * @param \Application\Entity\UserDetails $user
     * @return User
     */
    public function setUserDetail(\Application\Entity\User $user = null)
    {
        $this->userDetail = $user;

        return $this;
    }
    /**
     * Get user
     *
     * @return \Application\Entity\UserDetails 
     */
    public function getUserDetail()
    {
       return $this->userDetail;
        
    }
}
