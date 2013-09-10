$(document).ready(function() {
    var endtime = new Date();
    endtime.setTime($('#time-end').val());
    $("#time-countdown").countdown({
        until: endtime,
        serverSync: serverTime,
        compact: true,
        format: 'HMS',
        alwaysExpire: true,
        onExpiry: endTime
    });
 
    $('.finish-test').click(function() {
        if (confirm('Bạn có chắc chắn muốn nộp bài?') == true){
        	$(this).attr('disabled', 'disabled');
            $(this).hide();
            endTest();
        }
    });
    $("#time-countdown").countdown('pause');        
 });

 function serverTime() {
    var time = null;
    var serverurl = $('#server-time').val();
    $.ajax({
    	url: serverurl,
        async: false, 
        dataType: 'text',
        success: function(text) {
            time = new Date(text);
        }, 
        error: function(http, message, exc) {
            time = new Date();
    }});
    return time;
 }
 
function endTime() {
	html = "Thời gian đã hết,hệ thống sẽ tự động nộp bài của bạn.";
	alert(html);
   	endTest();
 }