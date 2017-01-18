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

    function EditarPrecioComis(id){
        $('#ModalLabelEdit').text('Editar Combinación');
        $('#Modal_EditarPrecioComis').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
    }

    function LoadForm(id){

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("precios/LoadFormpcomis"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                  GetData:{
                      num: id
                  },
            },
            success : function(data, newValue) {
                if (data.requestresult == 'ok') {
                    displayNotify('success', data.message);
                    $('#E-afiliados').val(data.GetData.afiliados);
                    $("#E-dproducto option[value='"+ data.GetData.dproducto +"']").attr("selected",true);
                    $("#E-dformap option[value='"+ data.GetData.dformap +"']").attr("selected",true);
                    $("#E-dtipopago option[value='"+ data.GetData.dtipopago +"']").attr("selected",true);
                    $('#E-tarifains').val(data.GetData.tarifains);
                    $('#E-tarifa').val(data.GetData.tarifa);
                    $("#E-empresa option[value='"+ data.GetData.empresa +"']").attr("selected",true);
                    $('#E-comision1').val(data.GetData.comision1);
                    $('#E-comision2').val(data.GetData.comision2);
                    $('#E-comision3').val(data.GetData.comision3);
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
    <div class="form-legend"><h3>Precio Comis</h3></div>
    <!--Tabs begin-->
    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a data-toggle="tab" href="#ListDTable">Precio Comis</a></li>
        <li class=""><a data-toggle="tab" href="#Create">Agregar Precio</a></li>
    </ul>

    <div class="tab-content"> <!-- Start TABS -->
        <div id="ListDTable" class="tab-pane fade active in">
            <?php include_once('modals/EditaPrecioComis.modal.php'); ?>
            <table id="ListaPreciosTable" class="table table-hover table-condensed table-striped"  >
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Afiliados</th>
                        <th>StockID</th>
                        <th>FormaPago</th>
                        <th>Frecuencia</th>
                        <th>Inscripción</th>
                        <th>Tarifa</th>
                        <th>Empresa</th>
                        <th>Comision1</th>
                        <th>Comision2</th>
                        <th>Comision3</th>
                        <th></th>
                        <!-- <th>Empresa</th> -->
                    </tr>
                </thead>
                <?php foreach($PreciosData as $Precio){ ?>
                    <tr id="<?=$Precio['num']?>" >
                        <td><?=$Precio['num'] ?></td>
                        <td><?=$Precio['afiliados'] ?></td>
                        <td><?=$ListaPlanes[$Precio['dproducto']] ?></td>
                        <td><?=$Precio['paymentname'] ?></td>
                        <td><?=$Precio['FPago'] ?></td>
                        <td><?=$Precio['tarifains'] ?></td>
                        <td><?=$Precio['tarifa'] ?></td>
                        <td><?=$ListaEmpresas[$Precio['empresa']] ?></td>
                        <td><?=$Precio['comision1'] ?></td>
                        <td><?=$Precio['comision2'] ?></td>
                        <td><?=$Precio['comision3'] ?></td>
                        <td style="width: 50px;">
                            <a onclick="EditarPrecioComis('<?=$Precio['num']?>');" title="Editar Combinación" ><i class="icon-edit"></i></a>
                            <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("deletepcomis","id"=>$Precio['num']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de Eliminar esta combinación?')) { return; }else{return false;};", "title"=>"Eliminar Combinación")); ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <div id="Create" class="tab-pane fade active">
            <?php include("preciocomis/addnew.php"); ?>
        </div>
    </div><!-- END TAB CONTENT -->
</div>
