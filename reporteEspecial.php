<?php
$PageSecurity = 2;
include('includes/session.inc');
$title = _('Reporte Especial');
include('includes/header.inc');

$SQL="select debtorsmaster.debtorno as codigo,debtorsmaster.name as cliente,rh_cfd__cfd.serie,rh_cfd__cfd.folio, sum(stockmoves.price*-stockmoves.qty) as subtotal,
	avg(if(isnull(D1.descuento),0,D1.descuento*100))as `%Descuento1`,sum(if(isnull(D1.monto),0,D1.monto*D1.cant))as Descuento1,
	(debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight-debtortrans.ovdiscount)as total
	from stockmoves
		join stockmaster on stockmoves.stockid=stockmaster.stockid and stockmaster.rh_marca = 2
		left join rh_descuentos D1 on stockmoves.stkmoveno = D1.stkmoveno and D1.tipo_descuento=1
		join debtortrans on stockmoves.transno=debtortrans.transno and stockmoves.type=debtortrans.type
		join rh_cfd__cfd on rh_cfd__cfd.id_debtortrans = debtortrans.id
		join debtorsmaster on debtorsmaster.debtorno = stockmoves.debtorno
	where stockmoves.type = 10 and stockmaster.rh_marca = 2 and date(stockmoves.trandate)>='2011-06-27'  and date(stockmoves.trandate)<='2011-07-11' group by stockmoves.transno;";

$RS=DB_query($SQL,$db);

?>
<br />
<br />
<center>
    <b>Reporte del 27 de Junio al 11 de julio 2011 - Marca Royal</b>
</center>
<br />
<table width="100%" cellspacing="0" cellpadding="0" border="0">
    <TR>
        <TD class="tableheader">
            Codigo
        </TD>
        <TD class="tableheader">
            Descripcion
        </TD>
        <TD class="tableheader">
           Serie
        </TD>
        <TD class="tableheader">
           Folio
        </TD>
        <TD class="tableheader">
            SubTotal
        </TD>
        <TD class="tableheader">
           %Descuento
        </TD>
        <TD class="tableheader">
           Descuento
        </TD>
        <TD class="tableheader">
           Total
        </TD>
    </TR>
<?
    while($RW=DB_fetch_array($RS)){
      $SUMSub+= $RW[4];
      $SUMDESC+= $RW[6];
      $DUMTOT+=  $RW[7];
      ?>
    <TR>
        <TD >
            <? echo $RW[0] ;?>
        </TD>
        <TD >
            <? echo $RW[1] ;?>
        </TD>
        <TD >
           <? echo $RW[2] ;?>
        </TD>
        <TD >
           <? echo $RW[3] ;?>
        </TD>
        <TD align="right">
            <? echo number_format($RW[4],2) ;?>
        </TD>
        <TD align="right">
           <? echo number_format($RW[5],2) ;?>
        </TD>
        <TD align="right">
           <? echo number_format($RW[6],2) ;?>
        </TD>
        <TD align="right">
           <? echo number_format($RW[7],2) ;?>
        </TD>
    </TR>
    <?
    }
?>
<tr>
    <td colspan="4" align="right">TOTAL</td>
        <TD align="right">
            <? echo number_format($SUMSub,2) ;?>
        </TD>
        <TD align="right"></TD>
        <TD align="right">
           <? echo number_format($SUMDESC,2) ;?>
        </TD>
        <TD align="right">
           <? echo number_format($DUMTOT,2) ;?>
        </TD>
</tr>
</table>
<?
include('includes/footer.inc');
?>