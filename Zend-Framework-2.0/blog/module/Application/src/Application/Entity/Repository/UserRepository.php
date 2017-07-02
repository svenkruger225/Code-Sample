<?php
namespace Application\Entity\Repository;
use Doctrine\ORM\EntityRepository;
use Application\Entity\Repository\BaseRepository;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\AuthenticationService;

class UserRepository extends EntityRepository
{
    public function authenticate($adapter,$auth,$emailId,$password){
        $authAdapter = new AuthAdapter($adapter, 'user', 'email', 'password','MD5(?) and state = 1');

        $authAdapter->setIdentity($emailId)
                ->setCredential($password);

        $result = $auth->authenticate($authAdapter);

        if ($result->isValid())
        {
            $userObject = $authAdapter->getResultRowObject(null, array('password'));
            $user = $this->getEntityManager()->find('\Application\Entity\User', $userObject->user_id);
            return $user;
        }
        else
        {
            return false;
        }
    }
}
?>
