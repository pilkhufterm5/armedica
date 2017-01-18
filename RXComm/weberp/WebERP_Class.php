<?php
 class WebERP_Class
// extends DB_Class
{
        protected $masterFields=array();
        protected $tableName;
        protected $db;

        function __construct($connect=false){
            if($connect){
                $this->getConnect();
            }

            $ax=explode('_',get_class($this));
            $aux=array();
            for($x=1;$x<count($ax);$x++){
                array_push($aux,$ax[$x]);
            }
            $this->tableName=strtolower(implode('_',$aux));
        }

        protected function getConnect(){
                $this->db = @new mysqli(Config::GENERAL_HOST,Config::GENERAL_USER,Config::GENERAL_PASS,Config::GENERAL_DB);   
                if ($this->db->connect_error) {
                    throw new Exception('Connect Error (' .$this->db->connect_errno . ') '. $this->db->connect_error.' en '.get_class($this));
                }
        }

        protected function getScript($action){
            $sql="";
            $ax=explode('_',get_class($this));
            $aux=array();
            for($x=1;$x<count($ax);$x++){
                array_push($aux,$ax[$x]);
            }
            $TABLE=strtolower(implode('_',$aux));
            $vars= get_class_vars(get_class($this));
            $varsAux=array();
            foreach ($vars as $key=>$value){
                if($this->__get($key)!=null &&$key!='masterFields' && $key!='tableName' ){
                    if($key!='db'){
                        $varsAux[$key]= "'".$this->__get($key)."'";
                    }
                }
            }
            $queryAux=Array();
            foreach($varsAux as $key=>$value){
                array_push($queryAux," ".$key."=".$value);
            }

             switch($action){
                case 'update':

                    $wa=array();
                    foreach($this->masterFields as $key){
                        array_push($wa," ".$key."='".$this->__get($key)."'");
                    }

                    if(count($varsAux)>0){
                        $sql.="Update ".$TABLE." set ";
                        $sql.= implode(',',$queryAux);
                        $sql.=" Where ".implode(' and ',$wa)."";
                    }else{
                        throw new Exception('0x00003 Campos insuficientes para la operacion '.get_class($this));
                    }
                break;
                case 'select':
                    if(count($varsAux)>0){
                        $sql.="Select * from ".$TABLE." ";
                        $sql.=" Where ".implode(' and ',$queryAux)."";
                    }else{
                        throw new Exception('0x00003 Campos insuficientes para la operacion '.get_class($this));
                    }
                break;
                case 'delete':

                    if(count($varsAux)>0){
                        $sql.="delete from ".$TABLE." ";
                        $sql.=" Where ".implode(' and ',$queryAux)."";
                    }else{
                        throw new Exception('0x00003 Campos insuficientes para la operacion '.get_class($this));
                    }
                break;
                case 'insert':
                    $vars= get_class_vars(get_class($this));
                    $varsAux=array();
                    $varsAuxVal=array();
                    foreach ($vars as $key=>$value){
                        if($this->__get($key)!=null &&$key!='masterFields' && $key!='tableName' ){
                            if($key!='db'){
                                $varsAux[$key]= "`".$key."`";
                                $varsAuxVal[$key]= "'".$this->__get($key)."'";
                            }
                        }
                     }
                    if(count($varsAux)>0){
                        $sql.="insert into ".$TABLE." (".implode(',',$varsAux).')';
                        $sql.=" values (".implode(',',$varsAuxVal).")";
                    }else{
                        throw new Exception('0x00003 Campos insuficientes para la operacion '.get_class($this));
                    }
                break;
                default:
                    throw new Exception('0x00002 Action no definida '.get_class($this));
                break;
            }
            return $sql;
        }

        protected function update(){
            return $this->getScript('update');
        }

        protected function select(){
            return $this->getScript('select');
        }

        protected function insert(){
            return $this->getScript('insert');
        }

        protected function delete(){
            return $this->getScript('delete');
        }
        public function ValidarUsuario(){
        	$xmlResp='';
        	$this->RecordSet=$this->db->query("select count(*) as users from www_users where userid='".$this->user."' and '".sha1($this->password)."' = password;");
        	if($Row=$this->RecordSet->fetch_assoc()){
        		if($Row['users']!=1){
        			$xmlResp.="<data>";
        			$xmlResp.="<error>Datos de Acceso incorrectos</error>";
        			$xmlResp.="</data>";
        		}
        	}
        	return  $xmlResp;
        }
    }
?>
