
<script>
    $(document).on('ready',function() {
        $('#tableSortableRes').dataTable( {
            "sPaginationType": "bootstrap",
            "fnInitComplete": function(){
                $(".dataTables_wrapper select").select2({
                    dropdownCssClass: 'noSearch'
                });
            }
        });
    });
</script>


   <div class="container-fluid">
       <div style="height: 20px;"></div>
       <div class="row-fluid">
           <div class="span12">
               <table class="table table-striped table-responsive table-hover" id="tableSortableRes">
                   <thead>
                       <tr>
                           <th>Asesor</th>
                           <th>2013-12-30</th>
                           <th>Contacto</th>
                           <th>Plan</th>
                           <th>Cotizacion</th>
                           <th>Frecuencia de Pago</th>
                           <th>Vidas</th>
                           <th>Folio Afiliacion</th>
                           <th>Activar Folio</th>
                       </tr>
                   </thead>
                   <tbody>
                       <tr class="gradeX">
                           <td>Asesor 1</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="left">123456789123</td>
                           <td class="left">Mensual</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center">
                               <a title="Edit" ><img src="<?php echo Yii::app()->theme->baseUrl;?>/images/pencil.png" /></a>
                               <a title="Delete" ><img src="<?php echo Yii::app()->theme->baseUrl;?>/images/cross.png" /></a>
                           </td>
                       </tr>
                       <tr class="gradeA">
                           <td>Asesor 1</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">Mensual</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>Asesor 1</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">Mensual</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>Asesor 1</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">Mensual</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>Asesor 1</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">Mensual</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>Asesor 1</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">Mensual</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>Asesor 1</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">Mensual</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>Asesor 1</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">Mensual</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>Asesor 1</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">Mensual</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>Asesor 1</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">Mensual</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>Asesor 1</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">Mensual</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>Asesor 1</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">Mensual</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>Asesor 1</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">Mensual</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>014</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">erasto@realhost.com.nx</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>015</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">erasto@realhost.com.nx</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>016</td>
                           <td>2013-12-30</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">erasto@realhost.com.nx</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>017</td>
                           <td>RealHost</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">erasto@realhost.com.nx</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>018</td>
                           <td>RealHost</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">erasto@realhost.com.nx</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>019</td>
                           <td>RealHost</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">erasto@realhost.com.nx</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>020</td>
                           <td>RealHost</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">erasto@realhost.com.nx</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>021</td>
                           <td>RealHost</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">erasto@realhost.com.nx</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>022</td>
                           <td>RealHost</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">erasto@realhost.com.nx</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>023</td>
                           <td>RealHost</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">erasto@realhost.com.nx</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>023</td>
                           <td>RealHost</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">erasto@realhost.com.nx</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                       <tr class="gradeA">
                           <td>024</td>
                           <td>RealHost</td>
                           <td>RealHost</td>
                           <td class="center">123456789123</td>
                           <td class="center">erasto@realhost.com.nx</td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td class="center"></td>
                       </tr>
                   </tbody>
               </table>
           </div>
       </div>
       <!--Sortable Responsive Table end-->
   </div><!-- end container -->
