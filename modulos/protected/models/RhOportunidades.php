<?php

/**
 * This is the model class for table "rh_oportunidades".
 *
 * The followings are the available columns in table 'rh_oportunidades':
 * @property integer $id
 * @property string $debtorno
 * @property string $branchcode
 * @property integer $lead_id
 * @property string $folio
 * @property string $nombre
 * @property string $apellido
 * @property integer $initial_contact
 * @property string $estatus
 * @property string $ocupacion
 * @property string $calle
 * @property string $colonia
 * @property string $entrecalles
 * @property string $municipio
 * @property integer $cp
 * @property string $telefono
 * @property string $telefono2
 * @property string $fax
 * @property string $email
 * @property string $referidopor
 * @property string $nombre_empresa
 * @property string $razon_social
 * @property string $clasificacion
 * @property integer $nempleados
 * @property string $turnos
 * @property string $medico_planta
 * @property string $visitantes
 * @property string $tiempo_permanencia
 * @property string $dias_publico
 * @property string $plan_cotizado
 * @property integer $socios
 * @property string $forma_pago
 * @property string $frecuencia_pago
 * @property string $convenio_empresa
 * @property string $inscripcion_pmes
 * @property string $segundo_mes
 * @property string $sales_stage
 * @property string $probabilidad
 * @property string $monto
 * @property integer $asignada_a
 * @property integer $campania
 * @property integer $lead_source
 */
