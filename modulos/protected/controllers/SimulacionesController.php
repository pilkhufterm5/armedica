<?php
class SimulacionesController extends Controller{
        
        public function actionIndex(){
                FB::INFO($_POST,'_____________________-POST');
 
        if (!empty($_POST['INICIO']) && !empty($_POST['FIN'])) {
                $FECHA .= " AND (date(titular.fecha_ingreso) >= date('".$_POST['INICIO']."') 
                            AND date(titular.fecha_ingreso) <= date('".$_POST['FIN']."')) ";                
            }else{
                $fecha = date('Y-m-d');
                                $nuevafecha = strtotime ('-1 year', strtotime($fecha));
                                $POSTFIN = date('Y-m-d', $nuevafecha );
                                $FECHA = "";
                                $FECHA .= " AND titular.fecha_ingreso >= '1990-01-01' 
                                                AND titular.fecha_ingreso <= '$POSTFIN'";
            }

                if(isset($_POST['BUSCAR'])){
                        if(!empty($_POST['Folio'])){
                                $Folio .= " AND titular.folio = '{$_POST['Folio']}' ";
                        }
                        if(!empty($_POST['FRECUENCIA_PAGO'])){
                                $FRECUENCIA_PAGO .= " AND cobranza.frecuencia_pago = '{$_POST['FRECUENCIA_PAGO']}' ";
                        }
                        if(!empty($_POST['PLAN'])){
                                $PLAN .= " AND cobranza.stockid = '{$_POST['PLAN']}' ";
                        }
                        if(!empty($_POST['FORMA_PAGO'])){
                                $FORMA_PAGO .= " AND cobranza.paymentid = '{$_POST['FORMA_PAGO']}' ";
                        }
            
                }

                $AumentosprecioData = Yii::app()->db->createCommand("select
                (titular.folio) AS Folio,
                (titular.debtorno) AS DebtorNo,
                CONCAT(titular.name,' ',titular.apellidos) AS NOMBRE,
                (emp.empresa) AS Empresa,
                (titular.movimientos_afiliacion) AS ESTATUS_TITULAR,
                count(custbranch.branchcode) AS NumSocios,
                (titular.fecha_ingreso) AS FECHA_INSCIPCION,
                (stkm.description) AS PLAN,
                (pm.paymentname) AS FORMA_PAGO,
                (fp.frecuencia) AS FRECUENCIA_PAGO,
                (titular.servicios_mes) AS ServiciosMes,
                (titular.servicios_acumulados) AS ServiciosAcum,
                (titular.fecha_ultaum) AS FECHA_ULTIMO_AUMENTO,titular.costo_total,
                (wrk.id) AS id,
                (wrk.folio) AS folio,
                wrk.prc_aumento_tarifa,
                (wrk.fecha_aumento_tarifa) AS FechaAumento,
                wrk.nueva_tarifa,
                round(wrk.prc_aumento_tarifa*titular.costo_total/100+titular.costo_total) as CostoNuevo,
                (wrk.nueva_tarifa) AS tarifa_r,
                (wrk.nueva_tarifa_redondeada) AS tarifa_redondeada,
                (wrk.usuario) AS Usuario
                from rh_titular titular
                left join wrk_simulacion_aumentosprecio wrk on wrk.folio = titular.folio and wrk.id=(select max(id) from
                wrk_simulacion_aumentosprecio wrkmax where wrkmax.folio=wrk.folio)
                left join rh_cobranza cobranza on cobranza.folio = titular.folio
                left join custbranch on custbranch.folio = titular.folio
                AND custbranch.movimientos_socios<>'Titular' AND movimientos_socios='Activo'
                left join stockmaster stkm on stkm.stockid = cobranza.stockid
                left join paymentmethods pm on cobranza.paymentid = pm.paymentid
                left join rh_frecuenciapago fp on cobranza.frecuencia_pago = fp.id
                left join rh_empresas emp on cobranza.empresa = emp.id
                where titular.movimientos_afiliacion='Activo'
                AND titular.folio<>' '
                AND titular.folio<>0
                AND stkm.is_cortesia = 0 
                AND titular.fecha_ultaum<=CURDATE()
                AND titular.fecha_ultaum <= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
                {$Folio}
                {$FRECUENCIA_PAGO}
                {$PLAN}
                {$FORMA_PAGO}
                {$FECHA}        
                group by titular.folio
                ")
                ->queryAll();
                $ListaEmpresas = CHtml::listData(Empresa::model()->findAll(), 'id', 'empresa');
                $ListaFormasPago = CHtml::listData(Paymentmethod::model()->findAll(), 'paymentid', 'paymentname');
                $ListaFrecuenciaPago = CHtml::listData(FrecuenciaPago::model()->findAll(), 'id', 'frecuencia');
                $_ListaPlanes = Yii::app()->db->createCommand()->select(' stockid, description ')->from('stockmaster')->where('categoryid = "AFIL" ORDER BY stockid ASC')->queryAll();
                $ListaPlanes = array();
                
                foreach ($_ListaPlanes as $Planes) {
                        $ListaPlanes[$Planes['stockid']] = $Planes['description'];
                }
                
                $this->render("index", array(
                "AumentosprecioData" => $AumentosprecioData,
                "ListaEmpresas" => $ListaEmpresas,
                "ListaFormasPago" => $ListaFormasPago,
                "ListaFrecuenciaPago" => $ListaFrecuenciaPago,
                "ListaPlanes" => $ListaPlanes
                ));
        }




