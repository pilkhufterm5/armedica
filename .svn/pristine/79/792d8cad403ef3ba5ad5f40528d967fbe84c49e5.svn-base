<?php

class LeadsController extends Controller {

    public function actionIndex() {
        
        $this->render('index');
    }

    public function actionCreate() {
        
        FB::INFO($_POST, '_____POST');

        if(!empty($_POST)){
            $model = new RhLeads;
            $model->attributes = $_POST;

             FB::INFO( $model->attributes, '_______attr');
            if ($model->save()){
                Yii::app()->user->setFlash("success", "Lead successfully saved");
            }else{
                Yii::app()->user->setFlash("warning", "No se guardo el producto, por favor intente de nuevo.");
            }
        }
        $this->render('create');
    }

    public function actionView() {
        
        $this->render('view');
    }

}
?>