<?php
class OportunidadesController extends Controller
{

    public $layout = 'webroot.themes.found.views.layouts.main';

    public function actionIndex() {

        $Fases_Venta = Yii::app()->db->createCommand()
        ->select('*')->from('rh_crm_fases_venta')
        ->where('status=1')->order('orden')
        ->queryAll();

        $ProspectoData = Yii::app()->db->createCommand()
            ->select('idProspecto, nombre, debtorno')
            ->from('rh_crm_prospecto')
            ->where("tipo='CUENTA'")
            ->queryAll();

        $Oportunidades = Yii::app()->db->createCommand()
            ->select('rh_crm_oportunidades.*, rh_crm_fases_venta.nombre as fase_venta')
            ->from('rh_crm_oportunidades')
            ->leftJoin('rh_crm_fases_venta' , 'rh_crm_oportunidades.id_fase_venta=rh_crm_fases_venta.id')
            ->queryAll();

        $this->render('index', array(
            'Oportunidades' => $Oportunidades,
            'Fases_Venta'=> $Fases_Venta,
            'ProspectoData'=> $ProspectoData
        ));
    }
    public function actionView($id=null) {
        if(!empty($id)){
            $DetalleOportunidades = Yii::app()->db->createCommand()
            ->select('rh_crm_oportunidades.*, rh_crm_prospecto.nombre as prospecto, rh_crm_prospecto.apellidoPaterno, rh_crm_prospecto.apellidoMaterno, rh_crm_prospecto.debtorno as cuenta, rh_crm_fases_venta.nombre as fase_venta')
            ->from('rh_crm_oportunidades')
            ->leftJoin('rh_crm_prospecto', 'rh_crm_prospecto.idProspecto=rh_crm_oportunidades.id_prospecto')
            ->leftJoin('rh_crm_fases_venta', 'rh_crm_fases_venta.id=rh_crm_oportunidades.id_fase_venta')
            ->where("rh_crm_oportunidades.id='" . $id . "'")
            ->queryAll();

            $Fases_Venta = Yii::app()->db->createCommand()
                ->select('*')->from('rh_crm_fases_venta')
                ->where('status=1')->order('orden')
                ->queryAll();

            $ProspectoData = Yii::app()->db->createCommand()
                ->select('idProspecto, nombre, debtorno')
                ->from('rh_crm_prospecto')
                ->where("tipo='CUENTA'")
                ->queryAll();


            $SalesOrdesData = Yii::app()->db->createCommand()
            ->select('salesorders.*, (SELECT SUM(SOD.quantity*SOD.unitprice) as OrderTotal FROM salesorderdetails as SOD WHERE SOD.orderno = salesorders.orderno ) as OrderTotal2 ')
            ->from('salesorders')
            ->where("debtorno='" . $DetalleOportunidades[0]['cuenta'] . "'")
            ->queryAll();
            FB::INFO($DetalleOportunidades,'____________________________GETSALES');

            $this->render('view', array(
                'DetalleOportunidades' => $DetalleOportunidades[0],
                'SalesOrdesData' => $SalesOrdesData,
                'Fases_Venta'=> $Fases_Venta,
                'ProspectoData'=> $ProspectoData
            ));
        }else{
            $this->redirect(array("oportunidades/index"));
        }
    }

