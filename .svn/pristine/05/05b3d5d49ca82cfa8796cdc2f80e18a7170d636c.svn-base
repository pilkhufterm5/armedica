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
	
	function detorsmaster(){
		
		
		$sql = "SELECT * FROM debtorsmaster order by name";
		
		 $respuesta  =DB_query($sql,$this->db);
	
		$tabla = array();
		while($myrow = DB_fetch_array($respuesta)){
			
			$tabla[] = $myrow;
		}
		 
		 
		 return $tabla;
	}
	
	function  list_type(){
		
		$sql = "SELECT systypes.typeid, systypes.typename FROM systypes WHERE typeid IN(SELECT type FROM debtortrans GROUP BY type)";
		
		 $respuesta  =DB_query($sql,$this->db);
	
		$tabla = array();
		while($myrow = DB_fetch_array($respuesta)){
			
			$tabla[] = $myrow;
		}
		 
		 return $tabla;
		
		
	}
	
	function find_trans($DateAfterCriteria , $DateBeforeCriteria,$type ,$deb){
		
		if(isset($type) && $type != '' && $type != 'All'){
			 
			$WHERE .= " AND debtortrans.type = '$type' ";
		}
		
		if(isset($deb)  && $deb != '' && $deb != 'All'){
			
			$WHERE .= " AND debtortrans.debtorno = '$deb'"; 
		}
		
		
		$SQL = "SELECT if(isnull(v.id_salesorders),if(isnull(cp.id_salesorders),systypes.typename,if(c.tipo_de_comprobante='ingreso','Carta Porte Ingreso', 'Carta Porte Traslado')),'Transportista') typename,
		debtortrans.id,
		debtortrans.type,
		debtortrans.transno,
		debtortrans.branchcode,
		debtortrans.trandate,
		debtortrans.reference,
		debtortrans.invtext,
		debtortrans.debtorno,
		debtortrans.debtorno,
		debtortrans.order_,
		debtortrans.rate,
		debtortrans.rh_status,
		debtorsmaster.name,
		(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount) AS totalamount,
		debtortrans.alloc AS allocated,
                not isnull(c.id) is_cfd,
                c.serie,
                c.folio,
                c.no_certificado,
                c.fk_transno,
                not isnull(cp.id_salesorders) is_carta_porte,
                not isnull(v.id_salesorders) is_transportista
	FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id
		inner join debtorsmaster on debtorsmaster.debtorno = 	debtortrans.debtorno
        left join rh_carta_porte cp on debtortrans.order_ = cp.id_salesorders
        left join rh_vps__transportista v on debtortrans.order_ = v.id_salesorders,
		systypes
	WHERE debtortrans.type = systypes.typeid 
	$WHERE
	AND (debtortrans.trandate >= '$DateAfterCriteria 00:00:00' and debtortrans.trandate <= '$DateBeforeCriteria 23:59:59' )  order by debtortrans.trandate ,  debtortrans.debtorno , debtortrans.type";
		
		 $respuesta  =DB_query($SQL,$this->db);
	
		$tabla = array();
		while($myrow = DB_fetch_array($respuesta)){
			
			$tabla[] = $myrow;
		}
		 
		 return $tabla;
		 
	}
}

class view {
	
	var $html;
	var $rootpath;
	function view($rootpath) {
		
		$this->rootpath = $rootpath;
		$this->html = '';
	
	}
	
	/**  
	 * 
	 * Método para imprimir ...
	 * @param String $title que se mostrara el título
	 */
	function display($title) {
		
		$this->head ( $title );
		
		echo $this->html;
		
		$this->footer ();
	
	}
	
	function footer() {
		///TODO Seria bueno imprimir datos de debug, si este estado esta activado.
		include ('includes/footer.inc');
		
	}
	
	function filtros($from = null , $to = null , $type = null, $debselect = null,$listType, $deb){
		
		$optionDeb = '<option value="All"> '._('All').' </option>';
		foreach ($deb as $key => $val){
			
			if($debselect == $val['debtorno'])
				$optionDeb  .= '<option selected value="'.$val['debtorno'].'"> '.$val['name'].' </option>';
			else 
				$optionDeb  .= '<option value="'.$val['debtorno'].'"> '.$val['name'].' </option>';
			

		}
		
			$option .= ' <option value="All"> '._('All').' </option>';
		if($listType)
			foreach ($listType as $key =>  $val) {
				if($type == $val['typeid'])
					$option .= ' <option selected value="'.$val['typeid'].'"> '.$val['typename'].' </option>';
				else 
					$option .= ' <option value="'.$val['typeid'].'"> '.$val['typename'].' </option>';
			}
		
			
		
		$this->html .= '<center><form action="rh_clientestrans.php" method="post" > <table>';
		
		$this->html .= '<tr> 
								<td> '._('From').' </td>
								<td>  <input type="text" name="from" id="from" value="'.$from.'"  /></td>
								<td> </td>
						</tr>
						
						<tr>
								<td> '._('to').' </td>
								<td> <input type="text" name="to" id="to" value="'.$to.'" </td>
						</tr>
						<tr>
								<td> '._('Customer').' </td>
								<td>  <select name="deb" > '.$optionDeb.' </select></td>
						</tr>
						
						<tr>
								<td> '._('Type').': </td>
								<td> <select name="type" >  '.$option.'  </select></td>
								
						</tr>
						
						
						
						<tr>
							<td>  </td>
							<td> <input type="submit" name="search" value="'._('Search').'" id="submit" /></td>
						</tr>
						
						';
		
		
		$this->html .= '</table> </form></center>';
		
	}
	
