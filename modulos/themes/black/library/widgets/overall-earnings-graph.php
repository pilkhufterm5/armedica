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
    <img class="widget-white-shadow" src="images/photon/w_shadow.png" alt="shadow"/>
</div>
</div>
<!-- Overall Earnings Graph Widget ends -->
