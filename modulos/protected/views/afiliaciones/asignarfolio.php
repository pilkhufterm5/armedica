<script type="text/javascript">
    $(document).on('ready',function() {
        $("#Asesor_ID").select2();
        $("#RAsesor_ID").select2();
        $("#tipo_membresia2").select2();// Se agrego para mostrar lista con buscador Angeles Perez 2016-07-28
        $("#CMCancelacion").select2();
        $("#datepicker").datepicker();
    });
    
    function IsNumeric(input){//valida que el dato ingresado sea numerico
        if((input - 0) == input && (''+input).replace(/^\s+|\s+$/g, "").length > 0){
        }else{
            alert('Solo se admiten numeros');
        }
    }
    // Se agrego para mostrar los datos del socio  en la opcion para cambiar el tipo de Membresia 2016-07-27
   $(window).on('ready',function() {

    $(".SearchbyName").autocomplete({
            select: function( event, ui ) {
                $("#folio").val(ui.item.folio);
                return ui.item.value;
            },
            source: function( request, response ) {

                $.ajax({
                    url: "<?php echo $this->createUrl("afiliaciones/Searchfolio"); ?>",
                    type: "POST",
                    dataType: "json",
                    data: {
                        Search: {
                            string: request.term,
                        },
                    }
                }).complete( function( data ) {
                    Response={};
                    if(data.readyState==4){
                        if (typeof(data.responseText)!='undefined'){
                            Response=eval('('+data.responseText+')');
                        }
                        if (typeof(Response.requestresult)!='undefined'&&Response.requestresult == 'ok') {
                            response(Response.DataList.slice(0, 10));
                        }else{
                            //displayNotify('alert', Response);
                        }
                    }
                });

            },
            minLength: 2,
            messages: {
                noResults: '',
                results: function() {}
            }
        });
 });
//Termina
    
</script>


