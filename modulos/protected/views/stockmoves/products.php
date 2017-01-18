<script type="text/javascript">
    $(document).on('ready',function() {
        $('#StockMasterTable').dataTable({
            "sPaginationType": "bootstrap",
            "aLengthMenu": [
                [10,25, 50, 100, 200, -1],
                [10,25, 50, 100, 200, "Todo"]
            ],
            "sDom": 'T<"clear">lfrtip',
            "oTableTools": {
                "aButtons": [{
                    "sExtends": "collection",
                    "sButtonText": "Exportar",
                    "aButtons": [ "print", "csv", "xls", {
                        "sExtends": "pdf",
                        "sPdfOrientation": "landscape",
                        "sTitle": "Catalogo Productos - <?=date('Y-m-d')?>",
                        }, ]
                }]
            },
            "fnInitComplete": function(){
                $(".dataTables_wrapper select").select2({
                    dropdownCssClass: 'noSearch'
                });
            }
        });
    });

</script>

<div class="container-fluid">
    <div class="form-legend"><h3>Catalogo de Productos</h3></div>
    <table id="StockMasterTable" class="table table-striped table-hover table-bordered">
        <thead>
            <tr>
                <th>StockID</th>
                <th>Agrupador</th>
                <th>Categoria</th>
                <th>Descripción</th>
                <th>Cat.Impuestos</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($ProductData as $Data) { ?>
            <tr>
                <td><?=$Data['stockid']?></td>
                <td><?=$Data['id_agrupador']?></td>
                <td><?=$Data['categorydescription']?></td>
                <td><?=$Data['description']?></td>
                <td><?=$Data['taxcatname']?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>


</div>
<!--
             stockid: A0060
          categoryid: MATERI
         description: INDICADOR QUIMICO PAQ. 250 PZA PLASMA
     longdescription: INDICADOR QUIMICO PAQ. 250 PZA PLASMA
               units: PAQUETE
              mbflag: B
     lastcurcostdate: 1800-01-01
          actualcost: 0.0000
            lastcost: 0.0000
        materialcost: 0.0000
          labourcost: 0.0000
        overheadcost: 0.0000
         lowestlevel: 0
        discontinued: 0
          controlled: 0
                 eoq: 0
              volume: 0.0000
                 kgs: 0.0000
             barcode: A0060
    discountcategory:
            taxcatid: 6
          serialised: 0
          perishable: 1
       decimalplaces: 0
    vtiger_productid:
       rh_lowestprod: 1
            rh_marca: 1
        nextserialno: 0
           netweight: 0.0000
     rh_sales_factor: 1
      rh_miniumprice: 0
  rh_lastminiumprice: 0
  rh_sustanciaactiva: 0
rh_cantidadporunidad: 0
         is_farmacia: 0
        id_agrupador:
4 rows in set (0.01 sec)
-->
