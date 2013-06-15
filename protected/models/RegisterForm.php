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
class RegisterForm extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RegisterForm the static model class
	 */
        public $username;
        public $password;
        public $repassword;
    
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
			array('username, password, repassword, email', 'required'),
			array('username', 'length', 'max'=>90),
			array('repassword, password, email', 'length', 'max'=>255),
			array('role', 'length', 'max'=>9),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('uid, username, password, repassword, email, register_date, login_date, role', 'safe', 'on'=>'search'),
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
			'username' => 'Tunnus ',
			'password' => 'Salasana ',
                        'repassword' => 'Salasana uudestaan ',
			'email' => 'Sähköpostiosoite ',
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
                $criteria->compare('repassword',$this->repassword,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('register_date',$this->register_date,true);
		$criteria->compare('login_date',$this->login_date,true);
		$criteria->compare('role',$this->role,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
        public function setMD5Password($password)
        {
            $this->password = md5($password);
            return $this->password;
        }
}