
<?php
class HospitalesController extends Controller{

	public function actionIndex(){

		$MunicipiosData=Yii::app()->db->createCommand()
		->select('*')
		->from('rh_municipios')
		->queryAll();

		$HospitalesData=Yii::app()->db->createCommand()
		->select('rh_hospitales.*, rh_municipios.municipio as municipio')
		->from('rh_hospitales')
		->leftJoin('rh_municipios', 'rh_hospitales.id_municipio=rh_municipios.id')
		->queryAll();

		$this->render('index', array(
			'HospitalesData'=>$HospitalesData,
			'MunicipiosData'=>$MunicipiosData
			));

	}
	public function actionCreate(){

		if(!empty($_POST['nombre'])){
			$Hospitales="insert into rh_hospitales
			(nombre, calle, numero, colonia, telefono, entrecalles, id_municipio, cuadrante1, cuadrante2, cuadrante3, status, sucursal)
			values (:nombre, :calle, :numero, :colonia, :telefono, :entrecalles, :municipio, :cuadrante1, :cuadrante2, :cuadrante3, :status, :sucursal)";

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

	public function actionLoadForm(){

        if(!empty($_POST['GetData'])){
            fb::info($_POST);
            $where="id ='".$_POST['GetData']['id']."'";
            $LoadForm=Yii::app()->db->createCommand()
            ->select('*')
            ->from('rh_hospitales')
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
            $SQLUpdate = "UPDATE rh_hospitales SET
           		nombre='" . $_POST['Update']['nombre'] . "',
				calle='".$_POST['Update']['calle']."',
				numero='".$_POST['Update']['numero']."',
				colonia='".$_POST['Update']['colonia']."',
				telefono='".$_POST['Update']['telefono']."',
				entrecalles='".$_POST['Update']['entrecalles']."',
				id_municipio='".$_POST['Update']['municipio']."',
				cuadrante1='".$_POST['Update']['cuadrante1']."',
				cuadrante2='".$_POST['Update']['cuadrante2']."',
				cuadrante3='".$_POST['Update']['cuadrante3']."',
				status='".$_POST['Update']['status']."',
				sucursal='".$_POST['Update']['sucursal']."'
            WHERE id = '" . $_POST['Update']['id'] . "'";
            if (DB_query($SQLUpdate, $db)) {

            	$Municipio=Yii::app()->db->createCommand()
				->select('*')
				->from('rh_municipios')
				->where('id='.$_POST['Update']['municipio'])
				->queryAll();

                if($_POST['Update']['status'] ==1)  $_POST['Update']['status'] = "Activo"; else $_POST['Update']['status'] = "Inactivo";
                $NewRow = "";
                $NewRow .= "
                <td >{$_POST['Update']['id']}</td>
                <td >{$_POST['Update']['nombre']}</td>
                <td >{$_POST['Update']['calle']}</td>
                <td >{$_POST['Update']['numero']}</td>
                <td >{$_POST['Update']['colonia']}</td>
                <td >{$_POST['Update']['telefono']}</td>
                <td >{$_POST['Update']['entrecalles']}</td>
                <td >{$Municipio[0]['municipio']}</td>
                <td >{$_POST['Update']['cuadrante1']}</td>
                <td >{$_POST['Update']['cuadrante2']}</td>
                <td >{$_POST['Update']['cuadrante3']}</td>
                <td >{$_POST['Update']['sucursal']}</td>
                <td >{$_POST['Update']['status']}</td>
                <td >
                    <a title=\"Editar Hospitales\" onclick=\"EditarHospitales('{$_POST['Update']['id']}');\"><i class=\"icon-edit\"></i></a>
                    <a href='" . $this->createUrl("Hospitales/disable&id=" . $_POST['Update']['id']) . "' title=\"Desactivar Hospitales\" onclick=\"javascript:if(confirm('¿Esta seguro de desactivar este registro?')) { return; }else{return false;};\"><i class=\"icon-trash\"></i></a>
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

            $Disable="update rh_hospitales set status= :status where id=:id";
            $parameters=array(':status' => 0, ':id'=>$_GET['id']);

            if(Yii::app()->db->createCommand($Disable)->execute($parameters)){

                Yii::app()->user->setFlash("success", "La información se ha actualizado correctamente");
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
