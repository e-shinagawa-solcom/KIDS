<!--


//--------------------------------------
// ���� : �־�ǧ�ץܥ��󥤥᡼������
//--------------------------------------
var recognWfBtJ1 = '<img onmouseover="fncRecognizeButton( \'onJ\' , this );" onmouseout="fncRecognizeButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + recognwfJ1 + '" width="72" height="20" border="0">';

var recognWfBtE1 = '<img onmouseover="fncRecognizeButton( \'onE\' , this );" onmouseout="fncRecognizeButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + recognwfE1 + '" width="72" height="20" border="0">';



//--------------------------------------
// ���� : ����ǧ�ץܥ��󥤥᡼������
//--------------------------------------
var denyWfBtJ1 = '<img onmouseover="fncDenyButton( \'onJ\' , this );" onmouseout="fncDenyButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + denywfJ1 + '" width="72" height="20" border="0">';

var denyWfBtE1 = '<img onmouseover="fncDenyButton( \'onE\' , this );" onmouseout="fncDenyButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + denywfE1 + '" width="72" height="20" border="0">';



//--------------------------------------
// ���� : �־�ǧ��áץܥ��󥤥᡼������
//--------------------------------------
var cancelWfBtJ1 = '<img onmouseover="fncCancelButton( \'onJ\' , this );" onmouseout="fncCancelButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + cancelwfJ1 + '" width="72" height="20" border="0">';

var cancelWfBtE1 = '<img onmouseover="fncCancelButton( \'onE\' , this );" onmouseout="fncCancelButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + cancelwfE1 + '" width="72" height="20" border="0">';



//--------------------------------------
// ���� : �ֽ����ץܥ��󥤥᡼������
//--------------------------------------
var processWfBtJ1 = '<img onmouseover="fncProcessButton( \'onJ\' , this );" onmouseout="fncProcessButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + processwfJ1 + '" width="72" height="20" border="0">';

var processWfBtE1 = '<img onmouseover="fncProcessButton( \'onE\' , this );" onmouseout="fncProcessButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + processwfE1 + '" width="72" height="20" border="0">';



//--------------------------------------
// ���� : ���Ĥ���ץܥ��󥤥᡼������
//--------------------------------------
var closeWfBtJ1 = '<a href="#" onclick="window.close();return false;"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0"></a>';

var closeWfBtE1 = '<a href="#" onclick="window.close();return false;"><img onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0"></a>';





//--------------------------------------
// ���� : �񤭽Ф�
//--------------------------------------
function fncWfQuery( strMode , lngLanguageCode , strRecognAction , strDenyAction , strCancelAction , strProcessAction )
{

	// �Ѹ�
	if( lngLanguageCode == 0 )
	{

		// ���ǥ��å�
		if( strMode == 'edit' )
		{
			if( strRecognAction != '' )
			{
				RecognBt.innerHTML = '<a href="' + strRecognAction + '">' + recognWfBtE1 + '</a>&nbsp;&nbsp;';
			}
			else
			{
				RecognBt.outerHTML = '';
			}

			if( strDenyAction != '' )
			{
				DenyBt.innerHTML = '<a href="' + strDenyAction + '">' + denyWfBtE1 + '</a>&nbsp;&nbsp;';
			}
			else
			{
				DenyBt.outerHTML = '';
			}

			if( strCancelAction != '' )
			{
				CancelBt.innerHTML = '<a href="' + strCancelAction + '">' + cancelWfBtE1 + '</a>&nbsp;&nbsp;';
			}
			else
			{
				CancelBt.outerHTML = '';
			}

			RemarkSet.style.visibility = 'hidden';

			ControlTitle.innerText = 'Select processing';
		}

		// ��ǧ
		else if( strMode == 'confirm' )
		{
			if( strProcessAction != '' )
			{
				ProcessBt.innerHTML = '<a href="' + strProcessAction + '">' + processWfBtE1 + '</a>&nbsp;&nbsp;';
			}
			else
			{
				ProcessBt.outerHTML = '';
			}

			RemarkSet.style.visibility = 'visible';
			Column7.innerText = 'Remark';

			ControlTitle.innerText = 'Process';
		}

		// �ܺٳ�ǧ
		else if( strMode == 'detail' )
		{

			RemarkSet.style.visibility = 'hidden';

			ControlTitle.innerText = 'DETAIL';
		}


		CloseBt.innerHTML = closeWfBtE1;

		Column0.innerText = 'Application date';
		Column1.innerText = 'Issue information';
		Column2.innerText = 'Applicant';
		Column3.innerText = 'Input person';
		Column4.innerText = 'Issue information';
		Column5.innerText = 'Limit date';
		Column6.innerText = 'State';

	}


	// ���ܸ�
	else if( lngLanguageCode == 1 )
	{

		// ���ǥ��å�
		if( strMode == 'edit' )
		{
			if( strRecognAction != '' )
			{
				RecognBt.innerHTML = '<a href="' + strRecognAction + '">' + recognWfBtJ1 + '</a>&nbsp;&nbsp;';
			}
			else
			{
				RecognBt.outerHTML = '';
			}

			if( strDenyAction != '' )
			{
				DenyBt.innerHTML = '<a href="' + strDenyAction + '">' + denyWfBtJ1 + '</a>&nbsp;&nbsp;';
			}
			else
			{
				DenyBt.outerHTML = '';
			}

			if( strCancelAction != '' )
			{
				CancelBt.innerHTML = '<a href="' + strCancelAction + '">' + cancelWfBtJ1 + '</a>&nbsp;&nbsp;';
			}
			else
			{
				CancelBt.outerHTML = '';
			}

			RemarkSet.style.visibility = 'hidden';

			ControlTitle.innerText = '��������';
		}

		// ��ǧ
		else if( strMode == 'confirm' )
		{
			if( strProcessAction != '' )
			{
				ProcessBt.innerHTML = '<a href="' + strProcessAction + '">' + processWfBtJ1 + '</a>&nbsp;&nbsp;';
			}
			else
			{
				ProcessBt.outerHTML = '';
			}

			RemarkSet.style.visibility = 'visible';
			Column7.innerText = '����';

			ControlTitle.innerText = '������ǧ';
		}

		// �ܺٳ�ǧ
		else if( strMode == 'detail' )
		{

			RemarkSet.style.visibility = 'hidden';

			ControlTitle.innerText = '�ܺٳ�ǧ';
		}


		CloseBt.innerHTML = closeWfBtJ1;

		Column0.innerText = '������';
		Column1.innerText = '�Ʒ����';
		Column2.innerText = '������';
		Column3.innerText = '���ϼ�';
		Column4.innerText = '��ǧ��';
		Column5.innerText = '��λ����';
		Column6.innerText = '����';

	}

	return false;
}


//-->