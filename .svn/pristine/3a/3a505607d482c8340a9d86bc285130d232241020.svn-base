<?php

/**
 * @Todo
 * Modulo de Contactos
 * @Author erasto@realhost.com.mx
 */
class ContactosController extends Controller
{

    public $layout = 'webroot.themes.found.views.layouts.main';

    // Regresa una lista en json con los nombres de los prospectos
    public function actionProspectosPorAjax() {
      //  FB::INFO($_REQUEST, '__________________REQ');
        if (Yii::app()->request->isAjaxRequest) {
            $query = '';
            if (isset($_GET['query'])) {
                $query = $_GET['query'];
            }

            // TODO: Arreglar el query a que jale los registros con un LIKE en ves de con un igual.
            $data = Yii::app()->db->createCommand()->select('idProspecto ,nombre, apellidoPaterno, apellidoMaterno, CONCAT(nombre, apellidoPaterno, apellidoMaterno) AS nombrecompleto')->from('rh_crm_prospecto')->where("nombre LIKE '%{$query}%' or apellidoPaterno = '{$query}' or apellidoMaterno = '{$query}' or CONCAT(nombre,' ', apellidoPaterno,' ',apellidoMaterno) = '{$query}'")->queryAll();
            $suggestions = array();
            foreach ($data as $lead) {
                $suggestions[] = array('value' => $lead['nombre'] . ' ' . $lead['apellidoPaterno'] . ' ' . $lead['apellidoMaterno'], 'data' => $lead['idProspecto'],);
            }

            // Formateo especial para hacer funcionar el Autocomplete
            echo '{"query": "Unit","suggestions": ' . CJSON::encode($suggestions) . '}';

            Yii::app()->end();
        }
    }

    /**
     * @Todo
     * Lista Contactos
     * @Author erasto@realhost.com.mx
     */
    public function actionIndex() {

        $ContactosData = Yii::app()->db->createCommand()->select(' * ')->from('rh_crm_contacto')->queryAll();
        FB::INFO($ContactosData, '______DATA');
        $LeadsList=Yii::app()->db->createCommand()->select(' * ')->from('rh_crm_prospecto')->queryAll();
        $this->render('index', array('ContactosData' => $ContactosData, 'LeadsList'=>$LeadsList));
    }

