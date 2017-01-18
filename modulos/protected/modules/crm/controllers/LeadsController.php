<?php
class LeadsController extends Controller
{

    public $layout = 'webroot.themes.found.views.layouts.main';

    /**
     * @Todo
     * Lista Prospectos
     * @Author erasto@realhost.com.mx
     */
    public function actionIndex() {

        $LeadsData = Yii::app()->db->createCommand()->select(' * ')->from('rh_crm_prospecto')->queryAll();
        FB::INFO($LeadsData, '______DATA');
        $this->render('index', array(
            'LeadsData' => $LeadsData
        ));
    }

    /**
     * @Todo
     * Crea Nuevo Prospecto
     * Automaticamente crea un contacto ligado al prospecto creado
     * @Author erasto@realhost.com.mx
     */
    public function actionCreate() {
        $Lead = new Lead;
        $LeadSource=Yii::app()->db->createCommand()
            ->select('*')
            ->from('rh_crm_leadsource')
            ->where('status=1')
            ->queryAll();

        if (!empty($_POST['Lead']['nombre'])) {

            $Lead->attributes = $_POST['Lead'];
            $Lead->source_id=$_POST['source_id'];
            $Lead->fechaAlta = date('Y-m-d H:i:s');
            $Lead->fechaUltimaActualizacion = date('Y-m-d H:i:s');
            if ($Lead->save()) {
                $LastLeadID = Yii::app()->db->getLastInsertID();
                $CrearContacto = "insert into rh_crm_contacto (
                    nombre,
                     apellidoPaterno,
                     apellidoMaterno,
                     fechaAlta,
                     fechaUltimaActualizacion,
                     email,
                     telefono,
                     celular,
                     contactarPorCelular,
                     contactarPorEmail,
                     direccion1,
                     direccion2,
                     direccion3,
                     direccion4,
                     direccion5,
                     direccion6,
                     direccion7,
                     direccion8,
                     direccion9,
                     direccion10,
                     codigoPostal,
                     fkPais,
                     descripcion,
                     facebook,
                     twitter,
                     googlePlus,
                     estatus,
                     linkedin,
                     skype,
                     contactoWeb,
                     userid
                     ) values (
                     :nombre,
                     :apellidoPaterno,
                     :apellidoMaterno,
                     :fechaAlta,
                     :fechaUltimaActualizacion,
                     :email,
                     :telefono,
                     :celular,
                     :contactarPorCelular,
                     :contactarPorEmail,
                     :direccion1,
                     :direccion2,
                     :direccion3,
                     :direccion4,
                     :direccion5,
                     :direccion6,
                     :direccion7,
                     :direccion8,
                     :direccion9,
                     :direccion10,
                     :codigoPostal,
                     :fkPais,
                     :descripcion,
                     :facebook,
                     :twitter,
                     :googlePlus,
                     :estatus,
                     :linkedin,
                     :skype,
                     :contactoWeb,
                     :userid
                      )";

                $ContactParameters = array(
                    ':nombre' => $_POST['Lead']['nombre'],
                    ':apellidoPaterno' => $_POST['Lead']['apellidoPaterno'],
                    ':apellidoMaterno' => $_POST['Lead']['apellidoMaterno'],
                    ':fechaAlta' => date('Y-m-d H:i:s') ,
                    ':fechaUltimaActualizacion' => date('Y-m-d H:i:s') ,
                    ':email' => $_POST['Lead']['email'],
                    ':telefono' => $_POST['Lead']['telefono'],
                    ':celular' => $_POST['Lead']['celular'],
                    ':contactarPorCelular' => $_POST['Lead']['contactarPorCelular'],
                    ':contactarPorEmail' => $_POST['Lead']['contactarPorEmail'],
                    ':direccion1' => $_POST['Lead']['direccion1'],
                    ':direccion2' => $_POST['Lead']['direccion2'],
                    ':direccion3' => $_POST['Lead']['direccion3'],
                    ':direccion4' => $_POST['Lead']['direccion4'],
                    ':direccion5' => $_POST['Lead']['direccion5'],
                    ':direccion6' => $_POST['Lead']['direccion6'],
                    ':direccion7' => $_POST['Lead']['direccion7'],
                    ':direccion8' => $_POST['Lead']['direccion8'],
                    ':direccion9' => $_POST['Lead']['direccion9'],
                    ':direccion10' => $_POST['Lead']['direccion10'],
                    ':codigoPostal' => $_POST['Lead']['codigoPostal'],
                    ':fkPais' => $_POST['Lead']['fkPais'],
                    ':descripcion' => $_POST['Lead']['descripcion'],
                    ':facebook' => $_POST['Lead']['facebook'],
                    ':twitter' => $_POST['Lead']['twitter'],
                    ':googlePlus' => $_POST['Lead']['googlePlus'],
                    ':estatus' => $_POST['Lead']['estatus'],
                    ':linkedin' => $_POST['Lead']['linkedin'],
                    ':skype' => $_POST['Lead']['skype'],
                    ':contactoWeb' => $_POST['Lead']['contactoWeb'],
                    ':userid' => $_SESSION['UserID']
                );

                if (Yii::app()->db->createCommand($CrearContacto)->execute($ContactParameters)) {

                    $LastContactID = Yii::app()->db->getLastInsertID();

                    $ContactoProspecto = "insert into rh_crm_contactoprospecto (idContacto, idProspecto) values (:idContacto, :idProspecto)";

                    $ContactoProspectoParameters = array(
                        'idContacto' => $LastContactID,
                        'idProspecto' => $LastLeadID
                    );

                    if (Yii::app()->db->createCommand($ContactoProspecto)->execute($ContactoProspectoParameters)) {

                        $LeadLog = "insert into events (title, prospecto_id, tipo_log, userid, Descripcion, start, end)
                              values(:title, :prospecto_id, :tipo_log, :userid, :Descripcion, :start, :end)";
                        $parameters = array(
                            ':title' => 'Update',
                            ':prospecto_id' => $LastLeadID,
                            ':tipo_log' => 'Update',
                            ':userid' => $_SESSION['UserID'],
                            ':Descripcion' => 'Se creó el prospecto',
                            ':start' => date('Y-m-d H:i:s') ,
                            ':end' => date('Y-m-d H:i:s')
                        );

                        if (Yii::app()->db->createCommand($LeadLog)->execute($parameters)) {
                            Yii::app()->user->setFlash("success", "El log del prospecto se actualizó correctamente. ");
                            $this->redirect($this->createUrl("leads/view", array('id' => $LastLeadID)));
                        } else {
                            Yii::app()->user->setFlash("fail", "No se pudo actuzalizar el log del prospecto, intente de nuevo. ");
                        }
                    }
                } else {
                    Yii::app()->user->setFlash("fail", "No se pudo crear el Contacto. Debera crear el contacto manualmente. ");
                    $this->redirect($this->createUrl("leads/view", array(
                        'id' => $LastID
                    )));
                }
            } else {
                Yii::app()->user->setFlash("fail", "No se pudo crear el Prospecto, intente de nuevo. ");
            }
        }
        $SourceList = CHtml::listData($LeadSource, 'id', 'nombre');
        $this->render('create', array(
            'model' => $Lead,
            'SourceList'=>$SourceList
        ));
    }

    public function actionView($id) {
        $ProspectoData = Yii::app()->db->createCommand()
            ->select('rh_crm_prospecto.*, rh_crm_leadsource.nombre as source')
            ->from('rh_crm_prospecto')
            ->leftJoin('rh_crm_leadsource', 'rh_crm_leadsource.id=rh_crm_prospecto.source_id')
            ->where('idProspecto =' . $id)
            ->queryAll();
        $TipoActividad = Yii::app()->db->createCommand()->select('*')->from('rh_tipoactividad')->queryAll();
        $WhereActivities = "prospecto_id='" . $id . "' and tipo_log = 'Calendario'";
        $WhereUpdates = "prospecto_id='" . $id . "' and tipo_log = 'Update'";
        $ListActivities = Yii::app()->db->createCommand()->select('*')->from('events')->where($WhereActivities)->order('start desc')->queryAll();
        $ListUpdates = Yii::app()->db->createCommand()->select('*')->from('events')->where($WhereUpdates)->order('start desc')->queryAll();
        $ListaContactos = Yii::app()->db->createCommand()->select('rh_crm_contacto.*, rh_crm_contactoprospecto.*')->from('rh_crm_contactoprospecto')->leftJoin('rh_crm_contacto', 'rh_crm_contacto.idContacto=rh_crm_contactoprospecto.idContacto')->where('rh_crm_contactoprospecto.idProspecto=' . $id)->queryAll();
        $Fases_Venta = Yii::app()->db->createCommand()->select('*')->from('rh_crm_fases_venta')->where('status=1')->order('orden')->queryAll();

        /*$ListActivities = Yii::app()->db->createCommand()->select('*')->from('events')->where('contacto_id=' . $id)->queryAll();*/

        $this->render('view', array(
            'ProspectoData' => $ProspectoData[0],
            'ListActivities' => $ListActivities,
            'ListaContactos' => $ListaContactos,
            'Fases_Venta' => $Fases_Venta,
            'TipoActividad' => $TipoActividad,
            'ListUpdates' => $ListUpdates
        ));
    }

    /**
     * @Todo
     * Crea Prospecto Rapido
     * @Author erasto@realhost.com.mx
     */
    public function actionQuicklead() {

        FB::INFO($_POST, '_____________________________________POST');
        if (!empty($_POST['QuickLead']['email'])) {
            $model = new Lead;

            $model->nombre = $_POST['QuickLead']['nombre'];
            $model->apellidoPaterno = $_POST['QuickLead']['apellidoPaterno'];
            $model->email = $_POST['QuickLead']['email'];
            $model->estatus = $_POST['QuickLead']['estatus'];
            $model->status_prospecto = $_POST['QuickLead']['status_prospecto'];
            $model->fechaAlta = date('Y-m-d H:i:s');
            $model->fechaUltimaActualizacion = date('Y-m-d H:i:s');

            if ($model->save()) {
                $LastLeadID = Yii::app()->db->getLastInsertID();

                $CreateQuickContact = "insert into rh_crm_contacto(
                    nombre,
                    apellidoPaterno,
                    email,
                    estatus,
                    fechaAlta,
                    fechaUltimaActualizacion)
                    values(
                    :nombre,
                    :apellidoPaterno,
                    :email,
                    :estatus,
                    :fechaAlta,
                    :fechaUltimaActualizacion
                    )";

                $QuickContactParameters = array(
                    ':nombre' => $_POST['QuickLead']['nombre'],
                    ':apellidoPaterno' => $_POST['QuickLead']['apellidoPaterno'],
                    ':email' => $_POST['QuickLead']['email'],
                    ':estatus' => $_POST['QuickLead']['estatus'],
                    ':fechaAlta' => date('Y/m/d H:i:s') ,
                    ':fechaUltimaActualizacion' => date('Y/m/d H:i:s')
                );

                FB::INFO($QuickContactParameters);

                if (Yii::app()->db->createCommand($CreateQuickContact)->execute($QuickContactParameters)) {
                    $LastContactID = Yii::app()->db->getLastInsertID();
                    $ProspectoContacto = " insert into rh_crm_contactoprospecto(idProspecto, idContacto)values(:idProspecto, :idContacto)";
                    $parameters = array(
                        ':idProspecto' => $LastLeadID,
                        ':idContacto' => $LastContactID
                    );

                    if (Yii::app()->db->createCommand($ProspectoContacto)->execute($parameters)) {
                        $LeadLog = "insert into events (title, prospecto_id, tipo_log, userid, Descripcion, start, end)
                              values(:title, :prospecto_id, :tipo_log, :userid, :Descripcion, :start, :end)";
                        $parameters = array(
                            ':title' => 'Update',
                            ':prospecto_id' => $LastLeadID,
                            ':tipo_log' => 'Update',
                            ':userid' => $_SESSION['UserID'],
                            ':Descripcion' => 'Se creó el prospecto',
                            ':start' => date('Y-m-d H:i:s') ,
                            ':end' => date('Y-m-d H:i:s')
                        );

                        Yii::app()->db->createCommand($LeadLog)->execute($parameters);
                    }
                }
                $NewRow = "
                <tr>
                    <td class=\" \">{$_POST['QuickLead']['nombre']}</td>
                    <td class=\" \">{$_POST['QuickLead']['apellidoPaterno']}</td>
                    <td class=\" \">{$_POST['QuickLead']['email']}</td>
                    <td>{$_POST['QuickLead']['status_prospecto']}</td>
                    <td>PROSPECTO</td>
                    <td class=\" \">
                        <span data-tooltip class='has-tip radius' title='Ver informacion a detalle'><a href='" . Yii::app()->createUrl('crm/leads/view', array('id'=>$LastLeadID)) . "' class='fi-magnifying-glass'></a></span>&nbsp;
                        <span data-tooltip class='has-tip radius' title='Editar contacto'><a href='" . Yii::app()->createUrl('crm/leads/update', array('id'=>$LastLeadID)) . "' class='fi-pencil'></a></span>&nbsp;
                    </td>
                </tr>";
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El Prospecto se ha guardado correctamente...",
                    'NewRow' => $NewRow
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "El Prospecto no se pudo guardar, intente de nuevo..."
                ));
            }
            return;
        }
    }

    public function actionUpdate($id) {

        $model = Lead::model()->findByPk($id);
        $now = new DateTime();

        FB::INFO($model, '_______________________________model');
        FB::INFO($_POST, '_______________________________model');

        // Cargar el contacto al que esta manejando al lead
        if (!empty($_POST['Lead'])) {
            $model->attributes = $_POST['Lead'];
            $model->fechaUltimaActualizacion = date('Y-m-d H:i:s');

            $Where = "idProspecto ='" . $model->idProspecto . "'";
            $contact = Yii::app()->db->createCommand()->select('*')->from('rh_crm_contactoprospecto')->where($Where)->queryAll();

            if ($model->save()) {

                $LogUpdate = "insert into events (title, prospecto_id, tipo_log, userid, Descripcion, start, end)
                          values(:title, :prospecto_id, :tipo_log, :userid, :Descripcion, :start, :end)";

                $parameters = array(
                    ':title' => 'Update',
                    ':prospecto_id' => $id,
                    ':tipo_log' => 'Update',
                    ':userid' => $_SESSION['UserID'],
                    ':Descripcion' => 'Se editó el prospecto',
                    ':start' => date('Y-m-d H:i:s') ,
                    ':end' => date('Y-m-d H:i:s') ,
                );

                if (Yii::app()->db->createCommand($LogUpdate)->execute($parameters)) {
                    Yii::app()->user->setFlash("success", "El Prospecto se editó Correctamente. ");
                }
            }
        }


        $LeadSource=Yii::app()->db->createCommand()
            ->select('*')
            ->from('rh_crm_leadsource')
            ->where('status=1')
            ->queryAll();
        $SourceList = CHtml::listData($LeadSource, 'id', 'nombre');

        $this->render('update', array(
            'model' => $model,
            'nombrecompleto' => $nombrecompleto,
            'SourceList' => $SourceList
        ));
    }

    public function actioncambiarFase() {

        if (!empty($_POST['Fase'])) {

            $UpdateFase = "update rh_crm_prospecto set id_fase_venta = :id_fase_venta where idProspecto=:prospecto_id";

            $parameters = array(
                ':id_fase_venta' => $_POST['Fase']['id_fase_venta'],
                ':prospecto_id' => $_POST['Fase']['prospecto_id']
            );

            if (Yii::app()->db->createCommand($UpdateFase)->execute($parameters)) {

                $LogUpdate = "insert into events (title, prospecto_id, tipo_log, userid, Descripcion, start, end)
                    values(:title, :prospecto_id, :tipo_log, :userid, :Descripcion, :start, :end)";

                $date = date('Y-m-d H:i:s');

                $parameters = array(
                    ':title' => $_POST['Fase']['title'],
                    ':prospecto_id' => $_POST['Fase']['prospecto_id'],
                    ':tipo_log' => $_POST['Fase']['tipo_log'],
                    ':userid' => $_SESSION['UserID'],
                    ':Descripcion' => $_POST['Fase']['descripcion'],
                    ':start' => $date,
                    ':end' => $date
                );

                if (Yii::app()->db->createCommand($LogUpdate)->execute($parameters)) {

                    $NewUpdate = "
                        <div class='panel2 callout radius' style='margin-bottom: 5px;' >
                            <h4 style='margin-top: -5px;'><small>{$_SESSION['UserID']}</small></h4>
                            <p style='font-size:10px; margin-bottom:5px;'>{$_POST['Fase']['descripcion']}</p>
                            <p style='font-size:10px; margin-bottom:5px;'>{$date}</p>
                        </div>";
                }
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => 'La fase de venta para el prospecto ' . $_POST['Fase']['nombre_prospecto'] . ' ha cambiado a: ' . $_POST['Fase']['nombre_fase'] . '',
                    'CurrentFase' => $_POST['Fase']['id_fase_venta'],
                    'LastFase' => $_POST['Fase']['CurrentFase'],
                    'NewUpdate' => $NewUpdate
                ));
            }else{
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => 'No se realizo ningun Cambio, intente de nuevo.'
                ));
            }
        }
        return;
    }

    // public function actionCuentas() {

    //     $Where = "tipo='CUENTA'";
    //     $LeadsData = Yii::app()->db->createCommand()->select(' * ')->from('rh_crm_prospecto')->where($Where)->queryAll();
    //     FB::INFO($LeadsData, '______DATA');
    //     $this->render('cuentas', array(
    //         'LeadsData' => $LeadsData
    //     ));
    // }
}
