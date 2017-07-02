<?php
namespace Application\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Application\Entity\Repository\BaseRepository;
use DoctrineExtensions\NestedSet\Config;
use DoctrineExtensions\NestedSet\Manager;

class CategoryRepository extends EntityRepository
{
    public $data = array();
    
    /**
     * get list of all catgeories
     * @return type
     */    
    public function getCategoryList()
    {        
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('cat1.id,cat1.name,cat2.name as root')
                ->from('Application\Entity\Category','cat1')
                ->join('Application\Entity\Category', 'cat2',  'WITH', 'cat1.root = cat2.id')
                ->orderBy('cat1.root,cat1.lft', 'asc');
        $result = $queryBuilder->getQuery()->getArrayResult();
        return $result;
    }

    /**
     * get root nodes
     */
    public function getRootNodes()
    {
         $qb = $this->_em->createQueryBuilder();
            $qb->select('c')
                ->from('Application\Entity\Category', 'c')
                ->groupBy('c.root');
        return $qb->getQuery()->getResult();
    }
    
    /**
     * get category for select
     */    
    public function getCategoriesForSelect(){
        $allCategories = $this->getRootNodes();
        $categories[] = "Parent Level Category"; 
        foreach ($allCategories as $category)
        {
            $categories[$category->getId()] = $category->getName(); 
        }
        return $categories;
    }
    /**
     * get options for select
     */
    
