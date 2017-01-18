<?php
$PageSecurity = 2;

include('includes/session.inc');
$title = _('Reporte Ventas - Tractoref');
include('includes/header.inc');
?>
<script src="jscalendar/src/js/jscal2.js"></script>
<script src="jscalendar/src/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/steel/steel.css" />
<form name="Form" method="POST" enctype="multipart/form-data" style="width:100%;">
<center>
    <table cellpadding="0" cellspacing="2" borde="0" width="35%">
        <tr>
            <td colspan="2" align="center"> <b>Filtro de seleccion</b></td>
        </tr>
        <tr>
            <td width="20%">Fecha inicial:</td>
            <td width="80%">
                <input type="text" name="fecha_ini" id="fecha_ini"  style="width:100%" value="<?php echo $_POST['fecha_ini'] ?>" />
            </td>
        </tr>
        <tr>
            <td>Fecha Final:</td>
            <td>
                <input type="text" name="fecha_fin" id="fecha_fin" style="width:100%" value="<?php echo $_POST['fecha_fin'] ?>" />
            </td>
        </tr>
        <tr>
            <td>Sucursal:</td>
            <td>
                <select name="sucursal" style="width:100%">
<?php
                $sql="select loccode,locationname from locations;";
                $result = DB_query($sql,$db);
                echo "<option value='%'>---------------------------------Todas---------------------------------</option>";
                while ($myrow = DB_fetch_array($result)) {
                    echo "<option ".(($myrow['loccode']==$_POST['sucursal']?"selected='selected'":" "))."value='".$myrow['loccode']."'>".$myrow['locationname']."</option>";
                }

?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Marca:</td>
            <td>
                <select name="marca" style="width:100%">
<?php
                $sql="select id,nombre from rh_marca;";
                $result = DB_query($sql,$db);
                echo "<option value='%'>---------------------------------Todas---------------------------------</option>";
                while ($myrow = DB_fetch_array($result)) {
                    echo "<option ".(($myrow['id']==$_POST['marca']?"selected='selected'":" "))."value='".$myrow['id']."'>".$myrow['nombre']."</option>";
                }

?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Cliente:</td>
            <td>
                <select name="cliente" style="width:100%">
<?php
                $sql="select debtorno,name from debtorsmaster;";
                $result = DB_query($sql,$db);
                echo "<option value='%'>---------------------------------Todos---------------------------------</option>";
                while ($myrow = DB_fetch_array($result)) {
                    echo "<option ".(($myrow['debtorno']==$_POST['cliente']?"selected='selected'":" "))."value='".$myrow['debtorno']."'>".$myrow['name']."</option>";
                }

?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Categor&iacute;a:</td>
            <td>
                <select name="categoria" style="width:100%">
<?php
                $sql="select categoryid,categorydescription from stockcategory;";
                $result = DB_query($sql,$db);
                echo "<option value='%'>---------------------------------Todas---------------------------------</option>";
                while ($myrow = DB_fetch_array($result)) {
                    echo "<option ".(($myrow['categoryid']==$_POST['categoria']?"selected='selected'":" "))."value='".$myrow['categoryid']."'>".$myrow['categorydescription']."</option>";
                }

?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Art&iacute;culo:</td>
            <td>
                <select name="arti" style="width:100%">
<?php
                $sql="select stockid,description from stockmaster order by stockid;";
                $result = DB_query($sql,$db);
                echo "<option value='%'>---------------------------------Todas---------------------------------</option>";
                while ($myrow = DB_fetch_array($result)) {
                    echo "<option ".(($myrow['stockid']==$_POST['arti']?"selected='selected'":" "))."value='".$myrow['stockid']."'>".$myrow['stockid'].' - '.$myrow['description']."</option>";
                }

?>
                </select>
            </td>
        </tr>
        <tr>
            <td align="center" colspan="2"><input type="submit" name="search" value="Mostrar" style="width:100%;"  /></td>
        </tr>
    </table>
