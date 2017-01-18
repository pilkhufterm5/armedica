<?php

/**
 * This is the model class for table "rh_comisionistas".
 *
 * The followings are the available columns in table 'rh_comisionistas':
 * @property integer $id
 * @property string $comisionista
 * @property integer $coordina_id
 * @property integer $activo
 */
class Comisionista extends CActiveRecord {
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'rh_comisionistas';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'coordina_id, activo',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'comisionista',
                'length',
                'max' => 150
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, comisionista, coordina_id, activo',
                'safe',
                'on' => 'search'
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'comisionista' => 'Comisionista',
            'coordina_id' => 'Coordina',
            'activo' => 'Activo',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('comisionista', $this->comisionista, true);
        $criteria->compare('coordina_id', $this->coordina_id);
        $criteria->compare('activo', $this->activo);

        return new CActiveDataProvider($this, array('criteria' => $criteria, ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Comisionista the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
