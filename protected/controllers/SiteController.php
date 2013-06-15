<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
        
        public function actionRegister()
        {
        $model=new RegisterForm('register');

        // uncomment the following code to enable ajax-based validation
        
        if(isset($_POST['ajax']) && $_POST['ajax']==='register-form-register-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        

        if(isset($_POST['RegisterForm']))
        {
            $model->attributes=$_POST['RegisterForm'];
            if($model->validate())
            {      
                $model->setMD5Password($model->password);
                $model->role = "student"; // Default role
                $model->register_date =date('Y-m-d H:i:s');
                $model->save();
                
                $connection=Yii::app()->db; 
                $lastId = Yii::app()->db->createCommand('SELECT uid FROM users ORDER BY uid DESC LIMIT 1')->queryScalar();
               // an SQL with two placeholders ":username" and ":email"
                $sql="INSERT INTO profile (uid) VALUES(:uid)";
                $command=$connection->createCommand($sql);
                $command->bindParam(":uid",$lastId);
                $command->execute();
                
                mkdir("C:\\xampp\\htdocs\\amklogi\\images\\profileimages\\" . $model->username . '\\');
                
                $js = Yii::app()->getClientScript();
                $js->registerScript(
                'register-js',
                '$(function() {
                    $(".form").hide();
                    $( "#dialog-message" ).dialog({
                            modal: true,
                            buttons: {
                            Ok: function() {
                            $( this ).dialog( "close" );
                            window.location = "./";
                            }
                        }
                    });
                });
                ',
                CClientScript::POS_END ); 
            }
        }
        $this->render('register',array('model'=>$model));
    }
}