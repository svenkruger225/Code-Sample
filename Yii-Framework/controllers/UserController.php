<?php

class UserController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/thamour';

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
                'actions' => array('view', 'create', 'success', 'forgotpassword', 'recoverpassword'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('view', 'success', 'forgotpassword', 'recoverpassword', 'update'),
                'users' => array('*'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'approveuser', 'bannuser', 'approved', 'banned', 'delete', 'view', 'update', 'index', 'pending', 'create'),
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
        $this->layout = '//layouts/column2';
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $this->pageTitle = "Thamor - Register";
        $model = new User('register');
        $model->country = "Canada";

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['User'])) {

            $model->attributes = $_POST['User'];
            // echo $model->dob; exit;
            if ($model->validate()) {
                $model->password = md5($model->password);
                $model->updated_at = date("Y-m-d H:i:s");
                $model->status = USER::USER_PENDING;

                $model->save();
                Yii::app()->user->setFlash('register', 'Registation Successful! You can login after we approve your application within 24 Hours.');
                if (Yii::app()->user->isAdmin()) {
                    $this->redirect(array('/user/admin'));
                } else {
                    $this->redirect(array('/user/success'));
                }
            }
        }
        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionSuccess() {
        //$this->layout = '//layouts/column2';
        $messages = Yii::app()->user->getFlashes();

        if (isset($messages)) {
            $this->render('createsuccess', array('messages' => $messages));
        } else {
            $this->redirect(array('/site/index'));
        }
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $this->pageTitle = "Thamor - Update User";
        $model = $this->loadModel($id);
        $password = $model->password;
        $oldPassError = "";
        $newPassError = "";
        $confirmPassError = "";
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['User'])) {
            $errorFlag = false;
            //print_r($_POST['User']); exit;
            $oldPass = $_POST['User']['old_pass'];
            $newPass = $_POST['User']['new_pass'];
            $confirmPass = $_POST['User']['confirm_pass'];
            if (!empty($oldPass) || !empty($newsPass) || !empty($confirmPass)) {
                if (empty($oldPass) || empty($newPass) || empty($confirmPass)) {
                    if (empty($oldPass)) {
                        $errorFlag = true;
                        $oldPassError = "Old Password Cannot Be Empty";
                    } else {
                        $oldPass = md5($oldPass);
                    }
                    if (empty($newPass)) {
                        $errorFlag = true;
                        $newPassError = "New Password Cannot Be Empty";
                    }
                    if (empty($confirmPass)) {
                        $errorFlag = true;
                        $confirmPassError = "Confirm Password Cannot be Empty";
                    }
                } elseif (md5($oldPass) != $password) {
                    $errorFlag = true;
                    $oldPassError = "You have not Entered Correct Old Password";
                } elseif ($newPass != $confirmPass) {
                    $errorFlag = true;
                    $newPassError = "New Password Doesn't Match With Confirm Password";
                }
                $model->password = md5($newPass);
            }
            $model->attributes = $_POST['User'];
            if ($errorFlag == false && $model->save()) {

                if (Yii::app()->user->isAdmin()) {
                    $this->redirect(array('view', 'id' => $model->id));
                } else {
                    Yii::app()->user->setFlash('udpate', 'Profile is Successfully Updated');
                    $this->redirect(array('update', 'id' => $model->id));
                }
            }
        }
        //print_r($model->getErrors());exit;

        $this->render('update', array(
            'model' => $model,
            'oldPassError' => $oldPassError,
            'newPassError' => $newPassError,
            'confirmPassError' => $confirmPassError,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        $this->layout = '//layouts/column2';
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $this->loadModel($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        //$this->layout = '//layouts/column2';
        $dataProvider = new CActiveDataProvider('User');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $this->layout = '//layouts/column2';

        $model = new User('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['User']))
            $model->attributes = $_GET['User'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        if ($id == Yii::app()->user->id || Yii::app()->user->isAdmin()) {
            $model = User::model()->findByPk($id);
            if ($model === null)
                throw new CHttpException(404, 'The requested page does not exist.');
            return $model;
        }
        else {
            echo "<h1>You Are Not Authorized to See Other Members Profile, DON'T DO IT AGAIN!!!</h1>";
            exit;
        }
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionPending() {
        $this->layout = '//layouts/column2';
        //$model = User::model()->findAll('status =:status', array(':status'=>0));
        //$this->render('admin', array('model' => $model));
        $model = new User('search');
        $model->unsetAttributes();  // clear any default values
        $model->status = USER::USER_PENDING;
        if (isset($_GET['User']))
            $model->attributes = $_GET['User'];

        $this->render('pending', array(
            'model' => $model,
        ));
    }

    public function actionApproved() {
        $this->layout = '//layouts/column2';
        $model = new User('search');
        $model->unsetAttributes();  // clear any default values
        $model->status = USER::USER_APPROVED;
        if (isset($_GET['User']))
            $model->attributes = $_GET['User'];

        $this->render('approved', array(
            'model' => $model,
        ));
    }

    public function actionBanned() {
        $this->layout = '//layouts/column2';
        $model = new User('search');
        $model->unsetAttributes();  // clear any default values
        $model->status = USER::USER_BANNED;
        if (isset($_GET['User']))
            $model->attributes = $_GET['User'];

        $this->render('banned', array(
            'model' => $model,
        ));
    }

    public function actionApproveUser($id, $src) {
        $this->layout = '//layouts/column2';
        $user = User::model()->findByPk($id);
        $user->status = 1;
        $user->save();

        $to = $user->email;
        $subject = "Account Approved";
        $message = "
                                Hi <br><br>
                                We are happy to inform that your account has been Approved at Thamor Online Payment<br>
                                Now you can start using our services by logging in at http://www.ithamor.com/login <br><br>
                                If you've received this mail in error, it's likely that another user entered
                                your email address by mistake while trying to reset a password. If you didn't
                                initiate the request, you don't need to take any further action and can safely
                                disregard this email.<br /><br />
                                Sincerely,<br />
                                <b>Thamor Team,</b><br /><br />

                                Note: This email address cannot accept replies. To fix an issue or learn more about your account<br />
                                Please Contact at info@thamor.com";
        $headers = "FROM: webmaster@thamor.com\n To:" . $user->first_name . " - " . $to . "";

        $email = Yii::app()->email;
        $email->to = $to;
        $email->subject = $subject;
        $email->message = $message;
        $email->from = "webmaster@thamor.com";
        $email->send();

        if ($src == 'ban') {
            $this->redirect(array('/user/banned'));
        } else if ($src == 'pending') {
            $this->redirect(array('/user/pending'));
        }
    }

    public function actionBannUser($id) {
        $this->layout = '//layouts/column2';
        $user = User::model()->findByPk($id);
        $user->status = 2;
        $user->save();
        $this->redirect(array('/user/approved'));
    }

    public function actionForgotpassword() {
        $this->pageTitle = "Recover Password";
        $model = new User('forgotpassword');

        // uncomment the following code to enable ajax-based validation
        /*
          if(isset($_POST['ajax']) && $_POST['ajax']==='user-forgotpassword-form')
          {
          echo CActiveForm::validate($model);
          Yii::app()->end();
          }
         */

        if (isset($_POST['User'])) {

            $model->attributes = $_POST['User'];
            // form inputs are valid, do something here
            $email = $model->email;

            $criteria = new CDbCriteria();
            $criteria->addCondition('email = :email');
            $criteria->params = array('email' => $email);

            $result = User::model()->find($criteria);

            if (isset($result->email) && !empty($result->email)) {
                $token = md5(rand(0, 100));
                $result->token = $token;
                $result->save();

                $this->redirect(array('/user/recoverpassword/token/' . $token . '/id/' . $result->id));
            }
        }
        $this->render('forgotpassword', array('model' => $model));
    }

    public function actionRecoverPassword() {

        $token = $_GET['token'];
        $id = $_GET['id'];
        $criteria = new CDbCriteria();
        $criteria->condition = 'id = :id';
        $criteria->params = (array(':id' => $id));
        $result = User::model()->find($criteria);
        $secret_answer = $result->secret_answer;

        if (isset($_POST['User'])) {
            $result->attributes = $_POST['User'];
            //echo $result->secret_answer; exit;
            if (strtolower($result->secret_answer) == strtolower($secret_answer)) {
                //echo "correct answer"; exit;
                $array = 'zbskf';
                $array2 = 'lknst';
                $new_password = str_shuffle($array) . rand(0, 100) . str_shuffle($array2) . rand(0, 100);

                $result->password = md5($new_password);

                if ($result->save()) {
                    $to = $result->email;
                    $subject = "Password Recovery";
                    $message = "
                                Hi <br><br>
                                We Have received password recovery request from the email address: <b>" . $to . ".</b><br /><br />Your new password is <b>" . $new_password . "</b><br><br> You can login at thamor by using link below<br /><br />
                                http://www.ithamor.com/login<br /><br />
                                If clicking the link above doesn't work, please copy and paste the URL in a
                                new browser window instead.<br /><br />
                                If you've received this mail in error, it's likely that another user entered
                                your email address by mistake while trying to reset a password. If you didn't
                                initiate the request, you don't need to take any further action and can safely
                                disregard this email.<br /><br />
                                Sincerely,<br />
                                <b>Thamor Team,</b><br /><br />

                                Note: This email address cannot accept replies. To fix an issue or learn more about your account<br />
                                Please Contact at info@thamor.com";

                    $headers = "FROM: webmaster@thamor.com\n To:" . $result->first_name . " - " . $to . "";

                    $email = Yii::app()->email;
                    $email->to = $to;
                    $email->subject = $subject;
                    $email->message = $message;
                    $email->from = "webmaster@thamor.com";
                    if ($email->send()) {
                        Yii::app()->user->setFlash('recover', 'Success! An Email has been sent to your Email address with your Password');
                    } else {
                        Yii::app()->user->setFlash('recover', 'Error Sending Email');
                    }

                    Yii::app()->user->setFlash('recover', 'Success! An Email has been sent to your Email address with your Password');
                } else {
                    //print_r($result->getErrors()); exit;
                }
            } else {
                Yii::app()->user->setFlash('recover', 'Wrong Answer!');
            }
        }
        $result->secret_answer = "";
        $this->render('recoverpassword', array('model' => $result));
    }

}
