<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $uid
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $register_date
 * @property string $login_date
 * @property string $role
 */
class User extends CActiveRecord
{
        private $userID;
        private $firstname;
        private $surname;
        private $gender;
        private $birthday;
        private $email;
        private $city;
        private $relationship;
        private $homepage;
        private $skype;
        public $personal_image_url;
        
        public function getUserID() {
            return $this->userID;
        }

        public function setUserID($userID) {
            $this->userID = $userID;
        }

        public function getFirstname() {
            return $this->firstname;
        }

        public function getSurname() {
            return $this->surname;
        }

        public function getGender() {
            return $this->gender;
        }

        public function getBirthday() {
            return $this->birthday;
        }

        public function getEmail() {
            return $this->email;
        }

        public function getCity() {
            return $this->city;
        }

        public function getRelationship() {
            return $this->relationship;
        }

        public function getHomepage() {
            return $this->homepage;
        }

        public function getSkype() {
            return $this->skype;
        }

        public function getPersonal_image_url() {
            return $this->personal_image_url;
        }
        
        public function setFirstname($firstname) {
            $this->firstname = $firstname;
        }

        public function setSurname($surname) {
            $this->surname = $surname;
        }

        public function setGender($gender) {
            if($gender=="male")
            {
                $gender = "Mies";
            }
            else if($gender=="female")
            {
                $gender = "Nainen";
            }
            $this->gender = $gender;
        }

        public function setBirthday($birthday) {
            $this->birthday = $birthday;
        }

        public function setEmail($email) {
            $this->email = $email;
        }

        public function setCity($city) {
            $this->city = $city;
        }

        public function setRelationship($relationship) {
            $this->relationship = $relationship;
        }

        public function setHomepage($homepage) {
            $this->homepage = $homepage;
        }

        public function setSkype($skype) {
            $this->skype = $skype;
        }

        public function setPersonal_image_url($personal_image_url) {
            $this->personal_image_url = $personal_image_url;
        }
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
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
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, password, email, register_date, login_date, role', 'required'),
			array('username', 'length', 'max'=>90),
			array('password, email', 'length', 'max'=>255),
			array('role', 'length', 'max'=>9),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('uid, username, password, email, register_date, login_date, role', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'uid' => 'Uid',
			'username' => 'Username',
			'password' => 'Password',
			'email' => 'Email',
			'register_date' => 'Register Date',
			'login_date' => 'Login Date',
			'role' => 'Role',
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

		$criteria->compare('uid',$this->uid);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('register_date',$this->register_date,true);
		$criteria->compare('login_date',$this->login_date,true);
		$criteria->compare('role',$this->role,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
                
        public function getProfile($uid)
        {
            $dbC = Yii::app()->db->createCommand();
            $dbC->setFetchMode(PDO::FETCH_OBJ);
            $dbC->select()->from('profile')->where("uid=" . $uid);
            
            foreach ($dbC->queryAll() as $row)
            {
                $this->setFirstname($row->firstname);
                $this->setSurname($row->surname);
                $this->setGender($row->gender);
                $this->setBirthday($row->birthday);
                $this->setEmail($row->email);
                $this->setCity($row->city);
                $this->setRelationship($row->relationship);
                $this->setHomepage($row->homepage);
                $this->setSkype($row->skype);
                $this->setPersonal_image_url($row->personal_image_url);
            }
        }
        
        public function getProfileUIDByUsername($username)
        {
            $dbC = Yii::app()->db->createCommand();
            $dbC->setFetchMode(PDO::FETCH_OBJ);
            $dbC->select()->from('users')->where("username='" . $username . "'");
            foreach ($dbC->queryAll() as $row)
            {
                $this->setUserID($row->uid);
            }
            
            return $this->userID;
        }
        
        public function getUserComments($from_uid, $to_uid)
        {
            $dbC = Yii::app()->db->createCommand();
            $dbC->setFetchMode(PDO::FETCH_OBJ);
            //$dbC->select('users.uid, comments.text')->from('comments, users')->where("users.uid=" . $from_uid . " AND to_uid =" . $to_uid);
            $dbC->select("users.uid, users.username, comments.text, comments.isprivate, comments.datetime ")->from("users")->join("comments", "comments.from_uid=users.uid")->where("comments.to_uid = " . $to_uid)->order("comments.datetime");
            foreach ($dbC->queryAll() as $row)
            {
               $uid       = $row->uid;
               $username  = $row->username;
               $text      = $row->text;
               $datetime  = $row->datetime;
               $isprivate = $row->isprivate;
               if($isprivate=="false")
               {
                   echo "<li><a href='" . $username . "'>" . $username . "</a>: " .  $text . " - (" . $datetime . ")</li>";
               }
               else if($isprivate=="true" && $from_uid==$to_uid || $uid == $from_uid)
               {
                   echo "<li style='color:#A229FF'><a href='" . $username . "'>" . $username . "</a>: " .  $text . " - (" . $datetime . ")</li>";
               }
            }
       
        }
        
        public function getUserCommentsCount($uid)
        {
            $count = Comments::model()->count('to_uid=:to_uid AND isnew=:isnew', array('to_uid'=>$uid,'isnew'=>"true"));
            if($count!=0)
            {
                return $count;
            }
            $count="";
            return $count;
        }
        
        public function readComments($to_uid)
        {
            if(Yii::app()->user->id == $to_uid)
            {
                $dbC1 = Yii::app()->db->createCommand();
                $dbC1->update('comments', array('isnew'=>'false'), 'to_uid=:to_uid', array(':to_uid'=>$to_uid));
            }
        }
        
        public function getAllUsers()
        {
            $dbC = Yii::app()->db->createCommand();
            $dbC->setFetchMode(PDO::FETCH_OBJ);
            $dbC->select()->from('users');
            foreach ($dbC->queryAll() as $row)
            {
                echo "<li><a href='../" . $row->username . "'>" .  $row->username . "</a></li>";
            }
        }
}