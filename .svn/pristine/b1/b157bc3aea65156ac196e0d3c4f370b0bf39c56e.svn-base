<?php

/**
 * This is the model class for table "rh_coordinadores".
 *
 * The followings are the available columns in table 'rh_coordinadores':
 * @property integer $id
 * @property integer $coordina_id
 * @property string $coordinador
 */
class Coordinadores extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'rh_coordinadores';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			//array('coordina_id', 'required'),
			array('id, activo', 'numerical', 'integerOnly'=>true),
			array('coordinador', 'length', 'max'=>150),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, coordinador', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Coordinadores the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
