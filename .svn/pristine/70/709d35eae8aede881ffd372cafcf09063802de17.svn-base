<script type="text/javascript">
    $.blockUI();
    //$.blockUI();
    $(document).on('ready',function() {

        $('#ReporteBPC').dataTable( {
            "sPaginationType": "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "oTableTools": {
                "aButtons": [{
                    "sExtends": "collection",
                    "sButtonText": "Exportar",
                    "aButtons": [ "print", "csv", "xls", {
                        "sExtends": "pdf",
                        "sPdfOrientation": "landscape",
                        //"sPdfMessage": "Reporte de Inventario Almacen <?=$_POST['LockStock']?>",
                        "sTitle": "Reporte de Inventario por Almacen (<?=$_SESSION['rh_permitionlocation'][$_POST['LockStock']] . ') - ' . date('Y-m-d') ?> ",
                        }, ]
                }]
            },
            "aLengthMenu": [
                [10,25, 50, 100, 200, -1],
                [10,25, 50, 100, 200, "All"]
            ],
            "fnInitComplete": function(){
                $(".dataTables_wrapper select").select2({
                    dropdownCssClass: 'noSearch'
                });
                $.unblockUI();
            }
        });
        $('div[id="table_id_length"] select').addClass('span1');
        $("#LockStock").select2();
        $("#Category1").select2();
        $("#Category2").select2();

        $("#Search").click(function(event) {
            $('form#StocksReport').submit();
        });

    });

</script>
<div style="height: 50px;"></div>
<div class="accordion-group">
    <div class="accordion-heading">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-BajasPC" href="#BajasPC"><label class="accordion-header">Reporte de Inventario</label></a>
    </div>
    <div id="BajasPC" class="accordion-body in collapse">
        <div id="test" class="accordion-inner">
            <form method="POST" id="StocksReport" action="<?php echo $this->createUrl("stockmoves/reporteinventario"); ?>">

                <div class="control-group row-fluid">
                    <div class="span3">
                        <label class="control-label" >de Categoria:</label>
                        <div class="controls">
                            <select id="Category1" name="Search[Category1]">
                                <?php foreach (Controller::GetStockCatList() as $idx => $Category){
                                        echo "<option value='{$idx}'>{$Category}</option>";
                                    } ?>
                            </select>
                            <script type="text/javascript">
                                $("#Category1 option[value='<?php echo $_POST['Search']['Category1']; ?>']").attr("selected",true);
                            </script>
                        </div>
                    </div>
                    <div class="span3">
                        <label class="control-label" >a Categoria:</label>
                        <div class="controls">
                            <select id="Category2" name="Search[Category2]">
                                <?php foreach (Controller::GetStockCatList() as $idx => $Category){
                                        echo "<option value='{$idx}'>{$Category}</option>";
                                    } ?>
                            </select>
                            <script type="text/javascript">
                                $("#Category2 option[value='<?php echo $_POST['Search']['Category2']; ?>']").attr("selected",true);
                            </script>
                        </div>
                    </div>
                    
                    <div class="span3">
                        <label>Almacen: </label>
                        <select id="LockStock" name="Search[LockStock]" >
                            <!-- <option value="All">Todas</option>-->
                            <?php foreach($_SESSION['rh_permitionlocation'] as $id => $Name){ ?>
                                <option value="<?=$id?>"><?=$Name?></option>
                            <?php } ?>
                        </select>
                        <script type="text/javascript">
                            $("#LockStock option[value='<?php echo $_POST['Search'][LockStock]; ?>']").attr("selected",true);
                        </script>
                    </div>
                    <div class="span3">
                    	<label class="control-label" >
                    	<input style="margin-top: 0" type="checkbox" name="Search[InventarioCero]" <?=(isset($_POST['Search']['InventarioCero'])?' checked=checked ':'')?>value="1">Mostrar Todos
                    	</label>
                    </div>
                </div>
                <div class="control-group row-fluid">
                <?php foreach($Categorias as $valor){?>
                    <div class="span3">
                        <label class="control-label" ><?php echo $valor['Nombre'];?></label>
                        <div class="controls">
                		<?php
							echo '<select name="'.$valor['SelectName'].'">';
							
                			foreach($valor['SelectOption'] as $data){	
                        		echo '<option ';
                        		echo 'value="'.$data['value'].'"';
                        		if($data['selected'])
                        			echo ' selected=selected ';
                        		echo '>';
                        		echo $data['html'];
                        		echo '</option>';
                        	}
                        	echo '</select>';
                        	?>
                        
                        </div>
                     </div>
                     <?php }?>
                     
				</div>
				<div class="control-group row-fluid">
				<div class="span13">
				<label class="control-label" ></label>
					<input type="button" class="btn btn-success btn-small" id="Search"  value="Buscar" style="margin-bottom: -10px;" >
				</div>
				</div>
            </form>
            <table class="table table-striped table-hover table-bordered" id="ReporteBPC">
                <thead>
                    <tr>

                        <th>Categoria</th>
                        <th>Cod. Barras</th>
                        <th>IDAgrupador</th>
                        <th>Agrupador</th>
                        <?php foreach($Categorias as $valor){
                        	echo '<th>'.$valor['Nombre'].'</th>';
                        }?>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>NoSerie</th>
                        <th>Fecha Expiración</th>
                        <th>Existencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($ListMovesData as $Data){ ?>
                    <tr>
                        <td><?=$Data['categorydescription']?></td>
                        <td><?=$Data['barcode']?></td>
                        <td><?=$Data['id_agrupador']?></td>
                        <td><?=$Data['id_agrupador_description']?></td>
                        <?php
                        foreach($Categorias as $i=>$valor){
							echo '<td>'.$Data['Categoria'.$i].'</td>';
                        }
                        ?>
                        
                        <td><?=$Data['stockid']?></td>
                        <td><?=$Data['description']?></td>
                        <td><?=$Data['serialno']?></td>
                        <td><?=$Data['expirationdate']?></td>
                        <td style="text-align: right;"><?=($Data['qtyonhand']==''?0:$Data['qtyonhand'])?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


