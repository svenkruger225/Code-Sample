<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class MyActiveRecord extends CActiveRecord{

    public function behaviors() {
        return array(
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created_at',
                'updateAttribute' => 'updated_at',
                'timestampExpression' => 'date("Y-m-d H:i:s")',
            ),
            /*
           'CAdvancedArBehavior' => array(
            'class' => 'application.extensions.arbehaviour.CAdvancedArBehavior')
             * 
             */
        );
    }

    public function filterResult($result,$column){
        $filteredData = array();
        foreach($result as $data){
            $filteredData[] = $data->$column;
        }
        return $filteredData;
    }

    static public function Slugify($text)
    {
        $text = str_replace(array(".","£"),array("DOT","pound"),$text);
        // replace all non letters or digits by -
        $text = preg_replace('/\W+|_/', '-', $text);
        $text = str_replace("DOT", ".", $text);
        // trim and lowercase
        $text = strtolower(trim($text, '-'));
        $length = strlen($text);
        if ($length >= 100)
        {
            $text  = rtrim(substr($text, 0,100),"-");
        }
        return $text;
    }
    /**
     * calculate last X days from today
     * @param integer $x
     * @return array
     */

    public function getLastXDates($x){
        $dates = array();
        for($i=1;$i<=$x;$i++){
            $dates[] = date('Y-m-d',  strtotime("-$i days"));
        }
        return $dates;
    }

    public function getLastXMonths($x){
        $months = array();
        $currentMonthFirst = date("Y-m-01");
        for($i=1;$i<=$x;$i++){
            $months[] = date('Y-m', strtotime("-$i month",strtotime($currentMonthFirst)));
        }
        return $months;
    }


}

?>
