<!-- Task Completion Widget begins -->
<script>
    $().ready(function() {
        if (widgetsLoaded['task-completion']) return;
        widgetsLoaded['task-completion'] = true;
        setTimeout(function() {
            var target = parseInt($('.processed-pct .bar').attr('data-target'));
            $('.processed-pct .bar').attr('style', 'width: ' + target + '%');
        }, 1000);
    });
</script>
<div class="widget-holder">
<div class="widget-area task-completion">
        <div class="widget-head">
            Task Completion
            <div>
                <img src="images/photon/w_task@2x.png" alt="arrows"/>
            </div>
        </div>
        <ul>
            <li>
                Processed orders
                <span>56</span>
            </li>
            <li>Pending orders
                <span>14</span>
            </li>
            <li>Unproc. orders
                <span>12</span>
            </li>
            <li class="processed-pct">
                Processed orders:&nbsp;&nbsp;<span>63</span>
                <div class="progress progress-info">
                    <div class="bar" data-target="63" style="width: 0;"></div>
                </div>
            </li>
        </ul>
</div>
</div>
<!-- Task Completion Widget ends -->
