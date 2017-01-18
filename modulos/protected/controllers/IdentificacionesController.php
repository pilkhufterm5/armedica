<?php
class IdentificacionesController extends Controller{

	public function actionIndex(){

		$IdentificacionesData=Yii::app()->db->createCommand()
		->select('*')
		->from('rh_identificaciones')
		->queryAll();


		$this->render('index', array('IdentificacionesData'=>$IdentificacionesData));

	}
	public function actionCreate(){

		if(!empty($_POST['nombre'])){
			$Identificacion="insert into rh_identificaciones(nombre, sucursal, status) values ( :nombre, :sucursal, :status)";

			$parameters=array(
				':nombre'=>$_POST['nombre'],
				':sucursal'=>$_POST['sucursal'],
				':status'=>$_POST['status']
				);
			if(Yii::app()->db->createCommand($Identificacion)->execute($parameters)){

				Yii::app()->user->setFlash("success", "La información se ha registrado correctamente");
				$this->redirect($this->createUrl("identificaciones/index"));
			}else{
				Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
				$this->redirect($this->createUrl("identificaciones/index"));
			}
		}else{

			Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
			$this->redirect($this->createUrl("identificaciones/index"));

		}
	}

    public function actionLoadForm(){

    	if(!empty($_POST['GetData'])){
    		fb::info($_POST);
    		$where="id ='".$_POST['GetData']['id']."'";
    		$LoadForm=Yii::app()->db->createCommand()
    		->select('*')
    		->from('rh_identificaciones')
    		->where($where)
    		->queryAll();
    		fb::info($LoadForm);
    		if(!empty($LoadForm)){
    			echo CJSON::encode(array(
    				'requestresult' => 'ok',
    				'message' => "Seleccionando " . $LoadForm['id'] . "...",
    				'GetData' => $LoadForm[0]
    				));
    			fb::info($_POST);
    		} else {
    			echo CJSON::encode(array(
    				'requestresult' => 'fail',
    				'message' => "La información no se pudo seleccionar, intente de nuevo..."
    				));
    		}
    		return;
    	}

    }

    public function actionUpdate() {
    	global $db;
    	if (!empty($_POST['Update'])) {
    		$SQLUpdate = "UPDATE rh_identificaciones SET
    		nombre = '" . $_POST['Update']['nombre'] . "',
    		sucursal = '" . $_POST['Update']['sucursal'] . "',
    		status = '" . $_POST['Update']['status'] . "'
    		WHERE id = '" . $_POST['Update']['id'] . "'";
    		if (DB_query($SQLUpdate, $db)) {

    			if($_POST['Update']['status'] ==1)  $_POST['Update']['status'] = "Activo"; else $_POST['Update']['status'] = "Inactivo";
    			$NewRow = "";
    			$NewRow .= "
    			<td >{$_POST['Update']['id']}</td>
    			<td >{$_POST['Update']['nombre']}</td>
    			<td >{$_POST['Update']['sucursal']}</td>
    			<td >{$_POST['Update']['status']}</td>
    			<td >
    				<a title=\"Editar Identificación\" onclick=\"EditarIdentificaciones('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
    				<a href='" . $this->createUrl("Identificaciones/disable&id=" . $_POST['Update']['id']) . "' title=\"Desactivar Identificación\" onclick=\"javascript:if(confirm('¿Esta seguro de desactivar este registro?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
    			</td>";
    			echo CJSON::encode(array(
    				'requestresult' => 'ok',
    				'message' => "Los datos se han actualizado correctamente...",
    				'NewRow' => $NewRow,
    				'id' => $_POST['Update']['id']
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

    public function actionDisable(){

    	if(!empty($_GET['id'])){

    		$Disable="update rh_identificaciones set status= :status where id=:id";
    		$parameters=array(':status' => 0, ':id'=>$_GET['id']);

    		if(Yii::app()->db->createCommand($Disable)->execute($parameters)){

    			Yii::app()->user->setFlash("success", "La información se ha actualizado correctamente");
    			$this->redirect($this->createUrl("identificaciones/index"));
    		}else{
    			Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
    			$this->redirect($this->createUrl("identificaciones/index"));
    		}
    	}else{

    		Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
    		$this->redirect($this->createUrl("identificaciones/index"));

    	}

    }

}

?>
