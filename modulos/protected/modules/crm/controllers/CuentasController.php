<?php
class CuentasController extends Controller{

    public $layout = 'webroot.themes.found.views.layouts.main';

    public function actionIndex(){
        $Cuentas=Yii::app()->db->createCommand()
        ->select('*')
        ->from('debtorsmaster')
        ->limit(100)
        ->queryAll();

        //$Cuentas=Yii::app()->db->createCommand()->select('*')->from('custbranch')->queryAll();

        $this->render('index', array(
            'Cuentas'=>$Cuentas
            ));
    }

    public function actionCreate() {
        global $db;
        if (!empty($_POST['crearCuenta'])) {

            if(empty($_POST['crearCuenta']['nombre'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, el nombre es requerido"
                ));
                return;
            }

            if(empty($_POST['crearCuenta']['apellidoPaterno'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, el apellido paterno es requerido"
                ));
                return;
            }

            if(empty($_POST['crearCuenta']['apellidoMaterno'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, el apellido materno es requerido"
                ));
                return;
            }

            if(empty($_POST['crearCuenta']['telefono'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, el teléfono es requerido"
                ));
                return;
            }

            if(empty($_POST['crearCuenta']['direccion1'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, la calle es requerida"
                ));
                return;
            }

            if(empty($_POST['crearCuenta']['direccion2'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, el número exterior"
                ));
                return;
            }

            if(empty($_POST['crearCuenta']['direccion3'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, el numero interior es requerido"
                ));
                return;
            }

            if(empty($_POST['crearCuenta']['direccion4'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, la colonia es requerida"
                ));
                return;
            }

            if(empty($_POST['crearCuenta']['direccion5'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, la localidad es requerida"
                ));
                return;
            }

            if(empty($_POST['crearCuenta']['direccion6'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, la referencia es requerida"
                ));
                return;
            }

            if(empty($_POST['crearCuenta']['direccion7'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, el municicpio es requerido"
                ));
                return;
            }

            if(empty($_POST['crearCuenta']['direccion8'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, el estado es requerido"
                ));
                return;
            }
            if(empty($_POST['crearCuenta']['direccion9'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, el país es requerido"
                ));
                return;
            }

            if(empty($_POST['crearCuenta']['direccion10'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, el código postal es requerido"
                ));
                return;
            }

            if(empty($_POST['crearCuenta']['taxref'])){
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error, los campos marcados con * son obligatorios"
                ));
                return;
            }


            $DebtorNo = GetNextTransNo(500, $db);
            $PaymentTerms = 30;
            $CreditLimit = $_SESSION['DefaultCreditLimit'];
            $SalesType = "L1";
            $NombreCompleto= $_POST['crearCuenta']['nombre']." ".$_POST['crearCuenta']['apellidoPaterno']." ".$_POST['crearCuenta']['apellidoMaterno'];
            FB::INFO($NombreCompleto);

