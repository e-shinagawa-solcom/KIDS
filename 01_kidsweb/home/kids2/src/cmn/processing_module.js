

//@-------------------------------------------------------------------------------------------------------------------
/**
* �ե����복�� : [��Ͽ���ܺ١������̵����]�����ܥ���ơ��֥�⥸�塼��
*
*
*
* ���� : ���ʡ�����ȯ����塦�����θ�����̲��̤���γƽ�������
*        �񤭽Ф��ơ��֥��������Ƥ��롣
*
* @package k.i.d.s.
* @license http://www.wiseknot.co.jp/
* @copyright Copyright &copy; 2004, Wiseknot, Inc.
* @author Ryosuke Tomita <r-tomita@wiseknot.co.jp>
* @access public
* @version 0.1
*/ 
//--------------------------------------------------------------------------------------------------------------------





















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ���ܸ�Ѹ������ѥ����Х��ѿ�
*
* �о� : [��Ͽ���ܺ١������̵����]�ѥƥ�ץ졼��
*
* @param [g_lngCode] : [���ͷ�] . ��iframe�������
*/
//--------------------------------------------------------------------------------------------------------------------
var g_lngCode = parent.lngLanguageCode.value; // [lngLanguageCode]�ͼ���















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �ƽ����ܥ���ơ��֥륻�åȤ����
*
* ���� : ��[��Ͽ��ǧ���ܺٳ�ǧ�������ǧ��̵������ǧ]�Ѥ�
*        �ơ��֥륻�å�(���ܸ졦�Ѹ�)��������롣
*
* �о� : [��Ͽ���ܺ١������̵����]�ѥƥ�ץ졼��
*
* �����ؿ� : [fncProcessingOutputModule] �����ܥ���ơ��֥�񤭽Ф��⥸�塼��
*
* @param [registTableJ]    : [ʸ����] . ��Ͽ��ǧ��(���ܸ�)
* @param [registTableE]    : [ʸ����] . ��Ͽ��ǧ��(�Ѹ�)
* @param [deleteTableJ]    : [ʸ����] . �����ǧ��(���ܸ�)
* @param [deleteTableE]    : [ʸ����] . �����ǧ��(�Ѹ�)
* @param [detailTableJ]    : [ʸ����] . �ܺٳ�ǧ��(���ܸ�)
* @param [detailTableE]    : [ʸ����] . �ܺٳ�ǧ��(�Ѹ�)
* @param [invalidTable01J] : [ʸ����] . ̵���������ѥѥ�����(���ܸ�)
* @param [invalidTable01E] : [ʸ����] . ̵���������ѥѥ�����(�Ѹ�)
* @param [invalidTable02J] : [ʸ����] . ̵���������ѥѥ�����(���ܸ�)
* @param [invalidTable02E] : [ʸ����] . ̵���������ѥѥ�����(�Ѹ�)
* @param [invalidTable03J] : [ʸ����] . ̵���������ѥѥ�����(���ܸ�)
* @param [invalidTable03E] : [ʸ����] . ̵���������ѥѥ�����(�Ѹ�)
* @param [invalidTable04J] : [ʸ����] . ̵���������ѥѥ�����(���ܸ�)
* @param [invalidTable04E] : [ʸ����] . ̵���������ѥѥ�����(�Ѹ�)
*/
//--------------------------------------------------------------------------------------------------------------------
// ��Ͽ��ǧ��(���ܸ�)
var registTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>���������Ƥ���Ͽ���ޤ�����</td></tr><tr><td bgcolor="#f1f1f1"><span id="regist_proc"><a href="#" onclick="window.close();return false;"><img id="close_btn" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="document.getElementById('+'\'regist_proc\''+').outerText='+'\'��Ͽ������...\''+'; window.form1.submit();return false;"><img id="regist_btn" onfocus="GrayRegistJOn( this );" onblur="GrayRegistJOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="GrayRegistJOn(this)" onmouseout="GrayRegistJOff(this);fncAlphaOff( this );" src="/img/type01/cmn/querybt/regist_gray_off_ja_bt.gif" width="72" height="20" border="0" alt="��Ͽ"><a></span></td></tr></table>';
// ��Ͽ��ǧ��(�Ѹ�)
var registTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it register from the following contents?</td></tr><tr><td bgcolor="#f1f1f1"><span id="regist_proc"><a href="#" onclick="window.close();return false;"><img id="close_btn" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<span name=""><a href="#" onclick="document.getElementById('+'\'regist_proc\''+').outerText='+'\'Registering...\''+'; window.form1.submit();return false;"><img id="regist_btn" onfocus="GrayRegistEOff( this );" onblur="GrayRegistEOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="GrayRegistEOn(this);fncAlphaOff( this );" onmouseout="GrayRegistEOff(this);fncAlphaOff( this );" src="/img/type01/cmn/querybt/regist_gray_off_en_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></span></td></tr></table>';


