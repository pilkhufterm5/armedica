<?php
/**
* @todo
* Peticiones al WS CECOM desde AFILIACIONES
* WS para CECOM AR MEdica
*
* @author erasto@realhost.com.mx
*/
class WS_CECOM {

    Const APPLICATION_ID = 'RHWSCECOM';

    public $Method = 'POST';
    public $PLAZA = 'CECOM';


    // function __construct($PLAZA=''){
    //     $this->PLAZA = Yii::app()->params['PLAZA'];
    // }

    /**
    * @todo
    * Hace la Peticion a WS
    * @param $URL
    * @param $POST_FIELDS
    * @return Json
    *
    * */
    private function SendToWS($URL, $POST_FIELDS){

        $curl = curl_init();
        switch ($this->Method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($POST_FIELDS){
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $POST_FIELDS);
                }
                //curl_setopt($curl, CURLOPT_HEADER, true);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($POST_FIELDS){
                    $URL = sprintf("%s?%s", $URL, http_build_query($POST_FIELDS));
                }
        }

        $headers = array();
        $headers[] = 'X-'. self::APPLICATION_ID .'-USERNAME: realhost';
        $headers[] = 'X-'. self::APPLICATION_ID .'-PASSWORD: FGr326';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // curl_setopt($curl, CURLOPT_USERPWD, "realhost:FGr326");

        curl_setopt($curl, CURLOPT_URL, $URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $RESPONSE = curl_exec($curl);
        curl_close($curl);
        return $RESPONSE;
    }



    /**
    * @todo
    * Obtiene los Datos de una Tabla de la BD de CECOM
    * @param $Model = Nombre del modelo a consultar;
    * @return Json
    * @author erasto@realhost.com.mx
    *
    * */
    public function GetCatalog($Model){

        //$URL = "http://localhost/ar_cecom_ws/index.php?r=api/Getcatalog";
        $URL = "http://192.168.10.12/ar_cecom_ws/index.php?r=api/Getcatalog";

        $CREATE_POST_FIELDS = "";
        $CREATE_POST_FIELDS .= "TYPE=CATALOG&";
        $CREATE_POST_FIELDS .= "MODEL={$Model}&";
        $CREATE_POST_FIELDS .= "PLAZA={$this->PLAZA}";
        $POST_FIELDS = $CREATE_POST_FIELDS;

        $RESPONSE = $this->SendToWS($URL, $POST_FIELDS);
        $RESPONSE = json_decode($RESPONSE,1);
        //FB::INFO($RESPONSE,'_________RESULT2');
        return $RESPONSE;
    }




}



