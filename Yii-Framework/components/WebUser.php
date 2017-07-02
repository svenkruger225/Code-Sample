<?php
class WebUser extends CWebUser
{
    protected $_model;
    public $loginUrl = "/site/userlogin";
    public function isAdmin()
    {
        $user = $this->loadUser();
        if($user)
        {
            return $user->is_admin == LevelLookup::ADMIN;
        }
    }
    public function loadUser()
    {
        if($this->_model === null)
        {
            $this->_model = User::model()->findByPk($this->id);
        }
        
        return $this->_model;
    }
}
?>
