<script type="text/javascript">
    $(document).on('ready', function() {
        $('#ListaPreciosTable').dataTable({
            "sPaginationType" : "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "oTableTools": {
                "aButtons": [{
                    "sExtends": "collection",
                    "sButtonText": "Exportar",
                    "aButtons": [ "print", "csv", "xls", "pdf" ]
                }]
            },
            "aLengthMenu" : [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "All"]],
            "fnInitComplete" : function() {
                $(".dataTables_wrapper select").select2({
                    dropdownCssClass : 'noSearch'
                });
            }
        });
    });

    function EditarCombinacion(id){
        $('#ModalLabelEdit').text('Editar Combinación');
        $('#Modal_EditarCombinacion').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
    }

    function LoadForm(id){

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("precios/LoadForm"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                  GetData:{
                      id: id
                  },
            },
            success : function(data, newValue) {
                if (data.requestresult == 'ok') {
                    displayNotify('success', data.message);
                    $('#E-nafiliados').val(data.GetData.nafiliados);
                    $("#E-stockid option[value='"+ data.GetData.stockid +"']").attr("selected",true);
                    $("#E-paymentid option[value='"+ data.GetData.paymentid +"']").attr("selected",true);
                    $("#E-frecpagoid option[value='"+ data.GetData.frecpagoid +"']").attr("selected",true);
                    $('#E-porcdesc').val(data.GetData.porcdesc);
                    if(data.GetData.aplicadesc==1){
                        $('#E-aplicadesc').attr('checked','checked');
                    }
                    $('#E-costouno').val(data.GetData.costouno);
                    $('#E-costodos').val(data.GetData.costodos);
                }else{
                    displayNotify('alert', data.message);
                }
            },
            error : ajaxError
        });
    }

</script>


<div style="height: 20px;"></div>
<div class="container-fluid">
    <div class="form-legend"><h3>Matriz de Precios Afiliación</h3></div>
    <!--Tabs begin-->
    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a data-toggle="tab" href="#ListDTable">Matriz de Precios</a></li>
        <li class=""><a data-toggle="tab" href="#Create">Agregar Precio</a></li>
    </ul>

    <div class="tab-content"> <!-- Start TABS -->
        <div id="ListDTable" class="tab-pane fade active in">
            <?php include_once('modals/EditarCombinacion.modal.php'); ?>
            <table id="ListaPreciosTable" class="table table-hover table-condensed table-striped"  >
                <thead>
                    <tr>
                        <th>N° Afiliados</th>
                        <th>StockID</th>
                        <th>FormaPago</th>
                        <th>TipoPago</th>
                        <th>%Desc</th>
                        <th>AplicaDesc</th>
                        <th>Costo1</th>
                        <th>Costo2</th>
                        <th>FecAumAct</th>
                        <th>FecAumAnt</th>
                        <th>Costo1An</th>
                        <th>Costo2An</th>
                        <th></th>
                        <!-- <th>Empresa</th> -->
                    </tr>
                </thead>
                <tbody>
                <?php foreach($PreciosData as $Precio){ ?>
                    <tr id="<?=$Precio['id']?>" >
                        <td><?=$Precio['nafiliados'] ?></td>
                        <td><?=$ListaPlanes[$Precio['stockid']] ?></td>
                        <td><?=$Precio['paymentname'] ?></td>
                        <td><?=$Precio['frecuencia'] ?></td>
                        <td><?=$Precio['porcdesc'] ?></td>
                        <td><?=$Precio['aplicadesc'] ?></td>
                        <td><?=$Precio['costouno'] ?></td>
                        <td><?=$Precio['costodos'] ?></td>
                        <td><?=$Precio['fecaumact'] ?></td>
                        <td><?=$Precio['fecaumant'] ?></td>
                        <td><?=$Precio['costounoan'] ?></td>
                        <td><?=$Precio['costodosan'] ?></td>
                        <td style="width: 50px;">
                            <a onclick="EditarCombinacion('<?=$Precio['id']?>');" title="Editar Combinación" ><i class="icon-edit"></i></a>
                            <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("delete","id"=>$Precio['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de Eliminar esta combinación?')) { return; }else{return false;};", "title"=>"Eliminar Combinación")); ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div id="Create" class="tab-pane fade active">
            <?php include("index/addnew.php"); ?>
        </div>
    </div><!-- END TAB CONTENT -->
</div>
