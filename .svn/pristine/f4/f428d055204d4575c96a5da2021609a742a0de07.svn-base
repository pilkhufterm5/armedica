<?php

class TipoactividadController extends Controller {

    public $layout = 'webroot.themes.found.views.layouts.main';

    public function actionIndex(){

    	$TipoActividadData=Yii::app()->db->createCommand()
		->select('*')
		->from('rh_tipoactividad')
		->queryAll();

    	$this->render('index', array('TipoActividadData'=>$TipoActividadData));

    }

    public function actionCreate(){
    	if(!empty($_POST['Agregar'])){
    		$TipoActividad="insert into rh_tipoactividad(descripcion)values(:descripcion)";
    		$parameters=array('descripcion'=>$_POST['Agregar']['descripcion']);

    		if(Yii::app()->db->createCommand($TipoActividad)->execute($parameters)){
    			$LastID = Yii::app()->db->getLastInsertID();
    			$NewRow .= "
                            <tr>
                            <td class=\" \">{$LastID}</td>
                            <td class=\" \">{$_POST['Agregar']['descripcion']}</td>
                            <td class=\" \">
                                <span data-tooltip class='has-tip radius' title='Editar información'><a onclick='LoadForm({$LastID})' class='fi-pencil'></a></span>&nbsp;
                            </td>
                            </tr>";

                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'NewRow' => $NewRow,
                    'message' => "Información guardada correctamente..."
                    ));
    		}else{
    			 echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error al guardar la información, intente de nuevo..."
                    ));
    		}
        }
    }

    public function actionLoadForm(){

        if(!empty($_POST['LoadForm']['id'])){
            $LoadForm=Yii::app()->db->createCommand()
            ->select('*')
            ->from('rh_tipoactividad')
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
            $Update="update rh_tipoactividad set descripcion = :descripcion where id = :id";

            $parameters = array(
                ':id' => $_POST['Actualizar']['id'],
                ':descripcion' => $_POST['Actualizar']['descripcion'],
                );

            $NewRow = "
                    <td>{$_POST['Actualizar']['id']}</td>
                    <td>{$_POST['Actualizar']['descripcion']}</td>
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
?>
