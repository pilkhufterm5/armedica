<script type="text/javascript">
    $(document).on('ready', function() {
        $("#Exec").click(function(event) {
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("sqlserver/index"); ?>",
                type: "POST",
                dataType : "html",
                timeout : (120 * 1000),
                data: {
                    SQL:{
                        Query: $("#Query").val(),
                    },
                },
                success : function(data, newValue) {
                    displayNotify('success', "Query OK");
                    $("#Result").html(data);

                    oTable2 = $('#Table_Result').dataTable( {
                        "sPaginationType": "bootstrap",
                        "sDom": 'T<"clear">lfrtip',
                        "scrollX": true,
                        "fnInitComplete": function(){
                            $(".dataTables_wrapper select").select2({
                                dropdownCssClass: 'noSearch'
                            });
                        }
                    });

                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                    }else{
                        //displayNotify('fail', data.message);
                    }
                },
                error : ajaxError
            });
        });

        oTable1 = $('#BD_SQL').dataTable( {
            "sPaginationType": "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "fnInitComplete": function(){
                $(".dataTables_wrapper select").select2({
                    dropdownCssClass: 'noSearch'
                });
            }
        });

	});
</script>

    <div style="height:50px;"></div>

    <label>SQL Query:</label>
    <textarea id="Query" placeholder="SELECT TOP 100 * FROM CCM_Foltitular" ></textarea>
    <input id="Exec" type="button" class="btn btn-success" value="Ejecutar">
    <div id="Result"></div>

    <div style="height:100px;"></div>

    <label>Tablas:</label>
    <table id="BD_SQL" style="width:100%;" class="table table-striped" >
        <thead>
            <tr>
                <th>computed</th>
                <th>TABLE_CATALOG</th>
                <th>TABLE_SCHEMA</th>
                <th>TABLE_NAME</th>
                <th>TABLE_TYPE</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($Tables as $Data){ ?>
            <tr>
                <td><?=$Data['computed']?></td>
                <td><?=$Data['TABLE_CATALOG']?></td>
                <td><?=$Data['TABLE_SCHEMA']?></td>
                <td><?=$Data['TABLE_NAME']?></td>
                <td><?=$Data['TABLE_TYPE']?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>








