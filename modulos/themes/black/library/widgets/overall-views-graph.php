<!-- Overall Views Graph Widget begins -->
<script>
    $(window).load(function () {
        if (widgetsLoaded['overall-views-graph']) return;
        widgetsLoaded['overall-views-graph'] = true;
        var d1 = [];
        for (var i = 0; i <= 30; i += 1)
            d1.push([i, parseInt(Math.random() * 30)]);
        var d2 = [];
        for (var i = 0; i <= 30; i += 1)
            d2.push([i, parseInt(Math.random() * 30)]);
        $.plot($("#placeholder"), [ d1, d2 ], {
            grid: { show: true, borderWidth: 0.2 },
            xaxis: { show: true, ticks: 0 },
            yaxis: { show: true, ticks: 8, color: '#bbb'},
            colors: ["#aad5f5", "#008fde"],
            series: {
                stack: 0,
                fill: 1,
                bars: { show: true, barWidth: 0.9, lineWidth: 0, fill: 1 }
            }
        });
    });
</script>
<div class="widget-holder widget-white">
<div class="widget-area overall-views-graph skin-white">
    <div class="widget-head">Overall Views Graph</div>
    <p class="widget-description">Morbi consequat felis vitae <a href="javascript:;">enim</a></p>
    <div id="placeholder" style="width:260px;height:133px;"></div>
    <img class="widget-white-shadow" src="images/photon/w_shadow.png" alt="shadow"/>
</div>
</div>
<!-- Overall Views Graph Widget ends -->
