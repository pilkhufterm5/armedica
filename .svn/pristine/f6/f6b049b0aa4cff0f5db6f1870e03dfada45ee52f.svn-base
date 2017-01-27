<?php

/**
 * This is the model class for table "rh_crmcontacto".
 *
 * The followings are the available columns in table 'rh_crmcontacto':
 * @property integer $id_contacto
 * @property string $nombre
 * @property string $apellido_paterno
 * @property string $apellido_materno
 * @property string $email
 * @property string $telefono_particular
 * @property string $fecha_nacimiento
 * @property string $titulo
 * @property string $departamento
 * @property integer $no_enviar_correo
 * @property integer $no_llamar_telefono
 * @property integer $asginado_a
 * @property string $fecha_alta
 * @property string $fecha_ultima_actualizacion
 * @property integer $usuario_alta
 * @property string $descripcion
 * @property integer $contacto_activo
 * @property string $telefono_empresa
 * @property integer $cliente
 * @property string $calle1
 * @property string $colonia1
 * @property string $entre_calles1
 * @property integer $ciudad1
 * @property integer $estado1
 * @property string $calle2
 * @property string $colonia2
 * @property integer $ciudad2
 * @property integer $estado2
 * @property string $entre_calles2
 * @property string $skype
 * @property string $facebook
 * @property string $twitter
 * @property string $googlplus
 * @property string $imagen
 */
class CCContacto extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'rh_crmcontacto';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nombre, apellido_paterno, email, asginado_a', 'required'),
			array('no_enviar_correo, no_llamar_telefono, asginado_a, usuario_alta, contacto_activo, cliente, ciudad1, estado1, ciudad2, estado2', 'numerical', 'integerOnly'=>true),
			array('nombre, apellido_paterno, apellido_materno, telefono_particular, titulo, telefono_empresa', 'length', 'max'=>30),
			array('email', 'length', 'max'=>100),
			array('departamento', 'length', 'max'=>40),
			array('descripcion', 'length', 'max'=>160),
			array('calle1, colonia1, calle2, colonia2, skype', 'length', 'max'=>85),
			array('entre_calles1, entre_calles2', 'length', 'max'=>125),
			array('facebook, twitter, googlplus, imagen', 'length', 'max'=>45),
			array('fecha_nacimiento', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id_contacto, nombre, apellido_paterno, apellido_materno, email, telefono_particular, fecha_nacimiento, titulo, departamento, no_enviar_correo, no_llamar_telefono, asginado_a, fecha_alta, fecha_ultima_actualizacion, usuario_alta, descripcion, contacto_activo, telefono_empresa, cliente, calle1, colonia1, entre_calles1, ciudad1, estado1, calle2, colonia2, ciudad2, estado2, entre_calles2, skype, facebook, twitter, googlplus, imagen', 'safe', 'on'=>'search'),
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
			'id_contacto' => 'Id Contacto',
			'nombre' => 'Nombre',
			'apellido_paterno' => 'Apellido paterno',
			'apellido_materno' => 'Apellido materno',
			'email' => 'Email',
			'telefono_particular' => 'Telefono particular',
			'fecha_nacimiento' => 'Fecha de nacimiento',
			'titulo' => 'Titulo',
			'departamento' => 'Departamento',
			'no_enviar_correo' => 'No contactar por correo',
			'no_llamar_telefono' => 'No contactar por teléfono',
			'asginado_a' => 'Asginado a',
			'fecha_alta' => 'Fecha de alta',
			'fecha_ultima_actualizacion' => 'Fecha de última actualización',
			'usuario_alta' => 'Registrado por ',
			'descripcion' => 'Descripción',
			'contacto_activo' => 'Contacto activo',
			'telefono_empresa' => 'Telefono de empresa',
			'cliente' => 'Cliente',
			'calle1' => 'Calle',
			'colonia1' => 'Colonia',
			'entre_calles1' => 'Entre Calles',
			'ciudad1' => 'Ciudad',
			'estado1' => 'Estado',
			'calle2' => 'Calle',
			'colonia2' => 'Colonia',
			'ciudad2' => 'Ciudad',
			'estado2' => 'Estado',
			'entre_calles2' => 'Entre calles',
			'skype' => 'Skype',
			'facebook' => 'Facebook',
			'twitter' => 'Twitter',
			'googlplus' => 'Google Plus',
			'imagen' => 'Imagen',
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

		$criteria->compare('id_contacto',$this->id_contacto);
		$criteria->compare('nombre',$this->nombre,true);
		$criteria->compare('apellido_paterno',$this->apellido_paterno,true);
		$criteria->compare('apellido_materno',$this->apellido_materno,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('telefono_particular',$this->telefono_particular,true);
		$criteria->compare('fecha_nacimiento',$this->fecha_nacimiento,true);
		$criteria->compare('titulo',$this->titulo,true);
		$criteria->compare('departamento',$this->departamento,true);
		$criteria->compare('no_enviar_correo',$this->no_enviar_correo);
		$criteria->compare('no_llamar_telefono',$this->no_llamar_telefono);
		$criteria->compare('asginado_a',$this->asginado_a);
		$criteria->compare('fecha_alta',$this->fecha_alta,true);
		$criteria->compare('fecha_ultima_actualizacion',$this->fecha_ultima_actualizacion,true);
		$criteria->compare('usuario_alta',$this->usuario_alta);
		$criteria->compare('descripcion',$this->descripcion,true);
		$criteria->compare('contacto_activo',$this->contacto_activo);
		$criteria->compare('telefono_empresa',$this->telefono_empresa,true);
		$criteria->compare('cliente',$this->cliente);
		$criteria->compare('calle1',$this->calle1,true);
		$criteria->compare('colonia1',$this->colonia1,true);
		$criteria->compare('entre_calles1',$this->entre_calles1,true);
		$criteria->compare('ciudad1',$this->ciudad1);
		$criteria->compare('estado1',$this->estado1);
		$criteria->compare('calle2',$this->calle2,true);
		$criteria->compare('colonia2',$this->colonia2,true);
		$criteria->compare('ciudad2',$this->ciudad2);
		$criteria->compare('estado2',$this->estado2);
		$criteria->compare('entre_calles2',$this->entre_calles2,true);
		$criteria->compare('skype',$this->skype,true);
		$criteria->compare('facebook',$this->facebook,true);
		$criteria->compare('twitter',$this->twitter,true);
		$criteria->compare('googlplus',$this->googlplus,true);
		$criteria->compare('imagen',$this->imagen,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RhCrmcontacto the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
