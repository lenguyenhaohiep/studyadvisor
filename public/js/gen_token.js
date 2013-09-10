/**
 * Trong IE, có cơ chế nhớ những (response) của đường dẫn đã request rồi
 * nếu ta cứ gọi ajax đến 1 địa chỉ cố định sẽ dẫn đến việc nó tự động response 
 * lại các giá trị đã có, thực chất nó chẳng gửi đi cái request nào cả
 * mà khi được yêu cầu cần phải response lại các request nó sẽ tìm kiếm trong 
 * bộ nhớ có cái response nào ứng với cái request đó ko, nếu có rồi nó tự động trả lại
 * DKM nó chó thật.Tuy nhiên nó dựa vào url mà ta request mà đưa ra response tương ứng
 * thế nên có 1 cách khắc phục đó là tao thay đổi thường xuyên địa chỉ url bằng
 * cách thêm 1 tham số giả dạng vào cuối mỗi request, tham số này chẳng làm djt j cả hết
 * hàm fnGenToken dùng để sinh ra cái token để thêm vào mỗi lần gọi ajax
 * ta sẽ dùng thời gian bằng cách nối year+month+day+hour+minute+second+milisecond vào   
 */
function fnGenToken(){
	var currentTime = new Date();
	var year 		= currentTime.getYear()+"";
	var month 		= currentTime.getMonth()+"";
	var day 		= currentTime.getDay()+"";
	var hours 		= currentTime.getHours()+"";
	var minutes		= currentTime.getMinutes()+"";
	var seconds		= currentTime.getSeconds()+"";
	var milliSeconds  = currentTime.getMilliseconds()+"";
	return year + month + day + hours + minutes + seconds + milliSeconds;
	
}