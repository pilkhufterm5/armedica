<?php

class PaymentmethodController extends Controller{

    public function actionLoadform() {
        global $db;
        if (!empty($_POST['GetData'])) {
            $_GetData = array();
            $GetData = "SELECT * FROM paymentmethods WHERE paymentid = '" . $_POST['GetData']['paymentid'] . "'";
            $_2GetData = DB_query($GetData, $db);
            $_GetData = DB_fetch_assoc($_2GetData, $db);
            if (!empty($_GetData)) {
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "Seleccionando " . $_GetData['paymentid'] . "...",
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

    public function actionIndex(){
        $PaymentmethodData = Yii::app()->db->createCommand()->select(' * ')->from('paymentmethods')->queryAll();
        $this->render('index', array('PaymentmethodData' => $PaymentmethodData));
    }

    public function loadModel($id){
        $model=Paymentmethod::model()->findByPk($id);
        if($model===null){
            throw new CHttpException(404,'The requested page does not exist.');
        }
        return $model;
    }

     public function actionCreate(){
        FB::INFO($_POST,'___________________________');
        //return;
        if (!empty($_POST['paymenttype'])) {
            $_POST['paymenttype'] = 1;
        } else {
            $_POST['paymenttype'] = 0;
        }

        if (!empty($_POST['receipttype'])) {
            $_POST['receipttype'] = 1;
        } else {
            $_POST['receipttype'] = 0;
        }
        /*
        +-------------+-------------+------+-----+---------+----------------+
        | Field       | Type        | Null | Key | Default | Extra          |
        +-------------+-------------+------+-----+---------+----------------+
        | paymentid   | tinyint(4)  | NO   | PRI | NULL    | auto_increment |
        | paymentname | varchar(50) | NO   |     | NULL    |                |
        | paymenttype | int(11)     | NO   |     | 1       |                |
        | receipttype | int(11)     | NO   |     | 1       |                |
        | activo      | tinyint(4)  | NO   |     | 1       |                |
        | arid        | int(11)     | YES  |     | NULL    |                |
        +-------------+-------------+------+-----+---------+----------------+
        */

        if (!empty($_POST['Save'])) {

            try {
                $InsertData = "INSERT INTO paymentmethods (paymentname,
                    satid, -- Se agrego satid para el identificador que requiere el SAT Angeles Perez 2016-07-12
                    satname, -- Se agrego para la descripcion del metodo de pago SAT Angeles Perez 2016-07-12
                    paymenttype,
                    receipttype,
                    activo)
                VALUES(:paymentname,
                    :satid-- Se agrego satid para el identificador que requiere el SAT Angeles Perez 2016-07-12
                    :satname, -- Se agrego para la descripcion del metodo de pago SAT Angeles Perez 2016-07-12
                    :paymenttype,
                    :receipttype,
                    :activo)";
                $parameters = array(
                    ':paymentname' => $_POST['paymentname'],
                    ':satid' => $_POST['satid'],//Se agrego satid para el identificador que requiere el SAT Angeles Perez 2016-07-12
                    ':satname' => $_POST['satname'],//Se agrego satname para el descripcion del metodo de pago SAT Angeles Perez 2016-07-12
                    ':paymenttype' => $_POST['paymenttype'],
                    ':receipttype' => $_POST['receipttype'],
                    ':activo' => 1
                    );
                Yii::app()->db->createCommand($InsertData)->execute($parameters);
                $LasRHID= Yii::app()->db->getLastInsertID();

                $SQLServerWS = new SQLServerWS();
                $Table = "CZA_FormaPago";
                $TableID = "IdFormaPago";
                $SQLServerWS->InsertPaymentMethod($_POST, $Table, $TableID, $LasRHID);

                Yii::app()->user->setFlash("success", "La informacion del metodo de pago se ha guardado exitosamente.");
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", "No se pudo guardar el Metodo de Pago, intente de nuevo. " . $e->getMessage());
            }
        }

     $this->redirect(array('paymentmethod/index'));
    }


    public function actionUpdate() {
       global $db;

        if (!empty($_POST['Update'])) {
            $SQLUpdate = "UPDATE paymentmethods SET
            paymentname = '" . $_POST['Update']['paymentname'] . "',
            satid = '" . $_POST['Update']['satid'] . "',
            satname = '" . $_POST['Update']['satname'] . "',
            paymenttype = '" . $_POST['Update']['paymenttype'] . "',
            receipttype = '" . $_POST['Update']['receipttype'] . "',
            receipttype = '" . $_POST['Update']['activo'] . "'
            WHERE paymentid = '" . $_POST['Update']['paymentid'] . "'
            ";

            if (DB_query($SQLUpdate, $db)) {
                if($_POST['Update']['paymenttype'] ==1)  $_POST['Update']['paymenttype'] = "Si"; else $_POST['Update']['paymenttype'] = "No";
                if($_POST['Update']['receipttype'] ==1)  $_POST['Update']['receipttype'] = "Si"; else $_POST['Update']['receipttype'] = "No";
                if($_POST['Update']['activo'] ==1)  $_POST['Update']['activo'] = "Si"; else $_POST['Update']['activo'] = "No";

                $NewRow = ""; // Se agrego satid y satname  para el identificador que requiere el SAT Angeles Perez 2016-07-12
                $NewRow .= " 
                <td >{$_POST['Update']['id']}</td>
                <td >{$_POST['Update']['paymentname']}</td>
                <td >{$_POST['Update']['satid']}</td>
                <td >{$_POST['Update']['satname']}</td>
                <td >{$_POST['Update']['paymenttype']}</td>
                <td >{$_POST['Update']['receipttype']}</td>
                <td >{$_POST['Update']['activo']}</td>
                <td >
                    <a title=\"Editar Forma de pago\" onclick=\"EditarFormapago('{$_POST['Update']['paymentid']}');\"><i class=\"icon-edit\"></i></a>
                    <a href='" . $this->createUrl("paymentmethod/delete&id=" . $_POST['Update']['paymentid']) . "' title=\"Eliminar Forma de pago\" onclick=\"javascript:if(confirm('¿Esta seguro de Eliminar esta forma de pago?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
                </td>";

                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "La forma de pago  " . $_POST['Update']['paymentname'] . " se ha Actualizado Correctamente...",
                    'NewRow' => $NewRow,
                    'paymentid' => $_POST['Update']['paymentid']
                ));
            }else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "No se pudo actualizar, intente de nuevo"
                ));
            }
        }
        return;
    }

    public function actionUpdateMetodo() {
        $model=$this->loadModel($_POST['paymentid']);
        $model->attributes=$_POST;
        if($_POST['paymenttype']=='on'){
            $model->paymenttype =1;
        }else{
            $model->paymenttype =0;
        }

        if($_POST['receipttype']=='on'){
            $model->receipttype =1;
        }else{
            $model->receipttype =0;
        }
        $model->save();
        unset($_POST);
        $this->actionIndex();
    }


    public function actionDelete2($id) {
        if (!empty($id)) {
            if (Paymentmethod::model()->findByPk($id)->delete()) {
                Yii::app()->user->setFlash("success", "La informacion del metodo de pago se ha eliminado.");
            }
            $this->redirect($this->createUrl("paymentmethod/index"));
        } else {
            Yii::app()->user->setFlash("error", "Debe Seleccionar un metodo de pago.");
            $this->redirect($this->createUrl("paymentmethod/index"));
        }
    }

    public function actionDelete($id) {
        if (!empty($id)) {
            //Ponemos la Combinacion en Activo = 0
             $SQLDelete = "UPDATE paymentmethods SET activo = :activo WHERE paymentid = :paymentid";
             $parameters = array(
                ':activo' => 0,
                ':paymentid' => $id
            );
            if (Yii::app()->db->createCommand($SQLDelete)->execute($parameters)) {
                Yii::app()->user->setFlash("success", "El Metodo de Pago se ha desactivado.");
            }else{
                Yii::app()->user->setFlash("success", "No se desactivo el Metodo de Pago, intente de nuevo.");
            }
            $this->redirect($this->createUrl("paymentmethod/index"));
        } else {
            Yii::app()->user->setFlash("error", "Debe Seleccionar una Metodo de Pago.");
            $this->redirect($this->createUrl("paymentmethod/index"));
        }
    }


}
