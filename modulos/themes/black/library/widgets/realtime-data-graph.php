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
    <img class="widget-white-shadow" src="images/photon/w_shadow.png" alt="shadow"/>
</div>
</div>
<!-- Realtime Data Graph Widget ends -->