    public function getOptionsForSelect(){
        $allCategories = $this->getRootNodes();
        foreach ($allCategories as $category)
        {
            $categories[$category->getId()] = $category->getName(); 
        }
        return $categories;
    }
    //category show
    public function getOptionsForSelectGroup()
    {
        $groupList = $this->getEntityManager()->getRepository('Application\Entity\Group')->findAll();
        $groups = array();
        $groups[''] = 'Select Group';
        foreach ($groupList as $group)
        {
            $groups[$group->getId()] = $group->getName();
        }
        return $groups;
    }
    public function getParentChildCategory($parentId){
        
        $categories = $this->_em->getRepository('\Application\Entity\Category')->findBy(array("parentId" => $parentId));
        foreach ($categories as $category)
        {
            if($category->getParentId()==null)
            {
                $this->data[$category->getId()] = array('id'=>$category->getId(),'name'=>$category->getName());
            }
            else
            {
                $this->data[$category->getParentId()->getId()]['child'][$category->getId()] = array('id'=>$category->getId(),'name'=>$category->getName());
            }
            $this->getParentChildCategory($category->getId());    
        }
        return $this->data;
    }
    public function getRootNodesAgainstCountry($countryId,$cityId)
    {
        $qb = $this->_em->createQueryBuilder();
            $qb->select('c')
                ->from('Application\Entity\Category', 'c')
                ->join('Application\Entity\CountryCategory','countryC', 'WITH', 'c.id = countryC.category')
                ->groupBy('c.root')
                ->where('countryC.country= ?1')
                ->setParameter(1,$countryId);
        $result = $qb->getQuery()->getArrayResult();
        $categories=array();
        $qbCount = $this->_em->createQueryBuilder();
        $qbCount->select("c.id,Count(insCategory.id) as instituteNo")
            ->from('Application\Entity\Category', 'c')
            ->join('Application\Entity\InstituteCategory','insCategory', 'WITH', 'c.id = insCategory.category')
            ->join('Application\Entity\InstituteLocation', 'insLocation',  'WITH', 'insCategory.institute = insLocation.institute')
            ->join('Application\Entity\Institute', 'ins',  'WITH', 'insLocation.institute = ins.id')
            ->groupBy('insCategory.category')
            ->where('insLocation.city= ?1')
            ->andWhere('ins.status= ?2')
            ->setParameter(1,$cityId)
            ->setParameter(2,1);
        $resultCount = $qbCount->getQuery()->getArrayResult();
        $instituteCount=array();
        foreach($resultCount as $institute):
            $instituteCount[$institute['id']]=$institute['instituteNo'];
        endforeach;
        foreach ($result as $category)
        {
            $id=$category['id'];
            $categories[$id]['name'] = $category['name']; 
            if(array_key_exists($id,$instituteCount)):
                $categories[$id]['count']=$instituteCount[$id];
            else:
                $categories[$id]['count']=0;
            endif;
        }
        return $categories;
    }
    public function getParentOrChildNodes($countryId,$cityId,$categoryId)
    {
        $config = new Config($this->_em, 'Application\Entity\Category');
        $nsm = new Manager($config);
        if(!empty($categoryId)){
            $category = $this->_em->find('Application\Entity\Category',$categoryId);
            $node = $nsm->wrapNode($category);
            $isLeaf = $node->isLeaf();
            $isRoot = $node->isRoot();
        }
        
        if(!empty($categoryId) && $isRoot==true){
           $categories=$this->categoryWithCount($countryId,$cityId,$categoryId);
           return $categories;
        }
        else if(!empty($categoryId) && $isLeaf==true){
           $categoryId=$node->getParent()->getNode()->getId();
           $categories=$this->categoryWithCount($countryId,$cityId,$categoryId);
           return $categories;
        }
        else{
            $qb = $this->_em->createQueryBuilder();
            $qb->select('c')
                ->from('Application\Entity\Category', 'c')
                ->join('Application\Entity\CountryCategory','countryC', 'WITH', 'c.id = countryC.category')
                ->groupBy('c.root')
                ->where('countryC.country= ?1')
                ->setParameter(1,$countryId);
            $result = $qb->getQuery()->getArrayResult();
            
            foreach ($result as $category)
            {
                $id=$category['id'];
                $categories[$id]['name'] = $category['name']; 
                $categoryList=$this->categoryWithCount($countryId,$cityId,$id);
                $totalCount=0;
                foreach($categoryList as  $key=>$ctg){
                     $totalCount=$totalCount+$ctg['count'];
                }
                $categories[$id]['count']=$totalCount;
            }
            return $categories;
        }
    }
    public function categoryWithCount($countryId,$cityId,$categoryId){
          $qb = $this->_em->createQueryBuilder();
            $qb->select('c')
                ->from('Application\Entity\Category', 'c')
                ->join('Application\Entity\CountryCategory','countryC', 'WITH', 'c.id = countryC.category')
                ->where('countryC.country= ?1')
                ->andWhere('c.root= ?2')
                ->setParameter(1,$countryId)
                ->setParameter(2,$categoryId);
            $result = $qb->getQuery()->getArrayResult();
            $categories=array();
            $qbCount = $this->_em->createQueryBuilder();
            $qbCount->select("c.id,Count(crsCategory.id) as instituteNo")
                ->from('Application\Entity\Category', 'c')
                ->join('Application\Entity\CourseCategory','crsCategory', 'WITH', 'c.id = crsCategory.category')
                ->join('Application\Entity\Course','course', 'WITH', 'crsCategory.course = course.id')
                ->join('Application\Entity\InstituteLocation', 'insLocation',  'WITH', 'course.institute = insLocation.institute')
                ->groupBy('crsCategory.category')
                ->where('insLocation.city= ?1')
                ->setParameter(1,$cityId);
            $resultCount = $qbCount->getQuery()->getArrayResult();
            $instituteCount=array();
            foreach($resultCount as $institute):
                $instituteCount[$institute['id']]=$institute['instituteNo'];
            endforeach;
            foreach ($result as $category)
            {
                $id=$category['id'];
                $categories[$id]['name'] = $category['name']; 
                if(array_key_exists($id,$instituteCount)):
                    $categories[$id]['count']=$instituteCount[$id];
                else:
                    $categories[$id]['count']=0;
                endif;
            }
            return $categories;
    }
    public function remove($id){
        $childcategories = $this->getEntityManager()->getRepository('Application\Entity\Category')->findBy(array("root" => $id));
        foreach ($childcategories as $child)
        {
            $category = $this->_em->find('\Application\Entity\Category',$child);
            $this->_em->remove($category);
            $this->_em->flush();
        }
        $parentCategory = $this->_em->find('\Application\Entity\Category',$id);
        if(!empty($parentCategory))
        {
            $this->_em->remove($parentCategory);
            $this->_em->flush();
        }
        return true;
    }
}
?>