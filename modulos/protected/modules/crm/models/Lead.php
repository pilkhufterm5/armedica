<?php

/**
 *
mysql> desc rh_crm_prospecto;
+--------------------------+--------------+------+-----+---------+----------------+
| Field                    | Type         | Null | Key | Default | Extra          |
+--------------------------+--------------+------+-----+---------+----------------+
| idProspecto              | int(11)      | NO   | PRI | NULL    | auto_increment |
| nombre                   | varchar(30)  | NO   |     | NULL    |                |
| apellidoPaterno          | varchar(30)  | NO   |     | NULL    |                |
| apellidoMaterno          | varchar(30)  | YES  |     | NULL    |                |
| fechaAlta                | datetime     | NO   |     | NULL    |                |
| fechaUltimaActualizacion | datetime     | NO   |     | NULL    |                |
| titulo                   | varchar(30)  | YES  |     | NULL    |                |
| email                    | varchar(60)  | NO   |     | NULL    |                |
| telefono                 | varchar(20)  | YES  |     | NULL    |                |
| celular                  | varchar(20)  | YES  |     | NULL    |                |
| contactarPorCelular      | bit(1)       | NO   |     | b'0'    |                |
| contactarPorEmail        | bit(1)       | NO   |     | b'0'    |                |
| direccion1               | varchar(100) | YES  |     | NULL    |                |
| direccion2               | varchar(100) | YES  |     | NULL    |                |
| direccion3               | varchar(100) | YES  |     | NULL    |                |
| direccion4               | varchar(100) | YES  |     | NULL    |                |
| direccion5               | varchar(100) | YES  |     | NULL    |                |
| direccion6               | varchar(100) | YES  |     | NULL    |                |
| direccion7               | varchar(100) | YES  |     | NULL    |                |
| direccion8               | varchar(100) | YES  |     | NULL    |                |
| direccion9               | varchar(100) | YES  |     | NULL    |                |
| direccion10              | varchar(300) | YES  |     | NULL    |                |
| fkCiudad                 | int(11)      | YES  |     | NULL    |                |
| fkEstado                 | int(11)      | YES  |     | NULL    |                |
| codigoPostal             | int(11)      | YES  |     | NULL    |                |
| fkPais                   | int(11)      | YES  |     | NULL    |                |
| descripcion              | varchar(160) | YES  |     | NULL    |                |
| facebook                 | varchar(50)  | YES  |     | NULL    |                |
| twitter                  | varchar(50)  | YES  |     | NULL    |                |
| googlePlus               | varchar(50)  | NO   |     | NULL    |                |
| estatus                  | bit(1)       | NO   |     | b'0'    |                |
| linkedin                 | varchar(60)  | YES  |     | NULL    |                |
| skype                    | varchar(60)  | YES  |     | NULL    |                |
| contactoWeb              | varchar(60)  | YES  |     | NULL    |                |
+--------------------------+--------------+------+-----+---------+----------------+

 */
class Lead extends CActiveRecord{

	public function tableName()
	{
		return 'rh_crm_prospecto';
	}


	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nombre, apellidoPaterno', 'required'),
			array('source_id, contactarPorCelular, contactarPorEmail, fkCiudad, fkEstado, codigoPostal, fkPais', 'numerical', 'integerOnly'=>true),
			array('company, nombre, apellidoPaterno, titulo, status_prospecto', 'length', 'max'=>100),
			array('RFC', 'length', 'max'=>13),
			//array('RFC', 'ext.validators.satRfcValidator'),
			array('apellidoMaterno', 'length', 'max'=>30),
			array('email, linkedin, skype, contactoWeb', 'length', 'max'=>60),
			//array('email', 'email'),
			array('telefono, celular', 'length', 'max'=>20),
			array('direccion1, direccion2, direccion3, direccion4, direccion5, direccion6, direccion7, direccion8, direccion9', 'length', 'max'=>100),
			array('direccion10', 'length', 'max'=>300),
			array('descripcion', 'length', 'max'=>160),
			array('facebook, twitter, googlePlus', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('idContacto, nombre, apellidoPaterno, apellidoMaterno, fechaAlta, fechaUltimaActualizacion, titulo, email, telefono, celular, contactarPorCelular, contactarPorEmail, direccion1, direccion2, direccion3, direccion4, direccion5, direccion6, direccion7, direccion8, direccion9, direccion10, fkCiudad, fkEstado, codigoPostal, fkPais, descripcion, facebook, twitter, googlePlus, status_prospecto, linkedin, skype, contactoWeb, idProspecto', 'safe', 'on'=>'search'),
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
