<?php

class MunicipiosController extends Controller {


	public function actionLoadform() {
        global $db;
        if (!empty($_POST['GetData'])) {
            $_GetData = array();
            $GetData = "SELECT * FROM rh_municipios WHERE id = '" . $_POST['GetData']['id'] . "'";
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
                    'message' => "El municipio no se pudo Seleccionar, intente de nuevo..."
                ));
            }
        }
        return;
    }

    public function actionIndex() {

        $ListData = Yii::app()->db->createCommand()->select(' * ')->from('rh_municipios')->queryAll();
        $this->render('index', array('ListData' => $ListData));
    }

    public function loadModel($id) {

        $model = Municipio::model()->find('id=' . $id);
        if ($model===null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function actionCreate() {

        if (!empty($_POST)) {

            try {
                //| id | estado  |

                $InsertData = "INSERT INTO rh_municipios (municipio)
                VALUES(:municipio)";
                $parameters = array(
                    ':municipio' => $_POST['municipio']
                    );
                Yii::app()->db->createCommand($InsertData)->execute($parameters);
                $LasRHID= Yii::app()->db->getLastInsertID();

                $SQLServerWS = new SQLServerWS();
                $Table = "CZA_Municipio";
                $TableID = "IdMunicipio";
                $SQLServerWS->InsertMunicipios($_POST, $Table, $TableID, $LasRHID);

                Yii::app()->user->setFlash("success", "La informacion se ha Guardado exitosamente.");
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo. " . $e->getMessage());
            }

        }
        $this->redirect(array('municipios/index'));
    }

    public function actionUpdate() {
         global $db;
        if (!empty($_POST['Update'])) {
            $SQLUpdate = "UPDATE rh_municipios SET
                            municipio = '" . $_POST['Update']['municipio'] . "'
                        WHERE id = '" . $_POST['Update']['id'] . "'
           ";
           if(DB_query($SQLUpdate, $db)){

			   $NewRow = "";
                $NewRow .= "
                            <td class=\" \">{$_POST['Update']['municipio']}</td>
                            <td class=\" \">
                                <a title=\"Editar Municipio\" onclick=\"EditarMunicipio('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
                                <a href='" . $this->createUrl("municipios/delete&id=" . $_POST['Update']['id']) . "' title=\"Eliminar Municipio\" onclick=\"javascript:if(confirm('¿Esta seguro de Eliminar este municipio?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
                            </td>";

			   echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El municipio  " . $_POST['Update']['municipio'] . " se ha Actualizado Correctamente...",
                    'NewRow' => $NewRow,
                    'id' => $_POST['Update']['id']
                ));

		   }else{
				echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "No se pudo actualizar, intente de nuevo"
                ));
			}

	}
	return;

}

    public function actionUpdateMunicipio() {

        $model = $this->loadModel($_POST['id']);
        $model->attributes = $_POST;
        $model->save();
        unset($_POST);
        $this->actionIndex();
    }

    public function actionDelete($id) {
        if (!empty($id)) {
            //Como la tabla no tiene un PK definido, la instrucion findByPk arrojará ua excepción
            //En vez de eso, usamos deleteAll
            if (Municipio::model()->deleteAll('id=' . $id)) {
                Yii::app()->user->setFlash("success", "La informacion del municipio se ha eliminado.");
            }
            $this->redirect($this->createUrl("municipios/index"));
        } else {
            Yii::app()->user->setFlash("error", "Debe Seleccionar un municipio.");
            $this->redirect($this->createUrl("municipios/index"));
        }
    }

}
