<!--


// プレビューボタン書出し
function fncPreview( strURL )
{
	if( strURL != '' )
	{
		var previews = '<a href="#"><img onclick="fncListOutput(\'' +  strURL + '\');return false;" onmouseover="fncGrayPreviewButton( \'on\' , this );" onmouseout="fncGrayPreviewButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="/img/type01/cmn/querybt/graypreview_off_bt.gif" width="72" height="20" border="0" alt="PREVIEW"></a>';

		PreviewBt.innerHTML = previews;

	}
	else
	{
		PreviewBt.style.visibility = 'hidden';
	}

	return false;
}



// 日本語英語切替
function fncChgEtoJ( strMode )
{
	
	
	// 英語切替
	if( g_lngCode == 0 )
	{

		// 処理用テーブル書き出し
		fncProcessingOutputModule( strMode , 0 );

		if( typeof(WFMessage) != 'undefined' )
		{
			WFMessage.innerText = 'Message';
		}

		CreationDate.innerText		=	'Creation date';
		OrderAppDate.innerText		=	'Order date';
		POrderCode.innerText		=	'P order No.';
		InputUser.innerText			=	'Input person';
		Customer.innerText			=	'Supplier';
		InChargeGroup.innerText		=	'Dept';
		InChargeUser.innerText		=	'In charge name';
		DeliveryPlace.innerText		=	'Location';
		MonetaryUnit.innerText		=	'Currency';
		MonetaryRate.innerText		=	'Rate type';
		ConversionRate.innerText	=	'Rate';
		OrderStatus.innerText		=	'Status';
		PayCondition.innerText		=	'Pay condition';
		ExpirationDate.innerText	=	'PO limit date';
		Remark.innerText			=	'Remark';
		TotalPrice.innerText		=	'Total';


	}

	// 日本語切替
	else if( g_lngCode == 1 )
	{

		// 処理用テーブル書き出し
		fncProcessingOutputModule( strMode , 1 );

		if( typeof(WFMessage) != 'undefined' )
		{
			WFMessage.innerText = 'メッセージ';
		}

		CreationDate.innerText		=	'登録日';
		OrderAppDate.innerText		=	'計上日';
		POrderCode.innerText		=	'発注NO.';
		InputUser.innerText			=	'入力者';
		Customer.innerText			=	'仕入先';
		InChargeGroup.innerText		=	'部門';
		InChargeUser.innerText		=	'担当者';
		DeliveryPlace.innerText		=	'納品場所';
		MonetaryUnit.innerText		=	'通貨';
		MonetaryRate.innerText		=	'レートタイプ';
		ConversionRate.innerText	=	'換算レート';
		OrderStatus.innerText		=	'状態';
		PayCondition.innerText		=	'支払条件';
		ExpirationDate.innerText	=	'発注有効期限日';
		Remark.innerText			=	'備考';
		TotalPrice.innerText		=	'合計金額';


	}

	return false;
}




function fncSetWFMessage( obj , objHdn )
{
	objHdn.value = obj.value;

	// デバッグ
	// alert( objHdn.value );
}



//2007.08.10 matsuki update start
function fncPayConditionConfirm( strPayMode , lngPayConditionCode , lngPayConditionCodeCrt )
{
	var strMessage;
	var strMessageArray_J = new Array(
									"設定されている支払条件は発注内容から推奨される支払条件と一致していません。\n推奨される支払い条件に変更しますか？",
									"複数存在する明細に対して、支払条件は一括で設定されます。\n支払条件を確認して下さい。",
									"複数存在する明細に対して、支払条件は一括で設定されます。\n推奨される支払い条件に変更しますか？",
									"支払条件を確認して下さい。");
	
	var strMessageArray_E = new Array(
									"'Pay Condition' you selected doesn't correspond with that recommend.\nChange 'Pay Condition' to recommend?",
									"There are several dital datas you have registed. On the other hand 'Pay Condition' is setted to those datas at one time.\nConfirm 'Pay Condition'.",
									"There are several dital datas you have registed. On the other hand 'Pay Condition' is setted to those datas at one time.\nChange 'Pay Condition' to recommend?",
									"Confirm 'Pay Condition'.");
	
	var lngPayMode = eval(strPayMode);
	window.lngPayConditionCode.selectedIndex = eval(lngPayConditionCode);//ユーザーが設定した値をセット
	//objHdn.value = obj.value;

	// 英語切替
	if( g_lngCode == 0 ){
		strConfirm = strMessageArray_E[lngPayMode];
		strAlert = strMessageArray_E[3];
		
		
		
	}
	else {
		strConfirm = strMessageArray_J[lngPayMode];
		strAlert = strMessageArray_J[3];
	}
	
	if( lngPayMode != 1 ){//1の場合のみconfirmではなくalertにする
		
		result = confirm(strConfirm);
		if (result == true ){
			window.lngPayConditionCode.selectedIndex = eval(lngPayConditionCodeCrt);//正しい値をselect
			window.form1.lngPayConditionCode.value = lngPayConditionCodeCrt;
			//送信する値にも正しい値をセット
		}
		
		else{
		 alert(strAlert);
		 //window.frmPayConditionCode.lngPayConditionCode.disabled = true;
		 window.strRecommendPayCondition.innerHTML = '<marquee behavior="scroll" scrolldelay="150">推奨される支払条件：' + window.lngPayConditionCode.options[lngPayConditionCodeCrt].text + '</marquee>';
		}
	}
	
	else{
		alert(strConfirm);
	}

	return false;
}


function fncPayConditionFrmChanged(){
	//選択された値を送信フォームにも反映
	window.form1.lngPayConditionCode.value = window.lngPayConditionCode.selectedIndex;
}


//2007.08.10 matsuki update end
//-->