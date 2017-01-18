<?php

/**
 * This is the model class for table "paymentmethods".
 *
 * The followings are the available columns in table 'paymentmethods':
 * @property integer $paymentid
 * @property string $paymentname
 * @property integer $paymenttype
 * @property integer $receipttype
 */
class Metodopago extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'paymentmethods';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('paymenttype, receipttype', 'numerical', 'integerOnly'=>true),
			array('paymentname', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('paymentid, paymentname, paymenttype, receipttype, satid, satname', 'safe', 'on'=>'search'),
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
			'paymentid' => 'Paymentid',
			'paymentname' => 'Paymentname',
			'paymenttype' => 'Paymenttype',
			'receipttype' => 'Receipttype',
			'satid' => 'Satid',
			'satname' => 'Satname',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('paymentid',$this->paymentid);
		$criteria->compare('paymentname',$this->paymentname,true);
		$criteria->compare('paymenttype',$this->paymenttype);
		$criteria->compare('receipttype',$this->receipttype);
		$criteria->compare('satid',$this->satid);
		$criteria->compare('satname',$this->satname);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Paymentmethod the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
