<!--

var now    = new Date();
var absnow = now;



//---------------------------------------------------
// Ŭ�ѡ���NEXT�ץܥ���
//---------------------------------------------------
function fncNextButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = '/img/type01/date/next_off_bt.gif';
			break;

		case 'on':
			obj.src = '/img/type01/date/next_off_on_bt.gif';
			break;

		default:
			break;
	}

	return false;
}

//---------------------------------------------------
// Ŭ�ѡ���PREV�ץܥ���
//---------------------------------------------------
function fncPrevButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = '/img/type01/date/prev_off_bt.gif';
			break;

		case 'on':
			obj.src = '/img/type01/date/prev_off_on_bt.gif';
			break;

		default:
			break;
	}

	return false;
}

//---------------------------------------------------
// Ŭ�ѡ���NOW�ץܥ���
//---------------------------------------------------
function fncNowButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = '/img/type01/date/now_off_bt.gif';
			break;

		case 'on':
			obj.src = '/img/type01/date/now_off_on_bt.gif';
			break;

		default:
			break;
	}

	return false;
}





function fncCalendar( object, returnFlg ,arg1 )
{
	//�ͤ򥻥åȤ��륪�֥�������
	returnObject = 'parent.'+ object.form.name + '.' + object.name;

	//���ư�ե饰�ǥե��������
	if( !arguments[2] )
	{
		arg1 = 0;
	}

	//���߽����
	if( arg1 == 0 )
	{
		now = new Date()
	}

	//ǯ��������
	nowdate  = now.getDate();
	nowmonth = now.getMonth();
	nowyear  = now.getYear();

	//���ư����
	//12���arg1��+�ʤ�
	if( nowmonth == 11 && arg1 > 0 )
	{
		//���arg1-1;1ǯ�û�
		nowmonth = -1 + arg1 ;
		nowyear++ ;
	}
	//1���arg1��-�ʤ�
	else if(nowmonth==0 && arg1 < 0)
	{
		//���arg1+12;1ǯ����
		nowmonth = 12 + arg1;
		nowyear--;
	}
	else
	{
		//2-11��ʤ���+arg1
		nowmonth +=  arg1;
	}

	//2000ǯ�����б�
	if( nowyear < 1900 )
	{
		nowyear = 1900 + nowyear;
	}

	//���߷�����
	now = new Date(nowyear, nowmonth, 1);

	//YYYYMM����
	nowyyyymm = nowyear * 100 + nowmonth;

	//YYYY/MM����
	nowtitleyyyymm = nowyear + '/' + (nowmonth + 1);

	//������
	week = fncAryWeek();

	//�������������Ѵ�����μ���
	//�����1��
	fstday   = now;
	//�ǽ��������
	startday = fstday - ( fstday.getDay() * 1000*60*60*24 );
	startday = new Date(startday);


	// ��������������HTML
	ddata = '<form><table width="355" height="136" border="0">\n';

	// ǯ���ɽ������HTML
	ddata += '   <tr class="strYYYYMM">\n';
		ddata += '  <td colspan="7">\n';
		ddata +=      nowtitleyyyymm;
		ddata += '  </td>\n';
	ddata += '   </tr>\n';

	// ����ɽ������HTML
	ddata += '   <tr>\n';
		for( i=0; i<7; i++ )
		{
			ddata += '   <td id="Column' + i + '">\n';
			ddata +=       week[i];
			ddata += '   </td>\n';
		}
	ddata += '   </tr>\n';


	// ����ɽ������HTML
	for( j=0; j<6; j++ )
	{
		ddata += '   <tr bgcolor=#ffffff>\n';
		for( i=0;i<7;i++ )
		{
			nextday     = startday.getTime() + (i * 1000*60*60*24);
			wrtday      = new Date(nextday);
			wrtdate     = wrtday.getDate();
			wrtmonth    = wrtday.getMonth();
			wrtyear     = wrtday.getYear();
			if( wrtyear < 1900 ){ wrtyear = 1900 + wrtyear; }

			wrtyyyymm   = wrtyear * 100 + wrtmonth;
			wrtyyyymmdd = ''+wrtyear +'/'+ (wrtmonth + 1) +'/'+wrtdate;
			getday      = getWeek(wrtyyyymmdd);

			//���Ϥ�����
			wrtdateA = wrtdate;

			//����ǤϤʤ�����ɽ��
			if(wrtyyyymm != nowyyyymm)
			{
				ddata += ' <td class="strDateOther" \n';
				ddata += ' onclick="fncOutputDate(' + wrtyear + ',' + wrtmonth + ',' + wrtdate + ',' + returnObject + ',\'' + returnFlg + '\')" \n';
				ddata += ' onmousedown="javascript:this.style.backgroundColor=\'#a4e76b\'" \n';
				ddata += ' onmouseover="javascript:this.style.backgroundColor=\'#9bdbfc\'" \n';
				ddata += ' onmouseout="javascript:this.style.backgroundColor=\'#cccccc\'" >\n';
				ddata += wrtdateA;
			}
			//�������ξ���ɽ��
			else if( wrtdate         == absnow.getDate()  && 
					 wrtmonth         == absnow.getMonth() && 
					 wrtday.getYear() == absnow.getYear()  )
			{
				ddata += ' <td class="strDateToday" \n';
				ddata += ' onclick="fncOutputDate(' + wrtyear + ',' + wrtmonth + ',' + wrtdate + ',' + returnObject + ',\'' + returnFlg + '\')"\n';
				ddata += ' onmousedown="javascript:this.style.backgroundColor=\'#a4e76b\'" \n';
				ddata += ' onmouseover="javascript:this.style.backgroundColor=\'#9bdbfc\'" \n';
				ddata += ' onmouseout="javascript:this.style.backgroundColor=\'#ff9999\'" >\n';
				ddata += '<font color="#ffffff">'+wrtdateA+'</FONT>\n';
			}
			//�ǥե���Ȥ�ɽ��
			else
			{
				ddata += ' <td class="strDate" \n';
				ddata += ' onclick="fncOutputDate(' + wrtyear + ',' + wrtmonth + ',' + wrtdate + ',' + returnObject + ',\'' + returnFlg + '\')"\n';
				ddata += ' onmousedown="javascript:this.style.backgroundColor=\'#a4e76b\'" \n'; // #c0ec9a
				ddata += ' onmouseover="javascript:this.style.backgroundColor=\'#9bdbfc\'" \n';
				ddata += ' onmouseout="javascript:this.style.backgroundColor=\'#ffffff\'" >\n';
				ddata += wrtdateA;
			}
			ddata += '   </td>\n'
		}
		ddata += '   </tr>\n';

		startday = new Date(nextday);
		startday = startday.getTime() + (1000*60*60*24);
		startday = new Date(startday);
	}

	//���ѹ��ܥ��󡢸��߷�ܥ���
	ddata += '   <tr><td>&nbsp;</td></tr>\n';
	ddata += '   <tr>\n';
		ddata += '  <td colspan="7" align="center">\n';

		ddata += '    <a href="#"><img src="/img/type01/date/prev_off_bt.gif" width="58" height="16" border="0" \n';
		ddata += '      onmouseover="fncPrevButton( \'on\' , this );" onmouseout="fncPrevButton( \'off\' , this );" \n';
		ddata += '      onclick="fncCalendar(' + returnObject + ',\'' + returnFlg + '\',-1)"></a>&nbsp;\n';

		ddata += '    <a href="#"><img src="/img/type01/date/now_off_bt.gif" width="58" height="16" border="0" \n';
		ddata += '      onmouseover="fncNowButton( \'on\' , this );" onmouseout="fncNowButton( \'off\' , this );" \n';
		ddata += '      onclick="fncCalendar(' + returnObject + ',\'' + returnFlg + '\',0)"></a>&nbsp;\n';

		ddata += '    <a href="#"><img src="/img/type01/date/next_off_bt.gif" width="58" height="16" border="0" \n';
		ddata += '      onmouseover="fncNextButton( \'on\' , this );" onmouseout="fncNextButton( \'off\' , this );" \n';
		ddata += '      onclick="fncCalendar(' + returnObject + ',\'' + returnFlg + '\',1)"></a>\n';

		ddata += '  </td>\n';
	ddata += '   </tr>\n';

	ddata += '</table>\n';
	ddata += '</form>\n';
	ddata += '</body>\n';
	ddata += '</html>\n';

	//�����������̤�ȿ��

	document.all.DateDisplay.innerHTML = ddata;
	//alert( document.all.DateDisplay.innerHTML );
}

