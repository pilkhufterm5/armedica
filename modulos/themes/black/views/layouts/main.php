<?php /*  */
global $rootpath;
$URI=$_SERVER["UrlERP_BASE"];
$rootpath=rtrim($URI,"/");
$theme= $_SESSION['Theme'];
$title=CHtml::encode(Yii::app()->name." ");
include($_SERVER['LocalERP_path'].'/includes/header.inc');
?>

<script type="text/javascript">
    $(document).on('ready', function() {
        $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
    });
</script>
<style>
    .table thead th {
        font-size: 12px;
        line-height: 20px;
    }

    .table td {
        font-size: 10px;
    }

    .select2-container .select2-choice {
        display: block;
        margin-top: -5px !important;
    }

    .form-search input, .form-inline input, .form-horizontal input, .form-search textarea, .form-inline textarea, .form-horizontal textarea, .form-search .help-inline, .form-inline .help-inline, .form-horizontal .help-inline, .form-search .uneditable-input, .form-inline .uneditable-input, .form-horizontal .uneditable-input, .form-search .input-prepend, .form-inline .input-prepend, .form-horizontal .input-prepend, .form-search .input-append, .form-inline .input-append, .form-horizontal .input-append {
        display: inline-block;
        *display: inline;
        *zoom: 1;
        /* margin-bottom: -30px !important; */
        /*margin-bottom: 5px;*/
        vertical-align: middle
    }
    input[type="checkbox"] {
        margin-top: 22px;
    }
</style>

<?php if(($msgs=Yii::app()->user->getFlashes())!==null and $msgs!==array()):?>
    <div class="container" style="padding-top:0; ">
      <div class="row-fluid">
        <div class="span12">
          <?php foreach($msgs as $type => $message):?>
            <div class="alert alert-<?php echo $type?>">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <h4><?php echo ucfirst($type)?>!</h4>
              <?php echo $message?>
            </div>
          <?php endforeach;?>
        </div>
      </div>
    </div>
<?php endif;?>

<?php echo $content;?>

<div class="clear"></div>

<?php
if (file_exists($_SERVER['LocalERP_path'].'/includes/footer.inc')){
    //include($_SERVER['LocalERP_path'].'/includes/footer.inc');
}
?>

<?php
    echo '<TABLE width="100%" ALIGN="center" ID="footer" style="margin-top: 15%;">';
    echo '<TR>';
    echo '<TD ALIGN="center">';
    echo '<A HREF="http://www.weberp.org" TARGET="_blank"><IMG SRC="'. $rootpath . '/css/webERPsm.gif" BORDER="0" ALT="" TITLE="webERP ' . _('Copyright') . ' &copy; Logic Works Ltd - ' . date('Y') . '"></A>';
    echo '<BR>' . _('Version') . ' - ' . $Version;
    echo '</TD></TR>';
    echo '<TR><TD ALIGN="center" CLASS="footer">webERP ' . _('Copyright') . ' &copy; Logic Works Ltd - '.date('Y').'</TD></TR>';
    echo '</TABLE>';
    echo '</div>';
    echo '</BODY>';
    echo '</HTML>';
?>
        </div>
    </body>
</html>
