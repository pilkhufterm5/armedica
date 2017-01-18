<?php
if(!function_exists("ObEndLinks")){
	function EliminaPagina(&$Bufer,$pagina,$offset=0){
		if($pagina=='index.php') return true;
		$Posicion=strpos($Bufer,$pagina,$offset);
		if($Posicion!==false){
			if(
					strpos($pagina,'index.php')!==false||
					strpos($pagina,'ManualContents.php')!==false||
					strpos($pagina,'SelectProduct.php')!==false||
					strpos($pagina,'SelectCustomer.php')!==false||
					strpos($pagina,'Logout.php')!==false||
					strpos($pagina,'SelectSupplier.php')!==false||
					strpos($pagina,'crm')!==false
			){
				$TokenInicio='<td';
				$TokenFin='td>';
			}else{
				$TokenInicio='<tr';
				$TokenFin='tr>';
			}
			$offset=$Posicion+strlen($pagina);
			$from=strripos(substr($Bufer,0,$Posicion),$TokenInicio);
			$to=stripos($Bufer,$TokenFin,$Posicion)+3;
			$Elimina=true;
			$P=substr($Bufer,$from,$to-$from);//Elemento que nos interesa quitar
			$P=explode('/',$P);
			foreach($P as $item){
				$Posicion=strpos($item,$pagina);
				if($Posicion!==false&&$Posicion>0){
					$Elimina=false;
					EliminaPagina($Bufer,$pagina,$offset);
				}
			}
			if($Elimina)
				$Bufer=substr($Bufer,0,$from).
				//substr($Bufer,$from,$to-$from).//Elemento que nos interesa quitar
			substr($Bufer,$to);
		}
	}
	function ObEndLinks(){
		$Bufer=ob_get_contents();
		ob_end_clean();
		if(isset($_SESSION['BlockingPages']))
			foreach($_SESSION['BlockingPages'] as $pagina)
			if(trim($pagina)!=''){
			$offset=0;
			EliminaPagina($Bufer,$pagina);
		}
		echo $Bufer;
	}
}

if(!isset($ExternalLayout)){
    $ExternalLayout = false;
}

/* $Revision: 1.29 $ */
/* $Revision: 1.31 $ */
	// Titles and screen header
	// Needs the file config.php loaded where the variables are defined for
	//  $rootpath
	//  $title - should be defined in the page this file is included with

	if (!headers_sent()){
		header('Content-type: text/html; charset=utf-8');
	}
	if($_SESSION['title']!=-1)
    	$title = $_SESSION['title'];
    ?>
<!DOCTYPE html>
<html lang="es_MX">
    <head>
        <meta charset="utf-8">
        <title><?=$title?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
        <link REL="shortcut icon" HREF="<?=$rootpath?>/favicon.ico">
        <link REL="icon" HREF="<?=$rootpath?>/favicon.ico">

        <!--    PRODUCTION CSS -->
        <link rel="stylesheet" href="<?php echo $rootpath; ?>/modulos/themes/black/css/css_compiled/rherp-min.css" media="all" />
        <link rel="stylesheet" href="<?php echo $rootpath; ?>/modulos/themes/black/css/css_compiled/rherp-responsive-min.css" media="all" />
        <?php if($ExternalLayout == false){ ?>
        <link rel="stylesheet" href="<?php echo $rootpath; ?>/modulos/themes/black/css/css_compiled/rherp-min-part2.css" media="all" />
            <link rel="stylesheet" href="<?php echo $rootpath; ?>/modulos/themes/black/css/erp_style.css" media="all" />
            <link rel="stylesheet" href="<?php echo $rootpath; ?>/modulos/themes/black/css/select2.css" media="all" />
            <link rel="stylesheet" href="<?php echo $rootpath; ?>/modulos/themes/black/css/jwn-style.css" media="all" />
            <link rel="stylesheet" href="<?php echo $rootpath; ?>/modulos/themes/black/css/token-input.css" media="all" />            
        <?php } ?>
        <!--[if IE]>
                <link rel="stylesheet" type="text/css" href="<?php echo $rootpath; ?>/modulos/themes/black/css/css_compiled/ie-only-min.css" />
        <![endif]-->

        <!--[if lt IE 9]>
                <link rel="stylesheet" type="text/css" href="<?php echo $rootpath; ?>/modulos/themes/black/css/css_compiled/ie8-only-min.css" />
                <script type="text/javascript" src="<?php echo $rootpath; ?>/modulos/themes/black/js/plugins/excanvas.js"></script>
                <script type="text/javascript" src="<?php echo $rootpath; ?>/modulos/themes/black/js/plugins/html5shiv.js"></script>
                <script type="text/javascript" src="<?php echo $rootpath; ?>/modulos/themes/black/js/plugins/respond.min.js"></script>
                <script type="text/javascript" src="<?php echo $rootpath; ?>/modulos/themes/black/js/plugins/fixFontIcons.js"></script>
        <![endif]-->

        <?php if (file_exists(realpath(dirname(realpath(__FILE__))) . '/../modulos/themes/black/library/common/erp_javascript.php') && $ExternalLayout == false){
                include realpath(dirname(realpath(__FILE__))) . '/../modulos/themes/black/library/common/erp_javascript.php';
            }else{ ?>
            <script type="text/javascript" src="<?php echo $rootpath; ?>/modulos/themes/black/js/jquery.min.js"></script>
            <script type="text/javascript" src="<?php echo $rootpath; ?>/modulos/themes/black/js/bootstrap/bootstrap.min.js"></script>

        <?php } ?>
        <?php if($ExternalLayout == false){ ?>
            <link href="<?php echo $rootpath. '/css/'. $_SESSION['Theme']; ?>/default.css" REL="stylesheet" TYPE="text/css">
            <script type="text/javascript" src = "<?=$rootpath?>/javascript/MiscFunctions.js"></script>
        <?php } ?>
