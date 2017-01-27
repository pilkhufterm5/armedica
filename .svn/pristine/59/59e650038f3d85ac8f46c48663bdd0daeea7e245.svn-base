<?php

/**
 * This is the model class for table "rh_crm_contacto".
 *
 * The followings are the available columns in table 'rh_crm_contacto':
 * @property integer $idContacto
 * @property string $nombre
 * @property string $apellidoPaterno
 * @property string $apellidoMaterno
 * @property string $fechaAlta
 * @property string $fechaUltimaActualizacion
 * @property string $titulo
 * @property string $email
 * @property string $telefono
 * @property string $celular
 * @property integer $contactarPorCelular
 * @property integer $contactarPorEmail
 * @property string $direccion1
 * @property string $direccion2
 * @property string $direccion3
 * @property string $direccion4
 * @property string $direccion5
 * @property string $direccion6
 * @property string $direccion7
 * @property string $direccion8
 * @property string $direccion9
 * @property string $direccion10
 * @property integer $fkCiudad
 * @property integer $fkEstado
 * @property integer $codigoPostal
 * @property integer $fkPais
 * @property string $descripcion
 * @property string $facebook
 * @property string $twitter
 * @property string $googlePlus
 * @property integer $estatus
 * @property string $linkedin
 * @property string $skype
 * @property string $contactoWeb
 * @property integer $idProspecto
 */
class Contacto extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'rh_crm_contacto';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nombre, apellidoPaterno', 'required'),
			array('contactarPorCelular, contactarPorEmail, fkCiudad, fkEstado, codigoPostal, fkPais, estatus, idProspecto, activo', 'numerical', 'integerOnly'=>true),
			array('nombre, apellidoPaterno, titulo', 'length', 'max'=>30),
			array('apellidoMaterno', 'length', 'max'=>30),
			array('email, linkedin, skype, contactoWeb, userid', 'length', 'max'=>60),
			array('telefono, celular, tel_empresa', 'length', 'max'=>20),
			array('direccion1, direccion2, direccion3, direccion4, direccion5, direccion6, direccion7, direccion8, direccion9', 'length', 'max'=>100),
			array('direccion10', 'length', 'max'=>300),
			array('descripcion', 'length', 'max'=>160),
			array('facebook, twitter, googlePlus', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('idContacto, nombre, apellidoPaterno, apellidoMaterno, fechaAlta, fechaUltimaActualizacion, titulo, email, telefono, celular, contactarPorCelular, contactarPorEmail, direccion1, direccion2, direccion3, direccion4, direccion5, direccion6, direccion7, direccion8, direccion9, direccion10, fkCiudad, fkEstado, codigoPostal, fkPais, descripcion, facebook, twitter, googlePlus, estatus, linkedin, skype, contactoWeb, idProspecto', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CrmContacto the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
