
<html>
<head>

</head>
<body>
<?php
include_once 'php-ofc-library/open_flash_chart_object.php';

open_flash_chart_object( 320, 240, 'http://'. $_SERVER['SERVER_NAME'] .':90/BASE_ERP308/Top10Customers.php', false );
open_flash_chart_object( 320, 240, 'http://'. $_SERVER['SERVER_NAME'] .':90/BASE_ERP308/Top10Products.php', false );

?>



</body>
</html>