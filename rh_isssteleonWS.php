<?php

$PageSecurity = 2;

include('includes/session.inc');
include ('includes/tablas.php');
$title = _('Detalle de sincronizaci&oacute;n ISSSTELEON');
include('includes/header.inc');


$Header=array(
	'MedicamentoId'=>'ID Agrupador',
	'RecetaId'=>'Receta',
	'CodigoBarras'=>'Codigo de Barras',
	'UnidadesSurtidas'=>'Unidades Surtidas',
	'Medicamento'=>'Medicamento',
	'Comentarios'=>'Comentarios',
	'FechaInicial'=>'Fecha Receta',
	'FechaCreacion'=>'Fecha Registro Receta',
	'trandate'=>'Fecha Movimiento',
	'transno'=>'Transno',
	'stockid'=>'Articulo',
	'stkmoveno'=>'Estatus',
);

	$Fecha1=$_REQUEST['Fecha_1'];
	$Fecha2=$_REQUEST['Fecha_2'];
	if($Fecha1=='')$Fecha1=date('Y-m-d');
	if($Fecha2=='')$Fecha2=date('Y-m-d');
	$Fecha1=date('Y-m-d',strtotime($Fecha1));
	$Fecha2=date('Y-m-d',strtotime($Fecha2));


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
.cancel, .exitoso{
	background-image: url('css/silverwolf/images/cancel.gif');
	text-indent: -800px;
	overflow: hidden;
	width: 16px;
	height: 16px;
}
.exitoso{
	background-image: url('css/valid.png');
	width: 12px;
	height: 11px;
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
                                <th colspan="2"><?php echo $title;?></th>
                            </tr>
                        
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 25%;">Fecha Inicio</td>
                            <td>
                                <input type="date" name="Fecha_1" alt='Y/m/d' value="<?php echo $Fecha1?>" >
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 25%;">Fecha Fin</td>
                            <td>
                                <input type="date" name="Fecha_2" alt='Y/m/d' value="<?php echo $Fecha2?>">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 25%;">Con Problemas</td>
                            <td>
                                <input type="checkbox" name="Problemas" value="1 "<?php if(isset($_REQUEST['Problemas'])) echo ' checked=checked ';?>>
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
                            	<csv style="display:none" target="ISSSTELEON(<?=date('Y-m-d')?>)" title=".AjusteIngreso"><button>Excel</button></csv>
                            	<pdf style="display:none" target="ISSSTELEON(<?=date('Y-m-d')?>)" title=".AjusteIngreso"><button>Pdf</button></pdf>
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
	
	$NombreTabla="rh_isssteleonWS";
	$SQL="select ".
			"WS.*, ".
			"stockmoves.stkmoveno, ".
			"stockmoves.trandate, ".
			"stockmoves.transno, ".
			"stockmoves.stockid stockidmv, ".
			"stm.stockid ".
			"from rh_isssteleonWS WS ".
			"left join stockmaster stm on stm.barcode=WS.CodigoBarras ".
			"left join stockmoves on stockmoves.reference=WS.RecetaId and stockmoves.narrative like concat('%::.[',WS.CodigoBarras,']%') and type=20010 ".
	" where ".
	"WS.FechaInicial between '$Fecha1' and '$Fecha2'";
	if(isset($_REQUEST['Problemas'])){
		$SQL.=" having stockidmv is null";
		//$SQL.="  or stockid is null "; // Se quita restriccion de movimiento que no exista el codigo de barras
	}
	$Recetas=new tablas($SQL,$NombreTabla,$db);
}
?>
<div class="container-fluid">
    <div class="control-group row-fluid">
        <div class="span12">
            <table class="AjusteIngreso table table-striped table-hover">
                <thead>
                    <tr>
                    	<?php foreach($Header as $head){?>
                        <th class="tableheader"><?php echo $head;?></th>
                        <?php }?>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach($Recetas as $receta){ 
                ?>
					<tr>
						<?php foreach($Header as $llave=>$head){?>
	                    <td><?php 
	                    	switch($llave){
								case 'FechaInicial':
								case 'FechaCreacion':
								case 'trandate':
									if ($receta[$llave]!='')
										$receta[$llave]=date($_SESSION['DefaultDateFormat'],strtotime($receta[$llave]));
									break;
								case 'stkmoveno':
									if($receta[$llave]==''){
										$receta[$llave]='<div class="cancel">'._('Error').'</div>';
									}else
										$receta[$llave]='<div class="exitoso">'._('Exitoso').'</div>';
									break;
								case 'stockid':
									if($receta['stockid']==''){
										if($receta['stockidmv']!='')
											$receta[$llave].=$receta['stockidmv'];
										else 
											$receta[$llave].='<div class="cancel">No Existente</div>';
									}
							}
	                    	echo $receta[$llave];
	                    ?></td>
	                    <?php }?>
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