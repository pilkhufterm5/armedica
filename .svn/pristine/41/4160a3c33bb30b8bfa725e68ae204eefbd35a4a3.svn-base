<?php
 class DB_Class{
        protected $masterFields=array();
        protected $whereFields=array();
        protected $db;
        private $Error;
        private $mgsError;
        public $TABLE;

        function __construct($connect=false){
            if($connect){
                $this->getConnect();
            }
            $this->TABLE='';
            $this->getTable();
        }
        function getTable(){
        	$ax=explode('_',get_class($this));
        	$aux=array();
        	for($x=1;$x<count($ax);$x++){
        		array_push($aux,$ax[$x]);
        	}
        	$this->TABLE=strtolower(implode('_',$aux));
        	return $this->TABLE;
        }
        function setTable($table){
        	$this->TABLE=strtolower($table);
        	return $this;
        }

        protected function getConnect(){
                $this->db = @new mysqli(Config::GENERAL_HOST,Config::GENERAL_USER,Config::GENERAL_PASS,Config::GENERAL_DB);
                $this->db->set_charset("utf8");
                if ($this->db->connect_error) {
                    throw new Exception('Connect Error (' .$this->db->connect_errno . ') '. $this->db->connect_error.' en '.get_class($this));
                }
                return $this;
        }

        protected function getScript($action){
            $sql="";
            if($this->TABLE=='')
            	$this->getTable();
            $TABLE=$this->TABLE;
            $vars= get_class_vars(get_class($this));
            $varsAux=array();
            foreach ($vars as $key=>$value){
                if($this->__get($key)!=null &&$key!='masterFields' &&$key!='whereFields' ){
                    if($key!='db'){
                        $varsAux[$key]= "'".$this->db->real_escape_string($this->__get($key))."'";
                    }
                }
            }
            $queryAux=Array();
            foreach($varsAux as $key=>$value){
                array_push($queryAux," ".$key."=".$value);
            }

            $wf=array();
            foreach($this->whereFields as $key){
                array_push($wf,"(".$key.")");
            }

             switch($action){
                case 'update':

                    $wa=array();
                    foreach($this->masterFields as $key){
                        array_push($wa," ".$key."='".$this->__get($key)."'");
                    }

                    if(count($wa)>0){
                        $sql.="Update ".$TABLE." set ";
                        $sql.= implode(',',$queryAux);
                        $sql.=" Where ".implode(' and ',$wa)." ".((count($wf)>0)?' and '.implode(' and ',$wf):'');
                    }else{
                        $sql.="Update ".$TABLE." set ";
                        $sql.= implode(',',$queryAux)." ".((count($wf)>0)?' where '.implode(' and ',$wf):'');
                    }
                break;
                case 'select':
                    if(count($varsAux)>0){
                        $sql.="Select * from ".$TABLE." ";
                        $sql.=" Where ".implode(' and ',$queryAux)." ".((count($wf)>0)?' and '.implode(' and ',$wf):'');
                    }else{
                        $sql.="Select * from ".$TABLE." ".((count($wf)>0)?' where '.implode(' and ',$wf):'');
                    }
                break;
                case 'delete':

                    if(count($varsAux)>0){
                        $sql.="delete from ".$TABLE." ";
                        $sql.=" Where ".implode(' and ',$queryAux)." ".((count($wf)>0)?' and '.implode(' and ',$wf):'');
                    }else{
                        throw new Exception('0x00003 Campos insuficientes para la operacion '.get_class($this));
                    }
                break;
                case 'insert':
                    $vars= get_class_vars(get_class($this));
                    $varsAux=array();
                    $varsAuxVal=array();
                    foreach ($vars as $key=>$value){
                        if($this->__get($key)!=null &&$key!='masterFields' ){
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

        protected function setError($error=false,$msg=''){
          $this->Error = $error;
          $this->mgsError = $msg;
        }

        protected function getError(){
            return $this->Error;
        }

        protected function getMsgError(){
            return $this->mgsError;
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
        private function Paginador($SQL){
        	$xmlResp="";
        	if($SQL!=""){
        		$filasTotal=$this->RecordSet->num_rows;
        		$Extra=0; $Extra=$filasTotal%30; if($Extra>0)$Extra=1;
        
        		$this->RecordSet=$this->db->query($SQL.' limit '.$this->pages.',30');
        		$xmlResp.="<pages>";
        		$xmlResp.=htmlentities(((int)($filasTotal/30+$Extra)));
        		$xmlResp.="</pages>";
        	}
        	return $xmlResp;
        }
    }
?>