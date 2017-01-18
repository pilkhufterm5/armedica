
<?php
class RelacionEmpresasController extends Controller{

	public function actionEmpresasPadre(){

        // Lista de empresas padre
		$EmpresasPadre=Yii::app()->db->createCommand()
		->select('rh_titular.id,rh_titular.name,rh_titular.apellidos,rh_titular.tipopersona,rh_titular.taxref,rh_titular.folio')
		->from('or_empresaspadre')
        ->join('rh_titular','rh_titular.id = or_empresaspadre.id_empresapadre')
		->queryAll();

        // Lista de empresas disponible para hacer padre
        $EmpresasPadreDisponibles=Yii::app()->db->createCommand()
        ->select('rh_titular.id,rh_titular.name,rh_titular.apellidos,rh_titular.tipopersona,rh_titular.taxref,rh_titular.folio')
        ->from('rh_titular')
        ->where('id  NOT IN (SELECT id_empresapadre FROM or_empresaspadre) and rh_titular.id  NOT IN (SELECT id_sucursal FROM or_empresashijo)')
        ->order('rh_titular.name ASC')
        ->queryAll();

		$this->render('empresaspadre', array(
			'EmpresasPadre'=>$EmpresasPadre,
			'EmpresasPadreDisponibles'=>$EmpresasPadreDisponibles
			));

	}// end actionEmpresasPadre

    public function actionSucursales(){


        if(isset($_GET['empresapadre']) and $_GET['empresapadre']!='')
        {
            $ID_EmpresaPadre = $_GET['empresapadre'];

            // Datos empresa padre
            $DatosEmpresaPadre=Yii::app()->db->createCommand()
            ->select('rh_titular.id,rh_titular.name,rh_titular.apellidos,rh_titular.tipopersona,rh_titular.taxref,rh_titular.folio')
            ->from('or_empresaspadre')
            ->join('rh_titular','rh_titular.id = or_empresaspadre.id_empresapadre')
            ->where('rh_titular.id='.$ID_EmpresaPadre)
            ->queryAll();

            // Lista de sucursales hijo actuales
            $ListaSucursalesActuales=Yii::app()->db->createCommand()
            ->select('rh_titular.id,rh_titular.name,rh_titular.apellidos,rh_titular.tipopersona,rh_titular.taxref, or_empresashijo.id_sucursal,or_empresashijo.id_empresapadre,rh_titular.folio')
            ->from('or_empresashijo')
            ->join('rh_titular','rh_titular.id = or_empresashijo.id_sucursal')
            ->where('id_empresapadre='.$ID_EmpresaPadre)
             ->order('rh_titular.name ASC')
            ->queryAll();

           
            // Lista de sucursales hijo disponibles
            $ListaSucursalesDisponibles=Yii::app()->db->createCommand()
            ->select('rh_titular.id,rh_titular.name,rh_titular.apellidos,rh_titular.tipopersona,rh_titular.taxref,rh_titular.folio')
            ->from('rh_titular')
            ->where('rh_titular.id  NOT IN (SELECT id_sucursal FROM or_empresashijo) and rh_titular.id  NOT IN (SELECT id_empresapadre FROM or_empresaspadre)')
             ->order('rh_titular.name ASC')
            ->queryAll();
        
        $this->render('sucursales', array(
            'DatosEmpresaPadre'=>$DatosEmpresaPadre,
            'ListaSucursalesActuales'=>$ListaSucursalesActuales,
            'ListaSucursalesDisponibles'=>$ListaSucursalesDisponibles,
            'ID_EmpresaPadre'=>$ID_EmpresaPadre
            ));
        }
    }// end actionEmpresasHijo

