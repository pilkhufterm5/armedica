
<?php
class IncidenciasController extends Controller{

    // Se agrego funcion para validar que el socio no tenga incidencia registrada

 public function actionSearchfolio($callback = null)
    {
       
        if(!empty($_POST['Search']['string'])){
         // Obtenemos la palabra clave
         $keyword = $_POST['Search']['string'];

         // Buscamos coincidencias
         
$ListaFolios = Yii::app()->db
->createCommand()
->select("rh_titular.folio, rh_titular.name, rh_titular.apellidos")
            ->from("rh_titular")
            ->where("folio = :keyword ",
            array(":keyword" =>"$keyword"))->queryAll();

            

            foreach ($ListaFolios as $Data) {
                $_Data[] = array(
            'value' => $Data['folio']. '-' . $Data['name']. ' ' . $Data['apellidos'],
            'folio'=> $Data['folio']
                );
                 
            }

        }

        if(empty($_Data)){
            $_Data[] = array(
                'value' => 'ESTE FOLIO NO SE ENCUENTRA EN AFILIACIONES.',
                'folio'=> '',
            );
        }

        echo CJSON::encode(array(
            'requestresult' => 'ok',
            'DataList' => $_Data
            ));
        return;

        exit;
    }

// Termina

    public function actionIndex(){

        FB::INFO($_POST,'_____________________-POST'); 
        $GetData = array();
        
        $WhereString = " 1 = 1 ";
        $WhereParams=array();

        if(isset($_POST['BUSCAR'])){

        if (!empty($_POST['Folio'])) {
                $WhereString .= " AND wrk.folio_socio = :folio_socio";
                $WhereParams[':folio_socio'] = $_POST['Folio'];
            }

        if (!empty($_POST['INICIO']) && !empty($_POST['FIN'])) {
                $WhereString .= " AND (date(wrk.fecha_incidencia) >= date(:inicio) AND date(wrk.fecha_incidencia) <= date(:fin)) ";
                $WhereParams[':inicio'] = $_POST['INICIO'];
                $WhereParams[':fin'] = $_POST['FIN'];
            }

            if (!empty($_POST['Producto'])) {
                $WhereString .= " AND cobranza.stockid = :stockid";
                $WhereParams[':stockid'] = $_POST['Producto'];
            }

            if (!empty($_POST['FormaPago'])) {
                $WhereString .= " AND cobranza.paymentid = :paymentid";
                $WhereParams[':paymentid'] = $_POST['FormaPago'];
            }

            if (!empty($_POST['FrecuenciaPago'])) {
                $WhereString .= " AND cobranza.frecuencia_pago = :frecuencia_pago";
                $WhereParams[':frecuencia_pago'] = $_POST['FrecuenciaPago'];
            }

            if (!empty($_POST['Cobrador'])) {
                $WhereString .= " AND cobranza.cobrador = :cobrador";
                $WhereParams[':cobrador'] = $_POST['Cobrador'];
            }
            // Se agrego para filtrar por Asesor Angeles Perez 15/08/2016
            if (!empty($_POST['Asesor'])) {
                $WhereString .= " AND titular.asesor = :asesor";
                $WhereParams[':asesor'] = $_POST['Asesor'];
            }
            // Termina
             if (!empty($_POST['Empresa'])) {
                $WhereString .= " AND titular.name2 = :name2";
                $WhereParams[':name2'] = $_POST['Empresa'];
            }

            if (!empty($_POST['Cierre'])) {
                $WhereString .= " AND wrk.cierre = :cierre";
                $WhereParams[':cierre'] = $_POST['Cierre'];
            }

        

}

        FB::INFO($WhereString,'____________WHERE');
        FB::INFO($WhereParams,'____________WHEREPARAMS');

        $IncidenciasservicioData = Yii::app()->db->createCommand()->select("
            (titular.folio) AS Folio,
            CONCAT(titular.name,' ',titular.apellidos) AS Nombre,
            (titular.name2) as Empresa,
            (titular.movimientos_afiliacion) AS EstatusTitular,
            (comisionistas.comisionista) as Asesor,
            (cobrador.nombre) as Cobrador,
            (stkm.description) AS Producto,
            (pm.paymentname) AS FormaPago,
            (fp.frecuencia) AS FrecuenciaPago,
            (wrk.id) as id,
            (wrk.fecha_incidencia) as FechaIncidencia,
            (wrk.folio_socio) as FolioSocio,
            (wrkasignacion.nombre) as Asignado,
            (wrk.descripcion_incidencia) as DescripcionIncidencia,
            (wrk.solucion_incidencia) as SolucionIncidencia,
            (wrk.cierre) as Cierre,
            (wrk.fecha_cierre) as FechaCierre,
            (wrk.status) as Status,
            (wrk.fecha_cancelacion) as FechaCancelacion,
            (wrk.usuario) as Usuario,
            (wrk.motivo_incidencia) as motivoincidencia,
            (SELECT COUNT(*) FROM wrk_incidencias_servicio) as IncidenciasRegistradas
            ")
        ->from("rh_titular titular")
        ->leftjoin("rh_foliosasignados fasig", "fasig.folio = titular.folio")
        ->leftjoin("rh_cobranza cobranza", "cobranza.folio = titular.folio")
        ->leftjoin("debtorsmaster dm", "dm.debtorno = titular.debtorno")
        ->leftjoin("stockmaster stkm", "stkm.stockid = cobranza.stockid")
        ->leftjoin("paymentmethods pm", "cobranza.paymentid = pm.paymentid")
        ->leftjoin("rh_frecuenciapago fp", "cobranza.frecuencia_pago = fp.id")
        ->leftjoin("rh_cobradores cobrador", "cobranza.cobrador = cobrador.id")
        ->leftjoin("rh_comisionistas comisionistas", "titular.asesor = comisionistas.id")// Se agrego para mostrar el Asesor Angeles Perez 15/08/2016
        ->leftjoin("wrk_incidencias_servicio wrk", "titular.folio = wrk.folio_socio")
        ->leftjoin("wrk_asignacion_personal wrkasignacion", "wrk.asignado = wrkasignacion.id")  
        ->where($WhereString." AND wrk.folio_socio <>' ' ", $WhereParams)     
        ->queryAll();


        $ListaMotivosIncidenciasServicio =MotivosIncidencias::model()->findAll();
        $ListaAsignacionPersonal = AsignacionPersonal::model()->findAll();
        $ListaCobradores = CHtml::listData(Cobradores::model()->findAll(), 'id', 'nombre');
        $ListaAsesores = CHtml::listData(Comisionista::model()->findAll('activo=1'), 'id', 'comisionista');// Se agrego para mostrar el Asesor Angeles Perez 15/08/2016
        $ListaFormasPago = CHtml::listData(Paymentmethod::model()->findAll(), 'paymentid', 'paymentname');
        $ListaFrecuenciaPago = CHtml::listData(FrecuenciaPago::model()->findAll(), 'id', 'frecuencia');
        $_ListaPlanes = Yii::app()->db->createCommand()->select(' stockid, description ')->from('stockmaster')->where('categoryid = "AFIL" ORDER BY stockid ASC')->queryAll();
        $ListaPlanes = array();
        foreach ($_ListaPlanes as $Planes) {
            $ListaPlanes[$Planes['stockid']] = $Planes['description'];
        }



        $this->render("index", array(
            "IncidenciasservicioData" => $IncidenciasservicioData,
            "ListaCobradores" => $ListaCobradores,
            "ListaAsesores" => $ListaAsesores,// Se agrego para mostrar el Asesor Angeles Perez 15/08/2016
            "ListaFormasPago" => $ListaFormasPago,
            "ListaFrecuenciaPago" => $ListaFrecuenciaPago,
            "ListaPlanes" => $ListaPlanes,
            "ListaMotivosIncidenciasServicio" => $ListaMotivosIncidenciasServicio,
            "ListaAsignacionPersonal" => $ListaAsignacionPersonal
        ));
    }

    public function actionCreate(){

        if(!empty($_POST['folio_socio'])){
            $Incidencias="insert into wrk_incidencias_servicio
            (fecha_incidencia,folio_socio,asignado,motivo_incidencia,descripcion_incidencia,
             solucion_incidencia,cierre,fecha_cierre,status,fecha_cancelacion,usuario)

            values(:fecha_incidencia,:folio_socio,:asignado,:motivo_incidencia,:descripcion_incidencia,
            :solucion_incidencia,:cierre,:fecha_cierre,:status,:fecha_cancelacion,:usuario)";
     
            $motivoinc=implode(",",$_POST['motivo_incidencia']);

            $parameters=array(
                ':fecha_incidencia'=>$_POST['fecha_incidencia'],
                ':folio_socio'=>$_POST['folio_socio'],
                ':asignado'=>$_POST['asignado'],
                ':motivo_incidencia'=>$motivoinc,
                ':descripcion_incidencia'=>$_POST['descripcion_incidencia'],
                ':solucion_incidencia'=>$_POST['solucion_incidencia'],
                ':cierre'=>$_POST['cierre'],
                ':fecha_cierre'=>$_POST['fecha_cierre'],
                ':status'=>$_POST['status'],
                ':fecha_cancelacion'=>$_POST['fecha_cancelacion'],
                ':usuario'=>$_SESSION['UserID']
                 );


            if(Yii::app()->db->createCommand($Incidencias)->execute($parameters)){

                Yii::app()->user->setFlash("success", "La información se ha registrado correctamente");
                $this->redirect($this->createUrl("incidencias/index"));
            }else{
                Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
                $this->redirect($this->createUrl("incidencias/index"));
            }
        }else{

            Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
            $this->redirect($this->createUrl("incidencias/index"));

        }
    }
    public function actionLoadForm(){

        if(!empty($_POST['GetData'])){
            fb::info($_POST);
            $where="id ='".$_POST['GetData']['id']."'";
            $LoadForm=Yii::app()->db->createCommand()
            ->select('*')
            ->from('wrk_incidencias_servicio')
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
                    'message' => "EL SOCIO NO TIENE INCIDENCIA REGISTRADA"
                    ));
            }
            return;
        }

    }

    public function actionUpdate() {
        global $db;
        if (!empty($_POST['Update'])) {
            $SQLUpdate = "UPDATE wrk_incidencias_servicio SET
                id='" . $_POST['Update']['id'] . "',
                folio_socio='" . $_POST['Update']['folio_socio'] . "',
                asignado='".$_POST['Update']['asignado']."',
                motivo_incidencia='" . $_POST['Update']['motivo_incidencia'] . "',
                descripcion_incidencia='" . $_POST['Update']['descripcion_incidencia'] . "',
                solucion_incidencia='" . $_POST['Update']['solucion_incidencia'] . "',
                cierre='".$_POST['Update']['cierre']."',
                fecha_cierre='".$_POST['Update']['fecha_cierre']."',
                status='".$_POST['Update']['status']."',
                fecha_cancelacion='".$_POST['Update']['fecha_cancelacion']."',
                usuario='".$_POST['Update']['usuario']."'              
                WHERE id = '" . $_POST['Update']['id'] . "'";

                if (DB_query($SQLUpdate, $db)) {
                
                if ($_POST['Update']['status'] == 1) $_POST['Update']['status'] = "Activo";
                else $_POST['Update']['status'] = "Cancelado";
                $NewRow .= "";
                $NewRow .= "
                <td >{$_POST['Update']['id']}</td>
                <td >{$_POST['Update']['folio_socio']}</td>
                <td >{$_POST['Update']['asignado']}</td>
                <td >{$_POST['Update']['motivo_incidencia']}</td>
                <td >{$_POST['Update']['descripcion_incidencia']}</td>
                <td >{$_POST['Update']['solucion_incidencia']}</td>
                <td >{$_POST['Update']['cierre']}</td>
                <td >{$_POST['Update']['fecha_cierre']}</td>
                <td >{$_POST['Update']['status']}</td>
                <td >{$_POST['Update']['fecha_cancelacion']}</td>
                <td >{$_POST['Update']['usuario']}</td>
                <td >
                    <a title=\"Editar Incidencias\" onclick=\"EditarIncidencias('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
                    <a href='" . $this->createUrl("incidencias/disable&id=" . $_POST['Update']['id']) . "' title=\"Cancelar Incidencia\" onclick=\"javascript:if(confirm('¿Esta seguro de cancelar este registro?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
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

            $Disable="update wrk_incidencias_servicio set status= :status, fecha_cancelacion= :fecha_cancelacion  where id=:id";
            $parameters=array(':status' => 0, ':fecha_cancelacion'=>date('Y-m-d'), ':id'=>$_GET['id']);

            if(Yii::app()->db->createCommand($Disable)->execute($parameters)){

                Yii::app()->user->setFlash("success", "La información se ha eliminado correctamente");
                $this->redirect($this->createUrl("incidencias/index"));
            }else{
                Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
                $this->redirect($this->createUrl("incidencias/index"));
            }
        }else{

            Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
            $this->redirect($this->createUrl("incidencias/index"));

        }

    }

}
?>

    