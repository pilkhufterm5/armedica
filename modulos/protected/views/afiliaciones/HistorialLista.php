<script>
    $(document).on('ready', function(){
        

        $("#datosHistorial").dataTable({
             dom: 'T<"clear">lfrtip',
            tableTools: {
                aButtons: [{
                    "sExtends": "xls",
                    "sButtonText": "Exporta a Excel",
                }]
            },
            "aLengthMenu": [
                [25, 50, 100, 200, -1],
                [25, 50, 100, 200, "Todo"]
            ],
        });

    });

</script>

<!-- DAR FORMATO A LAS FECHAS SELECCIONADAS PARA MOSTRARLAS EN FORMATO DIA/MES/AÑO -->

	<?php
        $fechainical = $_POST['Fecha_Inicial'];
        $fechafinal = $_POST['Fecha_Final'];
        $parteinicial = explode('-', $fechainical);
        $partefinal = explode('-', $fechafinal);

        switch ($parteinicial[1]) {
        	case '01': $mes_seleccionado = "Enero"; 		break;
        	case '02': $mes_seleccionado = "Febrero"; 		break;
        	case '03': $mes_seleccionado = "Marzo"; 		break;
        	case '04': $mes_seleccionado = "Abril"; 		break;
        	case '05': $mes_seleccionado = "Mayo"; 			break;
        	case '06': $mes_seleccionado = "Junio"; 		break;
        	case '07': $mes_seleccionado = "Julio";			break;
        	case '08': $mes_seleccionado = "Agoosto"; 		break;
        	case '09': $mes_seleccionado = "Septiembre";		break;
        	case '10': $mes_seleccionado = "Octubre";		break;
        	case '11': $mes_seleccionado = "Noviembre";		break;
        	case '12': $mes_seleccionado = "Diciembre";		break;	
        }

        switch ($partefinal[1]) {
        	case '01': $mes_final_seleccionado = "Enero"; 			break;
        	case '02': $mes_final_seleccionado = "Febrero"; 		break;
        	case '03': $mes_final_seleccionado = "Marzo"; 			break;
        	case '04': $mes_final_seleccionado = "Abril"; 			break;
        	case '05': $mes_final_seleccionado = "Mayo"; 			break;
        	case '06': $mes_final_seleccionado = "Junio"; 			break;
        	case '07': $mes_final_seleccionado = "Julio";			break;
        	case '08': $mes_final_seleccionado = "Agoosto"; 		break;
        	case '09': $mes_final_seleccionado = "Septiembre";		break;
        	case '10': $mes_final_seleccionado = "Octubre";			break;
        	case '11': $mes_final_seleccionado = "Noviembre";		break;
        	case '12': $mes_final_seleccionado = "Diciembre";		break;	
        }

        $de = "{$parteinicial[2]} de {$mes_seleccionado} del {$parteinicial[0]}";
        $a = "{$partefinal[2]} de {$mes_final_seleccionado} del {$partefinal[0]}";
        ?>
<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<hr><br>
			<h2 align="center">Permanencia de socios Cancelados o Suspendidos en la empresa</h2>
			<hr>
			<div class="row span12">
			<form method="POST" action="<?php echo $this->createUrl("afiliaciones/HistorialPersonas"); ?>">
            <div class="span4">
                <label>Fecha inicial:</label>
                <input id="Fecha_Inicial" name="Fecha_Inicial" type="date" class="span6" required="true"/>
            </div>
            <div class="span4">
                <label>Fecha Final:</label>
                <input id="Fecha_Final" name="Fecha_Final" type="date" class="span6" required="true"/>
            </div>
            <div class="span4">
                <button type="submit" class="btn btn-large btn-success">Buscar</button>
            </div>
            <br>
            </form>
        </div>
			<hr>
			<div class="col-sm-6">
				<table>
					<tr>&nbsp;</tr>
					<tr>&nbsp;</tr>
					<tr>&nbsp;</tr>
				</table>
				<p>Fechas seleccionadas: &nbsp;&nbsp;&nbsp; 
				<b>Del</b> <font color="blue" size="2px"> <?php echo "'". $de."'"; ?></font>&nbsp;&nbsp; 
				<b>Al</b>  <font color="blue" size="2px"> <?php echo "'". $a ."'";?></font></p>
			</div>
			
			<div class="table-responsive">
                <table id="datosHistorial" class="table table-bordered table-hover table-striped">
	                <thead>
	                    <tr>
	                        
	                        <th>Folio</th>
	                        <th>Nombre</th>
	                        <th>Estatus</th>
	                        <th>Fecha Ingreso</th>
	                        <th>Fecha Canc/Susp.</th>
	                        <th>Permanencia</th>
	                        <th>Motivo Canc/Susp..</th>
	                    </tr>
	                </thead>
	                <tbody id="Contenido">
	                    <?php
	                        foreach ($datosHistorial as $_datosHistorial){
	                        	if (empty($_datosHistorial['permanencia'])) {
										$_datosHistorial['permanencia'] = "Sin Fecha Capturada";
									}else{
								// Dar formato a la cantidad de dias en año mes dias.
										$_datosHistorial['permanencia'] = $_datosHistorial['permanencia']/365;
								//obtener los años
										$num_year = $_datosHistorial['permanencia'];
										$anios = substr($num_year, 0,2);
										$_años = str_replace(".", "", $anios);
										if ($_años == 0) {
											$__años = "";
											$leyendaAños = "";
										}elseif ($_años == 1) {
											$__años = $_años;
											$leyendaAños = " Año ";
										}else{
											$__años = $_años;
											$leyendaAños = " Años ";
										}
								//obtener los meses
										$meses = substr($num_year, 2, 3);
										$meses_comparar = substr($num_year, 2,1);
										if ($meses_comparar == ".") {
											$_meses = str_replace(".", "0.", $meses);
										}else{
											$_meses = "0.".$meses;
										}
										$datmes = $_meses * 12 / 1;
								//obtener los dias
										$___meses = substr($datmes, 0,2);
										$eliminar_dias = substr($datmes, 1,1);
										if ($eliminar_dias == ".") {
											$__meses = str_replace(".", "", $___meses);
										}else{
											$__meses = substr($___meses, 0,2);
										}
										if ($__meses == 1) {
											$_______meses = $__meses." mes";
										}else{
											$_______meses = $__meses." meses";
										}								
									}

	                    ?>
	                        <tr>
	                            <td><?= $_datosHistorial['folio'] ?></td>
	                            <td><?= $_datosHistorial['nombre'] ?></td>
	                            <td><?= $_datosHistorial['estatus'] ?></td>
	                            <td><?= $_datosHistorial['fecha_ingreso'] ?></td>
	                            <td><?= $_datosHistorial['fecha_cancel_susp'] ?></td>
	                            <td><?= $__años.$leyendaAños.$_______meses ?></td>    
	                            <td><?= strtoupper($_datosHistorial['motivo']) ?></td>
	                        </tr>
	                    <?php } ?>
	                </tbody>
                                
                </table>
            </div>
		</div>
	</div>
</div>
    