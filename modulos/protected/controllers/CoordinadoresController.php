<?php

class CoordinadoresController extends Controller
{

	public $layout = '//layouts/main';


	public function actionLoadform() {
        global $db;
        if (!empty($_POST['GetData'])) {
            $_GetData = array();
            $GetData = "SELECT * FROM rh_coordinadores WHERE id = '" . $_POST['GetData']['coordina_id'] . "'";
            $_2GetData = DB_query($GetData, $db);
            $_GetData = DB_fetch_assoc($_2GetData, $db);
            if (!empty($_GetData)) {
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "Seleccionando " . $_GetData['id'] . "...",
                    'GetData' => $_GetData
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "La Direccion no se pudo Seleccionar, intente de nuevo..."
                ));
            }
        }
        return;
    }

	public function actionIndex()
	{

        $CoordinadoresData = Yii::app()->db->createCommand()->select(' * ')->from('rh_coordinadores')->queryAll();

        $this->render('index', array('coordinadoresData' => $CoordinadoresData));
	}


    public function loadModel($id)
	{
		$model=Coordinadores::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	public function actionCreate()
	{

		if (!empty($_POST['coordinador'])) {
            $model=new Coordinadores;
            $model->attributes = $_POST;
            if($model->save()){
                Yii::app()->user->setFlash("success", "La informacion del coordinador se ha Guardado exitosamente.");
            }
        }
        $this->redirect($this->createUrl("coordinadores/index"));
    }


 	public function actionUpdateCoordinador() {

 			$model=$this->loadModel($_POST['coordina_id']);
            $model->attributes=$_POST;
			$model->save();
			unset($_POST);

            $this->actionIndex();
    	}

	  public function actionUpdate() {
        global $db;

        if (!empty($_POST['Update'])) {
            $SQLUpdate = "UPDATE rh_coordinadores SET
                    coordinador = '{$_POST['Update']['coordinador']}',
                    activo = '{$_POST['Update']['activo']}'
                WHERE id = '" . $_POST['Update']['coordina_id'] . "'
           ";
           FB::INFO($SQLUpdate, 'select coordinadores');
            if (DB_query($SQLUpdate, $db)) {

                $Estatus = "Inactivo";
                if($_POST['Update']['activo'] == 1){
                    $Estatus = "Activo";
                }

                $NewRow = "";
                $NewRow .= "
                    <td >{$_POST['Update']['coordina_id']}</td>
                    <td >{$_POST['Update']['coordinador']}</td>
                    <td >{$Estatus}</td>
                    <td >
                        <a title=\"Editar Coordinador\" onclick=\"EditarCoordinador('{$_POST['Update']['coordina_id']}');\"><i class=\"icon-edit\"></i></a>
                        <a href='" . $this->createUrl("coordinadores/delete&id=" . $_POST['Update']['coordina_id']) . "'
                        title=\"Eliminar Coordinador\" onclick=\"javascript:if(confirm('¿Esta seguro de Eliminar este coordinador?')) { return; }
                        else{return false;};\"><i class=\"icon-trash\"></i></a>
                    </td>";

                    echo CJSON::encode(array(
                        'requestresult' => 'ok',
                        'message' => "El coordinador " . $_POST['Update']['coordinador'] . " se ha Actualizado Correctamente...",
                        'NewRow' => $NewRow,
                        'coordina_id' => $_POST['Update']['coordina_id']
                    ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "No se pudo actualizar, intente de nuevo"
                ));
            }
        }
        return;
    }


    public function actionDelete($id) {
        if (!empty($id)) {
            try {
                $Disable = "UPDATE rh_coordinadores SET activo = 0 WHERE id = :id";
                $Parameters = array(':id' => $id);
                Yii::app()->db->createCommand($Disable)->execute($Parameters);

                Yii::app()->user->setFlash("success", "Coordinador se ha desactivado.");
                $this->redirect($this->createUrl("coordinadores/index"));

            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", "NO se pudo Desactivar el  Coordinador. " . $e->getMessage());
                $this->redirect($this->createUrl("coordinadores/index"));
            }
        } else {
            Yii::app()->user->setFlash("error", "Debe Seleccionar un Coordinador.");
            $this->redirect($this->createUrl("coordinadores/index"));
        }
    }
}
