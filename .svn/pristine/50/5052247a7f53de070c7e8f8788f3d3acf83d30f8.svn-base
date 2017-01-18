<?php
/*
 * RealHost
 * 3 de Mayo del 2010
 * Ricardo Abularach Garcia
 * seccion para la adminsitracion
 */

 class SaleFail{
   private $debtorno;
   private $stockId;
   private $qtY;
   function SaleFail($debtorno){
        $this->stockId= array();
        $this->qtY= array();
        $this->debtorno=$debtorno;
   }
   function addStock($stockid,$qty){
       array_push($this->stockId,$stockid);
       array_push($this->qtY,$qty);
   }

   function getStockCount(){
     return count($this->stockId);
   }

   function getStockById($idx){
     return $this->stockId[$idx]; ;
   }

   function getQtyById($idx){
     return $this->qtY[$idx];
   }

   function getCliente(){
        return $this->debtorno;
   }
 }

$PageSecurity = 2;
include('includes/session.inc');
$title = _('Ventas perdidas');
include('includes/header.inc');


/*se agregan los datos necesarias para los combobox*/
echo "<script language='javascript' src='javascripts/funciones.js'></script>";
echo "<script language='javascript' src='javascripts/overlib_mini.js'></script>";
echo "<script language='javascript' src='javascripts/prototype.js'></script>";
echo "<script language='javascript' src='javascripts/cboOtro.js'></script>";
echo "<script language='javascript' src='javascripts/jquery.js'></script>";
echo "<script language='javascript' src='javascripts/calendar.js'></script>";
echo "<script language='javascript' src='javascripts/lang/calendar-es.js'></script>";
echo "<script language='javascript' src='javascripts/calendar-setup.js'></script>";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"javascripts/calendar.css\"/>";
echo "<script>jQuery.noConflict();</script>";