	function view_list($trans){
		
		
		$this->html .= '<center><table> <tr> 
		
							<th> '._('Type').' </th>
							<th>  '._('TransNo').'/'._('Customer').'</th>
							<th> '._('Date').' </th>
							<th>'._('Code').' </th>
							<th> '._('status').'</th>
							<th> '._('rate').'</th>
							<th> '._('Total').'</th>
							
		
						</tr>';
		
		$ini = 0;	
		$TOTAL = 0;
		if($trans){
			
			foreach ($trans as $key => $val){
			if($k == 0){
				$tr = "<tr bgcolor='EEEEEE'>";
				$k = 1;
			}else{
				$k = 0;
				$tr = "<tr bgcolor='CCCCCC' > ";
			}	
			if($ini == 0){
				
				$deb = $val['debtorno'];
				$ini = 1; 
			}
			$totaldeb += $val['totalamount'];
			
			$TOTAL += $val['totalamount'];
			
			if($deb != $trans[$key+1]['debtorno']){
				$ini 	= 0;
				if(abs($totaldeb) < 0.001 )
				$vadeb = 0;
				else
				$vadeb = number_format( $totaldeb,2) ;
				$totaldeb = 0;
				
				$barra = 'border-bottom:1px solid ';
			
			}else {
				$barra = ''; 
				$ini 	= 1;
				$vadeb = '';
			
			}
			
				
				$this->html .= $tr.' 
									<td> '.$val['typename'].' </td> 
									<td> ['.$val['transno'].'] '.$val['name'].' </td>
									<td> '.$val['trandate'].' </td>
									<td> '.$val['debtorno'].'</td>
									<td style="text-align:center"> '.$val['rh_status'].' </td>
									<td> '.$val['rate'].' </td>
									<td style="text-align:right; '.$barra.'"> '.number_format( $val['totalamount'] ,2).'</td>
									<td style="text-align:right" > '.$vadeb.'</td>
								</tr>';
				
				 
			}
			
		}
		
		
		$this->html .= ' <tr> 
		
							<td>  </td>
							<td>  </td>
							<td>  </td>
							<td> </td>
							<td> </td>
							<td> </td>
							<td> </td>
							<td style="border-top:1px solid"> '.number_format($TOTAL,2).' </td>
							
		
						</tr></table> </center>';

		//$this->html .= '<pre>' . print_r($trans,true) . '</pre>';
		
		
	}
	
	function head($title) {
		
		$rootpath = $this->rootpath;
		include ('includes/header.inc');
		
		?>
<style type="text/css">
.title {
	font-size: 18px;
	text-align: center;
	font-weight: bold;
}
</style>

<script src="jscalendar/src/js/jscal2.js"></script>
<script src="jscalendar/src/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/steel/steel.css" />

<script type="text/javascripts" src="javascripts/jquery.js"></script>

<div class="title">
<?php echo $title; ?></div>

<?php
	}

}
//Controller
$PageSecurity = 1;
include ('includes/session.inc');


if(false){
	
	
}elseif ($_REQUEST['search']){
	
	find($db,$rootpath,$_REQUEST['from'] , $_REQUEST['to'] , $_REQUEST['type'], $_REQUEST['deb']);
	
}else {
	 
	index($db,$rootpath, $_REQUEST['from'] , $_REQUEST['to'] , $_REQUEST['type'], $_REQUEST['deb']);	
}

/**
 * 
 * @param unknown_type $from
 * @param unknown_type $to
 * @param unknown_type $name
 */
function find($db,$rootpath,$from,$to,$type,$debSelect){
	
	$model = new model($db);
	
	$view = new view($rootpath);
	
	$deb = $model->detorsmaster();
	$trans = $model->find_trans($from , $to , $type , $debSelect);
	//	
	$view->filtros($from,$to,$type, $debSelect,$model->list_type(),$deb);
	
	$view->view_list($trans);
	
	$view->display(' ');
	
}
//

/**
 * 
 * Enter description here ...
 * @param unknown_type $db
 * @param unknown_type $rootpath
 */

function index($db,$rootpath,$from = null ,$to=null , $name =null , $debSelect = null){
	
	
	$model = new model($db);
	
	$view = new view($rootpath);
	
	$listaType = $model->list_type();
	$deb = $model->detorsmaster();
	
	$view->filtros($from , $to , $name , $debSelect , $listaType ,$deb );
	
	$view->display('Trans');
	
	
}

?>


<script>
var cal = Calendar.setup({
    onSelect: function(cal) { cal.hide() },
    selection     : Calendar.dateToInt(new Date()),
    inputField : "to",
    
    showTime: true
});
cal.setLanguage('es');
cal.manageFields("to", "to", "%Y-%m-%d");

var cal = Calendar.setup({
    onSelect: function(cal) { cal.hide() },
    selection     : Calendar.dateToInt(new Date()),
    inputField : "from",
    
    showTime: true
});
cal.setLanguage('es');
cal.manageFields("from", "from", "%Y-%m-%d");


</script>