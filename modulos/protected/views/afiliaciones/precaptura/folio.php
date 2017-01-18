

    <div class="container-fluid bootspin">
        <!--Spinners begin-->
        <div class="control-group row-fluid">
            <div class="span3">
                <label class="control-label" for="spin1">
                    <?php echo ('Folio Afiliado'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Folio del Afiliado"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span9">
                <div class="controls">
                    <input type="text" id="folio" name="folio" />
                </div>
            </div>
        </div><!--Spinners end-->

        <script>
            $('.spinner').spinner({
                min: 0,
                max: 10000
            });
        </script>
    </div><!-- end container -->

