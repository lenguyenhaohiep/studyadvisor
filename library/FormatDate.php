<?php
class Zend_View_Helper_FormatDate
{
	
	public  static  function convertYmdToMdy($date){
		try{
			if (!empty($date)){
				$date_format = new DateTime($date);
				return $date_format->format("d-m-Y H:i:s");
				// "F-Y H:i:s"
			}
			return $date;
		}catch(Exception $ex){
			throw new Exception("Lỗi định dạng ngày tháng khi chuyển YmdToMdy.");
		}
	}
	
	public  static  function convertMdyToYmd($date){
		try{
			if (!empty($date)){		
				$date_format = new DateTime($date);
				return $date_format->format("Y-m-d H:i:s");
			}
			return $date;
		}catch (Exception  $ex){
			throw new Exception("Lỗi định dạng ngày tháng chuyển MdyToYmd.");
		}
	}
	
	public  static  function convertDmyToYmd($date){
		try{
			if (!empty($date)){		
				$date_format = new DateTime($date);
				return $date_format->format("Y-m-d H:i:s");
			}
			return $date;
		}catch (Exception  $ex){
			throw new Exception("Lỗi định dạng ngày tháng chuyển DmyToYmd.");
		}
	}
	
	public  static  function convertYmdToDmy($date){
		if (!empty($date)){
			$date_format = new DateTime($date);
			return $date_format->format("d-m-Y H:i:s");
			//("dd-MM-yyyy"); "F-Y H:i:s"
		}
		return $date;
	}	
	
	public  static  function convertYmdToMdyJustDate($date){
		try{
			if (!empty($date)){
				$date_format = new DateTime($date);
				return $date_format->format("d-m-Y");
				//("dd-MM-yyyy"); "F-Y H:i:s"
			}
			return $date;
		}catch(Exception $ex){
			throw new Exception("Lỗi định dạng ngày tháng khi chuyển YmdToMdyJustDate.");
		}
	}
	
	/*
	 * $start_date format date("U")
	 * $end_date format date("U")
	 */
     public static  function timeBetween($start_date,$end_date)  
     {  
           $diff = $end_date-$start_date;  
           $seconds = 0;  
           $hours   = 0;  
           $minutes = 0;  
     
           if($diff % 86400 <= 0){$days = $diff / 86400;}  // 86,400 seconds in a day  
           if($diff % 86400 > 0)  
           {  
               $rest = ($diff % 86400);  
               $days = ($diff - $rest) / 86400;  
               if($rest % 3600 > 0)  
               {  
                   $rest1 = ($rest % 3600);  
                   $hours = ($rest - $rest1) / 3600;  
                   if($rest1 % 60 > 0)  
                   {  
                       $rest2 = ($rest1 % 60);  
                   $minutes = ($rest1 - $rest2) / 60;  
                   $seconds = $rest2;  
                   }  
                   else{$minutes = $rest1 / 60;}  
               }  
              else{$hours = $rest / 3600;}  
           }  
     
           if($days > 0){$days = $days.' Ngày, ';}  
           else{$days = false;}  
           if($hours > 0){$hours = $hours.' Giờ, ';}  
           else{$hours = false;}  
           if($minutes > 0){$minutes = $minutes.' Phút, ';}  
           else{$minutes = false;}  
           $seconds = $seconds.' Giây'; // always be at least one second  
     
           return $days.''.$hours.''.$minutes.''.$seconds;  
       }	
       
       public static  function timeBetweenNotGetSecond($start_date,$end_date)  
     {  
           $diff = $end_date-$start_date;  
           $seconds = 0;  
           $hours   = 0;  
           $minutes = 0;  
     
           if($diff % 86400 <= 0){$days = $diff / 86400;}  // 86,400 seconds in a day  
           if($diff % 86400 > 0)  
           {  
               $rest = ($diff % 86400);  
               $days = ($diff - $rest) / 86400;  
               if($rest % 3600 > 0)  
               {  
                   $rest1 = ($rest % 3600);  
                   $hours = ($rest - $rest1) / 3600;  
                   if($rest1 % 60 > 0)  
                   {  
                       $rest2 = ($rest1 % 60);  
                   $minutes = ($rest1 - $rest2) / 60;  
                   $seconds = $rest2;  
                   }  
                   else{$minutes = $rest1 / 60;}  
               }  
              else{$hours = $rest / 3600;}  
           }  
     
           if($days > 0){$days = $days.' Ngày, ';}  
           else{$days = false;}  
           if($hours > 0){$hours = $hours.' Giờ, ';}  
           else{$hours = false;}  
           if($minutes > 0){$minutes = $minutes.' Phút, ';}  
           else{$minutes = false;}  
           $seconds = $seconds.' Giây'; // always be at least one second
           if($days > 10)  
           		return $days.''.$hours;
     		if($days > 0)
     			return $days.''.$hours.''.$minutes;
           return $days.''.$hours.''.$minutes.''.$seconds;  
       }	
       
       // Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
     public static  function convertSecondToDateTimeHasHour($amountSeconds)  {
       		if($amountSeconds==0)
       			return "";
     		$date = getdate($amountSeconds);
           return $date['mday'].'-'.$date['mon'].'-'.$date['year'].'  '.$date['hours'].':'.$date['minutes'].':'.$date['seconds'];  
       	
       }
       
	public static function _tinyMce_get_absolute_path_image($strSearch, $strReplace, $_questionHtml){
		return str_replace($strSearch,$strReplace, $_questionHtml);		
	}
       
       
}  