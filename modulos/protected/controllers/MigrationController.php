<?php


/**
* Metodos necesarios para la migracion de Datos
*/
class MigrationController extends Controller {



    public function actionIndex()
    {
        # code...
        $this->render("index");
    }


    public function actionProcess($action=null, $run=null){

        if($run == true){
            switch ($action) {
                
    //Se agrego este nuevo case para la refacturacion del 01 de diciembre de la liga de Chihuahua, Angeles Pérez 2015-12-01
                case 'refacturachh_01dic':
                    $this->__FacturacionChh($this->__get01dic15(), '01-DIC-15');
                    break;

             default:
                    Yii::app()->user->setFlash("danger", "Error: No se Eligio ninguna Accion");
                    $this->redirect($this->createUrl("index"));
                    break;
            }
        }
        Yii::app()->user->setFlash("danger", "Error: No se Ejecuto ninguna Accion");
        $this->redirect($this->createUrl("index"));

    }
   
    protected function __FacturacionChh($Debtors,$Emision){

        $Refacturacion = new RefacturacionChh($Debtors);
        $Refacturacion->Emision = $Emision;
        try {
            if($Refacturacion->FacturaFolios()){
                Yii::app()->user->setFlash("success", "Procesados Correctamete");
            }
        } catch (Exception $e) {
            Yii::app()->user->setFlash("danger", "Error: " . $e->getMessage());
        }

        $this->redirect($this->createUrl("afiliaciones/index"));
    }

        
protected function __get01dic15(){

return array(
'22',

     );

    }



}






