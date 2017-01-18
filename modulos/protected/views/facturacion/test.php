
<script type="text/javascript">
    $(document).on('ready', function() {
        
        $('#BD_SQL').dataTable( {
            "sPaginationType": "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "fnInitComplete": function(){
                $(".dataTables_wrapper select").select2({
                    dropdownCssClass: 'noSearch'
                });
            }
        });
        $("select[name='SociosTable_length']").addClass('span2');
        $("select[name='SociosTable_length']").css({"height":"0px", "margin-top":"0px"});
    })
</script>


<table id="BD_SQL">
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