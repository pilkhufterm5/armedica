<?php
$PageSecurity = 2;

include('includes/session.inc');
include ('includes/tablas.php');
$title = _('Reporte Maximos y Minimos');
include('includes/header.inc');

$NombreTabla="rh_locstock_max_min_agr";
$encabezados=array(
		'locationname'=>'Almacen',
		'Id Agrupador'=>'Id Agrupador',
		'Maximo'=>'Maximo',
		'Minimo'=>'Minimo',
);
$SQL="select locations.locationname ,rh_locstock_max_min_agr.id_agrupador 'Id Agrupador',  ".
" rh_locstock_max_min_agr.maximo Maximo, rh_locstock_max_min_agr.minimo Minimo from ".
	$NombreTabla.' rh_locstock_max_min_agr left join locations on locations.loccode=rh_locstock_max_min_agr.loccode ';
$MaximosMinimos=new tablas($SQL,$NombreTabla,$encabezados,$db);
?>
<div class="container-fluid">
    <div class="control-group row-fluid">
        <div class="span12">
            <table class="AjusteIngreso table table-striped table-hover">
                <thead>
                    <tr>
                    <th class="tableheader">
                    <?=implode('</th><th class="tableheader">',$MaximosMinimos->getHeaders()); ?>
                     </th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach($MaximosMinimos as $linea){
                ?><tr>
					<?php foreach($linea as $celda){?>
	                    <td><?=htmlentities($celda)?></td>
	                    <?php }?>
	                </tr>
                <?php }?>
				</tbody>
            </table>
        </div>
    </div>
</div>
<?php 
include ('includes/footer.inc');