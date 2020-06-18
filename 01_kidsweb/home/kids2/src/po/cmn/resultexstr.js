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
function fncPayConditionConfirm(lngPayConditionCode , lngPayConditionCodeCrt)
{
	var list = document.getElementById("lngPayConditionCodeList");
    list.selectedIndex = eval(lngPayConditionCode);//ユーザーが設定した値をセット
	var strConfirmMessage = "設定されている支払条件は発注内容から推奨される支払条件と一致していません。\n推奨される支払い条件に変更しますか？";
	var strAlertMessage = "支払条件を確認して下さい。";

		if (confirm(strConfirmMessage) == true ){
			window.form1.lngPayConditionCode.value = lngPayConditionCodeCrt;
			list.value = lngPayConditionCodeCrt;
		} else{
		 alert(strAlertMessage);
		 document.getElementById("strRecommendPayCondition").innerHTML = '<marquee behavior="scroll" scrolldelay="150">推奨される支払条件：' + list.options[lngPayConditionCodeCrt].text + '</marquee>';
		}

	return false;
}


function fncPayConditionFrmChanged(){
	//選択された値を送信フォームにも反映
	window.form1.lngPayConditionCode.value = window.lngPayConditionCodeList.value;
}

//2007.08.10 matsuki update end
//-->