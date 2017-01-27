<script type="text/javascript">
    $(document).on('ready',function() {

        oTable1 = $('#ListAfiliados').dataTable( {
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

        $("#GSSearch").click(function(event) {
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/BuscarFolio"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    Search:{
                        Nombre: $("#GSNombre").val(),
                        Apellido: $("#GSApellido").val(),
                        Folio: $("#GSFolio").val(),
                    },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        oTable1.fnDestroy();
                        $('#BodyContent').html(Response.TableContent);

                        oTable1 = $('#ListAfiliados').dataTable( {
                        "sPaginationType": "bootstrap",
                        "sDom": 'T<"clear">lfrtip',
                        "oTableTools": {
                            "aButtons": [{
                                "sExtends": "collection",
                                "sButtonText": "Exportar",
                                "aButtons": [ "print", "csv", "xls", "pdf" ]
                            }]
                        },
                        "scrollX": true,
                        "fnInitComplete": function(){
                            $(".dataTables_wrapper select").select2({
                                dropdownCssClass: 'noSearch'
                            });
                        }
                    });

                        displayNotify('success', Response.message);
                    }else{
                        displayNotify('error', Response.message);
                    }
                },
                error : ajaxError
            });
        });


    });
</script>

<div style="height: 20px;"></div>

  <div class="container-fluid">
    <a href="<?php echo $this->createUrl("afiliaciones/afiliacion"); ?>"><input type="button" value="ir a Afiliaciones" class="btn btn-info"></a>
    <div class="control-group row-fluid">
        <div class="form-legend"><h3>Busqueda de Afiliados</h3></div>
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#Lista">Afiliados</a></li>
        </ul>
        <div class="tab-content"> <!-- Start TABS -->
            <div id="Lista" class="tab-pane fade active in">
                <form>
                <div class="control-group row-fluid">
                    <div class="span3">
                        <label class="control-label" >Folio:</label>
                        <div class="controls">
                            <input type="text" id="GSFolio" name="Search[GSFolio]" >
                        </div>
                    </div>
                    <div class="span3">
                        <label class="control-label" >Nombre / Empresa:</label>
                        <div class="controls">
                            <input type="text" id="GSNombre" name="Search[GSNombre]" >
                        </div>
                    </div>
                    <div class="span3">
                        <label class="control-label" >Apellido:</label>
                        <div class="controls">
                            <input type="text" id="GSApellido" name="Search[GSApellido]" >
                        </div>
                    </div>
                    <div class="span3">
                        <label class="control-label"  >&nbsp;</label>
                        <div class="controls">
                            <input type="button" id="GSSearch" value="Buscar" class="btn btn-success" style="height: 35px;" >
                        </div>
                    </div>
                </div>
                </form>
                <table id="ListAfiliados" class="table table-hover table-condensed table-striped">
                    <thead>
                        <tr>
                            <th><?php echo _('Folio') ?></th>
                            <th><?php echo _('Nombre') ?></th>
                            <th><?php echo _('Fecha Ingreso') ?></th>
                            <th><?php echo _('Calle') ?></th>
                            <th><?php echo _('Numero') ?></th>
                            <th><?php echo _('Colonia') ?></th>
                            <th><?php echo _('Municipio') ?></th>
                            <th><?php echo _('Teléfono') ?></th>
                            <th><?php echo _('Email') ?></th>
                            <th>Estatus</th>
                        </tr>
                    </thead>
                    <tbody id="BodyContent">

                    </tbody>
                </table>
            </div>
        </div> <!-- End Tabs -->
    </div>
</div>

