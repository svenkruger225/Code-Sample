<?php

class TransactionsController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'view'),
                'users' => array('@'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update', 'verifypayment', 'delete', 'viewtrans'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete', 'verifypayment', 'admin', 'approve', 'reject'),
                'expression' => '$user->isAdmin()',
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $this->pageTitle = "Thamor - Send Money";
        $model = new Transactions('search');
        $criteria = new CDbCriteria;
        $criteria->condition = 'user_id = :user_id';
        $criteria->params = array(':user_id' => Yii::app()->user->id);
        $recipients = Recipients::model()->findAll($criteria);
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if (isset($_GET['id'])) {
            $model->recipient_id = $_GET['id'];
            $recpCurrency = Transactions::model()->getRecipientCurrency($_GET['id']);
            $model->recieving_amount_currency = $recpCurrency;
        }

        if (isset($_POST['Transactions'])) {
            $model->recipient_id = $_POST['Transactions']['recipient_id'];
            $model->remittance_amount = $_POST['Transactions']['remittance_amount'];
            if ($model->validate(array('recipient_id', 'remittance_amount'))) {
                $tmpString = "";
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                $length = 5;
                for ($p = 0; $p < $length; $p++) {
                    $tmpString .= $characters[mt_rand(0, strlen($characters) - 1)];
                }
                $code = "thamor" . $tmpString;
                $model->payment_reference = $code;
                $model->user_id = Yii::app()->user->id;
                $model->attributes = $_POST['Transactions'];
                $model->date = date('Y-m-d h:i:s');
                $model->status = Transactions::UNAPPROVED_TRANSACTION;
                if ($model->save()) {
                    $this->redirect(array('transactions/verifyPayment/id/' . $model->id));
                } else {
                    print_r($model->getErrors());
                    exit;
                }
            }
        }

        $this->render('create', array(
            'model' => $model,
            'recipients' => $recipients,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Transactions'])) {
            $model->attributes = $_POST['Transactions'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $this->loadModel($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(array('transactions/create'));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $dataProvider = new CActiveDataProvider('Transactions');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {

        $model = new Transactions('search');
        $model->unsetAttributes();  // clear any default values
        $model->setsenderEmail("");

        if (isset($_GET['Transactions'])) {
            $model->attributes = $_GET['Transactions'];
            if (isset($_GET['Transactions']['senderEmail'])) {
                $model->setsenderEmail($_GET['Transactions']['senderEmail']);
            }
        }


        $this->render('admin', array(
            'model' => $model,
        ));
    }

    public function actionViewTrans($id) {
        $this->pageTitle = "Thamor - View Transacrions";
        if ($id != Yii::app()->user->id) {
            die("<h3>You Are Trying To Access Other User Transactions. Please Don't Repeat This Practice Again. We Have Recorded Your IP Address<h3>");
        }
        $model = new Transactions('search');
        $model->unsetAttributes();  // clear any default values
        $model->user_id = $id;
        $model->status = Transactions::PENDING_TRANSACTION;
        if (isset($_GET['Transactions'])) {
            $model->attributes = $_GET['Transactions'];
            $model->setRecipientName($_GET['Transactions']['recipientName']);
        }
        $this->render('view_trans', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Transactions::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'transactions-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionVerifyPayment($id) {
        $this->pageTitle = "Thamor - Verify Payment";
        $userId = Yii::app()->user->id;
        $userTrans = Yii::app()->db->createCommand()
                ->select('id')
                ->from('transactions')
                ->where('user_id = :id', array(':id' => $userId))
                ->queryAll();
        $match = false;
        foreach ($userTrans as $trans) {
            if ($trans['id'] == $id) {
                $match = true;
            }
        }
        if ($match == false) {
            die("<h3>You Are Trying To Access Other User Transactions. Please Don't Repeat This Practice Again. We Have Recorded Your IP Address<h3>");
        }
        $transModel = $this->loadModel($id);
        $recipientModel = Recipients::model()->find('id = :id', array(':id' => $transModel->recipient_id));
        $userModel = User::model()->find('id = :id', array(':id' => Yii::app()->user->id));
        $bankError = "";
        $verifyError = "";
        $questionError = "";
        $answerError = "";
        if (isset($_POST['verify'])) {
            if (isset($_POST['verify']['agree'])) {
                $verify = $_POST['verify']['agree'];
            } else {
                $verify = "";
            }
            if (empty($verify)) {
                $verifyError = "Please Agree With Terms And Conditions";
            }
            if (empty($_POST['verify']['referecne_question'])){
                $questionError = "Please Enter Reference Question";
            }
            if(empty($_POST['verify']['reference_answer'])){
                $answerError = "Please Enter Reference Answer";
            } 
            if(!empty($verify) && !empty($_POST['verify']['referecne_question']) && !empty($_POST['verify']['reference_answer']))
            {
                $transModel->status = Transactions::PENDING_TRANSACTION;
                $transModel->reference_question = $_POST['verify']['referecne_question'];
                $transModel->reference_answer = $_POST['verify']['reference_answer'];
                $transModel->save();
                $userModel->reference_question = $_POST['verify']['referecne_question'];
                $userModel->reference_answer = $_POST['verify']['reference_answer'];
                $userModel->save();
                $emailTemplate = $this->renderPartial('admin_email_template', array(
                    'trans' => $transModel,
                    'recip' => $recipientModel,
                    'user' => $userModel,
                        ), true, false);
                $email = Yii::app()->email;
                $email->from = "webmaster@thamor.com";
                $email->to = Yii::app()->params['adminEmail'];
                $email->subject = 'New Transaction';
                $email->message = 'New transaction has been made. The transaction details are as follows<br><br>' . $emailTemplate;
                $email->send();
                $this->render('trans_success');
                exit;
            }
        }

        $this->render('verify_payment', array(
            'trans' => $transModel,
            'recip' => $recipientModel,
            'sender' => $userModel,
            'bError' => $bankError,
            'aError' => $verifyError,
            'questionError' => $questionError,
            'answerError' => $answerError
        ));
    }

    public function actionApprove($id) {
        $transModel = $this->loadModel($id);
        $dob = date("Y-F-d",  strtotime($transModel->users->dob));
        $dob = explode("-", $dob);
        //print_r($dob); exit;
        if($transModel->recipients->country == 'Srilanka'){
            $recpCountry = str_replace("il", "i L", $transModel->recipients->country);
        } else {
            $recpCountry = $transModel->recipients->country;
        }
        $transModel->status = 1;
        if ($transModel->save()) {
            $email = Yii::app()->email;
            $email->from = "webmaster@thamor.com";
            $email->to = $transModel->users->email;
            $email->subject = 'Transaction Processed';
            $email->message = $emailTemplate = $this->renderPartial('user_email_template', array(
                'trans' => $transModel,
                'recip' => $transModel->recipients,
                'user' => $transModel->users,
                    ), true, false);
            ;
            $email->send();
            $data = array(
                'Create' =>"true",
                'user_name' => "iThamor",
                'city'=> $transModel->recipients->city,
                'rate1'=> $transModel->recieving_amount_currency,
                'sfee1'=> 10,
                'cnd'=> $transModel->remittance_amount,
                'totalForeign1'=> $transModel->recieving_amount,
                'rname'=> $transModel->recipients->first_name . ' ' . $transModel->recipients->last_name,
                'rid'=> $transModel->recipients->ic_ac_num,
                'radd'=> $transModel->recipients->address1 . ' ,' . $transModel->recipients->bank,
                'rcity'=> $transModel->recipients->address1,
                'rcountry'=> $recpCountry,
                'rphone'=> $transModel->recipients->phone,
                'note'=> $transModel->recipients->notes,
                'sname'=> $transModel->users->first_name . ' ' . $transModel->users->last_name,
                'idtype'=> $transModel->users->id_type,
                'sid'=> $transModel->users->id_number,
                'place_issue'=> $transModel->users->id_issuance_place,
                'sadd'=> $transModel->users->address,
                'scity'=> $transModel->users->city,
                'spostalCode'=> $transModel->users->postal_code,
                'sprovince'=> $transModel->users->province,
                'scountry'=> $transModel->users->country,
                'sphone'=> $transModel->users->phone_number,
                'soccup'=> $transModel->users->occupation,
                'spurpose'=> "0",
                'DOBYear'=> $dob[0],
                'DOBMonth'=> $dob[1],
                'DOBDay'=> $dob[2],
                'delmethod'=> $transModel->recipients->payment_method,
                'paymethod'=> $transModel->recipients->payment_method,
                'transaction_id'=> $transModel->id ,
                'a_dbt'=> 0,
                'a_sfee'=>0,
                'a_total'=>0,
                'a_paid'=>0,
                'referal'=>0
            );
            $jsonData = json_encode($data);

            Yii::app()->clientScript->registerScript(
                    'savedata', '$.ajax({
                        type : "POST",
                        url : "http://thamorgroup.com/mampalam/check.php",
                        success : function(data){
                            alert("request completed:"+data);
                        },
                        data : '.$jsonData.',
                     })', CClientScript::POS_LOAD
            );
        }
        $this->render("approved_transaction", array('trans' => $transModel, 'user' => $transModel->users, 'recip' => $transModel->recipients));
    }

    public function actionReject($id) {
        $transModel = $this->loadModel($id);
        $recipientModel = Recipients::model()->find('id = :id', array(':id' => $transModel->recipient_id));
        $userModel = User::model()->find('id = :id', array(':id' => Yii::app()->user->id));
        $transModel->status = -1;
        if ($transModel->save()) {
            $email = Yii::app()->email;
            $email->from = "webmaster@thamor.com";
            $email->to = Yii::app()->params['adminEmail'];
            $email->subject = 'Transaction Declined';
            $email->message = 'It is to Inform that one of your transaction has been declined by the Thamor. The details of the declined transaction are as followed<br><br>
                        Sender Name : ' . $transModel->users->first_name . ' ' . $transModel->users->last_name . '<br>
                        Reciever Name: ' . $transModel->recipients->first_name . ' ' . $transModel->recipients->last_name . '<br>
                        Remittance Amount: ' . $transModel->remittance_amount . '$ <br>
                        Exchange Rate: ' . $transModel->recieving_amount_currency . '<br>
                        Service Fee: 10$<br>
                        Total Charged:  ' . $transModel->total_charged . '<br>
                        Recieving Amount:  ' . $transModel->recieving_amount . '<br><br><br>

                        For Furthur Questions Please Forward Your Queries To ' . Yii::app()->params['adminEmail'];
            $email->send();
        }
    }

    public function convertStatusToValue($obj) {
        switch ($obj->status) {
            case Transactions::APPROVED_TRANSACTION:
                return "Approved";
                break;
            case Transactions::PENDING_TRANSACTION:
                return "Pending";
                break;
            case Transactions::REJECTED_TRANSACTION:
                return "Rejected";
                break;
            default:
                return "Error";
                break;
        }
    }

}
