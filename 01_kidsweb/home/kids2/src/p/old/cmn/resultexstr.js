

// �����⡼���ͼ���
var g_strMode = parent.g_aryArgs[0][4];

// [lngLanguageCode]�ͼ���
var g_lngCode = parent.lngLanguageCode.value;







// �ץ�ӥ塼�ܥ����Ф�
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








// �����꡼�ܥ���ơ��֥�񤭽Ф��⥸�塼��
function fncTitleOutput( lngCode )
{

	// �����ǧ�ѥơ��֥륻�å�(���ܸ�)
	var deleteTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>������ޤ�����</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();window.returnValue=true;"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="DeleteJOn( this );" onblur="DeleteJOff( this );" onmouseover="DeleteJOn( this );" onmouseout="DeleteJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/delete_off_ja_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';
	// �����ǧ�ѥơ��֥륻�å�(�Ѹ�)
	var deleteTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it delete?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();window.returnValue=true;"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="DeleteEOn( this );" onblur="DeleteEOff( this );" onmouseover="DeleteEOn( this );" onmouseout="DeleteEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/delete_off_en_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';

	// �ܺٳ�ǧ�ѥơ��֥륻�å�(���ܸ�)
	var detailTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a></td></tr></table>';
	// �ܺٳ�ǧ�ѥơ��֥륻�å�(�Ѹ�)
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
			ControlTitle.innerText = '�ܺٳ�ǧ';
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
			ControlTitle.innerText = '�����ǧ';
			objQuery.innerHTML = deleteTableJ;
		}
	}

	return false;
}





// ���ܸ�Ѹ�����
function fncChgEtoJ()
{

	// [lngLanguageCode]���ͼ���
	//var lngCode = parent.lngLanguageCode.value;

	// �Ѹ�����
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

	// ���ܸ�����
	else if( g_lngCode == 1 )
	{

		fncTitleOutput( 1 );

		CreationDate.innerText = 			'��������';
		GoodsPlanProgress.innerText = 		'���ʹԾ���';
		RevisionNo.innerText = 				'�����ֹ�';
		RevisionDate.innerText = 			'��������';
		ProductCode.innerText = 			'���ʥ�����';
		ProductName.innerText = 			'����̾��(���ܸ�)';
		ProductEnglishName.innerText = 		'����̾��(�Ѹ�)';
		InputUser.innerText = 				'���ϼ�';
		InChargeGroup.innerText = 			'�Ķ�����';
		InChargeUser.innerText = 			'��ȯô����';
		GoodsCode.innerText = 				'�ܵ�����';
		Category.innerText = 			'���ƥ���';
		GoodsName.innerText = 				'����̾��';
		Customer.innerText = 				'�ܵ�';
		CustomerUser.innerText = 			'�ܵ�ô����';
		PackingUnit.innerText = 			'�ٻ�ñ��';
		ProductUnit.innerText = 			'����ñ��';
		GoodsForm.innerText = 				'���ʷ���';
		BoxQuantity.innerText = 			'��Ȣ(��)����';
		CartonQuantity.innerText = 			'�����ȥ�����';
		RefoundQuantity.innerText = 		'����ͽ���';
		FirstDeliveryQuantity.innerText = 	'���Ǽ�ʿ�';
		CreationFactory.innerText = 		'��������';
		AssemblyFactory.innerText = 		'���å���֥깩��';
		Location.innerText = 				'Ǽ�ʾ��';
		DeliveryLimitDate.innerText = 		'Ǽ��';
		SellingPrince.innerText = 			'Ǽ��(pcs)';
		WholesalePrice.innerText = 			'����(pcs)';
		TargetAge.innerText = 				'�о�ǯ��';
		Royalty.innerText = 				'�����ƥ�(%)';
		Inspection.innerText = 				'�ڻ�';
		Copyright.innerText = 				'�Ǹ���';
		CopyrightNote.innerText = 			'�Ǹ�������';
		CopyrightStamp.innerText = 			'�Ǹ�ɽ��(���)';
		CopyrightPrint.innerText = 			'�Ǹ�ɽ��(����ʪ)';
		ProductsInfo.innerText = 			'���ʹ���';
		AssemblyInfo.innerText = 			'���å���֥�����';
		Details.innerText = 				'���;ܺ�';
	}

	return false;
}