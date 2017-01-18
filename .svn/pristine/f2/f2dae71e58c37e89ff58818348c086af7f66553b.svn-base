<?php

class NotasController extends Controller {

    public function actionCreateAjax() {
        $arr = array('response' => 'ok');
        if($_POST['titulo']!='' && $_POST['descripcion']!=''){
            $model = new RhNotas;
            $model->attributes = $_POST;
            $model->fechaalta = new CDbExpression('NOW()');
            if($model->save())
                $arr = array('response' => 'ok');
        }
        else{
            $arr = array('response' => 'fail');
        }
    echo json_encode($arr);
    }


}
?>