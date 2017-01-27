

  <script>
    $(function() {


        $( ".column" ).sortable({
            connectWith: ".column",
            handle: ".portlet-header",
            cancel: ".portlet-toggle",
            placeholder: "portlet-placeholder ui-corner-all",

            start: function( event, ui ) {/*Columna Inicial*/
                console.log($(this).attr('name'));
                //alert(event);
            },

            receive: function( event, ui ) {/*Columna Final*/
                //alert($(this).attr('name'));
                console.log(ui.item.context.id);
                console.log($(this).attr('name'));
            }
        });



        $( ".portlet" )
            .click(function(){
                //console.log($(this));
            })
          .addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
          .find( ".portlet-header" )
            .addClass( "ui-widget-header ui-corner-all" )
            .prepend( "<span class='ui-icon ui-icon-plusthick portlet-toggle'></span>");


        $( ".portlet-toggle" ).click(function() {
          var icon = $( this );
          icon.toggleClass( "ui-icon-plusthick ui-icon-minusthick" );
          icon.closest( ".portlet" ).find( ".portlet-content" ).toggle();
        });
    });
</script>

<style>

    body {
        min-width: 520px;
    }

    .column {
        /*width: 170px; */
        /*float: left;*/
        padding-bottom: 100px;
        /*border:solid;*/
        border-color: #D6E9C6;
        box-shadow: 2px 5px 3px 0 #DDDDDD, 0 1px 2px 0 #FFFFFF inset;
    }

    .portlet {
        margin: 0 1em 1em 0;
        padding: 0.3em;
    }

    .portlet-header {
        padding: 0.2em 0.3em;
        margin-bottom: 0.5em;
        position: relative;
    }

    .portlet-toggle {
        position: absolute;
        top: 50%;
        right: 0;
        margin-top: -8px;
    }

    .portlet-content {
        padding: 0.4em;
    }
    .portlet-placeholder {
        border: 1px dotted black;
        margin: 0 1em 1em 0;
        height: 50px;
    }

    .HeadCenter{
        text-align: center;
    }

    .ColumnsCenter{
        /*float: right; position: relative; right: 30%; */
    }

    .TDTop{
        vertical-align:top;
    }

    .portlet-content{
        display: none;
    }

  </style>



<div class="large-12 columns" >

    <table class="table-bordered" style="width:100%;">

        <thead>
            <tr>
                <th class="HeadCenter">INICIO</th>
                <th class="HeadCenter">PROCESO</th>
                <th class="HeadCenter">GANADO</th>
                <th class="HeadCenter">PERDIDO</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td class="TDTop">

                    <div class="column ColumnsCenter" name="INICIO">
                        <div class="portlet dropfalse"  id="1">
                            <div name="test" class="portlet-header">Feeds</div>
                            <div class="portlet-content" >Lorem ipsum dolor sit amet, consectetuer adipiscing elit</div>
                        </div>

                        <div class="portlet" id="2">
                            <div class="portlet-header">News</div>
                            <div class="portlet-content">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</div>
                        </div>
                    </div>

                </td>

                <td class="TDTop">

                    <div class="column ColumnsCenter" name="PROCESO">
                        <div class="portlet" id="3">
                            <div class="portlet-header">Shopping</div>
                            <div class="portlet-content">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</div>
                        </div>
                    </div>

                </td>

                <td class="TDTop">

                    <div class="column ColumnsCenter" name="GANADO">
                        <div class="portlet" id="4">
                            <div class="portlet-header">Links</div>
                            <div class="portlet-content">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</div>
                        </div>

                        <div class="portlet" id="5">
                            <div class="portlet-header">Images</div>
                            <div class="portlet-content">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</div>
                        </div>
                    </div>

                </td>

                <td class="TDTop">

                    <div class="column ColumnsCenter" name="PERDIDO">
                        <div class="portlet">
                            <div class="portlet-header">Links</div>
                            <div class="portlet-content">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</div>
                        </div>

                        <div class="portlet">
                            <div class="portlet-header">Images</div>
                            <div class="portlet-content">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</div>
                        </div>
                    </div>

                </td>
            </tr>
        </tbody>

    </table>
</div>