    public function actionCrearEmpresaPadre(){

        if (!empty($_POST['id'])) {

            $ID_EmpresaPadre = $_POST['id'];

            // Verificamos que la empresa NO sea padre ni Hija
            $VerificarEmpresaPadre=Yii::app()->db->createCommand()
            ->select('*')
            ->from('or_empresaspadre')
            ->where('id_empresapadre='.$ID_EmpresaPadre)
            ->queryAll();

            $VerificarEmpresaPadreSuc=Yii::app()->db->createCommand()
            ->select('*')
            ->from('or_empresashijo')
            ->where('id_sucursal='.$ID_EmpresaPadre)
            ->queryAll();

            if(empty($VerificarEmpresaPadre) and empty($VerificarEmpresaPadreSuc))
            {
                // Realizamos el insert en empresas padre 
                $InsertEmpresa = "insert into or_empresaspadre (id_empresapadre)
                                      values (:id_empresapadre)";

                $InsertEmpresaParameters = array(
                            ':id_empresapadre' => $ID_EmpresaPadre
                        );
                Yii::app()->db->createCommand($InsertEmpresa)->execute($InsertEmpresaParameters);

                // Realizamos el insert de la sucursal  
                $InsertSucursal = "insert into or_empresashijo (id_empresapadre,id_sucursal)
                                      values (:id_empresapadre, :id_sucursal)";

                $InsertSucursalParameters = array(
                            ':id_empresapadre' => $ID_EmpresaPadre,
                            ':id_sucursal' => $ID_EmpresaPadre
                        );
                Yii::app()->db->createCommand($InsertSucursal)->execute($InsertSucursalParameters);
                

                Yii::app()->user->setFlash("success", "Se creo satisfactoriamente la empresa " . $ID_EmpresaPadre . ", ya puede agregar sucursales");
                $this->redirect($this->createUrl("relacionempresas/empresaspadre"));
                /* echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "Se creo satisfactoriamente la empresa " . $ID_EmpresaPadre . ", ya puede agregar sucursales"
                ));*/

            } else {

                Yii::app()->user->setFlash("error", "El ID ".$ID_EmpresaPadre." ya se encuentra registrado como empresa padre, favor de verificar.");
                $this->redirect($this->createUrl("relacionempresas/empresaspadre"));
                /* echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "El ID ".$ID_EmpresaPadre." ya se encuentra registrado como empresa padre, favor de verificar."
                ));*/
            }

        }
        return;
    }// end actionCrearEmpresaPadre

    public function actionEliminarEmpresaPadre(){

        if (!empty($_GET['id_empresa'])) {

            $ID_EmpresaPadre = $_GET['id_empresa'];

            // Verificamos que la empresa padre exista como padre
            $VerificarEmpresa=Yii::app()->db->createCommand()
            ->select('*')
            ->from('or_empresaspadre')
            ->where('id_empresapadre='.$ID_EmpresaPadre)
            ->queryAll();

            if(!empty($VerificarEmpresa))
            {
                // Realizamos el delete de la empresa  
                $DeleteEmpresa = "delete from or_empresaspadre where id_empresapadre = :id_empresapadre ";

                $DeleteEmpresaParameters = array(
                            ':id_empresapadre' => $ID_EmpresaPadre
                        );
                Yii::app()->db->createCommand($DeleteEmpresa)->execute($DeleteEmpresaParameters);

                 // Realizamos el delete de la sucursal  
                $DeleteSucursal = "delete from or_empresashijo where id_empresapadre = :id_empresapadre";

                $DeleteSucursalParameters = array(
                            ':id_empresapadre' => $ID_EmpresaPadre
                        );
                Yii::app()->db->createCommand($DeleteSucursal)->execute($DeleteSucursalParameters);

               

                 Yii::app()->user->setFlash("success", "Se elimino satisfactoriamente la empresa " . $ID_EmpresaPadre . ".");
                $this->redirect($this->createUrl("relacionempresas/empresaspadre"));

            } else {

                Yii::app()->user->setFlash("error", "El ID ".$ID_EmpresaPadre." NO se pudo eliminar, favor de verificar.");
                $this->redirect($this->createUrl("relacionempresas/empresaspadre"));
            }

        }
            Yii::app()->user->setFlash("error", "No se pudo eliminar, favor de verificar.");
            $this->redirect($this->createUrl("relacionempresas/empresaspadre"));
    }// end actionEliminarEmpresaPadre

