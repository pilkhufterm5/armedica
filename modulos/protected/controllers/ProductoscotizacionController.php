<?php
class ProductoscotizacionController extends Controller{
	public function actionIndex(){
		$ProductoData=Yii::app()->db->createCommand()
		->select('*')
		->from('stockmaster')
		->where('categoryid = "AFIL" ORDER BY stockid ASC')
		->queryAll();$ProductoscotizacionData=Yii::app()->db->createCommand()
		->select('wrk_productos_cotizacion.*,
		stockmaster.description as stockid')
		->from('wrk_productos_cotizacion')
		->leftJoin('stockmaster', 'wrk_productos_cotizacion.stockid=stockmaster.stockid')
		->queryAll();
		$this->render('index', array(
			'ProductoscotizacionData'=>$ProductoscotizacionData,
			'ProductoData'=>$ProductoData
		));
	}
	public function actionCreate(){
		if(!empty($_POST['stockid'])){
			$Productoscotizacion="insert into wrk_productos_cotizacion(stockid,
			costo_inscripcion, costo_total, status)
			values ( :stockid, :costo_inscripcion, :costo_total, :status)";
			$parameters=array(
			':stockid'=>$_POST['stockid'],
			':costo_inscripcion'=>$_POST['costo_inscripcion'],
			':costo_total'=>$_POST['costo_total'],
			':status'=>$_POST['status']
			);
			if(Yii::app()->db->createCommand($Productoscotizacion)->execute($parameters)){
				Yii::app()->user->setFlash("success", "La información se ha registrado correctamente");
				$this->redirect($this->createUrl("productoscotizacion/index"));
			}else{
				Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
				$this->redirect($this->createUrl("productoscotizacion/index"));
			}
		}else{
			Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
			$this->redirect($this->createUrl("productoscotizacion/index"));
		}
	}
	public function actionLoadForm(){
			if(!empty($_POST['GetData'])){
			fb::info($_POST);
			$where="id ='".$_POST['GetData']['id']."'";
			$LoadForm=Yii::app()->db->createCommand()
			->select('*')
			->from('wrk_productos_cotizacion')
			->where($where)
			->queryAll();
			fb::info($LoadForm);

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
			$SQLUpdate = "UPDATE wrk_productos_cotizacion SET
			costo_inscripcion = '" . $_POST['Update']['costo_inscripcion'] . "',
			costo_total = '" . $_POST['Update']['costo_total'] . "',
			status = '" . $_POST['Update']['status'] . "'
			WHERE id = '" . $_POST['Update']['id'] . "'";
			if (DB_query($SQLUpdate, $db)) {

				if($_POST['Update']['status'] ==1)
				$_POST['Update']['status'] = "Activo";
				else
				$_POST['Update']['status'] = "Inactivo";
				
				$NewRow = "";
				$NewRow .= "
				<td >{$_POST['Update']['id']}</td>
				<td >{$_POST['Update']['stockid']}</td>
				<td >{$_POST['Update']['costo_inscripcion']}</td>
				<td >{$_POST['Update']['costo_total']}</td>
				<td >{$_POST['Update']['status']}</td>
				<td >
				<a title=\"Editar Producto\"
				onclick=\"EditarProductoscotizacion('{$_POST['Update']['id']}');\"><i class=\"icon-
				edit\"></i></a>
				<a href='" . $this->createUrl("productoscotizacion/disable&id=" . $_POST['Update']['id']) . "'
				title=\"Eliminar Producto\" onclick=\"javascript:if(confirm('¿Esta seguro de Eliminar este
				Producto?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
				</td>";
				echo CJSON::encode(array(
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
			$Disable="update wrk_productos_cotizacion set status= :status where id=:id";
			$parameters=array(':status' => 0, ':id'=>$_GET['id']);

			if(Yii::app()->db->createCommand($Disable)->execute($parameters)){
				Yii::app()->user->setFlash("success", "La información se ha actualizado correctamente");
				$this->redirect($this->createUrl("productoscotizacion/index"));
			}else{
				Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
				$this->redirect($this->createUrl("productoscotizacion/index"));
			}
		}else{
			Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
			$this->redirect($this->createUrl("productoscotizacion/index"));
		}
	}
}
?>