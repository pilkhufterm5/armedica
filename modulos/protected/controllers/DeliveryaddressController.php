<?php

/**
 * Crud : Direcciones de Entrega par las Ordenes de Compra.
 * @author erasto@realhost.com.mx
 */
class DeliveryaddressController extends Controller {

    public function actionIndex() {
        $GetPaginationData = array();
        $GetPaginationData = Yii::app()->db->createCommand()->select(' * ')->from('rh_direcciones_entrega')->queryAll();
        $ListaMunicipios = CHtml::listData(Municipio::model()->findAll(), 'id', 'municipio');
        $ListaEstados = CHtml::listData(Estado::model()->findAll(), 'id', 'estado');
        $this->render('index', array(
            'GetPaginationData' => $GetPaginationData,
            'ListaMunicipios' => $ListaMunicipios,
            'ListaEstados' => $ListaEstados,
        ));
    }

    public function actionCreate() {
        global $db;
        if (!empty($_POST['loccode']) && !empty($_POST['locationname'])) {

            $SQLInsert = "INSERT INTO rh_direcciones_entrega 
                       (loccode,
                        locationname,
                        deladd1,
                        deladd2,
                        deladd3,
                        deladd4,
                        deladd5,
                        deladd6,
                        deladd7,
                        deladd8,
                        deladd9,
                        deladd10,
                        tel,
                        fax,
                        email,
                        contact
                        )
                    VALUES
                       ('" . $_POST['loccode'] . "',
                        '" . $_POST['locationname'] . "',
                        '" . $_POST['deladd1'] . "',
                        '" . $_POST['deladd2'] . "',
                        '" . $_POST['deladd3'] . "',
                        '" . $_POST['deladd4'] . "',
                        '" . $_POST['deladd5'] . "',
                        '" . $_POST['deladd6'] . "',
                        '" . $_POST['deladd7'] . "',
                        '" . $_POST['deladd8'] . "',
                        '" . $_POST['deladd9'] . "',
                        '" . $_POST['deladd10'] . "',
                        '" . $_POST['tel'] . "',
                        '" . $_POST['fax'] . "',
                        '" . $_POST['email'] . "',
                        '" . $_POST['contact'] . "'
                        )
                    ";
            if (DB_query($SQLInsert, $db)) {
                Yii::app()->user->setFlash("success", "La Direccion de Envio se ha Guardado Correctamente.");
            } else {
                Yii::app()->user->setFlash("error", "No se pudo guardar la Direccion de Envio, intente de nuevo.");
            }
        }
        $this->redirect(array('deliveryaddress/index'));
    }

    public function actionLoadform() {
        global $db;
        if (!empty($_POST['GetData'])) {
            $_GetData = array();
            $GetData = "SELECT * FROM rh_direcciones_entrega WHERE loccode = '" . $_POST['GetData']['loccode'] . "'";
            $_2GetData = DB_query($GetData, $db);
            $_GetData = DB_fetch_assoc($_2GetData, $db);
            if (!empty($_GetData)) {
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "Seleccionando " . $_GetData['loccode'] . "...",
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

    public function actionUpdate() {
        global $db;
        if (!empty($_POST['Update'])) {
            $SQLUpdate = "UPDATE rh_direcciones_entrega SET 
                            locationname = '" . $_POST['Update']['locationname'] . "',
                            deladd1 = '" . $_POST['Update']['deladd1'] . "',
                            deladd2 = '" . $_POST['Update']['deladd2'] . "',
                            deladd3 = '" . $_POST['Update']['deladd3'] . "',
                            deladd4 = '" . $_POST['Update']['deladd4'] . "',
                            deladd5 = '" . $_POST['Update']['deladd5'] . "',
                            deladd6 = '" . $_POST['Update']['deladd6'] . "',
                            deladd7 = '" . $_POST['Update']['deladd7'] . "',
                            deladd8 = '" . $_POST['Update']['deladd8'] . "',
                            deladd9 = '" . $_POST['Update']['deladd9'] . "',
                            deladd10 = '" . $_POST['Update']['deladd10'] . "',
                            tel = '" . $_POST['Update']['tel'] . "',
                            fax = '" . $_POST['Update']['fax'] . "',
                            email = '" . $_POST['Update']['email'] . "',
                            contact = '" . $_POST['Update']['contact'] . "'
                        WHERE loccode = '" . $_POST['Update']['loccode'] . "'
           ";

            if (DB_query($SQLUpdate, $db)) {
                $NewRow = "";
                $NewRow .= "
                            <td class=\"sorting_1\">{$_POST['Update']['loccode']}</td>
                            <td class=\" \">{$_POST['Update']['locationname']}</td>
                            <td class=\" \">
                                <a title=\"Editar Dirección\" onclick=\"EditAddress('{$_POST['Update']['loccode']}');\"><i class=\"icon-edit\"></i></a>
                                <a href='" . $this->createUrl("deliveryaddress/delete&id=" . $_POST['Update']['loccode']) . "' title=\"Eliminar Dirección\" onclick=\"javascript:if(confirm('¿Esta seguro de ELiminar esta Dirección?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
                            </td>";
                
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "La Direccion  " . $_POST['Update']['loccode'] . " se ha Actualizado Correctamente...",
                    'NewRow' => $NewRow,
                    'loccode' => $_POST['Update']['loccode']
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
        global $db;
        if (!empty($id)) {
            $SQLDelete = "DELETE FROM rh_direcciones_entrega WHERE loccode = '" . $id . "'";
            if (DB_query($SQLDelete, $db, "", "", true)) {
                Yii::app()->user->setFlash("success", "La Direccion de Envio se ha Eliminado Correctamente.");
            } else {
                Yii::app()->user->setFlash("error", "No se pudo Eliminar la Direccion de Envio, intente de nuevo.");
            }
        }
        $this->redirect(array('deliveryaddress/index'));
    }

}
?>