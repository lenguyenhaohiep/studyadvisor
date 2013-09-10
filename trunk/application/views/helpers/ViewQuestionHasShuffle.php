<?php
// application/views/helpers/Table.php
require_once APPLICATION_PATH . '/models/Question.php';
require_once APPLICATION_PATH . '/models/Answer.php';
require_once APPLICATION_PATH . '/models/Test.php';
require_once APPLICATION_PATH . '/models/DoGrade.php';
require_once APPLICATION_PATH . '/models/ProcessXML/questionXML.php';
require_once LIBRARY_PATH.		'/lib_str.php';
require_once LIBRARY_PATH.		'/lib_arr.php';
require_once LIBRARY_PATH.		'/FormatDate.php';

class Zend_View_Helper_ViewQuestionHasShuffle {
	/*
	 * $test_id 		:  id của bài test chứa question
	 * $question_id 	:  id của question cần view
	 * $review_test 	: xem lại bài thi sau khi đã làm xong : 1<=> readonly,disabled với các input 
	 * $view_correct 	: cho xem đáp án đúng hay không?
	 * $view_feedback   : cho xem giải thích hay ko?
	 * $view_score		: Điểm của câu hỏi mà user làm
	 * $answer_of_user : những câu trả lời của user sau khi làm test
	 * note				: mảng $answer_of_user nếu không có count()= 0 <=> phải tự hiểu là người dùng chưa làm câu hỏi đó
	 *					  chứ ko phải lấy từ correct ở DB ra để hiển thị,mà cái hiển thị này phụ thuộc vào option $view_correct
	 * $score_of_question: điểm của câu hỏi (điểm này có thể được edit lại khi soạn thảo test hoặc là điểm mặc định khi soạn thảo question)
	 * $classEatScore    : class cho the div nam ngoai cung cua question: gom nhom cac question sau khi test xong thanh` 3 loai 
	 * EX : viewQuestion (1,2,"title",array_answer_of_user,1,1,1,1,1, 10) 		 
	 */
	function viewQuestionHasShuffle($test_id=null, $question_id, $title_question="",$answer_of_user = array(),$review_test=0,$view_corect=0,$view_feedback=0,$view_send_result=0,$view_score=-1, $score_of_question=null,$classEatScore="") {
 
		if(empty($answer_of_user)) 
			$answer_of_user = array();
		if(is_null($view_score))
			$view_score = -1;
		$html = "<div class='WrapperQuestion $classEatScore'>";
		$models_question   = new Default_Models_Question();
		$models_answer     = new Default_Models_Answer();
		$result = $models_question->find("id",$question_id);
		if(!is_null($result)){
			
			$question_XML = $models_question->getQuestiontext();
			$question_XML = new QuestionXML($question_XML);
			
			$question_type    = $question_XML->getQuestionType();
			$question_content = $models_question->getContent();
			$question_feedback= $models_question->getGeneralfeedback();      
			$allAnswers      = $question_XML->getAllAnswers();
			if(!empty($test_id)){
				$modelTest = new Default_Models_Test();
				$modelTest->find("id",$test_id);
				if($modelTest->getId())
					if($modelTest->getShuffle_answer()==1)
						shuffle($allAnswers);
			}
				if(empty($test_id)) {
					$score_of_question = $question_XML->getScoreFromXml();
					//$title_question = "Điểm cho câu hỏi này : ";
				}
			switch ($question_type){
/*--------------------------------------------------------------------------------------------------------------------
 *------------------------------------------VIEW OF TRUFALSE QUESTION ------------------------------------------------ 
 *--------------------------------------------------------------------------------------------------------------------*/				
				case 1: // true false
					$is_correct      = $question_XML->getIscorrectFromXml();
					/*Phần hiển thị câu trả lời*/
					$html .='<div id="question-'.$question_id.'" class="question-container-div">';
					$html .='<h3 id="question-title-'.$question_id.'">'.$title_question.' <span class="ui-state-highlight" style="font-size:11px;" >'.$score_of_question.' điểm</span></h3>';
					$html .='<input type="hidden" name="question_id[]" value="'.$question_id.'"/>';
					$html .='<ul style="list-style: none;">';
					$html .='<li><b>'.$question_content.'</b></li>';
						$html .='<ul style="list-style: none;">';
						if(count($allAnswers))
							$html_correct = '';						
							$asscci_code  = 'ABCDEFGHIJKLMNOPQRSTUVXYZ';
							foreach ($allAnswers as $key=>$allAnswersItem){								
								$readonly  = ' disabled="disabled" ';
								$check     = ' checked="checked" ';
								$li_css	   = ' class="ui-state-default ui-corner-all" ';
								$ans_id   = $allAnswersItem['id'];								
								$resultAns = $models_answer->find("id",$ans_id);							
																
								if(!is_null($result)){// nếu có ID 
									$ans_content   = $models_answer->getAns_content();
									$ans_feedback  = $models_answer->getFeedback();
									
									$tmp = "Đúng";
									if($ans_content==0)
										$tmp = "Sai";
									
									if($review_test==0)
										$readonly="";
									if(!in_array($ans_id,$answer_of_user))
										$check = ""; 
									if(!$check)
										$li_css  = '';
									$html .='<li '.$li_css.'>';
										$html .='<table>';
											$html.='<tr>';
												$html.='<td>';												
													if($view_corect==1){
														if($is_correct == $ans_content){// cái đúng mặc định trong DB										
															$html.='<img class="fugue fugue-tick" alt="" src="'. BASE_URL .'/img/icons/space.gif"/>';
															$html_correct.='<font color="red"><b>'.$asscci_code[$key].'</b></font>&nbsp;&nbsp;';											
														}								
														else{// những cái còn lại
															 //: có 2 trường hợp: 1: người dùng tick    : điền vào X
															 //					  2: người dùng ko tick : ảnh trắng cho nó cân kèo
															if(in_array($ans_id,$answer_of_user)) // đang xét đến cái id mà người dùng có tick vào
																$html.='<img class="fugue fugue-cross" alt="" src="'. BASE_URL .'/img/icons/space.gif"/>';																					
															else
																$html.='<img alt="" src="'. BASE_URL .'/img/icons/space.gif"/>';
														}		
													}																									
													$html .='<input type="radio"  name="question['.$question_id.'][]"  class="class-input-question-'.$question_id.'"  value="'.$ans_id.'" '.$readonly.$check.' />';
													$html .='&nbsp;<b>'.$asscci_code[$key].'.</b>';	
													$html .='&nbsp;&nbsp;<label>'.$tmp.'</label>';												
												$html.='</td>';																								
													// load feedback of answer
													if($view_feedback==1){
														if(in_array($ans_id,$answer_of_user)){
															$html.='<td width="100" style="color:black;border-left:1px dotted black;">';
															$html.= $ans_feedback;
															$html.='</td>';				
														}							
													}
												$html.='</tr>';
										$html .='</table>';										
									$html .='</li>';			
								}							
							}
						$html.='</ul>';
						/*Hết phần hiển thị câu trả lời*/
						/*--------Correct-----------*/
						if($view_corect==1){
							$html.='<li>';
							$html.='<b>Câu trả lời đúng :&nbsp;&nbsp;</b>'.$html_correct;
							$html.='</li>';
						}
						/*--------End Of Correct-----------*/
						/*--------Genelral Feedback của answer-----------*/
						if($view_feedback==1){
							
							$html.='<li>';
							$html.='<b>Giải thích :&nbsp;&nbsp;</b><br/><span style="padding-left:10px;">'.$question_feedback.'</span>';
							$html.='</li>';
						}
						/*-----------View Grade Of User-------------*/
						if($view_score>=0){
							
							if($score_of_question != null ){			
								//$question_score =  $question_XML->getScoreFromXml();
								$txt_score      =  $view_score."/".$score_of_question;										
								$html.='<li class="ui-state-highlight"><b>Điểm cho câu trả lời này: ';
									$html.=$txt_score;
								$html.='</b></li>';				
							}											
						}
						/*-----------End View Grade Of User-------------*/
						if($view_send_result){
							$html.='<li  style="list-style: none;"><br/>';
								$html .='<a href="#" class="button ui-state-default ui-corner-all" onclick="send_result(\''.$question_id.'\'); return false;">Gửi kết quả</a>';
							$html.='</li>';								
						}
						/*--------End Genelral Feedback của answer-----------*/
						$html .='</ul>';	
						$html .='</div>';						
					break;
/*--------------------------------------------------------------------------------------------------------------------
 *------------------------------------------VIEW OF MULTICHOICE QUESTION------------------------------------------- 
 *--------------------------------------------------------------------------------------------------------------------*/				
					
				case 2: // multi choice					
					/*Phần hiển thị câu trả lời*/
					$oneOrMoreTrue   = $question_XML->getOneOrMoreTrueFromXml();
					// 1:chỉ có 1 phương án đúng:  radio
					// 2:có nhiều phương án đúng: checkbox		
					$typeInput   = $oneOrMoreTrue==0?"radio":"checkbox";
								
					$html .='<div id="question-'.$question_id.'" class="question-container-div">';
					$html .='<h3 id="question-title-'.$question_id.'">'.$title_question.' <span class="ui-state-highlight" style="font-size:11px;">'.$score_of_question.' điểm</span></h3>';
					$html .='<input type="hidden" name="question_id[]" value="'.$question_id.'"/>';
					$html .='<ul style="list-style: none;">';
					$html .='<li><b>'.$question_content.'</b></li>';
						$html .='<ul style="list-style: none;">';						
						$html_correct = '';						
						$asscci_code  = 'ABCDEFGHIJKLMNOPQRSTUVXYZ';
						if(count($allAnswers))
							foreach ($allAnswers as $key=>$allAnswersItem){								
								$readonly  = ' disabled="disabled" ';
								$check     = ' checked="checked" ';
								$li_css	   = ' class="ui-state-default ui-corner-all" ';
								$ans_id   = $allAnswersItem['id'];								
								$resultAns = $models_answer->find("id",$ans_id);																							
								if(!is_null($result)){// nếu có ID 
									$ans_content   = $models_answer->getAns_content();
									$ans_feedback  = $models_answer->getFeedback();

									if($review_test==0)
										$readonly="";
									if(!in_array($ans_id,$answer_of_user))
										$check = ""; 
									if(!$check)
										$li_css  = '';
									$html .='<li '.$li_css.'>';
										$html .='<table>';
											$html.='<tr>';
												$html.='<td>';												
													$html.='<table class="default_table">';
													$html.='<tr>';												
													if($view_corect==1){
														
														// để tìm những ans nào đúng : kiểm tra perscore,để tick đúng,sai
														if($allAnswersItem['perscore']>0){// cái đúng mặc định trong DB										
															$html.='<td width="20"><img class="fugue fugue-tick" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></td>';
															$html_correct.='<font color="red"><b>'.$asscci_code[$key].'</b></font>&nbsp;&nbsp;';											
														}								
														else{// những cái còn lại
															 //: có 2 trường hợp: 1: người dùng tick    : điền vào X
															 //					  2: người dùng ko tick : ảnh trắng cho nó cân kèo
															if(in_array($ans_id,$answer_of_user)) // đang xét đến cái id mà người dùng có tick vào
																$html.='<td width="20"><img class="fugue fugue-cross" alt="" src="'. BASE_URL .'/img/icons/space.gif"/></td>';																					
															else
																$html.='<td width="20"><img alt="" src="'. BASE_URL .'/img/icons/space.gif"/></td>';
														}		
													}
														$html.='<td  width="50">';
															$html .='<p><input type="'.$typeInput.'"  name="question['.$question_id.'][]"  class="class-input-question-'.$question_id.'" value="'.$ans_id.'" '.$readonly.$check.' />';
															$html .='&nbsp;<b>'.$asscci_code[$key].'.</b></p>';															
														$html.='</td>';
															$html.='<td>';														
															$html .=$ans_content;														
														$html.='</td>';
														$html.='</tr>';
													$html.='</table>';																																																
												$html.='</td>';																								
													// load feedback of answer
													if($view_feedback==1){
														if(in_array($ans_id,$answer_of_user)){
															$html.='<td width="100" style="color:black;border-left:1px dotted black;">';
															$html.= $ans_feedback;
															$html.='</td>';				
														}							
													}
												$html.='</tr>';
										$html .='</table>';										
									$html .='</li>';			
								}							
							}
						$html.='</ul>';
						/*Hết phần hiển thị câu trả lời*/
						/*--------Correct-----------*/
						if($view_corect==1){
							
							$html.='<li>';
							$html.='<b>Câu trả lời đúng :&nbsp;&nbsp;</b>'.$html_correct;
							$html.='</li>';
						}
						/*--------End Of Correct-----------*/
						/*--------Genelral Feedback của answer-----------*/
						if($view_feedback==1){
							
							$html.='<li>';
							$html.='<b>Giải thích :&nbsp;&nbsp;</b><br/><span style="padding-left:10px;">'.$question_feedback.'</span>';
							$html.='</li>';
						}
						/*-----------View Grade Of User-------------*/
						if($view_score>=0){			
							if($score_of_question != null ){
								//$question_score =  $question_XML->getScoreFromXml();
								$txt_score      =  $view_score."/".$score_of_question;										
								$html.='<li class="ui-state-highlight"><b>Điểm cho câu trả lời này: ';
									$html.=$txt_score;
								$html.='</b></li>';
							}															
						}
						/*-----------End View Grade Of User------------*/
						if($view_send_result){
							$html.='<li  style="list-style: none;"><br/>';
								$html .='<a href="#" class="button ui-state-default ui-corner-all" onclick="send_result(\''.$question_id.'\'); return false;">Gửi kết quả</a>';
							$html.='</li>';								
						}
						/*--------End Genelral Feedback của answer-----------*/
						$html .='</ul>';	
						
						$html .='</div>';					 
					break;
/*--------------------------------------------------------------------------------------------------------------------
 *------------------------------------------VIEW OF MATCHING QUESTION ------------------------------------------------ 
 *--------------------------------------------------------------------------------------------------------------------*/				
					
				case 3: // matching
						$models_question = new Default_Models_Question();
						$result = $models_question->find("id",$question_id);  
						if(!is_null($result)){
							// sau khi toArray thi` 1 so truong trong question da vao` mang ObjQuestion roi`
							// con` lai cac thong tin answers ta chen` vao` mang o doan sau
								
							$ObjQuestion = $models_question->toArray() ;				
							
							$question_textXML = $models_question->getQuestiontext();
							$question_XML = new QuestionXML($question_textXML);
							
							$allAnswers   = $question_XML->getAllAnswers();
							
							$allCoupleMatchingCorrect = $question_XML->getAllMatchingCoupleCorrect("AnsCorrect");
							
							// lọc lấy các thông tin 
							$ans  		= Array();       // array answer content
							$ansID   	= Array();       // array answer id
							$part       = Array();       // array part ex: A,B  
							
							foreach($allAnswers as $ansItem){
								$ansID[]      = $ansItem['id'];
								$part[]  	  = $ansItem['part'];
								$order[]	  = $ansItem['order'];								
								$models_answer       = new Default_Models_Answer();
								$models_answer->find("id",$ansItem['id']);
								if($models_answer->getId())
									$ans[] 		= $models_answer->getAns_content();																	
							}   
							// xử lý đẩy matching couple 
							// dang id1-id2
							$couple_matching = array();
							$couple_matchingID = array();
							if(count($allCoupleMatchingCorrect)>0){
								foreach ($allCoupleMatchingCorrect as $item){
									$id1 = $item['id1'];
									$id2 = $item['id2'];
									$couple_matchingID[] = $id1.'-'.$id2;
									$str = "";
									// tìm id1 trong mảng ansID
									$keyPos = array_search($id1, $ansID);
									$str.="A".$order[$keyPos].'-';
									
									$keyPos = array_search($id2, $ansID);
									$str.="B".$order[$keyPos];
									$couple_matching[] = $str;
								}
							}
						}
						   $ansA   = array();
						   $ansB   = array();
						   $orderA = array();
						   $orderB = array();
						   $ansIDA = array();
						   $ansIDB = array();
						   
						   foreach($part as $key=>$partItem){
						   	  if ($partItem =="A"){
						   	  	$ansA[]    =  $ans[$key];
						   	  	$orderA[]  =  $order[$key];
						   	  	$ansIDA[]  =  $ansID[$key];
						   	  }else{
						   	  	$ansB[]    =  $ans[$key];
						   	  	$orderB[]  =  $order[$key];
						   	  	$ansIDB[]  =  $ansID[$key];						   	  	
						   	  }
						   }
						   $result1 =   Lib_Arr::my_array_sort($orderA,$ansA,$ansIDA);
						   $result2 =   Lib_Arr::my_array_sort($orderB,$ansB,$ansIDB);
						   
						   $orderA  = $result1[0];
						   $ansA	= $result1[1];
						   $ansIDA  = $result1[2];
						   
						   $orderB  = $result2[0];
						   $ansB	= $result2[1];
						   $ansIDB  = $result2[2];
					$html .='<div id="question-'.$question_id.'" class="question-container-div">';
					$html .='<h3 id="question-title-'.$question_id.'">'.$title_question.' <span class="ui-state-highlight" style="font-size:11px;">'.$score_of_question.' điểm</span></h3>';
					$html .='<input type="hidden" name="question_id[]" value="'.$question_id.'"/>';
					$html .='<ul style="list-style: none;">';
					$html .='<li><b>'.$question_content.'</b></li>';					
					$html .='<ul style="list-style: none;">';						
					$html_correct = '';																											
					if(count($allAnswers)){								
							$readonly  = ' disabled="disabled" ';
							$check     = ' checked="checked" ';
							$li_css	   = ' class="ui-state-default ui-corner-all" ';																
							if($review_test==0)	$readonly="";
							$max_count_AB = count($ansA) > count($ansB) ? count($ansA) : count($ansB);
							$html.='<li>';
								$html.='<table rules="all" class="datatable">';
									$html.='<tr>';
										$html.='<th class="ui-state-default"><font size="3">A</font></th>';
										$html.='<th class="ui-state-default">Chọn cặp đúng</th>';
										$html.='<th class="ui-state-default"><font size="3">B</font></th>';
									$html.='</tr>';
									for($i=0;$i<$max_count_AB;$i++){
										$html.='<tr>';
										$html.='<td>';
										if($i<count($ansA))											
											$html.='<b><font color="green">A'.($i+1).'. </font></b>&nbsp;&nbsp;'.$ansA[$i];
										$html.='</td>';
										$html.='<td style="text-align:center;width:100px;">';
										if($i<count($ansA)){
											if($view_corect==1){
												if(!empty($answer_of_user[$i])){ // người dùng có chọn
												 	if(in_array($answer_of_user[$i],$couple_matchingID))// người dùng chọn đúng
												 		$html.='<img class="fugue fugue-tick" alt="" src="'. BASE_URL .'/img/icons/space.gif"/>';
												 	else
												 		$html.='<img class="fugue fugue-cross" alt="" src="'. BASE_URL .'/img/icons/space.gif"/>';
										 		 }else// người dùng không thao tác j với selection này
													$html.='<img alt="" src="'. BASE_URL .'/img/icons/space.gif"/>';
											}
											 $html.='<select name="question['.$question_id.'][]"  class="class-input-question-'.$question_id.'" '.$readonly.'>';
											 $html.='<option value="">--chọn--</option>';											 
											 for($j=0;$j<count($ansB);$j++){											 	 
											 	 $valueOfOption = $ansIDA[$i].'-'.$ansIDB[$j];
											 	 if($answer_of_user[$i]==$valueOfOption)
											 	 	$selected = ' selected="selected" ';
											 	 else 		
											 	 	$selected = "";										 	
											     $html.='<option value="'.$valueOfOption.'" '.$selected.'>A'.($i+1).'-'.'B'.($j+1).'</option>';
											 }												 
											 $html.='</select>';
										}								 			
										 
										$html.='</td>';
										$html.='<td>';										
										if($i<count($ansB))
											$html.='<b><font color="blue">B'.($i+1).'.</font></b>&nbsp;&nbsp;'.$ansB[$i];	
										$html.='</td>';										
										$html.='</tr>';										
									}
										
								$html.='</table>';					
							$html.='</li>';														
							}
						if($view_corect==1){
							if(count($couple_matching)>0){
								foreach ($couple_matching as $key=>$couple_matchingItem){
									if($key<count($couple_matching)-1)
										$html_correct.=$couple_matchingItem.' ; ';
									else	
										$html_correct.=$couple_matchingItem;
								}
							}							
							$html.='<li>';
							$html.='<b>Câu trả lời đúng :&nbsp;&nbsp;<font color="red">'.$html_correct."</font></b>";
							$html.='</li>';
						}
						/*--------End Of Correct-----------*/
						/*--------Genelral Feedback của answer-----------*/
						if($view_feedback==1){
							
							$html.='<li>';
							$html.='<b>Giải thích :&nbsp;&nbsp;</b><br/><span style="padding-left:10px;">'.$question_feedback.'</span>';
							$html.='</li>';
						}
						/*-----------View Grade Of User-------------*/
						if($view_score>=0){			
							if($score_of_question != null ){
								//$question_score =  $question_XML->getScoreFromXml();
								$txt_score      =  $view_score."/".$score_of_question;										
								$html.='<li class="ui-state-highlight"><b>Điểm cho câu trả lời này: ';
									$html.=$txt_score;
								$html.='</b></li>';
							}															
						}						
						/*-----------End View Grade Of User------------*/
						if($view_send_result){
							$html.='<li  style="list-style: none;"><br/>';
								$html .='<a href="#" class="button ui-state-default ui-corner-all" onclick="send_result(\''.$question_id.'\'); return false;">Gửi kết quả</a>';
							$html.='</li>';								
						}
						/*--------End Genelral Feedback của answer-----------*/							
						$html.='</ul>';		
						$html .='</div>';				
					break;
/*--------------------------------------------------------------------------------------------------------------------
 *------------------------------------------VIEW OF COMPLETION QUESTION--------------------------------------------- 
 *--------------------------------------------------------------------------------------------------------------------*/				
					
				case 4: // completion
					// Trường hợp người dùng điền không đủ câu trả lời, ta thêm phần tử rỗng vào mảng
					// Mục đích: Khi review lại bài làm của người dùng đã làm, những chỗ người dùng không điền sẽ trống
					// Đồng bộ hàm foreach ở phía dưới: $answer_of_user <=> $allAnswers
					if(count($answer_of_user)==0) $flag_show_icon_tick_cross = false;
					else $flag_show_icon_tick_cross = true;
					while (count($answer_of_user)<count($allAnswers)) {
						$answer_of_user[] = "";
					}					
					$html .='<div id="question-'.$question_id.'" class="question-container-div">';
					$html .='<h3 id="question-title-'.$question_id.'">'.$title_question.' <span class="ui-state-highlight" style="font-size:11px;">'.$score_of_question.' điểm</span></h3>';
					$html.='<input type="hidden" name="question_id[]" value="'.$question_id.'">';
					$html.='<ul>';
						$html.='<li style="list-style: none;">';
							$html.='<span style="font-weight: 600;">Điền vào chỗ trống :</span> <br/>';
							$html.='<br>';
							$html.='<div id="completion-'.$question_id.'">'.$question_content.'</div>';
						$html.='</li>';
						/*Hết phần hiển thị câu trả lời*/
						/*--------Correct-----------*/
						if($view_corect==1){
							
							$html.='<li>';
							$html.='<b>Câu trả lời đúng :&nbsp;&nbsp;</b><div class="correctAnsCompletion">'.$question_content.'</div>';
							$html.='</li>';
						}
						/*--------End Of Correct-----------*/
						/*--------Genelral Feedback của answer-----------*/
						if($view_feedback==1){
							
							$html.='<li>';
							$html.='<b>Giải thích :&nbsp;&nbsp;</b><br/><span style="padding-left:10px;">'.$question_feedback.'</span>';
							$html.='</li>';
						}
						/*-----------View Grade Of User-------------*/
						if($view_score>=0){			
							if($score_of_question != null ){
								//$question_score =  $question_XML->getScoreFromXml();
								$txt_score      =  $view_score."/".$score_of_question;										
								$html.='<li class="ui-state-highlight"><b>Điểm cho câu trả lời này: ';
									$html.=$txt_score;
								$html.='</b></li>';		
							}													
						}						
						/*-----------End View Grade Of User------------*/
						if($view_send_result){
							$html.='<li  style="list-style: none;"><br/>';
								$html .='<a href="#" class="button ui-state-default ui-corner-all" onclick="send_result(\''.$question_id.'\'); return false;">Gửi kết quả</a>';
							$html.='</li>';								
						}
						/*--------End Genelral Feedback của answer-----------*/						
					$html.='</ul>';
					$html.='</div>';
					$html.='<script language="javascript">';
					
					/*$html.='$(".correctAnsCompletion").find("input").attr("disabled",true);';*/
					$html.='$(".correctAnsCompletion").find("input").removeClass("fillspace");';
					if(count($answer_of_user)>0)
						foreach ($answer_of_user as $key=>$answer_of_userItem){
							// Kiểm tra tồn tại của thẻ #completion-'.$question_id
							$html.='if($("#completion-'.$question_id.'")){';
							// Tìm kiếm và duyệt tất cả input có class là fillspace với thứ tự là: fillspace:eq('.$key.') ( thực ra chỉ có 1 phần tử) 
							$html.='$("#completion-'.$question_id.'").find(".fillspace:eq('.$key.')").each(function(){';
							if($review_test==1)
							// Nếu xem lại bài làm thì khóa thẻ lại
								$html.='$(this).attr("readonly","readonly");';
							else
								$html.='$(this).attr("readonly","");';
								// Đổi thuộc tính name từ giá trị là ans[] sang dạng question[6][] để phục vụ chấm điểm						
							$html.='$(this).attr("name","question['.$question_id.'][]");';
							// Thay đổi giá trị của thẻ input bằng giá trị người dùng điền vào							
							$html.='$(this).val("'.$answer_of_userItem.'");';
							$models_answer = new Default_Models_Answer();							
							$result   	   = $models_answer->find("id",$allAnswers[$key]['id']); 
							if(!is_null($result)){
								$ans_content  = $models_answer->getAns_content();
								// Kiểm tra câu trả lời của người dùng đúng hay sai và điền image vào trước mỗi input
								// Đúng thì tick: V
								// sAi thì cross: X
								if($flag_show_icon_tick_cross){
									// do khi khởi tạo để đồng bộ thì mảng $answer_of_user luôn luôn có 
									// số phần tử bằng với số câu trả lời 
									// thuận tiện cho việc js
									if(My_String::_compare_string($ans_content,$answer_of_userItem)==1)
										$html.='$(this).before(\'<img class="fugue fugue-tick" alt="" src="'. BASE_URL .'/img/icons/space.gif"/>\');';
									else
											$html.='$(this).before(\'<img class="fugue fugue-cross" alt="" src="'. BASE_URL .'/img/icons/space.gif"/>\');';
								}
							}
							$html.='});';
							$html.='}';							
						}
					$html.='</script>';
					break;
					
/*--------------------------------------------------------------------------------------------------------------------
 *------------------------------------------VIEW OF ESSAY QUESTION ------------------------------------------------ 
 *--------------------------------------------------------------------------------------------------------------------*/				
					
				case 5: // essay
					/*Phần hiển thị câu trả lời*/
					if($review_test==1)
						$class = "editor_readonly";
					else
						$class = "short-editor";					
						//$class = "short-editor";
					$html .='<div id="question-'.$question_id.'" class="question-container-div">';
					$html .='<h3 id="question-title-'.$question_id.'">'.$title_question.' <span class="ui-state-highlight" style="font-size:11px;">'.$score_of_question.' điểm</span></h3>';
					$html .='<input type="hidden" name="question_id[]" value="'.$question_id.'"/>';
					$html .='<ul style="list-style: none;">';
					$html .='<li><b>'.$question_content.'</b></li>';
						$html .='<ul style="list-style: none;">';	
						$html_correct = '';						
						if($answer_of_user)
						foreach ($answer_of_user as $answer_of_userItem){
								$html .='<li>';
								//$html .='<textarea   rows="5" cols="100"  name="question['.$question_id.'][]"  class="'.$class.'  class-input-question-'.$question_id.'" >'.$answer_of_userItem.'</textarea>';
								$html .='<p>'.$answer_of_userItem.'</p>';
								$html .='</li>';													
						}
						else{
								$html .='<li>';
								$html .='<textarea  rows="5" cols="20"  name="question['.$question_id.'][]"  class="'.$class.' class-input-question-'.$question_id.'" ></textarea>';
								$html .='</li>';
								//$html.='<script language="javascript">';
								//$html.='initShortEditorByClass("short-editor",400,false,true);';
								//$html.='</script>';
						}
						if(count($allAnswers))
							foreach ($allAnswers as $key=>$allAnswersItem){								
								$ans_id   = $allAnswersItem['id'];								
								$resultAns = $models_answer->find("id",$ans_id);																							
								if(!is_null($resultAns)){// nếu có ID 
									$ans_content   = $models_answer->getAns_content();
									$ans_feedback  = $models_answer->getFeedback();

									if($review_test==0)
										$readonly="";
									$html .='<li>';
									
									if($view_corect==1){
												$html_correct.='<img class="fugue fugue-tick" alt="" src="'. BASE_URL .'/img/icons/space.gif"/>';
												$html_correct .='<p>'.$ans_content.'</p';
										}																									
									$html .='</li>';			
								}							
							}						
						$html.='</ul>';
						/*Hết phần hiển thị câu trả lời*/
						/*--------Correct-----------*/
						if($view_corect==1){
							
							$html.='<li>';
							$html.='<b>Câu trả lời đúng :&nbsp;&nbsp;</b>'.$html_correct;
							$html.='</li>';
						}
						/*--------End Of Correct-----------*/
						/*--------Genelral Feedback của answer-----------*/
						if($view_feedback==1){
							
							$html.='<li>';
							$html.='<b>Giải thích :&nbsp;&nbsp;</b><br/><span style="padding-left:10px;">'.$question_feedback.'</span>';
							$html.='</li>';
						}
						/*-----------View Grade Of User-------------*/
						if($view_score>=0){			
							if($score_of_question != null ){
								//$question_score =  $question_XML->getScoreFromXml();
								$txt_score      =  $view_score."/".$score_of_question;										
								$html.='<li class="ui-state-highlight"><b>Điểm cho câu trả lời này: ';
									$html.=$txt_score;
								$html.='</b></li>';		
							}													
						}						
						/*-----------End View Grade Of User------------*/
						if($view_send_result){
							$html.='<li  style="list-style: none;"><br/>';
								$html .='<a href="#" class="button ui-state-default ui-corner-all" onclick="send_result(\''.$question_id.'\'); return false;">Gửi kết quả</a>';
							$html.='</li>';								
						}
						/*--------End Genelral Feedback của answer-----------*/
						$html .='</ul>';	
						
						$html .='</div>';			
					
					break;
					
/*--------------------------------------------------------------------------------------------------------------------
 *------------------------------------------VIEW OF SHORT ANSWER QUESTION--------------------------------------------- 
 *--------------------------------------------------------------------------------------------------------------------*/				
					
				case 6: // short answer
					$typeInput   = "text";
					/*Phần hiển thị câu trả lời*/
					$html .='<div id="question-'.$question_id.'" class="question-container-div">';
					$html .='<h3 id="question-title-'.$question_id.'">'.$title_question.' <span class="ui-state-highlight" style="font-size:11px;">'.$score_of_question.' điểm</span></h3>';
					$html .='<input type="hidden" name="question_id[]" value="'.$question_id.'"/>';
					$html .='<ul style="list-style: none;">';
					$html .='<li><b>'.$question_content.'</b></li>';
						$html .='<ul style="list-style: none;">';	
						$html_correct = '';						
						$readonly  = ' readonly="readonly" ';
						if($review_test==0)
										$readonly="";
						$asscci_code  = 'ABCDEFGHIJKLMNOPQRSTUVXYZ';
						if($answer_of_user){
									$answer_of_userItem = $answer_of_user[0]; 
									$html .='<li>';
									$ok    = false;	
									if(count($allAnswers))
										foreach($allAnswers as $allAnswersItem){
											$ans_id   = $allAnswersItem['id'];								
											$resultAns = $models_answer->find("id",$ans_id);																							
											if(My_String::_compare_string($models_answer->getAns_content(),$answer_of_userItem)==1) {
												$ok = true;
												break;
											}
										}
									if($ok)// đang so sánh giá trị mà người dùng điền vào
										$html.='<img class="fugue fugue-tick" alt="" src="'. BASE_URL .'/img/icons/space.gif"/>';
									else									
										$html.='<img class="fugue fugue-cross" alt="" src="'. BASE_URL .'/img/icons/space.gif"/>';
									$html .='<input type="'.$typeInput.'"  name="question['.$question_id.'][]"  class="class-input-question-'.$question_id.'" value="'.$answer_of_userItem.'" '.$readonly.' size="70" />';
									$html .='</li>';													
						}else{
								$html.="<li>";
									$html .='<input type="'.$typeInput.'"  name="question['.$question_id.'][]"  class="class-input-question-'.$question_id.'" value="'.$answer_of_userItem.'" '.$readonly.' size="70" />';
								$html.="</li>";							
						}
						
						if(count($allAnswers))
							foreach ($allAnswers as $key=>$allAnswersItem){								
								$readonly  = ' disabled="disabled" ';
								$check     = ' checked="checked" ';
								$li_css	   = ' class="ui-state-default ui-corner-all" ';
								$ans_id   = $allAnswersItem['id'];								
								$resultAns = $models_answer->find("id",$ans_id);																							
								if(!is_null($resultAns)){// nếu có ID 
									$ans_content   = $models_answer->getAns_content();
									$ans_feedback  = $models_answer->getFeedback();

									if($review_test==0)
										$readonly="";
									$html .='<li>';
									
									if($view_corect==1){
												$html_correct.='<ul>';	
												if($allAnswersItem['perscore']>0){// cái đúng mặc định trong DB
														$html_correct.='<li>';																
														$html_correct.='<img class="fugue fugue-tick" alt="" src="'. BASE_URL .'/img/icons/space.gif"/>';
														$html_correct.='<font color="red"><b>'.$asscci_code[$key].'</b></font>&nbsp;&nbsp;';
														$html_correct .='<input type="'.$typeInput.'" value="'.$ans_content.'" disabled="disabled" size="70" />';
														$html_correct .="<font color='red'>(".$allAnswersItem['perscore']."%)</font>";
														$html_correct.='</li>';											
													}																				
											$html_correct.='</ul>';
										}																																						
									$html .='</li>';			
								}							
							}						
						$html.='</ul>';
						/*Hết phần hiển thị câu trả lời*/
						/*--------Correct-----------*/
						if($view_corect==1){
							
							$html.='<li>';
							$html.='<b>Câu trả lời đúng :&nbsp;&nbsp;</b>'.$html_correct;
							$html.='</li>';
						}
						/*--------End Of Correct-----------*/
						/*--------Genelral Feedback của answer-----------*/
						if($view_feedback==1){
							
							$html.='<li>';
							$html.='<b>Giải thích :&nbsp;&nbsp;</b><br/><span style="padding-left:10px;">'.$question_feedback.'</span>';
							$html.='</li>';
						}
						/*-----------View Grade Of User-------------*/
						if($view_score>=0){	
							if($score_of_question != null ){		
								//$question_score =  $question_XML->getScoreFromXml();
								$txt_score      =  $view_score."/".$score_of_question;										
								$html.='<li class="ui-state-highlight"><b>Điểm cho câu trả lời này: ';
									$html.=$txt_score;
								$html.='</b></li>';
							}															
						}						
						/*-----------End View Grade Of User------------*/
						if($view_send_result){
							$html.='<li  style="list-style: none;"><br/>';
								$html .='<a href="#" class="button ui-state-default ui-corner-all" onclick="send_result(\''.$question_id.'\'); return false;">Gửi kết quả</a>';
							$html.='</li>';								
						}
						/*--------End Genelral Feedback của answer-----------*/
						$html .='</ul>';	
						
						
							/*
							$html .='<div class="commentClick">';
							$html .='<p>';
							$html .='<a href="#">Gửi phản hồi </a>';
							$html .='</p>';
							$html .='<div class="commentButtonBlock">';
							$html .='<textarea class="commentTxtInput" rows="" cols="" style="height: 22px" name="commentTxtInput"></textarea>';
							$html .='<input type="button" value="Gửi" class="commentInput" name="" style="float: right;">';
							$html .='</div>';
							$html .='</div>';							
						*/
						
						
						$html .='</div>';							
					
					break;
			}
		}
		$html.='</div>';
		return $html;
	}
}