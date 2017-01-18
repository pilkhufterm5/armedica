

<form class="form-horizontal" method="POST">
    <div class="container-fluid bootspin">
        <div class="form-legend" id="Spinners">Information</div>

            <!--Spinners begin-->
            <div class="control-group row-fluid">
                <div class="span3">
                    <label class="control-label" for="spin1">
                        <?php echo ('Company Name'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Company Name."><i class="icon-photon info-circle"></i></a>
                    </label>
                </div>
                <div class="span9">
                    <div class="controls">
                        <label class="control-label">Text</label>
                    </div>
                </div>
            </div>
            
            <div class="control-group row-fluid">
                <div class="span3">
                    <label class="control-label" for="spin2">
                        <?php echo ('Source'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="SS."><i class="icon-photon info-circle"></i></a>
                    </label>
                </div>
                <div class="span9">
                    <div class="controls">
                        <label class="control-label">Text</label> 
                    </div>
                </div>
            </div>
            
            <div class="control-group row-fluid">
                <div class="span3">
                    <label class="control-label" for="spin3">
                        <?php echo _('Parent Account '); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Parent Account."><i class="icon-photon info-circle"></i></a>
                    </label>
                </div>
                <div class="span9">
                    <div class="controls">
                        <label class="control-label">Text</label> 
                    </div>
                </div>
            </div>
            
            <div class="control-group row-fluid">
                <div class="span3">
                    <label class="control-label" for="spin4">
                        <?php echo _('Description'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Description."><i class="icon-photon info-circle"></i></a>
                    </label>
                </div>
                <div class="span9">
                    <div class="controls">
                        <label class="control-label">Text</label> 
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
</form>

