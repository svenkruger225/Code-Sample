<?php
class LevelLookup
{
    const MEMBER = 0;
    const ADMIN = 1;
    
    public static function getLabel($level)
    {
        if($level == self::MEMBER)
        {
            return 'member';
        }
        if($level == self::ADMIN)
        {
            return 'admin';
        }
        
        return false;
    }
    
    public function getLevelList()
    {
        return array(
            self::MEMBER => 'Member',
            self::ADMIN => 'Admin'
        );  
    }
}
?>
