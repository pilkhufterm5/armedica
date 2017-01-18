<script type="text/javascript">
    $(document).on('ready', function() {
        
        
    });
    
    function GetSocioData(DebtorNo,BranchCode){
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("afiliaciones/GetSocio"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                  GetSocioData:{
                      SBranchCode: BranchCode,
                      SDebtorNo: DebtorNo
                  },
            },
            success : function(data, newValue) {
                if (data.requestresult == 'ok') {
                    displayNotify('success', data.message);
                    $('#SDetail').html(data.GetData);
                }else{
                    displayNotify('alert', data.message);
                }
            },
            error : ajaxError
        });
    }
    
</script>

<div id="Modal_ViewSocio" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 600px;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelView"> </h3>
    </div>
    <div class="modal-body">
        <p>
            <div id="SDetail"></div>
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-UpdateSocio" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-UpdateSocio" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
    </div>
</div>



<!--
            <th>Nombre</th>
            <th>Sexo</th>
            <th>Nombre Comercial</th>
            <th>Fecha Nacimiento</th>
            <th>Calle</th>
            <th>N°</th>
            <th>Fecha Ingreso</th>
            <th>Fecha ult. Aum</th>
            <th>C.P.</th>
            <th>Colonia</th>
            <th>Sector</th>
            <th>Entre Calles</th>
            <th>Municipio</th>
            <th>Estado</th>
            <th>CuadranteA</th>
            <th>CuadranteB</th>
            <th>CuadranteC</th>
            <th>Telefono</th>
            <th>Padecimientos</th>
            <th>Otros Padecimientos</th>
            <th>N° Socio</th>
-->
