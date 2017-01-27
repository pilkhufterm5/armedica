<?php
class tablas implements Iterator, ArrayAccess{
	private $db;
	private $sql;
	private $resource;
	private $fila;
	private $table;
	private $header;
	private $CampoClave;
	private $ReflejarBaseDatos;
	public $id;
	public function __construct($sql='',$table="",$head=array(),$databaseconex=''){
		$this->noReflejarBD();
		$this->setCampoClave('id');
		if(!is_array($head))
			$databaseconex=$head;
		$this->resource=false;
		$this->setHead($head)
		->setConexion($databaseconex)
		->setSql($sql)
		->setTable($table);
	}
	public function __destruct(){
		if($this->resource)
			DB_free_result($this->resource);
	}
	public function noReflejarBD($reflejar=true){
		$this->ReflejarBaseDatos=!!!$reflejar;
		return $this;
	}
	public function setCampoClave($campo=''){
		if(trim($campo)!='')
			$this->CampoClave=$campo;
		return $this;
	}
	public function setHead($head){
		if (is_array($head)&&count($head)>0)
			$this->header=$head;
		return $this;
	}
	public function getHead($head=''){
		if (is_array($head)&&count($head)>0)
			$this->header=$head;
		if(count($this->header)==0)$this->getSchema();
		return $this->header;
	}
	public function setTable($table){
		$this->table=$table;
		return $this;
	}
	public function setSql($sql){
		if($sql!=''){
			$this->sql=$sql;
		}
		return $this;
	}
	public function getSql(){
		return $this->sql;
	}
	public function setConexion($databaseconex){
		if ($databaseconex==''){
			global $db;
			$databaseconex=$db;
		}
		$this->db=$databaseconex;
		return $this;
	}
	public function getConexion(){
		return $this->db;
	}
	public function rewind(){
		$this->resource=DB_query($this->getSql(),$this->getConexion(),'','',0,0);
		$this->getHeaders();
		$this->next();
		return $this;
	}
	public function valid(){
		return !!$this->fila;
	}
	public function current(){
		return $this->fila;
	}
	public function key()
	{
		$this->id=$this->fila['id'];
		return $this->fila['id'];
	}
	public function next(){
		$this->fila=DB_fetch_assoc($this->resource);
		return $this->current();
	}
	public function getHeaders(){
		if(!isset($this->header)){
			$this->header=array();
			$res=DB_show_fields($this->table,$this->getConexion());
			while($fila=DB_fetch_assoc($res)){
				if($fila["Key"]=='PRI'&&$this->CampoClave!=$fila['Field'])
					$this->setCampoClave($fila['Field']);
				$this->header[]=$fila['Field'];
			}
		}
		return $this->header;
	}
	public function getSchema(){
		static $data;
		if(!isset($data)){
			$data= $this->getHeaders();
			$data=array_flip($data);
			foreach($data as $id=>$val)
				$data[$id]='';
		}
		return $data;
	}
	public function first(){
		$this->rewind();
		return $this->current();
	}
	public function Save($data){
		$Replace=array();
		$header=$this->header;
		unset($this->header);
		$this->getHeaders();
		$sql='REPLACE '.$this->table.' SET ';
		foreach($this->header as $id)
			if(isset($data[$id]))
			$Replace[$id]=$id.'="'.//DB_escape_string
			($data[$id]).'"';
			else $Replace[$id]=$id.'='.$id;
		$sql.=implode(', ',$Replace);
		
		DB_query($sql,$this->getConexion());
		$this->id= DB_Last_Insert_ID($this->getConexion());
		$this->header=$header;
		return $this;
	}
	public function Delete($data,$idName='id'){
		$id=0; if(is_array($data)) $id=$data[$idName]; else if(is_object($data)){if(property_exists($data,$idName))$id=$data->{$idName};}else $id=$data;
		$sql='DELETE FROM '.$this->table.' WHERE '.$idName.'="'.((int)$id).'"';
		DB_query($sql,$this->getConexion(),'','',0,0);
		return DB_error_no($this->getConexion()) == 0;
	}
	public function count(){
		$count= DB_num_rows($this->resource);
		if(!$count){
			$this->next();
			$count= DB_num_rows($this->resource);
			if(!$count){
				$this->rewind();
				$count= DB_num_rows($this->resource);
			}
		}
		return $count;
		
	}
	
	/**
	 * Array Access
	 */
	public function offsetExists ($offset ){
		$sql=str_replace(' where ',' where '.$this->table.'.'.$this->CampoClave.'="'.DB_escape_String($offset).'" and ',$this->getSql());
		$this->resource=DB_query(
				$sql
				,$this->getConexion(),'','',0,0);
		return DB_num_rows($this->resource)>0;
		
	}
	public function offsetGet ($offset ){
		$sql=str_replace(' where ',' where '.$this->table.'.'.$this->CampoClave.'="'.DB_escape_String($offset).'" and ',$this->getSql());
		$this->resource=DB_query(
			$sql	
			,$this->getConexion(),'','',0,0);
		return $this->next();
	}
	public function offsetSet ($offset , $value ){
		if(!$this->ReflejarBaseDatos) return false;
		if($offset==null)$offset=0;
		if(!($offset>0&&isset($value[$this->CampoClave]))){
			$value[$this->CampoClave]=0;
		}
		$this->Save($value);
	}
	public function offsetUnset ($offset ){
		if(!$this->ReflejarBaseDatos) return false;
		if($this->offsetExists($offset))
			$this->Delete($offset,$this->CampoClave);
		
	}
}