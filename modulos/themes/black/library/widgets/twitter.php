<!-- Twitter Widget begins -->
<script>
    $().ready(function() {
        if (widgetsLoaded['tweet-widget']) return;
        widgetsLoaded['tweet-widget'] = true;
        jqtweet.loadTweets({
            user: 'envato',
            numTweets: 1
        });
    });
</script>
<div class="widget-holder">
<div class="widget-area tweet-widget">
        <div class="widget-head">
            Twitter
            <div>
                <img src="images/photon/w_twitter@2x.png" alt="twitter"/>
            </div>
        </div>
        <p class="widget-description"><a href="https://twitter.com/envato" target="_blank">@envato</a>'s latest tweet:</p>    
        <div id="jqtwitter"></div>
</div>
</div>
<!-- Twitter Widget ends -->
