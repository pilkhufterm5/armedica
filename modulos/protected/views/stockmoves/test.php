<script>

	function UpdateOnline(){

 		var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("stockmoves/index"); ?>",
                type: "POST",
                dataType : "json",
                data: {},
                beforeSend:function(error){
                	console.log(error);
                },
                success : function(data, newValue) {
              		console.log(data);
                },
            });

		setTimeout('UpdateOnline()',900000);
	}


	UpdateOnline();

</script>
