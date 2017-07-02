<?php
namespace Application\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Repository\BaseRepository;


class CityRepository extends EntityRepository
{
    public function deleteCitites($countryId){
        $list = $this->getEntityManager()->getRepository('\Application\Entity\City')->findBy(array('country'=>$countryId));
        foreach($list as $record){
            $citylist=$this->getEntityManager()->find('\Application\Entity\City', $record->getId());
            $this->getEntityManager()->remove($citylist);
            $this->getEntityManager()->flush();
        }
    }
}
?>
