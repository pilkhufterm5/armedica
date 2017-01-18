<?php
/* @var $this SiteController */
$this->pageTitle=Yii::app()->name;
?>


<form class="form-horizontal">
               
                   <script>
                       $(document).ready(function() {
                           $('#tableSortable, #tableSortableRes, #tableSortableResMed').dataTable( {
                               "sPaginationType": "bootstrap",
                               "fnInitComplete": function(){
                                   $(".dataTables_wrapper select").select2({
                                       dropdownCssClass: 'noSearch'
                                   });
                               }
                           });
                           //                            $("#simpleSelectBox").select2({
                           //                                dropdownCssClass: 'noSearch'
                           //                            }); 
                       });
                   </script>



                        <!-- Overall Earnings Graph Widget begins -->
                        <script>
                            $(window).load(function () {
                                if (widgetsLoaded['overall-earnings-graph']) return;
                                widgetsLoaded['overall-earnings-graph'] = true;
                                var data = [];
                                
                                for( var i = 0; i < 3; i++) {
                                    data[i] = { label: "&nbsp;Series&nbsp;"+(i+1), data: Math.floor(Math.random()*100)+1 }
                                }
                                $.plot($("#donut"), data, {
                                    colors: ["#aad5f5", "#008fde", '#c6d695'],
                                    legend: { backgroundOpacity: 0 }, 
                                    series: {
                                        pie: { 
                                            innerRadius: 0.5,
                                            show: true
                                        }
                                    }
                                });
                            });
                        </script>
                        <div class="widget-holder widget-white">
                        <div class="widget-area overall-earnings-graph skin-white">
                            <div class="widget-head">Overall Earnings Graph</div>
                            <p class="widget-description">Morbi consequat felis vitae <a href="javascript:;">enim</a></p>
                            <div id="donut" style="width:260px;height:129px;"></div>    
                            <img class="widget-white-shadow" src="<?php echo Yii::app()->theme->baseUrl; ?>/images/photon/w_shadow.png" alt="shadow"/>
                        </div>
                        </div>
                        <!-- Overall Earnings Graph Widget ends -->


                        <!-- Realtime Data Graph Widget begins -->
                        <script>
                            $(window).load(function () {
                                if (widgetsLoaded['realtime-data-graph']) return;
                                widgetsLoaded['realtime-data-graph'] = true;
                                var data = [], totalPoints = 15;
                                function getRandomData() {
                                    if (data.length > 0)
                                        data = data.slice(1);
                                    while (data.length < totalPoints) {
                                        var prev = data.length > 0 ? data[data.length - 1] : 50;
                                        var y = prev + Math.random() * 20 - 10;
                                        if (y < 0)
                                            y = 0;
                                        if (y > 100)
                                            y = 100;
                                        data.push(y);
                                    }
                                    var res = [];
                                    for (var i = 0; i < data.length; ++i)
                                        res.push([i, data[i]])
                                    return res;
                                }
                                var updateInterval = 600;
                                var options = {
                                    series: {   shadowSize: 0, 
                                        lines: { show: true, fill:true, fillColor: { colors: [{opacity: 0.25}, {opacity: 0}] } }, 
                                        points: { show: true, radius: 2, color: '#008fde' }
                                    },
                                    grid: { show: true, borderWidth: 0.2 },
                                    xaxis: { show: true, ticks: 0 },
                                    yaxis: { show: true, min: 0, max: 100, ticks:8, color: '#bbb'},
                                    colors: ["#aad5f5"]
                                };
                                var plot = $.plot($("#realtime"), [ getRandomData() ], options);
                                function update() {
                                    plot.setData([ getRandomData() ]);
                                    plot.draw();
                                    setTimeout(update, updateInterval);
                                }
                                update();
                            });
                        </script>
                        <div class="widget-holder widget-white">
                        <div class="widget-area realtime-data-graph skin-white">
                            <div class="widget-head">Realtime Data Graph</div>
                            <p class="widget-description">Morbi consequat felis vitae <a href="javascript:;">enim</a></p>    
                            <div id="realtime" style="width:260px;height:133px;"></div>
                            <img class="widget-white-shadow" src="<?php echo Yii::app()->theme->baseUrl; ?>/images/photon/w_shadow.png" alt="shadow"/>
                        </div>
                        </div>
                        <!-- Realtime Data Graph Widget ends -->


                        <!--Select Box with Filter Search begin-->
                        <div id="Simple_Select_Box_with_Filter_Search" class="control-group row-fluid">
                            <div class="span3">
                                <label class="control-label" for="selectBoxFilter">Select Box with Filter Search</label>
                            </div>
                            <div class="span4">
                                <div class="controls">
                                    <select name="selectBoxFilter" id="selectBoxFilter">
                                        <option selected="" value="All">All</option>
                                        <option value="Beige">Beige</option>
                                        <option value="Black">Black</option>
                                        <option value="Blue">Blue</option>
                                        <option value="Bronze">Bronze</option>
                                        <option value="Brown">Brown</option>
                                        <option value="Gold">Gold</option>
                                        <option value="Gray">Gray</option>
                                        <option value="Green">Green</option>
                                        <option value="Orange">Orange</option>
                                        <option value="Pink">Pink</option>
                                        <option value="Purple">Purple</option>
                                        <option value="Red">Red</option>
                                        <option value="Silver">Silver</option>
                                        <option value="Turquoise">Turquoise</option>
                                        <option value="White">White</option>
                                        <option value="Yellow">Yellow</option>
                                    </select>
                                </div>
                            </div>
                            <script>
                                $().ready(function(){
                                    $("#selectBoxFilter").select2();
                                });
                            </script>
                        </div>
                        <!--Select Box with Filter Search end-->

                        <!--Multiple Select Box with Filter Search begin-->
                        <div id="Multiple_Select_Box_with_Filter_Search" class="control-group row-fluid">
                            <div class="span3">
                                <label class="control-label" for="multiFilter">Multiple Select Box with Filter Search</label>
                            </div>
                            <div class="span9">
                                <div class="controls">
                                    <select multiple name="multiFilter" id="multiFilter">
                                        <option value="Beige">Beige</option>
                                        <option value="Black">Black</option>
                                        <option value="Blue">Blue</option>
                                        <option value="Bronze">Bronze</option>
                                        <option value="Brown">Brown</option>
                                        <option value="Gold">Gold</option>
                                        <option value="Gray">Gray</option>
                                        <option value="Green">Green</option>
                                        <option value="Orange">Orange</option>
                                        <option value="Pink">Pink</option>
                                        <option value="Purple">Purple</option>
                                        <option value="Red">Red</option>
                                        <option value="Silver">Silver</option>
                                        <option selected="" value="Turquoise">Turquoise</option>
                                        <option value="White">White</option>
                                        <option value="Yellow">Yellow</option>
                                    </select>
                                </div>
                            </div>
                            <script>
                                $().ready(function(){
                                    $("#multiFilter").select2();
                                });
                            </script>
                        </div>
                        <!--Multiple Select Box with Filter Search end-->


                        <!--Dual Multi Select begin-->
                        <div id="Dual_Multi_Select_with_Filter_Search" class="control-group row-fluid">
                            <div class="span3">
                                <label class="control-label" for="dualMulti">Dual Multi Select</label>
                            </div>
                            <div class="span9">
                                <div class="controls">
                                    <select multiple name="dualMulti" id="dualMulti">
                                        <option selected="" value="Beige">Beige</option>
                                        <option value="Black">Black</option>
                                        <option value="Blue">Blue</option>
                                        <option value="Bronze">Bronze</option>
                                        <option value="Brown">Brown</option>
                                        <option selected="" value="Gold">Gold</option>
                                        <option selected="" value="Gray">Gray</option>
                                        <option value="Green">Green</option>
                                        <option value="Orange">Orange</option>
                                        <option value="Pink">Pink</option>
                                        <option value="Purple">Purple</option>
                                        <option value="Red">Red</option>
                                        <option value="Silver">Silver</option>
                                        <option value="Turquoise">Turquoise</option>
                                        <option value="White">White</option>
                                        <option value="Yellow">Yellow</option>
                                    </select>
                                </div>
                            </div>
                            <script>
                                $().ready(function(){
                                    $("#dualMulti").pickList();
                                });
                            </script>
                        </div>
                        <!--Dual Multi Select end-->



               <div id="Sortable_Responsive_Table" class="row-fluid">
                   <div class="span12 span-table-title">
                       <div class="alert alert-info alert-block">
                           <i class="icon-alert icon-alert-info"></i>
                           <strong>Sortable Responsive Table</strong>
                       </div>
                   </div>
               </div>


               <div class="container-fluid">
                       <!--Sortable Responsive Table begin-->
                       <div class="row-fluid">
                           <div class="span12">
                               <table class="table table-striped table-responsive" id="tableSortableRes">
                                   <thead>
                                       <tr>
                                           <th>Rendering engine</th>
                                           <th>Browser</th>
                                           <th>Platform(s)</th>
                                           <th>Engine version</th>
                                           <th>CSS grade</th>
                                       </tr>
                                   </thead>
                                   <tbody>
                                       <tr class="gradeX">
                                           <td>Trident</td>
                                           <td>
                                               Internet
                                               Explorer 
                                               4.0
                                           </td>
                                           <td>Win 95+</td>
                                           <td class="center">4</td>
                                           <td class="center">X</td>
                                       </tr>
                                       <tr class="gradeC">
                                           <td>Trident</td>
                                           <td>Internet
                                               Explorer 5.0</td>
                                           <td>Win 95+</td>
                                           <td class="center">5</td>
                                           <td class="center">C</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Trident</td>
                                           <td>Internet
                                               Explorer 5.5</td>
                                           <td>Win 95+</td>
                                           <td class="center">5.5</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Trident</td>
                                           <td>Internet
                                               Explorer 6</td>
                                           <td>Win 98+</td>
                                           <td class="center">6</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Trident</td>
                                           <td>Internet Explorer 7</td>
                                           <td>Win XP SP2+</td>
                                           <td class="center">7</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Trident</td>
                                           <td>AOL browser (AOL desktop)</td>
                                           <td>Win XP</td>
                                           <td class="center">6</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Firefox 1.0</td>
                                           <td>Win 98+ / OSX.2+</td>
                                           <td class="center">1.7</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Firefox 1.5</td>
                                           <td>Win 98+ / OSX.2+</td>
                                           <td class="center">1.8</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Firefox 2.0</td>
                                           <td>Win 98+ / OSX.2+</td>
                                           <td class="center">1.8</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Firefox 3.0</td>
                                           <td>Win 2k+ / OSX.3+</td>
                                           <td class="center">1.9</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Camino 1.0</td>
                                           <td>OSX.2+</td>
                                           <td class="center">1.8</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Camino 1.5</td>
                                           <td>OSX.3+</td>
                                           <td class="center">1.8</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Netscape 7.2</td>
                                           <td>Win 95+ / Mac OS 8.6-9.2</td>
                                           <td class="center">1.7</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Netscape Browser 8</td>
                                           <td>Win 98SE+</td>
                                           <td class="center">1.7</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Netscape Navigator 9</td>
                                           <td>Win 98+ / OSX.2+</td>
                                           <td class="center">1.8</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Mozilla 1.0</td>
                                           <td>Win 95+ / OSX.1+</td>
                                           <td class="center">1</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Mozilla 1.1</td>
                                           <td>Win 95+ / OSX.1+</td>
                                           <td class="center">1.1</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Mozilla 1.2</td>
                                           <td>Win 95+ / OSX.1+</td>
                                           <td class="center">1.2</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Mozilla 1.3</td>
                                           <td>Win 95+ / OSX.1+</td>
                                           <td class="center">1.3</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Mozilla 1.4</td>
                                           <td>Win 95+ / OSX.1+</td>
                                           <td class="center">1.4</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Mozilla 1.5</td>
                                           <td>Win 95+ / OSX.1+</td>
                                           <td class="center">1.5</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeA">
                                           <td>Gecko</td>
                                           <td>Mozilla 1.6</td>
                                           <td>Win 95+ / OSX.1+</td>
                                           <td class="center">1.6</td>
                                           <td class="center">A</td>
                                       </tr>
                                       <tr class="gradeC">
                                           <td>Misc</td>
                                           <td>IE Mobile</td>
                                           <td>Windows Mobile 6</td>
                                           <td class="center">-</td>
                                           <td class="center">C</td>
                                       </tr>
                                       <tr class="gradeC">
                                           <td>Misc</td>
                                           <td>PSP browser</td>
                                           <td>PSP</td>
                                           <td class="center">-</td>
                                           <td class="center">C</td>
                                       </tr>
                                       <tr class="gradeU">
                                           <td>Other browsers</td>
                                           <td>All others</td>
                                           <td>-</td>
                                           <td class="center">-</td>
                                           <td class="center">U</td>
                                       </tr>
                                   </tbody>
                               </table>
                           </div>
                       </div>
                       <!--Sortable Responsive Table end-->
               </div><!-- end container -->


               <div id="Sortable_Responsive_Media_Table" class="row-fluid">
                   <div class="span12 span-table-title">
                       <div class="alert alert-info alert-block">
                           <i class="icon-alert icon-alert-info"></i>
                           <strong>Sortable Responsive Media Table</strong>
                       </div>
                   </div>
               </div>


              <div class="container-fluid">
                      <!--Sortable Responsive Media Table begin-->
                <div class="row-fluid">
                   <div class="span12">
                       <table class="table table-striped table-responsive" id="tableSortableResMed">
                           <thead class="cf sorthead">
                               <tr>
                                   <th>Image</th>
                                   <th>Date Modified</th>
                                   <th>size</th>
                                   <th>Kind</th>
                                   <th>Comments</th>
                               </tr>
                           </thead>
                           <tbody>
                               <tr class="gradeX">
                                   <td>
                                       <img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/photon/user1.jpg" alt="user" />
                                   </td>
                                   <td>Today, 11:17 PM</td>
                                   <td>212kb</td>
                                   <td class="center">JPEG image</td>
                                   <td class="center">Consectetur adipisicing elit</td>
                               </tr>
                               <tr class="gradeX">
                                   <td>
                                       <img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/photon/user5.jpg" alt="user" />
                                   </td>
                                   <td>Oct 14, 2012 08:53 PM</td>
                                   <td>167kb</td>
                                   <td class="center">JPEG image</td>
                                   <td class="center">Aliquip ex ea commodo</td>
                               </tr>
                           </tbody>
                       </table>
                   </div>
                </div><!--Sortable Responsive Media Table end-->
              </div><!-- end container -->
      </form>
