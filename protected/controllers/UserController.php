<?php

class UserController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
        public $username;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','upload','album', 'comment'),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
        
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
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($username)
	{
            $model = User::model()->find("username = '" . $username . "'");
            if($model==NULL)
            {
                throw new CHttpException(404,'The specified post cannot be found.');
            }
            $this->render('view',array('model'=>$model));
	}
        
        public function actionAlbum($id)
	{
            $this->render('album');
	}

	public function actionUpload()
	{
            $model = new User();
            if(isset($_POST['User']))
            {
                $model->attributes=$_POST['User'];
                $uploadedFile = CUploadedFile::getInstance($model, 'personal_image_url');
                //$md5file = $uploadedFile;
                $uniq = uniqid();
                $fileName = "{$uniq}.jpg";
                $model->personal_image_url = $fileName;
                   
                $uploadedFile->saveAs("./images/profileimages/" . Yii::app()->user->name . "/" . $fileName);
                
                $path = "./images/profileimages/" . Yii::app()->user->name . "/" . $fileName;
                $uid = Yii::app()->user->id;
                
                $connection=Yii::app()->db; 
                $sql="UPDATE profile SET personal_image_url=:image_url WHERE uid=:uid";
                $command=$connection->createCommand($sql);
                $command->bindParam(":uid",$uid);
                $command->bindParam(":image_url",$path);
                $command->execute();
                
                list($width, $height, $type, $attr) = getimagesize($path);
                $width = $width / 2;
                $height = $height / 2;
                        
                $image = Yii::app()->image->load("./images/profileimages/" . Yii::app()->user->name . "/" . $fileName);
                $image->resize($width, $height);
                $image->save();
            }
            $this->render('upload', array('model'=>$model));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('User');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}
        
        public function actionComment()
        {
            $comment   = $_POST['comment'];
            $from_uid  = $_POST['from_uid'];
            $to_uid    = $_POST['to_uid'];
            $isprivate = $_POST['isprivate'];
            $isnew     = "true";
            
            $connection=Yii::app()->db; 
            $sql="INSERT INTO comments (from_uid, to_uid, text, datetime, isprivate, isnew) VALUES (:from_uid, :to_uid, :text, now(), :isprivate, :isnew)";
            $command=$connection->createCommand($sql);
            $command->bindParam(":from_uid",$from_uid);
            $command->bindParam(":to_uid", $to_uid);
            $command->bindParam(":text",$comment);
            $command->bindParam(":isprivate", $isprivate);
            $command->bindParam(":isnew", $isnew);
            $command->execute();
            
            $model = new User();
            $model->getUserComments($from_uid, $to_uid);
            die("");
        }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return User the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=User::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param User $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
