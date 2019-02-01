<!--


//--------------------------------------
// 解説 : 「承認」ボタンイメージ生成
//--------------------------------------
var recognWfBtJ1 = '<img onmouseover="fncRecognizeButton( \'onJ\' , this );" onmouseout="fncRecognizeButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + recognwfJ1 + '" width="72" height="20" border="0">';

var recognWfBtE1 = '<img onmouseover="fncRecognizeButton( \'onE\' , this );" onmouseout="fncRecognizeButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + recognwfE1 + '" width="72" height="20" border="0">';



//--------------------------------------
// 解説 : 「否認」ボタンイメージ生成
//--------------------------------------
var denyWfBtJ1 = '<img onmouseover="fncDenyButton( \'onJ\' , this );" onmouseout="fncDenyButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + denywfJ1 + '" width="72" height="20" border="0">';

var denyWfBtE1 = '<img onmouseover="fncDenyButton( \'onE\' , this );" onmouseout="fncDenyButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + denywfE1 + '" width="72" height="20" border="0">';



//--------------------------------------
// 解説 : 「承認取消」ボタンイメージ生成
//--------------------------------------
var cancelWfBtJ1 = '<img onmouseover="fncCancelButton( \'onJ\' , this );" onmouseout="fncCancelButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + cancelwfJ1 + '" width="72" height="20" border="0">';

var cancelWfBtE1 = '<img onmouseover="fncCancelButton( \'onE\' , this );" onmouseout="fncCancelButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + cancelwfE1 + '" width="72" height="20" border="0">';



//--------------------------------------
// 解説 : 「処理」ボタンイメージ生成
//--------------------------------------
var processWfBtJ1 = '<img onmouseover="fncProcessButton( \'onJ\' , this );" onmouseout="fncProcessButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + processwfJ1 + '" width="72" height="20" border="0">';

var processWfBtE1 = '<img onmouseover="fncProcessButton( \'onE\' , this );" onmouseout="fncProcessButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + processwfE1 + '" width="72" height="20" border="0">';



//--------------------------------------
// 解説 : 「閉じる」ボタンイメージ生成
//--------------------------------------
var closeWfBtJ1 = '<a href="#" onclick="window.close();return false;"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0"></a>';

var closeWfBtE1 = '<a href="#" onclick="window.close();return false;"><img onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0"></a>';





//--------------------------------------
// 解説 : 書き出し
//--------------------------------------
function fncWfQuery( strMode , lngLanguageCode , strRecognAction , strDenyAction , strCancelAction , strProcessAction )
{

	// 英語
	if( lngLanguageCode == 0 )
	{

		// エディット
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

		// 確認
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

		// 詳細確認
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


	// 日本語
	else if( lngLanguageCode == 1 )
	{

		// エディット
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

			ControlTitle.innerText = '処理選択';
		}

		// 確認
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
			Column7.innerText = '備考';

			ControlTitle.innerText = '処理確認';
		}

		// 詳細確認
		else if( strMode == 'detail' )
		{

			RemarkSet.style.visibility = 'hidden';

			ControlTitle.innerText = '詳細確認';
		}


		CloseBt.innerHTML = closeWfBtJ1;

		Column0.innerText = '申請日';
		Column1.innerText = '案件情報';
		Column2.innerText = '申請者';
		Column3.innerText = '入力者';
		Column4.innerText = '承認者';
		Column5.innerText = '完了期日';
		Column6.innerText = '状態';

	}

	return false;
}


//-->