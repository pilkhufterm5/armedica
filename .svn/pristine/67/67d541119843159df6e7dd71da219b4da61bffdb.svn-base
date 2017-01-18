<?php

class ConveniosController extends Controller {

    public $layout = '//layouts/main';

    public function actionLoadform() {
        global $db;
        if (!empty($_POST['GetData'])) {
            $_GetData = array();
            $GetData = "SELECT * FROM rh_convenios WHERE id = '" . $_POST['GetData']['id'] . "'";
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
        $ConveniosData = Yii::app()->db->createCommand()->select(' * ')->from('rh_convenios')->queryAll();
        $this->render('index', array('ConveniosData' => $ConveniosData));
    }

    public function actionCreate() {

        if (!empty($_POST['convenio'])) {

            $sql = "insert into rh_convenios (convenio,bcotrans,activo) values (:convenio, :bcotrans, :activo)";
            $parameters = array(
                ':convenio' => $_POST['convenio'],
                ':bcotrans' => $_POST['bcotrans'],
                ':activo' => $_POST['activo']
            );
            Yii::app()->db->createCommand($sql)->execute($parameters);
            Yii::app()->user->setFlash("success", "La informacion se ha Guardado exitosamente.");
        }
        $this->redirect(array('convenios/index'));
    }

    public function actionUpdate() {

        global $db;
        if (!empty($_POST['Update'])) {

            $SQLUpdate = "UPDATE rh_convenios SET
                            convenio = '" . $_POST['Update']['convenio'] . "',
                            bcotrans = '" . $_POST['Update']['bcotrans'] . "',
                            activo = '" . $_POST['Update']['activo'] . "'
                        WHERE id = '" . $_POST['Update']['id'] . "'
           ";

            if (DB_query($SQLUpdate, $db)) {
                if($_POST['Update']['activo'] ==1)  $_POST['Update']['activo'] = "Activo"; else $_POST['Update']['activo'] = "Inactivo";
                $NewRow = "";
                $NewRow .= "
                            <td class=\" \">{$_POST['Update']['convenio']}</td>
                            <td class=\" \">{$_POST['Update']['bcotrans']}</td>
                            <td class=\" \">{$_POST['Update']['activo']}</td>
                            <td class=\" \">
                                <a title=\"Editar Convenio\" onclick=\"EditarConvenios('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
                                <a href='" . $this->createUrl("convenios/delete&id=" . $_POST['Update']['id']) . "' title=\"Desactivar Convenio\" onclick=\"javascript:if(confirm('¿Esta seguro de desactivar este convenio?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
                            </td>";

					echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El convenio  " . $_POST['Update']['convenio'] . " se ha Actualizado Correctamente...",
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
    public function actionDelete() {
        if (!empty($_GET['id'])) {

            $Update="update rh_convenios set activo = :activo where id=:id";
            $parameters=array(
                ':activo'=>0,
                ':id'=>$_GET['id']
                );

            if (Yii::app()->db->createCommand($Update)->execute($parameters)) {
                Yii::app()->user->setFlash("success", "El convenio ha sido desactivado.");
            }
            $this->redirect($this->createUrl("convenios/index"));
        } else {
            Yii::app()->user->setFlash("error", "Debe Seleccionar un Cobrador.");
            $this->redirect($this->createUrl("convenios/index"));
        }
    }

}
