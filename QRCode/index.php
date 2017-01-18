<?php


function qr_code($data, $ecc = 'M', $type = 'J', $size = '2', $version = '',$name='') {
    $qr_code_plugin_dir = str_replace('\\','/',dirname(__FILE__)).'/';
    $qr_code_plugin_cache_dir = str_replace('\\','/',dirname(__FILE__)).'/cache/';
	//global $qr_code_plugin_cache_dir,$qr_code_plugin_dir, $debug;
	$params = array('d'=>$data, 'e'=>$ecc, 't'=>$type, 's'=>$size, 'v'=>$version, );
	$cache_id = md5(serialize($params));
	$cache_file = $name.($type == 'J' ? '.jpg' : '.png');
	if (is_writable($qr_code_plugin_cache_dir) && !is_readable($qr_code_plugin_cache_dir.$cache_file)) {
		$qrcode_data_string = urlencode($data);
		$qrcode_error_correct = $ecc;
		$qrcode_module_size = $size;
		$qrcode_image_type = $type;
        $qrcode_version=$version;
		ob_start();
		require (str_replace('\\','/',dirname(__FILE__)).'/qr_img.php');
		$out = ob_get_contents();
		ob_end_clean();
		$cache = fopen($qr_code_plugin_cache_dir.$cache_file, 'w+');
		fwrite($cache, $out);
		fclose($cache);
	} elseif (!is_writable($qr_code_plugin_cache_dir)) {
		throw new Exception('Error al escribir en los directorios'.$qr_code_plugin_dir.' '.$qr_code_plugin_cache_dir);
	}
	return $qr_code_plugin_cache_dir.$cache_file;
}

//qr_code($data,'M','J','7','','Vcard');

?>