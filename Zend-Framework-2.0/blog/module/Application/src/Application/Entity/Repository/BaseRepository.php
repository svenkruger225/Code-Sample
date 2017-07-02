<?php
namespace Application\Entity\Repository;
use Doctrine\ORM\EntityRepository;
use App\Application;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * 
 * Base class for doctrine entity repositories
 * 
 * 
 */
abstract class BaseRepository extends EntityRepository {
    
    /**
     *
     * @var ServiceLocatorInterface 
     */
    protected $serviceLocator = null;
    
    /**
     * Overwrite in any child and set it to namespace
     * @var string 
     */
    protected $entityNamespace;
    
    /**
     * 
     * @return ServiceLocatorInterface
     */
    protected function getServiceLocator() {
        if ($this->serviceLocator == null) {
            $this->serviceLocator = Application::getInstance()->getServiceManager();
        }
        return $this->serviceLocator;
    }
    
    /**
     * 
     * @param string $classIdentifier
     * @return BaseRepository
     */
    public static function repositoryFor($classIdentifier) {
        $serviceLocator = Application::getInstance()->getServiceManager();
        return $serviceLocator->get($classIdentifier)->getRepository();
    }
}
