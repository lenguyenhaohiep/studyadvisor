<?php
// Lớp này dùng để in ra 1 đoạn XML 
// vì firefox không cho in ra màn hình file xml => mình phải viết hàm riêng để xử lý
class lib_xml{
 	private $static_str="";
 	
 	public function GetNodeName(DOMElement  $dom){
 		return $dom->nodeName;
 	}
 	
	public function  GetAttributes(DOMElement $dom){
		$atts = array();
		if($dom->attributes)
		foreach($dom->attributes as $attName=>$attrNode){
			$atts[$attName] = $attrNode->nodeValue;
		}
		return $atts;
	} 	
	
 	public function viewDOM($domE,$space){
 		if(get_class($domE)=="DOMElement"){
	 		$name_tag    = $this->GetNodeName($domE); 		
	 		$attributes  = $this->GetAttributes($domE);
	 		$this->static_str .=$space."[".$name_tag." "; 		
	 		foreach($attributes as $key=>$value)
	 			$this->static_str .= $key.'='.$value." ";
	 			
	 		$this->static_str .=" ]";
	 		if($domE->childNodes->length > 1){
	 			$this->static_str.="<br/>";
	 		}
 		}elseif(get_class($domE)=="DOMText"){
 			$value = $domE->nodeValue;
 			$this->static_str .=$value;
 		} 			 			
 		if($domE->childNodes)
	 		foreach($domE->childNodes as $item)
			 		$this->viewDOM($item,$space."----");
		
		if(get_class($domE)=="DOMElement")
			if($domE->childNodes->length > 1)
				$this->static_str.=$space."[/$name_tag]<br/>";
			else
				$this->static_str.="[/$name_tag]<br/>";
 	}
 	
 	public function viewXML($xml){
 		$domXML = new DOMDocument();
 		$domXML->loadXML($xml);
 		$dom_question = $domXML->getElementsByTagName("Question")->item(0);
 		$this->viewDOM($dom_question,"");
 		echo $this->static_str;
 	}
 }
?>