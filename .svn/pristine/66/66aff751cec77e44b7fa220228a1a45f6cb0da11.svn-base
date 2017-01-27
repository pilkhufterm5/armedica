<?php
class ComisionistaController extends Controller
{

    public $layout = '//layouts/main';

    public function actionLoadform() {
        global $db;
        if (!empty($_POST['GetData'])) {
            $_GetData = array();
            $GetData = "SELECT * FROM rh_comisionistas WHERE id = '" . $_POST['GetData']['id'] . "'";
            $_2GetData = DB_query($GetData, $db);
            $_GetData = DB_fetch_assoc($_2GetData, $db);
            if (!empty($_GetData)) {
                echo CJSON::encode(array('requestresult' => 'ok', 'message' => "Seleccionando " . $_GetData['id'] . "...", 'GetData' => $_GetData));
            } else {
                echo CJSON::encode(array('requestresult' => 'fail', 'message' => "El comisionista no se pudo Seleccionar, intente de nuevo..."));
            }
        }
        return;
    }

    public function actionIndex() {
        $ComisionistaData = Yii::app()->db->createCommand()->select('com.id, com.comisionista,com.activo, coor.coordinador')
        ->from('rh_comisionistas com')
        ->leftjoin('rh_coordinadores coor', 'coor.id = com.coordina_id')->queryAll();
        // "select com.id, com.comisionista,com.activo, coor.coordinador FROM rh_comisionistas as com inner join rh_coordinadores as coor on coor.coordina_id=com.coordina_id;";
        //$ComisionistaData = Yii::app()->db->createCommand($sql)->query();
        $ListaCorrdinadores = CHtml::listData(Coordinadores::model()->findAll(), 'id', 'coordinador');

        $this->render('index', array('ComisionistaData' => $ComisionistaData, 'ListaCorrdinadores' => $ListaCorrdinadores));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Comisionista the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Comisionista::model()->findByPk($id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Comisionista $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'comisionista-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionCreate() {
        FB::INFO($_POST,'__________________________CREATE');
        if (isset($_POST['activo'])) {
            $_POST['activo'] = 1;
        } else {
            $_POST['activo'] = 0;
        }

        if (!empty($_POST)) {

            try {
                //| id | comisionista                 | coordina_id | activo |
                $InsertData = "INSERT INTO rh_comisionistas (comisionista,
                    coordina_id,
                    activo)
                VALUES(:comisionista,
                    :coordina_id,
                    :activo)";
                $parameters = array(
                    ':comisionista' => $_POST['comisionista'],
                    ':coordina_id' => $_POST['coordina_id'],
                    ':activo' => 1
                    );
                Yii::app()->db->createCommand($InsertData)->execute($parameters);
                $LasRHID= Yii::app()->db->getLastInsertID();

                $SQLServerWS = new SQLServerWS();
                $Table = "CZA_Asesor";
                $TableID = "IdAsesor";
                $SQLServerWS->InsertAsesores($_POST, $Table, $TableID, $LasRHID);

                Yii::app()->user->setFlash("success", "La informacion se ha Guardado exitosamente.");
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo. " . $e->getMessage());
            }
        }
        $this->redirect($this->createUrl("comisionista/index"));
    }

    public function actionUpdate() {
        global $db;
        if (!empty($_POST['Update'])) {
            $SQLUpdate = "UPDATE rh_comisionistas SET
                            comisionista = '" . $_POST['Update']['comisionista'] . "',
                            coordina_id = '" . $_POST['Update']['coordina_id'] . "',
                            activo = '" . $_POST['Update']['activo'] . "'
                        WHERE id = '" . $_POST['Update']['id'] . "'";
            if (DB_query($SQLUpdate, $db)) {
                if ($_POST['Update']['activo'] == 1) $_POST['Update']['activo'] = "Activo";
                else $_POST['Update']['activo'] = "Inactivo";
                $ListaCorrdinadores = CHtml::listData(Coordinadores::model()->findAll(), 'id', 'coordinador');
                 //Selecciona coordinadores
                $NewRow = "";
                $NewRow.= "
                            <td class=\" \">{$_POST['Update']['comisionista']}</td>
                            <td class=\" \">{$ListaCorrdinadores[$_POST['Update']['coordina_id']]}</td>
                            <td class=\" \">{$_POST['Update']['activo']}</td>
                            <td class=\" \">
                                <a title=\"Editar Comisionista\" onclick=\"EditarComisionista('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
                                <a href='" . $this->createUrl("comisionista/delete&id=" . $_POST['Update']['id']) . "' title=\"Eliminar Comisionista\" onclick=\"javascript:if(confirm('¿Esta seguro de Eliminar este comisionista?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
                            </td>";
                echo CJSON::encode(array('requestresult' => 'ok', 'message' => "El comisionista  " . $_POST['Update']['comisionista'] . " se ha Actualizado Correctamente...", 'NewRow' => $NewRow, 'id' => $_POST['Update']['id']));
            } else {
                echo CJSON::encode(array('requestresult' => 'fail', 'message' => "No se pudo actualizar, intente de nuevo"));
            }
        }
        return;
    }

    public function actionUpdateComisionista() {
        $model = $this->loadModel($_POST['id']);
        $model->attributes = $_POST;
        $model->save();
        unset($_POST);
        $this->actionIndex();
    }

    public function actionDelete() {
        if (!empty($_GET['id'])) {

            $Update = "update rh_comisionistas set activo = :activo where id=:id";
            $parameters = array(':activo' => 0, ':id' => $_GET['id']);

            if (Yii::app()->db->createCommand($Update)->execute($parameters)) {
                Yii::app()->user->setFlash("success", "El comisionista ha sido desactivado.");
            }
            $this->redirect($this->createUrl("comisionista/index"));
        } else {
            Yii::app()->user->setFlash("error", "Debe Seleccionar un Cobrador.");
            $this->redirect($this->createUrl("comisionista/index"));
        }
    }
}