        public function actionCreate(){
                $fecha_inicio = $_POST['INICIO'];
                $fecha_fin = $_POST['FIN'];
                $CheckRedondear = $_POST['Redondeartxt'];

                $GetAfil = Yii::app()->db->createCommand()->select ('
                titular.folio,
                titular.name,
                titular.apellidos,
                titular.name2,
                titular.movimientos_afiliacion,
                titular.fecha_ingreso,
                cobranza.stockid,
                cobranza.paymentid,
                cobranza.frecuencia_pago,
                titular.servicios_mes,
                titular.servicios_acumulados,
                titular.fecha_ultaum,
                titular.costo_total ')
                ->from('rh_titular titular')
                ->leftjoin('rh_cobranza cobranza','cobranza.folio = titular.folio')
                ->leftjoin('stockmaster stkm', 'stkm.stockid = cobranza.stockid')
                ->where('titular.movimientos_afiliacion="Activo"
                AND titular.folio<>" "
                AND titular.folio<>0
                AND stkm.is_cortesia = 0
                AND titular.fecha_ultaum<=CURDATE()
                AND titular.fecha_ultaum <= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) 
                AND titular.fecha_ingreso >= "'.$fecha_inicio.'"
                AND titular.fecha_ingreso <= "'.$fecha_fin.'" ')
                ->queryAll();

                $prc_aumento_tarifa = $_POST['prc_aumento_tarifa'];
                $fecha_aumento_tarifa = $_POST['fecha_aumento_tarifa'];
                $actualizar_tarifa = $_POST['actualizar_tarifa'];
                
                foreach($GetAfil as $rows)
                {
                        $Folio = $rows['folio'];
                        $Nombre = $rows['name'];
                        $Apellidos = $rows['apellidos'];
                        $Estatus = $rows['movimientos_afiliacion'];
                        $Empresa = $rows['name2'];
                        $FechaIngreso = $rows['fecha_ingreso'];
                        $CostoActual = $rows['costo_total'];
                        $FechaUltaum = $rows['fecha_ultaum'];
                        $ServiciosAcum=$rows['servicios_acumulados'];
                        $ServiciosMes=$rows['servicios_mes'];
                        $Plan=$rows['stockid'];
                        $FormaPago=$rows['paymentid'];
                        $FrecuenciaPago=$rows['frecuencia_pago'];
                        $CostoNuevo = round($prc_aumento_tarifa*$rows['costo_total']/100+$rows['costo_total']);

                        $cifra = $CostoNuevo;
                        $tamaño_cifra = strlen($cifra);

                        switch ($tamaño_cifra) {
                        case '7':
                                $unidades                       = substr($cifra, -1,1);
                                $decenas                        = substr($cifra, -2,1);
                                $centenas                       = substr($cifra, -3,1);
                        $unidad_de_millar               = substr($cifra, -4,1);
                        $decena_de_millar               = substr($cifra, -5,1);
                        $centena_de_millar              = substr($cifra, -6,1);
                        $unidad_de_millon               = substr($cifra, -7,1);
                        break;

                        case '6':
                        $unidades                       = substr($cifra, -1,1);
                        $decenas                        = substr($cifra, -2,1);
                        $centenas                       = substr($cifra, -3,1);
                        $unidad_de_millar               = substr($cifra, -4,1);
                        $decena_de_millar               = substr($cifra, -5,1);
                        $centena_de_millar              = substr($cifra, -6,1);
                        $unidad_de_millon               = 0;
                        break;

                        case '5':
                        $unidades                       = substr($cifra,-1,1);
                        $decenas                        = substr($cifra,-2,1);
                        $centenas                       = substr($cifra, -3,1);
                        $unidad_de_millar               = substr($cifra, -4,1);
                        $decena_de_millar               = substr($cifra, -5,1);
                        $centena_de_millar              = 0;
                        $unidad_de_millon               = 0;
                        break;

                        case '4':
                        $unidades                       = substr($cifra,-1,1);
                        $decenas                        = substr($cifra,-2,1);
                        $centenas                       = substr($cifra, -3,1);
                        $unidad_de_millar               = substr($cifra, -4,1);
                        $decena_de_millar               = 0;
                        $centena_de_millar              = 0;
                        $unidad_de_millon               = 0;
                        break;

                        case '3':
                        $unidades                       = substr($cifra,-1,1);
                        $decenas                        = substr($cifra,-2,1);
                        $centenas                       = substr($cifra, -3,1);
                        $unidad_de_millar               = 0;
                        $decena_de_millar               = 0;
                        $centena_de_millar              = 0;
                        $unidad_de_millon               = 0;
                        break;

                        case '2':
                        $unidades                       = substr($cifra,-1,1);
                        $decenas                        = substr($cifra,-2,1);
                        $centenas                       = 0;
                        $unidad_de_millar               = 0;
                        $decena_de_millar               = 0;
                        $centena_de_millar              = 0;
                        $unidad_de_millon               = 0;
                        break;

                        case '1':
                        $unidades                       = substr($cifra,-1,1);
                        $decenas                        = 0;
                        $centenas                       = 0;
                        $unidad_de_millar               = 0;
                        $decena_de_millar               = 0;
                        $centena_de_millar              = 0;
                        $unidad_de_millon               = 0;
                        break;  
                        }
//Validar el aumento de las cifras al redondear numeros que son 9
                        if ($unidades <= 5) {
                                $unidades = 0;
                        }elseif ($unidades > 5) {
                        if ($decenas < 9) {
                                $unidades       = 0;
                                $decenas        ++;
                        }elseif ($centenas < 9) {
                                $unidades       = 0;
                                $decenas        = 0;
                                $centenas       ++;
                        }elseif ($unidad_de_millar < 9) {
                                $unidades       = 0;
                                $decenas        = 0;
                                $centenas       = 0;
                                $unidad_de_millar ++;
                        }elseif ($decena_de_millar < 9) {
                                $unidades       = 0;
                                $decenas        = 0;
                                $centenas       = 0;
                                $unidad_de_millar = 0;
                                $decena_de_millar ++;
                        }elseif ($centena_de_millar < 9) {
                                $unidades       = 0;
                                $decenas        = 0;
                                $centenas       = 0;
                                $unidad_de_millar = 0;
                                $decena_de_millar = 0;
                                $centena_de_millar ++;
                        }elseif ($unidad_de_millon < 9) {
                                $unidades       = 0;
                                $decenas        = 0;
                                $centenas       = 0;
                                $unidad_de_millar       = 0;
                                $decena_de_millar       = 0;
                                $centena_de_millar      = 0;
                                $unidad_de_millon       ++;
                        }
                        }

                        if ($unidad_de_millon != 0) {
                                $_numero_redondeado = $unidad_de_millon.$centena_de_millar.$decena_de_millar.$unidad_de_millar.$centenas.$decenas.$unidades;
                        }elseif ($centena_de_millar != 0) {
                                $_numero_redondeado = $centena_de_millar.$decena_de_millar.$unidad_de_millar.$centenas.$decenas.$unidades;
                        }elseif ($decena_de_millar != 0) {
                                $_numero_redondeado = $decena_de_millar.$unidad_de_millar.$centenas.$decenas.$unidades;
                        }elseif ($unidad_de_millar != 0) {
                                $_numero_redondeado = $unidad_de_millar.$centenas.$decenas.$unidades;
                        }elseif ($centenas != 0) {
                                $_numero_redondeado = $centenas.$decenas.$unidades;
                        }elseif ($decenas != 0) {
                                $_numero_redondeado = $decenas.$unidades;
                        }else{
                                $_numero_redondeado = $unidades;
                        }
               
                        /*$redondeo = substr($CostoNuevo,-1,1);
                        $redondeodecimal = substr($CostoNuevo,-2,1);
                        $numero_entero = substr($CostoNuevo, 0,-2);             

                        if ($redondeo <= 5) {
                                        $redondeo = 0;
                                }else{
                                        $redondeo = 0;
                                        $redondeodecimal ++;
                                }
                        $_numero_redondeado = $numero_entero.$redondeodecimal.$redondeo;
                        */
        
                        $ult_fech_aum = date("Y-m-d");
                        //echo "<pre>";print_r($_numero_redondeado);exit();
//Termia redondeoss de numeros enteros
                        if ($CheckRedondear == "NoAplica") {
                                $_numero_redondeado = $CostoNuevo;
                        }
                        
                        $Simulacion="insert into wrk_simulacion_aumentosprecio
                        (folio,nombre,apellidos,name2,fecha_ingreso, movimientos_afiliacion,servicios_mes,servicios_acumulados,stockid,paymentid,frecuencia_pago,fecha_ultimo_aumento,costo_actual, prc_aumento_tarifa, fecha_aumento_tarifa,nueva_tarifa,usuario,nueva_tarifa_redondeada)
                        values(:folio,:nombre,:apellidos,:name2,:fecha_ingreso,:movimientos_afiliacion,:servicios_mes,
                        :servicios_acumulados,:stockid,:paymentid,:frecuencia_pago,:fecha_ultimo_aumento,:costo_actual,
                        :prc_aumento_tarifa,:fecha_aumento_tarifa,:nueva_tarifa,:usuario,:nueva_tarifa_redondeada)";

                        $parameters=array(
                        ':folio'=>$Folio,
                        ':nombre'=>$Nombre,
                        ':apellidos'=>$Apellidos,
                        ':name2'=>$Empresa,
                        ':fecha_ingreso'=>$FechaIngreso,
                        ':movimientos_afiliacion'=>$Estatus,
                        ':servicios_mes'=>$ServiciosMes,
                        ':servicios_acumulados'=>$ServiciosAcum,
                        ':stockid'=>$Plan,
                        ':paymentid'=>$FormaPago,
                        ':frecuencia_pago'=>$FrecuenciaPago,
                        ':costo_actual'=>$CostoActual,
                        ':fecha_ultimo_aumento'=>$FechaUltaum,
                        ':prc_aumento_tarifa'=>$prc_aumento_tarifa,
                        ':fecha_aumento_tarifa'=>$fecha_aumento_tarifa,
                        ':nueva_tarifa'=>$CostoNuevo,
                        ':nueva_tarifa_redondeada'=>$_numero_redondeado,
                        ':usuario'=>$_SESSION['UserID']
                        );
                        Yii::app()->db->createCommand($Simulacion)->execute($parameters);       
                }

                Yii::app()->user->setFlash("success", "La información se ha registrado correctamente");

                //$this->redirect(array('index', 'fecha_inicio'=>$fecha_inicio));

                $this->redirect($this->createUrl("simulaciones/index"));
        }



        public function actionLoadForm(){

                if(!empty($_POST['GetData'])){
                        fb::info($_POST);
                        $where="id ='".$_POST['GetData']['id']."'";
                        $LoadForm=Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('wrk_simulacion_aumentosprecio')
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
                                'message' => "EL SOCIO AÚN NO CUENTA SIMULACION DE AUMENTO DE
                                PRECIO"
                                ));
                        }
                        return;
                }
        }




        public function actionUpdate() {
                global $db;
                $GetAfilUpdate = Yii::app()->db->createCommand()->select ('
                wrk.id,
                wrk.nueva_tarifa_redondeada,
                titular.folio,
                titular.name,
                titular.apellidos,
                titular.name2,titular.movimientos_afiliacion,
                titular.fecha_ingreso,
                cobranza.stockid,
                cobranza.paymentid,
                cobranza.frecuencia_pago,
                titular.servicios_mes,
                titular.servicios_acumulados,
                titular.fecha_ultaum,
                titular.costo_total ')
                ->from('rh_titular titular')
                ->leftjoin('wrk_simulacion_aumentosprecio wrk',' wrk.folio = titular.folio and wrk.id=(select max(id)
                from wrk_simulacion_aumentosprecio wrkmax where wrkmax.folio=wrk.folio)')
                ->leftjoin('rh_cobranza cobranza','cobranza.folio = titular.folio')
                ->where('titular.movimientos_afiliacion="Activo"
                AND titular.folio<>" "
                AND titular.folio<>0
                AND titular.fecha_ultaum<=CURDATE()
                AND titular.fecha_ultaum <= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
                AND titular.folio =' . $_POST['Update']['folio']. ' ')
                ->queryAll();
                
                $CostoNuevo = round($_POST['Update']['prc_aumento_tarifa']*$GetAfilUpdate[0]['costo_total']/100+$GetAfilUpdate[0]['costo_total']);
                $cifra = $CostoNuevo;
                        $tamaño_cifra = strlen($cifra);

                        switch ($tamaño_cifra) {
                        case '7':
                                $unidades                       = substr($cifra, -1,1);
                                $decenas                        = substr($cifra, -2,1);
                                $centenas                       = substr($cifra, -3,1);
                        $unidad_de_millar               = substr($cifra, -4,1);
                        $decena_de_millar               = substr($cifra, -5,1);
                        $centena_de_millar              = substr($cifra, -6,1);
                        $unidad_de_millon               = substr($cifra, -7,1);
                        break;

                        case '6':
                        $unidades                       = substr($cifra, -1,1);
                        $decenas                        = substr($cifra, -2,1);
                        $centenas                       = substr($cifra, -3,1);
                        $unidad_de_millar               = substr($cifra, -4,1);
                        $decena_de_millar               = substr($cifra, -5,1);
                        $centena_de_millar              = substr($cifra, -6,1);
                        $unidad_de_millon               = 0;
                        break;

                        case '5':
                        $unidades                       = substr($cifra,-1,1);
                        $decenas                        = substr($cifra,-2,1);
                        $centenas                       = substr($cifra, -3,1);
                        $unidad_de_millar               = substr($cifra, -4,1);
                        $decena_de_millar               = substr($cifra, -5,1);
                        $centena_de_millar              = 0;
                        $unidad_de_millon               = 0;
                        break;

                        case '4':
                        $unidades                       = substr($cifra,-1,1);
                        $decenas                        = substr($cifra,-2,1);
                        $centenas                       = substr($cifra, -3,1);
                        $unidad_de_millar               = substr($cifra, -4,1);
                        $decena_de_millar               = 0;
                        $centena_de_millar              = 0;
                        $unidad_de_millon               = 0;
                        break;

                        case '3':
                        $unidades                       = substr($cifra,-1,1);
                        $decenas                        = substr($cifra,-2,1);
                        $centenas                       = substr($cifra, -3,1);
                        $unidad_de_millar               = 0;
                        $decena_de_millar               = 0;
                        $centena_de_millar              = 0;
                        $unidad_de_millon               = 0;
                        break;

                        case '2':
                        $unidades                       = substr($cifra,-1,1);
                        $decenas                        = substr($cifra,-2,1);
                        $centenas                       = 0;
                        $unidad_de_millar               = 0;
                        $decena_de_millar               = 0;
                        $centena_de_millar              = 0;
                        $unidad_de_millon               = 0;
                        break;

                        case '1':
                        $unidades                       = substr($cifra,-1,1);
                        $decenas                        = 0;
                        $centenas                       = 0;
                        $unidad_de_millar               = 0;
                        $decena_de_millar               = 0;
                        $centena_de_millar              = 0;
                        $unidad_de_millon               = 0;
                        break;  
                        }
//Validar el aumento de las cifras al redondear numeros que son 9
                        if ($unidades <= 5) {
                                $unidades = 0;
                        }elseif ($unidades > 5) {
                        if ($decenas < 9) {
                                $unidades       = 0;
                                $decenas        ++;
                        }elseif ($centenas < 9) {
                                $unidades       = 0;
                                $decenas        = 0;
                                $centenas       ++;
                        }elseif ($unidad_de_millar < 9) {
                                $unidades       = 0;
                                $decenas        = 0;
                                $centenas       = 0;
                                $unidad_de_millar ++;
                        }elseif ($decena_de_millar < 9) {
                                $unidades       = 0;
                                $decenas        = 0;
                                $centenas       = 0;
                                $unidad_de_millar = 0;
                                $decena_de_millar ++;
                        }elseif ($centena_de_millar < 9) {
                                $unidades       = 0;
                                $decenas        = 0;
                                $centenas       = 0;
                                $unidad_de_millar = 0;
                                $decena_de_millar = 0;
                                $centena_de_millar ++;
                        }elseif ($unidad_de_millon < 9) {
                                $unidades       = 0;
                                $decenas        = 0;
                                $centenas       = 0;
                                $unidad_de_millar       = 0;
                                $decena_de_millar       = 0;
                                $centena_de_millar      = 0;
                                $unidad_de_millon       ++;
                        }
                        }

                        if ($unidad_de_millon != 0) {
                                $_numero_redondeado = $unidad_de_millon.$centena_de_millar.$decena_de_millar.$unidad_de_millar.$centenas.$decenas.$unidades;
                        }elseif ($centena_de_millar != 0) {
                                $_numero_redondeado = $centena_de_millar.$decena_de_millar.$unidad_de_millar.$centenas.$decenas.$unidades;
                        }elseif ($decena_de_millar != 0) {
                                $_numero_redondeado = $decena_de_millar.$unidad_de_millar.$centenas.$decenas.$unidades;
                        }elseif ($unidad_de_millar != 0) {
                                $_numero_redondeado = $unidad_de_millar.$centenas.$decenas.$unidades;
                        }elseif ($centenas != 0) {
                                $_numero_redondeado = $centenas.$decenas.$unidades;
                        }elseif ($decenas != 0) {
                                $_numero_redondeado = $decenas.$unidades;
                        }else{
                                $_numero_redondeado = $unidades;
                        }


                        if (!empty($_POST['Update']['nueva_tarifa_editada'])) {
                                $_tarifa_tarifa = $_POST['Update']['nueva_tarifa_editada'];
                        }elseif ($_POST['Update']['aplica_redondeo'] == "NoAplica") {
                        	$_tarifa_tarifa = $CostoNuevo;
                        }else{                          
                                $_tarifa_tarifa = $_numero_redondeado;
                        }

                        //echo "<pre>";print_r($_POST['Update']['nueva_tarifa_editada'];)exit();

                if(!empty($_POST['Update'])){

                                

                        $SQLUpdate = "UPDATE wrk_simulacion_aumentosprecio SET folio = :folio,
                        prc_aumento_tarifa = :prc_aumento_tarifa,
                        fecha_aumento_tarifa = :fecha_aumento_tarifa,
                        nueva_tarifa_redondeada = :nueva_tarifa,
                        usuario = :usuario
                        WHERE id = :id ";

                        $parameters = array(
                        ':id' => $_POST['Update']['id'],
                        ':folio' => $_POST['Update']['folio'],
                        ':prc_aumento_tarifa' => $_POST['Update']['prc_aumento_tarifa'],
                        ':fecha_aumento_tarifa' => $_POST['Update']['fecha_aumento_tarifa'],
                        ':nueva_tarifa' => $_tarifa_tarifa,
                        ':usuario' => $_SESSION['UserID']
                        );
                        if (Yii::app()->db->createCommand($SQLUpdate)->execute($parameters)) {
                                echo CJSON::encode(array(
                                'requestresult' => 'ok',
                                'message' => 'Los datos se han actualizado correctamente...',
                                ));
                        } else {
                                echo CJSON::encode(array(
                                'requestresult' => 'fail',
                                'message' => "No se pudo actualizar, intente de nuevo",
                                ));
                        }
                }
                return;
        }
        public function actionActualizar() {
                if(!empty($_POST['Actualizar']['Folio'])){
                        
                    parse_str($_POST['Actualizar']['Folio'], $datos);
                    
                    foreach ($datos['ActualizarTarifa'] as $key => $folio) {
                        $GetActualizaTarifa = Yii::app()->db->createCommand()->select ('
                        titular.folio,
                        titular.name,
                        titular.apellidos,
                        titular.name2,
                        titular.movimientos_afiliacion,
                        count(custbranch.branchcode) AS NumSocios,
                        titular.fecha_ingreso,
                        cobranza.stockid,
                        cobranza.paymentid,
                        cobranza.frecuencia_pago,
                        titular.servicios_mes,
                        titular.servicios_acumulados,
                        titular.fecha_ultaum,
                        titular.costo_total,
                        wrk.id,
                        wrk.prc_aumento_tarifa,
                        wrk.fecha_aumento_tarifa,
                        wrk.nueva_tarifa,
                        wrk.nueva_tarifa_redondeada,
                        wrk.prc_aumento_tarifa*titular.costo_total/100+titular.costo_total as CostoNuevo,
                        wrk.usuario
                        ')
                        ->from('rh_titular titular')
                        ->leftjoin('wrk_simulacion_aumentosprecio wrk',' wrk.folio = titular.folio and wrk.id=(select max(id)
                        from wrk_simulacion_aumentosprecio wrkmax where wrkmax.folio=wrk.folio)')
                        ->leftjoin('rh_cobranza cobranza','cobranza.folio = titular.folio')
                        ->leftjoin('custbranch','custbranch.folio = titular.folio and custbranch.movimientos_socios<>"Titular" ')
                        ->where('titular.movimientos_afiliacion="Activo"
                        AND titular.folio<>" "
                        AND titular.folio<>0
                        AND titular.fecha_ultaum<=CURDATE()
                        AND titular.fecha_ultaum <= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
                        AND titular.folio =' . $folio. '
                        ')
                        ->group('titular.folio')
                        ->queryAll();

                
                $NuevaTarifa = $GetActualizaTarifa[0]['nueva_tarifa_redondeada'];
                $NuevaFecha = $GetActualizaTarifa[0]['fecha_aumento_tarifa'];
                $ActualizarTarifa = "update rh_titular set costo_total = :costo_total, fecha_ultaum = :fecha_ultaum where
                folio = :folio";

                $Parametros_tarifa = array(
                ':costo_total'=>$NuevaTarifa,
                ':fecha_ultaum'=>$NuevaFecha,
                ':folio'=>$folio,
                );
                                
                Yii::app()->db->createCommand($ActualizarTarifa)->execute($Parametros_tarifa);
                $ActualizarStatus = "update wrk_simulacion_aumentosprecio set status = :status where id = :id";
                $Parametros_status = array(
                ':status'=>'1',
                ':id'=>$GetActualizaTarifa[0]['id']
                );
                Yii::app()->db->createCommand($ActualizarStatus)->execute($Parametros_status);
            }
                    echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "Actualizados Correctamente... "
                    ));
                    exit;
                }
        }
}
?>