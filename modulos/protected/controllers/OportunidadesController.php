<?php

class OportunidadesController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	 public function actionCreate() {
        
        FB::INFO($_POST, '_____POST');

        if(!empty($_POST)){
            $model = new RhOportunidades;
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
    
	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}