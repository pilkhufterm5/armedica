<?php
    class WebERP_recepcion extends WebERP_Class{
      protected $debtorno;//clave del cliente
      protected $branchcode;//clave de la sucursal
      protected $user;
      protected $password;
      protected $items;
      protected $idfactura;
      private $idx=0;
      private $Data=array();
      private $RecordSet;
      private $Error;
      protected $pages;
      protected $orderno;
      protected $quantityrecd;
      protected $podetailitem;

      protected $macaddress;
      protected $idgcm;
      protected $longitude;
      protected $latitude;
      protected $build_device; 
      protected $build_display;
      protected $build_fingerprint;
      protected $build_hardware;
      protected $build_host;
      protected $build_id;
      protected $build_manufacturer;
      protected $build_model;
      protected $build_product;
      protected $articles;
      protected $datereceived;

      private $validate = array();

        function __construct($remoteClass=true,$db=null){
            if($db==null){
                parent::__construct(!$remoteClass);
            }else{
              parent::__construct(false);
              $this->db =$db;
            }
            $this->masterFields=array();
        }

        public function __set($property,$val) {
          if (array_key_exists($property, get_class_vars(__CLASS__))) {
            if(isset($this->validate[$property])){
              if (preg_match($this->validate[$property], $val)) {
                 $this->$property=$val;
              }else{
                  throw new Exception('0x00001 '.$property.' Atributo no valido en la clase '.get_class($this));
              }
            }else{
                 $this->$property=$val;
            }
          }
        }

        public function __get($property) {
          if (array_key_exists($property, get_class_vars(__CLASS__))) {
            return $this->$property;
          }
        }

        public function update(){
          return parent::update();
        }

        public function select(){
          return parent::select();
        }

        public function insert(){
          return parent::insert();
        }

        public function delete(){
          return parent::delete();
        }

        public function getError(){
          return $this->Error;
        }
	private function parseXMLitems(){
		/*
		<Items>
		    <item stockid="0001" quantity="3"></item>		
		    <item stockid="0002" quantity="7"></item>		
		    <item stockid="0003" quantity="4"></item>		
i		</Items>
		*/
		$xml = base64_decode($this->items);
		$xml = (array)simplexml_load_string($xml);
		return $xml;

	
	}
	private function insertPedido($items){
		$xml = $items;
		$str = '';	
		foreach($xml['item'] as $item){
		        $info_item = (array)$item;
		        //var_dump($info_item["@attributes"]["stockid"],$info_item["@attributes"]["quantity"]);
			$str .= $info_item["@attributes"]["stockid"]." -- ".$info_item["@attributes"]["quantity"];
		}
		return $str;

	}

  public function updateInvoice(){
    if($this->db==null){
      parent::getConnect();
    }
    //user password
    $this->RecordSet=$this->db->query("SELECT COUNT(*) AS users FROM www_users WHERE userid = '".$this->user."' AND password = '".sha1($this->password)."';");
    if($Row=$this->RecordSet->fetch_assoc()){
      if($Row['users']!=1){
        $xmlResp.="<data>";
        $xmlResp.="<error>error</error>";
        $xmlResp.="</data>";
        return  $xmlResp;
      }
    }
    $this->articles=base64_decode($this->articles);
    $xmlObject = new SimpleXMLElement($this->articles);
    $xmlJsonObj=json_decode(json_encode($xmlObject),1);
    if(!isset($xmlJsonObj['article'][0]))
    	$xmlJsonObj['article']=array($xmlJsonObj['article']);
    foreach ($xmlJsonObj['article'] as $node) {
	    $arr = $node["@attributes"];
	      $series=array();
	      if(isset($node['serie'])){
	        if(!isset($node['serie'][0]))
	        	$node=array('serie'=>array($node['serie']));
	        foreach($node['serie'] as $serie){
	      		$series[]=$serie["@attributes"];
	      	}
	      }
	      if($arr['quantity']!=0){  
	      $sql="INSERT INTO rh_recepcion_scaneo (".
		        		"podetailitem, ".
		        		"quantity, ".
		        		"userid, ".
		        		"longitud, ".
		        		"latitud, ".
		        		"macaddress_disp, ".
		        		"datereceived, ".
		        		"barcode, ".
		        		"seriesDetalle".
	        		") ".
	      			"VALUES ".
	      			"('".
		      			$arr['podetailitem']."', '".
		      			$arr['quantity']."', '".
		      			$this->user."', '".
		      			$this->longitude."', '".
		      			$this->latitude."', '".
		      			$this->macaddress."', '".
		      			$this->datereceived."', '".
		      			$arr['barcode']."', '".
		      			serialize($series).
	      			"');";
	
	      if(!$this->db->query($sql)){
	        $xmlResp.="<data>";
	        $xmlResp.="<error>Error Imposible Insertar el Registro</error>";
	        $xmlResp.="</data>";
	        return  $xmlResp;
	      }
	    }
	  }
  
    $xmlResp.="<data>";
    $xmlResp.="<success>Registro exitoso</success>";
    $xmlResp.="</data>";
    return  $xmlResp;
  }

  public function login(){
    if($this->db==null){
      parent::getConnect();
    }
    //user password
    $this->RecordSet=$this->db->query("SELECT COUNT(*) AS users FROM www_users WHERE userid = '".$this->user."' AND password = '".sha1($this->password)."';");
    if($Row=$this->RecordSet->fetch_assoc()){
      if($Row['users']!=1){
        $xmlResp.="<data>";
        $xmlResp.="<error>error</error>";
        $xmlResp.="</data>";
        return  $xmlResp;
      }
    }

    $this->RecordSet=$this->db->query("SELECT COUNT(macaddress) AS dispositivos FROM rh_recepcion_dispositivos WHERE macaddress = '".$this->macaddress."';");
    if($Row=$this->RecordSet->fetch_assoc()){
      if($Row['dispositivos']>0){
        $xmlResp.="<data>";
        $xmlResp.="<success>Ya está registrado</success>";
        $xmlResp.="</data>";
        return  $xmlResp;
      }
    }
////////////////////////////////login
    $sql="INSERT INTO rh_recepcion_dispositivos 
      (macaddress, idgcm, build_device, build_display, build_fingerprint, 
        build_hardware, build_host, build_id, build_manufacturer, 
        build_model, build_product ) 
        VALUES ('".$this->macaddress."', 
          '".$this->idgcm."', 
          '".$this->build_device."',  
          '".$this->build_display."', 
          '".$this->build_fingerprint."', 
          '".$this->build_hardware."', 
          '".$this->build_host."', 
          '".$this->build_id."', 
          '".$this->build_manufacturer."', 
          '".$this->build_model."', 
          '".$this->build_product."');";

    if($this->db->query($sql)){
      $xmlResp.="<data>";
      $xmlResp.="<success>Registro exitoso</success>";
      $xmlResp.="</data>";
      return  $xmlResp;
    }
    else{
      $xmlResp.="<data>";
      $xmlResp.="<error>Error Imposible Insertar el Registro</error>";
      $xmlResp.="</data>";
      return  $xmlResp;
    }
  }
//Obtiene artículos la aplicación
	public function getArticles()
	{
		if($this->db==null)
		{
			parent::getConnect();
		}
		//user password
		$this->RecordSet=$this->db->query("SELECT COUNT(*) AS users FROM www_users WHERE userid = '".$this->user."' AND password = '".sha1($this->password)."';");

		if($Row=$this->RecordSet->fetch_assoc())
		{
			if($Row['users']!=1)
			{
				$xmlResp.="<data>";
				$xmlResp.="<error>error</error>";
				$xmlResp.="</data>";
				return  $xmlResp;
			}
		}

		$this->RecordSet=$this->db->query("
		SELECT	stockmaster.barcode, 
					purchorderdetails.podetailitem, 
					purchorderdetails.orderno, 
					purchorderdetails.itemdescription, 
					purchorderdetails.quantityord- purchorderdetails.quantityrecd-sum(IF(rh_recepcion_scaneo.quantity is NULL,0,rh_recepcion_scaneo.quantity)) porrecibir 
					,purchorderdetails.id_agrupador,
          			stockmaster.controlled,
          			stockmaster.serialised,
          			stockmaster.perishable
					FROM purchorderdetails LEFT JOIN stockmaster 
					ON stockmaster.stockid = purchorderdetails.itemcode 
					LEFT JOIN rh_recepcion_scaneo ON rh_recepcion_scaneo.podetailitem=purchorderdetails.podetailitem and rh_recepcion_scaneo.grnno is null
					WHERE 
					purchorderdetails.completed = 0 
					AND (quantityord - quantityrecd) > 0 
					AND orderno = '".$this->orderno."' 
					GROUP BY purchorderdetails.podetailitem
					ORDER BY purchorderdetails.orderno, purchorderdetails.itemdescription;");

		$xmlResp="<data>";
		$xmlResp.="<articleData>";
		$serialised=0;
		$perishable=0;
		$controlled=0;
		while($Row=$this->RecordSet->fetch_assoc()){
			$xmlResp.="<row>";
			foreach($Row as $name => $value){
				if($name!='serialised'&&$name!='perishable'&&$name!='controlled'){
					if(!($name=='porrecibir'&&$value<=0))
						$xmlResp.="<".$name.">".utf8_encode($value)."</".$name.">";
				}else{
					if($name=='controlled')
						$controlled=$value|$controlled;
					if($name=='serialised')
						$serialised=$value|$serialised;
					if($name=='perishable')
						$perishable=$value|$perishable;
				}
			}
			if($Row['id_agrupador']!=''&&$Row['barcode']==''){
				$xmlResp.="<codigosbarras>";
				$RecordSet2=$this->db->query("SELECT * FROM stockmaster WHERE is_farmacia=1 AND id_agrupador = ".$Row['id_agrupador']);
				while($Row2=$RecordSet2->fetch_assoc()) {
						$xmlResp.="<codigo>";
						$xmlResp.=utf8_encode($Row2['barcode']);
						$xmlResp.="</codigo>";	
						$serialised=$Row2['serialised']|$serialised;
						$perishable=$Row2['perishable']|$perishable;
						$controlled=$Row2['controlled']|$controlled;
				}
				$xmlResp.="</codigosbarras>";
			}
			$xmlResp.="<controlled>".utf8_encode($controlled)."</controlled>";
			$xmlResp.="<perishable>".utf8_encode($perishable)."</perishable>";
			$xmlResp.="<serialised>".utf8_encode($serialised)."</serialised>";
      // Aqui se termina de agregar el lote y la fecha de caducidad
			$xmlResp.="</row>";
		}
		$xmlResp.="</articleData>";
		$xmlResp.="</data>";
		return  $xmlResp;
	}

	//funcion que insertara el pedido en BD
	public function setPedido(){
		$xmlResp="<data>";
	        $xmlResp.="<pedido>";
		$xmlResp.=$xml = $this->insertPedido($this->parseXMLitems());
		$xmlResp.="</pedido>";
		$xmlResp.="</data>";
		return $xmlResp;
	}
        public function getArticulos(){
		if(strlen($this->pages)<=0){
                    $this->pages=0;
                }else{
                    $this->pages=($this->pages-1)*12/*30*/;
                }

         if($this->db==null){
             parent::getConnect();
          }
	 $xmlResp=$this->ValidarUsuario();
          if($xmlResp!='') return $xmlResp;

	$where_deb = " limit ".$this->pages.",12";//",30 ";

	$xmlResp_2 = '';
        $this->RecordSet=$this->db->query("SELECT (count(*) div 30) as paginas FROM prices p,stockmaster stkm WHERE p.debtorno='{$this->debtorno}'  AND p.branchcode='{$this->branchcode}' AND stkm.stockid= p.stockid group by p.stockid ");
        $xmlResp_2 .= "<pages>";
        if($Row=$this->RecordSet->fetch_assoc()){
                $xmlResp_2.=$Row['paginas'];
        }
        $xmlResp_2.="</pages>";


	$SQL= "SELECT concat(stkm.stockid,'.jpg') as picture, p.price,p.debtorno,p.branchcode, p.stockid as item ,stkm.longdescription,stkm.description,(select sum(quantity) as total  from locstock  where stockid=item) as quantity FROM prices p,stockmaster stkm WHERE p.debtorno='{$this->debtorno}'  AND p.branchcode='{$this->branchcode}' AND stkm.stockid= p.stockid group by p.stockid  {$where_deb}";

	$xmlResp="<data>";
	$this->RecordSet=$this->db->query($SQL);
	$xmlResp.= $xmlResp_2;	
        $xmlResp.="<productsData>";


        while($Row=$this->RecordSet->fetch_assoc()){
                $xmlResp.="<row>";
		$xmlResp.="<product>";
		$xmlResp.="<price>{$Row['price']}</price>";
		$xmlResp.="<debtorno>{$Row['debtorno']}</debtorno>";
		$xmlResp.="<branchcode>{$Row['branchcode']}</branchcode>";
		$xmlResp.="<item>{$Row['item']}</item>";	
		$xmlResp.="<quantity>{$Row['quantity']}</quantity>";	
		$xmlResp.="<description>{$Row['description']}</description>";	
		$xmlResp.="<longdescription>{$Row['longdescription']}</longdescription>";	
		$xmlResp.="<picture>{$Row['picture']}</picture>";	
		$xmlResp.="</product>";

                $xmlResp.="</row>";
        }
	
        $xmlResp.="</productsData>";
        $xmlResp.="</data>";
        return  $xmlResp;
        }

	public function getSalesRow(){

		if($this->db==null){
	             parent::getConnect();
                }
  	        $xmlResp=$this->ValidarUsuario();
	        if($xmlResp!='') return $xmlResp;

		//select type, transno from debtortrans where id='{$this->idfactura}';
		$this->RecordSet=$this->db->query(" select type, transno from debtortrans where id='{$this->idfactura}' ");		
		
	        $xmlResp="<data>";
		$xmlResp.="<facturaData>";
		
		if($Row=$this->RecordSet->fetch_assoc()){
			//descr y el stockid y barcode
			/*
			-stockmoves.qty as quantity, stockmoves.discountpercent, ((1 - stockmoves.discountpercent) * stockmoves.price * 1* -stockmoves.qty) AS fxnet, (stockmoves.price * 1) AS fxprice, stockmoves.narrative, stockmaster.units
			*/

			$SQL = "SELECT barcode, stockmoves.stockid,stockmaster.description  FROM stockmoves, stockmaster WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type='{$Row["type"]}'AND stockmoves.transno='{$Row["transno"]}' AND stockmoves.show_on_inv_crds=1 ";
		
			$this->RecordSet=$this->db->query($SQL);
			while($Row=$this->RecordSet->fetch_assoc()){	
				$xmlResp.="<row>";

				foreach($Row as $name=>$value){
					if($name=="description"){
					$xmlResp.="<{$name}>".utf8_encode($value)."</{$name}>";
					}else{
					$xmlResp.="<{$name}>{$value}</{$name}>";
					}
				}	
				$xmlResp.="</row>";			
			}

		}else{
			
		}
		$xmlResp.="</facturaData>";
	        $xmlResp.="</data>";
			
	
		return $xmlResp;	
	}
}

