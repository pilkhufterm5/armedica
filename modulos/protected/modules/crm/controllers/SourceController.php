<?php
class SourceController extends Controller{

public $layout = 'webroot.themes.found.views.layouts.main';

    public function actionIndex(){
        $SourceData=Yii::app()->db->createCommand()
        ->select('*')
        ->from('rh_crm_leadsource')
        ->queryAll();
        $this->render('index', array('SourceData'=>$SourceData));
    }

     public function actionCreate(){

        if(!empty($_POST['Agregar']['nombre'])){
            $CrearFaseVenta="INSERT INTO rh_crm_leadsource (nombre, status) values(:nombre, :status)";

            $parameters=array(
                ':nombre'=>$_POST['Agregar']['nombre'],
                ':status'=>$_POST['Agregar']['status']);

            if(Yii::app()->db->createCommand($CrearFaseVenta)->execute($parameters)){
                $LastFaseID=Yii::app()->db->getLastInsertID();

                if($_POST['Agregar']['status']==1){
                    $status='Activo';
                }else{
                    $status='Inactivo';
                }

                $NewRow = "
                <tr id='{$LastFaseID}'>
                    <td>{$LastFaseID}</td>
                    <td>{$_POST['Agregar']['nombre']}</td>
                    <td>{$status}</td>
                    <td>
                       <span data-tooltip class='has-tip radius' title='Editar información'><a onclick='LoadForm({$LastFaseID})' class='fi-pencil'></a></span>&nbsp;
                    </td>
                </tr>";

                echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => "El source se ha guardado correctamente...",
                'NewRow' => $NewRow));
            }else{
                echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "Ocurrio un error al registrar los datos. Intente de nuevo...",
                ));
            }
        }else{
            echo CJSON::encode(array(
            'requestresult' => 'fail',
            'message' => "Ocurrio un error al registrar los datos. Intente de nuevo...",
            ));
        }
    }

    public function actionLoadForm(){

        if(!empty($_POST['LoadForm']['id'])){
            $LoadForm=Yii::app()->db->createCommand()
            ->select('*')
            ->from('rh_crm_leadsource')
            ->where('id='.$_POST['LoadForm']['id'])
            ->queryAll();

            if(!empty($LoadForm)){
                echo CJSON::encode(array(
                    'requestresult'=>'ok',
                    'LoadForm'=>$LoadForm[0]
                    ));
            }else{
                echo CJSON::encode(array(
                    'requestresult'=>'fail',
                    'message'=>'Ocurrió un error al cargar el formulario. Intente de nuevo... '
                    ));
            }
        }else{
            echo CJSON::encode(array(
                'requestresult'=>'fail',
                'message'=>'Ocurrió un error al cargar el formulario. Intente de nuevo... '
                ));
        }
        return;
    }
     public function actionUpdate(){
        if(!empty($_POST['Actualizar']['id'])){
            $Update="update rh_crm_leadsource set nombre = :nombre , status = :status where id = :id";

            $parameters = array(
                ':id' => $_POST['Actualizar']['id'],
                ':nombre' => $_POST['Actualizar']['nombre'],
                ':status' => $_POST['Actualizar']['status']
                );

            if($_POST['Actualizar']['status']==1){
                $status='Activo';
            }else{
                $status='Inactivo';
            }

            $NewRow = "
                    <td>{$_POST['Actualizar']['id']}</td>
                    <td>{$_POST['Actualizar']['nombre']}</td>
                    <td>{$status}</td>
                    <td>
                        <span data-tooltip class='has-tip radius' title='Editar información'><a onclick='LoadForm({$_POST['Actualizar']['id']})' class='fi-pencil'></a></span>&nbsp;
                    </td>";

            if(Yii::app()->db->createCommand($Update)->execute($parameters)){
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => 'La información se ha editado correctamente',
                    'NewRow' => $NewRow,
                    'id' => $_POST['Actualizar']['id']
                ));
            }else{
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => 'Ocurrió un error al editar la información. Intente de nuevo por favor...',
                    'NewRow' => $NewRow
                ));
            }
        }else{
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => 'Ocurrió un error al editar la información. Intente de nuevo por favor...',
                'NewRow' => $NewRow
            ));
        }
    }

}
