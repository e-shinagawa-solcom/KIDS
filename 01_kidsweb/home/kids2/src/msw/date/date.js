<!--

var now    = new Date();
var absnow = now;



//---------------------------------------------------
// 適用：「NEXT」ボタン
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
// 適用：「PREV」ボタン
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
// 適用：「NOW」ボタン
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
	//値をセットするオブジェクト
	returnObject = 'parent.'+ object.form.name + '.' + object.name;

	//月移動フラグデフォルト設定
	if( !arguments[2] )
	{
		arg1 = 0;
	}

	//現在初期化
	if( arg1 == 0 )
	{
		now = new Date()
	}

	//年月日取得
	nowdate  = now.getDate();
	nowmonth = now.getMonth();
	nowyear  = now.getYear();

	//月移動処理
	//12月でarg1が+なら
	if( nowmonth == 11 && arg1 > 0 )
	{
		//月はarg1-1;1年加算
		nowmonth = -1 + arg1 ;
		nowyear++ ;
	}
	//1月でarg1が-なら
	else if(nowmonth==0 && arg1 < 0)
	{
		//月はarg1+12;1年減算
		nowmonth = 12 + arg1;
		nowyear--;
	}
	else
	{
		//2-11月なら月は+arg1
		nowmonth +=  arg1;
	}

	//2000年問題対応
	if( nowyear < 1900 )
	{
		nowyear = 1900 + nowyear;
	}

	//現在月を確定
	now = new Date(nowyear, nowmonth, 1);

	//YYYYMM作成
	nowyyyymm = nowyear * 100 + nowmonth;

	//YYYY/MM作成
	nowtitleyyyymm = nowyear + '/' + (nowmonth + 1);

	//週設定
	week = fncAryWeek();

	//カレンダー構築用基準日の取得
	//今月の1日
	fstday   = now;
	//最初の日曜日
	startday = fstday - ( fstday.getDay() * 1000*60*60*24 );
	startday = new Date(startday);


	// カレンダー構築用HTML
	ddata = '<form><table width="355" height="136" border="0">\n';

	// 年月を表示するHTML
	ddata += '   <tr class="strYYYYMM">\n';
		ddata += '  <td colspan="7">\n';
		ddata +=      nowtitleyyyymm;
		ddata += '  </td>\n';
	ddata += '   </tr>\n';

	// 週を表示するHTML
	ddata += '   <tr>\n';
		for( i=0; i<7; i++ )
		{
			ddata += '   <td id="Column' + i + '">\n';
			ddata +=       week[i];
			ddata += '   </td>\n';
		}
	ddata += '   </tr>\n';


	// 日を表示するHTML
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

			//出力する日
			wrtdateA = wrtdate;

			//今月ではない日の表示
			if(wrtyyyymm != nowyyyymm)
			{
				ddata += ' <td class="strDateOther" \n';
				ddata += ' onclick="fncOutputDate(' + wrtyear + ',' + wrtmonth + ',' + wrtdate + ',' + returnObject + ',\'' + returnFlg + '\')" \n';
				ddata += ' onmousedown="javascript:this.style.backgroundColor=\'#a4e76b\'" \n';
				ddata += ' onmouseover="javascript:this.style.backgroundColor=\'#9bdbfc\'" \n';
				ddata += ' onmouseout="javascript:this.style.backgroundColor=\'#cccccc\'" >\n';
				ddata += wrtdateA;
			}
			//現在日の場合の表示
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
			//デフォルトの表示
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

	//月変更ボタン、現在月ボタン
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

	//作成した画面を反映

	document.all.DateDisplay.innerHTML = ddata;
	//alert( document.all.DateDisplay.innerHTML );
}

// 曜日取得
function getWeek(date)
{
	if( arguments.length > 0 )
	{
		date = date;
	}else{
		date = null;
	}
	var now  = new Date(date);
	var week = new Array('日','月','火','水','木','金','土');
	return week[now.getDay()];
}


//整形し出力
function fncOutputDate( wrtyear , wrtmonth , wrtdate , object, returnFlg)
{
	wrtmonth += 1;

	//1-9のとき01-09に整形
	if( wrtmonth < 10 )  { wrtmonth = "0" + wrtmonth; }
	if( wrtdate  < 10 )  { wrtdate  = "0" + wrtdate; }

	//日付けデータをyyyy/mm/dd型に整形
	outputdate = wrtyear + '/' + wrtmonth + '/' + wrtdate ;

	//指定された入力欄に出力
	object.value = outputdate;

	//ウィンドウを閉じる
	// 計上日
	if( returnFlg == "DateA" )
	{
		parent.DisplayerM10( '' , document.all.Mdata10 );
		parent.ExchangeM10( 0 , parent.Pwin );

		if( parent.document.all.InputA.style.visibility != 'hidden' )
		{
			parent.document.all.DateAImg.focus();
		}

		// 換算レート再計算
		// 受注管理
		if( typeof( window.parent.HSO.SOFlg ) == "object" )
		{
			window.parent.DLwin.fncCalConversionRate();

			// 締め済み年月チェック
			window.parent.subLoadMasterHidden( 'caCheckClosedReceive', 0, window.parent.document.all.check_alert_appdate, Array ( outputdate ), window.parent.document.all.objDataSourceSettingCheckAlertAppDate, 9 );

		}
		//発注管理
		if( typeof( window.parent.HSO.POFlg ) == "object" )
		{
			window.parent.DLwin.fncCalConversionRate();

			// 締め済み年月チェック
			window.parent.subLoadMasterHidden( 'caCheckClosedOrder', 0, window.parent.document.all.check_alert_appdate, Array ( outputdate ), window.parent.document.all.objDataSourceSettingCheckAlertAppDate, 9 );

		}
		// 売上管理
		if( typeof( window.parent.HSO.SCFlg ) == "object" )
		{
			window.parent.DLwin.fncCalConversionRate();

			// 締め済み年月チェック
			window.parent.subLoadMasterHidden( 'caCheckClosedSales', 0, window.parent.document.all.check_alert_appdate, Array ( outputdate ), window.parent.document.all.objDataSourceSettingCheckAlertAppDate, 9 );
		}
		// 仕入管理
		if( typeof( window.parent.HSO.PCFlg ) == "object" )
		{
			window.parent.DLwin.fncCalConversionRate();

			// 締め済み年月チェック
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
// 概要   : 曜日を配列で返す
// 対象   : Dボタンを押したときのカレンダー
// 戻り値 : 曜日の配列
//******************************************************************************

function fncAryWeek()
{
	if( window.parent.lngLanguageCode == 1 )
	{
		week = new Array('日', '月', '火', '水', '木', '金', '土');
	}
	else if( window.parent.lngLanguageCode == 0 )
	{
		week = new Array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
	}

	return week;
}




//-->