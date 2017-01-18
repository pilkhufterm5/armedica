<?php
$PageSecurity = 2;

include('includes/session.inc');
include ('includes/tablas.php');
$title = _('Reporte Recetas ISSSTELEON Detalle');
include('includes/header.inc');

?>
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
                            <td style="width: 25%;">Receta</td>
                            <td>
                                <input type="text" name="receta" >
                            </td>
                        </tr>
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
                            	<csv style="display:none" target="Receta det(<?=date('Y-m-d')?>)" title=".AjusteIngreso"><button>Excel</button></csv>
                            	<pdf style="display:none" target="Receta det(<?=date('Y-m-d')?>)" title=".AjusteIngreso"><button>Pdf</button></pdf>
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
			" where type=20010 ".
			"and trandate between '".$Fecha1."' and '".$Fecha2."'".
	" group by rh_orderline";
	$SQL="select rh_orderline,description,reference,trandate,narrative, -sum(qty) cantidad, narrative barrcode, group_concat(DISTINCT stockserialmoves.serialno) serialno ".
			"from stockmoves ".
			" left join stockserialmoves on stockserialmoves.stockmoveno=stockmoves.stkmoveno ".
			"where type=20010 ".
	(isset($_REQUEST['Fecha_1'])?" and trandate between '".$Fecha1."' and '".$Fecha2."'":"").
	(isset($_REQUEST['receta'])&&trim($_REQUEST['receta'])!=''?
			" and reference='".DB_escape_string(($_REQUEST['receta']))."'"
			:"").
	"group by reference,rh_orderline;";
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
                        <th class="tableheader">C&oacute;digo de Barras</th>
                        <th class="tableheader">Descripci&oacute;n</th>
                        <th class="tableheader">Receta</th>
                        <th class="tableheader">Fecha</th>
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
	                    <td><?php
		                    $barra=explode("::.",$receta['barrcode']);
		                    echo trim($barra[1]);
	                    ?></td>
	                    <td><?=$receta['description']?></td>
	                    <td><?=$receta['reference']?></td>
	                    <td><?=$receta['trandate']?></td>
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