// �����ǧ��(���ܸ�)
var deleteTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>������ޤ�����</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="DeleteJOn( this );" onblur="DeleteJOff( this );" onmouseover="DeleteJOn( this );" onmouseout="DeleteJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/delete_off_ja_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';
// �����ǧ��(�Ѹ�)
var deleteTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it delete?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="DeleteEOn( this );" onblur="DeleteEOff( this );" onmouseover="DeleteEOn( this );" onmouseout="DeleteEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/delete_off_en_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';


// �ܺٳ�ǧ��(���ܸ�)
var detailTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a></td></tr></table>';
// �ܺٳ�ǧ��(�Ѹ�)
var detailTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a></td></tr></table>';



// ̵���������ѥѥ����� [����]��̵���������ǥǡ����������ؤ��ʤɲ���ȯ�����ʤ����
// (���ܸ�)
var invalidTable01J = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>̵�������ޤ�����</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onJ\' , this );" onblur="fncInvalidBigButton( \'offJ\' , this );" onmouseover="fncInvalidBigButton( \'onJ\' , this );" onmouseout="fncInvalidBigButton( \'offJ\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_ja_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';
// (�Ѹ�)
var invalidTable01E = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it invalid?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onE\' , this );" onblur="fncInvalidBigButton( \'offE\' , this );" onmouseover="fncInvalidBigButton( \'onE\' , this );" onmouseout="fncInvalidBigButton( \'offE\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_en_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';


// ̵���������ѥѥ����� [����]��̵��������������ȯ��ͭ���ˤʤ���
// (���ܸ�)
var invalidTable02J = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>�����Υǡ��������褷�ޤ���̵�������ޤ�����</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onJ\' , this );" onblur="fncInvalidBigButton( \'offJ\' , this );" onmouseover="fncInvalidBigButton( \'onJ\' , this );" onmouseout="fncInvalidBigButton( \'offJ\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_ja_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';
// (�Ѹ�)
var invalidTable02E = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Former data revives. Does it invalid?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onE\' , this );" onblur="fncInvalidBigButton( \'offE\' , this );" onmouseover="fncInvalidBigButton( \'onE\' , this );" onmouseout="fncInvalidBigButton( \'offE\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_en_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';


// ̵���������ѥѥ����� [����]������ǡ�����̵�������뤳�Ȥǡ��ǡ��������褹����
// (���ܸ�)
var invalidTable03J = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>������줿�ǡ��������褷�ޤ���̵�������ޤ�����</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onJ\' , this );" onblur="fncInvalidBigButton( \'offJ\' , this );" onmouseover="fncInvalidBigButton( \'onJ\' , this );" onmouseout="fncInvalidBigButton( \'offJ\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_ja_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';
// (�Ѹ�)
var invalidTable03E = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>The deleted data revives. Does it invalid?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onE\' , this );" onblur="fncInvalidBigButton( \'offE\' , this );" onmouseover="fncInvalidBigButton( \'onE\' , this );" onmouseout="fncInvalidBigButton( \'offE\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_en_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';


