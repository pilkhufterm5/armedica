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
    $sqlTrans = "select * from stockmoves where stockid = '".$rowGetMov['stockid']."' and type in (10,20000)";
    $resTrans = DB_query($sqlTrans, $db);

    while ($rowTrans = DB_fetch_array($resTrans))
    {
        $sqlCom = "select * from bom where parent = '".$rowGetMov['stockid']."'";
        $resCom = DB_query($sqlCom, $db);

        while ($rowCom = DB_fetch_array($resCom))
        {
            $sqlVer = "select * from stockmoves where stockid = '".$rowCom['component']."' and type = ".$rowTrans['type']." 
                       and transno = ".$rowTrans['transno']." and reference like '% ".$rowGetMov['stockid']." %'";
            $resVer = DB_query($sqlVer, $db);

            if (DB_num_rows($resVer) <= 0){
                $sqlIns = "insert into stockmoves (stockid, type, transno, loccode, trandate, debtorno,
                           branchcode, prd, reference, qty, standardcost, show_on_inv_crds, newqoh, rh_orderline)
                           values ('".$rowCom['component']."',".$rowTrans['type'].",".$rowTrans['transno'].",'".$rowTrans['loccode']."','".$rowTrans['trandate']."','".$rowTrans['debtorno']
                           ."','".$rowTrans['branchcode']."',".$rowTrans['prd'].",'|| Ensamblado: ".$rowGetMov['stockid']." ',".($rowTrans['qty']*$rowCom['quantity']).",
                           (select stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost AS standardcost from stockmaster where stockid = '".$rowGetMov['stockid']."'),0,0,".$rowTrans['rh_orderline'].")";
                DB_query($sqlIns, $db);
                echo $sqlIns."<br><br>";
            }
        }

//        $sqlCom = "select sum(qty) from stockmoves where stockid in (select component from bom where parent = '".$rowGetMov['stockid']."')
//                   and type = ".$rowTrans['type']." and transno = ".$rowTrans['transno']." group by stockid";

//        $sqlCom = "select sum(qty) from stockmoves inner join bom on stockmoves.stockid = bom.component and bom.parent = '".$rowGetMov['stockid']."'
//                   and type = ".$rowTrans['type']." and transno = ".$rowTrans['transno']." group by stockid";


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
