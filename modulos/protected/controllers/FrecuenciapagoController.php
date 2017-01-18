<?php
class FrecuenciapagoController extends Controller
{

    public $layout = '//layouts/main';

    public function actionIndex() {

        $FrecuenciaPagoData = Yii::app()->db->createCommand()->select(' * ')->from('rh_frecuenciapago')->queryAll();
        $this->render('index', array('FrecuenciaPagoData' => $FrecuenciaPagoData));
    }

    public function actionLoadform() {

        global $db;
        if (!empty($_POST['GetData'])) {
            $_GetData = array();
            $GetData = "SELECT * FROM rh_frecuenciapago WHERE id = '" . $_POST['GetData']['id'] . "'";
            $_2GetData = DB_query($GetData, $db);
            $_GetData = DB_fetch_assoc($_2GetData, $db);
            if (!empty($_GetData)) {
                echo CJSON::encode(array('requestresult' => 'ok', 'message' => "Seleccionando " . $_GetData['id'] . "...", 'GetData' => $_GetData));
            } else {
                echo CJSON::encode(array('requestresult' => 'fail', 'message' => "La Direccion no se pudo Seleccionar, intente de nuevo..."));
            }
        }
        return;
    }

    public function loadModel($id) {

        $model = FrecuenciaPago::model()->findByPk($id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function actionCreate() {
        if (!empty($_POST)) {

            try {
                //| id | frecuencia  | dias | sucursal |

                $InsertData = "INSERT INTO rh_frecuenciapago (frecuencia,
                    dias)
                VALUES(:frecuencia,
                    :dias)";
                $parameters = array(
                    ':frecuencia' => $_POST['frecuencia'],
                    ':dias' => $_POST['dias']
                    );
                Yii::app()->db->createCommand($InsertData)->execute($parameters);
                $LasRHID= Yii::app()->db->getLastInsertID();

                $SQLServerWS = new SQLServerWS();
                $Table = "CZA_FrecuenciaPago";
                $TableID = "IdFrecuenciaPago";
                $SQLServerWS->InsertFrecuenciaPago($_POST, $Table, $TableID, $LasRHID);

                Yii::app()->user->setFlash("success", "La informacion se ha Guardado exitosamente.");
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo. " . $e->getMessage());
            }

        }

        $this->redirect(array('frecuenciapago/index'));
    }


    public function actionUpdateFrecuencia() {

        $model = $this->loadModel($_POST['id']);
        $model->attributes = $_POST;
        $model->save();
        unset($_POST);
        $this->actionIndex();
    }

    public function actionUpdate() {
        global $db;
        if (!empty($_POST['Update'])) {
            $SQLUpdate = "UPDATE rh_frecuenciapago SET
                            frecuencia = '" . $_POST['Update']['frecuencia'] . "',
                            dias = '" . $_POST['Update']['dias'] . "'
                        WHERE id = '" . $_POST['Update']['id'] . "'
           ";
            if (DB_query($SQLUpdate, $db)) {
                $NewRow = "";
                $NewRow.= "
                            <td class=\" \">{$_POST['Update']['frecuencia']}</td>
                            <td class=\" \">{$_POST['Update']['dias']}</td>
                            <td class=\" \">
                                <a title=\"Editar Frecuencia\" onclick=\"EditarFrecuencia('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
                                <a href='" . $this->createUrl("coordinadores/delete&id=" . $_POST['Update']['id']) . "'
                                title=\"Eliminar Frecuencia\" onclick=\"javascript:if(confirm('¿Esta seguro de Eliminar esta frecuencia?')) { return; }
                                else{return false;};\"><i class=\"icon-trash\"></i></a>
                            </td>";

                echo CJSON::encode(array('requestresult' => 'ok', 'message' => "La frecuencia " . $_POST['Update']['frecuencia'] . " se ha Actualizado Correctamente...", 'NewRow' => $NewRow, 'id' => $_POST['Update']['id']));
            } else {
                echo CJSON::encode(array('requestresult' => 'fail', 'message' => "No se pudo actualizar, intente de nuevo"));
            }
        }
        return;
    }

    public function actionDelete($id) {
        if (!empty($id)) {
            if (FrecuenciaPago::model()->findByPk($id)->delete()) {
                Yii::app()->user->setFlash("success", "La informacion de la frecuencia de pago se ha eliminado.");
            }
            $this->redirect($this->createUrl("frecuenciapago/index"));
        } else {
            Yii::app()->user->setFlash("error", "Debe Seleccionar una frecuencia de pago.");
            $this->redirect($this->createUrl("frecuenciapago/index"));
        }
    }
}