</center>
</form>
<br />
<br />
<br />
<br />
<br />
<?php
    if(isset($_POST['search'])){
       /* $sql="SELECT
                        COUNT(stockmaster.stockid) as id
                        ,stockmaster.stockid
                        , stockmaster.description
                        , SUM(salesorderdetails.quantity) as total

                    FROM
                        debtortrans
                        INNER JOIN salesorders
                            ON (debtortrans.order_ = salesorders.orderno)
                        INNER JOIN rh_cfd__cfd
                            ON (rh_cfd__cfd.fk_transno = debtortrans.transno)
                        INNER JOIN debtorsmaster
                            ON (debtortrans.debtorno = debtorsmaster.debtorno)
                        INNER JOIN systypes
                            ON (debtortrans.type = systypes.typeid) and (systypes.typeid=10)
                        INNER JOIN salesorderdetails
                            ON (salesorderdetails.orderno = salesorders.orderno)
                        INNER JOIN locations
                            ON (salesorders.fromstkloc = locations.loccode) AND (locations.loccode like '".$_POST['sucursal']."' )
                        INNER JOIN stockmaster
	                        ON (salesorderdetails.stkcode = stockmaster.stockid) AND  (stockmaster.stockid like '".$_POST['arti']."')
                        INNER JOIN rh_marca
                            ON (stockmaster.rh_marca = rh_marca.id) AND (rh_marca.id like '".$_POST['marca']."')
                        INNER JOIN stockcategory
                            ON (stockmaster.categoryid = stockcategory.categoryid) AND (stockcategory.categoryid like '".$_POST['categoria']."')
                        where
	                        debtortrans.trandate>='".$_POST['fecha_ini']."' AND debtortrans.trandate<='".$_POST['fecha_fin']."'
                        Group by  stockmaster.stockid";
                $result = DB_query($sql,$db);
                if (DB_num_rows($result)==0){
                	prnMsg(_('No se ha podido localizar información de ventas'),'info');
                }
                $x=0;
  echo "<script type=\"text/javascript\" src=\"http://www.google.com/jsapi\"></script>
    <script type=\"text/javascript\">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type=\"text/javascript\">
      function drawVisualization() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Articulo');
        data.addColumn('number', 'Cantidad vendida');
        data.addRows(".DB_num_rows($result).");";
        while ($myrow = DB_fetch_array($result)) {
            echo "data.setValue(".$x.", 0, '".stripslashes ($myrow['stockid'])."');";
            echo "data.setValue(".$x.", 1, ".$myrow['total'].");";
            $x++;
        }
   echo"
        // Create and draw the visualization.
        new google.visualization.PieChart(document.getElementById('visualization')).
            draw(data, {title:\"Ventas por articulo\"});
      }


      google.setOnLoadCallback(drawVisualization);
    </script>";  */
?>
<center>
<div style="width:90%">
        <table width="100%">
            <tr>
                <td class='tableheader' style="width:80px;">Fecha</td>
                <td class='tableheader' style="width:250px;">Cliente</td>
                <td class='tableheader' style="width:80px;">Tipo</td>
                <td class='tableheader' style="width:80px;">Trans</td>
                <td class='tableheader' style="width:130px;">Codigo</td>
                <td class='tableheader' style="width:180px;">Descripcion</td>
                <td class='tableheader' style="width:100px;">Marca</td>
                <td class='tableheader' style="width:140px;">Categoria</td>
                <td class='tableheader' style="width:80px;">Cantidad</td>
                <td class='tableheader' style="width:80px;">Precio</td>
                <td class='tableheader' style="width:80px;">Total</td>
                <td class='tableheader' style="width:80px;">Costo</td>
            </tr>
            <?php
                $sql="SELECT
                        DATE_FORMAT(debtortrans.trandate,'%d-%m-%Y')as fecha
                        , debtorsmaster.name
                        , systypes.typename
                        , CONCAT(rh_cfd__cfd.serie,rh_cfd__cfd.folio,' (',rh_cfd__cfd.fk_transno,')') as trans
                        , stockmaster.stockid
                        , stockmaster.description
                        , rh_marca.nombre
                        , stockcategory.categorydescription
                        , salesorderdetails.quantity
                        , salesorderdetails.unitprice
                        , (salesorderdetails.quantity*salesorderdetails.unitprice)as total
                        , (stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost) AS actualcost
                    FROM
                        debtortrans
                        INNER JOIN salesorders
                            ON (debtortrans.order_ = salesorders.orderno)
                        INNER JOIN rh_cfd__cfd
                            ON (rh_cfd__cfd.fk_transno = debtortrans.transno)
                        INNER JOIN systypes
                            ON (debtortrans.type = systypes.typeid) and (systypes.typeid=10)
                        INNER JOIN salesorderdetails
                            ON (salesorderdetails.orderno = salesorders.orderno)
                        INNER JOIN locations
                            ON (salesorders.fromstkloc = locations.loccode) AND (locations.loccode like '".$_POST['sucursal']."' )
                        INNER JOIN stockmaster
	                        ON (salesorderdetails.stkcode = stockmaster.stockid) AND  (stockmaster.stockid like '".$_POST['arti']."')
                        INNER JOIN rh_marca
                            ON (stockmaster.rh_marca = rh_marca.id) AND (rh_marca.id like '".$_POST['marca']."')
                        INNER JOIN debtorsmaster
                            ON (debtortrans.debtorno = debtorsmaster.debtorno) and (debtorsmaster.debtorno like '".$_POST['cliente']."')
                        INNER JOIN stockcategory
                            ON (stockmaster.categoryid = stockcategory.categoryid) AND (stockcategory.categoryid like '".$_POST['categoria']."')
                        where
	                        debtortrans.trandate>='".$_POST['fecha_ini']."' AND debtortrans.trandate<='".$_POST['fecha_fin']."'
                        order by debtortrans.rh_createdate;";
                $result = DB_query($sql,$db);
                if (DB_num_rows($result)==0){
                	prnMsg(_('No se ha podido localizar información de ventas'),'info');
                }
                $k=0;
                $TotalCount=0;
                $TotalPrice=0;
                $TotalGran=0;
                $TotalCost=0;
                while ($myrow = DB_fetch_array($result)) {
                	if ($k==1){
		                echo "<tr bgcolor='#CCCCCC'>";
		                $k=0;
	                } else {
		                echo "<tr bgcolor='#EEEEEE'>";
		                $k=1;
	                }
                        echo '<td>'.$myrow['fecha'].'</td>
                        <td>'.$myrow['name'].'</td>
                        <td>'.$myrow['typename'].'</td>
                        <td>'.$myrow['trans'].'</td>
                        <td>'.$myrow['stockid'].'</td>
                        <td>'.$myrow['description'].'</td>
                        <td>'.$myrow['nombre'].'</td>
                        <td>'.$myrow['categorydescription'].'</td>
                        <td align="right">'.number_format ($myrow['quantity'],5).'</td>
                        <td align="right">'.number_format ($myrow['unitprice'],5).'</td>
                        <td align="right">'.number_format ($myrow['total'],5).'</td>
                        <td align="right">'.number_format ($myrow['actualcost'],5).'</td>
                        </tr>';
                        $TotalCount*= $myrow['quantity'];
                        $TotalPrice+= $myrow['unitprice'];
                        $TotalGran+= $myrow['total'];
                        $TotalCost+= $myrow['actualcost'];
                }
            ?>
            <tr>
                <td colspan="8" align="right"><strong>Totales</strong></td>
                <td align="right"><strong><?php echo number_format ($TotalCount,2);  ?></strong></td>
                <td align="right"><strong><?php echo number_format ($TotalPrice,2);  ?></strong></td>
                <td align="right"><strong><?php echo number_format ($TotalGran,2);  ?></strong></td>
                <td align="right"><strong><?php echo number_format ($TotalCost,2);  ?></strong></td>
            </tr>
        </table>
</div>
</center>
<?php
  }
?>
<script type="text/javascript">//<![CDATA[
      var cal2 = Calendar.setup({
          onSelect: function(cal2) { cal2.hide() },
          showTime: false
      });

      var cal = Calendar.setup({
          onSelect: function(cal) { cal.hide() },
          showTime: false
      });
      cal.setLanguage('es');
      cal.manageFields("fecha_ini", "fecha_ini", "%Y-%m-%d");

      cal2.setLanguage('es');
      cal2.manageFields("fecha_fin", "fecha_fin", "%Y-%m-%d");
    //]]>
</script>
<?php
include('includes/footer.inc');
?>