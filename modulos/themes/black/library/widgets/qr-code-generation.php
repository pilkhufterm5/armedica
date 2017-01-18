<!-- QR Code Generation Widget begins -->
<script>
    $().ready(function() {
        if (widgetsLoaded['qr-code-generation']) return;
        widgetsLoaded['qr-code-generation'] = true;
        $('#qrcode').qrcode({
            text: "http://themeforest.net",
            render  : "table",
            width		: 128,
            height		: 128
        });
    });
</script>
<div class="widget-holder widget-white">
<div class="widget-area qr-code-generation skin-white">
    <div class="widget-head">QR Code Generation</div>
    <p class="widget-description">Links to <a href="http://themeforest.net/">http://themeforest.net/</a></p>
    <div id="qrcode"></div>
    <img class="widget-white-shadow" src="images/photon/w_shadow.png" alt="shadow"/>
</div>
</div>
<!-- QR Code Generation Widget ends -->