<?php
// bowikaxu realhost Feb 2008 - no backspace button or F5
if($rh_disableKeys == 1){ ?>

    <script type="text/javascript">
     if (typeof window.event == 'undefined'){
       document.onkeypress = function(e){
     	var test_var=e.target.nodeName.toUpperCase();
     	if (e.target.type) var test_type=e.target.type.toUpperCase();
     	if ((test_var == 'INPUT' && test_type == 'TEXT') || test_var == 'TEXTAREA'){
     	  return e.keyCode;
     	}else if (e.keyCode == 8 || e.keyCode == 116){
     	  e.preventDefault();
     	}
       }
     }else{
       document.onkeydown = function(){
     	var test_var=event.srcElement.tagName.toUpperCase();
     	if (event.srcElement.type) var test_type=event.srcElement.type.toUpperCase();
     	if ((test_var == 'INPUT' && test_type == 'TEXT') || test_var == 'TEXTAREA'){
     	  return event.keyCode;
     	}else if (event.keyCode == 8 || e.keyCode == 116){
     	  event.returnValue=false;
     	}
       }
     }
    </script>
<?php } ?>
</header>

<body class="body-inner">

<?php include 'rh_chat.php'; ?>

    <div class="row-fluid">
        <div class="span12">
            <div class="navbar" style="margin-bottom: 0px;">
                <div class="navbar-inner">
                    <div class="container-fluid">
                         <a data-target=".navbar-responsive-collapse" data-toggle="collapse" class="btn btn-navbar collapsed"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></a> <a href="#" class="brand"><?php echo  $_SESSION['CompanyRecord']['coyname']; ?> | <small><?php echo $title; ?></small></a>
                        <div class="nav-collapse navbar-responsive-collapse in collapse">
                            <ul class="nav">
                                <li class="divider-vertical"></li>
                                <li class="active">
                                    <a href="<?php echo $rootpath . '/UserSettings.php?' . SID; ?>" ><?php echo $_SESSION['UsersRealName']; ?></a>
                                </li>
                                <li class="divider-vertical"></li>
                                <li>
                                    <a href="<?php echo $rootpath . '/rh_upload.php?' . SID; ?>" ><?php echo _('File Upload'); ?></a>
                                </li>
                                <li class="divider-vertical"></li>
                                <li>
                                    <a ACCESSKEY="1"  href="<?php echo $rootpath . '/index.php?' . SID; ?>" ><?php echo _('Main Menu'); ?></a>
                                </li>
                                <li class="divider-vertical"></li>
                                <li>
                                    <a ACCESSKEY="2" href="<?php echo $rootpath . '/SelectCustomer.php?' . SID; ?>" ><?php echo _('Customers'); ?></a>
                                </li>
                                <li class="divider-vertical"></li>
                                <li>
                                    <a ACCESSKEY="3" href="<?php echo $rootpath . '/SelectProduct.php?' . SID; ?>" ><?php echo _('Items'); ?></a>
                                </li>
                                <li class="divider-vertical"></li>
                                <li>
                                    <a ACCESSKEY="4" href="<?php echo $rootpath . '/SelectSupplier.php?' . SID; ?>" ><?php echo _('Suppliers'); ?></a>
                                </li>
                                <li class="divider-vertical"></li>
                                <!--
                                <li class="dropdown">
                                     <a data-toggle="dropdown" class="dropdown-toggle" href="#">tst<strong class="caret"></strong></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="#">Action</a>
                                        </li>
                                        <li>
                                            <a href="#">Another action</a>
                                        </li>
                                        <li>
                                            <a href="#">Something else here</a>
                                        </li>
                                        <li class="divider">
                                        </li>
                                        <li class="nav-header">
                                            Nav header
                                        </li>
                                        <li>
                                            <a href="#">Separated link</a>
                                        </li>
                                        <li>
                                            <a href="#">One more separated link</a>
                                        </li>
                                    </ul>
                                </li>

                                -->

                            </ul>
                            <ul class="nav pull-right">
                                <li class="divider-vertical"></li>
                                <li>
                                    <form class="" METHOD="POST" ACTION="<?php echo $rootpath . '/rh_search.php?' . SID; ?>" NAME="rh_search">
                                        <div class="input-append">
                                            <input class="span2" id="txtsearch" name="txtsearch" type="text" style="width:100px;">
                                            <button class="btn" type="submit" style="margin-top: 5px;">Buscar</button>
                                        </div>
                                    </form>
                                </li>
                                <li class="divider-vertical"></li>
                                <li>
                                    <a ACCESSKEY="9" target="_blank" href="mailto:gerardo.delangel@armedica.com.mx" >Contacto</a>
                                </li>
                                <li class="divider-vertical"></li>
                                <li>
                                    <a ACCESSKEY="0" href="<?php echo $rootpath . '/Logout.php?' . SID; ?>" ><?php echo _('Logout'); ?></a>
                                </li>
                                <li class="divider-vertical"></li>
                                <!--
                                <li class="dropdown">
                                     <a data-toggle="dropdown" class="dropdown-toggle" href="#">menu test<strong class="caret"></strong></a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="#">test1</a>
                                        </li>
                                        <li>
                                            <a href="#">test2</a>
                                        </li>
                                        <li>
                                            <a href="#">test3</a>
                                        </li>
                                        <li class="divider">
                                        </li>
                                        <li>
                                            <a href="#">test4</a>
                                        </li>
                                    </ul>
                                </li> -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php if($ExternalLayout == false){ ?>
    <div id="notify-container"></div><!-- Notificaciones para llamadas Ajax -->
<?php } ?>