    public function actionUpdate($id = null) {
        if (!empty($id)) {
            $OportunidadesData = Yii::app()->db->createCommand()
            ->select('rh_crm_oportunidades.*, rh_crm_prospecto.nombre as prospecto, rh_crm_fases_venta.nombre as fase_venta')
            ->from('rh_crm_oportunidades')
            ->leftJoin('rh_crm_prospecto', 'rh_crm_prospecto.idProspecto=rh_crm_oportunidades.id_prospecto')
            ->leftJoin('rh_crm_fases_venta', 'rh_crm_fases_venta.id=rh_crm_oportunidades.id_fase_venta')
            ->where("rh_crm_oportunidades.id='" . $id . "'")
            ->queryAll();

            $Fases_Venta = Yii::app()->db->createCommand()
                ->select('*')->from('rh_crm_fases_venta')
                ->where('status=1')->order('orden')
                ->queryAll();

            $ProspectoData = Yii::app()->db->createCommand()
                ->select('idProspecto, nombre, debtorno')
                ->from('rh_crm_prospecto')
                ->where("tipo='CUENTA'")
                ->queryAll();
            //$OportunidadesData = Yii::app()->db->createCommand()->select('*')->from('rh_crm_oportunidades')->where("id='" . $id . "'")->queryAll();
        }
 // <!--  nombre,
 //                created,
 //                closed,
 //                amount,
 //                probability,
 //                assignedto,
 //                descripcion,
 //                id_prospecto,
 //                id_fase_venta -->
        if (!empty($_POST['Update']['nombre'])) {
            $UpdateOportunidad = "update rh_crm_oportunidades set
                nombre = :nombre,
                closed = :closed,
                amount = :amount,
                probability=:probability,
                id_prospecto=:id_prospecto,
                id_fase_venta=:id_fase_venta,
                descripcion=:descripcion
                where id= :id
                ";

            $parameters = array(
                ':id' => $_POST['Update']['id'],
                ':nombre' => $_POST['Update']['nombre'],
                ':closed' => $_POST['Update']['closed'],
                ':amount' => $_POST['Update']['monto'],
                ':probability' => $_POST['Update']['probability'],
                ':id_prospecto' => $_POST['Update']['id_prospecto'],
                ':id_fase_venta' => $_POST['Update']['id_fase_venta'],
                ':descripcion' => $_POST['Update']['descripcion'],

            );
            if (Yii::app()->db->createCommand($UpdateOportunidad)->execute($parameters)) {
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => 'La información ha sido actualizada con éxito'
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => 'Ocurrió un error al actualizar la información. Intente de nuevo...'
                ));
            }
            return;
        }

        $this->render('update', array(
            'OportunidadesData' => $OportunidadesData[0],
            'Fases_Venta'=> $Fases_Venta,
            'ProspectoData'=> $ProspectoData
        ));
    }

     public function actionCreate() {

        if(empty($_POST['crearOportunidad']['nombre'])){
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => 'El nombre de la oportunidad es requerido ...'
            ));
        return;
        }

         if(empty($_POST['crearOportunidad']['monto'])){
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => 'El monto de la oportunidad es requerido ...'
            ));
        return;
        }

        if(!empty($_POST['crearOportunidad']['debtorno'])){
            $debtorno=$_POST['crearOportunidad']['debtorno'];
        }else{
            $DebtorNo=Yii::app()->db->createCommand()
            ->select('debtorno')
            ->from('rh_crm_prospecto')
            ->where("idProspecto='".$_POST['crearOportunidad']['idProspecto']."'")
            ->queryAll();
            $debtorno=$DebtorNo[0]['debtorno'];
        }


        if (!empty($_POST['crearOportunidad']['idProspecto'])) {
            $crearOportunidad = "insert into rh_crm_oportunidades (
                nombre,
                created,
                closed,
                amount,
                probability,
                assignedto,
                descripcion,
                id_prospecto,
                id_fase_venta,
                debtorno
                )values(
                :nombre,
                :created,
                :closed,
                :amount,
                :probability,
                :assignedto,
                :descripcion,
                :id_prospecto,
                :id_fase_venta,
                :debtorno
                )";

            $parameters = array(
                ':nombre' => $_POST['crearOportunidad']['nombre'],
                ':created' => $_POST['crearOportunidad']['created'],
                ':closed' => $_POST['crearOportunidad']['closed'],
                ':amount' => $_POST['crearOportunidad']['monto'],
                ':probability'=> $_POST['crearOportunidad']['probability'],
                ':assignedto'=> $_SESSION['UserID'],
                ':descripcion'=> $_POST['crearOportunidad']['descripcion'],
                ':id_prospecto' => $_POST['crearOportunidad']['idProspecto'],
                ':id_fase_venta' => $_POST['crearOportunidad']['fase_venta'],
                ':debtorno' => $debtorno
            );

            if (Yii::app()->db->createCommand($crearOportunidad)->execute($parameters)) {
                 $LastOportunityID = Yii::app()->db->getLastInsertID();

                 $Fase_Venta=Yii::app()->db->createCommand()
                    ->select('nombre')
                    ->from('rh_crm_fases_venta')
                    ->where("id='".$_POST['crearOportunidad']['fase_venta']."'")
                    ->queryAll();

                 $NewRow = "
                <tr>
                    <td class='cantidades'>{$_POST['crearOportunidad']['idProspecto']}</td>
                    <td class='cantidades'>{$debtorno}</td>
                    <td>{$_POST['crearOportunidad']['nombre']}</td>
                    <td>{$Fase_Venta[0]['nombre']}</td>
                    <td class=\" \">{$_POST['crearOportunidad']['closed']}</td>
                    <td  class=\"cantidades\">$ ".number_format($_POST['crearOportunidad']['monto'], 2)."</td>
                    <td>
                        <span data-tooltip class='has-tip radius' title='Ver información a detalle'><a href='" . Yii::app()->createUrl('crm/oportunidades/view', array('id'=>$LastOportunityID)) . "' class='fi-magnifying-glass'></a></span>&nbsp;
                        <span data-tooltip class='has-tip radius' title='Editar oportunidad'><a href='" . Yii::app()->createUrl('crm/oportunidades/update', array('id'=>$LastOportunityID)) . "' class='fi-pencil'></a></span>&nbsp;
                    </td>
                </tr>";

                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "La oportinidad se ha registrado con éxito. El id de la oportunidad es ".$LastOportunityID."...",
                    'NewRow'=>$NewRow
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => 'Ocurrió un error al registrar la información. Intente de nuevo por favor...'
                ));
            }
        } else {
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => 'Ocurrió un error al registrar la información. Intente de nuevo por favor...'
            ));
        }
        return;
    }

}
