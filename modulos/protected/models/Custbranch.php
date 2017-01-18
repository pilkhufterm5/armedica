<?php

/**
 * This is the model class for table "custbranch".
 *
 * The followings are the available columns in table 'custbranch':
 * @property string $branchcode
 * @property string $debtorno
 * @property string $brname
 * @property string $braddress1
 * @property string $braddress2
 * @property string $braddress3
 * @property string $braddress4
 * @property string $braddress5
 * @property string $braddress6
 * @property integer $estdeliverydays
 * @property string $area
 * @property string $salesman
 * @property integer $fwddate
 * @property string $phoneno
 * @property string $faxno
 * @property string $contactname
 * @property string $email
 * @property string $defaultlocation
 * @property integer $taxgroupid
 * @property integer $defaultshipvia
 * @property integer $deliverblind
 * @property integer $disabletrans
 * @property string $brpostaddr1
 * @property string $brpostaddr2
 * @property string $brpostaddr3
 * @property string $brpostaddr4
 * @property string $brpostaddr5
 * @property string $brpostaddr6
 * @property string $specialinstructions
 * @property string $custbranchcode
 * @property integer $vtiger_accountid
 * @property string $rh_defaultlocation
 * @property string $braddress7
 * @property string $braddress8
 * @property string $braddress9
 * @property string $braddress10
 * @property integer $rh_succode
 * @property string $metodopago
 * @property string $cuentapago
 * @property string $cuadrante1
 * @property string $cuadrante2
 * @property string $cuadrante3
 * @property string $sexo
 * @property string $nombre_empresa
 * @property string $fecha_nacimiento
 * @property string $fecha_ingreso
 * @property string $fecha_ultaum
 * @property string $antecedentes_clinicos
 * @property string $folio
 */
