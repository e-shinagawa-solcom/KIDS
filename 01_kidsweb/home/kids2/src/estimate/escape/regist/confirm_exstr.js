


var jpBt = '<a href="#"><img onclick="javascript:window.close();return false;" onfocus="fncChangeBtImg( this, \'/img/type01/cmn/seg/close_off_on_ja_bt.gif\' );" onblur="fncChangeBtImg( this, \'/img/type01/cmn/seg/close_off_ja_bt.gif\' );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncChangeBtImg( this, \'/img/type01/cmn/seg/close_off_on_ja_bt.gif\' );" onmouseout="fncChangeBtImg( this, \'/img/type01/cmn/seg/close_off_ja_bt.gif\' ); fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0"></a>&nbsp;&nbsp;<a href="#"><img onclick="javascript:window.frmAction.submit();return false" onfocus="fncChangeBtImg( this, \'/img/type01/cmn/querybt/regist_gray_off_on_ja_bt.gif\' );" onblur="fncChangeBtImg( this, \'/img/type01/cmn/querybt/regist_gray_off_ja_bt.gif\' );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncChangeBtImg( this, \'/img/type01/cmn/querybt/regist_gray_off_on_ja_bt.gif\' );" onmouseout="fncChangeBtImg( this, \'/img/type01/cmn/querybt/regist_gray_off_ja_bt.gif\' ); fncAlphaOff( this );" src="/img/type01/cmn/querybt/regist_gray_off_ja_bt.gif" width="72" height="20" border="0"></a>';

var enBt ='<a href="#"><img onclick="javascript:window.close();return false;" onfocus="fncChangeBtImg( this, \'/img/type01/cmn/seg/close_off_on_en_bt.gif\' );" onblur="fncChangeBtImg( this, \'/img/type01/cmn/seg/close_off_en_bt.gif\' );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncChangeBtImg( this, \'/img/type01/cmn/seg/close_off_on_en_bt.gif\' );" onmouseout="fncChangeBtImg( this, \'/img/type01/cmn/seg/close_off_en_bt.gif\' ); fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0"></a>&nbsp;&nbsp;<a href="#"><img onclick="javascript:window.frmAction.submit();return false;" onfocus="fncChangeBtImg( this, \'/img/type01/cmn/querybt/regist_gray_off_on_en_bt.gif\' );" onblur="fncChangeBtImg( this, \'/img/type01/cmn/querybt/regist_gray_off_en_bt.gif\' );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncChangeBtImg( this, \'/img/type01/cmn/querybt/regist_gray_off_on_en_bt.gif\' );" onmouseout="fncChangeBtImg( this, \'/img/type01/cmn/querybt/regist_gray_off_en_bt.gif\' ); fncAlphaOff( this );" src="/img/type01/cmn/querybt/regist_gray_off_en_bt.gif" width="72" height="20" border="0"></a>';


var jpTxt = '�嵭���Ƥ���Ͽ���ޤ�����';
var enTxt = 'Does it register from this contents?';



// ���ܸ�Ѹ�����
function fncChgEtoJ( lngLanguageCode )
{
	// �Ѹ�����
	if( lngLanguageCode == 0 )
	{
		estimateActionTitle.innerHTML  = enTxt;
		estimateActionButton.innerHTML = enBt;
	}

	// ���ܸ�����
	else if( lngLanguageCode == 1 )
	{
		estimateActionTitle.innerHTML  = jpTxt;
		estimateActionButton.innerHTML = jpBt;
	}
	return false;
}
