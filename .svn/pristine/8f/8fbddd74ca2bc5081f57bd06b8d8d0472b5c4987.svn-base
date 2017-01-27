<?php
/**
 *
 */
class CobradoresController extends Controller {

    public $layout = '//layouts/main';

    public function actionIndex() {
        $CobradoresData = Yii::app()->db->createCommand()->select(' * ')->from('rh_cobradores')->queryAll();

        $this->render('index', array('CobradoresData' => $CobradoresData));
    }

	public function actionLoadform() {
        global $db;
        if (!empty($_POST['GetData'])) {
            $_GetData = array();
            $GetData = "SELECT * FROM rh_cobradores WHERE id = '" . $_POST['GetData']['id'] . "'";
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


    public function actionCreate() {


        if (!empty($_POST['nombre'])) {

            if (isset($_POST['activo'])) {
                $_POST['activo'] = 1;
            } else {
                $_POST['activo'] = 0;
            }

            if (isset($_POST['transfe'])) {
                $_POST['transfe'] = 1;
            } else {
                $_POST['transfe'] = 0;
            }

            if (isset($_POST['empresa'])) {
                $_POST['empresa'] = 1;
            } else {
                $_POST['empresa'] = 0;
            }

            $sql = "insert into rh_cobradores (nombre,comision,zona,activo,reasigna,cobori,empresa) values (:nombre, :comision, :zona, :activo, :reasigna, :cobori, :empresa)";
            $parameters = array(
                ':nombre' => $_POST['nombre'],
                ':comision' => $_POST['comision'],
                ':zona' => $_POST['zona'],
                ':activo' => $_POST['activo'],
                ':reasigna' => $_POST['reasigna'],
                ':cobori' => $_POST['cobori'],
                ':empresa' => $_POST['empresa']
            );
            try {
                Yii::app()->db->createCommand($sql)->execute($parameters);
                $LasRHID= Yii::app()->db->getLastInsertID();

                $SQLServerWS = new SQLServerWS();
                $Table = "CZA_Cobrador";
                $TableID = "IdCobrador";
                $SQLServerWS->InsertCobradores($_POST, $Table, $TableID, $LasRHID);

                Yii::app()->user->setFlash("success", "La informacion se ha Guardado exitosamente.");
                $this->redirect(array('cobradores/index'));
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo. " . $e->getMessage());
                $this->redirect($this->createUrl("tipostarjeta/index"));
            }
        }

    }

    public function actionUpdate() {
        global $db;
        if (!empty($_POST['Update'])) {
        FB::INFO($_POST,'______________________POST');
            $SQLUpdate = "UPDATE rh_cobradores SET
                            nombre = '" . $_POST['Update']['nombre'] . "',
                            comision = '" . $_POST['Update']['comision'] . "',
                            zona = '" . $_POST['Update']['zona'] . "',
                            activo = '" . $_POST['Update']['activo'] . "',
                            reasigna = '" . $_POST['Update']['reasigna'] . "',
                            cobori = '" . $_POST['Update']['cobori'] . "',
                            empresa = '" . $_POST['Update']['empresa'] . "'
                        WHERE id = '" . $_POST['Update']['id'] . "' ";
                        FB::INFO($SQLUpdate,'________________________SQL');
                if (DB_query($SQLUpdate, $db)) {
                     if($_POST['Update']['activo'] ==1){
                        $_POST['Update']['activo'] = "Activo";
                    }else{
                        $_POST['Update']['activo'] = "Inactivo";
                    }

                    $NewRow = "";
                    $NewRow .= "
                                <td>{$_POST['Update']['nombre']}</td>
                                <td>{$_POST['Update']['comision']}</td>
                                <td>{$_POST['Update']['zona']}</td>
                                <td>{$_POST['Update']['activo']}</td>
                                <td>
                                    <a title=\"Editar Cobrador\" onclick=\"EditarCobrador('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
                                    <a href='" . $this->createUrl("cobradores/delete&id=" . $_POST['Update']['id']) . "'
                                    title=\"Eliminar Cobrador\" onclick=\"javascript:if(confirm('¿Esta seguro de Eliminar este cobrador?')) { return; }
                                    else{return false;};\"><i class=\"icon-trash\"></i></a>
                                </td>";

    					echo CJSON::encode(array(
                            'requestresult' => 'ok',
                            'message' => "El cobrador  " . $_POST['Update']['nombre'] . " se ha Actualizado Correctamente...",
                            'NewRow' => $NewRow,
                            'id' => $_POST['Update']['id']
                        ));
                }else {
                FB::INFO($e,'___________________ERROR');
               echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "No se pudo actualizar, intente de nuevo"
                ));
           }


        }
        return;
    }

    public function loadModel($id)
    {
        $model=Cobradores::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }


    public function actionUpdate2() {
        if ($_POST['activo']=='on') {
            $_POST['activo'] = 1;
        } else {
            if(!$_POST['activo'])
                $_POST['activo'] = 0;
        }

        if ($_POST['transfe']=='on') {
            $_POST['transfe'] = 1;
        } else {
            if(!$_POST['transfe'])
                $_POST['transfe'] = 0;
        }

        if ($_POST['empresa']=='on') {
            $_POST['empresa'] = 1;
        } else {
            if(!$_POST['empresa'])
                $_POST['empresa'] = 0;
        }
        FB::INFO(($_POST['activo']), 'tipo de activ');
         FB::INFO(($_POST['transfe']), 'tipo de transfes');
         FB::INFO(($_POST['empresa']), 'tipo de empresa');

        $SQLUpdateCobradores = "UPDATE rh_cobradores SET nombre = :nombre,comision = :comision, zona = :zona, activo = :activo, reasigna = :reasigna, cobori = :cobori, empresa = :empresa WHERE id = :id";
        $parameters = array(
            ":nombre" => $_POST['nombre'],
            ":comision" => $_POST['comision'],
            ":zona" => $_POST['zona'],
            ":activo" => $_POST['activo'],
            ":reasigna" => $_POST['reasigna'],
            ":cobori" => $_POST['cobori'],
            ":empresa" => $_POST['empresa'],
            ":id" => $_POST['id']
        );
        Yii::app()->db->createCommand($SQLUpdateCobradores)->execute($parameters);
        $CobradoresData = Yii::app()->db->createCommand()->select(' * ')->from('rh_cobradores')->queryAll();
        $this->render('index', array('CobradoresData' => $CobradoresData));
    }

    public function actionDelete() {
        if (!empty($_GET['id'])) {

            $Update="update rh_cobradores set activo = :activo where id=:id";
            $parameters=array(
                ':activo'=>0,
                ':id'=>$_GET['id']
                );

            if (Yii::app()->db->createCommand($Update)->execute($parameters)) {
                Yii::app()->user->setFlash("success", "El cobrador ha sido desactivado.");
            }
            $this->redirect($this->createUrl("cobradores/index"));
        } else {
            Yii::app()->user->setFlash("error", "Debe Seleccionar un Cobrador.");
            $this->redirect($this->createUrl("cobradores/index"));
        }
    }

}
?>
