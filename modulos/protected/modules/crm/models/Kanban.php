<?php

/**
 * This is the model class for table "rh_kanban".
 *
 * The followings are the available columns in table 'rh_kanban':
 * @property integer $kanban_id
 * @property string $kanban_title
 * @property string $kanban_columns
 * @property string $kanban_cards
 * @property string $kanban_date_created
 * @property string $kanban_date_modified
 * @property string $kanban_owner
 */
class Kanban extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'rh_kanban';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(/*
            array('kanban_title, kanban_date_created, kanban_date_modified', 'required'),                        
            array('kanban_title, kanban_columns', 'length', 'max' => 255),
            array('kanban_cards', 'length', 'max' => 65,535),
            array('kanban_date_created', 'date', 'format'=>'Y/m/d h:i:s'),
            array('kanban_date_modified', 'date', 'format'=>'Y/m/d h:i:s'),*/            
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // TODO: Agregar relacion con la clase del usuario logueado 
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'kanban_id' => 'Kanban Id',
            'kanban_title' => 'Titulo',
            'kanban_columns' => 'Columnas',
            'kanban_cards' => 'Tarjetas',
            'kanban_date_created' => 'Fecha de Creación',
            'kanban_date_modified' => 'Modificado el',
            'kanban_owner' => 'Dueño de archivo',
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
        //TODO: Set the search term to $query
        $query = '';
        
        $criteria = new CDbCriteria;
        
        $criteria->compare('kanban_title', $this->kanban_title, true, 'OR');
        $criteria->condition = "kanban_columns LIKE :query";
        $criteria->params = array(':query' => trim($query).'%');

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CrmContacto the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}
