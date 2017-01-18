<?php
class model {
	var $db;
	/**
	 * Se necesita los datos de conexion a la DB.
	 * 
	 * @param  $db
	 */
	function model($db) {
		
		$this->db = $db;
	}
	
	/**
	 * 
	 * Almacenes
	 */
	function get_location(){
		
		$sql = "SELECT * FROM locations order by locationname"	;
		
		$result = DB_query($sql,$this->db,$ErrMsg,$DbgMsg);
		
		while ($myrow=db_fetch_array($result)) {
			
			$arrayR[$myrow['loccode']] = $myrow['locationname']; 
			
		}
		
		return $arrayR;
	}
	
	
	function report($date,$date1,$from,$to,$location,$concepto,$DetailedReport){
		$WHERE = ' ';
		if(isset($location) && $location != '' && $location != 'All'){
			$WHERE .= " AND rh_supptrans_locations.loccode =  '$location'";
		}
		
		if(isset($concepto) && $concepto != '' && $concepto != 'All'){
			if($concepto == 'v')
			$WHERE .= " AND NOW() > supptrans.duedate ";
			else 
			$WHERE .= " AND NOW() < supptrans.duedate ";
		}
		
		if(isset($date1) && $date1 != ''){
			
			$WHERE .= "AND supptrans.trandate <= '$date1'";
		}
		$sql = "select 
					supptrans.* , 
					suppliers.suppname,
					rh_supptrans_locations.loccode ,
					suppliers.supplierid 
				from suppliers, supptrans 
				left join rh_supptrans_locations ON rh_supptrans_locations.supptransid = supptrans.transno  
				WHERE  suppliers.supplierid >= '$from' 
				AND suppliers.supplierid <= '$to'  
				AND suppliers.supplierid = supptrans.supplierno 
				AND supptrans.type = 20
				AND supptrans.trandate >= '$date'
				$WHERE
				order by suppliers.suppname , supptrans.trandate
				
				";
		//echo $sql;
		$result = DB_query($sql,$this->db,$ErrMsg,$DbgMsg);
		
		while ($myrow=db_fetch_array($result)) {
			
			$array[] = $myrow;
		}
		
		return $array;
		
	}
	
	
}
/**
 * 
 * Genera los html
 * @author desarrollo05
 *
 */
class view {
//	
	var $html;
	var $rootpath;
	public function view($rootpath) {
		
		$this->rootpath = $rootpath;
		$this->html = '';
	
	}
	
	
	public function head($title) {
		
		$rootpath = $this->rootpath;
		include ('includes/header.inc');
		
		
		
		?>
<style type="text/css">
.title {
	font-size: 18px;
	text-align: center;
	font-weight: bold;
}

.tb11 {
	background: #0B615E url(img/search.png) no-repeat 4px 4px;
	color: #FFFFFF;
	padding: 4px 4px 4px 22px;
	border: 2px solid #04B4AE;
	width: 120px;
	height: 23px;
}

.col {
	background: #04B4AE;
	font-weight: bold;
	text-align: center;
	font-size: 13;
}

.col_datos {
	background: #0B615E;
	color: #FFFFFF;
	font-size: 13;
}
</style>

<script type="text/javascript" src="javascripts/jquery.js" ></script>
<script src="jscalendar/src/js/jscal2.js"></script>
<script src="jscalendar/src/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/steel/steel.css" />

<script type="text/javascript">

$(document).ready(function(){

	var cal = Calendar.setup({
	    onSelect: function(cal) { cal.hide() },
	    selection     : Calendar.dateToInt(new Date()),
	    inputField : "date",
	    
	    showTime: false
	});
	cal.setLanguage('es');
	cal.manageFields("date", "date", "%Y-%m-%d");	

	var cal = Calendar.setup({
	    onSelect: function(cal) { cal.hide() },
	    selection     : Calendar.dateToInt(new Date()),
	    inputField : "date1",
	    
	    showTime: false
	});
	cal.setLanguage('es');
	cal.manageFields("date1", "date1", "%Y-%m-%d");
});

</script>
<div class="title"><?php
		echo $title?></div>


<?php
	
	}
	/**
	 * 
	 * Método para imprimir ...
	 * @param String $title que se mostrara el título
	 */
	public function display($title) {
		
		$this->head ( $title );
		
		echo $this->html;
		$this->footer ();

	}
	
