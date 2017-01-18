<?php
class ParentescoController extends Controller{


	public function actionIndex(){

		$ParentescoData=Yii::app()->db->createCommand()
		->select('*')
		->from('rh_parentescos')
		->queryAll();


		$this->render('index', array('ParentescoData'=>$ParentescoData));

	}

	public function actionCreate(){

		if(!empty($_POST['parentesco'])){
			$Parentesco="insert into rh_parentescos(nombre, sucursal, status) values ( :nombre, :sucursal, :status)";

			$parameters=array(
				':nombre'=>$_POST['parentesco'],
				':sucursal'=>$_POST['sucursal'],
                ':status'=>$_POST['status']
				);
			if(Yii::app()->db->createCommand($Parentesco)->execute($parameters)){

				Yii::app()->user->setFlash("success", "La información se ha registrado correctamente");
                $this->redirect($this->createUrl("parentesco/index"));
			}else{
              Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
              $this->redirect($this->createUrl("parentesco/index"));
            }
		}else{

            Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
            $this->redirect($this->createUrl("parentesco/index"));

        }
    }

    public function actionLoadForm(){

    if(!empty($_POST['GetData'])){
        fb::info($_POST);
        $where="id ='".$_POST['GetData']['id']."'";
        $LoadForm=Yii::app()->db->createCommand()
        ->select('*')
        ->from('rh_parentescos')
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
            $SQLUpdate = "UPDATE rh_parentescos SET
                            nombre = '" . $_POST['Update']['parentesco'] . "',
                            sucursal = '" . $_POST['Update']['sucursal'] . "',
                            status = '" . $_POST['Update']['status'] . "'
                        WHERE id = '" . $_POST['Update']['id'] . "'";
            if (DB_query($SQLUpdate, $db)) {

            if($_POST['Update']['status'] ==1)  $_POST['Update']['status'] = "Activo"; else $_POST['Update']['status'] = "Inactivo";
                $NewRow = "";
                $NewRow .= "
                            <td >{$_POST['Update']['id']}</td>
                            <td >{$_POST['Update']['parentesco']}</td>
                            <td >{$_POST['Update']['sucursal']}</td>
                            <td >{$_POST['Update']['status']}</td>
                            <td >
                                <a title=\"Editar Parentesco\" onclick=\"EditarParentesco('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
                                <a href='" . $this->createUrl("parentesco/disable&id=" . $_POST['Update']['id']) . "' title=\"Desactivar Parentesco\" onclick=\"javascript:if(confirm('¿Esta seguro de desactivar este registro?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
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

            $Disable="update rh_parentescos set status= :status where id=:id";
            $parameters=array(':status' => 0, ':id'=>$_GET['id']);

        if(Yii::app()->db->createCommand($Disable)->execute($parameters)){

                Yii::app()->user->setFlash("success", "La información se ha actualizado correctamente");
                $this->redirect($this->createUrl("parentesco/index"));
            }else{
              Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
              $this->redirect($this->createUrl("parentesco/index"));
            }
        }else{

            Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
            $this->redirect($this->createUrl("parentesco/index"));

        }

    }

}

?>
