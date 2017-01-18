<?php

class EmpresasController extends Controller {

    public $layout = '//layouts/main';

	public function actionLoadform() {
        global $db;
        if (!empty($_POST['GetData'])) {
            $_GetData = array();
            $GetData = "SELECT * FROM rh_empresas WHERE id = '" . $_POST['GetData']['id'] . "'";
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

    public function actionIndex() {
        $EmpresasData = Yii::app()->db->createCommand()->select(' * ')->from('rh_empresas')->queryAll();
        $this->render('index', array('EmpresasData' => $EmpresasData));
    }

    public function loadModel($id) {
        $model = Empresa::model()->findByPk($id);
        if ($model===null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function actionCreate() {
        if (!empty($_POST)) {

            $Save = "insert into rh_empresas (empresa,folio)values(:empresa,:folio)";
            $parameters = array(
                ':empresa' => $_POST['empresa'],
                ':folio' => $_POST['folio']
            );
            if (Yii::app()->db->createCommand($Save)->execute($parameters)) {
                Yii::app()->user->setFlash("success", "La informacion de la empresa se ha Guardado exitosamente.");
            } else {
                Yii::app()->user->setFlash("error", "No se pudo agregar la empresa, intente de nuevo.");
            }
        }
        $this->redirect($this->createUrl("empresas/index"));
    }

    public function actionUpdate() {
        global $db;
        if (!empty($_POST['Update'])) {
            $SQLUpdate = "UPDATE rh_empresas SET 
                          empresa = '" . $_POST['Update']['empresa'] . "',
                            folio = '" . $_POST['Update']['folio'] . "'                                                  
                        WHERE id = '" . $_POST['Update']['id'] . "'
           ";           
            if (DB_query($SQLUpdate, $db)) {
                $NewRow = "";
                $NewRow .= "
                            <td class=\" \">{$_POST['Update']['empresa']}</td>
                            <td class=\" \">{$_POST['Update']['folio']}</td>                           
                            <td class=\" \">
                                <a title=\"Editar Empresa\" onclick=\"EditarEmpresa('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
                                <a href='" . $this->createUrl("empresas/delete&id=" . $_POST['Update']['id']) . "' 
                                title=\"Eliminar Coordinador\" onclick=\"javascript:if(confirm('¿Esta seguro de Eliminar esta empresa?')) { return; }
                                else{return false;};\"><i class=\"icon-trash\"></i></a>
                            </td>";
                            
					echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "La empresa " . $_POST['Update']['empresa'] . " se ha Actualizado Correctamente...",
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

    public function actionUpdateEmpresa() {
        $model = $this->loadModel($_POST['id']);
        $model->attributes = $_POST;
        $model->save();
        unset($_POST);
        $this->actionIndex();
    }

    public function actionDelete($id) {
        if (!empty($id)) {
            if (Empresa::model()->findByPk($id)->delete()) {
                Yii::app()->user->setFlash("success", "La informacion de la empresa se ha eliminado.");
            }
            $this->redirect($this->createUrl("empresas/index"));
        } else {
            Yii::app()->user->setFlash("error", "Debe Seleccionar una empresa.");
            $this->redirect($this->createUrl("empresas/index"));
        }
    }

}