class RhOportunidades extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'rh_oportunidades';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('debtorno, branchcode, lead_id, folio, nombre, apellido, initial_contact, estatus, ocupacion, calle, colonia, entrecalles, municipio, cp, telefono, telefono2, fax, email, referidopor, nombre_empresa, razon_social, clasificacion, nempleados, turnos, medico_planta, visitantes, tiempo_permanencia, dias_publico, plan_cotizado, socios, forma_pago, frecuencia_pago, convenio_empresa, inscripcion_pmes, segundo_mes, sales_stage', 'required'),
			array('lead_id, initial_contact, cp, nempleados, socios, asignada_a, campania, lead_source', 'numerical', 'integerOnly'=>true),
			array('debtorno, branchcode, folio, referidopor, clasificacion, turnos, medico_planta, visitantes, tiempo_permanencia, dias_publico, plan_cotizado, forma_pago, frecuencia_pago, convenio_empresa', 'length', 'max'=>10),
			array('nombre, apellido, ocupacion, nombre_empresa, razon_social', 'length', 'max'=>100),
			array('estatus', 'length', 'max'=>13),
			array('calle, colonia, entrecalles, municipio', 'length', 'max'=>200),
			array('telefono, telefono2, fax', 'length', 'max'=>35),
			array('email', 'length', 'max'=>50),
			array('inscripcion_pmes, segundo_mes, monto', 'length', 'max'=>20),
			array('sales_stage', 'length', 'max'=>45),
			array('probabilidad', 'length', 'max'=>8),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, debtorno, branchcode, lead_id, folio, nombre, apellido, initial_contact, estatus, ocupacion, calle, colonia, entrecalles, municipio, cp, telefono, telefono2, fax, email, referidopor, nombre_empresa, razon_social, clasificacion, nempleados, turnos, medico_planta, visitantes, tiempo_permanencia, dias_publico, plan_cotizado, socios, forma_pago, frecuencia_pago, convenio_empresa, inscripcion_pmes, segundo_mes, sales_stage, probabilidad, monto, asignada_a, campania, lead_source', 'safe', 'on'=>'search'),
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
			//id' => 'ID',
			//'debtorno' => 'Debtorno',
			//'branchcode' => 'Branchcode',
			//'lead_id' => 'Lead',
			//'folio' => 'Folio',
			//'nombre' => 'Nombre',
			//'apellido' => 'Apellido',
			//'initial_contact' => 'Initial Contact',
			//'estatus' => 'Estatus',
			//'ocupacion' => 'Ocupacion',
			//'calle' => 'Calle',
			//'colonia' => 'Colonia',
			//'entrecalles' => 'Entrecalles',
			//'municipio' => 'Municipio',
			//'cp' => 'Cp',
			//'telefono' => 'Telefono',
			//'telefono2' => 'Telefono2',
			//'fax' => 'Fax',
			//'email' => 'Email',
			//'referidopor' => 'Referidopor',
			//'nombre_empresa' => 'Nombre Empresa',
			//'razon_social' => 'Razon Social',
			//'clasificacion' => 'Clasificacion',
			//'nempleados' => 'Nempleados',
			//'turnos' => 'Turnos',
			//'medico_planta' => 'Medico Planta',
			//'visitantes' => 'Visitantes',
			//'tiempo_permanencia' => 'Tiempo Permanencia',
			'dias_publico' => 'Dias Publico',
			'plan_cotizado' => 'Plan Cotizado',
			'socios' => 'Socios',
			'forma_pago' => 'Forma Pago',
			'frecuencia_pago' => 'Frecuencia Pago',
			'convenio_empresa' => 'Convenio Empresa',
			'inscripcion_pmes' => 'Inscripcion Pmes',
			'segundo_mes' => 'Segundo Mes',
			'sales_stage' => 'Sales Stage',
			'probabilidad' => 'Probabilidad',
			'monto' => 'Monto',
			'asignada_a' => 'Asignada A',
			'campania' => 'Campania',
			'lead_source' => 'Lead Source',
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
		$criteria->compare('lead_id',$this->lead_id);
		$criteria->compare('folio',$this->folio,true);
		$criteria->compare('nombre',$this->nombre,true);
		$criteria->compare('apellido',$this->apellido,true);
		$criteria->compare('initial_contact',$this->initial_contact);
		$criteria->compare('estatus',$this->estatus,true);
		$criteria->compare('ocupacion',$this->ocupacion,true);
		$criteria->compare('calle',$this->calle,true);
		$criteria->compare('colonia',$this->colonia,true);
		$criteria->compare('entrecalles',$this->entrecalles,true);
		$criteria->compare('municipio',$this->municipio,true);
		$criteria->compare('cp',$this->cp);
		$criteria->compare('telefono',$this->telefono,true);
		$criteria->compare('telefono2',$this->telefono2,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('referidopor',$this->referidopor,true);
		$criteria->compare('nombre_empresa',$this->nombre_empresa,true);
		$criteria->compare('razon_social',$this->razon_social,true);
		$criteria->compare('clasificacion',$this->clasificacion,true);
		$criteria->compare('nempleados',$this->nempleados);
		$criteria->compare('turnos',$this->turnos,true);
		$criteria->compare('medico_planta',$this->medico_planta,true);
		$criteria->compare('visitantes',$this->visitantes,true);
		$criteria->compare('tiempo_permanencia',$this->tiempo_permanencia,true);
		$criteria->compare('dias_publico',$this->dias_publico,true);
		$criteria->compare('plan_cotizado',$this->plan_cotizado,true);
		$criteria->compare('socios',$this->socios);
		$criteria->compare('forma_pago',$this->forma_pago,true);
		$criteria->compare('frecuencia_pago',$this->frecuencia_pago,true);
		$criteria->compare('convenio_empresa',$this->convenio_empresa,true);
		$criteria->compare('inscripcion_pmes',$this->inscripcion_pmes,true);
		$criteria->compare('segundo_mes',$this->segundo_mes,true);
		$criteria->compare('sales_stage',$this->sales_stage,true);
		$criteria->compare('probabilidad',$this->probabilidad,true);
		$criteria->compare('monto',$this->monto,true);
		$criteria->compare('asignada_a',$this->asignada_a);
		$criteria->compare('campania',$this->campania);
		$criteria->compare('lead_source',$this->lead_source);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RhOportunidades the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
