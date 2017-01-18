<?php

class ClasificacionController extends Controller {


	public function actionLoadform() {
        global $db;
        if (!empty($_POST['GetData'])) {
            $_GetData = array();
            $GetData = "SELECT * FROM rh_clasificacion_empresas WHERE id = '" . $_POST['GetData']['id'] . "'";
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
                    'message' => "La Clasificacion no se pudo Seleccionar, intente de nuevo..."
                ));
            }
        }
        return;
    }

    public function actionIndex() {

        $ListData = Yii::app()->db->createCommand()->select(' * ')->from('rh_clasificacion_empresas')->queryAll();
        $this->render('index', array('ListData' => $ListData));
    }

    public function loadModel($id) {

        $model = Clasificacion::model()->find('id=' . $id);
        if ($model===null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function actionCreate() {

        if (!empty($_POST)) {

            try {
                

                $InsertData = "INSERT INTO rh_clasificacion_empresas (descripcion)
                VALUES(:descripcion)";
                $parameters = array(
                    ':descripcion' => $_POST['descripcion']
                    );
                Yii::app()->db->createCommand($InsertData)->execute($parameters);
                $LasRHID= Yii::app()->db->getLastInsertID();

                Yii::app()->user->setFlash("success", "La informacion se ha Guardado exitosamente.");
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo. " . $e->getMessage());
            }

        }
        $this->redirect(array('clasificacion/index'));
    }

    public function actionUpdate() {
         global $db;
        if (!empty($_POST['Update'])) {
            $SQLUpdate = "UPDATE rh_clasificacion_empresas SET
                            descripcion = '" . $_POST['Update']['descripcion'] . "'
                        WHERE id = '" . $_POST['Update']['id'] . "'
           ";
           if(DB_query($SQLUpdate, $db)){

			   $NewRow = "";
                $NewRow .= "
                            <td class=\" \">{$_POST['Update']['descripcion']}</td>
                            <td class=\" \">
                                <a title=\"Editar Clasificacion\" onclick=\"EditarClasificacion('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
                                <a href='" . $this->createUrl("clasificacion/delete&id=" . $_POST['Update']['id']) . "' title=\"Eliminar Clasificacion\" onclick=\"javascript:if(confirm('¿Esta seguro de Eliminar esta Clasificacion?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
                            </td>";

			   echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "La Clasificacion " . $_POST['Update']['descripcion'] . " se ha Actualizado Correctamente...",
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

    public function actionUpdateClasificacion() {

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
            if (Clasificacion::model()->deleteAll('id=' . $id)) {
                Yii::app()->user->setFlash("success", "La informacion de la clasificacion se ha eliminado.");
            }
            $this->redirect($this->createUrl("clasificacion/index"));
        } else {
            Yii::app()->user->setFlash("error", "Debe Seleccionar una clasificacion.");
            $this->redirect($this->createUrl("clasificacion/index"));
        }
    }

}
