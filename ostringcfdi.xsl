<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:fn="http://www.w3.org/2005/xpath-functions" xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:ecc="http://www.sat.gob.mx/ecc" xmlns:donat="http://www.sat.gob.mx/donat" xmlns:divisas="http://www.sat.gob.mx/divisas" xmlns:detallista="http://www.sat.gob.mx/detallista" xmlns:ecb="http://www.sat.gob.mx/ecb" xmlns:implocal="http://www.sat.gob.mx/implocal" xmlns:terceros="http://www.sat.gob.mx/terceros" >
<!--
		En esta sección se define la inclusión de las plantillas de utilería
	-->
	<!-- Manejador de datos requeridos -->
	<xsl:template name="Requerido">
		<xsl:param name="valor"/>|<xsl:call-template name="ManejaEspacios">
			<xsl:with-param name="s" select="$valor"/>
		</xsl:call-template>
	</xsl:template>

	<!-- Manejador de datos opcionales -->
	<xsl:template name="Opcional">
		<xsl:param name="valor"/>
		<xsl:if test="$valor">|<xsl:call-template name="ManejaEspacios"><xsl:with-param name="s" select="$valor"/></xsl:call-template></xsl:if>
	</xsl:template>
	
	<!-- Normalizador de espacios en blanco -->
	<xsl:template name="ManejaEspacios">
		<xsl:param name="s"/>
		<xsl:value-of select="normalize-space(string($s))"/>
	</xsl:template>
	<!-- 
		En esta sección se define la inclusión de las demás plantillas de transformación para 
		la generación de las cadenas originales de los complementos fiscales 
	-->
	<!-- Manejador de nodos tipo ecc:EstadoDeCuentaCombustible -->
	<xsl:template match="ecc:EstadoDeCuentaCombustible">
		<!-- Iniciamos el tratamiento de los atributos de ecc:EstadoDeCuentaCombustible -->
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@tipoOperacion"/></xsl:call-template>
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@numeroDeCuenta"/></xsl:call-template>
		<xsl:call-template name="Opcional"><xsl:with-param name="valor" select="./@subTotal"/></xsl:call-template>
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@total"/></xsl:call-template>
		<!-- Iniciamos el manejo de los nodos dependientes -->
		<xsl:apply-templates select="./ecc:Conceptos"/>
	</xsl:template>

	<!-- Manejador de nodos tipo ecc:Conceptos -->
	<xsl:template match="ecc:Conceptos">
		<!-- Iniciamos el manejo de los nodos dependientes -->
		<xsl:for-each select="./ecc:ConceptoEstadoDeCuentaCombustible"><xsl:apply-templates select="."/></xsl:for-each>
	</xsl:template>
	
	<!-- Manejador de nodos tipo ecc:Traslados -->
	<xsl:template match="ecc:Traslados">
		<!-- Iniciamos el manejo de los nodos dependientes -->
		<xsl:for-each select="./ecc:Traslado"><xsl:apply-templates select="."/></xsl:for-each>
	</xsl:template>
	
	<!-- Manejador de nodos tipo ecc:ConceptoEstadoDeCuentaCombustible -->
	<xsl:template match="ecc:ConceptoEstadoDeCuentaCombustible">
		<!-- Iniciamos el tratamiento de los atributos de ecc:ConceptoEstadoDeCuentaCombustible -->
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@identificador"/></xsl:call-template>
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@fecha"/></xsl:call-template>
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@rfc"/></xsl:call-template>
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@claveEstacion"/></xsl:call-template>
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@cantidad"/></xsl:call-template>
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@nombreCombustible"/></xsl:call-template>
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@folioOperacion"/></xsl:call-template>
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@valorUnitario"/></xsl:call-template>
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@importe"/></xsl:call-template>
		<xsl:for-each select="./ecc:Traslados"><xsl:apply-templates select="."/></xsl:for-each>
	</xsl:template>
	
	<!-- Manejador de nodos tipo ecc:Traslado -->
	<xsl:template match="ecc:Traslado">
		<!-- Iniciamos el tratamiento de los atributos de ecc:Traslado -->
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@impuesto"/></xsl:call-template>
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@tasa"/></xsl:call-template>
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@importe"/></xsl:call-template>
	</xsl:template>
	
	<!-- Manejador de nodos tipo donat:Donatarias -->
	<xsl:template match="donat:Donatarias">
		<!-- Iniciamos el tratamiento de los atributos de donat:Donatarias -->
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@version"/></xsl:call-template>
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@noAutorizacion"/></xsl:call-template>
		<xsl:call-template name="Requerido"><xsl:with-param name="valor" select="./@fechaAutorizacion"/></xsl:call-template>
	</xsl:template>

	<!-- Manejador de nodos tipo divisas:Divisas -->
	<xsl:template match="divisas:Divisas">
		<!-- Iniciamos el tratamiento de los atributos de divisas:Divisas -->
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@version"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@tipoOperacion"/>
		</xsl:call-template>
	</xsl:template>
	
		<!-- Manejador de nodos tipo ECB -->
	<xsl:template match="ecb:EstadoDeCuentaBancario">
		<!-- Iniciamos el tratamiento de los atributos de EstadoDeCuentaBancario -->
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@version"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@numeroCuenta"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@nombreCliente"/>
		</xsl:call-template>
		<xsl:for-each select="ecb:Movimientos/ecb:MovimientoECBFiscal">
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@fecha"/>
			</xsl:call-template>
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@RFCenajenante"/>
			</xsl:call-template>
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@Importe"/>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
	
	<!-- Manejador de nodos tipo detallista -->
	<xsl:template match="detallista:detallista">
		<!-- Iniciamos el tratamiento de los atributos del sector detallista -->
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@documentStructureVersion"/>
		</xsl:call-template>
		<xsl:for-each select="detallista:orderIdentification/detallista:referenceIdentification">
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="."/>
			</xsl:call-template>
		</xsl:for-each>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="detallista:orderIdentification/detallista:ReferenceDate"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="detallista:buyer/detallista:gln"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="detallista:seller/detallista:gln"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="detallista:seller/detallista:alternatePartyIdentification"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="detallista:totalAmount/detallista:Amount"/>
		</xsl:call-template>
		<xsl:for-each select="detallista:TotalAllowanceCharge/detallista:specialServicesType">
			<xsl:call-template name="Opcional">
				<xsl:with-param name="valor" select="."/>
			</xsl:call-template>
		</xsl:for-each>
		<xsl:for-each select="detallista:TotalAllowanceCharge/detallista:Amount">
			<xsl:call-template name="Opcional">
				<xsl:with-param name="valor" select="."/>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>

	<!-- Manejador de nodos tipo implocal -->
	<xsl:template match="implocal:ImpuestosLocales">
		<!--Iniciamos el tratamiento de los atributos de ImpuestosLocales -->
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@version"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@TotaldeRetenciones"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@TotaldeTraslados"/>
		</xsl:call-template>
		<xsl:for-each select="implocal:RetencionesLocales">
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@ImpLocRetenido"/>
			</xsl:call-template>
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@TasadeRetencion"/>
			</xsl:call-template>
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@Importe"/>
			</xsl:call-template>
		</xsl:for-each>
		<xsl:for-each select="implocal:TrasladosLocales">
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@ImpLocTrasladado"/>
			</xsl:call-template>
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@TasadeTraslado"/>
			</xsl:call-template>
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@Importe"/>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>

	<!-- Manejador de nodos tipo PorCuentadeTerceros -->
	<xsl:template match="terceros:PorCuentadeTerceros">
		<!--Iniciamos el tratamiento de los atributos del complemento concepto Por cuenta de Terceros -->
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@version"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@rfc"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@nombre"/>
		</xsl:call-template>
		<!--Iniciamos el tratamiento de los atributos de la información fiscal del complemento de terceros -->
		<xsl:for-each select="./terceros:InformacionFiscalTercero">
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@calle"/>
			</xsl:call-template>
			<xsl:call-template name="Opcional">
				<xsl:with-param name="valor" select="./@noExterior"/>
			</xsl:call-template>
			<xsl:call-template name="Opcional">
				<xsl:with-param name="valor" select="./@noInterior"/>
			</xsl:call-template>
			<xsl:call-template name="Opcional">
				<xsl:with-param name="valor" select="./@colonia"/>
			</xsl:call-template>
			<xsl:call-template name="Opcional">
				<xsl:with-param name="valor" select="./@localidad"/>
			</xsl:call-template>
			<xsl:call-template name="Opcional">
				<xsl:with-param name="valor" select="./@referencia"/>
			</xsl:call-template>
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@municipio"/>
			</xsl:call-template>
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@estado"/>
			</xsl:call-template>
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@pais"/>
			</xsl:call-template>
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@codigoPostal"/>
			</xsl:call-template>
		</xsl:for-each>
		<!-- Manejo de los atributos de la información aduanera del complemento de terceros -->
		<xsl:for-each select=".//terceros:InformacionAduanera">
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@numero"/>
			</xsl:call-template>
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@fecha"/>
			</xsl:call-template>
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@aduana"/>
			</xsl:call-template>
		</xsl:for-each>
		<!-- Manejo de los atributos de la cuenta predial del complento de terceros -->
		<xsl:for-each select=".//terceros:CuentaPredial">
			<xsl:call-template name="Requerido">
				<xsl:with-param name="valor" select="./@numero"/>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
	
	<!-- Aquí iniciamos el procesamiento de la cadena original con su | inicial y el terminador || -->
	<xsl:template match="/">|<xsl:apply-templates select="/cfdi:Comprobante"/>||</xsl:template>
	<!--  Aquí iniciamos el procesamiento de los datos incluidos en el comprobante -->
	<xsl:template match="cfdi:Comprobante">
		<!-- Iniciamos el tratamiento de los atributos de comprobante -->
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@version"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@fecha"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@tipoDeComprobante"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@formaDePago"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="./@condicionesDePago"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@subTotal"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="./@descuento"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="./@TipoCambio"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="./@Moneda"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@total"/>
		</xsl:call-template>
		<!--
			Llamadas para procesar al los sub nodos del comprobante
		-->
		<xsl:apply-templates select="./cfdi:Emisor"/>
		<xsl:apply-templates select="./cfdi:Receptor"/>
		<xsl:apply-templates select="./cfdi:Conceptos"/>
		<xsl:apply-templates select="./cfdi:Impuestos"/>
		<xsl:apply-templates select="./cfdi:Complemento"/>
	</xsl:template>
	<!-- Manejador de nodos tipo Emisor -->
	<xsl:template match="cfdi:Emisor">
		<!-- Iniciamos el tratamiento de los atributos del Emisor -->
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@rfc"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@nombre"/>
		</xsl:call-template>
		<!--
			Llamadas para procesar al los sub nodos del comprobante
		-->
		<xsl:apply-templates select="./cfdi:DomicilioFiscal"/>
		<xsl:if test="./cfdi:ExpedidoEn">
			<xsl:call-template name="Domicilio">
				<xsl:with-param name="Nodo" select="./cfdi:ExpedidoEn"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	<!-- Manejador de nodos tipo Receptor -->
	<xsl:template match="cfdi:Receptor">
		<!-- Iniciamos el tratamiento de los atributos del Receptor -->
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@rfc"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="./@nombre"/>
		</xsl:call-template>
		<!--
			Llamadas para procesar al los sub nodos del Receptor
		-->
		<xsl:call-template name="Domicilio">
			<xsl:with-param name="Nodo" select="./cfdi:Domicilio"/>
		</xsl:call-template>
	</xsl:template>
	<!-- Manejador de nodos tipo Conceptos -->
	<xsl:template match="cfdi:Conceptos">
		<!-- Llamada para procesar los distintos nodos tipo Concepto -->
		<xsl:for-each select="./cfdi:Concepto">
			<xsl:apply-templates select="."/>
		</xsl:for-each>
	</xsl:template>
	<!-- Manejador de nodos tipo Impuestos -->
	<xsl:template match="cfdi:Impuestos">
		<xsl:for-each select="./cfdi:Retenciones/cfdi:Retencion">
			<xsl:apply-templates select="."/>
		</xsl:for-each>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="./@totalImpuestosRetenidos"/>
		</xsl:call-template>
		<xsl:for-each select="./cfdi:Traslados/cfdi:Traslado">
			<xsl:apply-templates select="."/>
		</xsl:for-each>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="./@totalImpuestosTrasladados"/>
		</xsl:call-template>
	</xsl:template>
	<!-- Manejador de nodos tipo Retencion -->
	<xsl:template match="cfdi:Retencion">
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@impuesto"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@importe"/>
		</xsl:call-template>
	</xsl:template>
	<!-- Manejador de nodos tipo Traslado -->
	<xsl:template match="cfdi:Traslado">
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@impuesto"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@tasa"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@importe"/>
		</xsl:call-template>
	</xsl:template>
	<!-- Manejador de nodos tipo Complemento -->
	<xsl:template match="cfdi:Complemento">
		<xsl:for-each select="./*">
			<xsl:apply-templates select="."/>
		</xsl:for-each>
	</xsl:template>
	<!--
		Manejador de nodos tipo Concepto
	-->
	<xsl:template match="cfdi:Concepto">
		<!-- Iniciamos el tratamiento de los atributos del Concepto -->
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@cantidad"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="./@unidad"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="./@noIdentificacion"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@descripcion"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@valorUnitario"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@importe"/>
		</xsl:call-template>
		<!--
			Manejo de los distintos sub nodos de información aduanera de forma indistinta 
			a su grado de dependencia
		-->
		<xsl:for-each select=".//cfdi:InformacionAduanera">
			<xsl:apply-templates select="."/>
		</xsl:for-each>
		<!-- Llamada al manejador de nodos de Cuenta Predial en caso de existir -->
		<xsl:if test="./cfdi:CuentaPredial">
			<xsl:apply-templates select="./cfdi:CuentaPredial"/>
		</xsl:if>
		<!-- Llamada al manejador de nodos de ComplementoConcepto en caso de existir -->
		<xsl:if test="./cfdi:ComplementoConcepto">
			<xsl:apply-templates select="./cfdi:ComplementoConcepto"/>
		</xsl:if>
	</xsl:template>
	<!-- Manejador de nodos tipo Información Aduanera -->
	<xsl:template match="cfdi:InformacionAduanera">
		<!-- Manejo de los atributos de la información aduanera -->
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@numero"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@fecha"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@aduana"/>
		</xsl:call-template>
	</xsl:template>
	<!-- Manejador de nodos tipo Información CuentaPredial -->
	<xsl:template match="cfdi:CuentaPredial">
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@numero"/>
		</xsl:call-template>
	</xsl:template>
	<!-- Manejador de nodos tipo ComplementoConcepto -->
	<xsl:template match="cfdi:ComplementoConcepto">
		<xsl:for-each select="./*">
			<xsl:apply-templates select="."/>
		</xsl:for-each>
	</xsl:template>
	<!-- Manejador de nodos tipo domicilio fiscal -->
	<xsl:template match="cfdi:DomicilioFiscal">
		<!-- Iniciamos el tratamiento de los atributos del Domicilio Fiscal -->
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@calle"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="./@noExterior"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="./@noInterior"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="./@colonia"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="./@localidad"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="./@referencia"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@municipio"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@estado"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@pais"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="./@codigoPostal"/>
		</xsl:call-template>
	</xsl:template>
	<!-- Manejador de nodos tipo Domicilio -->
	<xsl:template name="Domicilio">
		<xsl:param name="Nodo"/>
		<!-- Iniciamos el tratamiento de los atributos del Domicilio  -->
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="$Nodo/@calle"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="$Nodo/@noExterior"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="$Nodo/@noInterior"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="$Nodo/@colonia"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="$Nodo/@localidad"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="$Nodo/@referencia"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="$Nodo/@municipio"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="$Nodo/@estado"/>
		</xsl:call-template>
		<xsl:call-template name="Requerido">
			<xsl:with-param name="valor" select="$Nodo/@pais"/>
		</xsl:call-template>
		<xsl:call-template name="Opcional">
			<xsl:with-param name="valor" select="$Nodo/@codigoPostal"/>
		</xsl:call-template>
	</xsl:template>
</xsl:stylesheet>