<script type="text/javascript">
    $(document).on('ready',function() {

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
        $("#cobradorid").select2(); // AGREGADO EL 31 DE DICIEMBRE DEL 2015 POR DANIEL VILLARREAL

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

        $("#CheckAll").click(function(event) {
            if(this.checked){
                $('.TimbrarFactura').attr('checked','checked')
            }else{
                $('.TimbrarFactura').removeAttr('checked');
            }
        });

    });


    function FacturarTimbrar(){

         var CambiarMesFacturacion = $('#CambiarMesFacturacion').val();
         
        if (confirm('¿Desea Facturar y Timbrar todas los Folios de la busqueda?')) {

            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("facturacion/FacturaEmision"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (0),
                data: {
                    Emision:{
                        Folios: $('.TimbrarFactura').serialize(),
                        Tipo: 'RecordatorioPago',
                         CambiarMesFacturacion: CambiarMesFacturacion
                    },
                },
                success : function(Response, newValue) {
                    console.log(Response);
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                        window.setTimeout(function() {
                            document.location.href = document.location.href;
                            //location.reload();
                        }, 1000);
                    }else{
                        displayNotify('error', Response.message);
                    }
                },
                error : ajaxError
            });

        }

    }


</script>
<style>
    .label, .badge {
        font-size: 10px;
    }
</style>
<div style="width: 100%; margin-left:0%;">
<div class="container-fluid">
    <form method="POST" action="<?php echo $this->createUrl("facturacion/emision/"); ?>" >
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

<!--                     <label style="margin-top: 10px;" >Estatus Factura:</label>
                    <select id="StatusFactura" name="StatusFactura" class="span6" >
                        <option value="Procesada">Procesada</option>
                        <option value="PendienteFacturar">Pendiente Facturar</option>
                        <option value="Programada">Programada</option>
                        <option value="PagoAdelantado">Pago Adelantado</option>
                        <option value="Cancelada">Cancelada</option>
                        <option value="Error">Error Timbrado</option>
                        <option value="%">Todas</option>
                    </select> -->
                </td>

                <td>
<!--                     <label style="margin-top: 10px;" >Tipo Factura:</label>
                    <select id="TipoSerie" name="TipoSerie" class="span6" >
                        <option value="SIN SERIE" >SIN SERIE</option>
                        <option value="SERIE L" >SERIE L</option>
                    </select> -->
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
                 <!-- == AGREGADO POR DANIEL VILLARREAL EL 30 DE DICIEMBRE DEL 2015 -->
                <td>
                    <label style="margin-top: 10px;" >Cobradores:</label>
                    <select id="cobradorid" name="cobradorid[]" class="required" multiple="" >
                        <option value="-1">&nbsp;</option>
                        <?php
                        foreach($ListaCobradores as $id => $Name){
                            if(in_array($id, $_POST['cobradorid'])){
                                echo "<option selected='selected' value='{$id}'>{$Name}</option>";
                            }else{
                                echo "<option value='{$id}'>{$Name}</option>";
                            }
                            ?>
                            <!--<option value="<?=$id?>"><?=$Name?></option>-->
                        <?php } ?>
                    </select>
                </td>
                   <!-- TERMINA -->
                <td>
                    <!-- == AGREGADO POR DANIEL VILLARREAL EL 25 DE NOVIEMBRE DEL 2015 -->
                    <label style="margin-top: 10px;" >
                        Seleccione el Periodo:
                    </label>
                    <select name="CambiarMesFacturacion" id="CambiarMesFacturacion" class="required">
                        <option value="0" <?php echo ($_POST['CambiarMesFacturacion']==0)?'selected':'';?> >Mes Actual</option>                             
                        <option value="1" <?php echo ($_POST['CambiarMesFacturacion']==1)?'selected':'';?> >Mes Siguiente</option>                             
                    </select>
                    <!-- TERMINA -->
                    
                </td>
               
                <td>
                    <input type="button" id="CreaFactura" name="CreaFactura" value="Facturar-Timbrar" onclick="FacturarTimbrar()" class="btn btn-small btn-danger" style=" height: 30px; margin-top: 18px;" />
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
    <table id="CustomerInquiry" class="table table-hover table-condensed table-bordered table-striped" style="float: 0;" >
        <thead>
            <tr>
                <th>FechaPedido</th>
                <th>Folio</th>
                <th>Nombre</th>
                <th>RFC</th>
                <th>C.P.</th>
                <th>Tipo</th>
                <th>Plan</th>
                <th>FormaPago</th>
                <th>Frec.Pago</th>
                 <!-- AGREGADO POR DANIEL VILLARREAL EL 30 DE DICIEMBRE DEL 2015 -->
                <th>Cobrador</th>
                <!-- TERMINA -->
                <th>Total</th>
                <th title="Generar Factura Fiscal">Fac.Fiscal <input type='checkbox' id='CheckAll'></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($DebtorData as $Data){ ?>
                <tr id="<?=$Data['id']?>" >
                    <td><?=$Data['FechaPedido']?></td>
                    <td><?=$Data['folio']?></td>
                    <td><?=$Data['AfilName']?></td>
                    <td><?=$Data['taxref']?></td>
                    <td><?=$Data['PostalCode']?></td>
                    <td><?=$Data['tipo_membresia']?></td>
                    <td><?=$ListaPlanes[$Data['CPlan']]?></td>
                    <td><?=$ListaFormasPago[$Data['CPaymentName']]?></td>
                    <td><?=$ListaFrecuenciaPago[$Data['CFrecPago']]?></td>
                    <td><?=$Data['nombre']?></td>
                    <td><?=$Data['costo_total']?></td>
                    <td style="text-align:center;">
                        <input type="checkbox" id="TimbrarFactura<?=$Data['id']?>" name="TimbrarFactura[]" value="<?=$Data['folio']?>" title="Generar Factura Si/NO"  class="success TimbrarFactura" />
                    </td>
                    <td id="actions<?=$Data['id']?>" style ="text-align:center;"></td>
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


