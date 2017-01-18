<script type="text/javascript">


    $(document).on('ready', function() {
        $('#Modal-EditarTipo').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("tipofacturas/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    Update:{
                        id: $("#E-ID").val(),
                        tipo: $('#E-tipo').val()
                    },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        setTimeout(function(){
                            window.location.href='<?=$this->createUrl('tipofacturas/index')?>';
                        }, 2000);
                    }else{
                        displayNotify('error', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });


    function EditarTipo(id){
        $('#ModalLabelEdit').text('Editar Tipo de Factura');
        $('#Modal_EditarTipo').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
    }

    function LoadForm(id){
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("tipofacturas/LoadForm"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                GetData:{
                    id: id
                },
            },
            success : function(data) {
                if (data.requestresult == 'ok') {
                    displayNotify('success', data.message);
                    $('#E-tipo').val(data.Data.tipo);
                }else{
                    displayNotify('error', data.message);
                }
            },
            error : ajaxError
        });
    }

</script>

<div id="Modal_EditarTipo" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;"  >

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelEdit"> </h3>
    </div>

    <div class="modal-body">

        <p>
            <table class="table">
                <tbody>
                    <tr><input type="hidden" value="" id="E-ID" >
                        <td><label class="control-label" style="margin-top: 10px;">Tipo de Factura: </label></td>
                        <td><input type="text" id="E-tipo" class="span12" /></td>
                </tbody>
            </table>
        </p>

    </div>

    <div class="modal-footer">
        <button id="Close-Modal-EditarEmpresa" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Modal-EditarTipo" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Aceptar</button>
    </div>

</div>