// ��������
function getWeek(date)
{
	if( arguments.length > 0 )
	{
		date = date;
	}else{
		date = null;
	}
	var now  = new Date(date);
	var week = new Array('��','��','��','��','��','��','��');
	return week[now.getDay()];
}


//����������
function fncOutputDate( wrtyear , wrtmonth , wrtdate , object, returnFlg)
{
	wrtmonth += 1;

	//1-9�ΤȤ�01-09������
	if( wrtmonth < 10 )  { wrtmonth = "0" + wrtmonth; }
	if( wrtdate  < 10 )  { wrtdate  = "0" + wrtdate; }

	//���դ��ǡ�����yyyy/mm/dd��������
	outputdate = wrtyear + '/' + wrtmonth + '/' + wrtdate ;

	//���ꤵ�줿������˽���
	object.value = outputdate;

	//������ɥ����Ĥ���
	// �׾���
	if( returnFlg == "DateA" )
	{
		parent.DisplayerM10( '' , document.all.Mdata10 );
		parent.ExchangeM10( 0 , parent.Pwin );

		if( parent.document.all.InputA.style.visibility != 'hidden' )
		{
			parent.document.all.DateAImg.focus();
		}

		// �����졼�ȺƷ׻�
		// �������
		if( typeof( window.parent.HSO.SOFlg ) == "object" )
		{
			window.parent.DLwin.fncCalConversionRate();

			// ����Ѥ�ǯ������å�
			window.parent.subLoadMasterHidden( 'caCheckClosedReceive', 0, window.parent.document.all.check_alert_appdate, Array ( outputdate ), window.parent.document.all.objDataSourceSettingCheckAlertAppDate, 9 );

		}
		//ȯ�����
		if( typeof( window.parent.HSO.POFlg ) == "object" )
		{
			window.parent.DLwin.fncCalConversionRate();

			// ����Ѥ�ǯ������å�
			window.parent.subLoadMasterHidden( 'caCheckClosedOrder', 0, window.parent.document.all.check_alert_appdate, Array ( outputdate ), window.parent.document.all.objDataSourceSettingCheckAlertAppDate, 9 );

		}
		// ������
		if( typeof( window.parent.HSO.SCFlg ) == "object" )
		{
			window.parent.DLwin.fncCalConversionRate();

			// ����Ѥ�ǯ������å�
			window.parent.subLoadMasterHidden( 'caCheckClosedSales', 0, window.parent.document.all.check_alert_appdate, Array ( outputdate ), window.parent.document.all.objDataSourceSettingCheckAlertAppDate, 9 );
		}
		// ��������
		if( typeof( window.parent.HSO.PCFlg ) == "object" )
		{
			window.parent.DLwin.fncCalConversionRate();

			// ����Ѥ�ǯ������å�
			window.parent.subLoadMasterHidden( 'caCheckClosedStock', 0, window.parent.document.all.check_alert_appdate, Array ( outputdate ), window.parent.document.all.objDataSourceSettingCheckAlertAppDate, 9 );

		}
	}
	else if( returnFlg == "DateB" )
	{
		parent.DisplayerM10_2( '' , document.all.Mdata10_2 );
		parent.ExchangeM10_2( 0 , parent.Pwin );


		if( parent.document.all.InputA.style.visibility != 'hidden' )
		{
			parent.document.all.DateBImg.focus();
		}

	}
	else if( returnFlg == "DateC" )
	{
		parent.DisplayerM10_3( '' , document.all.Mdata10_3 );
		parent.ExchangeM10_3( 0 , parent.Pwin );


		if( parent.document.all.InputB.style.visibility != 'hidden' )
		{
			parent.document.all.DateCImg.focus();
		}

	}
}




//@*****************************************************************************
// ����   : ������������֤�
// �о�   : D�ܥ���򲡤����Ȥ��Υ�������
// ����� : ����������
//******************************************************************************

function fncAryWeek()
{
	if( window.parent.lngLanguageCode == 1 )
	{
		week = new Array('��', '��', '��', '��', '��', '��', '��');
	}
	else if( window.parent.lngLanguageCode == 0 )
	{
		week = new Array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
	}

	return week;
}




//-->