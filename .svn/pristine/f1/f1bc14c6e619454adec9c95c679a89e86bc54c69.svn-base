<?php

/**
 * This is the model class for table "rh_contacts".
 *
 * The followings are the available columns in table 'rh_contacts':
 * @property integer $id
 * @property string $debtorno
 * @property string $branchcode
 * @property integer $opportunities_id
 * @property integer $lead_id
 * @property string $name
 * @property string $department
 * @property string $description
 * @property string $important_note
 * @property string $office_phone
 * @property integer $extension
 * @property string $email
 * @property string $lastname
 * @property string $mobile_phone
 * @property integer $organitation_name
 * @property integer $lead_source
 * @property string $title
 * @property string $assistant
 * @property integer $reports_to
 * @property integer $assigned_to
 * @property string $photo
 * @property string $home_phone
 * @property string $secondary_email
 * @property string $date_birth
 * @property string $assistant_phone
 * @property integer $donotcall
 * @property string $address
 * @property string $state
 * @property string $city
 * @property string $zip
 */
class RhContacts extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'rh_contacts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('debtorno, branchcode, opportunities_id, lead_id, name, email', 'required'),
			array('opportunities_id, lead_id, extension, organitation_name, lead_source, reports_to, assigned_to, donotcall', 'numerical', 'integerOnly'=>true),
			array('debtorno, branchcode', 'length', 'max'=>10),
			array('name, department, email, lastname, address, city', 'length', 'max'=>100),
			array('office_phone', 'length', 'max'=>35),
			array('mobile_phone, title, assistant, home_phone, secondary_email, assistant_phone, state', 'length', 'max'=>45),
			array('photo', 'length', 'max'=>150),
			array('zip', 'length', 'max'=>15),
			array('description, important_note, date_birth', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, debtorno, branchcode, opportunities_id, lead_id, name, department, description, important_note, office_phone, extension, email, lastname, mobile_phone, organitation_name, lead_source, title, assistant, reports_to, assigned_to, photo, home_phone, secondary_email, date_birth, assistant_phone, donotcall, address, state, city, zip', 'safe', 'on'=>'search'),
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
			'debtorno' => 'Debtorno',
			'branchcode' => 'Branchcode',
			'opportunities_id' => 'Opportunities',
			'lead_id' => 'Lead',
			'name' => 'Name',
			'department' => 'Department',
			'description' => 'Description',
			'important_note' => 'Important Note',
			'office_phone' => 'Office Phone',
			'extension' => 'Extension',
			'email' => 'Email',
			'lastname' => 'Lastname',
			'mobile_phone' => 'Mobile Phone',
			'organitation_name' => 'Organitation Name',
			'lead_source' => 'Lead Source',
			'title' => 'Title',
			'assistant' => 'Assistant',
			'reports_to' => 'Reports To',
			'assigned_to' => 'Assigned To',
			'photo' => 'Photo',
			'home_phone' => 'Home Phone',
			'secondary_email' => 'Secondary Email',
			'date_birth' => 'Date Birth',
			'assistant_phone' => 'Assistant Phone',
			'donotcall' => 'Donotcall',
			'address' => 'Address',
			'state' => 'State',
			'city' => 'City',
			'zip' => 'Zip',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('debtorno',$this->debtorno,true);
		$criteria->compare('branchcode',$this->branchcode,true);
		$criteria->compare('opportunities_id',$this->opportunities_id);
		$criteria->compare('lead_id',$this->lead_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('department',$this->department,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('important_note',$this->important_note,true);
		$criteria->compare('office_phone',$this->office_phone,true);
		$criteria->compare('extension',$this->extension);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('lastname',$this->lastname,true);
		$criteria->compare('mobile_phone',$this->mobile_phone,true);
		$criteria->compare('organitation_name',$this->organitation_name);
		$criteria->compare('lead_source',$this->lead_source);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('assistant',$this->assistant,true);
		$criteria->compare('reports_to',$this->reports_to);
		$criteria->compare('assigned_to',$this->assigned_to);
		$criteria->compare('photo',$this->photo,true);
		$criteria->compare('home_phone',$this->home_phone,true);
		$criteria->compare('secondary_email',$this->secondary_email,true);
		$criteria->compare('date_birth',$this->date_birth,true);
		$criteria->compare('assistant_phone',$this->assistant_phone,true);
		$criteria->compare('donotcall',$this->donotcall);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('zip',$this->zip,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RhContacts the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
