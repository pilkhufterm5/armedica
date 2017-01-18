<?php
class TipostarjetaController extends Controller
{

    public function actionIndex() {

        $TiposTarjeta = Yii::app()->db->createCommand()->select('*')->from('rh_tipotarjetas')->queryAll();

        $this->render('index', array('TiposTarjetaData' => $TiposTarjeta));
    }
    public function actionCreate() {

        if (!empty($_POST['tipotarjeta'])) {
            $TiposTarjeta = "INSERT INTO rh_tipotarjetas(tipotarjeta, status) VALUES ( :tipotarjeta, :status)";

            $parameters = array(
                ':tipotarjeta' => $_POST['tipotarjeta'],
                ':status' => $_POST['status']
            );

            try {
                Yii::app()->db->createCommand($TiposTarjeta)->execute($parameters);
                Yii::app()->user->setFlash("success", "La información se ha registrado correctamente");
                $LasRHID= Yii::app()->db->getLastInsertID();

                $SQLServerWS = new SQLServerWS();
                $Table = "CZA_TipoTarjetaCredito";
                $TableID = "IdTipoTarjetaCredito";
                $SQLServerWS->InsertTipoTarjetas($_POST, $Table, $TableID, $LasRHID);

                $this->redirect($this->createUrl("tipostarjeta/index"));
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo. " . $e->getMessage());
                $this->redirect($this->createUrl("tipostarjeta/index"));
            }

        } else {
            Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
            $this->redirect($this->createUrl("tipostarjeta/index"));
        }
    }

    public function actionLoadForm() {

        if (!empty($_POST['GetData'])) {
            fb::info($_POST);
            $where = "id ='" . $_POST['GetData']['id'] . "'";
            $LoadForm = Yii::app()->db->createCommand()->select('*')->from('rh_tipotarjetas')->where($where)->queryAll();
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
        if (!empty($_POST['Update'])) {
            $SQLUpdate = "UPDATE rh_tipotarjetas SET
            tipotarjeta = '" . $_POST['Update']['tipotarjeta'] . "',
            status = '" . $_POST['Update']['status'] . "'
            WHERE id = '" . $_POST['Update']['id'] . "'";
            if (DB_query($SQLUpdate, $db)) {

                if ($_POST['Update']['status'] == 1) $_POST['Update']['status'] = "Activo";
                else $_POST['Update']['status'] = "Inactivo";
                $NewRow = "";
                $NewRow.= "
                <td >{$_POST['Update']['id']}</td>
                <td >{$_POST['Update']['tipotarjeta']}</td>
                <td >{$_POST['Update']['status']}</td>
                <td >
                    <a title=\"Editar TiposTarjeta\" onclick=\"EditarTiposTarjeta('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
                    <a href='" . $this->createUrl("TiposTarjeta/disable&id=" . $_POST['Update']['id']) . "' title=\"Desactivar Tipo de Tarjeta\" onclick=\"javascript:if(confirm('¿Esta seguro de desactivar este registro?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
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

            $Disable = "update rh_tipotarjetas set status= :status where id=:id";
            $parameters = array(':status' => 0, ':id' => $_GET['id']);

            if (Yii::app()->db->createCommand($Disable)->execute($parameters)) {

                Yii::app()->user->setFlash("success", "La información se ha actualizado correctamente");
                $this->redirect($this->createUrl("tipostarjeta/index"));
            } else {
                Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
                $this->redirect($this->createUrl("tipostarjeta/index"));
            }
        } else {

            Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
            $this->redirect($this->createUrl("tipostarjeta/index"));
        }
    }
}
?>
