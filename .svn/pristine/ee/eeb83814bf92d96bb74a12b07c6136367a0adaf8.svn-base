<?php

class EstadosController extends Controller {

    public function actionLoadform() {
        global $db;
        if (!empty($_POST['GetData'])) {
            $_GetData = array();
            $GetData = "SELECT * FROM rh_estados WHERE id = '" . $_POST['GetData']['id'] . "'";
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
                    'message' => "El Estado no se pudo Seleccionar, intente de nuevo..."
                ));
            }
        }
        return;
    }

    public function actionIndex() {

        $ListData = Yii::app()->db->createCommand()->select(' * ')->from('rh_estados')->queryAll();
        $this->render('index', array('ListData' => $ListData));
    }

    public function loadModel($id) {

        $model = Estado::model()->find('id=' . $id);
        if ($model===null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function actionCreate() {

        if (!empty($_POST)) {

            try {
                //| id | estado  |

                $InsertData = "INSERT INTO rh_estados (estado)
                VALUES(:estado)";
                $parameters = array(
                    ':estado' => $_POST['estado']
                    );
                Yii::app()->db->createCommand($InsertData)->execute($parameters);
                $LasRHID= Yii::app()->db->getLastInsertID();

                $SQLServerWS = new SQLServerWS();
                $Table = "CZA_Estado";
                $TableID = "IdEstado";
                $SQLServerWS->InsertEstados($_POST, $Table, $TableID, $LasRHID);

                Yii::app()->user->setFlash("success", "La informacion se ha Guardado exitosamente.");
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo. " . $e->getMessage());
            }
        }
        $this->redirect(array('estados/index'));
    }

    public function actionUpdate() {
        global $db;
        if (!empty($_POST['Update'])) {
            $SQLUpdate = "UPDATE rh_estados SET
                            estado = '" . $_POST['Update']['estado'] . "'
                        WHERE id = '" . $_POST['Update']['id'] . "'
           ";
            if (DB_query($SQLUpdate, $db)) {

                $NewRow = "";
                $NewRow .= "
                            <td class=\" \">{$_POST['Update']['estado']}</td>
                            <td class=\" \">
                                <a title=\"Editar Estado\" onclick=\"EditarEstado('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
                                <a href='" . $this->createUrl("estados/delete&id=" . $_POST['Update']['id']) . "' title=\"Eliminar Estado\" onclick=\"javascript:if(confirm('¿Esta seguro de Eliminar este Estado?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
                            </td>";

                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El Estado  " . $_POST['Update']['estado'] . " se ha Actualizado Correctamente...",
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

    public function actionDelete($id) {
        if (!empty($id)) {
            //Como la tabla no tiene un PK definido, la instrucion findByPk arrojará ua excepción
            //En vez de eso, usamos deleteAll
            if (Estado::model()->deleteAll('id=' . $id)) {
                Yii::app()->user->setFlash("success", "La informacion del Estado se ha eliminado.");
            }
            $this->redirect($this->createUrl("estados/index"));
        } else {
            Yii::app()->user->setFlash("error", "Debe Seleccionar un Estado.");
            $this->redirect($this->createUrl("estados/index"));
        }
    }

}
