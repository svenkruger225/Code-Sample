<?php

/**
 * This is the model class for table "countries".
 *
 * The followings are the available columns in table 'countries':
 * @property integer $id
 * @property string $name
 */
class Countries extends CActiveRecord
{
        const COUNTRY_SRILANKA = "Srilanka";
        const COUNTRY_INDIA = "India";
        const COUNTRY_UK = "United Kingdom";
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Countries the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'countries';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
                    'countries' => array(self::BELONGS_TO, 'Recipients', 'country_id'),
                    'cities' => array(self::BELONGS_TO, 'Cities', 'country_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        public static function getBankList($country)
        {
            switch ($country)
            {
                case self::COUNTRY_SRILANKA:
                        return array(
                            "Commercial_Bank"=>"Commercial Bank",
                            "Bank_of_Ceylon"=>"Bank of Ceylon",
                            "Sampath_Bank"=>"Sampath Bank",
                            "Hatton_National_Bank"=>"Hatton National Bank",
                            "Seylan_Bank"=>"Seylan Bank",
                            "National_Savings_Bank"=>"National Savings Bank",
                            "Peoples_Bank" =>"People's Bank",
                            "Nations_Trust_Bank"=>"Nations Trust Bank",
                            "HSBC_Bank"=>"HSBC Bank",
                            "National_Development_Bank"=>"National Development Bank"
                            );
                    break;
                case self::COUNTRY_INDIA:
                        return array(
                            "Bank_of_India"=>"Bank of India",
                            "ICICI_Bank"=>"ICICI Bank",
                            "Indian_Bank"=>"Indian Bank",
                            "State_Bank_of_India"=>"State Bank of India",
                            "Union_Bank_of_India"=>"Union Bank of India"
                        );
                    break;
            }
        }

        public static function getCityListDelivery($country)
        {
            switch($country)
            {
                case self::COUNTRY_SRILANKA:
                    return array(
                        "Colombo"=>"Colombo",
                        "Jaffna"=>"Jaffna",
                    );
                    break;
               case self::COUNTRY_INDIA:
                   return array(
                       "Chennai"=>"Chennai",
                       "Trichy"=>"Trichy"
                   );
                   break;
               
            }
        }
        
        public static function getCityListPickup($country)
        {
            switch($country)
            {
                case self::COUNTRY_SRILANKA:
                    return array(
                        "Colombo"=>"Colombo",
                        "Jaffna"=>"Jaffna",
                        "Vavuniya"=>"Vavuniya",
                        "Trincomalae"=>"Trincomalae",
                        "Kilinochchi"=>"Kilinochchi"
                    );
                    break;
               case self::COUNTRY_UK:
                   return array(
                       "Wembly"=>"Wembly",
                       "Tooting"=>"Tooting",
                       "Victoria"=>"Victoria",
                   );
                   break;

            }
        }

}