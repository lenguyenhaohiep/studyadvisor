<?php
// application/views/helpers/Table.php
require_once APPLICATION_PATH . '/models/Question.php';
require_once APPLICATION_PATH . '/models/Answer.php';
require_once APPLICATION_PATH . '/models/DoGrade.php';
require_once APPLICATION_PATH . '/models/ProcessXML/questionXML.php';
require_once LIBRARY_PATH.		'/lib_str.php';
require_once LIBRARY_PATH.		'/lib_arr.php';
require_once LIBRARY_PATH.		'/FormatDate.php';

class Zend_View_Helper_ViewQuestionEssay{
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
	function ViewQuestionEssay($test_id=null, $question_id,$classEatScore="") {
 
		if(is_null($view_score))
			$view_score = -1;
		$html = "<div class='WrapperQuestion $classEatScore'>";
		$models_question   = new Default_Models_Question();
		$models_answer     = new Default_Models_Answer();
		$result = $models_question->find("id",$question_id);
		if($result == 1){
			$question_XML = $models_question->getQuestiontext();
			$question_XML = new QuestionXML($question_XML);
			$question_type    = $question_XML->getQuestionType();
			$question_content = $models_question->getContent();
			$question_feedback= $models_question->getGeneralfeedback();      
			$allAnswers      = $question_XML->getAllAnswers();
			//$strTitleTemp = "";
/*--------------------------------------------------------------------------------------------------------------------
 *------------------------------------------VIEW OF ESSAY QUESTION ------------------------------------------------ 
 *--------------------------------------------------------------------------------------------------------------------*/				
					
					/*Phần hiển thị câu trả lời*/
					$html .='<div id="question-'.$question_id.'" class="question-container-div">';
					$html .='<input type="hidden" name="question_id[]" value="'.$question_id.'"/>';
					$html .='<ul style="list-style: none;">';
					$html .='<li>'.$question_content.'</li>';
					$html_correct = '';
						if(count($allAnswers))
							foreach ($allAnswers as $key=>$allAnswersItem){
								$ans_id   = $allAnswersItem['id'];								
								$resultAns = $models_answer->find("id",$ans_id);																							
								if($resultAns==1){// nếu có ID 
									$ans_content   = $models_answer->getAns_content();
									$ans_feedback  = $models_answer->getFeedback();
									$html .='<li>';
									$html.='<p class="grade-byhand-head-title">Câu trả lời đúng :&nbsp;&nbsp;</p>';
									$html .='</li>';
									$html .='<li>';
										$html .='<p><img class="fugue fugue-tick" alt="" src="'. BASE_URL .'/img/icons/space.gif"/>';
										$html .=''.$ans_content.'</p>';
									$html .='</li>';			
								}							
							}						
					$html .='</ul>';	
					$html .='</div>';			
		}
		$html.='</div>';
		return $html;
	}
}