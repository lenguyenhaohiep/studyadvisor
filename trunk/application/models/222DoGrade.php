<?php
/*
 * modun chấm điểm tham số truyền vào gồm
 * $question_id,$answer_of_user = array(): các id của câu trả lời mà người dùng điền
 * 
 */
// application/models/Question.dphp
require_once APPLICATION_PATH . '/models/Question.php';
require_once APPLICATION_PATH . '/models/Answer.php';
require_once APPLICATION_PATH . '/models/ProcessXML/questionXML.php';
require_once LIBRARY_PATH.		'/lib_str.php';

class DoGrade {
	// do grade with one question 
	// param: question_id,$answer_of_user( mảng các ans_id,text... ứng với từng loại question)
	// return Object of Standar Object (PHP) ->num_score: 1.2323 ->text_score = 1.2323/5
	public static function _DoGrade($question_id,$answer_of_user = array()){
		$models_question  = new Default_Models_Question();
		$models_answer     = new Default_Models_Answer();
		$result = $models_question->find("id",$question_id);
		if($result == 1){
			$question_XML = $models_question->getQuestiontext();
			$question_XML = new QuestionXML($question_XML);
			$question_type    	= $question_XML->getQuestionType();
			$allAnswers      	= $question_XML->getAllAnswers();
			$question_score     = $question_XML->getScoreFromXml();
			$num_score 			= 0;
			switch ($question_type){
				case 1: // true false		return 1;
						if(count($answer_of_user)>0){
						// đúng sai<=> chỉ có 1 câu trả lời đc chọn <=> mảng $answer_of_user chỉ có 1 phần tử
							$answer_id_select = $answer_of_user[0];
							$result 		  = $models_answer->find("id",$answer_id_select);
							if($result==1){
								$ans_content  = $models_answer->getAns_content();
								$is_correct   = $question_XML->getIscorrectFromXml();
								
								if($ans_content==$is_correct)
									$num_score   = $question_score;																
							}
						}
					break;
				case 2: // multi choice
						if(count($answer_of_user)>0){
								foreach ($answer_of_user as $answer_of_userItem){
									$tmp_score = 0;
									foreach ($allAnswers as $allAnswersItem){
										if($allAnswersItem["id"]==$answer_of_userItem){
											$tmp_score += 0.01 * $allAnswersItem["perscore"] * $question_score;
											break;
										}
									}
									$num_score+= $tmp_score;												
								}								
						}	
					break;
				case 3: // MATCHING QUESTION
						$allCoupleMatchingCorrect = $question_XML->getAllMatchingCoupleCorrect("AnsCorrect");
						$couple_matching = array();
						if(count($allCoupleMatchingCorrect)>0){
							foreach ($allCoupleMatchingCorrect as $item){
								$id1 = $item['id1'];
								$id2 = $item['id2'];
								$couple_matching[] = $id1.'-'.$id2;
							}
						}
						$num_correct_ans = 0;
						if(count($answer_of_user)>0){
							foreach ($answer_of_user as $answer_of_userItem){ // $answer_of_userItem có dạng 11-3 (ans_id1-ans_id2)
								if(in_array($answer_of_userItem,$couple_matching))
									$num_correct_ans++;								
							}
						}
						if(count($allCoupleMatchingCorrect)>0)
							$num_score = ($num_correct_ans/count($allCoupleMatchingCorrect)) * $question_score;
						else
							$num_score  = 0;							
					break;
				case 4:// COMPETION QUESTION
						$num_correct_ans = 0; 
						if(count($answer_of_user)>0){						
							foreach ($answer_of_user as $key=>$answer_of_userItem){
								$models_answer = new Default_Models_Answer();							
								$result   	   = $models_answer->find("id",$allAnswers[$key]['id']); 
								if($result==1){
									$ans_content  = $models_answer->getAns_content();
									if(My_String::_compare_string($ans_content,$answer_of_userItem)==1)
										$num_correct_ans++;
								}
							}
						}
						if(count($answer_of_user)>0)
							$num_score = ($num_correct_ans/count($allAnswers)) * $question_score;
						else
							$num_score  = 0;		
					break;
				case 5: // ESSAY QUESTION 
						if(count($answer_of_user)>0){
						}	
					break;
				case 6: // SHORT ANSWER QUESTION 
						if(count($answer_of_user)>0){
							$ans_content_of_user = $answer_of_user[0]; //người dùng chỉ đc đẩy đc 1 phương án trả lời
							if(count($allAnswers)>0)
								foreach ($allAnswers as $allAnswersItem){
									$models_answer = new Default_Models_Answer();							
									$result   	   = $models_answer->find("id",$allAnswersItem["id"]);
									$per_score     =  $allAnswersItem["perscore"];
									if($result==1){
										
										$ans_content  = $models_answer->getAns_content();
										
										if(My_String::_compare_string($ans_content,$ans_content_of_user)==1){
											$num_score = 0.01 * $per_score * $question_score;
											break;											
										}
									}									
								}												
						}
					break;
				}
		}
		$object  = new stdClass();
		$object->num_score   = $num_score;
		$object->text_score  = $num_score."/".$question_score;
		return $object;
	}
}