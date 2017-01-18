<?php
class CotizadorController extends Controller{
	
	public function actionSearchfolio($callback = null)
	{
		if(!empty($_POST['Search']['string'])){
			// Obtenemos la palabra clave
			$keyword = $_POST['Search']['string'];
			// Buscamos coincidencias
			$ListaFolios = Yii::app()->db
			->createCommand()
			->select("rh_titular.folio,
			rh_titular.name,
			rh_titular.apellidos,
			rh_titular.address1,
			rh_titular.address2,
			rh_titular.address4,
			rh_titular.address6,
			rh_titular.rh_tel,
			rh_titular.cuadrante1,
			rh_titular.cuadrante2,
			rh_titular.cuadrante3,
			rh_titular.address10")
			->from("rh_titular")
			->where("folio = :keyword ",
			array(":keyword" =>"$keyword"))->queryAll();
			foreach ($ListaFolios as $Data) {
		        
		        $query = "select stockid, frecuencia_pago, paymentid, empresa from rh_cobranza where folio ='".$Data['folio']."'";
		        $res=sql_dq($query);
		        
		        $row=mysql_fetch_assoc($res);

				$_Data[] = array(
				'value' => $Data['folio']. ' '.'-' .' '.'Nombre:'.' '.$Data['name']
				. ' ' .$Data['apellidos']. ' '.' '.' '.'Calle:'.' '.$Data['address1']
				. ' '.' '.' Número:'.' '.$Data['address2'].' '.' '.' Colonia:'.' '.$Data['address4']
				.' '.' '.' Tel:'.' '.$Data['rh_tel'].' '.' '.' Entre Calles:'.' '.$Data['address6']
				.' '.' '.' Cuadrantes:'.' '.$Data['cuadrante1'].' '.$Data['cuadrante2'].' '.$Data['cuadrante3']
				.' '.' '.' Código Postal:'.' '.$Data['address10'],
				'folio'=> $Data['folio'],
				'socio' => array(
							'folio' => $Data['folio'],
							'nombre' => $Data['name'],
							'apellidos' => $Data['apellidos'],
							'calle' => $Data['address1'],
							'numero' => $Data['address2'],
							'colonia' => $Data['address4'],
							'tel' => $Data['rh_tel'],
							'entre_calles' => $Data['address6'],
							'cuadrantes' => $Data['cuadrante1'].' '.$Data['cuadrante2'].' '.$Data['cuadrante3'],
							'codigo_postal' => $Data['address10'],
							'stockid' => $row['stockid'],
							'frecuencia_pago' => $row['frecuencia_pago'],
							'paymentid' => $row['paymentid'],
							'empresa' => $row['empresa'],
						),

				);





			}
		}
		if(empty($_Data)){
			$_Data[] = array(
			'value' => 'ESTE FOLIO NO SE ENCUENTRA REGISTRADO EN AFILIACIONES.',
			'folio'=> '',
			);
		}
		echo CJSON::encode(array(
		'requestresult' => 'ok',
		'DataList' => $_Data
		));

		return;

		exit;
	}
	public function actionIndex(){
		FB::INFO($_POST, 'POST');
		$MunicipiosData=Yii::app()->db->createCommand()
		->select('*')
		->from('rh_municipios')
		->queryAll();
		$FrecuenciapagoData=Yii::app()->db->createCommand()
		->select('*')
		->from('rh_frecuenciapago')
		->queryAll();
		$EmpresasData=Yii::app()->db->createCommand()
		->select('*')
		->from('rh_empresas')
		->queryAll();
		$FormapagoData=Yii::app()->db->createCommand()
		->select('*')
		->from('paymentmethods')
		->queryAll();
		$ProductoData=Yii::app()->db->createCommand()
		->select('*')
		->from('stockmaster')
		->where('categoryid = "AFIL" ORDER BY stockid ASC')
		->queryAll();
		$MatrizpreciosData=Yii::app()->db->createCommand()
		->select('*')
		->from('rh_matrizprecios')
		->queryAll();
		$PreciocomisData=Yii::app()->db->createCommand()
		->select('*')
		->from('rh_preciocomisionista')
		->queryAll();$ProductosCotizacionData=Yii::app()->db->createCommand()
		->select('*')
		->from('wrk_productos_cotizacion')
		->queryAll();

		$CotizadorData=Yii::app()->db->createCommand()
		->select('wrk_cotizador_maestro.*, rh_municipios.municipio as municipio,
		rh_frecuenciapago.frecuencia as frecuencia_pago,
		rh_empresas.empresa as empresa,paymentmethods.paymentname as
		paymentid,stockmaster.description as stockid,
		rh_matrizprecios.costouno as costo_inscripcion,rh_matrizprecios.costodos as
		costo_total, rh_preciocomisionista.tarifains as costo_inscripcion_2,
		rh_preciocomisionista.tarifa as costo_total_2,
		wrk_productos_cotizacion.costo_inscripcion as costo_inscripcion_3,
		wrk_productos_cotizacion.costo_total as costo_total_3')
		->from('wrk_cotizador_maestro')
		->leftJoin('rh_municipios', 'wrk_cotizador_maestro.id_municipio=rh_municipios.id')
		->leftJoin('rh_frecuenciapago',
		'wrk_cotizador_maestro.frecuencia_pago=rh_frecuenciapago.id')
		->leftJoin('rh_empresas', 'wrk_cotizador_maestro.empresa=rh_empresas.id')
		->leftJoin('paymentmethods',
		'wrk_cotizador_maestro.paymentid=paymentmethods.paymentid')
		->leftJoin('stockmaster', 'wrk_cotizador_maestro.stockid=stockmaster.stockid')
		->leftJoin('rh_matrizprecios', 'rh_matrizprecios.stockid=wrk_cotizador_maestro.stockid
		and rh_matrizprecios.paymentid=wrk_cotizador_maestro.paymentid
		and rh_matrizprecios.frecpagoid=wrk_cotizador_maestro.frecuencia_pago
		and rh_matrizprecios.nafiliados=wrk_cotizador_maestro.CNT_socios
		')
		->leftJoin('rh_preciocomisionista',
		'rh_preciocomisionista.dproducto=wrk_cotizador_maestro.stockid
		and rh_preciocomisionista.dformap=wrk_cotizador_maestro.paymentid
		and rh_preciocomisionista.dtipopago=wrk_cotizador_maestro.frecuencia_pago
		and rh_preciocomisionista.empresa=wrk_cotizador_maestro.empresa
		and rh_preciocomisionista.activo=1
		')->leftJoin('wrk_productos_cotizacion',
		'wrk_productos_cotizacion.stockid=wrk_cotizador_maestro.stockid
		')
		->queryAll();
		
		$this->render('index', array(
		'CotizadorData'=>$CotizadorData,
		'MunicipiosData'=>$MunicipiosData,
		'FrecuenciapagoData'=>$FrecuenciapagoData,
		'EmpresasData'=>$EmpresasData,
		'FormapagoData'=>$FormapagoData,
		'ProductoData'=>$ProductoData,
		'MatrizpreciosData'=>$MatrizpreciosData,
		'PreciocomisData'=>$PreciocomisData,
		'ProductosCotizacionData'=>$ProductosCotizacionData
		));
	}
	public function actionCreate(){
		$GetCostos=Yii::app()->db->createCommand()
		->select('wrk_cotizador_maestro.*, wrk_cotizador_maestro.CNT_socios as CNT_socios,
		rh_municipios.municipio as municipio, rh_frecuenciapago.frecuencia as frecuencia_pago,
		rh_empresas.empresa as empresa,paymentmethods.paymentname as
		paymentid,stockmaster.description as stockid, rh_matrizprecios.costouno as
		costo_inscripcion,rh_matrizprecios.costodos as costo_total, rh_preciocomisionista.tarifains as
		costo_inscripcion_2,rh_preciocomisionista.tarifa as costo_total_2,
		wrk_productos_cotizacion.costo_inscripcion as costo_inscripcion_3,
		wrk_productos_cotizacion.costo_total as costo_total_3')
		->from('wrk_cotizador_maestro')
		->leftJoin('rh_municipios', 'wrk_cotizador_maestro.id_municipio=rh_municipios.id')
		->leftJoin('rh_frecuenciapago', 'wrk_cotizador_maestro.frecuencia_pago=rh_frecuenciapago.id')
		->leftJoin('rh_empresas', 'wrk_cotizador_maestro.empresa=rh_empresas.id')
		->leftJoin('paymentmethods', 'wrk_cotizador_maestro.paymentid=paymentmethods.paymentid')->leftJoin('stockmaster', 'wrk_cotizador_maestro.stockid=stockmaster.stockid')
		->leftJoin('rh_matrizprecios', 'rh_matrizprecios.stockid=wrk_cotizador_maestro.stockid
		and rh_matrizprecios.paymentid=wrk_cotizador_maestro.paymentid
		and rh_matrizprecios.frecpagoid=wrk_cotizador_maestro.frecuencia_pago
		and rh_matrizprecios.nafiliados=wrk_cotizador_maestro.CNT_socios
		')
		->leftJoin('rh_preciocomisionista',
		'rh_preciocomisionista.dproducto=wrk_cotizador_maestro.stockid
		and rh_preciocomisionista.dformap=wrk_cotizador_maestro.paymentid
		and rh_preciocomisionista.dtipopago=wrk_cotizador_maestro.frecuencia_pago
		and rh_preciocomisionista.empresa=wrk_cotizador_maestro.empresa
		')
		->leftJoin('wrk_productos_cotizacion',
		'wrk_productos_cotizacion.stockid=wrk_cotizador_maestro.stockid
		')
		->queryAll();
		if(!empty($_POST['nombre'])){
			$Cotizador="insert into wrk_cotizador_maestro
			(folio_socio,nombre,fecha_cotizacion, calle,
			numero, colonia, telefono, entrecalles, id_municipio, sucursal,
			cuadrantes,codigo_postal,stockid,paymentid,frecuencia_pago,empresa,
			CNT_socios,comentarios,
			costo_inscripcion,costo_total,
			costo_inscripcion_2,costo_total_2,
			costo_inscripcion_3,costo_total_3,
			costo_inscripcion_libre,costo_total_libre,tipo,usuario,status)
			values(:folio_socio,:nombre,:fecha_cotizacion, :calle,
			:numero, :colonia, :telefono, :entrecalles, :municipio,:sucursal,
			:cuadrantes,:codigo_postal,:stockid,:paymentid,:frecuencia_pago,:empresa,
			:CNT_socios,:comentarios,:costo_inscripcion,:costo_total,
			:costo_inscripcion_2,:costo_total_2,
			:costo_inscripcion_3,:costo_total_3,
			:costo_inscripcion_libre,:costo_total_libre,:tipo,:usuario,:status)";
			$parameters=array(
			':folio_socio'=>$_POST['folio_socio'],
			':nombre'=>$_POST['nombre'],
			':fecha_cotizacion'=>$_POST['fecha_cotizacion'],
			':calle'=>$_POST['calle'],
			':numero'=>$_POST['numero'],
			':colonia'=>$_POST['colonia'],
			':telefono'=>$_POST['telefono'],
			':entrecalles'=>$_POST['entrecalles'],
			':municipio'=>$_POST['municipio'],
			':sucursal'=>$_POST['sucursal'],
			':cuadrantes'=>$_POST['cuadrantes'],
			':codigo_postal'=>$_POST['codigo_postal'],
			':stockid'=>$_POST['stockid'],
			':paymentid'=>$_POST['paymentid'],
			':frecuencia_pago'=>$_POST['frecuencia_pago'],
			':empresa'=>$_POST['empresa'],
			':CNT_socios'=>$_POST['CNT_socios'],
			':comentarios'=>$_POST['comentarios'],
			':costo_inscripcion'=>$GetCostos[0]['costo_inscripcion'],
			':costo_total'=>$GetCostos[0]['costo_total'],
			':costo_inscripcion_2'=>($GetCostos[0]['costo_inscripcion_2'])*($GetCostos[0]['CNT_socios']),
			':costo_total_2'=>($GetCostos[0]['costo_total_2'])*($GetCostos[0]['CNT_socios']),
			':costo_inscripcion_3'=>($GetCostos[0]['costo_inscripcion_3'])*($GetCostos[0]['CNT_socios']),
			':costo_total_3'=>($GetCostos[0]['costo_total_3'])*($GetCostos[0]['CNT_socios']),':costo_inscripcion_libre'=>$_POST['costo_inscripcion_libre'],
			':costo_total_libre'=>$_POST['costo_total_libre'],
			':tipo'=>$_POST['tipo'],
			':usuario'=>$_SESSION['UserID'],
			':status'=>$_POST['status']
			);
			if(Yii::app()->db->createCommand($Cotizador)->execute($parameters)){
				Yii::app()->user->setFlash("success", "La información se ha registrado
				correctamente");
				$this->redirect($this->createUrl("cotizador/index"));
			}else{
				Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
				$this->redirect($this->createUrl("cotizador/index"));
			}
		}else{
			Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
			$this->redirect($this->createUrl("cotizador/index"));
		}
	}
	public function actionLoadForm(){
		if(!empty($_POST['GetData'])){
		fb::info($_POST);
		$where="id ='".$_POST['GetData']['id']."'";
		$LoadForm=Yii::app()->db->createCommand()
		->select('*')
		->from('wrk_cotizador_maestro')
		->where($where)
		->queryAll();fb::info($LoadForm);
		if(!empty($LoadForm)){
		echo CJSON::encode(array(
		'requestresult' => 'ok',
		'message' => "Seleccionando " . $LoadForm['id'] . "...",
		'GetData' => $LoadForm[0]
		));
		fb::info($_POST);
		} else {
		echo CJSON::encode(array(
		'requestresult' => 'fail',
		'message' => "La información no se pudo seleccionar, intente de nuevo..."
		));
		}
		return;
		}
	}
	public function actionUpdate() {
		global $db;
		if (!empty($_POST['Update'])) {
			$SQLUpdate = "UPDATE wrk_cotizador_maestro SET
			folio_socio='" . $_POST['Update']['folio_socio'] . "',
			nombre='" . $_POST['Update']['nombre'] . "',
			fecha_cotizacion='" . $_POST['Update']['fecha_cotizacion'] . "',
			calle='".$_POST['Update']['calle']."',
			numero='".$_POST['Update']['numero']."',
			colonia='".$_POST['Update']['colonia']."',
			telefono='".$_POST['Update']['telefono']."',
			entrecalles='".$_POST['Update']['entrecalles']."',
			id_municipio='".$_POST['Update']['municipio']."',sucursal='".$_POST['Update']['sucursal']."',
			cuadrantes='".$_POST['Update']['cuadrantes']."',
			codigo_postal='".$_POST['Update']['codigo_postal']."',
			stockid='".$_POST['Update']['stockid']."',
			paymentid='".$_POST['Update']['paymentid']."',
			frecuencia_pago='".$_POST['Update']['frecuencia_pago']."',
			empresa='".$_POST['Update']['empresa']."',
			CNT_socios='".$_POST['Update']['CNT_socios']."',
			comentarios='".$_POST['Update']['comentarios']."',
			costo_inscripcion='".$_POST['Update']['costo_inscripcion']."',
			costo_total='".$_POST['Update']['costo_total']."',
			costo_inscripcion_libre='".$_POST['Update']['costo_inscripcion_libre']."',
			costo_total_libre='".$_POST['Update']['costo_total_libre']."',
			usuario='".$_POST['Update']['usuario']."',
			tipo='".$_POST['Update']['tipo']."',
			status='".$_POST['Update']['status']."'
			WHERE id = '" . $_POST['Update']['id'] . "'";

			if (DB_query($SQLUpdate, $db)) {
				$Municipio=Yii::app()->db->createCommand()
				->select('*')
				->from('rh_municipios')
				->where('id='.$_POST['Update']['municipio'])
				->queryAll();
				if($_POST['Update']['status'] ==1) $_POST['Update']['status'] = "Activo"; else
				$_POST['Update']['status'] = "Cancelado";
				$NewRow .= "";
				$NewRow .= "
				<td >{$_POST['Update']['id']}</td>
				<td >{$_POST['Update']['folio_socio']}</td>
				<td >{$_POST['Update']['nombre']}</td><td >{$_POST['Update']['fecha_cotizacion']}</td>
				<td >{$_POST['Update']['calle']}</td>
				<td >{$_POST['Update']['numero']}</td>
				<td >{$_POST['Update']['colonia']}</td>
				<td >{$_POST['Update']['telefono']}</td>
				<td >{$_POST['Update']['entrecalles']}</td>
				<td >{$Municipio[0]['municipio']}</td>
				<td >{$_POST['Update']['sucursal']}</td>
				<td >{$_POST['Update']['cuadrantes']}</td>
				<td >{$_POST['Update']['codigo_postal']}</td>
				<td >{$_POST['Update']['stockid']}</td>
				<td >{$_POST['Update']['paymentid']}</td>
				<td >{$_POST['Update']['frecuencia_pago']}</td>
				<td >{$_POST['Update']['empresa']}</td>
				<td >{$_POST['Update']['CNT_socios']}</td>
				<td >{$_POST['Update']['comentarios']}</td>
				<td >{$_POST['Update']['costo_inscripcion']}</td>
				<td >{$_POST['Update']['costo_total']}</td>
				<td >{$_POST['Update']['costo_inscripcion_libre']}</td>
				<td >{$_POST['Update']['costo_total_libre']}</td>
				<td >{$_POST['Update']['usuario']}</td>
				<td >{$_POST['Update']['tipo']}</td>
				<td >{$_POST['Update']['status']}</td>
				<td >
				<a title=\"Editar Cotizador\" onclick=\"EditarCotizador('{$_POST['Update']['id']}');\"><i
				class=\"icon-edit\"></i></a>
				<a href='" . $this->createUrl("cotizador/disable&id=" . $_POST['Update']['id']) . "'
				title=\"Cancelar Cotización\" onclick=\"javascript:if(confirm('¿Esta seguro de Cancelar esta
				cotización?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
				</td>";echo CJSON::encode(array(
				'requestresult' => 'ok',
				'message' => "Los datos se han actualizado correctamente...",
				'NewRow' => $NewRow,
				'id' => $_POST['Update']['id']
				));
			} else {
				echo CJSON::encode(array(
				'requestresult' => 'fail',
				'message' => "No se pudo actualizar, intente de nuevo"
				));
			}
		}
		return;
	}
	public function actionDisable(){
		if(!empty($_GET['id'])){
			$Disable="update wrk_cotizador_maestro set status= :status, usuario=:usuario where id=:id";
			$parameters=array(':status' => 0,':usuario'=>$_SESSION['UserID'],':id'=>$_GET['id']);
			if(Yii::app()->db->createCommand($Disable)->execute($parameters)){
				Yii::app()->user->setFlash("success", "La información se ha actualizado correctamente");
				$this->redirect($this->createUrl("cotizador/index"));
			}else{
				Yii::app()->user->setFlash("error", "La Cotización Ya Se Encontraba Cancelada.");
				$this->redirect($this->createUrl("cotizador/index"));
			}
		}else{
			Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
			$this->redirect($this->createUrl("cotizador/index"));
		}
	}
}
?>