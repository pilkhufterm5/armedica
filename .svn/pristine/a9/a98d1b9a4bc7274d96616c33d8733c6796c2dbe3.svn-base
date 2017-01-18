<?php
//Php
$PageSecurity = 2;
include('includes/session.inc');
$query;
switch($_POST["request"]) {
    case 'loadSelectInDiv':
        $valueColumn = str_replace('\\', '', $_POST['valueColumn']);
        //el str_replace es necesario ya que en includes/session.inc se utiliza mysql_real_escape_string($unescaped_string) para POST y GET y arruina el valor
        $optionColumn = str_replace('\\', '', $_POST['optionColumn']);
        $table = str_replace('\\', '', $_POST['table']);
        $query = "select $valueColumn value, $optionColumn my_option from $table";
    break;
}
$resultado = DB_query($query, $db);
$arreglo = array();
while($objeto = mysql_fetch_object($resultado))
    $arreglo[] = $objeto;
print json_encode($arreglo);
?>
