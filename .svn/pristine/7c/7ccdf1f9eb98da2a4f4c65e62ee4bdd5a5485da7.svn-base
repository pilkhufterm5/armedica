<script type="text/javascript">
    $(document).on('ready',function() {
        oTable1= $('#CuentasDTable').dataTable({
             "searching": false,
             "scrollX": true
        });
        $("#CuentasDTable_wrapper").prepend("<input type='text'   name='SearchName' placeholder='Buscar...' onkeydown='SearchByName(event)' style='float: right; margin-top: 20px; width: 250px;' />");

    });

    function SearchByName(e){
        console.log(e.keyCode);
        str= $("input[name='SearchName']").val();
        if (e.keyCode == 13 && str.length >= 5) {
        }else{
            return false;
        }

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("cuentas/Search"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                 Search:{
                      Nombre: $("input[name='SearchName']").val()
                  },
            },
            success : function(Response, newValue) {
                if (Response.requestresult == 'ok') {
                    oTable1.fnDestroy();
                    //displayNotify('success', Response.message);
                    $('#ReloadBody').html(Response.TBody);
                    oTable1 = $('#CuentasDTable').dataTable({
                        "searching": false,
                    });
                    $("#CuentasDTable_wrapper").prepend("<input type='text'   name='SearchName' placeholder='Buscar...' onkeydown='SearchByName(event)' style='float: right; margin-top: 20px; width: 250px;' />");
                }else{
                    displayNotify('error', Response.message);
                }
            },
            error : ajaxError
        });
    }
</script>



<?php FB::INFO($Cuentas); ?>

<div class="row">

    <div class="large-12 small-12 columns">

        <div class="large-6 small-12 columns">
            <h1><a href='<?php echo Yii::app()->createUrl("crm/leads/cuentas");?>'>Cuentas</a></h1>
        </div>

        <div class="large-6 small-12 columns">
            <div class="nav-bar right">
                <ul class="button-group radius">
                    <!-- <li><a href="<?php echo Yii::app()->createUrl('crm/cuentas/index');?>" class="small button">Listar Cuentas</a></li> -->
                    <!-- <li><a href="#" class="small button" data-reveal-id="Crearcuenta">Crear Cuenta</a></li> -->
                    <!-- <li><a href="#" class="small button" data-reveal-id="crearOportunidad">Crear Oportinidad</a></li> -->
                </ul>
            </div>
        </div>

    </div>
    <hr/>

    <div class="large-12 columns" role="content">
        <table id="CuentasDTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>RFC</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="ReloadBody">
                <?php foreach ($Cuentas as $Data) { ?>
                    <tr id="<?=$Data['debtorno']?>">
                        <td><?=$Data['debtorno']?></td>
                        <td><?=$Data['name']?></td>
                        <td><?=$Data['taxref']?></td>
                        <td><?=$Data['rh_tel']?></td>
                        <td>
                            <span data-tooltip class="has-tip radius" title="Ver informacion a detalle"><a href="<?php echo Yii::app()->createUrl("crm/cuentas/view", array('id'=>$Data['debtorno'])); ?>" class="fi-magnifying-glass"></a></span>&nbsp;
                            <span data-tooltip class="has-tip radius" title="Editar contacto"><a href="<?php echo Yii::app()->createUrl("crm/cuentas/update", array('id'=>$Data['debtorno'])); ?>" class="fi-pencil"></a></span>&nbsp;
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div id="Crearcuenta" class="reveal-modal" data-reveal>
    <h2>Crear cuenta</h2>
    <?php include_once "_crearCuenta.php"; ?>
    <a class="close-reveal-modal">&#215;</a>
</div>
