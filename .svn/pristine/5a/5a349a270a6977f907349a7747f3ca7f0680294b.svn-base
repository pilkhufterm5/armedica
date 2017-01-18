<script type="text/javascript">
    $(document).on('ready',function() {
        $('#CustomerInquiry').dataTable( {
            "sPaginationType": "bootstrap",
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
        $("#TipoFactura").select2();

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

    });

    function Elimiar(){
        if (confirm("¿Esta seguro de Eliminar la Dirección?")){
            return;
        }else{
            return false;
        }
    }
</script>

<style>

    .container {
        margin-left: 0px;
        margin-right: 0px;
    }

    .container-fluid {
        padding-left: 0px;
        padding-right: 0px;
    }

</style>

<div class="container-fluid">
    <form method="POST" action="<?php echo $this->createUrl("facturacion/index/"); ?>" >
        <table>
            <tr>
                <td><label>Despues de:</label>
                    <input id="TransAfterDate" name="TransAfterDate" type="text" value="<?=$_POST['TransAfterDate']?>" class="span8" />
                </td>

                <td>
                    <label>Antes de:</label>
                    <input id="TransBeforeDate" name="TransBeforeDate" type="text" value="<?=$_POST['TransBeforeDate']?>" class="span8" />
                </td>

                <td>
                    <label style="margin-top: 10px;" >Estatus Factura:</label>
                    <select id="StatusFactura" name="StatusFactura" class="span6" value="<?=$_POST['StatusFactura']?>" >
                        <option>Activa</option>
                        <option>Cancelada</option>
                    </select>
                </td>

                <td>
                    <label style="margin-top: 10px;" >Tipo Factura:</label>
                    <select id="TipoFactura" name="TipoFactura" class="span6" value="<?=$_POST['TipoFactura']?>" >
                        <option value="Sin Serie" >Sin Serie</option>
                        <option value="Serie L" >Serie L</option>
                    </select>
                </td>
                <td>
                    <input type="submit" name="Search" value="Buscar" class="btn btn-small btn-success" style=" height: 30px; margin-top: 18px;" />
                </td>
            </tr>
        </table>
    </form>

        <table id="CustomerInquiry" class="table table-hover table-condensed table-striped" style="float: 0;" >
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>N°</th>
                    <th>Fecha Factura</th>
                    <th>Fecha SAT</th>
                    <th>Folio</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>FormaPago</th>
                    <th>Frec.Pago</th>
                    <th>Total</th>
                    <th>TipoFactura</th>
                    <th>Estatus</th>
                    <th>Referencia</th>
                    <th>Comentarios</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($InvoiceData as $Data){
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
                            $RowColor = "class= ''";
                            break;
                    }
                ?>
                <tr <?=$RowColor?>>
                    <td><?=$Data['typename']?></td>
                    <td><?=$Data['serie'] . $Data['folio'] . "(" . $Data['transno'] . ")"?></td>
                    <td><?=$Data['FechaFactura']?></td>
                    <td><?=$Data['FechaTimbrado']?></td>
                    <td><?=$Data['TFolio']?></td>
                    <td><?=$Data['TName'] . " " .  $Data['TApellidos']?></td>
                    <td><?=$Data['FATipo']?></td>
                    <td><?=$Data['CPaymentName']?></td>
                    <td><?=$Data['CFrecPago']?></td>
                    <td style="text-align: right;">$ <?=number_format($Data['totalamount'],2)?></td>
                    <td></td>
                    <td><?=$Data['rh_status']?></td>
                    <td><?=$Data['reference']?></td>
                    <td><?=$Data['invtext']?></td>

                    <td>
                        <?php if($Data['rh_status'] == "N"){ ?>
                        <a href='../rh_Cancel_Invoice.php?InvoiceNumber=<?=$Data['transno']?>' target='_blank' ><img SRC='../css/silverwolf/images/cancel.gif' ></a>
                        <a target = "_blank" href='../rh_j_downloadFacturaElectronicaXML_CFDI.php?downloadPath=XMLFacturacionElectronica/xmlbycfdi/<?=$Data['uuid']?>.xml'><img SRC='../images/xml.gif' ></a>
                        <a target= "_blank" href="../PHPJasperXML/sample1.php?isTransportista=<?=$Data['is_transportista']?>&transno=<?=$Data['transno']?>&afil=true&
                            <?php ($myrow['is_carta_porte']?'isCartaPorte=true':'') . ($Data['rh_status']=='C'?'&isCfdCancelado=true':'') ?>" ><IMG src="../css/silverwolf/images/pdf.gif" ></a>
                        <?php }elseif($Data['rh_status'] == "Cancelacion de Factura" && $Data['type'] == 11){ ?>
                        <a target="_blank" href="../rh_PrintCustTrans.php?idDebtortrans=2148&FromTransNo=<?=$Data['transno']?>&InvOrCredit=Credit"><IMG SRC='../css/silverwolf/images/preview.gif' title="Click para visualizar el Abono"></a>

                        <?php }elseif($Data['rh_status'] == "Cancelada" && $Data['type'] == 10){ ?>
                            <a target= "_blank" href="../PHPJasperXML/sample1.php?isTransportista=<?=$Data['is_transportista']?>&transno=<?=$Data['transno']?>&&isCfdCancelado=true" ><IMG src="../css/silverwolf/images/pdf.gif" ></a>

                        <?php } ?>
                    </td>

                    <!--
                    <td><?=$Data['transno']?></td>
                    <td><?=$Data['branchcode']?></td>
                    <td><?=$Data['trandate']?></td>
                    <td><?=$Data['reference']?></td>
                    <td><?=$Data['invtext']?></td>
                    <td><?=$Data['order_']?></td>
                    <td><?=$Data['rate']?></td>
                    <td><?=$Data['rh_status']?></td>
                    <td><?=$Data['totalamount']?></td>
                    <td><?=$Data['allocated']?></td>
                    <td><?=$Data['is_cfd']?></td>
                    <td><?=$Data['uuid']?></td>
                    <td><?=$Data['no_certificado']?></td>
                    <td><?=$Data['fk_transno']?></td>
                    <td><?=$Data['is_carta_porte']?></td>
                    <td><?=$Data['is_transportista']?></td> -->

                </tr>
                <?php } ?>
            </tbody>
        </table>
</div>
