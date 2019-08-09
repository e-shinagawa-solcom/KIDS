

// 処理モード値取得
var g_strMode = parent.g_aryArgs[0][4];

// [lngLanguageCode]値取得
var g_lngCode = parent.lngLanguageCode.value;







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








// クエリーボタンテーブル書き出しモジュール
function fncTitleOutput( lngCode )
{

	// 削除確認用テーブルセット(日本語)
	var deleteTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>削除しますか？</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();window.returnValue=true;"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="DeleteJOn( this );" onblur="DeleteJOff( this );" onmouseover="DeleteJOn( this );" onmouseout="DeleteJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/delete_off_ja_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';
	// 削除確認用テーブルセット(英語)
	var deleteTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it delete?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();window.returnValue=true;"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="DeleteEOn( this );" onblur="DeleteEOff( this );" onmouseover="DeleteEOn( this );" onmouseout="DeleteEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/delete_off_en_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';

	// 詳細確認用テーブルセット(日本語)
	var detailTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a></td></tr></table>';
	// 詳細確認用テーブルセット(英語)
	var detailTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a></td></tr></table>';


	if( g_strMode == 'detail' )
	{
		if( lngCode == 0 )
		{
			ControlTitle.innerText = 'DETAIL';
			objQuery.innerHTML = detailTableE;
		}
		else if( lngCode == 1 )
		{
			ControlTitle.innerText = '詳細確認';
			objQuery.innerHTML = detailTableJ;
		}
	}
	else if( g_strMode == 'delete' )
	{
		if( lngCode == 0 )
		{
			ControlTitle.innerText = 'DELETE';
			objQuery.innerHTML = deleteTableE;
		}
		else if( lngCode == 1 )
		{
			ControlTitle.innerText = '削除確認';
			objQuery.innerHTML = deleteTableJ;
		}
	}

	return false;
}





// 日本語英語切替
function fncChgEtoJ()
{

	// [lngLanguageCode]の値取得
	//var lngCode = parent.lngLanguageCode.value;

	// 英語切替
	if( g_lngCode == 0 )
	{

		fncTitleOutput( 0 );

		CreationDate.innerText = 			'Creation date';
		GoodsPlanProgress.innerText = 		'Plan status';
		RevisionNo.innerText = 				'Revise No.';
		RevisionDate.innerText = 			'Revise date';
		ProductCode.innerText = 			'Product code';
		ProductName.innerText = 			'Products name(ja)';
		ProductEnglishName.innerText = 		'Products name(en)';
		InputUser.innerText = 				'Input person';
		InChargeGroup.innerText = 			'Dept';
		InChargeUser.innerText = 			'In charge name';
		GoodsCode.innerText = 				'Goods code(Corresp)';
		GoodsName.innerText = 				'Goods name';
		Category.innerText = 			'Category';
		Customer.innerText = 				'Vendor';
		CustomerUser.innerText = 			'In charge name';
		PackingUnit.innerText = 			'Packing Unit';
		ProductUnit.innerText = 			'Product Unit';
		GoodsForm.innerText = 				'Goods Form';
		BoxQuantity.innerText = 			'Box Qty';
		CartonQuantity.innerText = 			'Carton Qty';
		RefoundQuantity.innerText = 		'Refound Qty';
		FirstDeliveryQuantity.innerText = 	'Delivery Qty';
		CreationFactory.innerText = 		'Creation factory';
		AssemblyFactory.innerText = 		'Assembly factory';
		Location.innerText = 				'Location';
		DeliveryLimitDate.innerText = 		'Delivery date';
		SellingPrince.innerText = 			'Selling prince(pcs)';
		WholesalePrice.innerText = 			'Wholesale price(pcs)';
		TargetAge.innerText = 				'Target age';
		Royalty.innerText = 				'Royalty(%)';
		Inspection.innerText = 				'Inspection';
		Copyright.innerText = 				'Copyright';
		CopyrightNote.innerText = 			'Copyright remark';
		CopyrightStamp.innerText = 			'Copyright(Stamp)';
		CopyrightPrint.innerText = 			'Copyright(Print)';
		ProductsInfo.innerText = 			'Products info';
		AssemblyInfo.innerText = 			'Assembly info';
		Details.innerText = 				'Details';
	}

	// 日本語切替
	else if( g_lngCode == 1 )
	{

		fncTitleOutput( 1 );

		CreationDate.innerText = 			'作成日時';
		GoodsPlanProgress.innerText = 		'企画進行状況';
		RevisionNo.innerText = 				'改訂番号';
		RevisionDate.innerText = 			'改訂日時';
		ProductCode.innerText = 			'製品コード';
		ProductName.innerText = 			'製品名称(日本語)';
		ProductEnglishName.innerText = 		'製品名称(英語)';
		InputUser.innerText = 				'入力者';
		InChargeGroup.innerText = 			'営業部署';
		InChargeUser.innerText = 			'開発担当者';
		GoodsCode.innerText = 				'顧客品番';
		Category.innerText = 			'カテゴリ';
		GoodsName.innerText = 				'商品名称';
		Customer.innerText = 				'顧客';
		CustomerUser.innerText = 			'顧客担当者';
		PackingUnit.innerText = 			'荷姿単位';
		ProductUnit.innerText = 			'製品単位';
		GoodsForm.innerText = 				'商品形態';
		BoxQuantity.innerText = 			'内箱(袋)入数';
		CartonQuantity.innerText = 			'カートン入数';
		RefoundQuantity.innerText = 		'生産予定数';
		FirstDeliveryQuantity.innerText = 	'初回納品数';
		CreationFactory.innerText = 		'生産工場';
		AssemblyFactory.innerText = 		'アッセンブリ工場';
		Location.innerText = 				'納品場所';
		DeliveryLimitDate.innerText = 		'納期';
		SellingPrince.innerText = 			'納価(pcs)';
		WholesalePrice.innerText = 			'上代(pcs)';
		TargetAge.innerText = 				'対象年齢';
		Royalty.innerText = 				'ロイヤリティ(%)';
		Inspection.innerText = 				'証紙';
		Copyright.innerText = 				'版権元';
		CopyrightNote.innerText = 			'版権元備考';
		CopyrightStamp.innerText = 			'版権表示(刻印)';
		CopyrightPrint.innerText = 			'版権表示(印刷物)';
		ProductsInfo.innerText = 			'製品構成';
		AssemblyInfo.innerText = 			'アッセンブリ内容';
		Details.innerText = 				'仕様詳細';
	}

	return false;
}