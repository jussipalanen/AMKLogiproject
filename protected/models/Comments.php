<?php

/**
 * This is the model class for table "comments".
 *
 * The followings are the available columns in table 'comments':
 * @property integer $id
 * @property integer $from_uid
 * @property integer $to_uid
 * @property string $text
 * @property string $datetime
 * @property string $isprivate
 */
class Comments extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Comments the static model class
	 */
        private $id;
        private $from_uid;
        private $to_uid;
        private $text;
        private $datetime;
        private $isprivate;
        
        public function getId() {
            return $this->id;
        }

        public function setId($id) {
            $this->id = $id;
        }

        public function getFrom_uid() {
            return $this->from_uid;
        }

        public function setFrom_uid($from_uid) {
            $this->from_uid = $from_uid;
        }

        public function getTo_uid() {
            return $this->to_uid;
        }

        public function setTo_uid($to_uid) {
            $this->to_uid = $to_uid;
        }

        public function getText() {
            return $this->text;
        }

        public function setText($text) {
            $this->text = $text;
        }

        public function getDatetime() {
            return $this->datetime;
        }

        public function setDatetime($datetime) {
            $this->datetime = $datetime;
        }

        public function getIsprivate() {
            return $this->isprivate;
        }

        public function setIsprivate($isprivate) {
            $this->isprivate = $isprivate;
        }

        public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'comments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('from_uid, to_uid, text, datetime, isprivate', 'required'),
			array('from_uid, to_uid', 'numerical', 'integerOnly'=>true),
			array('isprivate', 'length', 'max'=>5),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, from_uid, to_uid, text, datetime, isprivate', 'safe', 'on'=>'search'),
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
			'from_uid' => 'From Uid',
			'to_uid' => 'To Uid',
			'text' => 'Text',
			'datetime' => 'Datetime',
			'isprivate' => 'Isprivate',
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
		$criteria->compare('from_uid',$this->from_uid);
		$criteria->compare('to_uid',$this->to_uid);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('datetime',$this->datetime,true);
		$criteria->compare('isprivate',$this->isprivate,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}