<div style="height: 20px;"></div>

    <div class="container-fluid">
        <div class="form-legend"><h3>Catalogo de Asignación, Reasignación, Tipo Membresía y Cancelación de Folios</h3></div>
        <!--Tabs begin-->
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#Asigna">Asignación</a></li>
            <li class=""><a data-toggle="tab" href="#Reasigna">Reasignación</a></li>
            <?php 
            // Agregado para ocultar o mostrar la opcion de modificar tipo de memebresia por Daniel Villarreal el 27 de Julio del 2016
            if($PermisoTipoMem['or_cambio_membresia']==1){ ?> 
            <li class=""><a data-toggle="tab" href="#Membresia">Tipo Membresia</a></li><!--Se agrego Para poder hacer el cambio de tipo Membresia Angeles Perez 2016-06-20-->
            <?php } 
            // Termina
            ?>
            <li class=""><a data-toggle="tab" href="#Cancela">Cancelación</a></li>
        </ul>
            
        <div class="tab-content">
            <div id="Asigna" class="tab-pane fade active in">
                <form class="form-horizontal" method="POST" name="FAsignacion">
                    <div id="Tipo" class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Tipo de Membresia'); ?></label>
                        </div>
                        
                        <div class="span1">
                            <label class="control-label"><?php echo _('Socio'); ?></label>
                        </div>
                        <div class="span1">
                            <div class="controls">
                                <input type="radio" id="tipo_membresia" name="tipo_membresia" value="Socio" style=" margin-bottom: -30px;" />
                            </div>
                        </div>
                        
                        <div class="span1">
                            <label class="control-label"><?php echo _('Cliente'); ?></label>
                        </div>
                        <div class="span1">
                            <div class="controls">
                                <input type="radio" id="tipo_membresia" name="tipo_membresia" value="Cliente" style=" margin-bottom: -30px;" />
                            </div>
                        </div>
                        <div class="span6"></div>
                    </div>
                    
                    <div id="AsesorID" class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo ('Asesor'); ?></label>
                        </div>
                        <div class="span10">
                            <div class="controls">
                                <select id="Asesor_ID" name="Asesor_ID" >
                                    <option value="">&nbsp;</option>
                                    <?php foreach($ListAsesores as $id => $value){ ?>
                                        <option value="<?=$id?>"><?=$value?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div id="Finicial" class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Folio Inicial'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="folio_inicial" name="folio_inicial" class="span6" onBlur="IsNumeric(this.value)" />
                            </div>
                        </div>
                        
                        <div class="span2">
                            <label class="control-label"><?php echo _('Folio Final'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="folio_final" name="folio_final" class="span6" onBlur="IsNumeric(this.value)" />
                            </div>
                        </div>
                    </div>
                    
                    <div id="Savebtn" class="control-group row-fluid">
                        <div class="span3">
                            <input type="submit" id="Save" name="Save" class="btn btn-success btn-small" value="Asignar" style="margin-bottom: 0px;" />
                        </div>
                    </div>
                </form>
            </div>
                
            <div id="Reasigna" class="tab-pane fade">
                <form class="form-horizontal" method="POST" name="FReasignacion" ACTION = "<?php echo $this->createUrl("afiliaciones/reasignarfolio"); ?>">
                
                    <div id="RFinicial" class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Folio Inicial'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="Rfolio_inicial" name="FReasignacion[Rfolio_inicial]" class="span6" />
                            </div>
                        </div>
                        
                        <div class="span2">
                            <label class="control-label"><?php echo _('Folio Final'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="Rfolio_final" name="FReasignacion[Rfolio_final]" class="span6" />
                            </div>
                        </div>
                    </div>
                    
                    <div id="RAsesorID" class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo ('Asesor'); ?></label>
                        </div>
                        <div class="span10">
                            <div class="controls">
                                <select id="RAsesor_ID" name="FReasignacion[RAsesor_ID]" >
                                    <option value="">&nbsp;</option>
                                    <?php foreach($ListAsesores as $id => $value){ ?>
                                        <option value="<?=$id?>"><?=$value?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div id="RSavebtn" class="control-group row-fluid">
                        <div class="span3">
                            <input type="submit" id="RSave" name="FReasignacion[RSave]" class="btn btn-success btn-small" value="Reasignar" style="margin-bottom: 0px;" />
                        </div>
                    </div>
                </form>
            </div>
            <?php 
// Agregado para ocultar o mostrar la opcion de modificar tipo de memebresia por Daniel Villarreal el 27 de Julio del 2016
            if($PermisoTipoMem['or_cambio_membresia']==1){ ?> 
<!--Se agrego para hacer el cambio del tipo de membresia del folio (Socio-Cliente) Angeles Perez 2016-06-20 Se actualizo 2016-07-27-->
            <div id="Membresia" class="tab-pane fade">
                <form class="form-horizontal" method="POST" name="FMembresia" ACTION = "<?php echo $this->createUrl("afiliaciones/membresiafolio"); ?>">
<!--Se agrego para mostrar los datos del socio 2016-07-27-->
                        
                            <div class="controls">
                              <input class="input-block-level SearchbyName" type="text" placeholder="INGRESE FOLIO para mostrar: Folio-Nombre-Estatus-TipoMembresia" name="folio" id="folio">
                            </div>
                        
<!--Termina-->
                    <div id="RFinicial" class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Folio:'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="Mfolio_inicial" name="FMembresia[Mfolio_inicial]" class="span6" />
                            </div>
                        </div>
                    </div>
                    
                    <div id="RAsesorID" class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo ('Tipo Membresia:'); ?></label>
                        </div>
                        <div class="span10">
                            <div class="controls">
                                <select id="tipo_membresia2" name="FMembresia[tipo_membresia]" >
                                    <option value="">SELECCIONE</option>
                                    <option value="Socio">SOCIO</option>
                                    <option value="Cliente">CLIENTE</option> 
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div id="MSavebtn" class="control-group row-fluid">
                        <div class="span3">
                            <input type="submit" id="MSave" name="FMembresia[MSave]" class="btn btn-success btn-small" value="Actualizar" style="margin-bottom: 0px;" />
                        </div>
                    </div>
                </form>
            </div>
<!--Termina-->
            <?php } 
// Termina
            ?>
                
            <div id="Cancela" class="tab-pane fade">
                <form class="form-horizontal" method="POST" name="FCancelacion" action="<?php echo $this->createUrl("afiliaciones/cancelarfolio"); ?>">
                
                    <div id="CFolio" class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Folio'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="CFolio" name="CFolio" class="span6" />
                            </div>
                        </div>
                    </div>
                    
                    <div id="CAsesorID" class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo ('Motivo de Cancelacion'); ?></label>
                        </div>
                        <div class="span10">
                            <div class="controls">
                                <select id="CMCancelacion" name="CMCancelacion" >
                                    <option value="">&nbsp;</option>
                                    <?php foreach($ListMotivosC as $id => $value){ ?>
                                        <option value="<?=$value?>"><?=$value?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div id="CSavebtn" class="control-group row-fluid">
                        <div class="span3">
                            <input type="submit" id="CSave" name="CSave" class="btn btn-danger btn-small" value="Cancelar" style="margin-bottom: 0px;" />
                        </div>
                    </div>
                </form>
            </div>
                
        </div>
        <!--Tabs end-->
    </div>