class Custbranch extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'custbranch';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('specialinstructions, sexo, nombre_empresa, fecha_nacimiento, fecha_ingreso, fecha_ultaum, antecedentes_clinicos', 'required'),
			array('estdeliverydays, fwddate, taxgroupid, defaultshipvia, deliverblind, disabletrans, vtiger_accountid, rh_succode', 'numerical', 'integerOnly'=>true),
			array('branchcode, debtorno, cuadrante1, cuadrante2, cuadrante3, folio', 'length', 'max'=>10),
			array('brname, braddress4, braddress8, braddress9, nombre_empresa', 'length', 'max'=>100),
			array('braddress1, braddress5, braddress7', 'length', 'max'=>200),
			array('braddress2, braddress3, brpostaddr1, brpostaddr2', 'length', 'max'=>40),
			array('braddress6', 'length', 'max'=>250),
			array('area', 'length', 'max'=>3),
			array('salesman', 'length', 'max'=>4),
			array('phoneno, faxno, brpostaddr4, brpostaddr5', 'length', 'max'=>20),
			array('contactname, brpostaddr3, custbranchcode', 'length', 'max'=>30),
			array('email', 'length', 'max'=>55),
			array('defaultlocation, rh_defaultlocation', 'length', 'max'=>5),
			array('brpostaddr6', 'length', 'max'=>15),
			array('braddress10', 'length', 'max'=>50),
			array('metodopago, cuentapago', 'length', 'max'=>512),
			array('sexo', 'length', 'max'=>9),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('branchcode, debtorno, brname, braddress1, braddress2, braddress3, braddress4, braddress5, braddress6, estdeliverydays, area, salesman, fwddate, phoneno, faxno, contactname, email, defaultlocation, taxgroupid, defaultshipvia, deliverblind, disabletrans, brpostaddr1, brpostaddr2, brpostaddr3, brpostaddr4, brpostaddr5, brpostaddr6, specialinstructions, custbranchcode, vtiger_accountid, rh_defaultlocation, braddress7, braddress8, braddress9, braddress10, rh_succode, metodopago, cuentapago, cuadrante1, cuadrante2, cuadrante3, sexo, nombre_empresa, fecha_nacimiento, fecha_ingreso, fecha_ultaum, antecedentes_clinicos, folio', 'safe', 'on'=>'search'),
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
			'branchcode' => 'Branchcode',
			'debtorno' => 'Debtorno',
			'brname' => 'Brname',
			'braddress1' => 'Braddress1',
			'braddress2' => 'Braddress2',
			'braddress3' => 'Braddress3',
			'braddress4' => 'Braddress4',
			'braddress5' => 'Braddress5',
			'braddress6' => 'Braddress6',
			'estdeliverydays' => 'Estdeliverydays',
			'area' => 'Area',
			'salesman' => 'Salesman',
			'fwddate' => 'Fwddate',
			'phoneno' => 'Phoneno',
			'faxno' => 'Faxno',
			'contactname' => 'Contactname',
			'email' => 'Email',
			'defaultlocation' => 'Defaultlocation',
			'taxgroupid' => 'Taxgroupid',
			'defaultshipvia' => 'Defaultshipvia',
			'deliverblind' => 'Deliverblind',
			'disabletrans' => 'Disabletrans',
			'brpostaddr1' => 'Brpostaddr1',
			'brpostaddr2' => 'Brpostaddr2',
			'brpostaddr3' => 'Brpostaddr3',
			'brpostaddr4' => 'Brpostaddr4',
			'brpostaddr5' => 'Brpostaddr5',
			'brpostaddr6' => 'Brpostaddr6',
			'specialinstructions' => 'Specialinstructions',
			'custbranchcode' => 'Custbranchcode',
			'vtiger_accountid' => 'Vtiger Accountid',
			'rh_defaultlocation' => 'Rh Defaultlocation',
			'braddress7' => 'Braddress7',
			'braddress8' => 'Braddress8',
			'braddress9' => 'Braddress9',
			'braddress10' => 'Braddress10',
			'rh_succode' => 'Rh Succode',
			'metodopago' => 'Metodopago',
			'cuentapago' => 'Cuentapago',
			'cuadrante1' => 'Cuadrante1',
			'cuadrante2' => 'Cuadrante2',
			'cuadrante3' => 'Cuadrante3',
			'sexo' => 'Sexo',
			'nombre_empresa' => 'Nombre Empresa',
			'fecha_nacimiento' => 'Fecha Nacimiento',
			'fecha_ingreso' => 'Fecha Ingreso',
			'fecha_ultaum' => 'Fecha Ultaum',
			'antecedentes_clinicos' => 'Antecedentes Clinicos',
			'folio' => 'Folio',
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

		$criteria->compare('branchcode',$this->branchcode,true);
		$criteria->compare('debtorno',$this->debtorno,true);
		$criteria->compare('brname',$this->brname,true);
		$criteria->compare('braddress1',$this->braddress1,true);
		$criteria->compare('braddress2',$this->braddress2,true);
		$criteria->compare('braddress3',$this->braddress3,true);
		$criteria->compare('braddress4',$this->braddress4,true);
		$criteria->compare('braddress5',$this->braddress5,true);
		$criteria->compare('braddress6',$this->braddress6,true);
		$criteria->compare('estdeliverydays',$this->estdeliverydays);
		$criteria->compare('area',$this->area,true);
		$criteria->compare('salesman',$this->salesman,true);
		$criteria->compare('fwddate',$this->fwddate);
		$criteria->compare('phoneno',$this->phoneno,true);
		$criteria->compare('faxno',$this->faxno,true);
		$criteria->compare('contactname',$this->contactname,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('defaultlocation',$this->defaultlocation,true);
		$criteria->compare('taxgroupid',$this->taxgroupid);
		$criteria->compare('defaultshipvia',$this->defaultshipvia);
		$criteria->compare('deliverblind',$this->deliverblind);
		$criteria->compare('disabletrans',$this->disabletrans);
		$criteria->compare('brpostaddr1',$this->brpostaddr1,true);
		$criteria->compare('brpostaddr2',$this->brpostaddr2,true);
		$criteria->compare('brpostaddr3',$this->brpostaddr3,true);
		$criteria->compare('brpostaddr4',$this->brpostaddr4,true);
		$criteria->compare('brpostaddr5',$this->brpostaddr5,true);
		$criteria->compare('brpostaddr6',$this->brpostaddr6,true);
		$criteria->compare('specialinstructions',$this->specialinstructions,true);
		$criteria->compare('custbranchcode',$this->custbranchcode,true);
		$criteria->compare('vtiger_accountid',$this->vtiger_accountid);
		$criteria->compare('rh_defaultlocation',$this->rh_defaultlocation,true);
		$criteria->compare('braddress7',$this->braddress7,true);
		$criteria->compare('braddress8',$this->braddress8,true);
		$criteria->compare('braddress9',$this->braddress9,true);
		$criteria->compare('braddress10',$this->braddress10,true);
		$criteria->compare('rh_succode',$this->rh_succode);
		$criteria->compare('metodopago',$this->metodopago,true);
		$criteria->compare('cuentapago',$this->cuentapago,true);
		$criteria->compare('cuadrante1',$this->cuadrante1,true);
		$criteria->compare('cuadrante2',$this->cuadrante2,true);
		$criteria->compare('cuadrante3',$this->cuadrante3,true);
		$criteria->compare('sexo',$this->sexo,true);
		$criteria->compare('nombre_empresa',$this->nombre_empresa,true);
		$criteria->compare('fecha_nacimiento',$this->fecha_nacimiento,true);
		$criteria->compare('fecha_ingreso',$this->fecha_ingreso,true);
		$criteria->compare('fecha_ultaum',$this->fecha_ultaum,true);
		$criteria->compare('antecedentes_clinicos',$this->antecedentes_clinicos,true);
		$criteria->compare('folio',$this->folio,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Custbranch the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
