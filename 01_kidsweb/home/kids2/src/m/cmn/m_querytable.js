

//////////////////////////////////////////////////////////////////
///// [����][�ɲ�]���˻��Ѥ����ѿ������ /////
if( typeof(parent.g_aryArgs) != 'undefined' )
{

	// �����⡼���ͼ���
	var g_strMode = parent.g_aryArgs[0][4];

}



	// �ǥХå�
	//g_strMode = 'add';





/*

//***************************************************************************
// �ޥ������ǡ����Խ��塢�����ץܥ���������ˡ��Խ��ǡ�����
// ��g_aryEditData�פ˳�Ǽ���Ƥ�������δؿ�
//***************************************************************************
function fncGetEditData()
{

	// �Խ��ե饰�ͤ���0�פξ��
	if( parent.g_EditCnt == 0 )
	{
		for( i = 0; i < window.PS.elements.length; i++ )
		{
			if( window.PS.elements[i].id.substring( 0 , 3 ) == 'Txt' )
			{
				parent.g_aryEditData[i] = window.PS.elements[i].value;
			}
		}

		// �Խ��ե饰�ͤ��ѹ�
		parent.g_EditCnt = 1;

	}

	//alert( parent.g_aryEditData );
	return false;

}

//***************************************************************************
// �ޥ������ǡ����Խ��塢�����ץܥ������äƤ������ˡ��ݻ����Ƥ�����
// �嵭�ǡ�����g_aryEditData�פ�Ÿ�����뤿��δؿ�
//***************************************************************************
function fncApplyEditData()
{

	// �Խ��ե饰�ͤ���1�פξ��
	if( parent.g_EditCnt == 1 )
	{
		for( i = 0; i < window.PS.elements.length; i++ )
		{
			if( window.PS.elements[i].id.substring( 0 , 3 ) == 'Txt' )
			{
				window.PS.elements[i].value = parent.g_aryEditData[i];
			}
		}

	// �Խ��ե饰�ͤν����
	parent.g_EditCnt = 0;

	}

	return false;

}

*/




//////////////////////////////////////////////////////////////////
// �����꡼�����Ѵؿ�
function fncGetAction(strAction)
{

	if( g_strMode == 'fix' ||
		g_strMode == 'add' )
	{
		window.PS.action = strAction;
		window.PS.submit();
	}

	// ��ǧ
	else if( g_strMode == '2' ||
			 g_strMode == '1' ||
			 g_strMode == '3' )
	{
		window.PS.action = strAction;
		window.PS.submit();
	}

}





/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// �����ѥơ��֥륻�å�(���ܸ�)
var fixTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="parent.window.close();"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'confirm.php\');"><img onmouseover="MasterRegJOn(this);" onmouseout="MasterRegJOff(this);" src="/img/type01/cmn/seg/regist_off_ja_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';
// �����ѥơ��֥륻�å�(�Ѹ�)
var fixTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="parent.window.close();"><img onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'confirm.php\');"><img onmouseover="MasterRegEOn(this);" onmouseout="MasterRegEOff(this);" src="/img/type01/cmn/seg/regist_off_en_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// �ɲ��ѥơ��֥륻�å�(���ܸ�)
var addTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="parent.window.close();"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'confirm.php\');"><img onmouseover="MasterRegJOn(this);" onmouseout="MasterRegJOff(this);" src="/img/type01/cmn/seg/regist_off_ja_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';
// �ɲ��ѥơ��֥륻�å�(�Ѹ�)
var addTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="parent.window.close();"><img onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'confirm.php\');"><img onmouseover="MasterRegEOn(this);" onmouseout="MasterRegEOff(this);" src="/img/type01/cmn/seg/regist_off_en_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';




