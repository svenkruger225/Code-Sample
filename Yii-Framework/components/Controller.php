<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

        public function  beforeAction($action)
        {
            if(Yii::app()->user->isAdmin())
            {
                $this->layout='//layouts/column2';
            }
            else
            {
                $this->layout='//layouts/thamour';
            }
            return parent::beforeAction($action);
        }
        
        public function shortSafety()
        {
            $model = Pages::model()->find("id = 3");
            $shortSafety = strip_tags(substr($model->page_content, 0, 250));
            
            return $shortSafety;
        }
        
        public function shortTesti()
        {
            $model = Pages::model()->find("id = 5");
            $shortSafety = strip_tags(substr($model->page_content, 0, 250));
            
            return $shortSafety;
        }

}