	public function footer() {
		///TODO Seria bueno imprimir datos de debug, si este estado esta activado.
		
		include ('includes/footer.inc');
	}
	
	/**
	 *  
	 * Agrega los campos de filtrado
	 * @param unknown_type $location
	 * @param unknown_type $date
	 */
	public function filtros($location = false, $date = '', $date1 = '', $locationD = '' , $concepto = 'All') {
		
		$option = '<option value="All" > '._('All').' </option> ';
		if($location)
			foreach($location as $key => $val){
			
				 	if($locationD == $key){
				 	$option .= '<option selected value="'.$key.'"> '.$val.' </option>';	
				 	}else
					$option .= '<option value="'.$key.'"> '.$val.' </option>';
			
			}
			
			$optionS = '<option value="All"> '._('All').'</option>';
			$ara  = array('v' => 'Vencido' , 'nv' => 'No vencido');
			foreach ($ara as $key => $val){
				if($concepto == $key )
				$optionS .= '<option selected value="'.$key.'"> '.$val.' </option>' ;
				else 
				$optionS .= '<option value="'.$key.'"> '.$val.' </option>' ;
			}
			
			  
		
		$this->html .= '<center> <form action="rh_agedSuppliers.php" method="post"> <table> 
						
						<tr> 
								<td> '._('From Supplier Code').' </td> 
								<td> <input tabindex="1" Type=text maxlength=6 size=7 name=FromCriteria value="0"></td>
						
						</tr>
						<tr>
								<td> '. _('To Supplier Code').'</td>
								<td> <input tabindex="2" Type=text maxlength=12 size=12 name=ToCriteria value="zzzzzz"></td>
						</tr>
						<tr> 
								<td> '._('Desde').':</td>
								<td> <input type="text" name="date" id="date" value="'.$date.'" /> </td>
						</tr>
						<tr> 
								<td> '._('Hasta').':</td>
								<td> <input type="text" name="date1" id="date1" value="'.$date1.'" /> </td>
						</tr>
						
						<tr>
								<td> '._('Almacen').': </td>
								<td> <select name="location" > '.$option.' </select></td>
						</tr>
						
						<tr>
								<td> '._('Concepto').':</td> 
								<td> <select name="concepto"> '.$optionS.'</select> </td>
						</tr>
						
						<!--<tr>
								<td> '._('Summary or Detailed Report') .' </td>
								<td> <select tabindex="5" name="DetailedReport"><option selected value="No">' . _('Summary Report') . '</option> <option value="Yes" >  '. _('Detailed Report').' </option>  </select></td>
						</tr>-->
						<tr> 
								<td colspan="2"> <center>  <input type="submit" name="action" value="Ver"  />  </center> </td>
								
						</tr>
					</table> </form></center>';
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $reporte
	 */
	public function reporte($reporte = false){
		  
		if($reporte){ 
			$this->html .= '<center><table> '; 
			$this->html .= '<tr> 
								<th> FECHA DE FACTURA  </th> 
								<th> No. DE FACTURA </th>
								<th> PROVEEDOR </th>
								<th> SUBTOTAL</th>
								<th> IVA </th>
								<th> TOTAL </th>
								<th> GRAN TOTAL </th>
								<th> FECHA DE VENCIMIENTO</th>   
							</tr>';
			$i = 1 ;
			$supId = 0;
			$grantodal = 0;
			$total = 0;
			$subtotal = 0;
			$ivatotal = 0;
			foreach($reporte as $key => $val){
				if($i == 1 ){
					$class = 'bgcolor = "#EEEEEE"'	;
					$i = 2;
				}else {
					
					$class = 'bgcolor = "#CCCCCC"'	;
					$i = 1;
				}
				
				$this->html .= '<tr '.$class.' > 
									<td> '.ConvertSQLDate($val['rh_invdate']).'</td>
									<td> ' .$val['suppreference']. ' </td>
									<td> ['.$val['supplierid'].']  ' .$val['suppname']. ' </td>
									<td> '.number_format($val['ovamount'] , 2).' </td>
									<td> '.number_format($val['ovgst'] , 2).'</td>
									';
									
				$this->html .=  '<td style="text-align:right"> ' .number_format( ($val['ovamount'] + $val['ovgst'] ) , 2 ) . ' </td>';
				$acuSupp +=  $val['ovamount'] + $val['ovgst'] ;
				$grantodal +=  $val['ovamount'] + $val['ovgst'];
				$total +=  $val['ovamount'] + $val['ovgst'];
				$subtotal += $val['ovamount'];
				$ivatotal += $val['ovgst'];
				
				if(  $supId !=  $reporte[$key+1]['supplierid'] ){
					 
					if($supId != 0){
					$supId = $reporte[$key+1]['supplierid'];
					
					$this->html .=  '<td style="text-align:right;">  '.number_format($acuSupp,2).'</td>';
					$acuSupp = 0;
					}else {
						$this->html .=  '<td>  </td>';
					$supId = $reporte[$key+1]['supplierid'];
					}
					
				}else 
					$this->html .=  '<td>  </td>';
				
				
				
				$this->html .= '
									
									<td> '.ConvertSQLDate($val['duedate']).' </td>
								</tr>';
				
			}
			
			$this->html .= '<tr> 
								<td>  </td> 
								<td>  </td>
								<td>  </td>
								<td style="text-align:right"> '.number_format( $subtotal,2 ).'</td>
								<td style="text-align:right"> '.number_format($ivatotal,2).' </td>
								<td style="text-align:right"> '.number_format($total,2).' </td>
								<td style="text-align:right"> '.number_format($grantodal,2).' </td>
								<td> </td>   
							</tr>';
			
			$this->html .= '</table></center>';
			
		}		
	}
	
}

//Controller 
$PageSecurity = 1;
include ('includes/session.inc');

if ($_REQUEST ['action'] == 'Ver') {
		//index ( $db, $rootpath, $busca );
		report($db, $rootpath, $_REQUEST['date'] , $_REQUEST['date1'] , $_REQUEST['FromCriteria'] , $_REQUEST['ToCriteria'] ,  $_REQUEST['location'] , $_REQUEST['concepto'] , $_REQUEST['DetailedReport']);
		
} else {
	
	index ( $db, $rootpath, $busca );
}

/**
 * 
 * Enter description here ...
 * @param unknown_type $db
 * @param unknown_type $rootpath
 * @param unknown_type $date
 * @param unknown_type $FromCriteria
 * @param unknown_type $ToCriteria
 * @param unknown_type $location
 * @param unknown_type $concepto
 * @param unknown_type $DetailedReport
 */
function report($db, $rootpath, $date , $date1,$FromCriteria  = '0', $ToCriteria = 'zzzzzz' , $location = 'All' , $concepto = 'v' , $DetailedReport = 'No'){
	
	$model = new model($db);
	$view = new view ( $rootpath );
	
	$report = $model->report($date,$date1,$FromCriteria,$ToCriteria,$location,$concepto,$DetailedReport);
	
	$location = $model->get_location();
	
	$view->filtros($location,$_REQUEST['date'],$_REQUEST['date1'],$_REQUEST['location'],$_REQUEST['concepto']);
	
	$view->reporte($report);
	
	$view->display ( 'Reporte de cuentas por pagar' );
}

/**
 * 
 * Punto de entrada al mod ...
 * @param DB $db Intancia de la db 
 * @param String $rootpath 
 * @param Array $busca parametros para buscar
 */
function index($db, $rootpath, $busca) {
	
	$view = new view ( $rootpath );
	$model = new model ( $db );
	
	$location = $model->get_location();
	
	$view->filtros($location,$_REQUEST['date'],$_REQUEST['date1'],$_REQUEST['location'],$_REQUEST['concepto']);
	
	
	$view->display ( 'Reporte de cuentas por pagar' );

}
?>