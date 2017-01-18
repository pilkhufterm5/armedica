<?php
/**
 * RealHost 31 de Marzo del 2010
 * Ricardo Abularach Garcia
 * Seccion principal del catalogo de sustancias activas
 */


$PageSecurity = 2;
include('includes/session.inc');
$title = _('Administraci&oacute;n de Catalogos');
include('includes/header.inc');

echo '<A HREF="'. $rootpath . '/rh_catalogo_marca.php?">'. _('Regresar a Marcas'). '</A><BR>';
echo '<center>';
echo '<BR><FONT SIZE=3><B>Listado de Marcas</B></FONT><br /><br />';

/*listado de todas las maquinas disponibles*/
$sql_ = "Select * from rh_marca ORDER BY nombre";
$maquinas = DB_query($sql_,$db);

echo '<TABLE CELLPADDING=2 COLSPAN=7 BORDER=2 width="100%">';

/*heeaders de la tabla*/
$TableHeader = "<TR>
                    <TD class='tableheader'>" . _('Id') . "</TD>
                    <TD class='tableheader'>" . _('C&oacute;digo') . "</TD>
                    <TD class='tableheader'>" . _('Marca') . "</TD>
                    <TD class='tableheader'>&nbsp;</TD>
                </TR>";

echo $TableHeader;
$j = 1;
$k=0;

while ($myrow=DB_fetch_array($maquinas)) {
        if ($k==1){
                echo "<tr bgcolor='#CCCCCC'>";
                $k=0;
        } else {
                echo "<tr bgcolor='#EEEEEE'>";
                $k++;
        }

        printf("<td ALIGN='CENTER'>%s</td>
                <td ALIGN='CENTER'>%s</td>
                <td>%s</td>
                <td><a href='rh_catalogo_marca.php?operacion=editar&ID=%s'>Editar</a></td>
                </tr>",
                $myrow['id'],
                $myrow['codigo'],
                $myrow['nombre'],
                $myrow['id']);

        $j++;
        If ($j == 12){
                $j=1;
                echo $TableHeader;
        }
}
echo "</table>";
include('includes/footer.inc');
?>