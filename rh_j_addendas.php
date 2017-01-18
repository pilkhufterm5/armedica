<?php
class Addendas{
    function soriana($idDebtortrans, $serie, $siguienteFolio, $Comprobante_subtotal, $Comprobante_total, $addendaSoriana_proveedor, $addendaSoriana_fechaRemision, $addendaSoriana_folioNotaEntrada, $addendaSoriana_numeroDeCajas, $addendaSoriana_tienda, $Conceptos){
        $remision = $serie . '-' . $siguienteFolio;

        $xmlArticulos = '';
        $cantidadArticulos = 0;
        $codigosDeArticulos = array();
        for($i = 0; $i < count($Conceptos); $i++){
            $noIdentificacion = $Conceptos[$i]['noIdentificacion'];
            $cantidad = $Conceptos[$i]['cantidad'];
            $valorUnitario = $Conceptos[$i]['valorUnitario'];

            //verificamos cuantos codigos de articulo hay diferentes
            if(!in_array($noIdentificacion, $codigosDeArticulos)){
                $cantidadArticulos++;
                array_push($codigosDeArticulos, $noIdentificacion);
            }
            //\verificamos cuantos codigos de articulo hay diferentes

            $xmlArticulos .=
            "<Articulos Id=\"Articulos" . ($i+1) . "\" RowOrder=\"" . ($i+1) . "\">
                <Proveedor>$addendaSoriana_proveedor</Proveedor>
                <Remision>$remision</Remision>
                <FolioPedido>$idDebtortrans</FolioPedido>
                <Tienda>$addendaSoriana_tienda</Tienda>
                <Codigo>$noIdentificacion</Codigo>
                <CantidadUnidadCompra>$cantidad</CantidadUnidadCompra>
                <CostoNetoUnidadCompra>$valorUnitario</CostoNetoUnidadCompra>
                <PorcentajeIEPS>0</PorcentajeIEPS>
                <PorcentajeIVA>0</PorcentajeIVA>
            </Articulos>\n";
        }

        $xmlXsd =
        "<DSCargaRemisionProv>
                <Remision Id=\"Remision1\" RowOrder=\"1\">
                    <Proveedor>$addendaSoriana_proveedor</Proveedor>
                    <Remision>$remision</Remision>
                    <Consecutivo>0</Consecutivo>
                    <FechaRemision>$addendaSoriana_fechaRemision</FechaRemision>
                    <Tienda>$addendaSoriana_tienda</Tienda>
                    <TipoMoneda>1</TipoMoneda>
                    <TipoBulto>1</TipoBulto>
                    <EntregaMercancia>1</EntregaMercancia>
                    <CumpleReqFiscales>true</CumpleReqFiscales>
                    <CantidadBultos>$addendaSoriana_numeroDeCajas</CantidadBultos>
                    <Subtotal>$Comprobante_subtotal</Subtotal>
                    <Descuentos>0</Descuentos>
                    <IEPS>0</IEPS>
                    <IVA>0</IVA>
                    <OtrosImpuestos>0</OtrosImpuestos>
                    <Total>$Comprobante_total</Total>
                    <CantidadPedidos>1</CantidadPedidos>
                    <FechaEntregaMercancia>$addendaSoriana_fechaRemision</FechaEntregaMercancia>
        <Cita>0</Cita>
        <FolioNotaEntrada>$addendaSoriana_folioNotaEntrada</FolioNotaEntrada>
                </Remision>
                <Pedidos Id=\"Pedidos1\" RowOrder=\"1\">
                    <Proveedor>$addendaSoriana_proveedor</Proveedor>
                    <Remision>$remision</Remision>
                    <FolioPedido>$idDebtortrans</FolioPedido>
                    <Tienda>$addendaSoriana_tienda</Tienda>
                    <CantidadArticulos>$cantidadArticulos</CantidadArticulos>
        <PedidoEmitidoProveedor>SI</PedidoEmitidoProveedor>
                </Pedidos>\n"
        .
        $xmlArticulos
        .
        "</DSCargaRemisionProv>\n";

        return $xmlXsd;
    }
}
?>
