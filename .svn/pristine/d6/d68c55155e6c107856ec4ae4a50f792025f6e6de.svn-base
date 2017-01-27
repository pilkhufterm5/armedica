<?php

/* $Revision: 1.27 $ */

	echo '<BR><BR>';
	echo '</TD>';
	echo '</TR>';
	echo '</TABLE>';

	echo '</TD>';
/*Do the borders */
	
	echo '</TABLE>';
    
      require("SimplePie/simplepie.inc");
      require("SimplePie/simplepie_yahoo_weather.inc");

      if(isset($_SESSION['CLIMA'][1])){
        $code = $_SESSION['CLIMA'][1];
        $path = "http://weather.yahooapis.com/forecastrss?u=c&p=";
        $feed = new SimplePie();
        $feed->set_feed_url($path.$code);
        $feed->set_item_class('SimplePie_Item_YWeather');
        $feed->init();
        $weather = $feed->get_item(0);
      }
      if(isset($_SESSION['CLIMA'][2])){
        $code = $_SESSION['CLIMA'][2];
        $path = "http://weather.yahooapis.com/forecastrss?u=c&p=";
        $feed2 = new SimplePie();
        $feed2->set_feed_url($path.$code);
        $feed2->set_item_class('SimplePie_Item_YWeather');
        $feed2->init();
        $weather2 = $feed2->get_item(0);
      }
      if(isset($_SESSION['CLIMA'][3])){
        $code = $_SESSION['CLIMA'][3];
        $path = "http://weather.yahooapis.com/forecastrss?u=c&p=";
        $feed3 = new SimplePie();
        $feed3->set_feed_url($path.$code);
        $feed3->set_item_class('SimplePie_Item_YWeather');
        $feed3->init();
        $weather3 = $feed3->get_item(0);
      }
?>
<table width="100%" height="30px" border="0" cellpadding="0" cellspacing="2" style="font: Verdana; size: 14px; font-weight: bolder; color: #FFF; background: #000; ">
    <tr>
        
        <td width="">Dolar</td>
        <td width="" style="cursor:pointer;" onclick="window.location='Currencies.php'"><?php echo '$ '.number_format($_SESSION['TCACTUAL'],6,'.',''); ?></td>
        <td width="5%"><img src="<?php if($_SESSION['TCACTUAL']>$_SESSION['TCPREVIUS']){echo 'images/up.png';}elseif($_SESSION['TCACTUAL']<$_SESSION['TCPREVIUS']){echo 'images/down.png';}else{echo 'images/same.png';} ?>" height="16px;" width="16px;" /></td>
        <td width=""></td>
        
        <?php if(isset($_SESSION['CLIMA'][1])){ ?>
        <td width=""><img src="<?php echo $weather->get_condition_image(); ?> " height="28px;" width="28px;" /></td>
        <td width="">Clima en <?php echo $weather->get_city();?></td>
        <td ><?php echo $weather->get_temperature().' C';?></td>
        <?php }?>
        
        <?php if(isset($_SESSION['CLIMA'][2])){ ?>
        <td ></td>
        <td width=""><img src="<?php echo $weather2->get_condition_image(); ?> " height="28px;" width="28px;" /></td>
        <td width="">Clima en <?php echo $weather2->get_city();?></td>
        <td><?php echo $weather2->get_temperature().' C';?></td>
        <?php }?>
        
        <?php if(isset($_SESSION['CLIMA'][3])){ ?>
        <td ></td>
        <td width=""><img src="<?php echo $weather3->get_condition_image(); ?> " height="28px;" width="28px;" /></td>
        <td width="">Clima en <?php echo $weather3->get_city();?></td>
        <td ><?php echo $weather3->get_temperature().' C';?></td>
         <?php }?>
         
        <td style="text-align:right"><?php echo htmlentities(strftime('%A')).', '.(date($_SESSION['DefaultDateFormat']).' | '.(strftime(($DefaultClock==12) ? '%I:%M %p' : '%H:%M')))?></td>
        
    </tr>
</table>
<?php
	
   //	echo '<p style="text-align:right">'.(utf8_encode(strftime('%A'))).', '.(date($_SESSION['DefaultDateFormat']).' | '.(strftime(($DefaultClock==12) ? '%I:%M %p' : '%H:%M'))).'</p>';
	
	
	echo '<TABLE width="100%" ALIGN="center" ID="footer">';
	
	echo '<TR>';
	echo '<TD ALIGN="center">';
	echo '<A HREF="http://www.weberp.org" TARGET="_blank"><IMG SRC="'. $rootpath . '/css/webERPsm.gif" BORDER="0" ALT="" TITLE="webERP ' . _('Copyright') . ' &copy; Logic Works Ltd - ' . date('Y') . '"></A>';
	echo '<BR>' . _('Version') . ' - ' . $Version;
	echo '</TD></TR>';

	echo '<TR><TD ALIGN="center" CLASS="footer">webERP ' . _('Copyright') . ' &copy; Logic Works Ltd - '.date('Y').'</TD></TR>';
	
//	echo '<TR><TD ALIGN="center"><a href="http://sourceforge.net"><img src="http://sflogo.sourceforge.net/sflogo.php?group_id=70949&amp;type=1" width="88" height="31" border="0" alt="SourceForge.net Logo" /></a></TD></TR>';

	echo '</TABLE>';
	
    echo '</div>';
    
	echo '</BODY>';
	echo '</HTML>';

?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-570099-71', 'auto');
  ga('send', 'pageview');

</script>
