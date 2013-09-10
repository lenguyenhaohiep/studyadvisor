<?php
class Zend_View_Helper_CountDownTimer
{
	public function countDownTimer($duration,$historyTestId=null)
	{
		if(!empty($historyTestId)){
			// chỗ này để xử lý chống f5 của user
			$modelHisTest = new Default_Models_Historyofusertest();
			$modelHisTest->find("id", $historyTestId);
			if($modelHisTest->getId())
				$now = $modelHisTest->getTime_start();		
		}else
				$now = date('U');
		$endtime = ($now + $duration * 60)*1000; 		
		$html='';
$html.='<div class="time-remain ui-widget-content">';
$html.='<input type="hidden" id="time-end"	value="'.$endtime.'" />'; 
	$html.='<input type="hidden" id="server-time" value="'.BASE_URL.'/index/time/duration/'.$duration.'">';
	$html.='<div id="div-countdown" >';
	$html.='<table>';
		$html.='<tr>';
			$html.='<td colspan="2" class="ui-state-focus" style="text-align: center; border-bottom: 1px solid #D6DDE6;"><b>Thời gian còn<br /></b>';
			$html.='</td>';
		$html.='</tr>';
		$html.='<tr>';
				$html.='<td style="vertical-align: middle; padding: 0px; padding-top: 5px; padding-right: 2px;">';
				$html.='<img src="'.BASE_URL.'/img/icons/space.gif" alt="Đồng hồ đếm giờ" class="fugue-alarm-clock" />';
				$html.='</td>';
			$html.='<td style="vertical-align: middle; padding: 0px;">';
				$html.='<span id="time-countdown" style="border:0px; padding:5px;">00:00:00</span>';
			$html.='</td>';
		$html.='</tr>';
		$html.='<tr>';
			$html.='<td colspan="2"';
				$html.='style="text-align: center; border-top: 1px solid #D6DDE6;">';
			$html.='<button class="finish-test">Nộp Bài</button>';
			$html.='</td>';
		$html.='</tr>';
	$html.='</table>';
	$html.='</div>';
$html.='</div>';
	return $html;		
	}
}