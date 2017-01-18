<script type="text/javascript">
    $(document).on('ready',function() {

        <?php
        if($_POST['StatusFactura'] == "Programada"){ ?>
            $('#Cron').show();
        <?php }else{ ?>
            $('#Cron').hide();
        <?php } ?>

        $('#CustomerInquiry').dataTable( {
            "sPaginationType": "bootstrap",
            "aLengthMenu": [
                [10,25, 50, 100, 200, -1],
                [10,25, 50, 100, 200, "Todo"]
            ],
            "sDom": 'T<"clear">lfrtip',
            "oTableTools": {
                "aButtons": [{
                    "sExtends": "collection",
                    "sButtonText": "Exportar",
                    "aButtons": [ "print", "csv", "xls", "pdf" ]
                }]
            },
            "fnInitComplete": function(){
                $(".dataTables_wrapper select").select2({
                    dropdownCssClass: 'noSearch'
                });
            }
        });
        $("#StatusFactura").select2();
        $("#TipoSerie").select2();
        $("#paymentid").select2();
        $("#frecuencia_pago").select2();

        $('#TransAfterDate').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

        $('#TransBeforeDate').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

        $('#StatusFactura').change(function(){
            if(($('#StatusFactura').val() == "Programada")){
                $('#Cron').show();
            }else{
                $('#Cron').hide();
            }
        });


    });

    function Timbrar(debtorno,folio,debtortrans_id,idBitacora){
        if (confirm('¿Desea Generar la Factura Fiscal?')) {
            // Timbra la Factura
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("facturacion/Process"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      GetInvoice:{
                          DebtorNo: debtorno,
                          Folio: folio,
                          debtortrans_id: debtortrans_id,
                          BitacoraID: idBitacora
                      },
                },
                success : function(GetInvoice, newValue) {
                    if (GetInvoice.requestresult == 'ok') {
                        displayNotify('success', GetInvoice.message);
                        $('#CustomerInquiry #status' + GetInvoice.BitacoraID).text('Procesada');
                        $('#CustomerInquiry #actions' + GetInvoice.BitacoraID).html(GetInvoice.actions_td);
                        $('#CustomerInquiry #ordertotal' + GetInvoice.BitacoraID).html(GetInvoice.OrderTotal);
                    }else{
                        displayNotify('alert', GetInvoice.message);
                    }
                },
                error : ajaxError
            });//End Timbrado
        }
    }

    function Facturar(debtorno,folio,orderno,idBitacora,Tipo){

        if (confirm('¿Desea Factura Fiscal?')) {
            // Genera la Factura y Timbra
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("facturacion/CreateInvoice"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (0),
                data: {
                    GetInvoice:{
                        DebtorNo: debtorno,
                        Folio: folio,
                        OrderNo: orderno,
                        BitacoraID: idBitacora,
                        Tipo: Tipo
                    },
                },
                success : function(Response, status, xhr ) {
                    console.log(status);
                    console.log(xhr);
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                        $('#CustomerInquiry #status' + Response.BitacoraID).text('Procesada');
                        $('#CustomerInquiry #actions' + Response.BitacoraID).html(Response.actions_td);
                        $('#CustomerInquiry #ordertotal' + Response.BitacoraID).html(Response.OrderTotal);
                    }else{
                        displayNotify('alert', Response.message);
                    }
                },
                error : ajaxError
            });//End Genera Factura
        }
    }

    function CheckFacturar(idBitacora){

        if($("#TimbrarFactura" + idBitacora).is(':checked')) {
            Value = 1;
        } else {
            Value = 0;
        }

        $.blockUI();
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("facturacion/CheckFacturar"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                Check:{
                    BitacoraID: idBitacora,
                    Value: Value
                },
            },
            success : function(Response, newValue) {
                if (Response.requestresult == 'ok') {
                    if(Response.Cheked == "1"){
                        $("#TimbrarFactura" + Response.idBitacora).attr('checked', true);
                        $("#PendingFacturar").show();
                    }else{
                        $("#TimbrarFactura" + Response.idBitacora).attr('checked', false);
                        $("#PendingFacturar").hide();
                    }
                    displayNotify('success', Response.message);
                }else{
                    displayNotify('error', Response.message);
                }
            },
            error : ajaxError
        });
    }

    function TestCron(){
        return false;
        if (confirm('¿Desea Timbrar todas las facturas de la busqueda?')) {

            window.setTimeout(function() {
                SendCron(1);
            }, 500);


            // window.setTimeout(function() {
            //     SendCron(2);
            // }, 5000);

            // window.setTimeout(function() {
            //     SendCron(3);
            // }, 7000);

            // window.setTimeout(function() {
            //     SendCron(4);
            // }, 9000);

            // window.setTimeout(function() {
            //     SendCron(5);
            // }, 11000);

            // window.setTimeout(function() {
            //     SendCron(6);
            // }, 14000);

            // window.setTimeout(function() {
            //     SendCron(7);
            // }, 17000);

            // window.setTimeout(function() {
            //     SendCron(8);
            // }, 20000);

            // window.setTimeout(function() {
            //     SendCron(9);
            // }, 23000);

            // window.setTimeout(function() {
            //     SendCron(10);
            // }, 25000);

            // window.setTimeout(function() {
            //     SendCron(11);
            // }, 12000);

            // window.setTimeout(function() {
            //     SendCron(12);
            // }, 13000);

            // window.setTimeout(function() {
            //     SendCron(13);
            // }, 14000);

            // window.setTimeout(function() {
            //     SendCron(14);
            // }, 15000);

            // window.setTimeout(function() {
            //     SendCron(15);
            // }, 16000);

            // window.setTimeout(function() {
            //     SendCron(16);
            // }, 17000);

            // window.setTimeout(function() {
            //     SendCron(17);
            // }, 18000);

            // window.setTimeout(function() {
            //     SendCron(18);
            // }, 19000);

            // window.setTimeout(function() {
            //     SendCron(19);
            // }, 20000);
        }
    }

    /**/
    function SendCron(threadno){

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("facturacion/Cron"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (0),
            data: {
                Check:{
                    TransBeforeDate: $("#TransBeforeDate").val(),
                    TransAfterDate: $("#TransAfterDate").val(),
                    StatusFactura: $("#StatusFactura").val(),
                    paymentid: $("#paymentid").val(),
                    frecuencia_pago: $("#frecuencia_pago").val(),
                    ThreadNo: threadno
                },
            },
            success : function(Response, newValue) {
                if (Response.requestresult == 'ok') {
                    displayNotify('success', Response.message);
                }else{
                    displayNotify('error', Response.message);
                }
            },
            error : ajaxError
        });
    }

    function ShowInvoiceErrors(debtortrans_id, DebtorNo){

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("facturacion/GetInvoiceError"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                  GetInvoice:{
                      debtortrans_id: debtortrans_id,
                      DebtorNo: DebtorNo
                  },
            },
            success : function(GetInvoice, newValue) {
                if (GetInvoice.requestresult == 'ok') {
                    displayNotify('success', GetInvoice.message);
                    $('#ModalLabelShowErrors').text('Error al Timbrar la Transaccion N° ' + debtortrans_id);
                    $("#ErrorMSG").text(GetInvoice.ErrorData);
                    $("#EditCustomerLink").attr('href', GetInvoice.URL);
                    $('#Modal_ShowErrors').modal('show');
                }else{
                    displayNotify('error', GetInvoice.message);
                }
            },
            error : ajaxError
        });//End
    }



    function EditOrder(debtorno,folio,orderno,idBitacora){
        var DebtorNo = $(this).attr('DebtoNo');
        var BranchCode = $(this).attr('BranchCode');
        $('#ModalLabel-EditSalesOrder').text('Editar Pedido N° ' + orderno);
        $('#Sus_BranchCode').val(BranchCode);
        $('#Sus_DebtorNo').val(DebtorNo);
        $('#Modal_EditSalesOrder').modal('show');
    }