echo "<script>";

    echo "
        function can_ce_lar(){
            window.location.href = \"rh_llamadas.php?\";
            return false;
        }
        ";

    echo "
        window.onload = function(){
           jQuery(\"#guardar\").submit(function(){
                var tipo = jQuery(\"#tipo\").val();
                var categoria = jQuery(\"#categoria\").val();
                var asignar = jQuery(\"#asignar\").val();
                var fecha_hora = jQuery(\"#fecha_hora\").val();
                var comentarios = jQuery(\"#ids_0\").val();
                var tipo = document.guardar.tipo.options[document.guardar.tipo.selectedIndex].value;
                var categoria = document.guardar.categoria.options[document.guardar.categoria.selectedIndex].value;
                var empleado = document.guardar.rh_empleado.options[document.guardar.rh_empleado.selectedIndex].value;
                var error = 0;
                var mensaje = \"No se pudo procesar tu solicitud por las siguientes razones:\\n\";

                if(tipo == \"-9\"){
                    error = 1;
                    mensaje += \" - El Campo de Tipo de Llamada se encuentra vacio\\n\";
                }

                if(categoria == \"-9\" || categoria == \"Selecciona\"){
                    error = 1;
                    mensaje += \" - El Campo de Categoria se encuentra vacio\\n\";
                }

                if(empleado== \"-9\"){
                    error = 1;
                    mensaje += \" - El Campo Asignar a se encuentra vacio\\n\";
                }

                if(error == 1){
                    alert(mensaje);
                    return false;
                }else{
                    return true;
                }
            });
        };
        ";

echo    "function charge(obj){
            var id = obj.options[obj.selectedIndex].value;
            var tip = document.getElementById('tipo').value;
            window.location.href = \"rh_llamadas.php?action=add&id=\"+id+\"&tipo=\"+tip;
        }";

echo    "function    selection(obj){
            var sel =documento.getElementById('categoria');
            sel.selectedIndex=0;
}";

echo "</script>";

$generales = "<span style='color:red !important;'>* </span>";
if(isset($_GET['action'])){
    /*aki es donde entra cuando hay acciones que ejecutar*/
    if($_GET['action'] == "add"){
        unset($_SESSION['SalesFail']);
    }
}

if(isset($_POST['Save'])){
    for ($x=0;$x<$_SESSION['SalesFail']->getStockCount();$x++){
        $sql="insert into rh_salesfails (debtorno,stockid,qty,fecha,userid) values('".$_SESSION['SalesFail']->getCliente()."','".$_SESSION['SalesFail']->getStockById($x)."','".$_SESSION['SalesFail']->getQtyById($x)."',now(),'".$_SESSION['UserID']."')";
        $result = DB_query($sql, $db);
    }
    unset($_SESSION['SalesFail']);
}
//print_r($_SESSION);

if(isset($_GET['action'])){
    /*se definene como las acciones que se tieene que realizar para ejecutar los datos*/
    switch($_GET['action']){
        case "insert":
            /*para el caso que se van a ingresar los datos con los cuales stamos trabajando*/
            /*se recuperan los valores via post con los cuales se estan trabajando*/
            $tipo = $_POST['tipo'];
            $categoria = $_POST['categoria'];
            $nombre = $_POST['nombre'];
            $empresa = $_POST['empresa'];
            $telefono = $_POST['telefono'];
            $comentario = $_POST['comentario'];
            $asignar = $_POST['asignar'];
            $fecha_hora = $_POST['fecha_hora'];
            $notify = $_POST['notify'];
            $notifyEmergency = $_POST['notifyEmergency'];
            $userid = $_SESSION['UserID'];
            //se agregan los nuevos campos a la seccion de la llamada telefonica
            $rh_empleado = $_POST['rh_empleado'];
            $rh_paciente = $_POST['rh_paciente'];

            if($rh_paciente == ""){
                /*cuando esta vacio*/
                $tipo_a = "";
                $paciente = "";
            }else{
                $dividir = explode("**", $rh_paciente);
                $tipo_a = $dividir[0];
                $paciente  = $dividir[1];
            }

            /*se hace el insert correspondiete a la base de datos*/
            $sql_insert = "INSERT INTO rh_llamadasRegistro(userid, alta, tipollamda, categoriallamada, nombre, empresa, comentarios, asignada, fechasignada, telefono, paci_pros, paci_pros_tipo, empleado)
                                                    VALUES('".$userid."', '".date('Y-m-d H:i:s')."', '".$tipo."', '".$categoria."', '".$nombre."', '".$empresa."', '".$comentario."', '".$asignar."', '".$fecha_hora."', '".$telefono."', '".$paciente."', '".$tipo_a."', '".$rh_empleado."')";
            DB_query($sql_insert,$db);

            if($notifyEmergency=='1'){
                $notifyEmergency = "2";
            }else {
                $notifyEmergency = "1";
            }
            $subjet='';
            if($_POST['categoria']=='1'){
                $sql_insert = "select name from debtorsmaster where debtorno ='".$paciente."'";
                $array = DB_query($sql_insert,$db);
                if($garray = DB_fetch_array($array)){

                }
                $subjet='Llamada de Paciente '.$garray['name'];
            }elseif($_POST['categoria']=='2'){
                $sql_insert = "select first_name,last_name from debtorsmaster where id =".$paciente;
                $array = DB_query($sql_insert,$db);
                if($garray = DB_fetch_array($array)){

                }
                $subjet='Llamada de Prospecto '.$garray['first_name'].' '.$garray['last_name'];
            }elseif($_POST['categoria']=='3'){
                $subjet='Llamada Personal';
            }

            if($notify=='1'){
                $sql_insert = "INSERT INTO rh_messages(to_user, from_user,send_at, `subject`, `status`, priority,message)
                VALUES('".$rh_empleado."','".$userid."', '".date('Y-m-d H:i:s')."', '".$subjet."',0,".$notifyEmergency.", '".$comentario."')";
                DB_query($sql_insert,$db);

            }

            /*ya se hizo el insert a la base de datos, ya por lo cual se tiene que hacer el redirecionamiento*/
            echo "<meta http-equiv=\"Refresh\" content=\"0;url=rh_llamadas.php\" />";
            exit();
            break;
    }
}

echo '<A HREF="'. $rootpath . '/index.php">'. _('Regresar'). '</A><BR>';

echo "<center>";
echo "<br /><h3 style='margin-bottom:2px !important;'>Ventas perdidas</h3>";
echo "<a href='rh_venta_perdida.php?action=add'>Agregar venta perdida</a><br/>";
/*seccion para buscar informacion dentro de la tabla*/
echo "<br /><br />";
echo "<form name='buscar' id='buscar' class='buscar' action='rh_venta_perdida.php?action=search' method='POST'>";
echo "<table>";
    echo "<tr>";
        echo "<td colspan=2 align='center'>B&uacute;squeda de Registros</td>";
    echo "</tr>";

    echo "<tr>";
        echo "<td colspan=2 align='center'><input type='text' tabindex='8' name='fecha_hora_i' id='fecha_hora_i' class='fecha_hora_i' value='".date('Y-m-d H:i:s')."' readonly='readonly' /><button type='reset' id='trigger_a'>...</button><script type='text/javascript'>Calendar.setup({ inputField:'fecha_hora_i', ifFormat:\"%Y-%m-%d %H:%M:%S\", showsTime: true, button:'trigger_a' });</script> a <input type='text' tabindex='8' name='fecha_hora_f' id='fecha_hora_f' class='fecha_hora_f' value='".date('Y-m-d H:i:s')."' readonly='readonly' /><button type='reset' id='trigger_b'>...</button><script type='text/javascript'>Calendar.setup({ inputField:'fecha_hora_f', ifFormat:\"%Y-%m-%d %H:%M:%S\", showsTime: true, button:'trigger_b' });</script> </td>";
    echo "</tr>";

   /* echo "<tr>";
        echo "<td colspan=2 align='center'>Palabra a Buscar<br /><input type='text' id='keyvalue' class='keyvalue' name='keyvalue' value='' /></td>";
    echo "</tr>";           */

    echo "<tr>";
        echo "<td colspan=2 align='center'><input type='submit' name='submit' id='submit' class='submit' value='Buscar' /></td>";
    echo "</tr>";

echo "</table>";
echo "</form>";


/*dependiendo de la accion con la cual se va a trabajar entonces es lo que se va a visualziar*/
$msg="";
if (!isset($_SESSION['CustomerID'])){ //initialise if not already done
	$_SESSION['CustomerID']="";
}

if (!isset($_POST['PageOffset'])) {
  $_POST['PageOffset'] = 1;
} else {
  if ($_POST['PageOffset']==0) {
    $_POST['PageOffset'] = 1;
  }
}

if (isset($_POST['Search']) OR isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])){
	if (isset($_POST['Search'])){
		$_POST['PageOffset'] = 1;
	}
	If ($_POST['Keywords'] AND $_POST['CustCode']) {
		$msg=_('Customer name keywords have been used in preference to the customer code extract entered') . '.';
		$_POST['Keywords'] = strtoupper($_POST['Keywords']);
	}
	If ($_POST['Keywords']=="" AND $_POST['CustCode']=="") {
		//$msg=_('At least one Customer Name keyword OR an extract of a Customer Code must be entered for the search');
		$SQL= "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno";
	} else {
		If (strlen($_POST['Keywords'])>0) {

			$_POST['Keywords'] = strtoupper($_POST['Keywords']);

			//insert wildcard characters in spaces

			$i=0;
			$SearchString = "%";
			while (strpos($_POST['Keywords'], " ", $i)) {
				$wrdlen=strpos($_POST['Keywords']," ",$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . "%";
				$i=strpos($_POST['Keywords']," ",$i) +1;
			}
			$SearchString = $SearchString . substr($_POST['Keywords'],$i)."%";
			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.name " . LIKE . " '$SearchString'";

		} elseif (strlen($_POST['CustCode'])>0){

			$_POST['CustCode'] = strtoupper($_POST['CustCode']);

			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.debtorno " . LIKE  . " '%" . $_POST['CustCode'] . "%'";
		}
	} //one of keywords or custcode was more than a zero length string
	$ErrMsg = _('The searched customer records requested cannot be retrieved because');
	$result = DB_query($SQL,$db,$ErrMsg);
	if (DB_num_rows($result)==1){

	} elseif (DB_num_rows($result)==0){
		prnMsg(_('No customer records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
	}$msg="";
if (!isset($_SESSION['CustomerID'])){ //initialise if not already done
	$_SESSION['CustomerID']="";
}

if (!isset($_POST['PageOffset'])) {
  $_POST['PageOffset'] = 1;
} else {
  if ($_POST['PageOffset']==0) {
    $_POST['PageOffset'] = 1;
  }
}

if (isset($_POST['Search']) OR isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])){
	if (isset($_POST['Search'])){
		$_POST['PageOffset'] = 1;
	}
	If ($_POST['Keywords'] AND $_POST['CustCode']) {
		$msg=_('Customer name keywords have been used in preference to the customer code extract entered') . '.';
		$_POST['Keywords'] = strtoupper($_POST['Keywords']);
	}
	If ($_POST['Keywords']=="" AND $_POST['CustCode']=="") {
		//$msg=_('At least one Customer Name keyword OR an extract of a Customer Code must be entered for the search');
		$SQL= "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno";
	} else {
		If (strlen($_POST['Keywords'])>0) {

			$_POST['Keywords'] = strtoupper($_POST['Keywords']);

			//insert wildcard characters in spaces

			$i=0;
			$SearchString = "%";
			while (strpos($_POST['Keywords'], " ", $i)) {
				$wrdlen=strpos($_POST['Keywords']," ",$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . "%";
				$i=strpos($_POST['Keywords']," ",$i) +1;
			}
			$SearchString = $SearchString . substr($_POST['Keywords'],$i)."%";
			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.name " . LIKE . " '$SearchString'";

} //end of if search
		}
	}
}

if(isset($_POST['Select'])||(isset($_SESSION['SalesFail']))){
if (isset($_POST['PartSearch']) && $_POST['PartSearch']!='' || !isset($_POST['QuickEntry'])){
  if(!isset($_SESSION['SalesFail'])){
    $_SESSION['SalesFail']=new SaleFail($_POST['Select']);
  }

  If (isset($_POST['orderMy'])&&(isset($_SESSION['SalesFail']))){
	     $Discount = 0;
	     $i=1;
	      while (isset($_POST['val' . $i])) {
			$QuickEntryCode = $_POST['itm' . $i];
			$QuickEntryQty = $_POST['val' . $i];
            if($_POST['val' . $i]>0){
                $_SESSION['SalesFail']->addStock($QuickEntryCode,$QuickEntryQty);
            }
            $i++;
          }
   }

        ?>
         <form name="orderform" method="POST">
        <?php
		echo '<input type="hidden" name="PartSearch" value="' .  _('Yes Please') . '">';

		$SQL="SELECT categoryid,
				categorydescription
			FROM stockcategory
			WHERE stocktype='F' OR stocktype='D'
			ORDER BY categorydescription";
		$result1 = DB_query($SQL,$db);

		echo '<B>' . $msg . '</B><BR><CENTER><b>' . _('Search for Order Items') . '</b><TABLE><TR><TD><FONT SIZE=2>' . _('Select a Stock Category') . ':</FONT><SELECT TABINDEX=1 NAME="StockCat">';

		if (!isset($_POST['StockCat'])){
			echo "<OPTION SELECTED VALUE='All'>" . _('All');
			$_POST['StockCat'] ='All';
		} else {
			echo "<OPTION VALUE='All'>" . _('All');
		}

		while ($myrow1 = DB_fetch_array($result1)) {

			if ($_POST['StockCat']==$myrow1['categoryid']){
				echo '<OPTION SELECTED VALUE=' . $myrow1['categoryid'] . '>' . $myrow1['categorydescription'];
			} else {
				echo '<OPTION VALUE='. $myrow1['categoryid'] . '>' . $myrow1['categorydescription'];
			}
		}

		?>

		</SELECT>
		<TD><FONT SIZE=2><?php echo _('Enter partial'); ?> <?php echo _('Description'); ?>:</FONT></TD>
		<TD><INPUT TABINDEX=2 TYPE="Text" NAME="Keywords" SIZE=20 MAXLENGTH=25 VALUE="<?php if (isset($_POST['Keywords'])) echo $_POST['Keywords']; ?>"></TD></TR>
		<TR><TD></TD>
		<TD><FONT SIZE 3><B><?php echo _('OR'); ?> </B></FONT><FONT SIZE=2><?php echo _('Enter partial'); ?> <?php echo _('Stock Code'); ?>:</FONT></TD>
		<TD><INPUT TABINDEX=3 TYPE="Text" NAME="StockCode" SIZE=15 MAXLENGTH=18 VALUE="<?php if (isset($_POST['StockCode'])) echo $_POST['StockCode']; ?>"></TD>
		</TR>
		</TABLE>
		<CENTER><INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Search Now'); ?>">
		<INPUT TYPE=SUBMIT Name="QuickEntry" VALUE="<?php echo _('Use Quick Entry'); ?>">
        <?php
            if($_SESSION['SalesFail']->getStockCount()>0){
               echo '<INPUT TYPE="SUBMIT" NAME="Save" VALUE="Guardar">';
            }
        ?>
        </FORM></CENTER>
		<script language='JavaScript' type='text/javascript'>
            	document.forms[0].StockCode.select();
            	document.forms[0].StockCode.focus();
		</script>

		<?php
        }
}

//********************************************************************************************************
//var_dump($_SESSION['SalesFail']);
	If ((isset($_POST['Search']) or isset($_POST['Next']) or isset($_POST['Prev']))&&(isset($_SESSION['SalesFail']))){
		If (isset($_POST['Keywords']) AND isset($_POST['StockCode'])) {
			$msg='<BR>' . _('Stock description keywords have been used in preference to the Stock code extract entered') . '.';
		}
		If (isset($_POST['Keywords']) AND strlen($_POST['Keywords'])>0) {
			//insert wildcard characters in spaces
			$_POST['Keywords'] = strtoupper($_POST['Keywords']);

			$i=0;
			$SearchString = '%';
			while (strpos($_POST['Keywords'], ' ', $i)) {
				$wrdlen=strpos($_POST['Keywords'],' ',$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . '%';
				$i=strpos($_POST['Keywords'],' ',$i) +1;
			}
			$SearchString = $SearchString. substr($_POST['Keywords'],$i).'%';

			if ($_POST['StockCat']=='All'){
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster,
						stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.description " . LIKE . " '$SearchString'
					AND stockmaster.discontinued = 0
					ORDER BY stockmaster.stockid";
			} else {
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE  stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.description " . LIKE . " '" . $SearchString . "'
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					AND stockmaster.discontinued = 0
					ORDER BY stockmaster.stockid";
			}

		} elseif (strlen($_POST['StockCode'])>0){

			$_POST['StockCode'] = strtoupper($_POST['StockCode']);
			$SearchString = '%' . $_POST['StockCode'] . '%';

			if ($_POST['StockCat']=='All'){
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
					AND stockmaster.discontinued = 0
					ORDER BY stockmaster.stockid";
			} else {
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					AND stockmaster.discontinued = 0
					ORDER BY stockmaster.stockid";
			}

		} else {
			if ($_POST['StockCat']=='All'){
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE  stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.discontinued = 0
					ORDER BY stockmaster.stockid";
			} else {
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					AND stockmaster.discontinued = 0
					ORDER BY stockmaster.stockid";
			  }
		}

		if (isset($_POST['Next'])) {
			$Offset = $_POST['nextlist'];
		}
		if (isset($_POST['Prev'])) {
			$Offset = $_POST['previous'];
		}
		if (!isset($Offset) or $Offset<0) {
			$Offset=0;
		}
		$SQL = $SQL . ' LIMIT ' . $_SESSION['DisplayRecordsMax'].' OFFSET '.number_format($_SESSION['DisplayRecordsMax']*$Offset);

		$ErrMsg = _('There is a problem selecting the part records to display because');
		$DbgMsg = _('The SQL used to get the part selection was');
		$SearchResult = DB_query($SQL,$db,$ErrMsg, $DbgMsg);

		if (DB_num_rows($SearchResult)==0 ){
			prnMsg (_('There are no products available meeting the criteria specified'),'info');

			if ($debug==1){
				prnMsg(_('The SQL statement used was') . ':<BR>' . $SQL,'info');
			}
		}
		if (DB_num_rows($SearchResult)==1){
			$myrow=DB_fetch_array($SearchResult);
			$NewItem = $myrow['stockid'];
			DB_data_seek($SearchResult,0);
		}
		if (DB_num_rows($SearchResult)<$_SESSION['DisplayRecordsMax']){
			$Offset=0;
		}

	} //end of if search

//********************************************************************************************************

if(isset($_GET['action'])){
    /*aki es donde entra cuando hay acciones que ejecutar*/
    if($_GET['action'] == "add"){
        /*se agrego la seccion para poder agregar nuevos registros al catalogo de llamadas*/
        echo "<hr / style='width:50%'>";
        echo "<br />";
        echo "<p>Registro de venta perdida</p>";
        echo "<form name='guardar' id='guardar' class='guardar' action='rh_venta_perdida.php?action=insert' method='POST' >";
            echo "<table cellpadding=\"5\" cellspacing=\"5\" width=\"700px\">";

            echo "</table>";
        echo "</form>";
    }elseif($_GET['action'] == "search"){
        echo "<p>Consulta de Resultados</p>";
        echo "<p>Rango de Fechas ".$_POST['fecha_hora_i']." a ".$_POST['fecha_hora_f']."</p>";
        /*echo "<p>Palabra a Buscar: ".$_POST['keyvalue']."</p>";*/
        echo "<p>Resultados</p>";
        echo "<hr / style='width:50%'>";
        echo "<br />";
        echo "<table COLSPAN=2 BORDER=2 CELLPADDING=4>";
            echo "<tr>";
                echo "<th>Fecha</th>";
                echo "<th>Cliente</th>";
                echo "<th>Art&iacute;culo</th>";
                echo "<th>Cantidad</th>";
            echo "</tr>";

            if($_POST['keyvalue'] == ""){
                $_POST['keyvalue'] = "%";
            }

            $sql_ = "Select debtorsmaster.name,stockmaster.description,rh_salesfails.qty,rh_salesfails.fecha from rh_salesfails join debtorsmaster on rh_salesfails.debtorno = debtorsmaster.debtorno join stockmaster on rh_salesfails.stockid=stockmaster.stockid WHERE (rh_salesfails.fecha >= '".$_POST['fecha_hora_i']."' AND rh_salesfails.fecha <= '".$_POST['fecha_hora_f']."')";
            $resultados = DB_query($sql_,$db);
            $registros = DB_num_rows($resultados);

            if($registros == 0){
                echo "<tr>";
                    echo "<td colspan=11 align='center'>Sin Registros</td>";
                echo "</tr>";
            }else{
                while ($myrow=DB_fetch_array($resultados)){
                    echo "<tr>";
                        echo "<td>".$myrow['fecha']."</td>";
                        echo "<td>".$myrow['name']."</td>";
                        echo "<td>".$myrow['description']."</td>";
                        echo "<td align='right'><strong>".$myrow['qty']."</strong></td>";
                    echo "</tr>";
                }
            }
        echo "</table>";
    }

}else{
    /*aki es donde entra cuando no hay acciones que ejecutar*/
}

if(!isset($_SESSION['SalesFail'])&&($_GET['action'] != "search")){
?>

<FORM ACTION="<?php echo $_SERVER['PHP_SELF'] . '?' . SID; ?>" METHOD=POST>
<CENTER>
<B><?php echo "<H2><B>Codigo Clientes Varios </B></H2>".$msg; ?></B>
<TABLE CELLPADDING=3 COLSPAN=4>
<TR>
<TD><?php echo _('Text in the'); ?> <B><?php echo _('name'); ?></B>:</TD>
<TD>
<?php
if (isset($_POST['Keywords'])) {
?>
<INPUT TYPE="Text" NAME="Keywords" value="<?php echo $_POST['Keywords']?>" SIZE=20 MAXLENGTH=25>
<?php
} else {
?>
<INPUT TYPE="Text" NAME="Keywords" SIZE=20 MAXLENGTH=25>
<?php
}
?>
</TD>
<TD><FONT SIZE=3><B><?php echo _('OR'); ?></B></FONT></TD>
<TD><?php echo _('Text extract in the customer'); ?> <B><?php echo _('code'); ?></B>:</TD>
<TD>
<?php
if (isset($_POST['CustCode'])) {
?>
<INPUT TYPE="Text" NAME="CustCode" value="<?php echo $_POST['CustCode'] ?>" SIZE=15 MAXLENGTH=18>
<?php
} else {
?>
<INPUT TYPE="Text" NAME="CustCode" SIZE=15 MAXLENGTH=18>
<?php
}
?>
</TD>
</TR>
</TABLE>
<INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Show All'); ?>">
<INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Search Now'); ?>">
<INPUT TYPE=SUBMIT ACTION=RESET VALUE="<?php echo _('Reset'); ?>"></CENTER>
<?php
}
		if (isset($SearchResult)) {

			echo '<CENTER><form name="orderform" method="POST"><TABLE CELLPADDING=2 COLSPAN=7 >';
			$TableHeader = '<TR><TD class="tableheader">' . _('Code') . '</TD>
                          			<TD class="tableheader">' . _('Description') . '</TD>
                          			<TD class="tableheader">' . _('Units') . '</TD>
                          			<TD class="tableheader">' . _('On Hand') . '</TD>
                          			<TD class="tableheader">' . _('On Demand') . '</TD>
                          			<TD class="tableheader">' . _('On Order') . '</TD>
                          			<TD class="tableheader">' . _('Available') . '</TD>
                          			<TD class="tableheader">' . _('Quantity') . '</TD></TR>';
			echo $TableHeader;
			$j = 1;
			$k=0; //row colour counter

			while ($myrow=DB_fetch_array($SearchResult)) {
// This code needs sorting out, but until then :
				$ImageSource = _('No Image');

/*
				if (function_exists('imagecreatefrompng') ){
					$ImageSource = '<IMG SRC="GetStockImage.php?SID&automake=1&textcolor=FFFFFF&bgcolor=CCCCCC&StockID=' . urlencode($myrow['stockid']). '&text=&width=64&height=64">';
				} else {
					if(file_exists($_SERVER['DOCUMENT_ROOT'] . $rootpath. '/' . $_SESSION['part_pics_dir'] . '/' . $myrow['stockid'] . '.jpg')) {
						$ImageSource = '<IMG SRC="' .$_SERVER['DOCUMENT_ROOT'] . $rootpath . '/' . $_SESSION['part_pics_dir'] . '/' . $myrow['stockid'] . '.jpg">';
					} else {
						$ImageSource = _('No Image');
					}
				}

*/
				// Find the quantity in stock at location
				$qohsql = "SELECT sum(quantity)
						   FROM locstock
						   WHERE stockid='" .$myrow['stockid'] . "' AND
						   loccode = '" . $_SESSION['Items']->Location . "'";
				$qohresult =  DB_query($qohsql,$db);
				$qohrow = DB_fetch_row($qohresult);
				$qoh = $qohrow[0];

				// Find the quantity on outstanding sales orders
				$sql = "SELECT SUM(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS dem
            			     FROM salesorderdetails,
                      			salesorders
			                 WHERE salesorders.orderno = salesorderdetails.orderno AND
            			     salesorders.fromstkloc='" . $_SESSION['Items']->Location . "' AND
 			                salesorderdetails.completed=0 AND
		 					salesorders.quotation=0 AND
                 			salesorderdetails.stkcode='" . $myrow['stockid'] . "'";

				$ErrMsg = _('The demand for this product from') . ' ' . $_SESSION['Items']->Location . ' ' .
				     _('cannot be retrieved because');
				$DemandResult = DB_query($sql,$db,$ErrMsg);

				$DemandRow = DB_fetch_row($DemandResult);
				if ($DemandRow[0] != null){
				  $DemandQty =  $DemandRow[0];
				} else {
				  $DemandQty = 0;
				}

				// Find the quantity on purchase orders
				$sql = "SELECT SUM(purchorderdetails.quantityord-purchorderdetails.quantityrecd) AS dem
            			     FROM purchorderdetails
			                 WHERE purchorderdetails.completed=0 AND
                			purchorderdetails.itemcode='" . $myrow['stockid'] . "'";

				$ErrMsg = _('The order details for this product cannot be retrieved because');
				$PurchResult = db_query($sql,$db,$ErrMsg);

				$PurchRow = db_fetch_row($PurchResult);
				if ($PurchRow[0]!=null){
				  $PurchQty =  $PurchRow[0];
				} else {
				  $PurchQty = 0;
				}

				// Find the quantity on works orders
				$sql = "SELECT SUM(woitems.qtyreqd - woitems.qtyrecd) AS dedm
				       FROM woitems
				       WHERE stockid='" . $myrow['stockid'] ."'";
				$ErrMsg = _('The order details for this product cannot be retrieved because');
				$WoResult = db_query($sql,$db,$ErrMsg);

				$WoRow = db_fetch_row($WoResult);
				if ($WoRow[0]!=null){
				  $WoQty =  $WoRow[0];
				} else {
				  $WoQty = 0;
				}

				if ($k==1){
					echo '<tr class="EvenTableRows">';
					$k=0;
				} else {
					echo '<tr class="OddTableRows">';
					$k=1;
				}
				$OnOrder = $PurchQty + $WoQty;

				$Available = $qoh - $DemandQty + $OnOrder;
/****************************************************************************************************************************/
				printf('<TD><FONT SIZE=1>%s</FONT></TD>
					<TD><FONT SIZE=1>%s</FONT></TD>
					<TD><FONT SIZE=1>%s</FONT></TD>
					<TD style="text-align:center"><FONT SIZE=1>%s</FONT></TD>
					<TD style="text-align:center"><FONT SIZE=1>%s</FONT></TD>
					<TD style="text-align:center"><FONT SIZE=1>%s</FONT></TD>
					<TD style="text-align:center"><FONT SIZE=1>%s</FONT></TD>
					<TD><FONT SIZE=1><input tabindex='.number_format($j+7).' type="textbox" size=6 name="val'.$j.'" value=0>
					<input type="hidden" size=6 name="itm'.$j.'" value='.$myrow['stockid'].'>
					</FONT></TD>
					</TR>',
					$myrow['stockid'],
					$myrow['description'],
					$myrow['units'],
					$qoh,
					$DemandQty,
					$OnOrder,
					$Available,
					$ImageSource,
					$rootpath,
					SID,
					$myrow['stockid']);
				$j++;
/****************************************************************************************************************************/
	#end of page full new headings if
			}
	#end of while loop
			echo '<tr><td align=center><input type="hidden" name="previous" value='.number_format($Offset-1).'><input tabindex='.number_format($j+7).' type="submit" name="Prev" value="'._('Prev').'"></td>';
			echo '<td align=center colspan=6><input type="hidden" name="order_items" value=1><input tabindex='.number_format($j+8).' name="orderMy" type="submit" value="'._('Order').'"></td>';
			echo '<td align=center><input type="hidden" name="nextlist" value='.number_format($Offset+1).'><input tabindex='.number_format($j+9).' type="submit" name="Next" value="'._('Next').'"></td></tr>';
/****************************************************************************************************************************/
			echo '</TABLE><input type="hidden" name="jjjjj" value='.$j.'>';
/****************************************************************************************************************************/
			echo '</form>';

		}#end if SearchResults to show

If ((isset($result))&&(!isset($_SESSION['SalesFail']))) {
  unset( $_SESSION['SalesFail']);
  $ListCount=DB_num_rows($result);
  $ListPageMax=ceil($ListCount/$_SESSION['DisplayRecordsMax']);

  if (isset($_POST['Next'])) {
    if ($_POST['PageOffset'] < $ListPageMax) {
	    $_POST['PageOffset'] = $_POST['PageOffset'] + 1;
    }
	}

  if (isset($_POST['Previous'])) {
    if ($_POST['PageOffset'] > 1) {
	    $_POST['PageOffset'] = $_POST['PageOffset'] - 1;
    }
  }

  echo "&nbsp;&nbsp;" . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';
?>

  <select name="PageOffset">

<?php
  $ListPage=1;
  while($ListPage<=$ListPageMax) {
	  if ($ListPage==$_POST['PageOffset']) {
?>

  		<option value=<?php echo($ListPage); ?> selected><?php echo($ListPage); ?></option>

<?php
	  } else {
?>

		  <option value=<?php echo($ListPage); ?>><?php echo($ListPage); ?></option>

<?php
	  }
	  $ListPage=$ListPage+1;
  }
?>

  </select>
  <INPUT TYPE=SUBMIT NAME="Go" VALUE="<?php echo _('Go'); ?>">
  <INPUT TYPE=SUBMIT NAME="Previous" VALUE="<?php echo _('Previous'); ?>">
  <INPUT TYPE=SUBMIT NAME="Next" VALUE="<?php echo _('Next'); ?>">

<?php

  echo '<BR><BR>';

	echo '<TABLE CELLPADDING=2 COLSPAN=7 BORDER=2>';
	$TableHeader = '<TR>
				<TD Class="tableheader">' . _('Code') . '</TD>
				<TD Class="tableheader">' . _('Customer Name') . '</TD>
				<TD Class="tableheader">' . _('Branch') . '</TD>
				<TD Class="tableheader">' . _('Contact') . '</TD>
				<TD Class="tableheader">' . _('Phone') . '</TD>
				<TD Class="tableheader">' . _('Fax') . '</TD>
			</TR>';

	echo $TableHeader;
	$j = 1;
	$k = 0; //row counter to determine background colour
  $RowIndex = 0;

  if (DB_num_rows($result)<>0){
  	DB_data_seek($result, ($_POST['PageOffset']-1)*$_SESSION['DisplayRecordsMax']);
  }

	while (($myrow=DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {

		if ($k==1){
			echo '<tr bgcolor="#CCCCCC">';
			$k=0;
		} else {
			echo '<tr bgcolor="#EEEEEE">';
			$k=1;
		}

		printf("<td><FONT SIZE=1><INPUT TYPE=SUBMIT NAME='Select' VALUE='%s'</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td></tr>",
			$myrow["debtorno"],
			$myrow["name"],
			$myrow["brname"],
			$myrow["contactname"],
			$myrow["phoneno"],
			$myrow["faxno"]);

		$j++;
		If ($j == 11 AND ($RowIndex+1 != $_SESSION['DisplayRecordsMax'])){
			$j=1;
			echo $TableHeader;
		}

    $RowIndex = $RowIndex + 1;
//end of page full new headings if
	}
//end of while loop

	echo '</TABLE>';

}
//end if results to show
echo '</FORM></CENTER>';
// </FORM>
echo "<br>";

echo "</center>";
include('includes/footer.inc');
?>