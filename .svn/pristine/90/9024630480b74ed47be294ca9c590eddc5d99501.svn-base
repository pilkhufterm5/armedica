<?php /*  */
global $rootpath;
$URI=$_SERVER["UrlERP_BASE"];
$rootpath=rtrim($URI,"/");
$theme= $_SESSION['Theme'];
$title=CHtml::encode(Yii::app()->name." ");
$ExternalLayout = true;
include($_SERVER['LocalERP_path'].'/includes/header.inc');
?>

<!DOCTYPE html> <!--[if IE 9]>
<html class="lt-ie10" lang="en" >
    <![endif]-->
    <html class="no-js" lang="en" >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $this->pageTitle='CRM'; ?></title>
        <!-- If you are using CSS version, only link these 2 files, you may add app.css to use for your overrides if you like. -->
        <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/css/foundation.css">
        <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/foundation-icons/foundation-icons.css">

        <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/datatables/jquery.dataTables.css">
        <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/datatables/dataTables.responsive.css">
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/datatables/rhViewDataTable.css">
        <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/css/jwn-style.css">
        <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/css/jquery.datetimepicker.css">

        <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/css/fullcalendar.print.css">
        <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/css/fullcalendar.css">
        <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/css/select2.css">
        <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/css/jquery-ui.css">
        <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/css/normalize.css">

        <script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/vendor/modernizr.js"></script>

        <script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/vendor/jquery.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/foundation.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/foundation.min.js"></script>

        <script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/fullcalendar/lib/moment.min.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/fullcalendar/fullcalendar.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/fullcalendar/lang-all.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/foundation.abide.js"></script>

         <script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/jquery-ui.js"></script>
         <script src="<?php echo Yii::app()->request->baseUrl; ?>/themes/found/js/jquery-migrate-1.0.0.js"></script>
         <script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/blockUi.js"></script>

        <script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/noty.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/configuration.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/jquery.datetimepicker.js"></script>
        <script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/select2.js"></script>

        <script type="text/javascript" language="javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/datatables/jquery.dataTables.min.js"></script>
        <!--<script type="text/javascript" language="javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/datatables/dataTables.js"></script> -->
        <!--<script type="text/javascript" language="javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/datatables/dataTables.responsive.min.js"></script>-->

    <style>
        .main-section{
            margin: 1.5rem;
        }
        .even{
            background-color:#F1F1F1 !important;
        }
    </style>

    </head>

    <body>
         <!-- body content here -->
        <div class="off-canvas-wrap" data-offcanvas>
            <div class="inner-wrap">
                <nav class="tab-bar">
                    <section class="left-small"> <a class="left-off-canvas-toggle menu-icon" href="#"><span></span></a> </section>
                    <section class="middle tab-bar-section">
                        <h1 class="title"><a class="fi-first-aid size24" style="color: blue;"></a> &nbsp;CRM AR Medica</h1>
                    </section>
                </nav>
                <aside class="left-off-canvas-menu">
                    <ul class="off-canvas-list">
                        <li><label>CUENTA</label></li>
                        <li><a href='<?php echo Yii::app()->createUrl("crm/leads/index");?>'  class="fi-torso">&nbsp;Prospectos</a></li>
                        <li><a href='<?php echo Yii::app()->createUrl("crm/contactos/index");?>' class="fi-torsos">&nbsp;Contactos</a></li>
                        <li><a href='<?php echo Yii::app()->createUrl("crm/cuentas");?>'  class="fi-torso">&nbsp;Cuentas</a></li>
                        <li><a href='<?php echo Yii::app()->createUrl("crm/oportunidades/index");?>' class="fi-widget">&nbsp;Oportunidades</a></li>
                        <!-- <li><a href='<?php echo Yii::app()->createUrl("crm/orders");?>'  class="fi-torso">&nbsp;Cotizaciones</a></li> -->
                        <li><a href='<?php echo Yii::app()->createUrl("crm/activities/calendar");?>' class="fi-calendar">&nbsp;Calendario</a></li>
                        <li><a href='<?php echo Yii::app()->createUrl("crm/kanban/index");?>' class="fi-calendar">&nbsp;Kanban</a></li>
                        <li><a href='<?php echo Yii::app()->createUrl("crm/source");?>' class="fi-widget">&nbsp;Origen del prospecto</a></li>
                        <li><a href='<?php echo Yii::app()->createUrl("crm/fases_venta");?>' class="fi-widget">&nbsp;Fases de Venta</a></li>
                        <li><a href='<?php echo Yii::app()->createUrl("crm/tipoactividad");?>' class="fi-widget">&nbsp;Tipo de Actividad</a></li>
                        <!-- <li><a href="#" class="fi-unlock">Logout</a></li-->
                    </ul>
                </aside>

                <?php if(($msgs=Yii::app()->user->getFlashes())!==null and $msgs!==array()):?>
                    <?php foreach($msgs as $type => $message):?>
                        <div data-alert class="alert-box <?php echo $type?> radius row" _style="max-width: ;">
                            <!-- <h6><?php //echo ucfirst($type)?>!</h6> -->
                            <?php echo $message?>
                            <a href="#" class="close">&times;</a>
                        </div>
                    <?php endforeach;?>
                <?php endif;?>

                <section class="main-section">
                    <div id="notify-container"></div><!-- Notificaciones para llamadas Ajax -->
                    <!-- content goes here -->
                     <?php echo $content; ?>
                </section>
                <a class="exit-off-canvas"></a>
            </div>
        </div>

        <script type="text/javascript">
            $(document).on('ready',function() {
                $(document).foundation();
            });
                function emailValido(email){
                    //alert(email);
                    if((email == '') || email == null){
                        displayNotify('error', 'El e-mail no es valido...');
                        return false;
                    }
                    emailExpression =email;
                    if(emailExpression.match(/^[a-z][\w.-]+@\w[\w.-]+\.[\w.-]*[a-z][a-z]$/i)){
                        return true;
                    }else{
                        displayNotify('error', 'El e-mail no es valido...');
                        return false;
                    }
                }

                function vRfcs(taxref){
            var rfc = taxref.val();
            if(!rfc){
                taxref.focus()
                displayNotify('error', 'El campo RFC es obligatorio');
                return false
            }

            if($('#tipopersona').val() == 'FISICA'){
                var pf = $('#tipopersona').val();
            }

            if($('#tipopersona').val() == 'MORAL'){
                var pf = $('#tipopersona').val();
            }

            if(!($('#tipopersona').val())){
                displayNotify('error', 'Debe seleccionar un tipo de Persona');
                return false
            }

            if($('#tipopersona').val() == 'MORAL'){
                if(!rfc.match(/^[A-ZÑ&]{3}(\d\d(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01]))[0-9A-ZÑ]{3}$/)){
                    displayNotify('error', 'El RFC no es un RFC valido para Persona Moral');
                    return false
                }
            }

            if($('#tipopersona').val() == 'FISICA'){  //VECJ880326XXX
                if(rfc == 'XAXX010101000' || rfc == 'XEXX010101000')
                    return true
                if(!rfc.match(/^[A-ZÑ&]{4}(\d\d(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01]))[0-9A-ZÑ]{3}$/)){
                    displayNotify('error', 'El RFC no es un RFC valido para Persona Fisica');
                    return false
                }
            }
            return true
        }

        </script>

      </body>
   </html>
