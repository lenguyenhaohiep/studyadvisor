<?php
	class Lib_Arr{
		// Lớp này dùng để sắp xếp nhiều mảng theo một mảng 
		// So sánh trong mảng $a rồi sắp xếp tất cả theo mảng a
		public static function my_array_sort($a,$b=null,$c=null,$d=null,$e=null){
			if(count($a)>0){
				for($i=0;$i<count($a)-1;$i++)
				  for($j=$i+1;$j<count($a);$j++){
				  	if($a[$i]>$a[$j]){
				  		$tmp   = $a[$i];
				  		$a[$i] = $a[$j];
				  		$a[$j] = $tmp;
				  		
				  		$tmp   = $b[$i];
				  		$b[$i] = $b[$j];
				  		$b[$j] = $tmp;

				  		$tmp   = $c[$i];
				  		$c[$i] = $c[$j];
				  		$c[$j] = $tmp;

				  		$tmp   = $d[$i];
				  		$d[$i] = $d[$j];
				  		$d[$j] = $tmp;

				  		$tmp   = $e[$i];
				  		$e[$i] = $e[$j];
				  		$e[$j] = $tmp;
				  	}
				  }
			  		$result = array("0"=>$a,"1"=>$b,"2"=>$c,"3"=>$d,"4"=>$e);
			  		return $result;				  		
				  
			}else return 0;
		}
		
		// hàm Dùng để trộn mảng list question id với list score in test table, để khi mà xáo trộn câu trả lời còn 
		// lấy đúng điểm số của từng câu hỏi  
		/*
		 * mang a : ["552","548","568","549","550","564","567","556","551","547","554","555"]
		   mang b :[0,44,"5",7,0,"9","7",0,9,18,0,0]
		   Return array [548]=>  string(2) "44" [568]=>  string(1) "5"]
				
		 */
		public static function syn_2array_to_one($a,$b){
			if(count($a)>0){
				$arrReturn = array();
				foreach($a as $key=>$aItem){
					$arrReturn[$aItem] = $b[$key];
				}
			}
			return $arrReturn;
		}
		
		// Hàm để cập nhật lại điểm hiển thị cho sinh viên thi sau khi xáo trộn câu hỏi
		/*
		 * Mang a: [0=>"552",1=>"548",2=>"568",3=>"549"] 
		 * Mang b: array(12) { [568]=>  string(2) "25" [567]=>  string(2) "44" [564]=>  string(1) "5" [556]=>  string(1) "7" [555]=>  string(1) "8" [554]=>  string(1) "9" [552]=>  string(1) "7" [551]=>  string(1) "9" [550]=>  string(1) "9" [549]=>  string(2) "18" [548]=>  string(2) "87" [547]=>  string(2) "34" } 
		 */ 
			public static function update_score_new_listQuestion($a,$b){
				if(count($a)>0){
					$arrScoreReturn = array();
					foreach($a as $key_a=>$aItem){
						foreach ($b as $key_b=>$bItem){
							if($aItem==$key_b)
								$arrScoreReturn[$key_a]=$bItem;
						}
					}
				}
				return $arrScoreReturn;
			}
	}
 ?>