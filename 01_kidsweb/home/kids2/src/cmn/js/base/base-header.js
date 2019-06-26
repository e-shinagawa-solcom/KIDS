
// ����(���߻���)��ɽ��������
$(function(){

	aryWeek = new Array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );

	// 1����˸��߻����ɽ��/��������
	setInterval(function(){

		objDate = new Date();
		var yy1 = objDate.getYear();
		var yy2 = ( yy1 < 2000 ) ? yy1+1900 : yy1;
		var mm  = objDate.getMonth() + 1;
		var dd  = objDate.getDate();
		var num = objDate.getDay();

		if( mm < 10 ) { mm = '0' + mm; }
		if( dd < 10 ) { dd = '0' + dd; }

		var h = objDate.getHours();
		var m = objDate.getMinutes();
		var s = objDate.getSeconds();

		if( h < 10 ) { h = '0' + h; }
		if( m < 10 ) { m = '0' + m; }
		if( s < 10 ) { s = '0' + s; }

		var date   = yy2 + '/' + mm + '/' + dd;
		var week   = aryWeek[num];
		var time   = h + ':' + m +':' + s;

		var dwt    = date + ' ' + week + ' ' + time;

		$('.Login-information__clock').text('DATE : ' + dwt);
	}, 1000)
})






// �ۥ���URL��ɽ������
// defer���ɤ߹���Τǥȥꥬ���ʤ��Ǽ¹Ԥ�����
$('.Login-information__host').text(
	'HOST : '+ location.protocol + '//' + location.hostname
)
