
<?php
class SeguimientoController extends Controller{
    public function actionIndex(){

        FB::INFO($_POST,'____________________POST');
        $SeguimientoData = array();
        
        $WhereString = "1=1 ";// Se agregaron para inicializar las variables y que muestre toda la informacion de wrk_bitacora_seguimiento
        
        $WhereParams=array(); // Se agregaron para inicializar las variables y que muestre toda la informacion de wrk_bitacora_seguimiento
        
        if(!empty($_POST)){

            //$WhereString = "1=1";

            if (!empty($_POST['folio'])) {
                $WhereString .= " AND wrk_bitacora_seguimiento.folio = :folio";
                $WhereParams[':folio'] = $_POST['folio'];
            }
            if (!empty($_POST['usuario'])) {
                $WhereString .= " AND wrk_bitacora_seguimiento.usuario = :usuario";
                $WhereParams[':usuario'] = $_POST['usuario'];
            }
            if (!empty($_POST['Ffecha_registro'])) {
                $WhereString .= " AND wrk_bitacora_seguimiento.fecha_registro = :fecha_registro ";
                $WhereParams[':fecha_registro'] = $_POST['Ffecha_registro'];
            }
            if (!empty($_POST['Ffecha_alerta'])) {
                $WhereString .= " AND wrk_bitacora_seguimiento.fecha_alerta = :fecha_alerta ";
                $WhereParams[':fecha_alerta'] = $_POST['Ffecha_alerta'];
            }
        }

        $SeguimientoData = Yii::app()->db->createCommand()->select("
                wrk_bitacora_seguimiento.id,
                wrk_bitacora_seguimiento.folio,
                wrk_bitacora_seguimiento.fecha_registro,
                wrk_bitacora_seguimiento.fecha_alerta,
                wrk_bitacora_seguimiento.descripcion,
                wrk_bitacora_seguimiento.usuario
                ")
            ->from("wrk_bitacora_seguimiento")
            ->where($WhereString, $WhereParams)
            ->queryAll();

       $this->render("index", array("SeguimientoData" => $SeguimientoData));
    }

public function actionCreate(){

        if(!empty($_POST['folio'])){
            $Seguimiento="insert into wrk_bitacora_seguimiento
            (folio,fecha_registro, fecha_alerta, descripcion, usuario)
            values(:folio,:fecha_registro, :fecha_alerta, :descripcion, :usuario)";

            $parameters=array(
                ':folio'=>$_POST['folio'],
                ':fecha_registro'=>$_POST['fecha_registro'],
                ':fecha_alerta'=>$_POST['fecha_alerta'],
                ':descripcion'=>$_POST['descripcion'],
                ':usuario'=>$_SESSION['UserID']
                );

            if(Yii::app()->db->createCommand($Seguimiento)->execute($parameters)){

                Yii::app()->user->setFlash("success", "La información se ha registrado correctamente");
                $this->redirect($this->createUrl("seguimiento/index"));
            }else{
                Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
                $this->redirect($this->createUrl("seguimiento/index"));
            }
        }else{

            Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
            $this->redirect($this->createUrl("seguimiento/index"));

        }
    }

    public function actionLoadForm(){

        if(!empty($_POST['GetData'])){
            fb::info($_POST);
            $where="id ='".$_POST['GetData']['id']."'";
            $LoadForm=Yii::app()->db->createCommand()
            ->select('*')
            ->from('wrk_bitacora_seguimiento')
            ->where($where)
            ->queryAll();
            fb::info($LoadForm);
            if(!empty($LoadForm)){
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "Seleccionando " . $LoadForm['id'] . "...",
                    'GetData' => $LoadForm[0]
                    ));
                fb::info($_POST);
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "La información no se pudo seleccionar, intente de nuevo..."
                    ));
            }
            return;
        }

    }

    public function actionUpdate() {
        global $db;
        if (!empty($_POST['Update'])) {
            $SQLUpdate = "UPDATE wrk_bitacora_seguimiento SET
                folio='" . $_POST['Update']['folio'] . "',
                fecha_registro='" . $_POST['Update']['fecha_registro'] . "',
                fecha_alerta='".$_POST['Update']['fecha_alerta']."',
                descripcion='".$_POST['Update']['descripcion']."',
                usuario='".$_POST['Update']['usuario']."'             
                WHERE id = '" . $_POST['Update']['id'] . "'
                AND fecha_alerta <='" .$_POST['Update']['fecha_alerta']. "'" ;

                if (DB_query($SQLUpdate, $db)) {
                
                $NewRow .= "";
                $NewRow .= "
                <td >{$_POST['Update']['id']}</td>
                <td >{$_POST['Update']['folio']}</td>
                <td >{$_POST['Update']['fecha_registro']}</td>
                <td >{$_POST['Update']['fecha_alerta']}</td>
                <td >{$_POST['Update']['descripcion']}</td>
                <td >{$_POST['Update']['usuario']}</td>
                <td >
                    <a title=\"Editar Seguimiento\" onclick=\"EditarSeguimiento('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
                    <a href='" . $this->createUrl("seguimiento/disable&id=" . $_POST['Update']['id']) . "' title=\"Eliminar Seguimiento\" onclick=\"javascript:if(confirm('¿Esta seguro de eliminar este registro?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
                </td>";
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "Los datos se han actualizado correctamente...",
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
    public function actionDisable(){

        if(!empty($_GET['id'])){

            $Disable="delete from  wrk_bitacora_seguimiento where id=:id";
            $parameters=array(':id'=>$_GET['id']);

            if(Yii::app()->db->createCommand($Disable)->execute($parameters)){

                Yii::app()->user->setFlash("success", "La información se ha eliminado correctamente");
                $this->redirect($this->createUrl("seguimiento/index"));
            }else{
                Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
                $this->redirect($this->createUrl("seguimiento/index"));
            }
        }else{

            Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
            $this->redirect($this->createUrl("seguimiento/index"));

        }

    }

}
?>
