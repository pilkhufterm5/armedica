<?php

/**
 * This is the model class for table "rh_cobradores".
 *
 * The followings are the available columns in table 'rh_cobradores':
 * @property integer $id
 * @property string $nombre
 * @property string $comision
 * @property string $zona
 * @property integer $activo
 * @property integer $reasigna
 * @property integer $transfe
 * @property integer $cobori
 * @property integer $empresa
 */
class Cobradores extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'rh_cobradores';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nombre, comision, zona, reasigna, transfe, cobori, empresa', 'required'),
			array('activo, reasigna, transfe, cobori, empresa', 'numerical', 'integerOnly'=>true),
			array('nombre', 'length', 'max'=>50),
			array('comision, zona', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, nombre, comision, zona, activo, reasigna, transfe, cobori, empresa', 'safe', 'on'=>'search'),
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
			'nombre' => 'Nombre',
			'comision' => 'Comision',
			'zona' => 'Zona',
			'activo' => 'Activo',
			'reasigna' => 'Reasigna',
			'transfe' => 'Transfe',
			'cobori' => 'Cobori',
			'empresa' => 'Empresa',
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
		$criteria->compare('nombre',$this->nombre,true);
		$criteria->compare('comision',$this->comision,true);
		$criteria->compare('zona',$this->zona,true);
		$criteria->compare('activo',$this->activo);
		$criteria->compare('reasigna',$this->reasigna);
		$criteria->compare('transfe',$this->transfe);
		$criteria->compare('cobori',$this->cobori);
		$criteria->compare('empresa',$this->empresa);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cobradores the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
