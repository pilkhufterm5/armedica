<?php

/**
 * This is the model class for table "www_users".
 *
 * The followings are the available columns in table 'www_users':
 * @property string $userid
 * @property string $password
 * @property string $realname
 * @property string $customerid
 * @property string $phone
 * @property string $email
 * @property string $defaultlocation
 * @property integer $fullaccess
 * @property string $lastvisitdate
 * @property string $branchcode
 * @property string $pagesize
 * @property string $modulesallowed
 * @property integer $blocked
 * @property integer $displayrecordsmax
 * @property string $theme
 * @property string $language
 * @property integer $rh_updatecost
 * @property string $rh_permitionlocation
 * @property string $rh_pagelock
 */
class User extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'www_users';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {

        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(array('password, rh_pagelock', 'required'), array('fullaccess, blocked, displayrecordsmax, rh_updatecost', 'numerical', 'integerOnly' => true), array('userid, pagesize, modulesallowed', 'length', 'max' => 20), array('realname', 'length', 'max' => 35), array('customerid, branchcode', 'length', 'max' => 10), array('phone, theme', 'length', 'max' => 30), array('email', 'length', 'max' => 55), array('defaultlocation, language', 'length', 'max' => 5), array('rh_permitionlocation', 'length', 'max' => 255), array('lastvisitdate', 'safe'),

        // The following rule is used by search().
        // @todo Please remove those attributes that should not be searched.
        array('userid, password, realname, customerid, phone, email, defaultlocation, fullaccess, lastvisitdate, branchcode, pagesize, modulesallowed, blocked, displayrecordsmax, theme, language, rh_updatecost, rh_permitionlocation, rh_pagelock', 'safe', 'on' => 'search'),);
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
        return array('userid' => 'Userid', 'password' => 'Password', 'realname' => 'Realname', 'customerid' => 'Customerid', 'phone' => 'Phone', 'email' => 'Email', 'defaultlocation' => 'Defaultlocation', 'fullaccess' => 'Fullaccess', 'lastvisitdate' => 'Lastvisitdate', 'branchcode' => 'Branchcode', 'pagesize' => 'Pagesize', 'modulesallowed' => 'Modulesallowed', 'blocked' => 'Blocked', 'displayrecordsmax' => 'Displayrecordsmax', 'theme' => 'Theme', 'language' => 'Language', 'rh_updatecost' => 'Rh Updatecost', 'rh_permitionlocation' => 'Rh Permitionlocation', 'rh_pagelock' => 'Rh Pagelock',);
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

        $criteria->compare('userid', $this->userid, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('realname', $this->realname, true);
        $criteria->compare('customerid', $this->customerid, true);
        $criteria->compare('phone', $this->phone, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('defaultlocation', $this->defaultlocation, true);
        $criteria->compare('fullaccess', $this->fullaccess);
        $criteria->compare('lastvisitdate', $this->lastvisitdate, true);
        $criteria->compare('branchcode', $this->branchcode, true);
        $criteria->compare('pagesize', $this->pagesize, true);
        $criteria->compare('modulesallowed', $this->modulesallowed, true);
        $criteria->compare('blocked', $this->blocked);
        $criteria->compare('displayrecordsmax', $this->displayrecordsmax);
        $criteria->compare('theme', $this->theme, true);
        $criteria->compare('language', $this->language, true);
        $criteria->compare('rh_updatecost', $this->rh_updatecost);
        $criteria->compare('rh_permitionlocation', $this->rh_permitionlocation, true);
        $criteria->compare('rh_pagelock', $this->rh_pagelock, true);

        return new CActiveDataProvider($this, array('criteria' => $criteria,));
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function validatePassword($password) {
        return $this->hashPassword($password) === $this->password;
        //return $this->hashPassword($password,$this->salt)===$this->password;

    }

    public function hashPassword($password, $salt) {
        return md5($password);
        //return md5($salt.$password);

    }

    protected function generateSalt() {
        return uniqid('', true);
    }
}
