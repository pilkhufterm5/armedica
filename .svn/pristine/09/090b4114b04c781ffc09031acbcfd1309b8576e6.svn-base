<?
class HospitalesController extends Controller{

	public function actionIndex(){

		/*$HospitalesData=Yii::app()->db->createCommand()
		->select('rh_hospitales.*, rh_municipios.municipio as municipio')
		->from('rh_hospitales')
		->leftJoin('rh_municipios', 'rh_hospitales.id_municipio=municipio.id')
		->queryAll();*/

		$this->render('index', array('HospitalesData'=>$HospitalesData));

	}
	public function actionCreate(){

		if(!empty($_POST['nombre'])){
			$Hospitales="insert into rh_hospitales
			(nombre, calle, nuemro, colonia, telefono, entercalles, id_municipio, cuadrante1, cuadrante2, cuadrante3, status, sucursal)
			values (:nombre, :calle, :nuemro, :colonia, :telefono, :entercalles, :municipio, :cuadrante1, :cuadrante2, :cuadrante3, :status, :sucursal)";

			$parameters=array(
				':nombre'=>$_POST['nombre'],
				':calle'=>$_POST['calle'],
				':numero'=>$_POST['numero'],
				':colonia'=>$_POST['colonia'],
				':telefono'=>$_POST['telefono'],
				':entrecalles'=>$_POST['entrecalles'],
				':municipio'=>$_POST['municipio'],
				':cuadrante1'=>$_POST['cuadrante1'],
				':cuadrante2'=>$_POST['cuadrante2'],
				':cuadrante3'=>$_POST['cuadrante3'],
				':status'=>$_POST['status'],
				':sucursal'=>$_POST['sucursal']
				);
			if(Yii::app()->db->createCommand($Hospitales)->execute($parameters)){

				Yii::app()->user->setFlash("success", "La información se ha registrado correctamente");
				$this->redirect($this->createUrl("hospitales/index"));
			}else{
				Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
				$this->redirect($this->createUrl("hospitales/index"));
			}
		}else{

			Yii::app()->user->setFlash("error", "Ha ocurrido un error, intente de nuevo.");
			$this->redirect($this->createUrl("hospitales/index"));

		}
	}
}
?>
