<?php

require_once APPLICATION_PATH . '/models/Classhasstudent.php';
require_once APPLICATION_PATH . '/models/Recommendation.php';
require_once APPLICATION_PATH . '/models/Recommend.php';
require_once APPLICATION_PATH . '/models/Rating.php';
require_once APPLICATION_PATH . '/models/Classs.php';
require_once APPLICATION_PATH . '/models/User.php';
require_once APPLICATION_PATH . '/models/Test.php';
require_once APPLICATION_PATH . '/models/Course.php';
require_once APPLICATION_PATH . '/models/Question.php';
require_once APPLICATION_PATH . '/models/DoGrade.php';
require_once APPLICATION_PATH . '/models/Sheduleexam.php';
require_once APPLICATION_PATH . '/models/Exam.php';
require_once LIBRARY_PATH . '/FormatDate.php';
require_once APPLICATION_PATH . '/models/Historyofusertest.php';
require_once APPLICATION_PATH . '/models/Subject.php';
require_once APPLICATION_PATH . '/models/Statisticadvisory.php';
require_once 'Zend/Filter/StripTags.php';
require_once LIBRARY_PATH . '/lib_arr.php';
require_once APPLICATION_PATH . '/models/Scoretemp.php';
require_once APPLICATION_PATH . '/models/News.php';
require_once APPLICATION_PATH . '/models/Category.php';
require_once APPLICATION_PATH . '/models/Statisticquestion.php';
require_once APPLICATION_PATH . '/models/Chaptersubject.php';
require_once APPLICATION_PATH . '/models/UserVector.php';
require_once APPLICATION_PATH . '/models/QuestionVector.php';
require_once APPLICATION_PATH . '/models/Rating.php';

class PagestudentController extends Zend_Controller_Action {

    private $_arrError;
    private $_user;