    public function actionCrearSucursal(){

        if (!empty($_POST['id_sucursal']) and !empty($_POST['id_empresa'])) {

            $ID_Sucursal = $_POST['id_sucursal'];
            $ID_EmpresaPadre = $_POST['id_empresa'];

            // Verificamos que la empresa NO exista como sucursal
            $VerificarSucursal=Yii::app()->db->createCommand()
            ->select('*')
            ->from('or_empresashijo')
            ->where('id_sucursal='.$ID_Sucursal)
            ->queryAll();

            if(empty($VerificarSucursal))
            {
                // Realizamos el insert de la sucursal  
                $InsertSucursal = "insert into or_empresashijo (id_empresapadre,id_sucursal)
                                      values (:id_empresapadre, :id_sucursal)";

                $InsertSucursalParameters = array(
                            ':id_empresapadre' => $ID_EmpresaPadre,
                            ':id_sucursal' => $ID_Sucursal
                        );
                Yii::app()->db->createCommand($InsertSucursal)->execute($InsertSucursalParameters);
                // Actualizamos el campo de folio asociado en rh_cobranza.folio_asociado, ingresando el folio de la empresa padre.
                $folioempresapadre = $_POST['folioempresapadre'];
                $_foliosuc = Yii::app()->db->createCommand()
                    ->select('folio')
                    ->from('rh_titular')
                    ->where('id='.$ID_Sucursal)
                    ->queryAll();
                $foliosuc = $_foliosuc[0]['folio'];

                $actualizarfolioasociado = '
                    UPDATE rh_cobranza set folio_asociado = :folio_asociado where folio = :folio
                ';
                $actualizarfolioasociadoparams =  array(
                            ':folio_asociado' => $folioempresapadre,
                            ':folio' => $foliosuc
                        );
                Yii::app()->db->createCommand($actualizarfolioasociado)->execute($actualizarfolioasociadoparams);
                /*echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "Se creo satisfactoriamente la sucursal " . $ID_Sucursal . "."
                ));
                */

                Yii::app()->user->setFlash("success", "Se creo satisfactoriamente la sucursal " . $ID_Sucursal . ".");
                $this->redirect($this->createUrl("relacionempresas/sucursales&empresapadre=".$ID_EmpresaPadre));

            } else {

                /* echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "El ID ".$ID_Sucursal." ya se encuentra registrado como sucursal, favor de verificar."
                ));*/

                Yii::app()->user->setFlash("error", "El ID ".$ID_Sucursal." ya se encuentra registrado como sucursal, favor de verificar.");
                $this->redirect($this->createUrl("relacionempresas/sucursales&empresapadre=".$ID_EmpresaPadre));
            }

        }
        return;
    }// end actionCrearSucursal

        public function actionEliminarSucursal(){

        if (!empty($_GET['id_sucursal']) and !empty($_GET['id_empresa'])) {

            $ID_Sucursal = $_GET['id_sucursal'];
            $ID_EmpresaPadre = $_GET['id_empresa'];

            // Verificamos que la empresa padre coincida con la sucursal
            $VerificarSucursal=Yii::app()->db->createCommand()
            ->select('*')
            ->from('or_empresashijo')
            ->where('id_sucursal='.$ID_Sucursal.' and id_empresapadre ='.$ID_EmpresaPadre)
            ->queryAll();

            if(!empty($VerificarSucursal))
            {
                // Realizamos el delete de la sucursal  
                $DeleteSucursal = "delete from or_empresashijo where id_empresapadre = :id_empresapadre and id_sucursal = :id_sucursal";

                $DeleteSucursalParameters = array(
                            ':id_empresapadre' => $ID_EmpresaPadre,
                            ':id_sucursal' => $ID_Sucursal
                        );
                Yii::app()->db->createCommand($DeleteSucursal)->execute($DeleteSucursalParameters);

               // actualizamos el folio asociado
                $_foliosuc = Yii::app()->db->createCommand()
                    ->select('folio')
                    ->from('rh_titular')
                    ->where('id='.$ID_Sucursal)
                    ->queryAll();
                $foliosuc = $_foliosuc[0]['folio'];
                
                $actualizarfolioasociado = '
                    UPDATE rh_cobranza set folio_asociado = "" where folio = :folio
                ';
                $actualizarfolioasociadoparams =  array(
                            ':folio' => $foliosuc
                        );
                Yii::app()->db->createCommand($actualizarfolioasociado)->execute($actualizarfolioasociadoparams);

             Yii::app()->user->setFlash("success", "Se elimino satisfactoriamente la sucursal " . $ID_Sucursal . ".");
                $this->redirect($this->createUrl("relacionempresas/sucursales&empresapadre=".$ID_EmpresaPadre));

            } else {

                Yii::app()->user->setFlash("error", "El ID ".$ID_Sucursal." NO se pudo eliminar, favor de verificar.");
                $this->redirect($this->createUrl("relacionempresas/sucursales&empresapadre=".$ID_EmpresaPadre));
            }

        }
        return;
    }// end actionCrearSucursal
}
?>