</script>
<style>
    .label, .badge {
        font-size: 10px;
    }
</style>
<div style="width: 100%; margin-left:0%;">
<div class="container-fluid">
    <form method="POST" action="<?php echo $this->createUrl("facturacion/pendientesfacturar/"); ?>" >
        <table style="width: 100%">
            <tr>
                <td>
                    <label>Despues de:</label>
                    <input id="TransAfterDate" name="TransAfterDate" type="text" value="<?=$_POST['TransAfterDate']?>" class="span8 Date2" />
                </td>
                <td>
                    <label>Antes de:</label>
                    <input id="TransBeforeDate" name="TransBeforeDate" type="text" value="<?=$_POST['TransBeforeDate']?>" class="span8 Date2" />
                </td>
                <td>

                    <label style="margin-top: 10px;" >Estatus Factura:</label>
                    <select id="StatusFactura" name="StatusFactura" class="span6" >
                        <option value="Procesada">Procesada</option>
                        <option value="PendienteFacturar">Pendiente Facturar</option>
                        <option value="Programada">Programada</option>
                        <option value="PagoAdelantado">Pago Adelantado</option>
                        <option value="Cancelada">Cancelada</option>
                        <option value="Error">Error Timbrado</option>
                        <option value="%">Todas</option>
                    </select>
                </td>

                <td>
                    <label style="margin-top: 10px;" >Tipo Factura:</label>
                    <select id="TipoSerie" name="TipoSerie" class="span6" >
                        <option value="SIN SERIE" >SIN SERIE</option>
                        <option value="SERIE L" >SERIE L</option>
                    </select>
                </td>

                <td>
                    <input type="submit" name="Search" value="Buscar" class="btn btn-small btn-success" style=" height: 30px; margin-top: 18px;" />
                </td>
            </tr>
            <tr id="FProgramadas">
                <td>
                    <label style="margin-top: 10px;" >Forma de Pago:</label>
                    <select id="paymentid" name="paymentid[]" class="required" multiple="" >
                        <option value="-1">&nbsp;</option>
                        <?php
                        foreach($ListaFormasPago as $id => $Name){
                            if(in_array($id, $_POST['paymentid'])){
                                echo "<option selected='selected' value='{$id}'>{$Name}</option>";
                            }else{
                                echo "<option value='{$id}'>{$Name}</option>";
                            }
                            ?>
                            <!--<option value="<?=$id?>"><?=$Name?></option>-->
                        <?php } ?>
                    </select>
                </td>
                <td>
                    <label style="margin-top: 10px;" >Frecuencia de Pago:</label>
                    <select id="frecuencia_pago" name="frecuencia_pago[]" class="required" multiple="" >
                        <option value="-1">&nbsp;</option>
                        <?php
                        foreach($ListaFrecuenciaPago as $id => $Name){
                            if(in_array($id, $_POST['frecuencia_pago'])){
                                echo "<option selected='selected' value='{$id}'>{$Name}</option>";
                            }else{
                                echo "<option value='{$id}'>{$Name}</option>";
                            }
                            ?>
                            <!--<option value="<?=$id?>"><?=$Name?></option>-->
                        <?php } ?>
                    </select>
                </td>
                <td>
                    <!--FILTRO AGREGADO POR CAROLINA CASTILLO 28-06-2016 -->
                    <label style="margin-top: 10px;" >Requiere factura:</label>
                    <select id="Requierefactura" name="Requierefactura" class="span6" >
                        <option value="" >TODOS</option>
                        <option value="1" <?=($_POST['Requierefactura']==1)?"selected":""; ?>>SI</option>
                        <option value="0" <?=(isset($_POST['Requierefactura']) && $_POST['Requierefactura']==0 && $_POST['Requierefactura']!='')?"selected":""; ?> >NO</option>
                    </select>
                </td>
                <td></td>
                <td>
                    <input type="button" id="Cron" name="Cron" value="Timbrar" onclick="TestCron()" class="btn btn-small btn-danger" style=" height: 30px; margin-top: 18px;" />
                </td>
            </tr>
        </table>

        <script type="text/javascript">
            <?php
            if(!empty($_POST['StatusFactura'])){ ?>
                $("#StatusFactura option[value='<?=$_POST['StatusFactura']?>']").attr("selected",true);
            <?php } ?>

            <?php
            if(!empty($_POST['paymentid'])){ ?>
                $("#paymentid option[value='<?=$_POST['paymentid']?>']").attr("selected",true);
            <?php } ?>

            <?php
            if(!empty($_POST['frecuencia_pago'])){ ?>
                $("#frecuencia_pago option[value='<?=$_POST['frecuencia_pago']?>']").attr("selected",true);
            <?php } ?>
        </script>
    </form>
    <table id="CustomerInquiry" class="table table-hover table-condensed table-striped" style="float: 0;" >
        <thead>
            <tr>
                <th>Documento</th>
                <th>FechaPedido</th>
                <th>Folio</th>
                <th>Nombre</th>
                <th>RFC</th>
                <th>C.P.</th>
                <th>Tipo</th>
                <th>Plan</th>
                <th>FormaPago</th>
                <th>Frec.Pago</th>
                <th>Cobrador</th> <!--Se agrego cobrador Angeles Perez 28-06-2016 -->
                <th>Total</th>
                <th>Movimiento</th>
                <th>Estatus</th>
                <th>Requiere Factura</th> <!--Se agrego Requiere Factura Angeles Perez 28-06-2016 -->
                <th title="Generar Factura Fiscal">Fac.Fiscal</th>
                <th>Timbrado</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($InvoiceData as $Data){
                $RowColor = "";
                $InvoiceBadge = "";
                switch ($Data['rh_status']) {
                    case 'C':
                        $RowColor = "class= 'danger'";
                        $Data['rh_status'] = "Cancelada";
                        break;
                    case 'F':
                        $RowColor = "class= 'warning'";
                        $Data['rh_status'] = "Cancelacion de Factura";
                        break;
                    default:
                        break;
                }

                if(!empty($Data['FolioFactura'])){
                    $InvoiceBadge = "success";
                }else{
                    $InvoiceBadge = "info";
                }

                if($Data['status'] == 'Error'){
                    $InvoiceBadge = "danger";
                }

                if($Data['facturar']==1){
                    $ChekedIvoice = "checked='checked'";
                }

            ?>
            <tr <?=$RowColor?> id="<?=$Data['id']?>" >
                <td><span class='badge badge-<?=$InvoiceBadge?>' ><?=$Data['TypeName'] . ' ' . $Data['FolioFactura']?></span></td>
                <td><?=$Data['fecha_corte']?></td>
                <td><?=$Data['folio']?></td>
                <td><?=$Data['TName'] . " " .  $Data['TApellidos']?></td>
                <td><?=$Data['TaxRef']?></td>
                <td><?=$Data['PostalCode']?></td>
                <td><?=$Data['FATipo']?></td>
                <td><?=$ListaPlanes[$Data['CPlan']]?></td>
                <td><?=$Data['CPaymentName']?></td>
                <td><?=$Data['CFrecPago']?></td>
                <td><?=$Data['Cobrador']?></td> <!--Se agrego Cobrador Angeles Perez 28-06-2016 -->
                <?php
                if(empty($Data['TotalInvoice'])){
                    $Data['TotalInvoice'] = $Data['OrderTotal2'];
                } ?>
                <td id="ordertotal<?=$Data['id']?>" style="text-align: right;">$ <?=number_format($Data['TotalInvoice'],2)?></td>
                <td><?=$Data['tipo']?></td>
                <td id="status<?=$Data['id']?>"><?=$Data['status']?></td>
                <!--IMPRESION DE TEXTO SI / NO POR CAROLINA CASTILLO 28-06-2016 -->
                <td><?=($Data['RequiereFactura']==1)?'SI':'NO';?></td> <!--Se agrego Requiere Factura Angeles Perez 28-06-2016 -->
                <td style="text-align:center;">
                <?php if($Data['status'] != "Procesada"){ ?>
                    <input type="checkbox" id="TimbrarFactura<?=$Data['id']?>" name="TimbrarFactura" <?=$ChekedIvoice?> title="Generar Factura Si/NO" onclick="CheckFacturar(<?=$Data['id']?>,$(this).val())" class="success" />
                <?php } ?>
                </td>
                <td>
                    <?php if($Data['status'] == "Procesada"){
                        echo $Data['FechaTimbrado'];
                    } ?>
                </td>
                <td id="actions<?=$Data['id']?>" style ="text-align:center;">
                    <?php
                    switch ($Data['status']) {
                        case 'PendienteFacturar':
                            if($Data['facturar']==1){ ?>
                                <a id="PendingFacturar" name = "PendingFacturar" BranchCode="<?=$Data['branchcode']?>" DebtoNo="<?=$Data['debtorno']?>" title="Generar Factura" onclick="Facturar(<?=$Data['debtorno']?>,<?=$Data['folio']?>,<?=$Data['orderno']?>,<?=$Data['id']?>,'<?=$Data['tipo']?>')" ><i class="icon-file"></i></a>
                                <a target= "_blank" href="../SelectOrderItems.php?&ModifyOrderNumber=<?=$Data['orderno']?>" id="EditOrder" name = "EditOrder" DebtoNo="<?=$Data['debtorno']?>" title="Editar Datos del Pedido"  ><i class="icon-edit"></i></a>
                            <?php
                            }
                            break;
                        case 'Programada':
                            if($Data['facturar']==1){ ?>
                                <a id="PendingFacturar" name = "PendingFacturar" BranchCode="<?=$Data['branchcode']?>" DebtoNo="<?=$Data['debtorno']?>" title="Generar Factura" onclick="Facturar(<?=$Data['debtorno']?>,<?=$Data['folio']?>,<?=$Data['orderno']?>,<?=$Data['id']?>,'<?=$Data['tipo']?>')" ><i class="icon-file"></i></a>
                                <a target= "_blank" href="../SelectOrderItems.php?&ModifyOrderNumber=<?=$Data['orderno']?>" id="EditOrder" name = "EditOrder" DebtoNo="<?=$Data['debtorno']?>" title="Editar Datos del Pedido"  ><i class="icon-edit"></i></a>
                            <?php
                            }
                            break;

                        case 'PendienteTimbrar': ?>
                            <a id="Pending" name = "Pending" BranchCode="<?=$Data['branchcode']?>" DebtoNo="<?=$Data['debtorno']?>" title="Timbrar Factura" onclick="Timbrar(<?=$Data['debtorno']?>,<?=$Data['folio']?>,<?=$Data['debtortrans_id']?>,<?=$Data['id']?>)" ><i class="icon-qrcode"></i></a>
                            <?php
                            break;
                        case 'Procesada': ?>

                            <?php if($Data['rh_status'] == 'Cancelada'){ ?>
                                <a target= "_blank" href="../PHPJasperXML/sample1.php?isTransportista=<?=$Data['is_transportista']?>&transno=<?=$Data['transno']?>&&isCfdCancelado=true&afil=true" ><IMG src="../css/silverwolf/images/pdf.gif" ></a>
                                <a target = "_blank" href="<?php echo $this->createUrl("facturacion/refacturar",array('transno'=>$Data['transno'])); ?>" title="Refacturar" ><img SRC='../css/silverwolf/images/refact.png' ></a>
                            <?php }elseif(empty($Data['uuid'])){ ?>
                                <a target="_blank" onclick="ShowInvoiceErrors(<?=$Data['DebtorTransID']?>,'<?=$Data['debtorno']?>')" ><img SRC="../css/silverwolf/images/recovery.png" title="Ver Error" ></a>
                                <a target="_blank" href="../rh_recoverxml.php?id=<?=$Data['DebtorTransID']?>" ><img SRC="../recover.png" title="Recuperar Factura" ></a>
                            <?php }else{ ?>
                                <a target = "_blank" href="../PHPJasperXML/sample1.php?transno=<?=$Data['transno']?>&afil=true" title="Ver Factura PDF" ><IMG src="../css/silverwolf/images/pdf.gif" ></a>
                                <a target = "_blank" href='../rh_Cancel_Invoice.php?InvoiceNumber=<?=$Data['transno']?>' ><img SRC='../css/silverwolf/images/cancel.gif' ></a>
                            <?php } ?>
                            <?php
                            break;
                        case 'PagoAdelantado': ?>
                            <a target = "_blank" href="../PHPJasperXML/sample1.php?transno=<?=$Data['transno']?>&afil=true" title="Ver Factura PDF" ><IMG src="../css/silverwolf/images/pdf.gif" ></a>
                            <?php
                            break;
                        default:
                            break;
                    }
                    ?>
                    <a target = "_blank" href="../CustomerInquiry.php?CustomerID=<?=$Data['debtorno']?>" title="Ver Transacciones de Cliente" ><img SRC='../css/silverwolf/images/view_detail.png' ></a>
                    <!-- CustomerInquiry.php?CustomerID=74067-->
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</div>

<div id="Modal_ShowErrors" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 600px;" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelShowErrors"></h3>
    </div>
    <div class="modal-body" style="height: 200px;">
        <p>
            <div class="alert alert-error" id="ErrorMSG" ></div>
        </p>
    </div>
    <div class="modal-footer">
        <a  href="../Customers.php?DebtorNo=83201" id="EditCustomerLink" class="btn btn-info" target="_blank">Editar Datos del Socio/Cliente</a>
        <button id="Close-Modal-ReactivarSocio" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Cerrar</button>

    </div>
</div>


