<?php

/**
 *
 */
class PurchordersController extends Controller {

    public function actionIndex() {
        global $db;

        $SQL = "SELECT  grnbatch,
            grnno,
            purchorderdetails.orderno,
            purchorderdetails.unitprice,
            grns.itemcode,
            grns.deliverydate,
            grns.itemdescription,
            grns.supplierid,
            sum(grns.qtyrecd) as qty_recib,
            sum(grns.quantityinv) as qty_fact,
            grns.stdcostunit,
            purchorderdetails.glcode,
            purchorderdetails.shiptref,
            purchorderdetails.jobref,
            purchorderdetails.podetailitem,
            purchorderdetails.assetid,
            sum(purchorderdetails.unitprice) as price,
            sum(purchorderdetails.quantityord) as qty_ord,
            sum(((purchorderdetails.unitprice * purchorderdetails.quantityord) * purchorderdetails.rh_tax)/100) as tax
        FROM grns INNER JOIN purchorderdetails
        ON  grns.podetailitem=purchorderdetails.podetailitem
        WHERE  grns.qtyrecd - grns.quantityinv > 0
        GROUP BY purchorderdetails.orderno
            ORDER BY grns.grnno";
        $QueryResult = DB_query($SQL, $db);
        $OrderRowData = array();
        while ($_2OrderRowData = DB_fetch_assoc($QueryResult)) {
            $OrderRowData[] = $_2OrderRowData;
        }

        $GetSuppliers = Yii::app()->db->createCommand()->select(' supplierid,suppname ')->from('suppliers')->queryAll();
        $ListaSuppliers = CHtml::listData($GetSuppliers, 'supplierid', 'suppname');
        FB::INFO($OrderRowData, '_______________________$OrderRowData');

        $this->render('index', array(
            'OrderRowData' => $OrderRowData,
            'ListaSuppliers' => $ListaSuppliers
        ));
    }

}
?>