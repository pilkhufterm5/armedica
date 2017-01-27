<?php
/**
 *@Todo
 * Kanban
 * @Author erasto@realhost.com.mx
 */
class KanbanController extends Controller{

    public $layout = 'webroot.themes.found.views.layouts.main';

    public function actionIndex() {
        $GetFases = Yii::app()->db->createCommand()->select(' * ')->from(' rh_crm_fases_venta ')->where(" status = '1' ")->order('orden')->queryAll();
        FB::INFO($GetFases,'___________________________FASES VENTA');
        $ListFases = CHtml::listData($GetFases, 'id', 'nombre');
        FB::INFO($ListFases,'___________________________LISTA FASES');

        $GetLeadsData = Yii::app()->db->createCommand()->select(' idProspecto, nombre, apellidoPaterno, apellidoPaterno, email, telefono, celular, tipo, id_fase_venta, colorpicker ')->from(' rh_crm_prospecto ')->where()->queryAll();

        $Oportunidades= Yii::app()->db->createCommand()->select('*')->from('rh_crm_oportunidades')->queryAll();

        $LeadsGoup = array();
        foreach($GetLeadsData as $Lead){
            $LeadsGoup[$Lead['id_fase_venta']][] = $Lead;
        }
        FB::INFO($LeadsGoup, '_______________________________LEADS');
        $this->render('index', array('ListFases' => $ListFases, 'LeadsGoup' => $LeadsGoup));
    }


    public function actionTest() {

        $this->render('test');
    }

    public function actionSearch(){
        FB::INFO($_REQUEST,'________________________REQ');
        if(!empty($_REQUEST['Search']['prospecto'])){
            $Where=" (idProspecto like '%".$_REQUEST['Search']['prospecto']."%' OR nombre like '%".$_REQUEST['Search']['prospecto']."%') ";

            $Prospectos=Yii::app()->db->createCommand()
            ->select('idProspecto, nombre')
            ->from('rh_crm_prospecto')
            ->limit(10)
            ->where($Where)
            ->queryAll();

            foreach ($Prospectos as $Data) {
               $_Data[] = array(
                    'value' => $Data['idProspecto'] . "--" . $Data['nombre'],
                     'id' => $Data['idProspecto']
                    );
            }
            FB::INFO($_Data,'________________________DATA');
            echo CJSON::encode(array('requestresult' => 'ok', 'DataList' => $_Data));
        }

        return;
    }

    public function actionCambiarfase(){
        if(!empty($_POST['Fase']['id_fase_venta'])){

            $FaseDeVenta= Yii::app()->db->createCommand()
            ->select('nombre')
            ->from('rh_crm_fases_venta')
            ->where("id='".$_POST['Fase']['id_fase_venta']."'")
            ->queryAll();

            $LeadName=explode('--', $_POST['Fase']['lead_name']);


//             <!-- <div nombre_prospecto="prospecto rapído 001" id_prospecto="1" id="Lead_1" class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
//     <div id_forcolor="1" style="background-color:#0247FE !important;" class="portlet-header ui-widget-header ui-corner-all" name="LeadName"><span class="ui-icon portlet-toggle ui-icon-plusthick"></span>
//     Prospecto: prospecto rapído 001                                </div>
//     <div class="portlet-content" style="display: none;">
//         <div class="large-12 small-12 columns">
//             <label class="label1">Nombre completo:</label>
//             <input type="hidden" id="header_color" value="#0247FE">
//         </div>
//         <div class="large-12 small-12 columns"><label class="label2">prospecto rapído 001 prospecto rapído 001 </label></div>
//         <div class="large-12 small-12 columns"><label class="label1">Teléfono:</label></div>
//         <div class="large-12 small-12 columns"><label class="label2">8881111111</label></div>
//         <div class="large-12 small-12 columns"><label class="label1">Celular:</label></div>
//         <div class="large-12 small-12 columns"><label class="label2"></label></div>
//         <div class="large-12 small-12 columns"><label class="label1">E-mail:</label></div>
//         <div class="large-12 small-12 columns"><label class="label2">manuel_carrillo_90@hotmail.com</label></div>
//     </div>
// </div> -->


            $NewPortlet="<div class='portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all' id='Lead_{$_POST['Fase']['prospecto_id']}' id_prospecto='{$_POST['Fase']['prospecto_id']}' nombre_prospecto='{$LeadName[1]}'>
                            <div name='LeadName' class='portlet-header ui-widget-header ui-corner-all' style='background-color:{$_POST['Fase']['colorpicker']} !important;'>
                                <span class='ui-icon ui-icon-plusthick portlet-toggle'></span>
                                {$LeadName[1]}
                            </div>
                            <div class='portlet-content' style='display: none;'>
                            </div>
                        </div>";

            $UpdateFaseVenta= "update rh_crm_prospecto set id_fase_venta = :id_fase_venta , colorpicker = :colorpicker where idProspecto = :idProspecto";

            $parameters=array(
                ':id_fase_venta'=> $_POST['Fase']['id_fase_venta'],
                ':idProspecto'=> $_POST['Fase']['prospecto_id'],
                ':colorpicker' => $_POST['Fase']['colorpicker'],
                );

            if(Yii::app()->db->createCommand($UpdateFaseVenta)->execute($parameters)){

                 $LogUpdate = "insert into events (title, prospecto_id, tipo_log, userid, Descripcion, start, end)
                    values(:title, :prospecto_id, :tipo_log, :userid, :Descripcion, :start, :end)";

                $date = date('Y-m-d H:i:s');

                $parameters = array(
                    ':title' => 'UPDATE',
                    ':prospecto_id' => $_POST['Fase']['prospecto_id'],
                    ':tipo_log' => 'UPDATE',
                    ':userid' => $_SESSION['UserID'],
                    ':Descripcion' => 'La fase de venta cambió a '.$FaseDeVenta[0]['nombre'].'',
                    ':start' => $date,
                    ':end' => $date
                );

                if(Yii::app()->db->createCommand($LogUpdate)->execute($parameters)){
                     echo CJSON::encode(array(
                        'requestresult'=>'ok',
                        'message'=>'La fase de venta para el prospecto '.$LeadName[1].' se actualizó correctamente',
                        'NewPortlet'=>$NewPortlet,
                        'Data'=>$_POST['Fase']
                    ));
                }else{
                     echo CJSON::encode(array(
                    'requestresult'=>'fail',
                    'message'=>'Ocurrió un error al actualizar la informacion, intente de nuevo',
                ));
                }
            }else{
                echo CJSON::encode(array(
                    'requestresult'=>'fail',
                    'message'=>'Ocurrió un error al actualizar la informacion, intente de nuevo',
                ));
            }
        }else{
            echo CJSON::encode(array(
                'requestresult'=>'fail',
                'message'=>'Ocurrió un error al actualizar la informacion, intente de nuevo',
            ));
        }
    }
}
