<?php

/**
 * This is the model class for table "rh_leads".
 *
 * The followings are the available columns in table 'rh_leads':
 * 		@property integer $id
 * 		@property string $debtorno
 * 		@property string $branchcode
 * 		@property integer $opportunities_id
 * 		@property string $company_name
 * 		@property string $name
	 * @property string $address1
	 * @property string $address2
	 * @property string $address3
	 * @property string $address4
	 * @property string $address5
	 * @property string $address6
	 * @property string $address7
	 * @property string $address8
	 * @property string $address9
	 * @property string $address10
 * @property string $source
 * @property string $description
 * @property string $important_note
 * @property integer $parent_account
 * @property string $phone
 * @property integer $extension
 * @property string $email
 * @property string $mobile_phone
 * @property string $website
 * @property string $annual_revenue
 		* @property integer $number_employees
 		* @property string $rating
 * @property integer $assigned_to
 * @property integer $industry
 * @property string $secondary_email
 * @property string $address
 * @property integer $city
 * @property integer $state
 * @property string $zip
 */
class RhLeads extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'rh_leads';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('debtorno, branchcode, opportunities_id, company_name, name, address1, address2, address3, address4, address5, address6, address7, address8, address9, address10, email', 'required'),
			array('opportunities_id, parent_account, extension, number_employees, assigned_to, industry, city, state', 'numerical', 'integerOnly'=>true),
			array('debtorno, branchcode', 'length', 'max'=>10),
			array('company_name, name, address4, address8, address9, email, website, address', 'length', 'max'=>100),
			array('address1, address5, address7', 'length', 'max'=>200),
			array('address2, address3', 'length', 'max'=>40),
			array('address6', 'length', 'max'=>250),
			array('address10, source', 'length', 'max'=>50),
			array('phone', 'length', 'max'=>30),
			array('mobile_phone, zip', 'length', 'max'=>45),
			array('annual_revenue', 'length', 'max'=>20),
			array('rating', 'length', 'max'=>15),
			array('secondary_email', 'length', 'max'=>75),
			array('description, important_note', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, debtorno, branchcode, opportunities_id, company_name, name, address1, address2, address3, address4, address5, address6, address7, address8, address9, address10, source, description, important_note, parent_account, phone, extension, email, mobile_phone, website, annual_revenue, number_employees, rating, assigned_to, industry, secondary_email, address, city, state, zip', 'safe', 'on'=>'search'),
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
			'company_name' => 'Company Name',
			'name' => 'Name',
			'address1' => 'Address1',
			'address2' => 'Address2',
			'address3' => 'Address3',
			'address4' => 'Address4',
			'address5' => 'Address5',
			'address6' => 'Address6',
			'address7' => 'Address7',
			'address8' => 'Address8',
			'address9' => 'Address9',
			'address10' => 'Address10',
			'source' => 'Source',
			'description' => 'Description',
			'important_note' => 'Important Note',
			'parent_account' => 'Parent Account',
			'phone' => 'Phone',
			'extension' => 'Extension',
			'email' => 'Email',
			'mobile_phone' => 'Mobile Phone',
			'website' => 'Website',
			'annual_revenue' => 'Annual Revenue',
			'number_employees' => 'Number Employees',
			'rating' => 'Rating',
			'assigned_to' => 'Assigned To',
			'industry' => 'Industry',
			'secondary_email' => 'Secondary Email',
			'address' => 'Address',
			'city' => 'City',
			'state' => 'State',
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
		$criteria->compare('company_name',$this->company_name,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('address1',$this->address1,true);
		$criteria->compare('address2',$this->address2,true);
		$criteria->compare('address3',$this->address3,true);
		$criteria->compare('address4',$this->address4,true);
		$criteria->compare('address5',$this->address5,true);
		$criteria->compare('address6',$this->address6,true);
		$criteria->compare('address7',$this->address7,true);
		$criteria->compare('address8',$this->address8,true);
		$criteria->compare('address9',$this->address9,true);
		$criteria->compare('address10',$this->address10,true);
		$criteria->compare('source',$this->source,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('important_note',$this->important_note,true);
		$criteria->compare('parent_account',$this->parent_account);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('extension',$this->extension);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('mobile_phone',$this->mobile_phone,true);
		$criteria->compare('website',$this->website,true);
		$criteria->compare('annual_revenue',$this->annual_revenue,true);
		$criteria->compare('number_employees',$this->number_employees);
		$criteria->compare('rating',$this->rating,true);
		$criteria->compare('assigned_to',$this->assigned_to);
		$criteria->compare('industry',$this->industry);
		$criteria->compare('secondary_email',$this->secondary_email,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('city',$this->city);
		$criteria->compare('state',$this->state);
		$criteria->compare('zip',$this->zip,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RhLeads the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
