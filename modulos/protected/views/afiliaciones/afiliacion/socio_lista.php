<script type="text/javascript">
    $(document).on('ready', function() {
        $('#SociosTable').dataTable( {
            "sPaginationType": "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "aLengthMenu": [
                [10,25, 50, 100, 200, -1],
                [10,25, 50, 100, 200, "All"]
            ],
            "fnInitComplete": function(){
                //$(".dataTables_wrapper select").select2({
                    //dropdownCssClass: 'noSearch'
                //});
            }
        });

        $("#colstatus").removeAttr( 'style' );
        $("select[name='SociosTable_length']").addClass('span2');
        $("select[name='SociosTable_length']").css({"height":"0px", "margin-top":"0px"});

        $("#CartaBienvenida").click(function(event) {

            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/SendMail"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      SendMail:{
                          Folio: '<?=$_POST['Folio']?>',
                          Tipo: 'CartaBienvenida'
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        //OpenInNewTab("<?php echo $this->createUrl("afiliaciones/bienvenidapdf"); ?>");
                    }else{
                        displayNotify('fail', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });

    function EditSocio(DebtorNo,BranchCode,Folio){
        $('#ModalLabelEdit').text('Editar Socio N° ' + BranchCode + ' Folio ' + Folio);
        $('#Modal_EditSocio').modal('show');
        LoadForm(DebtorNo,BranchCode);
    }

    function ViewSocio(DebtorNo,BranchCode,Folio){
        $('#ModalLabelView').text('Detalle del Socio N° ' + BranchCode + ' Folio ' + Folio);
        $('#Modal_ViewSocio').modal('show');
        GetSocioData(DebtorNo,BranchCode);
    }

    function SuspenderSocio(DebtorNo,BranchCode,folio){
        var DebtorNo = $(this).attr('DebtoNo');
        var BranchCode = $(this).attr('BranchCode');
        $('#ModalLabelSuspension').text('Suspender Socio N° ' + BranchCode + ' Folio ' + folio);
        $('#Sus_BranchCode').val(BranchCode);
        $('#Sus_DebtorNo').val(DebtorNo);
        $('#Modal_SuspenderSocio').modal('show');
    }

    function ActivarSocio(DebtorNo,BranchCode,Folio){

        if (confirm('¿Desea Agregar este Socio?')) {
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/ActivarSocio"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Activar:{
                          BranchCode: BranchCode,
                          DebtorNo: DebtorNo,
                          Folio: Folio,
                          Tipo: "NuevosSocios"
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#SociosAgregados').show();
                        $('#SociosAgregados').text(data.message2);
                        $('#btnSaveNews').html(data.btnSaveNews);
                        $('#TotalFacturar').val(data.CostoTotal);
                        $('#SociosTable #' + data.BranchCode).html(data.NewRow);
                        $('#SociosTable #' + data.BranchCode).removeClass('precapturado');
                        $("#SociosTable #" + data.BranchCode + " #Change_Status option[value='Activo']").attr("selected",true);
                        $('#FN_TipoFactura').val(data.TipoFactura);
                        $('#FNtarifa_total').val(data.CostoTodosSocios);
                    }else{
                        displayNotify('error', data.message);
                    }
                },
                error : ajaxError
            });
        }
    }

    function OpenInNewTab(url ){
        var win = window.open(url, '_blank');
        win.focus();
    }

    function EliminarSocio(DebtorNo,BranchCode,Folio){

        if (confirm('¿Desea Eliminar este Socio?')) {
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/EliminarSocio"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Activar:{
                          BranchCode: BranchCode,
                          DebtorNo: DebtorNo,
                          Folio: Folio
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#SociosAgregados').show();
                        $('#SociosAgregados').text(data.message2);
                        $('#btnSaveNews').html(data.btnSaveNews);
                        $('#TotalFacturar').val(data.CostoTotal);
                        $('#SociosTable #' + data.BranchCode).remove();
                        //$('#SociosTable #' + data.BranchCode).html(data.NewRow);
                        //$('#SociosTable #' + data.BranchCode).removeClass('precapturado');
                    }else{
                        displayNotify('fail', data.message);
                    }
                },
                error : ajaxError
            });
        }
    }


    function ChangeStatus(Action,DebtorNo,BranchCode,folio){
        switch(Action){
            case 'Activo':
                if (confirm('Esta seguro de Reactivar el Socio: ' + BranchCode)) {
                    var jqxhr = $.ajax({
                        url: "<?php echo $this->createUrl("afiliaciones/ActivarSocio"); ?>",
                        type: "POST",
                        dataType : "json",
                        timeout : (120 * 1000),
                        data: {
                              Activar:{
                                  BranchCode: BranchCode,
                                  DebtorNo: DebtorNo,
                                  Folio: folio,
                                  Tipo: "ReactivacionSocio"
                              },
                        },
                        success : function(data, newValue) {
                            if (data.requestresult == 'ok') {
                                displayNotify('success', data.message);
                                $('#SociosAgregados').show();
                                $('#SociosAgregados').text(data.message2);
                                $('#btnSaveNews').html(data.btnSaveNews);
                                $('#TotalFacturar').val(data.CostoTotal);
                                $('#SociosTable #' + data.BranchCode).html(data.NewRow);
                                $('#SociosTable #' + data.BranchCode).removeClass('precapturado');
                                $('#SociosTable #' + data.BranchCode).removeClass('warning');
                                $('#SociosTable #' + data.BranchCode).removeClass('danger');
                                $("#SociosTable #" + data.BranchCode + " #Change_Status option[value='Activo']").attr("selected",true);
                                $('#FN_TipoFactura').val(data.TipoFactura);
                                $('#FNtarifa_total').val(data.CostoTodosSocios);
                            }else{
                                displayNotify('error', data.message);
                            }
                        },
                        error : ajaxError
                    });
                } else {
                    // Do nothing!
                };
            break;
            case 'Cancelado':
                $('#ModalLabelCancelacion').text('Cancelar Socio N° ' + DebtorNo + ' Folio ' + folio);
                $('#Cancel_BranchCode').val(BranchCode);
                $('#Cancel_DebtorNo').val(DebtorNo);
                $('#Modal_CancelarSocio').modal('show');
                $('#Motivo_Cancelacion').select2();
            break;
            case 'Suspendido':
                $('#ModalLabelSuspension').text('Suspender Socio N° ' + DebtorNo + ' Folio ' + folio);
                $('#Sus_BranchCode').val(BranchCode);
                $('#Sus_DebtorNo').val(DebtorNo);
                $('#Modal_SuspenderSocio').modal('show');
            break;
            default:
            break;
        }
    }