/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [CONFIRM]�����ѥơ��֥륻�å�(���ܸ�)
var confirmfixTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>�������ޤ�����</td></tr><tr><td bgcolor="#f1f1f1"><a href="javascript:fncGetAction(\'edit.php\');"><img onmouseover="GrayBackJOn( this );" onmouseout="GrayBackJOff( this );" src="/img/type01/cmn/querybt/back_gray_off_ja_bt.gif" width="72" height="20" border="0" alt="BACK"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'action.php\');"><img onmouseover="MasterRegJOn(this);" onmouseout="MasterRegJOff(this);" src="/img/type01/cmn/seg/regist_off_ja_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';
// [CONFIRM]�����ѥơ��֥륻�å�(�Ѹ�)
var confirmfixTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it fix?</td></tr><tr><td bgcolor="#f1f1f1"><a href="javascript:fncGetAction(\'edit.php\');"><img onmouseover="GrayBackEOn( this );" onmouseout="GrayBackEOff( this );" src="/img/type01/cmn/querybt/back_gray_off_en_bt.gif" width="72" height="20" border="0" alt="BACK"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'action.php\');"><img onmouseover="MasterRegEOn(this);" onmouseout="MasterRegEOff(this);" src="/img/type01/cmn/seg/regist_off_en_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [CONFIRM]�ɲ��ѥơ��֥륻�å�(���ܸ�)
var confirmaddTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>�ɲä��ޤ�����</td></tr><tr><td bgcolor="#f1f1f1"><a href="javascript:fncGetAction(\'edit.php\');"><img onmouseover="GrayBackJOn( this );" onmouseout="GrayBackJOff( this );" src="/img/type01/cmn/querybt/back_gray_off_ja_bt.gif" width="72" height="20" border="0" alt="BACK"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'action.php\');"><img onmouseover="MasterRegJOn(this);" onmouseout="MasterRegJOff(this);" src="/img/type01/cmn/seg/regist_off_ja_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';
// [CONFIRM]�ɲ��ѥơ��֥륻�å�(�Ѹ�)
var confirmaddTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it addition?</td></tr><tr><td bgcolor="#f1f1f1"><a href="javascript:fncGetAction(\'edit.php\');"><img onmouseover="GrayBackEOn( this );" onmouseout="GrayBackEOff( this );" src="/img/type01/cmn/querybt/back_gray_off_en_bt.gif" width="72" height="20" border="0" alt="BACK"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'action.php\');"><img onmouseover="MasterRegEOn(this);" onmouseout="MasterRegEOff(this);" src="/img/type01/cmn/seg/regist_off_en_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [CONFIRM]����ѥơ��֥륻�å�(���ܸ�)
var confirmdeleteTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>������ޤ�����</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="parent.window.close();"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'action.php\');"><img onmouseover="DeleteJOn( this );" onmouseout="DeleteJOff( this );" src="/img/type01/cmn/seg/delete_off_ja_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';
// [CONFIRM]����ѥơ��֥륻�å�(�Ѹ�)
var confirmdeleteTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it delete?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="parent.window.close();"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:fncGetAction(\'action.php\');"><img onmouseover="DeleteEOn( this );" onmouseout="DeleteEOff( this );" src="/img/type01/cmn/seg/delete_off_en_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';






//////////////////////////////////////////////////////////////////
// �����꡼�ܥ���ơ��֥�񤭽Ф��⥸�塼��
function fncTitleOutput( lngCode )
{


	switch( g_strMode )
	{

		// [����]
		case 'fix':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'FIX';
				objQuery.innerHTML = fixTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '����';
				objQuery.innerHTML = fixTableJ;
			}

			break;


		// [�ɲ�]
		case 'add':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'ADDITION';
				objQuery.innerHTML = addTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '�ɲ�';
				objQuery.innerHTML = addTableJ;
			}

			break;


		// [������ǧ]
		case '2':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'FIX';
				objQuery.innerHTML = confirmfixTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '������ǧ';
				objQuery.innerHTML = confirmfixTableJ;
			}

			break;


		// [�ɲó�ǧ]
		case '1':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'ADDITION';
				objQuery.innerHTML = confirmaddTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '�ɲó�ǧ';
				objQuery.innerHTML = confirmaddTableJ;
			}

			break;


		// [�����ǧ]
		case '3':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'DELETE';
				objQuery.innerHTML = confirmdeleteTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '�����ǧ';
				objQuery.innerHTML = confirmdeleteTableJ;
			}

			break;


		default:
			break;

	}


	return false;
}






//////////////////////////////////////////////////////////////////
// [�ǥХå�]HTML��������ɽ���ؿ�
function fncShowHTML( strID )
{
	// ����Υ���(ID)��Υƥ����ȥ�󥸤��������
	var doc1 = document.body.createTextRange();

	doc1.moveToElementText(strID);

	alert(doc1.htmlText);
}