// ̵���������ѥѥ����� [����]��̵����������ͭ���ˤʤ�ǡ�����̵�����
// (���ܸ�)
var invalidTable04J = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>���褹��ǡ�����¸�ߤ��ޤ���̵�������ޤ�����</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onJ\' , this );" onblur="fncInvalidBigButton( \'offJ\' , this );" onmouseover="fncInvalidBigButton( \'onJ\' , this );" onmouseout="fncInvalidBigButton( \'offJ\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_ja_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';
// (�Ѹ�)
var invalidTable04E = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>The data to revitalize does not exist. Does it invalid?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onE\' , this );" onblur="fncInvalidBigButton( \'offE\' , this );" onmouseover="fncInvalidBigButton( \'onE\' , this );" onmouseout="fncInvalidBigButton( \'offE\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_en_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';




















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �����ܥ���ơ��֥�񤭽Ф��⥸�塼��
*
* ���� : �ƽ����ѥƥ�ץ졼�Ȥǻ��Ѥ���[fncChgEtoJ]�ؿ���ǻ��ѡ�
*
* �о� : ���ϥե������
*
* �����ؿ� : [fncChgEtoJ] �ƽ���ؿ���
*            /src/pc/cmn/resultexstr.js
*            /src/po/cmn/resultexstr.js
*            /src/sc/cmn/resultexstr.js
*            /src/so/cmn/resultexstr.js
*            /src/uc/cmn/resultexstr.js
*
* @param [lngCode] : [���ͷ�]   . [0/1]�ο���
* @param [strMode] : [ʸ����] . 'regist' , 'detail' , 'delete' , 'use' ,
*                                 'Invalid01' , 'Invalid02' , 'Invalid03' , 'Invalid04'
*                                 �ν�����ʬ���Ѥΰ�դ�ʸ����
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function fncProcessingOutputModule( strMode , lngCode )
{

	switch( strMode )
	{

		case 'regist':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'REGISTRATION CONFIRM';
				objQuery.innerHTML = registTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '��Ͽ��ǧ';
				objQuery.innerHTML = registTableJ;
			}

			break;


		case 'renew':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'REGISTRATION CONFIRM';
				objQuery.innerHTML = registTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '��Ͽ��ǧ';
				objQuery.innerHTML = registTableJ;
			}

			break;


		case 'detail':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'DETAIL CONFIRM';
				objQuery.innerHTML = detailTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '�ܺٳ�ǧ';
				objQuery.innerHTML = detailTableJ;
			}

			break;


		case 'delete':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'DELETE CONFIRM';
				objQuery.innerHTML = deleteTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '�����ǧ';
				objQuery.innerHTML = deleteTableJ;
			}

			break;


		case 'use':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'DATA UNDER USE';
				objQuery.innerHTML = detailTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '�ǡ���������';
				objQuery.innerHTML = detailTableJ;
			}

			break;


		case 'Invalid01':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'INVALID CONFIRM';
				objQuery.innerHTML = invalidTable01E;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '̵������ǧ';
				objQuery.innerHTML = invalidTable01J;
			}

			break;


		case  'Invalid02':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'INVALID CONFIRM';
				objQuery.innerHTML = invalidTable02E;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '̵������ǧ';
				objQuery.innerHTML = invalidTable02J;
			}

			break;


		case 'Invalid03':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'INVALID CONFIRM';
				objQuery.innerHTML = invalidTable03E;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '̵������ǧ';
				objQuery.innerHTML = invalidTable03J;
			}

			break;


		case 'Invalid04':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'INVALID CONFIRM';
				objQuery.innerHTML = invalidTable04E;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '̵������ǧ';
				objQuery.innerHTML = invalidTable04J;
			}

			break;


		default:
			break;

	}

	return false;
}