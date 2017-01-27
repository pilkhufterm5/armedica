

<?php

/**
*
*/
class TipofacturasController extends Controller
{

    public function actionIndex()
    {
        $GetData = Yii::app()->db->createCommand()->select("*")->from("rh_tipofacturas")->where(" activo=1 ")->queryAll();
        $this->render("index", array("GetData" => $GetData));
    }


    public function actionCreate(){

        if(!empty($_POST['Save'])){
            try {
                $Insert = "INSERT INTO rh_tipofacturas (tipo, activo)VALUES(:tipo, :activo)";
                $parameters = array(
                    ":tipo" => $_POST['tipo'],
                    ":activo" => 1
                    );
                Yii::app()->db->createCommand($Insert)->execute($parameters);
                Yii::app()->user->setFlash("success", "La Informacion se guardo correctamente.");
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", "Error al Guardar: " . $e->getMessage());
            }
        }
        $this->redirect($this->createUrl("tipofacturas/index"));
    }

    public function actionLoadform(){
        if($_POST['GetData']['id']){
            $GetData = Yii::app()->db->createCommand()->select("*")
            ->from("rh_tipofacturas")
            ->where("id = :id", array(":id" => $_POST['GetData']['id']))->queryAll();

            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => "Editar {$GetData[0]['tipo']}...",
                'Data' => $GetData[0]
            ));
            return;
        }
    }

    public function actionUpdate(){

        if(!empty($_POST['Update']['id'])){
            try {
                $Update = "UPDATE rh_tipofacturas SET tipo = :tipo WHERE id = :id ";
                $parameters = array(
                    ":tipo" => $_POST['Update']['tipo'],
                    ":id" => $_POST['Update']['id']
                    );
                Yii::app()->db->createCommand($Update)->execute($parameters);
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "Actualizado correctamente...",
                ));
                //Yii::app()->user->setFlash("success", "La Informacion se Actualizo correctamente.");
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", "Error al Actualizar: " . $e->getMessage());
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error al Actualizar: " . $e->getMessage(),
                ));
            }
        }
        return;

    }



    public function actionDelete($id){
        if(!empty($id)){
            try {
                $Update = "UPDATE rh_tipofacturas SET activo = :activo WHERE id = :id";
                $parameters = array(
                    ":activo" => 0,
                    ":id" => $id
                    );
                Yii::app()->db->createCommand($Update)->execute($parameters);
                Yii::app()->user->setFlash("success", "La Informacion se Elimino correctamente.");
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", "Error al Eliminar: " . $e->getMessage());
            }
        }
        $this->redirect($this->createUrl("tipofacturas/index"));
    }


}







