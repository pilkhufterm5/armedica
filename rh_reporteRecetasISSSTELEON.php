<?php
$PageSecurity = 2;

include('includes/session.inc');
include ('includes/tablas.php');
$title = _('Reporte Recetas ISSSTELEON Resumen');
include('includes/header.inc');

?>
<div class="container" style="padding-top:0">
	<div class="row-fluid">
		<div class="span12">
			<style>
.form-horizontal input, .form-horizontal textarea, .form-horizontal select, .form-horizontal .help-inline, .form-horizontal .uneditable-input, .form-horizontal .input-prepend, .form-horizontal .input-append {
    display: block;
    *display: inline;
    *zoom: 1;
    /*margin-bottom: -30px;*/
    margin-bottom: 5px;
    vertical-align: middle
}
</style>
<script type="text/javascript">
<!--
$(function(){
	$('input[type=date]').map(function(){
		if($(this)[0].type!='date'){
			$(this).attr('class','date');
		}
	});
	initial();
});
//-->
</script>  
<form name="FormaEnvio" enctype="multipart/form-data" method="post" >
<div style="height: 20px;"></div>
<div class="container-fluid">
    <div class="control-group row-fluid">
        <div class="span12">
                <table class="table">
                    <thead>
                        <tr>
                            </tr><tr>
                                <th colspan="2">Reporte Recetas</th>
                            </tr>
                        
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 25%;">Fecha Inicio</td>
                            <td>
                                <input type="date" name="Fecha_1" alt='Y/m/d' >
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 25%;">Fecha Fin</td>
                            <td>
                                <input type="date" name="Fecha_2" alt='Y/m/d'>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">
                            <center><input type="submit" name="Buscar">
								<script type="text/javascript" src="javascript/descargar/csvExporter.js"></script>
                            	<script type="text/javascript" src="javascript/descargar/pdfExporter.js"></script>
                            	<script type="text/javascript">
                            	$(function(){
                            		$('csv').show();
                            		
                            	})
                            	</script>
                            	<csv style="display:none" target="Reporte Existencias(<?=date('Y-m-d')?>)" title=".AjusteIngreso"><button>Excel</button></csv>
                            	<pdf style="display:none" target="Reporte Existencias(<?=date('Y-m-d')?>)" title=".AjusteIngreso"><button>Pdf</button></pdf>
                            </center>
                            </td>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>
</div>
</form>
<div style="height: 50px;"></div>


<div style="height: 50px;"></div>
<?php
$Recetas=array();
if(isset($_REQUEST['Buscar'])){
	$Fecha1=$_REQUEST['Fecha_1'];
	$Fecha2=$_REQUEST['Fecha_2'];
	if($Fecha1=='')$Fecha1=date('Y-m-d');
	if($Fecha2=='')$Fecha2=date('Y-m-d');
	$Fecha1=date('Y-m-d',strtotime($Fecha1));
	$Fecha2=date('Y-m-d',strtotime($Fecha2));
	
	$NombreTabla="stockmoves";
	$SQL="select group_concat(DISTINCT serialno) serialno, rh_orderline,description,narrative barrcode, sum(-qty) cantidad from ".
			" stockmoves left join stockserialmoves on stockserialmoves.stockmoveno=stockmoves.stkmoveno ".
			"where type=20010 ".
			"and trandate between '".$Fecha1."' and '".$Fecha2."'".
	" group by rh_orderline";
	$Recetas=new tablas($SQL,$NombreTabla,$db);
} 
?>
<div class="container-fluid">
    <div class="control-group row-fluid">
        <div class="span12">
            <table class="AjusteIngreso table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="tableheader">id Agrupador</th>
                        <th class="tableheader">Descripci&oacute;n</th>
                        <th class="tableheader">C&oacute;digo de Barras</th>
                        <th class="tableheader">Lotes</th>
                        <th class="tableheader">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach($Recetas as $receta){ 
                ?>
					<tr>
	                    <td><?=$receta['rh_orderline']?></td>
	                    <td><?=$receta['description']?></td>
	                    <td><?php
		                    $barra=explode("::.",$receta['barrcode']);
		                    echo $barra[1];
	                    ?></td>
	                    <td><?=$receta['serialno']?></td>
	                    <td><?=$receta['cantidad']?></td>
	                </tr>
                <?php }?>
				</tbody>
            </table>
        </div>
    </div>
</div>
		</div>
	</div>
</div>
<?php 
include ('includes/footer.inc');