</script>

<style>
    .table thead th {
        font-size: 12px;
        line-height: 20px;
    }

    .table td {
        font-size: 10px;
    }

    .dataTables_filter input {
        margin-bottom: 0 !important;
    }

    .select2-container {
        margin: 15px 0;
        min-width: 200px;
        vertical-align: top;
    }

    #Change_Status {
       /*margin-bottom: 0 !important;*/
       width: 120px;
    }

    #colstatus {
        width: 100px; !important;
    }

    #Search{
        margin-bottom: 0px;
        height: 35px;
    }

    input[type="text"]{
        /*margin-bottom: -20px;*/
    }

</style>
<?php include_once('modals/EditSocio.modal.php'); ?>
<?php include_once('modals/ViewSocio.modal.php'); ?>
<?php include_once('modals/SuspenderSocio.modal.php'); ?>
<?php include_once('modals/CancelarSocio.modal.php'); ?>
<?php include_once('modals/ChangeStatus.modal.php'); ?>
<?php include_once('modals/ReactivarSocio.modal.php'); ?>
<?php include_once("modals/Emergencia_Contacto.modal.php"); ?>


<input type="button" id="Emergencia_Contacto" value="Llamar en Caso de Emergencia" class="btn btn-success" >
<?php //if($_POST['cobro_inscripcion'] == 1)
    { ?>
    <input type="button" id="CartaBienvenida" value="Carta de Bienvenida" class="btn btn-success" >
<?php } ?>
<div style="height: 10px;" ></div>
<?php 

/*
$debtorn = $GetSocios['debtorno'];
echo $Socio['debtorno'];
echo $debtorn;
 echo $ultimoregistro = "SELECT max(id) FROM rh_movimientos_afiliacion 
                WHERE debtorno = '$GetSocios'";
        $resultimoregistro = DB_query($ultimoregistro, $db);
        $id_ult_registro = DB_fetch_assoc($resultimoregistro);
        //echo "<pre>";print_r($id_ult_registro);exit();
        echo "Hola mundo";exit();
        echo $id_ult_registro;

*/
        