            $CrearCuenta = "insert into debtorsmaster (
                debtorno,
                name,
                name2,
                address1,
                address2,
                address3,
                address4,
                address5,
                address6,
                address7,
                address8,
                address9,
                address10,
                rh_tel,
                currcode,
                clientsince,
                holdreason,
                paymentterms,
                discount,
                discountcode,
                pymtdiscount,
                creditlimit,
                salestype,
                invaddrbranch,
                taxref,
                customerpoline)
                VALUES (
                :debtorno,
                :name,
                :name2,
                :address1,
                :address2,
                :address3,
                :address4,
                :address5,
                :address6,
                :address7,
                :address8,
                :address9,
                :address10,
                :rh_tel,
                :currcode,
                :clientsince,
                :holdreason,
                :paymentterms,
                :discount,
                :discountcode,
                :pymtdiscount,
                :creditlimit,
                :salestype,
                :invaddrbranch,
                :taxref,
                :customerpoline
                )";

            $parameters = array(
                ':debtorno' => $DebtorNo,
                ':name' => $NombreCompleto,
                ':name2' => '',
                ':address1' => $_POST['crearCuenta']['direccion1'],
                ':address2' => $_POST['crearCuenta']['direccion2'],
                ':address3' => $_POST['crearCuenta']['direccion3'],
                ':address4' => $_POST['crearCuenta']['direccion4'],
                ':address5' => $_POST['crearCuenta']['direccion5'],
                ':address6' => $_POST['crearCuenta']['direccion6'],
                ':address7' => $_POST['crearCuenta']['direccion7'],
                ':address8' => $_POST['crearCuenta']['direccion8'],
                ':address9' => $_POST['crearCuenta']['direccion9'],
                ':address10' => $_POST['crearCuenta']['direccion10'],
                ':rh_tel' => $_POST['crearCuenta']['telefono'],
                ':currcode' => 'MXN',
                ':clientsince' => $_POST['crearCuenta']['fechaAlta'],
                ':holdreason' => '',
                ':paymentterms' => $PaymentTerms,
                ':discount' => '',
                ':discountcode' => '',
                ':pymtdiscount' => '',
                ':creditlimit' => $CreditLimit,
                ':salestype' => $SalesType,
                ':invaddrbranch' => '',
                ':taxref' => $_POST['crearCuenta']['taxref'],
                ':customerpoline' => ''
                );

            if (Yii::app()->db->createCommand($CrearCuenta)->execute($parameters)) {

                /*Inserto Branch para Facturar*/
                $Area = "MTY";
                $Salesman = "2RC";
                $DefaultLocation = "MTY";
                $DefaultShipVia = "1";
                $TaxGroupid = "4";

                $CreateBranch = "insert into custbranch (
                    branchcode,
                    folio,
                    brname,
                    braddress1,
                    braddress2,
                    braddress3,
                    braddress4,
                    braddress5,
                    braddress6,
                    braddress7,
                    braddress8,
                    braddress10,
                    fecha_ingreso,
                    phoneno,
                    debtorno,
                    area,
                    salesman,
                    defaultlocation,
                    defaultshipvia,
                    taxgroupid
                    )
                    values (
                    :branchcode,
                    :folio,
                    :brname,
                    :braddress1,
                    :braddress2,
                    :braddress3,
                    :braddress4,
                    :braddress5,
                    :braddress6,
                    :braddress7,
                    :braddress8,
                    :braddress10,
                    :fecha_ingreso,
                    :phoneno,
                    :debtorno,
                    :area,
                    :salesman,
                    :defaultlocation,
                    :defaultshipvia,
                    :taxgroupid
                    )";

                $parameters = array(
                    ':branchcode' => "T-" . $DebtorNo,
                    ':folio' => '',//"F-" . $DebtorNo,
                    ':brname' => $NombreCompleto,
                    ':braddress1' => $_POST['crearCuenta']['direccion1'],
                    ':braddress2' => $_POST['crearCuenta']['direccion2'],
                    ':braddress3' => $_POST['crearCuenta']['direccion3'],
                    ':braddress4' => $_POST['crearCuenta']['direccion4'],
                    ':braddress5' => $_POST['crearCuenta']['direccion5'],
                    ':braddress6' => $_POST['crearCuenta']['direccion6'],
                    ':braddress7' => $_POST['crearCuenta']['direccion7'],
                    ':braddress8' => $_POST['crearCuenta']['direccion8'],
                    ':braddress10' => $_POST['crearCuenta']['direccion10'],
                    ':fecha_ingreso' => date('Y-m-d'),
                    ':phoneno' => $_POST['crearCuenta']['telefono'],
                    ':debtorno' => $DebtorNo, ':area' => $Area,
                    ':salesman' => $Salesman,
                    ':defaultlocation' => $DefaultLocation,
                    ':defaultshipvia' => $DefaultShipVia,
                    ':taxgroupid' => $TaxGroupid);

                if (Yii::app()->db->createCommand($CreateBranch)->execute($parameters)) {

                    $UpdateDebtorno="update rh_crm_prospecto set debtorno = :debtorno where idProspecto = :idProspecto";

                    $parameters= array(
                        ':debtorno' => $DebtorNo,
                        ':idProspecto' => $_POST['crearCuenta']['idProspecto']
                        );

                    if(Yii::app()->db->createCommand($UpdateDebtorno)->execute($parameters)){

                        $ChangeStatus = "UPDATE rh_crm_prospecto SET tipo = :tipo WHERE idProspecto = :idProspecto";

                        $parameters = array(
                            ':idProspecto' => $_POST['crearCuenta']['idProspecto'],
                            ':tipo' => 'CUENTA'
                        );

                    }else {
                        echo CJSON::encode(array(
                            'requestresult' => 'fail',
                            'message' => "Ocurrio un error al momento de registrar la cuenta. Intente de nuevo por favor "
                        ));
                    }
                        if(Yii::app()->db->createCommand($ChangeStatus)->execute($parameters)){

                            $LeadLog = "INSERT INTO events (
                                title,
                                prospecto_id,
                                debtorno,
                                tipo_log,
                                userid,
                                Descripcion,
                                start,
                                end)
                            VALUES (
                                :title,
                                :prospecto_id,
                                :debtorno,
                                :tipo_log,
                                :userid,
                                :Descripcion,
                                :start,
                                :end)";

                            $parameters = array(
                                ':title' => 'Update',
                                ':prospecto_id' => 'prospecto_id',
                                ':debtorno' => $DebtorNo,
                                ':tipo_log' => 'Update',
                                ':userid' => $_SESSION['UserID'],
                                ':Descripcion' => "Se creó la cuenta para el prospecto ".$_POST['crearCuenta']['nombre']."",
                                ':start' => date('Y-m-d H:i:s') ,
                                ':end' => date('Y-m-d H:i:s')
                            );



                            $NewUpdate = "
                            <div class='panel2 callout radius' style='margin-bottom: 5px;' >
                                <h4 style='margin-top: -5px;'><small>{$_SESSION['UserID']}</small></h4>
                                <p style='font-size:10px; margin-bottom:5px;'>Se creó la cuenta para el prospecto ".$_POST['crearCuenta']['nombre']."</p>
                                <p style='font-size:10px; margin-bottom:5px;'>".date('Y-m-d H:i:s')."</p>
                            </div>";

                            if(Yii::app()->db->createCommand($LeadLog)->execute($parameters)){
                                echo CJSON::encode(array(
                                    'requestresult' => 'ok',
                                    'message' => "El estatus del prospecto  ha cambiado a Cuenta...",
                                    'NewUpdate'=>$NewUpdate
                                ));
                            }
                        }else {
                            echo CJSON::encode(array(
                                'requestresult' => 'fail',
                                'message' => "Ocurrio un error al momento de registrar la cuenta. Intente de nuevo por favor "
                            ));
                        }
                } else {
                    echo CJSON::encode(array(
                        'requestresult' => 'fail',
                        'message' => "Ocurrio un error al momento de registrar la cuenta. Intente de nuevo por favor "
                    ));
                }
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Ocurrio un error al momento de registrar la cuenta. Intente de nuevo por favor "
                ));
            }
        }else{
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "Error, los campos marcados con * son obligatorios"
            ));
        }
        return;
    }

    public function actionUpdate($id=null){

        $prospecto_id=Yii::app()->db->createCommand()
        ->select('idProspecto')
        ->from('rh_crm_prospecto')
        ->where("debtorno = '".$_POST['editarCuenta']['debtorno']."'")
        ->queryAll();

        if(!empty($id)){

            $PriceList = Yii::app()->db->createCommand()
            ->select('typeabbrev, sales_type')
            ->from('salestypes')
            ->queryAll();

            $AccountData=Yii::app()->db->createCommand()
            ->select('*')
            ->from('debtorsmaster')
            ->where('debtorno='.$id.'')
            ->queryAll();

        }

        if(!empty($_POST['editarCuenta']['debtorno'])){

            $UpdateAccount="update debtorsmaster set
            name = :name,
            name2 = :name2,
            address1 = :direccion1,
            address2 = :direccion2,
            address3 = :direccion3,
            address4 = :direccion4,
            address5 = :direccion5,
            address6 = :direccion6,
            address7 = :direccion7,
            address8 = :direccion8,
            address9 = :direccion9,
            address10 = :direccion10,
            rh_tel = :telefono,
            taxref = :taxref,
            salestype = :pricelist,
            discount = :discount,
            creditlimit = :creditlimit
            where debtorno = :debtorno
            ";

            $parameters= array(
                'name'=>$_POST['editarCuenta']['nombre'],
                'name2'=>$_POST['editarCuenta']['nombre2'],
                'direccion1'=>$_POST['editarCuenta']['direccion1'],
                'direccion2'=>$_POST['editarCuenta']['direccion2'],
                'direccion3'=>$_POST['editarCuenta']['direccion3'],
                'direccion4'=>$_POST['editarCuenta']['direccion4'],
                'direccion5'=>$_POST['editarCuenta']['direccion5'],
                'direccion6'=>$_POST['editarCuenta']['direccion6'],
                'direccion7'=>$_POST['editarCuenta']['direccion7'],
                'direccion8'=>$_POST['editarCuenta']['direccion8'],
                'direccion9'=>$_POST['editarCuenta']['direccion9'],
                'direccion10'=>$_POST['editarCuenta']['direccion10'],
                'telefono'=>$_POST['editarCuenta']['telefono'],
                'taxref'=>$_POST['editarCuenta']['taxref'],
                'pricelist'=>$_POST['editarCuenta']['pricelist'],
                'discount'=>$_POST['editarCuenta']['discount'],
                'creditlimit'=>$_POST['editarCuenta']['creditlimit'],
                'debtorno'=>$_POST['editarCuenta']['debtorno'],
                );

            if(Yii::app()->db->createCommand($UpdateAccount)->execute($parameters)){

                $LogUpdate = "insert into events (title, debtorno, prospecto_id, tipo_log, userid, Descripcion, start, end)
                          values(:title, :debtorno, :prospecto_id, :tipo_log, :userid, :Descripcion, :start, :end)";

                $parameters = array(
                    ':title' => 'Update',
                    ':prospecto_id' => "'".$prospecto_id[0]['idProspecto']."'",
                    ':debtorno' => $_POST['editarCuenta']['debtorno'],
                    ':tipo_log' => 'Update',
                    ':userid' => $_SESSION['UserID'],
                    ':Descripcion' => 'Se editó la cuenta',
                    ':start' => date('Y-m-d H:i:s') ,
                    ':end' => date('Y-m-d H:i:s') ,
                );
                fb::info($parameters);
                fb::info($prospecto_id);
                if(Yii::app()->db->createCommand($LogUpdate)->execute($parameters)){
                echo CJSON::encode(array(
                        'requestresult' => 'ok',
                        'message' => 'La cuenta ha sido editada correctamente',
                        'Data' => $_POST['editarCuenta']
                    ));
                }else{
                    echo CJSON::encode(array(
                            'requestresult' => 'fail',
                            'message' => 'Ocurrio un error al editar la información. Intente de nuevo...',
                            //'Data' => $_POST['editarCuenta']
                        ));
                }
            }else{
                echo CJSON::encode(array(
                        'requestresult' => 'fail',
                        'message' => 'Ocurrio un error al editar la información. Intente de nuevo...',
                        //'Data' => $_POST['editarCuenta']
                    ));
            }
            return;
        }

        $this->render('update', array(
            'AccountData'=>$AccountData[0],
            'PriceList' => $PriceList
            ));

    }

    public function actionView(){

        if(!empty($_GET['id'])){

            $AccountData= Yii::app()->db->createCommand()
            ->select('debtorsmaster.*, rh_crm_prospecto.idProspecto')
            ->from('debtorsmaster')
            ->leftJoin('rh_crm_prospecto', 'rh_crm_prospecto.debtorno=debtorsmaster.debtorno')
            ->where("debtorsmaster.debtorno='".$_GET['id']."'")
            ->queryAll();

            $WhereActivities = "debtorno='" .$_GET['id']. "' and tipo_log = 'Calendario'";
            $WhereUpdates = "debtorno='" .$_GET['id']. "' and tipo_log = 'Update'";


            $ListUpdates = Yii::app()->db->createCommand()
            ->select('*')->from('events')
            ->where($WhereUpdates)
            ->order('start desc')
            ->queryAll();


            $ListActivities = Yii::app()->db->createCommand()
            ->select('*')
            ->from('events')
            ->where($WhereActivities)
            ->order('start desc')
            ->queryAll();


            $SalesOrdesData = Yii::app()->db->createCommand()
            ->select('salesorders.*, (SELECT SUM(SOD.quantity*SOD.unitprice) as OrderTotal FROM salesorderdetails as SOD WHERE SOD.orderno = salesorders.orderno ) as OrderTotal2 ')
            ->from('salesorders')
            ->where("debtorno='" . $AccountData[0]['debtorno'] . "'")
            ->queryAll();

             $TipoActividad = Yii::app()->db->createCommand()->select('*')->from('rh_tipoactividad')->queryAll();

            FB::INFO($AccountData);
        }
        $this->render('view', array(
            'AccountData'=> $AccountData[0],
            'SalesOrdesData'=>$SalesOrdesData,
            'ListActivities'=> $ListActivities,
            'TipoActividad' => $TipoActividad,
            'ListUpdates'=> $ListUpdates,
            ));
    }

    public function actionSearch(){

        if (!empty($_POST['Search']['Nombre'])) {
                $Where.= "taxref LIKE '%" . $_POST['Search']['Nombre'] . "%'
                OR debtorno LIKE '%" . $_POST['Search']['Nombre'] . "%'
                OR name LIKE '%" . $_POST['Search']['Nombre'] . "%'";


        $Cuentas= Yii::app()->db->createCommand()
        ->select('*')
        ->from('debtorsmaster')
        ->where($Where)
        ->queryAll();

        $TBody = "";

            if(!empty($Cuentas)){
                foreach ($Cuentas as $Data) {
                    $TBody .="
                        <tr id='{$Data['debtorno']}'>
                            <td>{$Data['debtorno']}</td>
                            <td>{$Data['name']}</td>
                            <td>{$Data['taxref']}</td>
                            <td>{$Data['rh_tel']}</td>
                            <td>
                                <span data-tooltip class='has-tip radius' title='Ver informacion a detalle'><a href='".Yii::app()->createUrl('crm/cuentas/view', array('id'=>$Data['debtorno']))."' class='fi-magnifying-glass'></a></span>&nbsp;
                                <span data-tooltip class='has-tip radius' title='Editar contacto'><a href='".Yii::app()->createUrl('crm/cuentas/update', array('id'=>$Data['debtorno']))."' class='fi-pencil'></a></span>&nbsp;
                            </td>
                        </tr>
                        ";
                }

                echo CJSON::encode(array(
                    'requestresult'=>'ok',
                    'TBody'=>$TBody
                ));
            }else{
                echo CJSON::encode(array(
                    'requestresult'=>'fail',
                    'message' => 'No se encontraron Resultados...'
                ));
            }
        }else{
            echo CJSON::encode(array(
                'requestresult'=>'fail',
                'message' => 'Debe ingresar un Nombre de Cliente...'
            ));
        }
        return;
    }

}
