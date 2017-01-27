<?php 
/**
 *  Clase base para las respuestas del servidor SOAP
 */
class ModeloProductoBase
{

}


/**
 * Clase de disponibilidades de producto.
 */
class DisponibilidadProducto extends ModeloProductoBase
{
	/**
     * id unico
     * 
     * @var integer
     */
	public $stkmoveno;
}

/**
 * Clase de salidas de producto.
 */
class SalidaProducto extends ModeloProductoBase
{
	/**
     * id unico
     * 
     * @var integer
     */
	public $stkmoveno;
}

/**
 * Clase de transferencias de producto.
 */
class TransferenciaProducto extends ModeloProductoBase
{}

/**
 * Clase de registro de etiquetas
 * 
 *  @pw_element string $barcode Codigo de barras del producto
 *  @pw_element string $agrupador Codigo agrupador de la etiqueta
 *  @pw_element string $producto Codigo de producto
 *  @pw_element string $serialno Numero serial del producto
 *  @pw_element string $expiracion Fecha de expiracion del producto
 *  @pw_complex Etiquetas Fin de definicion+
 */
class Etiquetas
{
    /**
     * Codigo de barras del producto
     * 
     * @var integer
     */
    public $id;
    /**
     * Codigo de barras del producto
     * 
     * @var string
     */
    public $barcode;
    
    /**
     * Codigo agrupador de la etiqueta
     * 
     * @var string
     */
    public $id_agrupador;
    
    /**
     * Codigo de producto
     * 
     * @var string
     */
    public $stockid;
    
    /**
     * Numero serial del producto
     * 
     * @var string
     */
    public $serialno;
    
    /**
     * Fecha de expiracion del producto
     * 
     * @var datetime
     */
    public $expirationdate;
    
}