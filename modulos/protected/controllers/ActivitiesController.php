<?php

class ActivitiesController extends Controller {

    public function actionIndex() {

        $this->render('index');
    }

    public function actionCalendar() {

        $this->render('calendar');
    }


    public function actionSaveactivities(){
        
    }


    public function actionLoadactivities() {
        /*
         title: 'Long Event',
         start: new Date(y, m, d-5),
         end: new Date(y, m, d-2)
         */

        $LoadActivities = array(
        array(
            'id'=>'1',
            'title' => 'Long Event',
            'start' => '2013-11-26',
            'end' => '2013-11-26'),
        array(
            'id'=>'2',
            'title' => 'Long Event2',
            'start' => '2013-11-27',
            'end' => '2013-11-27'),
        array(
            'id'=>'3',
            'title' => 'Long Event3',
            'start' => '2013-11-28',
            'end' => '2013-11-28'),
        );
        echo CJSON::encode($LoadActivities);
        return;
        //Yii::app()->end();

    }

}
?>