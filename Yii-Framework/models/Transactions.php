<?php

/**
 * This is the model class for table "transactions".
 *
 * The followings are the available columns in table 'transactions':
 * @property integer $id
 * @property integer $recipient_id
 * @property integer $user_id
 * @property string $remittance_amount
 * @property string $coupon
 * @property string $date
 * @property integer $status
 * @property string $recieving_amount
 * @property string $recieving_amount_currency
 * @property string $total_payable
 */
class Transactions extends CActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Transactions the static model class
     */

    const APPROVED_TRANSACTION = 1;
    const PENDING_TRANSACTION = 0;
    const REJECTED_TRANSACTION = -1;
    const UNAPPROVED_TRANSACTION = 2;

    private $recipientName;
    private $senderEmail;

    //private $trans_status;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'transactions';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('recipient_id', 'numerical', 'min' => 1, 'tooSmall' => 'Please Select Recipient'),
            array('remittance_amount', 'required'),
            array('remittance_amount', 'numerical'),
            //array('remittance_amount, coupon, recieving_amount, recieving_amount_currency, total_charged', 'length', 'max' => 10),
            array('remittance_amount', 'maxAmount', 'amount' => 2000),
            array('id, recipient_id, user_id, remittance_amount, coupon, date, status, recieving_amount, recieving_amount_currency, total_charged, trans_status, payment_reference, bank_name, senderEmail, payment_reference, reference_question, reference_answer', 'safe', 'on' => 'search'),
        );
    }

    public function maxAmount($attribute, $params) {
        if ($this->$attribute > $params['amount']) {
            $this->addError($attribute, 'Transaction Above 2000$ is not Allowed');
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'users' => array(self::BELONGS_TO, 'User', 'user_id'),
            'recipients' => array(self::BELONGS_TO, 'Recipients', 'recipient_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'recipient_id' => 'Select Recipient',
            'user_id' => 'User',
            'remittance_amount' => 'Canadian $',
            'coupon' => 'Coupon',
            'date' => 'Date',
            'status' => 'Status',
            'recieving_amount' => 'Recieving Amount',
            'recieving_amount_currency' => 'Exchange Rate',
            'total_charged' => 'Total Recieving Amount',
            'bank_name' => 'Bank',
            'reference_question' => 'Reference Question',
            'reference_answer' => 'Reference Answer',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        //$criteria->together = true;
        //$criteria->with = 'users';
        $criteria->with = array('recipients','users');
        $criteria->compare('t.id', $this->id);
        $criteria->compare('email', $this->senderEmail, true);
        $criteria->compare('t.user_id', $this->user_id, true);
        $criteria->compare('remittance_amount', $this->remittance_amount, true);
        $criteria->compare('coupon', $this->coupon, true);
        $criteria->compare('date', $this->date, true);
        $criteria->compare('t.status', $this->status, false);
        $criteria->compare('recieving_amount', $this->recieving_amount, true);
        $criteria->compare('recieving_amount_currency', $this->recieving_amount_currency, true);
        $criteria->compare('total_charged', $this->total_charged, true);
        $criteria->compare('payment_reference', $this->payment_reference, true);
        $criteria->compare('bank_name', $this->bank_name, true);
        $criteria->compare('t.reference_question', $this->reference_question, true);
        $criteria->compare('t.reference_answer', $this->reference_answer, true);
        $criteria->addCondition('t.status <> ' . self::UNAPPROVED_TRANSACTION);
        //$criteria->compare('recipients.first_name', $this->recipientName, true);
        $criteria->addCondition("recipients.first_name like '%$this->recipientName%' OR recipients.last_name like '%$this->recipientName%'", 'AND');
        $criteria->order = 't.id DESC';
        //$criteria->condition = "status <> :status";
        //$criteria->params = array(':status' => self::UNAPPROVED_TRANSACTION);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

    public function getRecipientCurrency($recipientId) {
        $recipient = Recipients::model()->findByPk($recipientId);
        $criteria = new CDbCriteria();
        $criteria->select = "rate";
        $criteria->condition = "currency = :country";
        $criteria->params = array(':country' => $recipient->country);

        $currency = Rates::model()->find($criteria);
        //print_r($currency); exit;
        $rate = $currency->rate;
        return $rate;
    }

    public function gettrans_status() {
        switch ($this->status) {
            case(Transactions::APPROVED_TRANSACTION):
                $trans_status = 'Approved';
                break;
            case(Transactions::PENDING_TRANSACTION):
                $trans_status = 'Pending';
                break;
            case(Transactions::REJECTED_TRANSACTION):
                $trans_status = 'Rejected';
                break;

            default:
                $trans_status = '';
                break;
        }

        return $trans_status;
    }

    public function settrans_status($value) {
        $this->status = $value;
    }

    public function getRecipientName() {

        if (!empty($this->recipient_id)) {
            $recip = Recipients::model()->find('id = :id', array(':id' => $this->recipient_id));
            $this->recipientName =  $recip->first_name . " " . $recip->last_name;
        }
        return $this->recipientName;
    }
    
    public function setRecipientName($value){
        $this->recipientName = $value;
    }

    public function getsenderEmail() {
        if (!empty($this->user_id)) {
            $user = User::model()->find('id = :id', array(':id' => $this->user_id));
            if(!empty($user))
            {
                $this->senderEmail = $user->email;
            }
            return $this->senderEmail;
        } else {
            return $this->senderEmail;
        }
    }

    public function setsenderEmail($val) {
        $this->senderEmail = $val;
    }

    public function beforeSave() {
        parent::beforeSave();
        if ($this->isNewRecord) {
            $last_trans_date = $this->getRecipLastTransDate($this->recipient_id);
            //echo $last_trans_date; exit;
            if (isset($last_trans_date)) {
                $non_flagged_date = date("Y-m-d", strtotime($last_trans_date . "+14 days"));
                $today_date = date("Y-m-d");
                $recip = Recipients::model()->find('id = :id', array(':id' => $this->recipient_id));
                if (strtotime($non_flagged_date) > strtotime($today_date)) {
                    //echo $non_flagged_date." ";
                    //echo $today_date;
                    //exit;
                    $recip->flag = Recipients::FLAGGED;
                    $recip->save();
                } else {
                    $recip->flag = Recipients::NON_FLAGGED;
                    $recip->save();
                }
            }
        }

        return true;
    }

    public function getRecipLastTransDate($recip_id) {
        $date = Yii::app()->db->createCommand()
                ->select('date')
                ->from('transactions')
                ->where('recipient_id = :id', array(':id' => $recip_id))
                ->order('id DESC')
                ->queryScalar();

        return date("Y-m-d", strtotime($date));
    }

}