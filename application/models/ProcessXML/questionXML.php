<?php
/* Date create: 18-10-2010
 * Created by: Ngô Chí Công, Nguyễn Tiến Dũng
 * Description: class QuestionXML xử lý thao tác với đoạn text XML trong question
 * gồm thêm xóa sửa các thuộc tính trong đoạn XML đó
 * 
 */

 class QuestionXML{
 	public $domXml;
 	private $defaultXml;
 	/*
 	 *Attribute of question type: multiple choise 
 	 */
 	private $dom_Score;   // điểm cho câu hỏi 
 	private $dom_Level; 	// độ khó
 	private $dom_Classification;   // độ phân cách
 	private $dom_OneOrMoreTrue; // một hay nhiều đúng
 	private $dom_Shuffle; // đảo lộn câu trả lời
 	private $dom_DisplayAnswer; // Cách hiển thị thứ tự câu trả lời vd: a,b,c or 1,2,3
 	private $dom_Answers;  // gồm các câu trả lời
 	private $dom_Question; // thẻ lớn nhất bao ngoài tất cả
 	private $dom_Iscorrect; // dùng trong true false question
	/*
 	 * Attribute of question type:  matching
 	 */
 	private $dom_Correct ;
 	
 	public function Init(){
 		$this->dom_Score 			=	$this->domXml->getElementsByTagName("Score")->item(0);
 		//$this->dom_Level 			=	$this->domXml->getElementsByTagName("Level")->item(0);
 		//$this->dom_Classification 	=	$this->domXml->getElementsByTagName("Classification")->item(0);
 		$this->dom_OneOrMoreTrue 	=	$this->domXml->getElementsByTagName("OneOrMoreTrue")->item(0);
 		$this->dom_Shuffle 			=	$this->domXml->getElementsByTagName("Shuffle")->item(0);
 		$this->dom_DisplayAnswer 	=	$this->domXml->getElementsByTagName("DisplayAnswer")->item(0);
 		$this->dom_Answers 			=	$this->domXml->getElementsByTagName("Answers")->item(0);
 		$this->dom_Question 		=	$this->domXml->getElementsByTagName("Question")->item(0);
 		$this->dom_Correct			=   $this->domXml->getElementsByTagName("Correct")->item(0);
 		$this->dom_Iscorrect		=   $this->domXml->getElementsByTagName("Iscorrect")->item(0);
 	}
 	
 	public function __construct($strXML=""){
 		if($strXML==""){
	 		$this->defaultXml = '
	 		<Question type="">
				<Option>
			  		<Score></Score>
					<OneOrMoreTrue></OneOrMoreTrue>
					<Shuffle></Shuffle>
					<DisplayAnswer></DisplayAnswer>
					<Iscorrect></Iscorrect>
				</Option>
			 <Answers>
			 </Answers>
			 <Correct>
			 </Correct>
			</Question>
	 		';
	 		/*
	 		$this->defaultXml = '
	 		<Question type="">
				<Option>
			  		<Score></Score>
					<Level></Level>
					<Classification></Classification>
					<OneOrMoreTrue></OneOrMoreTrue>
					<Shuffle></Shuffle>
					<DisplayAnswer></DisplayAnswer>
					<Iscorrect></Iscorrect>
				</Option>
			 <Answers>
			 </Answers>
			 <Correct>
			 </Correct>
			</Question>
	 		';
	 		*/
	 		
	 		$this->domXml = new DOMDocument('1.0', 'UTF-8');
	 		$this->domXml->loadXML($this->defaultXml); 			
 		}else{
	 		$this->domXml = new DOMDocument('1.0', 'UTF-8');
	 		$this->domXml->loadXML($strXML); 		 			
 		}
 		$this->Init();
 	}
 	
/*************************************************************************************************
*	 							QUESTION MULTIPLE CHOISE										 *	  
**************************************************************************************************/
 	
 	
/*Get Set Ans... */
 	// return all Answers is array answers 
 	// ex return :
	// $ans = Array(
	//		  		0=>Array("id"=>1,"order"=>2),
	//		  		1=>array("id"=>2,"order"=>1)
	//		  ); 	
 	public  function getAllAnswers(){
 		$list_answers = $this->dom_Answers->getElementsByTagName("ans");
 		$arr_ans      = Array();
 		if($list_answers)
	 		foreach($list_answers as $ans){
	 			$answer = Array();
		 			foreach($ans->attributes as $attName=>$attrNode)
		 				$answer[$attName] = $attrNode->nodeValue;
		 		$arr_ans[] = $answer;
	 		}
 		return $arr_ans;
 	}
 	/*
 	 * Set one answer in answers tag
 	 * para    : ($answer is array that mean is one answer)
 	 * process : check id in $anser array if exists answers XML -> Update
 	 *  	  	 if not exitst -> Add
 	 * ex:     setOneAnswer(Array("id"=>1,"order"=>2));
 	 * note    : array paramater is exists key id 
	*/ 
 	
 	
 	public function setOneAnswer($answer){
 		$answer_id = $answer['id'];
 		// find all answers in this question
 		$list_answers = $this->dom_Answers->getElementsByTagName("ans");
 		// check id
 		$is_update = false;
 		if($list_answers)
	 		foreach($list_answers as $ans){
	 			// check exists id 			
	 			foreach($ans->attributes as $attName=>$attrNode)
		 				if($attName=="id" && $attrNode->nodeValue==$answer_id){
		 					$is_update = true;
		 					break;
		 				}
		 		// if check id is ok
		 		if($is_update){
		 			$dom_ans = $ans;
		 			foreach ($answer as $key=>$value){
		 				$this->setAttributeOfElementXml($dom_ans,$key,$value);
		 			}
		 			break;
		 		}
	 		}

 		if($is_update==false){// insert new answer 
 			$dom_ans = $this->domXml->createElement("ans");
 			foreach($answer as $key=>$value)
 				$this->setAttributeOfElementXml($dom_ans,$key,$value); 				
 			$this->dom_Answers->appendChild($dom_ans);
 			
		}
 	}
 	// return true if delete successfull
 	// return false if delete fail
 	
 	public function deleteOneAnswer($id){
 		// find all answers in this question
 		$list_answers = $this->dom_Answers->getElementsByTagName("ans");
 		// check id
 		$is_exists = false;
 		if($list_answers)
	 		foreach($list_answers as $ans){
	 			// check exists id 			
	 			foreach($ans->attributes as $attName=>$attrNode)
		 				if($attName=="id" && $attrNode->nodeValue==$id){
		 					$is_exists = true;
		 					break;
		 				}
		 		// if check id is ok
		 		if($is_exists){
		 			$old_ans = $this->dom_Answers->removeChild($ans);
		 			break;
		 		}		 		
		 	}
		if($is_exists) return true;
		else return false;
 	}
 	
/*---------------------*/ 	
/*Get Set Quetsion type*/
 	public function getQuestionType(){
 		return $this->getAttributeOfElementXml($this->dom_Question,"type");
 	}
 	
 	public function setQuestionType($value){
 		$this->setAttributeOfElementXml($this->dom_Question,"type",$value);
 	}

/*----------------------*/ 	
/*Get Set Score */ 	
 	public function getScoreFromXml(){
 		return $this->getValueOfElementXml($this->dom_Score);
 	}
 	
	public function setScoreToXml($value){
 		$this->setValueOfElementXml($this->dom_Score,$value);
 	}

/*------------------*/ 	
/*Get Set Level 
 	public function getLevelFromXml(){
 		return $this->getValueOfElementXml($this->dom_Level);
 	}
 	
	public function setLevelToXml($value){
 		$this->setValueOfElementXml($this->dom_Level,$value);
 	}
*/ 	
/*------------------*/ 	
/*Get Set Iscorrect */ 	
 	public function getIscorrectFromXml(){
 		return $this->getValueOfElementXml($this->dom_Iscorrect);
 	}
 	
	public function setIscorrectToXml($value){
 		$this->setValueOfElementXml($this->dom_Iscorrect,$value);
 	}

/*------------------
/*Get Set classification 	
 	public function getClassificationFromXml(){
 		return $this->getValueOfElementXml($this->dom_Classification);
 	}
 	
	public function setClassificationToXml($value){
 		$this->setValueOfElementXml($this->dom_Classification,$value);
 	}
*/ 
 	
/*------------------*/ 	
/*Get Set oneOrMoreTrue */ 	
 	public function getOneOrMoreTrueFromXml(){
 		return $this->getValueOfElementXml($this->dom_OneOrMoreTrue);
 	}
 	
	public function setOneOrMoreTrueToXml($value){
 		$this->setValueOfElementXml($this->dom_OneOrMoreTrue,$value);
 	}

/*------------------*/ 	
/*Get Set shuffle */ 	
 	public function getShuffleFromXml(){
 		return $this->getValueOfElementXml($this->dom_Shuffle);
 	}
 	
	public function setShuffleToXml($value){
 		$this->setValueOfElementXml($this->dom_Shuffle,$value);
 	}

/*------------------*/ 	
/*Get Set displayAnswer */ 	
 	public function getDisplayAnswerFromXml(){
 		return $this->getValueOfElementXml($this->dom_DisplayAnswer);
 	}
	
 	public function setDisplayAnswerToXml($value){
 		$this->setValueOfElementXml($this->dom_DisplayAnswer,$value);
 	}
 	
 	
 	public function setOption(Array $arrOptions){
 		foreach($arrOptions as $key => $value ) {
 			Switch($key){
 				case 'Score': 	$this->setScoretoXml($value); break;
 				  
 			}
 			
 		} 		
 	}

/* pasrse xml return string is XML for save in database*/
 	public function ParseXML(){
 		return $this->domXml->saveXML();
 	}
 	
 	
/*---------------MY METHOLD WITH DOM  FOR EASY USE DOM---------------------------------------------------------------*/ 	
 	public function getAttributeOfElementXml(DOMElement $domElement,$nameAttribute){
 		return $domElement->getAttribute($nameAttribute);
 	}
 	
 	public function setAttributeOfElementXml(DOMElement $domElement, $nameAttribute, $value){
 		$domElement->setAttribute($nameAttribute,$value);
 	}
 	
 	public function getValueOfElementXml(DOMElement $domElement){
 		return $domElement->nodeValue; 
 	}
 	
 	public function setValueOfElementXml(DOMElement $domElement,$value){
 		$domElement->nodeValue = $value;
 	}
/*-------------------------------------------------------------------------------------------------------------------*/ 	

 
/*************************************************************************************************
*	 							QUESTION MATCHING										         *	  
**************************************************************************************************/
 	
 public function getAllMatchingCoupleCorrect( $nameOfTag = "" ){
 		$arr_ans_corr     = Array();
 		if ($this->dom_Correct!= null ){
 		$list_couple_correct = $this->dom_Correct->getElementsByTagName("$nameOfTag");
 		if($list_couple_correct)
	 		foreach($list_couple_correct as $corr){
	 			$Corr_answer = Array();
		 			foreach($corr->attributes as $attName=>$attrNode)
		 				$Corr_answer[$attName] = $attrNode->nodeValue;
		 		$arr_ans_corr[] = $Corr_answer;
	 		}
 		}
 		return $arr_ans_corr;
 	}
 
 	/*
 	 * Set one MatchingCoupleTrue in Correct  tag
 	 * para    : ($MatchingCoupleTrue is array that mean is one correct couple matching answer)
 	 *  	  	  Add MatchingCoupleTrue 
 	 * ex:     setOneCoupleMatchingCorrect(Array("id1"=>1,"id2"=>2));
	*/ 
 	
 	public function setOneCoupleMatchingCorrect( $arrAnsIdCorrect ){
 	
 		// insert new answer 
 			$dom_matching = $this->domXml->createElement("AnsCorrect");
 			foreach($arrAnsIdCorrect as $key=>$value)
 				$this->setAttributeOfElementXml($dom_matching,$key,$value); 				
 			$this->dom_Correct->appendChild($dom_matching);
 	}
 	
 	/*
 	 * Delete one MatchingCoupleTrue in Correct  tag
 	 * ex:     deleteOneCoupleMatchingCorrect(id1=1,id2=2);
 	 * HÀM NÀY DÙNG CHUNG ĐỂ XÓA CORRECT CÓ 1 ID VÀ CORRECT CÓ 2 ID
	*/ 
 	
	// delete one couple ans correct if id1 or id2 is exists on correct ans 
	public function deleteOneCoupleMatchingCorrect ( $Id1, $Id2=""){
		// find all MatchingCoupleTrue in Correct tag
 		$list_MatchingCoupleTrue = $this->domXml->getElementsByTagName("AnsCorrect");
			/* HÀM NÀY ĐỂ IN RA MỘT TAG CỦA XML
			 * for($c = 0; $c<$list_MatchingCoupleTrue->length; $c++){
	 				echo "---- "." ($c+1) <br/>" ;
				     $text[$c] =$this->domXml->saveXML($list_MatchingCoupleTrue->item($c));
				     echo $text[$c];
				
			} 	*/
 		// check id
 		$is_exists1 = false;
 		$is_exists2 = false;
 		if($list_MatchingCoupleTrue)
	 		foreach($list_MatchingCoupleTrue as $matchCoupleDelete){
	 			// check exists id 			
	 			foreach($matchCoupleDelete->attributes as $attName=>$attrNode)
	 			{
	 				if( !empty($Id1) && !empty($Id2)){
	 					// TAG CÓ 2 ID CỦA THẺ CORRECT MATCHING
		 				if($attName=="id1" && $attrNode->nodeValue==$Id1){
		 					$is_exists1 = true;
		 				}
		 				if($attName=="id2" && $attrNode->nodeValue==$Id2){
		 					$is_exists2 = true;
		 				}
	 				}
	 			}
		 		// if check id is ok
		 		if($is_exists1 || $is_exists2 ){
		 			$old_mathCouple = $this->dom_Correct->removeChild($matchCoupleDelete);
		 			return true; 
		 		}	 		
		 	}
		 return false;
	}
 
 
 

 
 }
?>