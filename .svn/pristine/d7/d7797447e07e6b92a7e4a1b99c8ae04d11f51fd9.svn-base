<?php

class ActivitiesController extends Controller {

    public $layout = 'webroot.themes.found.views.layouts.main';


    // Funcion que guarda los nuevos tipo de actividad
    /* ASDFGHJKLÑ
    public function actionCreateActivity(){
        if(!empty($_POST['Create']) && !empty($_POST['Create']['descripcion']))
        {
            $SQLInsert = "INSERT INTO rh_tipoactividad (descripcion)
                                      VALUES(:descripcion)";
            $parameters = array(
                ':descripcion' => $_POST['Create']['descripcion'],
            );
            if (Yii::app()->db->createCommand($SQLInsert)->execute($parameters)) {
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "La Actividad se ha guardado correctamente..."
                ));
            }else{
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "La Actividad no se pudo guardar, intente de nuevo..."
                ));
            }
            return;
        }
    }*/

    // Ajax function que regresa las actividades actuales
    public function actionRefreshActivities(){
        if (Yii::app()->request->isAjaxRequest){
            $queryData  =  Yii::app()->db->createCommand('SELECT * from rh_tipoactividad;')->queryAll();
            $data = CHtml::listData($queryData, 'id', 'descripcion');
            foreach($data as $value=>$name){
                echo CHtml::tag('option',array('value'=>$value),CHtml::encode($name),true);
            }
        }
    }

    public function actionDeleteActivity(){
        if(!empty($_POST['Delete']) && !empty($_POST['Delete']['id']))
        {
            $SQLDelete = "DELETE FROM rh_tipoactividad WHERE id = :id";
            $parameters = array(
                ':id' => $_POST['Delete']['id'],
            );
            if (Yii::app()->db->createCommand($SQLDelete)->execute($parameters)) {
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "La Actividad se ha borrado correctamente..."
                ));
            }else{
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "La Actividad no se pudo borrar, intente de nuevo..."
                ));
            }
            return;
        }
    }

    public function actionIndex() {
        $this->render('index');
    }

    public function actionCalendar() {

        $ActivitiesData  =  Yii::app()->db
        ->createCommand('SELECT * from rh_tipoactividad;')
        ->queryAll();

        $ContactsData = Yii::app()->db->createCommand()
        ->select('idContacto, nombre')
        ->from('rh_crm_contacto')
        ->queryAll();
        $ListContactos = CHtml::listData($ContactsData, 'idContacto', 'nombre');
        $ListActivities = CHtml::listData($ActivitiesData, 'id', 'descripcion');
        $this->render('calendar',array('actividades'=>$ListActivities, 'ContactsData'=>$ListContactos));
    }

    public function actionCreateactivitie(){
        //date("Y-m-d", strtotime($_POST['Create']['start']));

        FB::INFO($_POST,'_____________________________POST');
        if(!empty($_POST['Create']['title'])){
            $_POST['Create']['start'] = date("Y-m-d H:i:s", strtotime($_POST['Create']['start']));
            $_POST['Create']['end'] = date("Y-m-d H:i:s", strtotime($_POST['Create']['end']));

            $SQLInsert = "INSERT INTO events (title, debtorno, start, end, actividad_id, userid, contacto_id, prospecto_id, Descripcion, tipo_log)
                          VALUES(:title, :debtorno, :start, :end, :actividad_id, :userid, :contacto_id, :prospecto_id, :descripcion, :tipo_log)";
            $parameters = array(
                ':title' => $_POST['Create']['title'],
                ':debtorno' => $_POST['Create']['debtorno'],
                ':start' => $_POST['Create']['start'],
                ':end' => $_POST['Create']['end'],
                ':actividad_id' => $_POST['Create']['actividad'],
                ':userid'=>$_SESSION['UserID'],
                ':contacto_id'=> $_POST['Create']['contacto_id'],
                ':prospecto_id'=> $_POST['Create']['prospecto_id'],
                ':descripcion' => $_POST['Create']['descripcion'],
                ':tipo_log' => $_POST['Create']['TipoLog'],
            );

            FB::INFO($parameters);
            if (Yii::app()->db->createCommand($SQLInsert)->execute($parameters)) {

                $NewActivity="  <div class='panel2 callout radius' style='margin-bottom: 5px;' >
                                    <h4 style='margin-top: -5px;'><small>{$_POST['Create']['title']}</small></h4>
                                    <p style='font-size:10px; margin-bottom:5px;'>{$_POST['Create']['start']}</p>
                                    <p style='font-size:10px; margin-bottom:5px;'>{$_POST['Create']['end']}</p>
                                    <p style='font-size:10px; margin-bottom:5px;'>{$_POST['Create']['descripcion']}</p>
                                </div>";

                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "La Actividad se ha guardado correctamente...",
                    'NewActivity'=>$NewActivity
                ));
            }else{
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "La Actividad no se pudo guardar, intente de nuevo..."
                ));
            }
        }else{
             echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "La Actividad no se pudo guardar, intente de nuevo..."
            ));
        }
        return;
    }

    public function actionUpdateactivitie(){
        //date("Y-m-d", strtotime($_POST['Update']['start']));

        FB::INFO($_POST,'_____________________________POST');
        if(!empty($_POST['Update']['title'])){
            $_POST['Update']['start'] = date("Y-m-d H:i:s", strtotime($_POST['Update']['start']));
            $_POST['Update']['end'] = date("Y-m-d H:i:s", strtotime($_POST['Update']['end']));

            $SQLUpdate = "UPDATE events SET title = :title, start = :start, end = :end, actividad_id = :actividad_id, contacto_id = :contacto_id WHERE event_id = :event_id";
            $parameters = array(
                ':title' => $_POST['Update']['title'],
                ':start' => $_POST['Update']['start'],
                ':end' => $_POST['Update']['end'],
                ':actividad_id' => $_POST['Update']['actividad'],
                ':contacto_id' => $_POST['Update']['contacto_id'],
                ':event_id' => $_POST['Update']['id']
            );
            if (Yii::app()->db->createCommand($SQLUpdate)->execute($parameters)) {
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "La Actividad se ha Actualizado correctamente..."
                ));
            }else{
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "La Actividad no se pudo Actualizar, intente de nuevo..."
                ));
            }
            return;
        }
    }

    public function actionLoadcalendar() {
        $Where="tipo_log='Calendario' and userid='".$_SESSION['UserID']."'";
        $LoadCalendar = Yii::app()->db->createCommand()
                        ->select(' (event_id)as id, title, start, end, actividad_id, tipo_log, contacto_id ')
                        ->from('events')->where($Where)->queryAll();
        FB::INFO($LoadCalendar,'__________________________CALENDAR DATA');
        echo CJSON::encode($LoadCalendar);
        Yii::app()->end();
    }

}
