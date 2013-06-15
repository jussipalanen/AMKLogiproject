<?php

/**
 * This is the model class for table "profile".
 *
 * The followings are the available columns in table 'profile':
 * @property integer $id
 * @property integer $uid
 * @property string $firstname
 * @property string $surname
 * @property string $gender
 * @property string $birthday
 * @property string $email
 * @property string $city
 * @property string $relationship
 * @property string $homepage
 * @property string $skype
 * @property string $personal_image_url
 */
class Profile extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Profile the static model class
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
		return 'profile';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('uid, firstname, surname, gender, birthday, email, city, relationship, homepage, skype, personal_image_url', 'required'),
			array('uid', 'numerical', 'integerOnly'=>true),
			array('firstname, surname, email, city, homepage, skype, personal_image_url', 'length', 'max'=>255),
			array('gender', 'length', 'max'=>7),
			array('relationship', 'length', 'max'=>15),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, uid, firstname, surname, gender, birthday, email, city, relationship, homepage, skype, personal_image_url', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'uid' => 'Uid',
			'firstname' => 'Firstname',
			'surname' => 'Surname',
			'gender' => 'Gender',
			'birthday' => 'Birthday',
			'email' => 'Email',
			'city' => 'City',
			'relationship' => 'Relationship',
			'homepage' => 'Homepage',
			'skype' => 'Skype',
			'personal_image_url' => 'Personal Image Url',
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
		$criteria->compare('uid',$this->uid);
		$criteria->compare('firstname',$this->firstname,true);
		$criteria->compare('surname',$this->surname,true);
		$criteria->compare('gender',$this->gender,true);
		$criteria->compare('birthday',$this->birthday,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('relationship',$this->relationship,true);
		$criteria->compare('homepage',$this->homepage,true);
		$criteria->compare('skype',$this->skype,true);
		$criteria->compare('personal_image_url',$this->personal_image_url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}