    /**
     * @Todo
     * Crea Contacto Rapido
     * @Author erasto@realhost.com.mx
     */
    public function actionQuickcontact() {

        if(empty($_POST['QuickContact']['nombre']) || empty($_POST['QuickContact']['apellidoPaterno']) || empty($_POST['QuickContact']['email']) || empty($_POST['QuickContact']['idProspecto']) ){
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "Todos los Campos son Obligatorios, intente de nuevo..."
            ));
            return;
            exit;
        }

        FB::INFO($CamposRequeridos, 'campos requeridos');
        FB::INFO($_POST, '_____________________________________POST');

        if (!empty($_POST['QuickContact']['nombre'])) {

            $SQLInsert = "INSERT INTO rh_crm_contacto (nombre, apellidoPaterno, email, idProspecto, fechaAlta, fechaUltimaActualizacion, userid)
                                              VALUES(:nombre, :apellidoPaterno, :email, :idProspecto, :fechaAlta , :fechaUltimaActualizacion, :userid)";
            $parameters = array(
                ':nombre' => $_POST['QuickContact']['nombre'],
                ':apellidoPaterno' => $_POST['QuickContact']['apellidoPaterno'],
                ':email' => $_POST['QuickContact']['email'],
                ':idProspecto' => $_POST['QuickContact']['idProspecto'],
                ':fechaAlta' => date('Y/m/d H:i:s ', time()),
                ':fechaUltimaActualizacion' => date('Y/m/d H:i:s ', time()),
                ':userid'=>$_SESSION['UserID']
                );

            if (Yii::app()->db->createCommand($SQLInsert)->execute($parameters)) {
                $LastID = Yii::app()->db->getLastInsertID();

                if (!empty($_POST['QuickContact']['idProspecto'])) {
                    $ProspectoContacto = " insert into rh_crm_contactoprospecto(idProspecto, idContacto)values(:idProspecto, :idContacto)";
                    $parameters = array(':idProspecto' => $_POST['QuickContact']['idProspecto'], ':idContacto' => $LastID);
                    Yii::app()->db->createCommand($ProspectoContacto)->execute($parameters);
                }

                $NewRow = "";
                $NewRow.= "
                            <tr>
                            <td class=\" \">{$_POST['QuickContact']['nombre']}</td>
                            <td class=\" \">{$_POST['QuickContact']['apellidoPaterno']}</td>
                            <td class=\" \">{$_POST['QuickContact']['email']}</td>
                            <td class=\" \">
                                <span data-tooltip class='has-tip radius' title='Ver informacion a detalle'><a href='" . Yii::app()->createUrl('crm/contactos/view', array('id'=>$LastID)) . "' class='fi-magnifying-glass'></a></span>&nbsp;
                                <span data-tooltip class='has-tip radius' title='Editar contacto'><a href='" . Yii::app()->createUrl('crm/contactos/update', array('id'=>$LastID)) . "' class='fi-pencil'></a></span>&nbsp;
                            </td>
                            </tr>";

                echo CJSON::encode(array('requestresult' => 'ok', 'NewRow' => $NewRow, 'message' => "El Contacto se ha guardado correctamente..."));
            } else {
                echo CJSON::encode(array('requestresult' => 'fail', 'message' => "El Contacto no se pudo guardar, intente de nuevo..."));
            }
        }else{
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "El Contacto no se pudo guardar, intente de nuevo..."
                ));
        }
        return;
    }

    /*
    array(
    ['QuickContact'] =>
        array(
            ['nombre'] =>'sfdsfsd'
            ['apellidoPaterno'] =>'fdsfdsf'
            ['email'] =>'fdsfdf'
            ['idProspecto'] =>'fdfd'
        )
    )*/

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $ContactoData = Yii::app()->db->createCommand()
        ->select(
            'rh_crm_contacto.*,
            rh_crm_prospecto.nombre as prospecto,
            rh_crm_prospecto.idProspecto as id_prospecto')
        ->leftJoin('rh_crm_contactoprospecto', 'rh_crm_contacto.idContacto=rh_crm_contactoprospecto.idContacto')
        ->leftJoin('rh_crm_prospecto', 'rh_crm_contactoprospecto.idProspecto=rh_crm_prospecto.idProspecto')
        ->from(' rh_crm_contacto ')
        ->where("rh_crm_contacto.idContacto = '{$id}'")
        ->queryAll();

        $TipoActividad = Yii::app()->db->createCommand()->select('*')
        ->from('rh_tipoactividad')
        ->queryAll();

        $ListActivities = Yii::app()->db->createCommand()
        ->select('*')->from('events')
        ->where('contacto_id=' . $id)
        ->order('start asc')
        ->queryAll();

        $LeadsList=Yii::app()->db->createCommand()->select(' * ')->from('rh_crm_prospecto')->queryAll();

        FB::INFO($ListActivities, '________________________DATA');
        $this->render('view', array(
            'ContactoData' => $ContactoData[0],
            'TipoActividad' => $TipoActividad,
            'ListActivities' => $ListActivities,
            'LeadsList' =>$LeadsList
            ));
    }

    /**
     * @Todo
     * Crea Nuevo Contacto
     * @Author erasto@realhost.com.mx
     */
    public function actionCreate() {

        /* if(!empty($_POST)){
        FB::info($_POST, 'POST');
        exit;
        }
        */
        $Contacto = new Contacto;
        $now = new DateTime();

        if (isset($_POST['Contacto'])) {
            $Contacto->attributes = $_POST['Contacto'];
            $Contacto->fechaAlta = $now->format('Y-m-d H:i:s');
            $Contacto->userid=$_SESSION['UserID'];
            $Contacto->fechaUltimaActualizacion = $now->format('Y-m-d H:i:s');
            if ($Contacto->save()) {
                $LastID = Yii::app()->db->getLastInsertID();

                if (!empty($_POST['Contacto']['idProspecto'])) {
                    $ProspectoContacto = " insert into rh_crm_contactoprospecto(idProspecto, idContacto)values(:idProspecto, :idContacto)";
                    $parameters = array(':idProspecto' => $_POST['Contacto']['idProspecto'], ':idContacto' => $LastID);
                    Yii::app()->db->createCommand($ProspectoContacto)->execute($parameters);
                }

                Yii::app()->user->setFlash("success", "El Contacto se creo Correctamente. ");
                $this->redirect(array('index'));
            } else {
                Yii::app()->user->setFlash("error", "No se pudo crear el contacto, intente de nuevo. ");
            }
        }
        $ListaLeads = CHtml::listData(Lead::model()->findAll(), 'idProspecto', 'nombre');
        FB::INFO($ListaLeads, '___________________LEADS ');
        $this->render('create', array('model' => $Contacto, 'ListaLeads' => $ListaLeads));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {

        FB::INFO($_REQUEST, '___________________');
        $model = $this->loadModel($id);
        $now = new DateTime();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        // $leadid = $model->idProspecto;
        // $sql = Yii::app()->db->createCommand("SELECT CONCAT(nombre,' ',apellidoPaterno,' ',apellidoMaterno) as nombrecompleto FROM rh_crm_prospecto Where idProspecto = {$leadid}")->queryRow();
        if (isset($_POST['Contacto'])) {
            $model->attributes = $_POST['Contacto'];
            $model->fechaUltimaActualizacion = $now->format('Y-m-d H:i:s');
            if ($model->save()) $LastID = Yii::app()->db->getLastInsertID();

            if (!empty($_POST['Contacto']['idProspecto'])) {
                $ProspectoContacto = " insert into rh_crm_contactoprospecto(idProspecto, idContacto)values(:idProspecto, :idContacto)";
                $parameters = array(':idProspecto' => $_POST['Contacto']['idProspecto'], ':idContacto' => $LastID);
                Yii::app()->db->createCommand($ProspectoContacto)->execute($parameters);
            }

            $this->redirect(array('view', 'id' => $model->idContacto));
        }
        $ListaLeads = CHtml::listData(Lead::model()->findAll(), 'idProspecto', 'nombre');
        FB::INFO($ListaLeads, '___________________LEADS ');
        $this->render('update', array('model' => $model, 'lead' => $sql['nombrecompleto'], 'ListaLeads' => $ListaLeads));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        if (Yii::app()->request->isPostRequest) {
            $this->loadModel($id)->delete();
            Yii::app()->end();
        } else

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax'])) $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new Contacto('search');
        $model->unsetAttributes();

        // clear any default values
        if (isset($_GET['Contacto'])) $model->attributes = $_GET['Contacto'];

        $this->render('admin', array('model' => $model,));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Contacto the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Contacto::model()->findByPk($id);
        if ($model === null) throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Contacto $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'rh-crmcontacto-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
