<?php
namespace Application\Entity;
use Application\Model\PropertyAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use App\Application;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Form;
use Zend\Form\Annotation;

/**
 * 
 * Basic class for busines objects
 * 
 * 
 * 
 */
abstract class Entity implements ValidateInterface {
    
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
     * 
     * @return ServiceLocatorInterface
     */
    protected function getServiceLocator() {
        if (!($this->serviceLocator instanceof ServiceLocatorInterface)) {
            $this->serviceLocator = Application::getInstance()->getServiceManager();
        }
        return $this->serviceLocator;
    }

    /**
     * Expose class properties
     * @param PropertyAwareInterface $requester
     * @return array
     */
    public function exposeProperties($requester) {
        if (in_array('PropertyAwareInterface', class_implements($requester))) {
            return array_keys(get_object_vars($this));
        }
    }

    /**
     * Bulck setter method 
     * @param array $data
     * @return Entity
     */
    public function setData($data) {
        if (is_array($data)) {
            foreach ($data as $property => $value) {
                $setter = 'set' . ucfirst($property);
                $this->$setter($value);
            }
        }
        return $this;
    }
    
    /**
     * Return object data
     * @param mixed $properties properties to retrive
     * @return array
     */
    public function getData($properties = null) {
        if (empty($properties)) {
            $properties = array_keys(get_class_vars(get_class($this)));
        }
        if (is_array($properties)) {
            $return  = array();
            foreach ($properties as $prop) {
                $return[$prop] = $this->$prop;
            }
            return $return;
        } elseif (is_string($properties)) {
            return $this->$properties;
        } else {
            return null;
        }
    }

    /**
     * Implements magic getter and setter
     * @param string $method
     * @param array $arguments
     * @return mixed It will return poperty value or accessed object
     * @throws \Exception
     */
    public function __call($method, $arguments) {
        $_prefix = strtolower( substr( $method, 0, 3 ) );
        $_property = substr( $method, 3 );

        if (empty($_prefix) || empty($_property)) {
           throw new \Exception($this->translate("Invalid getter or setter!"));
        }
        
        preg_match_all('/[A-Z][^A-Z]*/', $_property, $results);
        $_tmp = array_map('strtolower', $results[0]);
        $_property = ltrim(rtrim(join('_', $_tmp), '_'), '_');

        if ($_prefix == "get" && property_exists($this, $_property)) {
            
            return $this->$_property;
        }

        if ($_prefix == "set" && property_exists($this, $_property)) {
            $this->$_property = $arguments[0];
            return $this;
        }
    }
    
    /**
     * @return Translator
     */
    public function getTranslator() {
       $this->getServiceLocator->get('translator');
    }
    
    /**
     * Translate given string
     * @param string $text
     * @return mixed
     */
    public function translate($text) {
        if (is_string($text)) {
            return $this->getTranslator()->translate($text);
        } else {
            return $text;
        }
    }
   
    /**
     * 
     * @return EntityRepository
     */
    public function getRepository() {
        return \App\Application::getInstance()
                                ->getServiceManager()
                                ->get('doctrine.entity_menager')
                                ->getRepository(get_class($this));
    }

    /**
     * 
     * Implementation of ValidateInterface
     */
    public function validate($unsetInvalid = false) {
        return false;
    }
    
    /**
     * Retrive form for entity
     * @return Form
     */
    public function getForm() {
        if (is_null($this->form)) {
            //$object= $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $builder = new AnnotationBuilder();
            $this->form = $builder->createForm($this);
            
        }
        return $this->form;
    }
    
    /**
     * Populate application specific values for entuty creation
     */
    public function populateCreateSpecificValues() {
        return $this;
    }
    public function getSeprator(){
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')           
                return "\\";
        else
            return "/";
    }
}