    public function init() {
        $objSessionNamespace = new Zend_Session_Namespace('Zend_Auth');
        $objSessionNamespace->setExpirationSeconds(86400);
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $userhaslogin = $auth->getStorage()->read();
            if ($userhaslogin->group_id == 5 || $userhaslogin->group_id == 3 || $userhaslogin->group_id == 6) { // admin
                $this->_helper->layout->setLayout('admin');
                $this->view->headLink()->appendStylesheet(BASE_URL . '/css/teacher.css');
                $this->view->headLink()->appendStylesheet(BASE_URL . '/js/jquery-ui-1.8.9.custom/css/cupertino/jquery-ui-1.8.9.custom.css');
            } else {
                $this->_helper->layout->setLayout('student');
                $this->view->headLink()->appendStylesheet(BASE_URL . '/js/jquery-ui-1.8.9.custom/css/custom-theme/jquery-ui-1.8.9.custom.css');
            }
        }
        $this->view->headLink()->appendStylesheet(BASE_URL . '/css/student.css');
        $controllerName = $this->_request->getControllerName();
        $actionName = $this->_request->getActionName();
        $param = $this->_request->getParams();
        $this->view->controllerName = $controllerName;
        $this->view->actionName = $actionName;
        $this->view->param = $param;
    }

    function preDispatch() {
        $auth = Zend_Auth::getInstance();

        if (!$auth->hasIdentity()) {
            $this->view->haslogin = false;
            $this->_redirect('auth/login');
        } else {
            $this->view->haslogin = true;
            $this->view->userhaslogin = $auth->getStorage()->read();

        }
    }

    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
            } elseif ($request->isGet()) {
                
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
        
        /*
         * Phần tùy chỉnh để chọn ngay lớp đầu tiên
         */
            $userhaslogin = $auth->getStorage()->read();
            $modelsUser = new Default_Models_User();
            $modelsUser->find("id", $userhaslogin->id);
            $models_classhasstudent = new Default_Models_ClassHasStudent();
            //Lấy tất cả các lớp học mà sinh viên tham gia
            $where = "`user_id`=" . $userhaslogin->id;
            $result = $models_classhasstudent->groupBySql($where);
            $arrClassId = array();
            $arrCourseObj = array();
            if (count($result) > 0)
                foreach ($result as $key => $resultItem) {
                        $arrClassId[] = $resultItem->class_id;
                }
            $firstClassId=$arrClassId[0];
            $this->_redirect('/pagestudent/classstudent/class_id/'.$firstClassId);
            
    }

    public function studentjoinclassAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                //$data = $request->getParams();
            } elseif ($request->isGet()) {
                $auth = Zend_Auth::getInstance();
                if ($auth->hasIdentity()) {
                    $userhaslogin = $auth->getStorage()->read();
                    $modelsUser = new Default_Models_User();
                    $modelsUser->find("id", $userhaslogin->id);
                    $list_course_joinTemp = $modelsUser->getList_course_join();
                    if (empty($list_course_joinTemp))
                        $this->view->arrClassId = array();
                    else {
                        // Mỗi lần đăng nhập sinh viên chỉ thao tác với các lớp học ở 1 khóa học default mà sinh viên chọn
                        $arrListCourseId = explode(',', $list_course_joinTemp);
                        $models_classhasstudent = new Default_Models_ClassHasStudent();
                        //Lấy tất cả các lớp học mà sinh viên tham gia
                        $where = "`user_id`=" . $userhaslogin->id;
                        $result = $models_classhasstudent->groupBySql($where);
                        $arrClassId = array();
                        $arrCourseObj = array();
                        if (count($result) > 0)
                            foreach ($result as $key => $resultItem) {
                                $modelsClass = new Default_Models_Classs();
                                $modelsClass->find("id", $resultItem->class_id);
                                if (trim($arrListCourseId[0]) == trim($modelsClass->getCourse_id()))
                                    $arrClassId[] = $resultItem->class_id;
                            }

                        $modelsCourse = new Default_Models_Course();
                        $modelsCourse->find("id", $arrListCourseId[0]);
                        if ($modelsCourse->getId())
                            $arrObjCourse = $modelsCourse->toArray();

                        $this->view->arrClassId = $arrClassId;
                        $this->view->CourseObj = $arrObjCourse;

                        /* Đoạn này để sau, dùng để show all class mà student đã và đang tham gia học
                         * Rule: mỗi lần đăng nhập sinh viên chỉ thao tác ở trong 1 khóa học, nếu muốn chuyển 
                         * sang khóa học khác thì phải set default cho khóa học đó ở chỗ QUẢN LÝ KHÓA HỌC
                         * và đăng nhập lại
                          $models_classhasstudent = new Default_Models_ClassHasStudent();
                          $where = "`user_id`=".$userhaslogin->id;
                          $result = $models_classhasstudent->groupBySql($where);
                          $arrClassId = array();
                          $arrCourseObj = array();
                          if(count($result)>0)
                          foreach ( $result as $resultItem){
                          $arrClassId[] = $resultItem->class_id;
                          $modelsClass = new Default_Models_Classs();
                          $modelsClass->find("id",$resultItem->class_id);
                          $arrCourseObj[] = $modelsClass->toArray();
                          }
                          $this->view->arrClassId = $arrClassId;
                          $this->view->arrCourseObj = $arrCourseObj;
                          var_dump($arrClassId);
                          var_dump($arrCourseObj);
                          //die();
                         */
                    }
                }
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function classstudentAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                
            } elseif ($request->isGet()) {
                $data = $request->getParams();
                if (empty($data['class_id']))
                    throw new Exception("Đường dẫn không hợp lệ.");
                $modelsClass = new Default_Models_Classs();
                $modelsClass->find("id", $data['class_id']);
                $tempID = $modelsClass->getId();
                if (empty($tempID))
                    throw new Exception("Không tồn tại lớp học.");
                $this->view->class_obj = $modelsClass->toArray();
                $modelsCourse = new Default_Models_Course();
                $modelsCourse->find("id", $modelsClass->getCourse_id());
                if ($modelsCourse->getId())
                    $this->view->course_obj = $modelsCourse->toArray();
                else
                    throw new Exception("Không tồn tại khóa học của lớp học " . $modelsClass->getFull_name() . " .");
                $modelsTest = new Default_Models_Test();
                $modelsTest->setDuration_test(60);
                $modelsTest->setShuffle_question(1);
                $modelsTest->setQuestion_perpage(10);
                $modelsTest->setList_question("");
                $modelsTest->setList_score("");
                $modelsHisTest = new Default_Models_Historyofusertest();

                /*
                 * Begin Table show bài  thi đã làm của user
                 */
                $auth = Zend_Auth::getInstance();
                $userhaslogin = $auth->getStorage()->read();
                //$where = "`class_id`='".$data['class_id']."' AND `user_id`='".$userhaslogin->id."'";
                //$resultHisTest = $modelsHisTest->fetchAll($where);
                $arrHisTestObj = array();
                /*
                  if(count($resultHisTest)>0)
                  foreach($resultHisTest as $resultHisTestItem){
                  $arrHisTestObj[] = $resultHisTestItem->toArray();
                  }
                 */
                $this->view->test = $modelsTest->toArray();
                /*
                 * END Table show bài  thi đã làm của user
                 */

                // Biến kiểm tra xem bài thi có được làm lại không
                $RE_TEST_COUNT = array();

                /*
                 * Begin Table show lịch thi cho user
                 */
                $modelSheduleExam = new Default_Models_SheduleExam();
                $modelExam = new Default_Models_Exam();
                $where = "`class_id`='" . $data['class_id'] . "'";
                $result = $modelSheduleExam->fetchAll($where);
                $allTableData = array();
                if (count($result) > 0)
                    foreach ($result as $key => $resultItem) {
                        //$resultItem = new Default_Models_SheduleExam();

                        $where = "`shedule_exam_id`='" . $resultItem->getId() . "' AND `user_id`='" . $userhaslogin->id . "'";
                        $resultHisTest = $modelsHisTest->fetchAll($where);
                        if (count($resultHisTest) > 0) {
                            foreach ($resultHisTest as $resultHisTestItem) {
                                //$resultHisTestItem = new Default_Models_Historyofusertest();
                                //var_dump($resultHisTest); die();
                                $arrHisTestObj[$key]['hisTestObj'] = $resultHisTestItem->toArray();
                                if ($resultHisTestItem->getDo_test_again() == 1)
                                    $RE_TEST_COUNT[] = $resultHisTestItem->getDo_test_again();
                                $modelSheduleExam->find("id", $resultHisTestItem->getShedule_exam_id());
                                if ($modelSheduleExam->getId()) {
                                    $modelExam->find("id", $modelSheduleExam->getExam_id());
                                    if ($modelExam->getId())
                                        $arrHisTestObj[$key]['examObj'] = $modelExam->toArray();
                                }
                            }
                        }else {
                            $allTableData[$key]['shedule_exam'] = $resultItem->toArray();
                            $temp_list_testid = $resultItem->getList_test_id();
                            if (empty($temp_list_testid))
                                $tesid_temp = "";
                            else
                                $tesid_temp = explode(",", $temp_list_testid);
                            $allTableData[$key]['testId'] = $tesid_temp[0];
                            $modelExam->find("id", $resultItem->getExam_id());
                            $allTableData[$key]['exam'] = $modelExam->toArray();
                        }
                    }
                /*
                 * END Table show lịch thi cho user
                 */
                $this->view->arrHisTestObj = $arrHisTestObj;
                $this->view->tableData = $allTableData;
                $this->view->selectedTab = "reviewTest";
                $this->view->RE_TEST_COUNT = $RE_TEST_COUNT;

                /*
                 * Hiển thị các chapter cho Lớp
                 */
                $modelChapter = new Default_Models_ChapterSubject();
                $where = "subject_id = " . $modelsClass->getSubject_id();
                $result = $modelChapter->fetchAll($where, 'stt');
                $arrSubjects = array();
                foreach ($result as $key => $subj) {
                    $arrSubjects[$key] = $subj->toArray();
                }
                $this->view->arrSubjects = $arrSubjects;
                $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/pdfobject.js');
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function editinfoAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
                $data = $this->_filter_edit_info($data);

                // validate data nếu có lỗi thì thông báo trả lại form 
                $this->_validate($data, true);

                if (count($this->_arrError) > 0) {
                    $this->view->arrError = $this->_arrError;
                    $this->view->Obj = $data;
                } else {
                    $this->_user = new Default_Models_User();
                    $this->_user->find("id", $data['id'])->toArray();
                    $this->_user->setFirstname($data['firstname']);
                    $this->_user->setLastname($data['lastname']);
                    $this->_user->setEmail($data['email']);
                    $this->_user->setYahoo($data['yahoo']);
                    $this->_user->setSkyper($data['skyper']);
                    $this->_user->setPhone($data['phone']);
                    $this->_user->setAddress($data['address']);
                    $this->_user->setDepartment($data['department']);
                    $this->_user->setCity($data['city']);

                    $this->_user->save();
                    $this->view->Obj = $this->_user->toArray();
                    $this->view->selectedTab = "editInfo";
                    $this->view->msgSuccess = "success";
                }
                $this->view->selectedTab = "editInfo";
            } elseif ($request->isGet()) {
                $data = $request->getParams();
                if (empty($data['user_id']))
                    throw new Exception("User id không tồn tại");
                $auth = Zend_Auth::getInstance();
                if ($auth->hasIdentity())
                    $userhaslogin = $auth->getStorage()->read();
                if ($data['user_id'] != $userhaslogin->id)
                    throw new Exception("User id không tồn tại");
                $modelsUser = new Default_Models_User();
                $modelsUser->find("id", $data['user_id']);
                if ($modelsUser->getId()) {
                    $this->view->Obj = $modelsUser->toArray();
                }else
                    throw new Exception("User id không tồn tại");
            }
        } catch (Exception $ex) {
            //$this->_arrError[] = $ex->getMessage();  
            //$this->view->arrError = $this->_arrError; 
            $this->_redirect("error/error");
        }
    }

    public function studentgentestAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                
            } elseif ($request->isGet()) {
                $data = $request->getParams();

                $ListChapterId = $data["chapter_subject_id"];
                $subject_id = $data["subject_id"];
//                $ListLevelFrom = $data["levelFrom"];
//                $ListLevelTo = $data["levelTo"];


                $ListAmountQuestion = $data["amount_question"];
                if (count($data["amount_question"]) > 0) {
                    foreach ($data["amount_question"] as $key => $amount_questionItem) {
                        if (!is_numeric(trim($amount_questionItem)))
                            throw new Exception("Vui lòng nhập số lượng câu hỏi là số.");
                    }
                }
                $item_per_page = intval(trim($data["question_perpage"]));
                $duration = intval(trim($data["duration_test"]));
                if (!is_numeric($duration) || $duration == 0)
                    $duration = 10;
                if (!is_numeric($item_per_page) || $item_per_page == 0)
                    $item_per_page = 10;


                $result_test = array();
                if (count($ListChapterId) > 0) {
                    foreach ($ListChapterId as $key => $ListChapterIdItem) {
                        $where = "`hidden`='off' AND ";
                        if (!empty($ListChapterIdItem) && is_numeric(trim($ListChapterIdItem)))
                            $where .= "`chapter_id`=" . $ListChapterIdItem;
                        elseif (!empty($subject_id) && is_numeric(trim($subject_id)))
                            $where .= "`subject_id`=" . $subject_id;
                        // level: vì giá trị hiển thị người dùng ngược với value đẩy lên nên ta phải để thế này
                        if (!empty($ListLevelFrom[$key]) && empty($ListLevelTo[$key]))
                            $where .= " AND `level`<=" . $ListLevelFrom[$key];
                        if (empty($ListLevelFrom[$key]) && !empty($ListLevelTo[$key]))
                            $where .= " AND `level`>=" . $ListLevelFrom[$key];
                        if (!empty($ListLevelFrom[$key]) && !empty($ListLevelTo[$key]))
                            $where .= " AND `level` BETWEEN " . $ListLevelTo[$key] . " AND " . $ListLevelFrom[$key];
                        if (!empty($where)) {
                            $models_question = new Default_Models_Question();
                            $result_row = $models_question->fetchAll($where);
                        }
                        // Kiểm tra sv nhập nhiều câu hỏi quá ko
                        if (!is_numeric($ListAmountQuestion[$key]) || $ListAmountQuestion[$key] == 0)
                            $ListAmountQuestion[$key] = 10;
                        if ($ListAmountQuestion[$key] > 150)
                            $ListAmountQuestion[$key] = 100;
                        // Kiểm tra sv nhập nhiều câu hỏi quá ko						
                        $total = count($result_row);
                        if ($total > 0) {
                            // hàm array_rand($arr,$num) lấy ngẫu nhiên từ mảng $arr $num phần tử
                            // nhưng nó lại return lại cái key của mảng arr thôi
                            $ListAmountQuestion[$key] = intval($ListAmountQuestion[$key]);
                            if ($ListAmountQuestion[$key] > count($result_row))
                                $numSelect = count($result_row);
                            else
                                $numSelect = $ListAmountQuestion[$key];
                            $result_row_key = array_rand($result_row, $numSelect);
                            $row = array();
                            // Khi mà $numSelect == 1 có nghĩa là chỉ phát sinh radom 1 câu hỏi thì hàm array_rand trả về 
                            // 1 số kiểu int chứ không phải array => ta phải ép nó vào array  
                            if ($numSelect == 1)
                                $result_row_key = (Array('0' => $result_row_key));
                            if (count($result_row_key) > 0)
                                foreach ($result_row_key as $result_row_keyItem)
                                    $row[] = $result_row[$result_row_keyItem]->getId();
                            $result_test["list_question"][] = $row; // những question được chọn sau khi random
                            $result_test["total_question"][] = $total; // những question thỏa mãn điều kiện
                            $result_test["amount_question"][] = $ListAmountQuestion[$key];
                            $result_test["question_perpage"] = $item_per_page;
                            $result_test["duration_test"] = $duration;
                        }else {
                            $result_test["list_question"][] = 0;
                            $result_test["total_question"][] = 0;
                            $result_test["amount_question"][] = $ListAmountQuestion[$key];
                            $result_test["question_perpage"] = $item_per_page;
                            $result_test["duration_test"] = $duration;
                        }
                        //throw new Exception("Không có câu hỏi nào thỏa mãn điều kiện của bạn.");
                    }
                }
                $auth = Zend_Auth::getInstance();
                if ($auth->hasIdentity()) {
                    $userhaslogin = $auth->getStorage()->read();
                }
                $recommend = new Recommend();
                $result_test['list_question'] = $recommend->getRecommendationWithChapters($userhaslogin->id, $ListChapterId, $data['level2'], $data["amount_question"]);
                $json_data = array("success" => true, "result" => $result_test);
                echo Zend_Json::encode($json_data);
                die();
            }
        } catch (Exception $ex) {
            $json_data = array("success" => false, "error" => $ex->getMessage());
            echo Zend_Json::encode($json_data);
            die();
        }
    }

    public function studentpracticeAction() {
        $this->_helper->layout->setLayout('studentpractice');
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/test.do.js');
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/test/test.js');
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
                $this->view->review_test = 0;
                $this->view->view_corect = 1;
                $this->view->view_feedback = 0;
                $this->view->view_send_result = 0;

                $question_id = $data["question_id"]; // array
                $answer_of_user = isset($data["question"]) ? $data["question"] : array();    // array
                $total_score = 0;
                if (count($question_id) > 0) {
                    foreach ($question_id as $key => $question_idItem) {
                        if (!empty($answer_of_user[$question_idItem])) {

                            /* ----------BEGIN CHECK $answer_of_user[$question_idItem] */
                            // Check 1 số kiểu câu hỏi khi mà user không làm mà vẫn submit lên. Check để gán biến thống
                            // kê state_question_after_dogarade: câu đúng, câu sai, câu ko làm
                            $flag_tempQuesItem = false;
                            if (count($answer_of_user[$question_idItem]) > 0) {
                                foreach ($answer_of_user[$question_idItem] as $tempQuesItem) {
                                    if (!empty($tempQuesItem)) {
                                        $flag_tempQuesItem = true;
                                        break;
                                    }
                                }
                            }
                            /* ----------END CHECK $answer_of_user[$question_idItem] */

                            // người dùng có thao tác đến question này
                            // với true false,multichoice nếu ko check thì không đẩy lên post
                            // đối với matching,completion,short ans,essay test luôn luôn đẩy lên post
                            if ($flag_tempQuesItem) {
                                $obj = DoGrade::_DoGrade(null, $question_idItem, $answer_of_user[$question_idItem]);
                                $total_score+= $obj->num_score;
                                $data["question_score"][$key] = $obj->num_score;
                                if ($obj->num_score > 0) {
                                    $this->_updateStatisticAdvisory($question_idItem, "_true");
                                    $data["state_question_after_dogarade"][$key] = "answerEatScore";
                                } else {
                                    $this->_updateStatisticAdvisory($question_idItem, "_false");
                                    $data["state_question_after_dogarade"][$key] = "answerNoEatScore";
                                }
                            } else {
                                $data["question_score"][$key] = 0;
                                $data["state_question_after_dogarade"][$key] = "questionNotDone";
                            }
                        } else {
                            $data["question_score"][$key] = 0;
                            $data["state_question_after_dogarade"][$key] = "questionNotDone";
                        }
                    }
                }
                $data["total_score"] = $total_score;
                $data["hiTimeEndPractice"] = date("Y-m-d G:i:s");
                $data["timeEndCalculate"] = date("U");
                $data["totalTimeDoPractice"] = Zend_View_Helper_FormatDate::timeBetween($data["timeStartCalculate"], $data["timeEndCalculate"]);
                $this->view->data = $data;
                $this->view->result_test = "on";
                /*
                  $modelsHisTest = new Default_Models_Historyofusertest();
                  $modelsHisTest->setList_question_id(Zend_Json::encode($data["question_id"])); // question id
                  $modelsHisTest->setList_answer_of_user(Zend_Json::encode($data["question"])); // các câu trả lời của user
                  $modelsHisTest->setList_score_of_question(Zend_Json::encode($data["question_score"])); // điểm làm của  từng câu trả lời
                  $modelsHisTest->save();
                 */

                $auth = Zend_Auth::getInstance();
                if ($auth->hasIdentity()) {
                    $userhaslogin = $auth->getStorage()->read();
                }

                $question = $data['question_id'];
                $answer = $data['question_score'];
                $user_id = $userhaslogin->id;
                $date = $data['timeEndCalculate'];


                for ($i = 0; $i < count($question); $i++) {
                    $rating = new Default_Models_Rating();
                    $rating->setUserID($user_id);
                    $rating->setDate($date);
                    $rating->setQuestionID($question[$i]);
                    $rating->setResult($answer[$i]);
                    $rating->save();
                }


                $this->render("dopracticestudent");
            } elseif ($request->isGet()) {
                $data = $request->getParams();
                $this->view->review_test = 0;
                $this->view->view_corect = 0;
                $this->view->view_feedback = 0;
                $this->view->view_send_result = 0;
//                $list_score    				= explode(',',$models_test->getList_score());
                $data["question_id"] = explode(',', $data["listQuestionSubmit"]);
//                $data["list_score_in_test_table"]= $list_score;
                $data["question_score"] = array();
                $this->view->data = $data;
                $this->view->hasClock = true;
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function dopracticestudentAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                
            } elseif ($request->isGet()) {
                
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function registerclassAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
                if (!empty($data['course_id'])) {
                    $modelsCourse = new Default_Models_Course();
                    $modelsCourse->find("id", $data['course_id']);
                    if ($modelsCourse->getId())
                        $this->_redirect("/pagestudent/processjoinclass/course_id/" . $data['course_id']);
                    else
                        throw new Exception("Không tồn tại khóa học này");
                }
            }elseif ($request->isGet()) {
                
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function removestudentclassAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                
            } elseif ($request->isGet()) {
                $data = $request->getParams();
                if (!empty($data['class_id'])) {
                    $auth = Zend_Auth::getInstance();
                    if ($auth->hasIdentity())
                        $userhaslogin = $auth->getStorage()->read();
                    else {
                        $this->view->haslogin = false;
                        $this->_redirect('auth/login');
                    }
                    $classId = $data['class_id'];
                    $models_classhasstudent = new Default_Models_ClassHasStudent();
                    $where = "`class_id`='" . $classId . "' AND `user_id`='" . $userhaslogin->id . "'";
                    $result = $models_classhasstudent->fetchAll($where);
                    if (count($result) > 0) {
                        foreach ($result as $resultItem)
                            $arr = $resultItem->getId();
                        $models_classhasstudent->delete("id", $arr);
                        $this->view->removestudentOK = "removestudentOK";
                        $this->_redirect("/pagestudent/studentjoinclass");
                    }else
                        throw new Exception("Không tồn tại lớp học này");
                }else
                    throw new Exception("Không tồn tại lớp học này");
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function registerclassincourseAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
            } elseif ($request->isGet()) {
                $data = $request->getParams();
                $modelsCourse = new Default_Models_Course();
                if (!empty($data['courseid'])) {
                    $modelsCourse->find("id", $data['courseid']);
                    if ($modelsCourse->getId()) {
                        $this->view->courseObj = $modelsCourse->toArray();
                        $modelsClass = new Default_Models_Classs();
                        /* BEGIN tìm những class id mà sinh viên đã đăng ký tham gia học */
                        $auth = Zend_Auth::getInstance();
                        if ($auth->hasIdentity())
                            $userhaslogin = $auth->getStorage()->read();
                        else {
                            $this->view->haslogin = false;
                            $this->_redirect('auth/login');
                        }
                        $models_classhasstudent = new Default_Models_ClassHasStudent();
                        $where = "`user_id`=" . $userhaslogin->id;
                        $result = $models_classhasstudent->groupBySql($where);
                        $classIdTemp = array();
                        if (count($result) > 0)
                            foreach ($result as $key => $resultItem) {
                                $classIdTemp[] = $resultItem->class_id;
                            }
                        /* END tìm những class id mà sinh viên đã đăng ký tham gia học */

                        // NEXT tìm tất cả các lớp học của khóa học hiện tại
                        $result = $modelsClass->fetchAll("`course_id`='" . $data['courseid'] . "'");
                        $arrClassObj = array();
                        $arrClassStudentJoin = array();
                        if (count($result) > 0)
                            foreach ($result as $key => $resultItem) {
                                $arrClassObj[$key] = $resultItem->toArrayHaveConvertDate();
                                //Nếu mà lớp học sinh viên đã tham gia thì gán cờ không hiển thị link tham gia nữa
                                if (in_array($arrClassObj[$key]['id'], $classIdTemp))
                                    $arrClassObj[$key]['flagClassUserHasJoin'] = 1;
                                else
                                    $arrClassObj[$key]['flagClassUserHasJoin'] = 0;
                                // Kiểm tra lớp học đã max user chưa, nếu max rồi thì gán biến check xuống dưới 
                                $modelClassHasStudent = new Default_Models_ClassHasStudent();
                                $maxStudentInClass = $modelClassHasStudent->fetchAll("`class_id`='" . $arrClassObj[$key]['id'] . "'");
                                if (count($maxStudentInClass) >= $resultItem->getMax_user())
                                    $arrClassObj[$key]['fullStudent'] = 1;
                                else
                                    $arrClassObj[$key]['fullStudent'] = 0;
                                // Kiểm tra ra hết hạn đăng ký chưa?
                                if (strtotime($arrClassObj[$key]['time_end_register']) < date("U"))
                                    $arrClassObj[$key]['block_register'] = 1;
                                else
                                    $arrClassObj[$key]['block_register'] = 0;
                            }
                        else
                            $arrClassObj = null;
                        $this->view->arrClassObj = $arrClassObj;
                    }else
                        throw new Exception("Không tồn tại khóa học này");
                }else
                    throw new Exception("Không tồn tại khóa học này");
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function processjoinclassAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                
            } elseif ($request->isGet()) {
                $data = $request->getParams();
                if (!empty($data['course_id']) && !empty($data['class_id'])) {
                    $modelsCourse = new Default_Models_Course();
                    $modelsCourse->find("id", $data['course_id']);
                    $modelsClass = new Default_Models_Classs();
                    $modelsClass->find("id", $data['class_id']);
                    // Kiểm tra user dùng cố tình đăng ký lớp học đã max user
                    $modelClassHasStudent = new Default_Models_ClassHasStudent();
                    $maxStudentInClass = $modelClassHasStudent->fetchAll("`class_id`='" . $data['class_id'] . "'");
                    if (count($maxStudentInClass) >= $modelsClass->getMax_user())
                        throw new Exception("Lớp học đã đủ sinh viên.");
                    $tempID = $modelsCourse->getId();
                    if (empty($tempID) || !is_numeric($tempID))
                        throw new Exception("Không tồn tại id của khóa học.");

                    $tempID = $modelsClass->getId();
                    if (empty($tempID))
                        throw new Exception("Không tồn tại id của lớp học.");

                    $auth = Zend_Auth::getInstance();
                    if ($auth->hasIdentity()) {
                        $userhaslogin = $auth->getStorage()->read();
                        $modelsUser = new Default_Models_User();
                        $modelsUser->find("id", $userhaslogin->id);
                        if ($modelsUser->getId()) {
                            // Xử lý cập nhật list_course khi user tham gia vào khóa học
                            $strListCourseHasJoin = $modelsUser->getList_course_join();
                            if (empty($strListCourseHasJoin))
                                $modelsUser->setList_course_join(trim($data['course_id']));
                            else {
                                $arrListCourseHasJoin = explode(",", $strListCourseHasJoin);
                                if (!in_array(trim($data['course_id']), $arrListCourseHasJoin))
                                    $modelsUser->setList_course_join($strListCourseHasJoin . "," . trim($data['course_id']));
                            }

                            // xử lý cập nhật sinh viên vào lớp học
                            $modelsClassHasStudent = new Default_Models_ClassHasStudent();

                            $where = " `class_id`=`" . $data['class_id'] . "` AND `user_id`=`" . $userhaslogin->id . "`";
                            //var_dump($where); die();
                            $resul_class_has_stu = $modelsClassHasStudent->fetchAll(" `class_id`=" . $data['class_id'] . " AND `user_id`=" . $userhaslogin->id . "");
                            if (count($resul_class_has_stu) == 0) {
                                $modelsClassHasStudent->setClass_id($data['class_id']);
                                $modelsClassHasStudent->setUser_id($userhaslogin->id);
                                $modelsClassHasStudent->save();
                            }

                            $modelsUser->save();
                            $this->_redirect("/pagestudent/classstudent/class_id/" . $data['class_id']);
                        }
                    }
                } elseif (!empty($data['course_id']) && empty($data['class_id'])) {
                    $auth = Zend_Auth::getInstance();
                    if ($auth->hasIdentity()) {
                        $userhaslogin = $auth->getStorage()->read();
                        $modelsUser = new Default_Models_User();
                        $modelsUser->find("id", $userhaslogin->id);
                        if ($modelsUser->getId()) {
                            // Xử lý cập nhật list_course khi user tham gia vào khóa học
                            $list_course_temp = $modelsUser->getList_course_join();
                            if (!empty($list_course_temp))
                                $modelsUser->setList_course_join($list_course_temp . "," . trim($data['course_id']));
                            else
                                $modelsUser->setList_course_join(trim($data['course_id']));
                            $modelsUser->save();
                            $this->_redirect("pagestudent/studentmanagecourse");
                            //$this->_redirect("pagestudent/regsuccessandlogout");
                        }
                    }
                }else {
                    throw new Exception("Không tồn tại khóa học.");
                }
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
            //die();
        }
    }

    public function regsuccessandlogoutAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                
            } elseif ($request->isGet()) {
                
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function studentmanagecourseAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                // check id set default quăng lên nếu không tồn tại thì lấy list course cũ và  
                $data = $request->getParams();

                $idCourseDefault = $data["rdCourseDefault"];
                if (!is_numeric(trim($idCourseDefault))) {
                    $this->_arrError[] = "Giá trị course default không phải là số.";
                    throw new Exception("");
                }
                $auth = Zend_Auth::getInstance();
                $userhaslogin = $auth->getStorage()->read();
                $modelsUser = new Default_Models_User();
                $modelsUser->find("id", $userhaslogin->id);
                if ($modelsUser->getId()) {
                    $str_list_course = $modelsUser->getList_course_join();
                    $arr_course = array();
                    if (!empty($str_list_course)) {
                        $arr_list_course_id = explode(',', $modelsUser->getList_course_join());
                        $arr_list_course_id_temp = array();
                        $str_list_course_save_db = "";
                        if (count($arr_list_course_id) == 1) {
                            $str_list_course_save_db = trim($idCourseDefault);
                            $modelsCourse = new Default_Models_Course();
                            $modelsCourse->find("id", $idCourseDefault);
                            $arr_course[] = $modelsCourse->toArray();
                        } else {
                            foreach ($arr_list_course_id as $key => $arr_list_course_idItem) {
                                if ($idCourseDefault != $arr_list_course_idItem)
                                    $arr_list_course_id_temp[] = trim($arr_list_course_idItem);
                            }
                            $arr_list_course_id_temp = array_merge(array(trim($idCourseDefault)), $arr_list_course_id_temp);
                            $str_list_course_save_db = implode(',', $arr_list_course_id_temp);
                            // lấy obj các course trả xuống view

                            foreach ($arr_list_course_id_temp as $arr_list_course_id_tempItem) {
                                $modelsCourse = new Default_Models_Course();
                                $modelsCourse->find("id", $arr_list_course_id_tempItem);
                                $arr_course[] = $modelsCourse->toArray();
                            }
                        }
                    }
                    else
                        $str_list_course_save_db = "";
                    $this->view->arr_course = $arr_course;
                    $this->view->success = "success";
                    $modelsUser->setList_course_join($str_list_course_save_db);
                    $modelsUser->save();
                }
            }elseif ($request->isGet()) {
                $auth = Zend_Auth::getInstance();
                $userhaslogin = $auth->getStorage()->read();
                $modelsUser = new Default_Models_User();
                $modelsUser->find("id", $userhaslogin->id);
                if ($modelsUser->getId()) {
                    $str_list_course = $modelsUser->getList_course_join();
                    if (!empty($str_list_course))
                        $arr_list_course_id = explode(',', $modelsUser->getList_course_join());
                    else
                        $arr_list_course_id = array();
                    $arr_course = array();
                    if (count($arr_list_course_id > 0)) {
                        foreach ($arr_list_course_id as $arr_list_course_idItem) {
                            $modelsCourse = new Default_Models_Course();
                            $modelsCourse->find("id", $arr_list_course_idItem);
                            $arr_course[] = $modelsCourse->toArray();
                        }
                    }
                    $this->view->arr_course = $arr_course;
                }
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function studentbeforedotestAction() {
        $this->_helper->layout->setLayout('studentdotest');
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                
            } elseif ($request->isGet()) {
                $data = $request->getParams();
                $data = $this->_filterTest($data);

                if (empty($data['test_id']) || empty($data['exam_id']) || empty($data['class_id']) || empty($data['shedule_exam']))
                    throw new Exception("Không tồn tại đề thi này");
                $modelTest = new Default_Models_Test();
                $modelExam = new Default_Models_Exam();
                $modelClass = new Default_Models_Classs();
                $modelSheduleExam = new Default_Models_SheduleExam();
                $modelTest->find("id", $data['test_id']);
                $modelExam->find("id", $data['exam_id']);
                $modelClass->find("id", $data['class_id']);
                $modelSheduleExam->find("id", $data['shedule_exam']);
                $idtest = $modelTest->getId();
                $idExam = $modelExam->getId();
                $idClass = $modelClass->getId();
                $idSheduleExam = $modelSheduleExam->getId();
                if (empty($idtest) || empty($idExam) || empty($idClass) || empty($idSheduleExam))
                    throw new Exception("Không tồn tại đề thi này");
                $data['test_obj'] = $modelTest->toArray();
                $data['exam_obj'] = $modelExam->toArray();
                //$data['shedule_exam'] = $modelSheduleExam->toArray();
                $data["question_id"] = explode(',', $modelTest->getList_question());
                $this->view->data = $data;
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function studentdotestAction() {
        $this->_helper->layout->setLayout('studentdotest');
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/test.do.js');
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/test/test.js');
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
                $data = $this->_filterTest($data);
                if (empty($data['test_id']) || empty($data['exam_id']) || empty($data['class_id']) || empty($data['shedule_exam']))
                    throw new Exception("Không tồn tại đề thi này");
                $modelTest = new Default_Models_Test();
                $modelExam = new Default_Models_Exam();
                $modelClass = new Default_Models_Classs();
                $modelSheduleExam = new Default_Models_SheduleExam();
                $modelTest->find("id", $data['test_id']);
                $modelExam->find("id", $data['exam_id']);
                $modelClass->find("id", $data['class_id']);
                $modelSheduleExam->find("id", $data['shedule_exam']);
                $idtest = $modelTest->getId();
                $idExam = $modelExam->getId();
                $idClass = $modelClass->getId();
                $idSheduleExam = $modelSheduleExam->getId();
                if (empty($idtest) || empty($idExam) || empty($idClass) || empty($idSheduleExam))
                    throw new Exception("Không tồn tại đề thi này");
                $auth = Zend_Auth::getInstance();
                if ($auth->hasIdentity()) {
                    $userhaslogin = $auth->getStorage()->read();
                    // Kiểm tra nếu sinh viên bấm nút BACK ở trình duyệt thì vẫn cho làm bài test đó	
                    $modelHistoryUserTest = new Default_Models_Historyofusertest();
                    $where = "`shedule_exam_id`='" . $idSheduleExam . "' AND `class_id`='" . $idClass . "' AND `user_id`='" . $userhaslogin->id . "'";
                    $result = $modelHistoryUserTest->fetchAll($where);
                    if (count($result) > 0) {
                        foreach ($result as $resultItem)
                            $data["last_insert_history_test_id"] = $resultItem->getId();
                    } else {

                        /* 	
                          $this->view->review_test        = 0;
                          $this->view->view_corect        = 0;
                          $this->view->view_feedback 		= 0;
                          $this->view->view_send_result   = 1;
                          //$list_score    				= explode(',',$models_test->getList_score());
                          $data['test_obj'] = $modelTest->toArray();
                          $data["question_id"] 	= explode(',', $modelTest->getList_question());
                          $data["list_score_in_test_table"]=  explode(',',$modelTest->getList_score());
                          $data["question_perpage"] = $modelTest->getQuestion_perpage();
                          $data["question_score"] = array();
                         */
                        $modelHistoryUserTest = new Default_Models_Historyofusertest();
                        $modelHistoryUserTest->setTest_id($data['test_id']);
                        $modelHistoryUserTest->setUser_id($userhaslogin->id);
                        $modelHistoryUserTest->setTime_start(date("U"));
                        $modelHistoryUserTest->setShedule_exam_id($idSheduleExam);
                        $modelHistoryUserTest->setClass_id($idClass);
                        $last_insert_test_id = $modelHistoryUserTest->save();
                        $data["last_insert_history_test_id"] = $last_insert_test_id;
                    }
                } else {
                    $this->view->haslogin = false;
                    $this->_redirect('auth/login');
                }
                //$list_score    				= explode(',',$models_test->getList_score());
                //$data["question_id"] 	= explode(',', $data["listQuestionSubmit"]);
                //$data["list_score_in_test_table"]= $list_score;
                //$this->view->data     = $data;
                $this->_redirect("/pagestudent/studentdotest/histestid/" . $data["last_insert_history_test_id"]);
            } elseif ($request->isGet()) {
                $data = $request->getParams();
                $histestid = $data["histestid"];
                $this->view->hasClock = false;
                if (empty($histestid))
                    throw new Exception("Không tồn tại id bài làm của đề thi này");
                $modelHistoryUserTest = new Default_Models_Historyofusertest();
                $modelHistoryUserTest->find("id", $histestid);
                $idHisTest = $modelHistoryUserTest->getId();
                if (empty($idHisTest))
                    throw new Exception("Không tồn tại đề thi này");

                // Begin lấy thông tin user làm bài
                $auth = Zend_Auth::getInstance();
                if ($auth->hasIdentity()) {
                    $userhaslogin = $auth->getStorage()->read();
                    $modelsUser = new Default_Models_User();
                    $modelsUser->find("id", $userhaslogin->id);
                    if ($modelsUser->getId())
                        $this->view->UserObj = $modelsUser->toArray();
                }
                // End lấy thông tin user làm bài					
                if ($modelHistoryUserTest->getUser_id() != $userhaslogin->id)
                    throw new Exception("Không tồn tại đề thi này");

                $first_load = $modelHistoryUserTest->getFirst_load_done();
                if ($first_load == 1) {

                    $this->view->review_test = 0;
                    $this->view->view_corect = 0;
                    $this->view->view_feedback = 0;
                    $this->view->view_send_result = 0;

                    $modelTest = new Default_Models_Test();
                    $modelTest->find("id", $modelHistoryUserTest->getTest_id());
                    $idtest = $modelTest->getId();
                    if (empty($idtest))
                        throw new Exception("Không tồn tại đề thi này");
                    /*
                     * BeGin kiểm tra nếu bấm back lại mà hết giờ thì thông báo hết giờ, ko thì làm tiếp thời gian còn lại.... END
                     */
                    $timeBeginTest = $modelHistoryUserTest->getTime_start();
                    $durationTest = $modelTest->getDuration_test();
                    // Convert thời gian làm bài thi ra giây, vì Duration_test là phút 		
                    $durationTest = $durationTest * 60;
                    $timeEndTest = ($timeBeginTest * 1) + ($durationTest * 1);
                    if ($timeEndTest < date("U")) {
                        throw new Exception("Hết thời gian làm bài...");
                    }
                    /*
                     * END kiểm tra nếu bấm back lại mà hết giờ thì thông báo hết giờ, ko thì làm tiếp thời gian còn lại.... END
                     */

                    $data['test_id'] = $idtest;
                    $data['test_obj'] = $modelTest->toArray();

                    //$data["question_score"] 			= Zend_Json::decode($modelHistoryUserTest->getList_score_of_question());
                    $data["question_id"] = Zend_Json::decode($modelHistoryUserTest->getList_question_id()); // array
                    $data["list_score_in_test_table"] = Zend_Json::decode($modelHistoryUserTest->getList_score_table_test());
                    $data["question"] = Zend_Json::decode($modelHistoryUserTest->getList_answer_of_user());    // array
                    // Lưu list score của câu hỏi vào 1 mảng tạm để phục vụ chấm điểm
                    $modelScoreTemp = new Default_Models_Scoretemp();
                    $modelScoreTemp->setList_score_value(Zend_Json::encode($data["list_score_in_test_table"]));
                    $last_insert_id_ScoreTemp = $modelScoreTemp->save();
                    $this->view->scoretemp = $last_insert_id_ScoreTemp;

                    $data["question_perpage"] = $modelTest->getQuestion_perpage();
                    //$data["question_score"] = $data["list_score_in_test_table"];
                    $data["last_insert_history_test_id"] = $idHisTest;
                    $this->view->data = $data;
                    $this->view->first_load = 1;
                } else {

                    $this->view->review_test = 0;
                    $this->view->view_corect = 0;
                    $this->view->view_feedback = 0;
                    $this->view->view_send_result = 0;
                    $modelTest = new Default_Models_Test();
                    $modelTest->find("id", $modelHistoryUserTest->getTest_id());
                    $idtest = $modelTest->getId();
                    if (empty($idtest))
                        throw new Exception("Không tồn tại đề thi này");
                    /*
                     * BeGin kiểm tra nếu bấm back lại mà hết giờ thì thông báo hết giờ, ko thì làm tiếp thời gian còn lại.... END
                     */
                    $timeBeginTest = $modelHistoryUserTest->getTime_start();
                    $durationTest = $modelTest->getDuration_test();
                    // Conver thời gian làm bài thi ra giây, vì Duration_test là phút 		
                    $durationTest = $durationTest * 60;
                    $timeEndTest = ($timeBeginTest * 1) + ($durationTest * 1);
                    if ($timeEndTest < date("U")) {
                        throw new Exception("Hết thời gian làm bài...");
                    }
                    /*
                     * END kiểm tra nếu bấm back lại mà hết giờ thì thông báo hết giờ, ko thì làm tiếp thời gian còn lại.... END
                     */

                    $data['test_id'] = $idtest;
                    $data['test_obj'] = $modelTest->toArray();
                    $data["question_id"] = explode(',', $modelTest->getList_question());
                    $data["list_score_in_test_table"] = explode(',', $modelTest->getList_score());

                    // Chỗ này dùng để phục vụ thằng xáo trộn câu hỏi-> xáo trộn cả điểm của câu hỏi
                    $arrQuestionAndScore = Lib_Arr::syn_2array_to_one($data["question_id"], $data["list_score_in_test_table"]);

                    if ($modelTest->getShuffle_question() == 1) {
                        // Xáo trộn mảng câu hỏi
                        shuffle($data["question_id"]);
                        // Cập nhật lại điểm được giảng viên set khi tạo đề thi tương ứng với câu hỏi bị xáo trộn
                        $newListScore = Lib_Arr::update_score_new_listQuestion($data["question_id"], $arrQuestionAndScore);
                        $data["list_score_in_test_table"] = $newListScore;
                    }
                    // Lưu list score của câu hỏi bị xáo trộn này vào 1 mảng tạm để phục vụ chấm điểm
                    $modelScoreTemp = new Default_Models_Scoretemp();
                    $modelScoreTemp->setList_score_value(Zend_Json::encode($data["list_score_in_test_table"]));


                    /* Đoạn này viết hỏng thay bằng đoạn trên, nếu có lỗi ở đoạn trên thì dùng lại đoạn dưới này cũng được
                      $arrQuestionAndScore = Lib_Arr::syn_2array_to_one($data["question_id"],$data["list_score_in_test_table"]);
                      // Xáo trộn mảng câu hỏi
                      if($modelTest->getShuffle_question()==1)
                      shuffle($data["question_id"]);
                      // Cập nhật lại điểm được giảng viên set khi tạo đề thi tương ứng với câu hỏi bị xáo trộn
                      $newListScore = Lib_Arr::update_score_new_listQuestion($data["question_id"],$arrQuestionAndScore);
                      $data["list_score_in_test_table"] = $newListScore;
                      // Lưu list score của câu hỏi bị xáo trộn này vào 1 mảng tạm để phục vụ chấm điểm
                      $modelScoreTemp = new Default_Models_Scoretemp();
                      $modelScoreTemp->setList_score_value(Zend_Json::encode($newListScore));

                     */

                    $modelHistoryUserTest->setFirst_load_done(1);
                    $modelHistoryUserTest->save();
                    $last_insert_id = $modelScoreTemp->save();
                    $this->view->scoretemp = $last_insert_id;

                    $data["question_perpage"] = $modelTest->getQuestion_perpage();
                    //$data["question_score"] = $data["list_score_in_test_table"];
                    $data["last_insert_history_test_id"] = $idHisTest;
                    $this->view->data = $data;
                    $this->view->first_load = 0;
                }
                $this->view->hasClock = true;
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function dogradestudenttestAction() {
        $this->view->hasClock = false;
        $this->_helper->layout->setLayout('studentdotest');
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/test.do.js');
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/test/test.js');
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
                $data['test_id'] = trim($data['test_id']);
                $data['last_insert_history_test_id'] = trim($data['last_insert_history_test_id']);
                if (empty($data['test_id']) || empty($data['last_insert_history_test_id']))
                    throw new Exception("Thao tác không được phép, hết giờ làm bài thi.");
                $modelTest = new Default_Models_Test();
                $modelHistoryUserTest = new Default_Models_Historyofusertest();
                $modelTest->find("id", $data['test_id']);
                $modelHistoryUserTest->find("id", $data['last_insert_history_test_id']);
                $idtest = $modelTest->getId();
                $idHisUserTest = $modelHistoryUserTest->getId();
                if (empty($idtest) || empty($idHisUserTest))
                    throw new Exception("Không tồn tại đề thi này");
                // do grade
                // Lấy list score tươgn ứng với list câu hỏi xáo trộn mà sv làm để chấm điểm câu hỏi
                $modelScoreTemp = new Default_Models_Scoretemp();
                $modelScoreTemp->find("id", trim($data['scoretemp']));
                if ($modelScoreTemp->getId()) {
                    $list_score = Zend_Json::decode($modelScoreTemp->getList_score_value());
                    $modelScoreTemp->delete("id", trim($data['scoretemp'])); // Xóa list score tạm đi luôn
                }
                else
                    $list_score = array();

                $data["question_perpage"] = $modelTest->getQuestion_perpage();
                $data["list_score_in_test_table"] = $list_score;
                //$data["question_score"] = $data["list_score_in_test_table"];
                $OPTION_REVIEW_TEST = $modelTest->getReview_after_test();
                if (strlen($OPTION_REVIEW_TEST) != 4)
                    $OPTION_REVIEW_TEST = "0000";
                $OPTION_REVIEW_TEST = str_split($OPTION_REVIEW_TEST);
                $this->view->review_test = 1;
                $this->view->view_corect = $OPTION_REVIEW_TEST[0];
                $this->view->view_feedback = $OPTION_REVIEW_TEST[1];
                $this->view->view_send_result = 0;
                /*
                 * BEGIN variable xử lý tách câu hỏi luận đề ra, tách cả câu trả lời của sinh viên theo
                 * lấy cả điểm trong test table
                 * Điểm làm bài thì để giảng viên chấm và cập nhật sau. 
                 */
                $ESSAY_list_question_id = array();
                $ESSAY_list_answer_of_user = array();
                $ESSAY_list_score_of_question = array();
                $ESSAY_list_score_table_test = array();
                /*
                 * END variable xử lý tách câu hỏi luận đề ra, tách cả câu trả lời của sinh viên theo
                 */
                $timeEndTest = date("U");
                $question_id = $data["question_id"]; // array
                $answer_of_user = $data["question"];    // array
                $total_score = 0;

                if (count($question_id) > 0) {
                    foreach ($question_id as $key => $question_idItem) {
                        $flagAdd_Essay = 0;
                        // BEGIN get $ESSAY_list_question_id
                        $modelQuesion = new Default_Models_Question();
                        $modelQuesion->find("id", $question_idItem);
                        if ($modelQuesion->getId()) {
                            if ($modelQuesion->getType_question() == 5) {
                                $flagAdd_Essay = 1;
                                $ESSAY_list_question_id[] = $question_idItem;
                            }
                        }
                        // END get ESSAY_list_question_id
                        if (!empty($answer_of_user[$question_idItem])) {
                            if ($flagAdd_Essay == 1)
                                $ESSAY_list_answer_of_user [] = $answer_of_user[$question_idItem];
                            /* ----------BEGIN CHECK $answer_of_user[$question_idItem] */
                            // Check 1 số kiểu câu hỏi khi mà user không làm mà vẫn submit lên. Check để gán biến thống
                            // kê state_question_after_dogarade: câu đúng, câu sai, câu ko làm
                            $flag_tempQuesItem = false;
                            if (count($answer_of_user[$question_idItem]) > 0) {
                                foreach ($answer_of_user[$question_idItem] as $tempQuesItem) {
                                    if (!empty($tempQuesItem)) {
                                        $flag_tempQuesItem = true;
                                        break;
                                    }
                                }
                            }
                            /* ----------END CHECK $answer_of_user[$question_idItem] */

                            // người dùng có thao tác đến question này
                            // với true false,multichoice nếu ko check thì không đẩy lên post
                            // đối với matching,completion,short ans,essay test luôn luôn đẩy lên post
                            if ($flag_tempQuesItem && isset($list_score[$key])) {
                                $obj = DoGrade::_DoGrade($list_score[$key], $question_idItem, $answer_of_user[$question_idItem]);
                                $total_score+= $obj->num_score;
                                $data["question_score"][$key] = $obj->num_score;
                                if ($obj->num_score > 0) {
                                    $this->_updateStatisticAdvisory($question_idItem, "_true");
                                    $this->_updateStatisticQuestion($question_idItem, "_true");
                                    $data["state_question_after_dogarade"][$key] = "answerEatScore";
                                } else {
                                    $this->_updateStatisticAdvisory($question_idItem, "_false");
                                    $this->_updateStatisticQuestion($question_idItem, "_false");
                                    $data["state_question_after_dogarade"][$key] = "answerNoEatScore";
                                }
                            } else {
                                $data["question_score"][$key] = 0;
                                $data["state_question_after_dogarade"][$key] = "questionNotDone";
                            }
                        } else {
                            $data["question_score"][$key] = 0;
                            $data["state_question_after_dogarade"][$key] = "questionNotDone";
                        }
                    }
                }

                // List score trong test table (giảng viên set lúc tạo đề )tương ứng với list question bị xáo trộn
                // Ta lưu lại trong histest để đảm bảo đúng câu hỏi đúng điểm của nó
                $list_score_save_his_test = $list_score;
                if (!empty($ESSAY_list_question_id)) {
                    // BEGIN get essay_list_score_table_test
                    if (count($question_id) > 0)
                        foreach ($question_id as $key => $question_idItem) {
                            if (in_array($question_idItem, $ESSAY_list_question_id))
                                $ESSAY_list_score_table_test[] = $list_score[$key];
                        }
                    // END get essay_list_score_table_test
                }
                $total_score = round($total_score, $modelTest->getDecimal_digits_in_grades());
                $data["total_score"] = $total_score;
                if (!empty($ESSAY_list_question_id)) {
                    // NEW NEW ADD
                    $modelHistoryUserTest->setEssay_list_question_id(Zend_Json::encode($ESSAY_list_question_id));
                    $modelHistoryUserTest->setEssay_list_answer_of_user(Zend_Json::encode($ESSAY_list_answer_of_user));
                    //$modelHistoryUserTest->setEssay_list_score_of_question() ;
                    $modelHistoryUserTest->setEssay_list_score_table_test(Zend_Json::encode($ESSAY_list_score_table_test));
                    // NEW NEW ADD
                }
                $modelHistoryUserTest->setList_question_id(Zend_Json::encode($data["question_id"]));
                $modelHistoryUserTest->setList_answer_of_user(Zend_Json::encode($data["question"]));
                $modelHistoryUserTest->setList_score_of_question(Zend_Json::encode($data["question_score"]));

                $modelHistoryUserTest->setList_score_table_test(Zend_Json::encode($list_score_save_his_test));
                $modelHistoryUserTest->setTotal_score($total_score);
                $modelHistoryUserTest->setTime_finished($timeEndTest);
                $modelHistoryUserTest->setDo_test_again(0);
                $modelHistoryUserTest->save();





                /*
                 * Lưu vào bảng Rating
                 */
                $question = $data['question_id'];
                $answer = $data['question_score'];
                $user_id = $modelHistoryUserTest->getUser_id();
                $date = $modelHistoryUserTest->getTime_finished();


                for ($i = 0; $i < count($question); $i++) {
                    $rating = new Default_Models_Rating();
                    $rating->setUserID($user_id);
                    $rating->setDate($date);
                    $rating->setQuestionID($question[$i]);
                    $rating->setResult($answer[$i]);
                    $rating->save();
                }


                /*
                 * Lưu khuyến nghị
                 */

                $recommend = new Recommend();

                $recommendation = new Default_Models_Recommendation();
                $recommendation->setUserID($user_id);
                $recommendation->setTestID($data['test_id']);
                $recommendation->setRecommendations(Zend_Json::encode($recommend->getRecommendationLesson($user_id, $data['test_id'])));
                $recommendation->save();


                $this->_redirect("/pagestudent/studentreviewtest/his_test_id/" . $modelHistoryUserTest->id);

                $data["timeStartTest"] = $modelHistoryUserTest->getTime_start();
                $data["timeEndTest"] = $timeEndTest;
                $data["totalTimeDoTest"] = Zend_View_Helper_FormatDate::timeBetween($data["timeStartTest"], $data["timeEndTest"]);
                $this->view->data = $data;

                // Begin lấy thông tin user làm bài
                $auth = Zend_Auth::getInstance();
                if ($auth->hasIdentity()) {
                    $userhaslogin = $auth->getStorage()->read();
                    $modelsUser = new Default_Models_User();
                    $modelsUser->find("id", $userhaslogin->id);
                    if ($modelsUser->getId())
                        $this->view->UserObj = $modelsUser->toArray();
                }
                // End lấy thông tin user làm bài
            }elseif ($request->isGet()) {
                // định xử lý chỗ sau khi làm bài xong thì redirect để xem lại, nhưng mà có vẻ hơi phức tạp => để sau
                //                                    $data["timeStartTest"]		 = $modelHistoryUserTest->getTime_start();
                //                                    $data["timeEndTest"]   		 = $timeEndTest;
                //                                    $data["totalTimeDoTest"] 	 =  Zend_View_Helper_FormatDate::timeBetween($data["timeStartTest"], $data["timeEndTest"]);
                //                                    $this->view->data     		 = $data;	                            
                //                                    // Begin lấy thông tin user làm bài
                //                                    $auth = Zend_Auth::getInstance();
                //                                    if ($auth->hasIdentity()){
                //                                            $userhaslogin = $auth->getStorage()->read();
                //                                            $modelsUser = new Default_Models_User();
                //                                            $modelsUser->find("id",$userhaslogin->id);
                //                                            if($modelsUser->getId())
                //                                                    $this->view->UserObj     = $modelsUser->toArray();
                //                                    }
                //                                    // End lấy thông tin user làm bài                            
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function autosavetestAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
                $data['test_id'] = trim($data['test_id']);
                $data['last_insert_history_test_id'] = trim($data['last_insert_history_test_id']);
                if (empty($data['test_id']) || empty($data['last_insert_history_test_id']))
                    throw new Exception("Không tồn tại đề thi này");
                $modelTest = new Default_Models_Test();
                $modelHistoryUserTest = new Default_Models_Historyofusertest();
                $modelTest->find("id", $data['test_id']);
                $modelHistoryUserTest->find("id", $data['last_insert_history_test_id']);
                $idtest = $modelTest->getId();
                $idHisUserTest = $modelHistoryUserTest->getId();
                if (empty($idtest) || empty($idHisUserTest))
                    throw new Exception("Không tồn tại đề thi này");

                // Begin Kiểm tra lệnh hủy bài làm khi sinh viên đang làm bài của giảng viên	
                $stop_do_test_now = $modelHistoryUserTest->getStop_do_test_now();
                if ($stop_do_test_now == 1)
                    throw new Exception("stopdotest");
                //$this->_redirect("/pagestudent/stopdotest");
                // End Kiểm tra lệnh hủy bài làm khi sinh viên đang làm bài của giảng viên
                // do grade
                // Lấy list score (lưu tạm ở bảng khác)tươgn ứng với list câu hỏi xáo trộn mà sv làm để chấm điểm câu hỏi
                $modelScoreTemp = new Default_Models_Scoretemp();
                $modelScoreTemp->find("id", trim($data['scoretemp']));
                if ($modelScoreTemp->getId()) {
                    $list_score = Zend_Json::decode($modelScoreTemp->getList_score_value());
                }
                else
                    $list_score = array();
                /*
                 * BEGIN variable xử lý tách câu hỏi luận đề ra, tách cả câu trả lời của sinh viên theo
                 * lấy cả điểm trong test table
                 * Điểm làm bài thì để giảng viên chấm và cập nhật sau. 
                 */
                $ESSAY_list_question_id = array();
                $ESSAY_list_answer_of_user = array();
                $ESSAY_list_score_of_question = array();
                $ESSAY_list_score_table_test = array();
                /*
                 * END variable xử lý tách câu hỏi luận đề ra, tách cả câu trả lời của sinh viên theo
                 */

                $question_id = $data["question_id"]; // array
                $answer_of_user = $data["question"];    // array
                $total_score = 0;
                if (count($question_id) > 0) {
                    foreach ($question_id as $key => $question_idItem) {
                        // BEGIN get $ESSAY_list_question_id
                        $modelQuesion = new Default_Models_Question();
                        $modelQuesion->find("id", $question_idItem);
                        if ($modelQuesion->getId()) {
                            if ($modelQuesion->getType_question() == 5) {
                                $flagAdd_Essay = 1;
                                $ESSAY_list_question_id[] = $question_idItem;
                            }
                        }
                        // END get ESSAY_list_question_id
                        if (!empty($answer_of_user[$question_idItem])) {

                            //Code cũ : $flagAdd_Essay == 1
                            if (!isset($flagAdd_Essay))
                                $ESSAY_list_answer_of_user [] = $answer_of_user[$question_idItem];
                            /* ----------BEGIN CHECK $answer_of_user[$question_idItem] */
                            // Check 1 số kiểu câu hỏi khi mà user không làm mà vẫn submit lên. Check để gán biến thống
                            // kê state_question_after_dogarade: câu đúng, câu sai, câu ko làm
                            $flag_tempQuesItem = false;
                            if (count($answer_of_user[$question_idItem]) > 0) {
                                foreach ($answer_of_user[$question_idItem] as $tempQuesItem) {
                                    if (!empty($tempQuesItem)) {
                                        $flag_tempQuesItem = true;
                                        break;
                                    }
                                }
                            }
                            /* ----------END CHECK $answer_of_user[$question_idItem] */

                            // người dùng có thao tác đến question này
                            // với true false,multichoice nếu ko check thì không đẩy lên post
                            // đối với matching,completion,short ans,essay test luôn luôn đẩy lên post
                            if ($flag_tempQuesItem) {
                                $obj = DoGrade::_DoGrade($list_score[$key], $question_idItem, $answer_of_user[$question_idItem]);
                                $total_score+= $obj->num_score;
                                $data["question_score"][$key] = $obj->num_score;
                            } else {
                                $data["question_score"][$key] = 0;
                            }
                        } else {
                            $data["question_score"][$key] = 0;
                        }
                    }
                }
                $list_score_save_his_test = $list_score;
                if (!empty($ESSAY_list_question_id)) {
                    // BEGIN get essay_list_score_table_test
                    if (count($question_id) > 0)
                        foreach ($question_id as $key => $question_idItem) {
                            if (in_array($question_idItem, $ESSAY_list_question_id))
                                $ESSAY_list_score_table_test[] = $list_score[$key];
                        }
                    // END get essay_list_score_table_test
                }
                $total_score = round($total_score, $modelTest->getDecimal_digits_in_grades());
                // NEW NEW ADD
                if (!empty($ESSAY_list_question_id)) {
                    $modelHistoryUserTest->setEssay_list_question_id(Zend_Json::encode($ESSAY_list_question_id));
                    $modelHistoryUserTest->setEssay_list_answer_of_user(Zend_Json::encode($ESSAY_list_answer_of_user));
                    //$modelHistoryUserTest->setEssay_list_score_of_question() ;
                    $modelHistoryUserTest->setEssay_list_score_table_test(Zend_Json::encode($ESSAY_list_score_table_test));
                    // NEW NEW ADD
                }
                $modelHistoryUserTest->setList_question_id(Zend_Json::encode($data["question_id"]));
                $modelHistoryUserTest->setList_answer_of_user(Zend_Json::encode($data["question"]));
                $modelHistoryUserTest->setList_score_of_question(Zend_Json::encode($data["question_score"]));

                $modelHistoryUserTest->setList_score_table_test(Zend_Json::encode($list_score_save_his_test));
                $modelHistoryUserTest->setTotal_score($total_score);
                $modelHistoryUserTest->setTime_finished(date("U"));
                $modelHistoryUserTest->setDo_test_again(0);
                $modelHistoryUserTest->save();




                $dataResponse = array("success" => true);
                echo Zend_Json::encode($dataResponse);
                die();
            } elseif ($request->isGet()) {
                $dataResponse = array("error" => "Lỗi phương thức");
                echo Zend_Json::encode($dataResponse);
                die();
            }
        } catch (Exception $ex) {
            $dataResponse = array("error" => $ex->getMessage());
            echo Zend_Json::encode($dataResponse);
            die();
        }
    }

    public function studentreviewtestAction() {
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/pdfobject.js');
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
            } elseif ($request->isGet()) {
                $data = $request->getParams();
                $his_test_id = $data["his_test_id"];
                $modelsHisTest = new Default_Models_Historyofusertest();
                $modelsHisTest->find("id", $his_test_id);
                $auth = Zend_Auth::getInstance();
                $uId = false;
                if ($auth->hasIdentity()) {
                    $userhaslogin = $auth->getStorage()->read();
                    $uId = $userhaslogin->id;
                }

                if (!is_null($modelsHisTest) && $modelsHisTest->getId() && $uId && $uId == $modelsHisTest->getUser_id()) {
                    $data = $modelsHisTest->toArray();
                    /*
                      $models_test = new Default_Models_Test();
                      $models_test->find("id",$data["testID"]);
                      if($models_test->getId()){
                      $list_score    		= explode(',',$models_test->getList_score());
                      $data["list_score_in_test_table"]= $list_score;
                      $data["question_perpage"] = $models_test->getQuestion_perpage();
                      }
                     */
                    $data["question_score"] = Zend_Json::decode($modelsHisTest->getList_score_of_question());
                    $data["question_id"] = Zend_Json::decode($modelsHisTest->getList_question_id()); // array
                    $data["question"] = Zend_Json::decode($modelsHisTest->getList_answer_of_user());    // array

                    /*
                     * Begin xử lý thêm các thẻ answerNoEatScore, answerNoEatScore, questionNotDone đẻ cho gom nhóm các câu hỏi mà user được điểm, không được điểm
                     */
                    $answer_of_user = $data["question"];
                    $question_id = $data["question_id"];
                    $question_socre_user = $data["question_score"]; // dùng  
                    if (count($question_id) > 0) {
                        foreach ($question_id as $key => $question_idItem) {
                            if (!empty($answer_of_user[$question_idItem])) {

                                /* ----------BEGIN CHECK $answer_of_user[$question_idItem] */
                                // Check 1 số kiểu câu hỏi khi mà user không làm mà vẫn submit lên. Check để gán biến thống
                                // kê state_question_after_dogarade: câu đúng, câu sai, câu ko làm
                                $flag_tempQuesItem = false;
                                if (count($answer_of_user[$question_idItem]) > 0) {
                                    foreach ($answer_of_user[$question_idItem] as $tempQuesItem) {
                                        if (!empty($tempQuesItem)) {
                                            $flag_tempQuesItem = true;
                                            break;
                                        }
                                    }
                                }
                                /* ----------END CHECK $answer_of_user[$question_idItem] */

                                // người dùng có thao tác đến question này
                                // với true false,multichoice nếu ko check thì không đẩy lên post
                                // đối với matching,completion,short ans,essay test luôn luôn đẩy lên post
                                if ($flag_tempQuesItem) {
                                    if ($question_socre_user[$key] > 0)
                                        $data["state_question_after_dogarade"][$key] = "answerEatScore";
                                    else
                                        $data["state_question_after_dogarade"][$key] = "answerNoEatScore";
                                }
                                else {
                                    $data["state_question_after_dogarade"][$key] = "questionNotDone";
                                }
                            } else {
                                $data["state_question_after_dogarade"][$key] = "questionNotDone";
                            }
                        }
                    }

                    /*
                     * END xử lý thêm các thẻ answerNoEatScore, answerNoEatScore, questionNotDone đẻ cho gom nhóm các câu hỏi mà user được điểm, không được điểm
                     */

                    $tesid = $modelsHisTest->getTest_id();
                    $data['test_id'] = $tesid;
                    $modelTest = new Default_Models_Test();
                    $modelTest->find("id", $tesid);
                    $data['obj_test'] = null;
                    if ($modelTest->getId()) {
                        $data['obj_test'] = $modelTest;
                        $question_perpage = $modelTest->getQuestion_perpage();
                        $data["list_score_in_test_table"] = Zend_Json::decode($modelsHisTest->getList_score_table_test());
                        $question_perpage = $modelTest->getQuestion_perpage();
                        // Lấy các lựa chọn xem lại bài thi khi đề thi đóng và khi đề thi mở
                        if ($modelTest->getHidden() == "on")
                            $OPTION_REVIEW_TEST = $modelTest->getReview_while_test_open();
                        else
                            $OPTION_REVIEW_TEST = $modelTest->getReview_after_test_close();
                        if (strlen($OPTION_REVIEW_TEST) != 4)
                            $OPTION_REVIEW_TEST = "0000";
                        $OPTION_REVIEW_TEST = str_split($OPTION_REVIEW_TEST);
                        $this->view->review_test = 1;
                        $this->view->view_corect = $OPTION_REVIEW_TEST[0];
                        $this->view->view_feedback = $OPTION_REVIEW_TEST[1];
                        $this->view->view_send_result = 0;
                    }else {
                        $question_perpage = 10;
                        $this->view->review_test = 1;
                        $this->view->view_corect = 0;
                        $this->view->view_feedback = 0;
                        $this->view->view_send_result = 0;
                    }
                    // điểm làm bài của user

                    $data["question_perpage"] = $question_perpage; // gắn tạm	
                    // Begin lấy thông tin user làm bài
                    $auth = Zend_Auth::getInstance();
                    if ($auth->hasIdentity()) {
                        $userhaslogin = $auth->getStorage()->read();
                        $modelsUser = new Default_Models_User();
                        $modelsUser->find("id", $userhaslogin->id);
                        if ($modelsUser->getId())
                            $this->view->UserObj = $modelsUser->toArray();
                    }
                    // End lấy thông tin user làm bài

                    $recommendationModels = new Default_Models_Recommendation();

                    $where = 'test_id = ' . $data['id'] . ' and user_id=' . $userhaslogin->id;
                    $listLessonID = $recommendationModels->fetchAll($where);
                    if (count($listLessonID) > 0) {
                        foreach ($listLessonID as $topLessons) {
                            $listID = Zend_Json::decode($topLessons->toArray()['list_recommendations']);
                            break;
                        }

                        $subjectChapterModel = new Default_Models_ChapterSubject();
                        $listLessons = array();
                        foreach ($listID as $id) {
                            $subjectChapterModel->find("id", $id);
                            $listLessons[] = $subjectChapterModel->toArray();
                        }
                        $data['arrSubjects'] = $listLessons;
                    } else {
                        $data['arrSubjects'] = array();
                    }

                    $this->view->data = $data;
                }
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function systemsuggesttestAction() {
        $this->_helper->layout->setLayout('studentpractice');
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/test.do.js');
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/test/test.js');
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $userhaslogin = $auth->getStorage()->read();
        }

        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
                $data["timeEndCalculate"] = date("U");
                $this->view->review_test = 0;
                $this->view->view_corect = 1;
                $this->view->view_feedback = 0;
                $this->view->view_send_result = 0;

                $question_id = $data["question_id"]; // array
                $answer_of_user = isset($data["question"]) ? $data["question"] : array();    // array
                $total_score = 0;
                if (count($question_id) > 0) {
                    foreach ($question_id as $key => $question_idItem) {
                        if (!empty($answer_of_user[$question_idItem])) {

                            /* ----------BEGIN CHECK $answer_of_user[$question_idItem] */
                            // Check 1 số kiểu câu hỏi khi mà user không làm mà vẫn submit lên. Check để gán biến thống
                            // kê state_question_after_dogarade: câu đúng, câu sai, câu ko làm
                            $flag_tempQuesItem = false;
                            if (count($answer_of_user[$question_idItem]) > 0) {
                                foreach ($answer_of_user[$question_idItem] as $tempQuesItem) {
                                    if (!empty($tempQuesItem)) {
                                        $flag_tempQuesItem = true;
                                        break;
                                    }
                                }
                            }
                            /* ----------END CHECK $answer_of_user[$question_idItem] */

                            // người dùng có thao tác đến question này
                            // với true false,multichoice nếu ko check thì không đẩy lên post
                            // đối với matching,completion,short ans,essay test luôn luôn đẩy lên post
                            if ($flag_tempQuesItem) {
                                $obj = DoGrade::_DoGrade(null, $question_idItem, $answer_of_user[$question_idItem]);
                                $total_score+= $obj->num_score;
                                $data["question_score"][$key] = $obj->num_score;
                                if ($obj->num_score > 0) {
                                    $this->_updateStatisticAdvisory($question_idItem, "_true");
                                    $data["state_question_after_dogarade"][$key] = "answerEatScore";
                                } else {
                                    $this->_updateStatisticAdvisory($question_idItem, "_false");
                                    $data["state_question_after_dogarade"][$key] = "answerNoEatScore";
                                }
                            } else {
                                $data["question_score"][$key] = 0;
                                $data["state_question_after_dogarade"][$key] = "questionNotDone";
                            }
                        } else {
                            $data["question_score"][$key] = 0;
                            $data["state_question_after_dogarade"][$key] = "questionNotDone";
                        }
                    }
                }
                $data["total_score"] = $total_score;
                $data["question_perpage"] = $data["question_perpage"];
                $data["totalTimeDoPractice"] = Zend_View_Helper_FormatDate::timeBetween($data["timeStartCalculate"], $data["timeEndCalculate"]);


                $auth = Zend_Auth::getInstance();
                if ($auth->hasIdentity()) {
                    $userhaslogin = $auth->getStorage()->read();
                }

                $question = $data['question_id'];
                $answer = $data['question_score'];
                $user_id = $userhaslogin->id;
                $date = $data['timeEndCalculate'];


                for ($i = 0; $i < count($question); $i++) {
                    $rating = new Default_Models_Rating();
                    $rating->setUserID($user_id);
                    $rating->setDate($date);
                    $rating->setQuestionID($question[$i]);
                    $rating->setResult($answer[$i]);
                    $rating->save();
                }



                $this->view->data = $data;
                $this->view->result_practice = "on";
            } elseif ($request->isGet()) {
                $data = $request->getParams();
                if (empty($data['class_id']))
                    throw new Exception("Đường dẫn không hợp lệ.");
                $modelsClass = new Default_Models_Classs();
                $modelsClass->find("id", $data['class_id']);
                $tempID = $modelsClass->getId();
                if (empty($tempID))
                    throw new Exception("Không tồn tại lớp học.");

                $amountQues = trim($data['sys_suggest_amount_question']);
                $duration = trim($data['sys_suggest_duration_test']);
                $item_per_page = trim($data['sys_suggest_question_perpage']);
                $amountQues = intval($amountQues);
                $duration = intval($duration);
                $item_per_page = intval($item_per_page);

                if (!is_numeric($amountQues) || $amountQues == 0)
                    $amountQues = 10;
                if ($amountQues > 150)
                    $amountQues = 100;
                if (!is_numeric($duration) || $duration == 0)
                    $duration = 10;
                if (!is_numeric($item_per_page) || $item_per_page == 0)
                    $item_per_page = 10;
                /*
                 * PHÁT SINH 50% CÂU HỎI MÀ SINH VIÊN YẾU, 50% CÒN LẠI RANDOM TRONG NHỮNG CHƯƠNG MÀ SINH VIÊN LÀM
                 */

                $recommend = new Recommend();

                $ListQuestionIdFinal = $recommend->getRecommendation($modelsClass->getId(), $userhaslogin->id, $data['level'], $data["sys_suggest_amount_question"]);
                $this->view->view_corect = 0;
                $this->view->view_feedback = 0;
                $this->view->view_send_result = 0;
                $data["question_score"] = array();
                $data["question_id"] = $ListQuestionIdFinal;
                $data["question_perpage"] = $item_per_page;
                $data["duration_test"] = $duration;
                $data["timeStartCalculate"] = date("U");
                $this->view->data = $data;
                $this->view->hasClock = true;
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    /*
     * Hàm này là hàm khuyến nghị nội dung theo chủ đề
     */

    public function systemrandomtestAction() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $userhaslogin = $auth->getStorage()->read();
        }
        $this->_helper->layout->setLayout('studentpractice');
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/test.do.js');
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/test/test.js');

        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $data = $request->getParams();
                $data["timeEndCalculate"] = date("U");
                $this->view->review_test = 0;
                $this->view->view_corect = 1;
                $this->view->view_feedback = 0;
                $this->view->view_send_result = 0;

                $question_id = $data["question_id"]; // array
                $answer_of_user = $data["question"];    // array
                $total_score = 0;
                if (count($question_id) > 0) {
                    foreach ($question_id as $key => $question_idItem) {
                        if (!empty($answer_of_user[$question_idItem])) {

                            /* ----------BEGIN CHECK $answer_of_user[$question_idItem] */
                            // Check 1 số kiểu câu hỏi khi mà user không làm mà vẫn submit lên. Check để gán biến thống
                            // kê state_question_after_dogarade: câu đúng, câu sai, câu ko làm
                            $flag_tempQuesItem = false;
                            if (count($answer_of_user[$question_idItem]) > 0) {
                                foreach ($answer_of_user[$question_idItem] as $tempQuesItem) {
                                    if (!empty($tempQuesItem)) {
                                        $flag_tempQuesItem = true;
                                        break;
                                    }
                                }
                            }
                            /* ----------END CHECK $answer_of_user[$question_idItem] */

                            // người dùng có thao tác đến question này
                            // với true false,multichoice nếu ko check thì không đẩy lên post
                            // đối với matching,completion,short ans,essay test luôn luôn đẩy lên post
                            if ($flag_tempQuesItem) {
                                $obj = DoGrade::_DoGrade(null, $question_idItem, $answer_of_user[$question_idItem]);
                                $total_score+= $obj->num_score;
                                $data["question_score"][$key] = $obj->num_score;
                                if ($obj->num_score > 0) {
                                    $this->_updateStatisticAdvisory($question_idItem, "_true");
                                    $data["state_question_after_dogarade"][$key] = "answerEatScore";
                                } else {
                                    $this->_updateStatisticAdvisory($question_idItem, "_false");
                                    $data["state_question_after_dogarade"][$key] = "answerNoEatScore";
                                }
                            } else {
                                $data["question_score"][$key] = 0;
                                $data["state_question_after_dogarade"][$key] = "questionNotDone";
                            }
                        } else {
                            $data["question_score"][$key] = 0;
                            $data["state_question_after_dogarade"][$key] = "questionNotDone";
                        }
                    }
                }
                $data["total_score"] = $total_score;
                $data["question_perpage"] = $data["question_perpage"];
                $data["totalTimeDoPractice"] = Zend_View_Helper_FormatDate::timeBetween($data["timeStartCalculate"], $data["timeEndCalculate"]);



                $question = $data['question_id'];
                $answer = $data['question_score'];
                $user_id = $userhaslogin->id;
                $date = $data['timeEndCalculate'];


                for ($i = 0; $i < count($question); $i++) {
                    $rating = new Default_Models_Rating();
                    $rating->setUserID($user_id);
                    $rating->setDate($date);
                    $rating->setQuestionID($question[$i]);
                    $rating->setResult($answer[$i]);
                    $rating->save();
                }


                $this->view->data = $data;
                $this->view->result_practice = "on";
            } elseif ($request->isGet()) {
                $data = $request->getParams();

                $amountQues = trim($data['sys_random_amount']);
                $duration = trim($data['sys_random_duration']);
                $item_per_page = isset($data['sys_random_itemperpage']) ? trim($data['sys_random_itemperpage']) : 10;
                $amountQues = intval($amountQues);
                $duration = intval($duration);
                $item_per_page = intval($item_per_page);

                if (!is_numeric($amountQues) || $amountQues == 0)
                    $amountQues = 10;
                if ($amountQues > 150)
                    $amountQues = 100;
                if (!is_numeric($duration) || $duration == 0)
                    $duration = 10;
                if (!is_numeric($item_per_page) || $item_per_page == 0)
                    $item_per_page = 10;

                // Chỉ truy vấn lấy id của question chứ ko lấy tất cả các columm theo kiểu fetchAll
                $models_question = new Default_Models_Question();
                $result_row = $models_question->fetchAllColumn("id"); // trả về 1 mảng id question luôn
                $total = count($result_row);


                $recommend = new Recommend();

                $list_id_question = $recommend->getRecommendationWithSingleChapter($userhaslogin->id, $data['chapterid'], $data['level'], $data["sys_random_amount"]);

                $this->view->view_corect = 0;
                $this->view->view_feedback = 0;
                $this->view->view_send_result = 0;
                $data["question_score"] = array();
                $data["question_id"] = $list_id_question;
                $data["question_perpage"] = $item_per_page;
                $data["duration_test"] = $duration;
                $data["timeStartCalculate"] = date("U");
                $this->view->data = $data;
                $this->view->hasClock = true;
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function viewnewsAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                
            } elseif ($request->isGet()) {
                $data = $request->getParams();
                $modelNews = new Default_Models_News();
                $modelNews->find("id", $data['id']);
                if ($modelNews->getId()) {
                    $this->view->news = $modelNews->toArray();
                    $modelCategory = new Default_Models_Category();
                    $modelCategory->find("id", $modelNews->getCategory_id());

                    if ($modelCategory->getId())
                        $this->view->category = $modelCategory->toArray();
                    $modelUser = new Default_Models_User();
                    $modelUser->find("id", $modelNews->getUser_create());
                    $this->view->user_create = $modelUser->getFirstname() . " " . $modelUser->getLastname();
                }
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function studentredotestagainAction() {
        $this->_helper->layout->setLayout('studentdotest');
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/test.do.js');
        $this->view->jQuery()->addJavascriptFile(BASE_URL . '/js/controller/test/test.js');
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                throw new Exception("Không tồn tại đề thi này");
            } elseif ($request->isGet()) {
                $data = $request->getParams();
                $histestid = $data["his_test_id"];
                $this->view->hasClock = false;
                if (empty($histestid))
                    throw new Exception("Không tồn tại id bài làm của đề thi này");

                $this->view->review_test = 0;
                $this->view->view_corect = 0;
                $this->view->view_feedback = 0;
                $this->view->view_send_result = 0;
                $modelTest = new Default_Models_Test();
                $modelHistoryUserTest = new Default_Models_Historyofusertest();
                $modelHistoryUserTest->find("id", $histestid);
                $modelTest->find("id", $modelHistoryUserTest->getTest_id());
                $idHisTest = $modelHistoryUserTest->getId();
                if (empty($idHisTest))
                    throw new Exception("Không tồn tại đề thi này");
                $idtest = $modelTest->getId();
                if (empty($idtest))
                    throw new Exception("Không tồn tại đề thi này");
                // Begin lấy thông tin user làm bài
                $auth = Zend_Auth::getInstance();
                if ($auth->hasIdentity()) {
                    $userhaslogin = $auth->getStorage()->read();
                    $modelsUser = new Default_Models_User();
                    $modelsUser->find("id", $userhaslogin->id);
                    if ($modelsUser->getId())
                        $this->view->UserObj = $modelsUser->toArray();
                }
                // End lấy thông tin user làm bài					
                if ($modelHistoryUserTest->getUser_id() != $userhaslogin->id)
                    throw new Exception("Không tồn tại đề thi này");


                /*
                 * BeGin kiểm tra nếu bấm back lại mà hết giờ thì thông báo hết giờ, ko thì làm tiếp thời gian còn lại.... END
                 */
                $timeBeginTest = $modelHistoryUserTest->getTime_start();
                $durationTest = $modelTest->getDuration_test();
                // Convert thời gian làm bài thi ra giây, vì Duration_test là phút 		
                $durationTest = $durationTest * 60;
                $timeEndTest = ($timeBeginTest * 1) + ($durationTest * 1);
                if ($timeEndTest < date("U")) {
                    throw new Exception("Hết thời gian làm bài...");
                }
                /*
                 * END kiểm tra nếu bấm back lại mà hết giờ thì thông báo hết giờ, ko thì làm tiếp thời gian còn lại.... END
                 */

                $data['test_id'] = $idtest;
                $data['test_obj'] = $modelTest->toArray();

                //$data["question_score"] 			= Zend_Json::decode($modelHistoryUserTest->getList_score_of_question());
                $data["question_id"] = Zend_Json::decode($modelHistoryUserTest->getList_question_id()); // array
                $data["list_score_in_test_table"] = Zend_Json::decode($modelHistoryUserTest->getList_score_table_test());
                $data["question"] = Zend_Json::decode($modelHistoryUserTest->getList_answer_of_user());    // array
                // Lưu list score của câu hỏi vào 1 mảng tạm để phục vụ chấm điểm
                $modelScoreTemp = new Default_Models_Scoretemp();
                $modelScoreTemp->setList_score_value(Zend_Json::encode($data["list_score_in_test_table"]));
                $last_insert_id_ScoreTemp = $modelScoreTemp->save();
                $this->view->scoretemp = $last_insert_id_ScoreTemp;

                $data["question_perpage"] = $modelTest->getQuestion_perpage();
                //$data["question_score"] = $data["list_score_in_test_table"];
                $data["last_insert_history_test_id"] = $idHisTest;
                $this->view->data = $data;
                $this->view->hasClock = true;
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function stopdotestAction() {
        try {
            $request = $this->getRequest();
            if ($request->isPost()) {
                
            } elseif ($request->isGet()) {
                
            }
        } catch (Exception $ex) {
            $this->_arrError[] = $ex->getMessage();
            $this->view->arrError = $this->_arrError;
        }
    }

    public function _filterTest($data) {
        $data['test_id'] = Zend_Filter::filterStatic($data['test_id'], 'StringTrim');
        $data['exam_id'] = Zend_Filter::filterStatic($data['exam_id'], 'StringTrim');
        $data['class_id'] = Zend_Filter::filterStatic($data['class_id'], 'StringTrim');
        $data['shedule_exam'] = Zend_Filter::filterStatic($data['shedule_exam'], 'StringTrim');

        //$data['test_id'] =  BASIC_String::Remove_Magic_Quote($data['test_id']);
        //$data['exam_id'] =  BASIC_String::Remove_Magic_Quote($data['exam_id']);
        //$data['class_id'] = BASIC_String::Remove_Magic_Quote($data['class_id']);
        // Chưa làm được BASIC_String::Remove_Magic_Quote   

        return $data;
    }

    public function _validate($data, $update = false) {
        $this->_arrError = Array();
        // CHECK MỘT SỐ TRƯỜNG NHẬP TRỐNG 
        if (empty($data['firstname']))
            $this->_arrError[] = "Tên của user trống.";
        if (empty($data['lastname']))
            $this->_arrError[] = "Tên của user trống.";
        if (!Zend_Validate::is($data['email'], 'EmailAddress'))
            $this->_arrError[] = "Email không hợp lệ.";
        /*
          if(empty($data['time_start'])) 				$this->_arrError[] = "Thời gian bắt đầu lớp học trống.";
          if(empty($data['time_end'])) 				$this->_arrError[] = "Thời gian kết thúc lớp học trống.";
         */

        if ($update) {// is update 
            if (empty($data['id']))
                $this->_arrError[] = "ID user không tồn tại.";
        }else {//is insert 
        }
    }

    private function _filter($data) {

        $data['username'] = Zend_Filter::filterStatic($data['username'], 'StringTrim');
        $data['password'] = Zend_Filter::filterStatic($data['password'], 'StringTrim');
        $data['firstname'] = Zend_Filter::filterStatic($data['firstname'], 'StringTrim');
        $data['lastname'] = ZendFilter::filterStatic($data['lastname'], 'StringTrim');
        // for filter
        $filter = new Zend_Filter_StripTags();
        $data['lastname'] = $filter->filter($data['lastname']);
        $data['firstname'] = $filter->filter($data['firstname']);
        $data['username'] = $filter->filter($data['username']);
        return $data;
    }

    private function _filter_edit_info($data) {
        if (isset($data['username'])) {
            $data['username'] = Zend_Filter::filterStatic($data['username'], 'StringTrim');
            $data['password'] = Zend_Filter::filterStatic($data['password'], 'StringTrim');
            $data['username'] = $filter->filter($data['username']);
            $data['firstname'] = $filter->filter($data['firstname']);
        }
        $data['firstname'] = Zend_Filter::filterStatic($data['firstname'], 'StringTrim');
        $data['lastname'] = Zend_Filter::filterStatic($data['lastname'], 'StringTrim');
        $data['department'] = Zend_Filter::filterStatic($data['department'], 'StringTrim');
        $data['email'] = Zend_Filter::filterStatic($data['email'], 'StringTrim');
        $data['yahoo'] = Zend_Filter::filterStatic($data['yahoo'], 'StringTrim');
        $data['skyper'] = Zend_Filter::filterStatic($data['skyper'], 'StringTrim');
        $data['address'] = Zend_Filter::filterStatic($data['address'], 'StringTrim');
        $data['city'] = Zend_Filter::filterStatic($data['city'], 'StringTrim');

        // for filter
        $filter = new Zend_Filter_StripTags();

        $data['lastname'] = $filter->filter($data['lastname']);
        $data['department'] = $filter->filter($data['department']);
        $data['email'] = $filter->filter($data['email']);
        $data['yahoo'] = $filter->filter($data['yahoo']);
        $data['skyper'] = $filter->filter($data['skyper']);
        $data['address'] = $filter->filter($data['address']);
        $data['city'] = $filter->filter($data['city']);

        return $data;
    }

    // Cập nhật dữ liệu tư vấn khi sinh viên thực hành
    public function _updateStatisticAdvisory($quesion_id, $_true_or_false) {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $userhaslogin = $auth->getStorage()->read();
            $modelQuestion = new Default_Models_Question();
            $modelQuestion->find("id", $quesion_id);
            if ($modelQuestion->getId()) {
                $modelStatisticAdvisory = new Default_Models_StatisticAdvisory();
                $result = $modelStatisticAdvisory->fetchAll("`user_id`='" . $userhaslogin->id . "' AND `question_id`='" . $quesion_id . "'");
                if (count($result) > 0) { // Cập nhật
                    $modelStatisticAdvisory->find("id", $result[0]->getId());
                    // Nếu làm đúng thì cập nhật tăng đúng lên 1 và tính lại % lần làm sai chia cho tổng số lần làm 
                    if ($_true_or_false == "_true") {
                        $update = ($modelStatisticAdvisory->getAmountTrue() + 1);
                        $totalDoQuestion = $modelStatisticAdvisory->getAmountFalse() + $update;
                        $falseDivisionTotalDoQues = round((float) $modelStatisticAdvisory->getAmountFalse() / $totalDoQuestion, 2);
                        $modelStatisticAdvisory->setTrue_false($falseDivisionTotalDoQues);
                        $modelStatisticAdvisory->setAmountTrue($update);
                        $modelStatisticAdvisory->save();
                    }
                    // Nếu làm sai thì cập nhật tăng sai lên 1 và tính lại % lần làm sai chia cho tổng số lần làm					
                    if ($_true_or_false == "_false") {
                        $update = ($modelStatisticAdvisory->getAmountFalse() + 1);
                        $totalDoQuestion = $modelStatisticAdvisory->getAmountTrue() + $update;
                        $falseDivisionTotalDoQues = round((float) $update / $totalDoQuestion, 2);
                        $modelStatisticAdvisory->setTrue_false($falseDivisionTotalDoQues);
                        $modelStatisticAdvisory->setAmountFalse($update);
                        $modelStatisticAdvisory->save();
                    }
                } else { // thêm mới
                    $modelStatisticAdvisory = new Default_Models_StatisticAdvisory();
                    $modelStatisticAdvisory->setUser_id($userhaslogin->id);
                    $modelStatisticAdvisory->setSubject_id($modelQuestion->getSubject_id());
                    $modelStatisticAdvisory->setChapter_id($modelQuestion->getChapter_id());
                    $modelStatisticAdvisory->setQuestion_id($quesion_id);
                    if ($_true_or_false == "_true") {
                        $modelStatisticAdvisory->setAmountTrue(1);
                        $modelStatisticAdvisory->setAmountFalse(0);
                        $modelStatisticAdvisory->setTrue_false(0);
                    }
                    if ($_true_or_false == "_false") {
                        $modelStatisticAdvisory->setAmountTrue(0);
                        $modelStatisticAdvisory->setAmountFalse(1);
                        $modelStatisticAdvisory->setTrue_false(1);
                    }
                    $modelStatisticAdvisory->save();
                }
            }
        }
    }

    // Cập nhật độ khó 1 câu hỏi khi sinh viên làm bài thi 
    public function _updateStatisticQuestion($quesion_id, $_true_or_false) {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $userhaslogin = $auth->getStorage()->read();
            $modelQuestion = new Default_Models_Question();
            $modelQuestion->find("id", $quesion_id);
            if ($modelQuestion->getId()) {
                $modelStatisticQuestion = new Default_Models_StatisticQuestion();
                $modelStatisticQuestion->find("question_id", $quesion_id);
                if ($modelStatisticQuestion->getId()) { // Cập nhật
                    // Nếu làm đúng thì cập nhật tăng đúng lên 1 và tính lại tổng số lần làm. Rồi cập nhật độ khó 
                    if ($_true_or_false == "_true") {
                        $amountQuesTrueUpdate = ($modelStatisticQuestion->getAmountTrue() + 1);
                        $modelStatisticQuestion->setAmountTrue($amountQuesTrueUpdate);
                    }
                    // Nếu làm sai thì chỉ cập nhật tăng tổng số lần làm lên				
                    $totalDoQuestion = ( $modelStatisticQuestion->getTotal_done() + 1);
                    $modelStatisticQuestion->setTotal_done($totalDoQuestion);
                    $modelStatisticQuestion->save();
                } else { // thêm mới
                    $modelStatisticQuestion->setQuestion_id($quesion_id);
                    if ($_true_or_false == "_true") {
                        $modelStatisticQuestion->setAmountTrue(1);
                    }else
                        $modelStatisticQuestion->setAmountTrue(0);
                    $modelStatisticQuestion->setTotal_done(1);
                    $modelStatisticQuestion->save();
                }
                $modelStatisticQuestion = new Default_Models_StatisticQuestion();
                $modelStatisticQuestion->find("question_id", $quesion_id);
                $amountQuesTrue = $modelStatisticQuestion->getAmountTrue();
                if (empty($amountQuesTrue))
                    $amountQuesTrue = 0;
                $modelQuestion->setLevel(round($amountQuesTrue / $modelStatisticQuestion->getTotal_done(), 2));
                $modelQuestion->save();
            }
        }
    }

    public function testAction() {
        echo "This_is_TestAction";
        $q = new Default_Models_QuestionVector();
        $result = $q->fetchAll();
        $arrSubjects = array();
        foreach ($result as $key => $subj) {
            $arrSubjects[$key] = $subj->toArray();
        }
        print_r($arrSubjects);
        die;
    }

}