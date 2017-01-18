<?php
class MotivosincidenciasController extends Controller
{

    public function actionIndex() {

        $MotivosIncidencias = Yii::app()->db->createCommand()->select('*')->from('wrk_motivos_incidencias')->queryAll();

        $this->render('index', array('MotivosIncidenciasData' => $MotivosIncidencias));
    }

    public function actionCreate() {

        if (!empty($_POST['motivo'])) {

            try {
                //| id | estado  |

                $InsertData = "INSERT INTO wrk_motivos_incidencias(motivo, sucursal, status)
                    VALUES ( :motivo, :sucursal, :status)";

                $parameters = array(
                    ':motivo' => $_POST['motivo'],
                    ':sucursal' => $_POST['sucursal'],
                    ':status' => $_POST['status']
                );
                Yii::app()->db->createCommand($InsertData)->execute($parameters);
                $LasRHID= Yii::app()->db->getLastInsertID();


                Yii::app()->user->setFlash("success", "La informacion se ha Guardado exitosamente.");
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo. " . $e->getMessage());
            }
        }
        $this->redirect($this->createUrl("motivosincidencias/index"));
    }
    public function actionLoadForm() {

        if (!empty($_POST['GetData'])) {
            fb::info($_POST);
            $where = "id ='" . $_POST['GetData']['id'] . "'";
            $LoadForm = Yii::app()->db->createCommand()->select('*')->from('wrk_motivos_incidencias')->where($where)->queryAll();
            fb::info($LoadForm);
            if (!empty($LoadForm)) {
                echo CJSON::encode(array('requestresult' => 'ok', 'message' => "Seleccionando " . $LoadForm['id'] . "...", 'GetData' => $LoadForm[0]));
                fb::info($_POST);
            } else {
                echo CJSON::encode(array('requestresult' => 'fail', 'message' => "La información no se pudo seleccionar, intente de nuevo..."));
            }
            return;
        }
    }

    public function actionUpdate() {
        global $db;

        FB::INFO($_POST);

        if (!empty($_POST['Update'])) {
            $SQLUpdate = "UPDATE wrk_motivos_incidencias SET
            motivo = '" . $_POST['Update']['motivo'] . "',
            sucursal = '" . $_POST['Update']['sucursal'] . "',
            status = '" . $_POST['Update']['status'] . "'
            WHERE id = '" . $_POST['Update']['id'] . "'";
            if (DB_query($SQLUpdate, $db)) {

                if ($_POST['Update']['status'] == 1) $_POST['Update']['status'] = "Activo";
                else $_POST['Update']['status'] = "Inactivo";
                $NewRow = "";
                $NewRow.= "
                <td >{$_POST['Update']['id']}</td>
                <td >{$_POST['Update']['motivo']}</td>
                <td >{$_POST['Update']['sucursal']}</td>
                <td >{$_POST['Update']['status']}</td>
                <td >
                    <a title=\"Editar Motivo de Incidencias del Servicio\" onclick=\"EditarMotivosIncidencias('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
                    <a href='" . $this->createUrl("motivosincidencias/disable&id=" . $_POST['Update']['id']) . "' title=\"Desactivar Motivo de Incidencia del Servicio\" onclick=\"javascript:if(confirm('¿Esta seguro de desactivar este registro?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
                </td>";
                echo CJSON::encode(array('requestresult' => 'ok', 'message' => "Los datos se han actualizado correctamente...", 'NewRow' => $NewRow, 'id' => $_POST['Update']['id']));
            } else {
                echo CJSON::encode(array('requestresult' => 'fail', 'message' => "No se pudo actualizar, intente de nuevo"));
            }
        }
        return;
    }

    public function actionDisable() {

        if (!empty($_GET['id'])) {

            $Disable = "update wrk_motivos_incidencias set status= :status where id=:id";
            $parameters = array(':status' => 0, ':id' => $_GET['id']);

            if (Yii::app()->db->createCommand($Disable)->execute($parameters)) {

                Yii::app()->user->setFlash("success", "La información se ha actualizado correctamente");
                $this->redirect($this->createUrl("motivosincidencias/index"));
            } else {
                Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
                $this->redirect($this->createUrl("motivosincidencias/index"));
            }
        } else {

            Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
            $this->redirect($this->createUrl("motivosincidencias/index"));
        }
    }
}
?>
