<?php

$PageSecurity = 2;
include('includes/session.inc');
$title = _('Identificar errores');

include('includes/header.inc');

DB_query('BEGIN', $db);

//analizar ensamblados dejar afuera tabla tmp
$sqlGetMov = "select * from stockmaster where mbflag in ('A','E')";
$resGetMov = DB_query($sqlGetMov, $db);

while ($rowGetMov = DB_fetch_array($resGetMov))
{
    $sqlSM = "select type, transno, sum(qty) qty, debtorno, trandate, stockid from stockmoves where stockid = '".$rowGetMov['stockid']."' group by transno";
    $resSM = DB_query($sqlSM, $db);


    while ($rowSM = DB_fetch_array($resSM))
    {
        unset($resVerify);

        //realizar suma

        $sqlVerify = "select * from (select sum(stockmoves.qty) cant, stockmoves.stockid as prod, stkmoveno, bom.parent, bom.component, bom.quantity, stockmoves.type, stockmoves.transno, stockmoves.debtorno,
                    stockmoves.trandate, stockmoves.reference from bom inner join stockmoves on bom.parent = '".$rowGetMov['stockid']."'
                    and bom.component = stockmoves.stockid where stockmoves.type = ".$rowSM['type']." and stockmoves.transno = ".$rowSM['transno']." and reference like '% ".$rowGetMov['stockid']." %' group by stockid, reference) sm
                    where ABS((cant - (quantity * ".$rowSM['qty']."))) > 0.001";
                    

//        $sqlVerify = "select stkmoveno, bom.parent, bom.component, stockmoves.type, stockmoves.transno, stockmoves.debtorno,
//                stockmoves.trandate from bom inner join stockmoves on bom.parent = '".$rowGetMov['stockid']."'
//                and bom.component = stockmoves.stockid where qtyT != bom.quantity * ".$rowSM['qty']." group by stockmoves.stockid";
        $resVerify = DB_query($sqlVerify, $db);

        $cantRows = DB_num_rows($resVerify);

        if ($cantRows > 0)
        {
            //prnMsg($rowSM[type].",".$rowSM['transno']."|".$rowSM['debtorno']."|".$rowSM['trandate']."|".$rowSM['stockid']);

            //$rowAjuste = DB_fetch_array($resVerify);

            while ($rowAjuste = DB_fetch_array($resVerify))
            {
                $cantAjuste = ($rowAjuste['cant']*-1) + ($rowAjuste['quantity']*$rowSM['qty']);

                $sqlUpdateAjuste = "update stockmoves set qty = qty + ".$cantAjuste." where type = ".$rowSM['type']." and transno = ".$rowSM['transno']." and stockid = '".$rowAjuste['component']."' and reference like '%".$rowGetMov['stockid']."%' limit 1;";
                echo "<br>".$sqlUpdateAjuste;
                DB_query($sqlUpdateAjuste, $db);

                //verificar de nueva cuenta
//                $sqlVerify = "select * from (select sum(stockmoves.qty) cant, stockmoves.stockid as prod, stkmoveno, bom.parent, bom.component, bom.quantity, stockmoves.type, stockmoves.transno, stockmoves.debtorno,
//                        stockmoves.trandate, stockmoves.reference from bom inner join stockmoves on bom.parent = '".$rowGetMov['stockid']."'
//                        and bom.component = stockmoves.stockid where stockmoves.type = ".$rowSM['type']." and stockmoves.transno = ".$rowSM['transno']." and reference like '%".$rowGetMov['stockid']."%' group by stockid, reference) sm
//                        where ABS((cant - (quantity * ".$rowSM['qty']."))) > 0.001";
//
//                $resVerify = DB_query($sqlVerify, $db);
//
//                if (DB_num_rows($resVerify) <= 0)
//                {
                    //prnMsg("Correcto","success");
//                }

            }
            
        }

    }

}

if (!isset($_GET['u'])){
    DB_query('rollback', $db);
    echo '<br>rollback';
}else{
    DB_query('commit', $db);
    echo '<br>commit';
}

?>