?>
<table id="SociosTable" class="table table-bordered" style="width: 100%;" >
    <thead>
        <tr>
            <th>N° Socio</th>
            <th>Nombre</th>
            <th>Sexo</th>
            <th>Nombre Comercial</th>
            <th>Fecha Nacimiento</th>
            <th>Calle</th>
            <th>N°</th>
            <th>Telefono</th>
            <th>Fecha Ingreso</th>
            <th>Fecha Canc/Susp...</th>
            <th>Cancelado por.</th>
            <th id="colstatus">Status</th>
            <th style="text-align: center; width: 60px;"><i class="icon-cog" title="Actions"></i></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($GetSocios as $Socio){
            FB::INFO($Socio,'____SOCIOS');
        if($Socio['movimientos_socios'] =='Suspendido'){
            $Class = "class= 'warning'";
        }elseif($Socio['movimientos_socios'] =='Cancelado'){
            $Class = "class= 'danger'";
        }else{
            $Class = "";
        }

        if(($Socio['rh_status_captura'] == 'Precapturado') && ($Socio['movimientos_socios'] == 'Nuevo')){
            $Class = "class= 'precapturado'";
            // Se agrego para mostrar mensaje al usuario de que no a Activado el nuevo Socio Angeles Perez 23-05-2016  
            Yii::app()->user->setFlash("error", "EL NUEVO SOCIO AUN NO ESTA ACTIVADO.");
            // Termina
        }

        /*
        COMENTADO POR DANIEL VILLARREAL EL 5 DE DICIEMBRE, A PETICION DE ALICIA VILLARREAL
        if(
            ($Socio['rh_status_captura'] == 'Facturar') &&
             (
                ($Socio['movimientos_socios'] == 'Nuevo') || ($Socio['movimientos_socios'] == 'Cancelado') || ($Socio['movimientos_socios'] == 'Suspendido')
             )
          )
         {
            $Class = "";
            $Socio['movimientos_socios'] = "Activo";
        }*/

        ?>
        <tr <?=$Class?> id="<?=$Socio['branchcode']?>" >
            <td><?=$Socio['branchcode']?></td>
            <td><?=$Socio['brname']?></td>
            <td><?=$Socio['sexo']?></td>
            <td><?=$Socio['nombre_empresa']?></td>
            <td><?=$Socio['fecha_nacimiento']?></td>
            <td><?=$Socio['braddress1']?></td>
            <td><?=$Socio['braddress2']?></td>
            <td><?=$Socio['phoneno']?></td>
            <td><?=$Socio['fecha_ingreso']?></td>
            <td><?=$Socio['fecha_baja']?></td>
            <td><?=$Cancelpor['userid'] ?></td>
            <td>
                <?php if(($_SESSION['rh_AdminAfil'] == 1) && ($Socio['movimientos_socios'] != 'Nuevo')){ ?>
                <select id="Change_Status" name="Change_Status" BranchCode="<?=$Socio['branchcode']?>" DebtorNo="<?=$Socio['debtorno']?>" onchange="ChangeStatus(this.value,<?=$Socio['debtorno']?>,<?=$Socio['branchcode']?>,<?=$Socio['folio']?>)" >
                    <option SELECTED="SELECTED" value="<?=$Socio['movimientos_socios']?>"><?=$Socio['movimientos_socios']?></option>
                    <?php if($_POST['movimientos_afiliacion'] == "Activo"){ ?>
                        <option value="Activo">Activo</option>
                    <?php } ?>
                    <option value="Cancelado">Cancelado</option>
                    <option value="Suspendido">Suspendido</option>
                </select>
                <?php }else{
                    echo $Socio['movimientos_socios'];
                } ?>
            </td>
            <td>
                <a id="ViewSocio"   name = "ViewSocio"   BranchCode="<?=$Socio['branchcode']?>" DebtorNo="<?=$Socio['debtorno']?>" title="Detalles" onclick="ViewSocio('<?=$Socio['debtorno']?>',<?=$Socio['branchcode']?>,<?=$Socio['folio']?>)" ><i class="icon-eye-open"></i></a>&nbsp;
                <a id="EditSocio"   name = "EditSocio"   BranchCode="<?=$Socio['branchcode']?>" DebtorNo="<?=$Socio['debtorno']?>" title="Editar Socio" onclick="EditSocio(<?=$Socio['debtorno']?>,<?=$Socio['branchcode']?>,<?=$Socio['folio']?>)" ><i class="icon-edit"></i></a>&nbsp;
                <?php if(($Socio['rh_status_captura'] == 'Precapturado') && ($Socio['movimientos_socios'] == 'Nuevo')){ ?>
                    <a id="ActivarSocio" name = "ActivarSocio" BranchCode="<?=$Socio['branchcode']?>" DebtoNo="<?=$Socio['debtorno']?>" title="Activar Socio" onclick="ActivarSocio(<?=$Socio['debtorno']?>,<?=$Socio['branchcode']?>,<?=$Socio['folio']?>)" ><i class="icon-check"></i></a>
                    <a id="EliminarSocio" name = "EliminarSocio" BranchCode="<?=$Socio['branchcode']?>" DebtoNo="<?=$Socio['debtorno']?>" title="Eliminar Socio" onclick="EliminarSocio(<?=$Socio['debtorno']?>,<?=$Socio['branchcode']?>,<?=$Socio['folio']?>)" ><i class="icon-remove"></i></a>
                <?php } ?>
            </td>
        </tr>
<?php //echo "<pre>";print_r($GetSocios[1]);exit();   ?>


        <?php } ?>
    </tbody>
</table>
