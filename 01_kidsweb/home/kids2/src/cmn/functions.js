

//@-------------------------------------------------------------------------------------------------------------------
/**
* �ե����복�� : K.I.D.S.�����ƥඦ�̻��Ѵؿ���
*
*
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
* ���� : �ȥ��ؿ�
*
* �о� : ���٤�
*/
//--------------------------------------------------------------------------------------------------------------------
function trim( str )
{
	var strRet, strFinal;

	strTemp = str;

	//LTRIM
	strRet = LTrim(strTemp);
	//RTRIM
	strFinal = RTrim(strRet);

	//���ɽ��
	//document.frmSample.txtWordLen.value = strTemp.length;
	//document.frmSample.txtResult.value = strFinal;
	//document.frmSample.txtResultLen.value = strFinal.length;

	return strFinal;
}
function RTrim(strTemp)
{
	var nLoop = 0;
	var strReturn = strTemp;
	while (nLoop < strTemp.length)
	{
		if ((strReturn.substring(strReturn.length - 1, strReturn.length) == " ") || (strReturn.substring(strReturn.length - 1, strReturn.length) == "��"))
		{
			strReturn = strTemp.substring(0, strTemp.length - (nLoop + 1));
		}
		else
		{
			break;
		}
		nLoop++;
	}
	return strReturn;
}
function LTrim(strTemp)
{
	var nLoop = 0;
	var strReturn = strTemp;
	while (nLoop < strTemp.length)
	{
		if ((strReturn.substring(0, 1) == " ") || (strReturn.substring(0, 1) == "��"))
		{
			strReturn = strTemp.substring(nLoop + 1, strTemp.length);
		}
		else
		{
			break;
		}
		nLoop++;
	}
	return strReturn;
}





//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ���ա����ּ��������ؿ�
*
* �о� : ���٤�
*/
//--------------------------------------------------------------------------------------------------------------------
function fncGetDate( obj )
{
	aryWeek = new Array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );
	objDate = new Date();

	var yy1 = objDate.getYear();
	var yy2 = ( yy1 < 2000 ) ? yy1+1900 : yy1;
	var mm  = objDate.getMonth() + 1;
	var dd  = objDate.getDate();
	var num = objDate.getDay();

	if( mm < 10 ) { mm = '0' + mm; }
	if( dd < 10 ) { dd = '0' + dd; }

	var h = objDate.getHours();
	var m = objDate.getMinutes();
	var s = objDate.getSeconds();

	if( h < 10 ) { h = '0' + h; }
	if( m < 10 ) { m = '0' + m; }
	if( s < 10 ) { s = '0' + s; }

	var date   = yy2 + '/' + mm + '/' + dd;
	var week   = aryWeek[num];
	var time   = h + ':' + m +':' + s;

	var dwt    = date + '&nbsp;' + week + '&nbsp;' + time;

	strDateVars.innerHTML = dwt;

	setTimeout( 'fncGetDate()', 1000 );
}













//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ���ܸ�Ѹ������ѥ����Х��ѿ�
*
* �о� : ���٤�
*
* @param [lngLanguageCode] : [���ͷ�] . COOKIE��
* @param [lngClickCode]    : [���ͷ�] . ����å���
*/
//--------------------------------------------------------------------------------------------------------------------
var lngLanguageCode = 1; //���å�����
var lngClickCode    = 0; //����å���
















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ���ǥ��󥰲��̽����ؿ�
*
* ���� : �ǡ����Υ��ɴ�λ���ˡ����ǥ��󥰲��̤���ɽ���ˤ���ؿ���
*
* �о� : ���٤�
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function Loading()
{

	Preload.style.display = 'none';
	Preload.style.width   = 0;
	Preload.style.height  = 0;

	if( typeof(OverLayer) != 'undefined' )
	{
		OverLayer.style.display = 'none';
	}

}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ���֥������ȤΥꥵ���������ؿ�
*
* ���� : ������ɥ�����ɽ����ǽ�ΰ襵��������
*        ���֥������Ȥ�width,height�ͤ�������
*
* �о� : �ץ������iframe
*
* @param [objId]     : [���֥������ȷ�] . ���֥�������ID
* @param [lngWidth]  : [���ͷ�]         . WIDTH��Ĵ����
* @param [lngHeight] : [���ͷ�]         . HEIGHT��Ĵ����
*
* @event [onload],[onresize] : body
* @event [onload]            : iframe
*/
//--------------------------------------------------------------------------------------------------------------------
function fncObjectResize( objId , lngWidth , lngHeight )
{

	// ������ɥ�����ɽ����ǽ�ΰ�μ���
	var winH = document.body.offsetHeight;
	var winW = document.body.offsetWidth;

	// �ꥵ���� - ��Ĵ����
	objId.style.width  = winW - lngWidth;
	objId.style.height = winH - lngHeight;

	return false;
}

















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ���֥�����ɥ��νŤʤ���ѹ��ؿ�
*
* ���� : �ǡ����Υ��ɴ�λ���ˡ����ǥ��󥰲��̤���ɽ���ˤ���ؿ���
*
* �о� : ���֥�����ɥ�
*
* @param [Znum] : [���ͷ�] . z-index�����
* @param [obj]  : [���֥������ȷ�] . �оݥ��֥�������̾
*
* @event [onclick] : body , �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
var Znum = 8;

function Zchange(obj)
{
	obj.style.zIndex = Znum++;
}


















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ����饤��إ�׵�ǽ�����ɥ��åȴؿ�
*
* ���� : �ƴ������̾�Ǥε�ǽ�����ɤ��������
*        ���줾��Υإ�ץڡ����Υե�����͡���Ȥ��롣
*
* �о� : �إ�ץܥ��󥪥֥�������
*
* �����ؿ� : [fncOpenHelp] ����饤��إ�ץ�����ɥ������ץ�⥸�塼��
*
* @param [g_lngFncCode] : [���ͷ�] . ��ǽ�������ͳ�Ǽ�ѥ����Х��ѿ�
* @param [lngFncCode]   : [���ͷ�] . ��ǽ��������
*
* @event [onclick]      : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
var g_lngFncCode; // ��ǽ�����ɳ�Ǽ�ѥ����Х��ѿ�

function fncSetFncCode( lngFncCode )
{
	// ��ǽ�����ɤ�������
	if( lngFncCode )
	{
		// ��ǽ�����ɤ�����
		g_lngFncCode = lngFncCode;
	}

	// ��ǽ�����ɤ��ʤ����
	else
	{
		// ��ǽ�����ɤ�����(����饤��إ�ץȥåץڡ���)
		g_lngFncCode = 1;
	}

	// �إ�ץ�����ɥ������ץ�
	fncOpenHelp();

	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ����饤��إ�ץ�����ɥ������ץ�⥸�塼��
*
* ���� : �إ�ץ�����ɥ��򳫤���
*
* �о� : [fncSetFncCode] ����饤��إ�׵�ǽ�����ɥ��åȴؿ�
*/
//--------------------------------------------------------------------------------------------------------------------
function fncOpenHelp()
{
	helpW = window.open( '/help/index.html' , 'helpWin' , 'top=10,left=10,width=600,height=500' );
}




















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �ۥ���̾�����ؿ�
*
* ���� : �ۥ���̾��������ơ��إå���ɽ�������롣
*
* �о� : ���٤�
*
* @param [obj]         : �оݥ��֥�������
* @param [strProtocol] : �ץ�ȥ���إå��������ѥ������ѿ�
* @param [strHostname] : �ۥ���̾�����ѥ������ѿ�
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function fncReferer( obj )
{
	var strProtocol = location.protocol;
	var strHostname = location.hostname;

	obj.innerHTML = strProtocol + '//' + strHostname + '/';

	return false;
}























//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ���ٹԥ��顼ɽ���ؿ�
*
* ���� : ���٥ǡ����ɤ߹��߻������ٹԤ˥��顼�����ä�����ɽ�����롣
*
* �о� : [����ȯ����塦����]
*
* @param [g_DetailErrorFlag] : [���ͷ�]   . ���顼��å�����ɽ���ե饰
* @param [strErrorMessage]   : [ʸ����] . ���顼��å�����ʸ����
*/
//--------------------------------------------------------------------------------------------------------------------
var g_DetailErrorFlag = 0;

function fncDetailErrorOpen( strErrorMessage )
{

	if( strErrorMessage != '' )
	{
		window.ErrorMessageFrame.style.visibility = 'visible';
		window.errorWin.ErrMeg.innerText = strErrorMessage;
		g_DetailErrorFlag = 1;
	}

	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ���ٹԥ��顼��ɽ���ؿ�
*
* ���� : ���顼��å�����ɽ���ե饰[g_DetailErrorFlag]��[1]�λ�
*        ���顼��å���������ɽ���ˤ��롣
*
* �о� : [����ȯ����塦����]
*
* @param [g_DetailErrorFlag] : [���ͷ�] . ���顼��å�����ɽ���ե饰
*/
//--------------------------------------------------------------------------------------------------------------------
function fncDetailErrorClose()
{
	if( g_DetailErrorFlag == 1 )
	{
		window.ErrorMessageFrame.style.visibility = 'hidden';
		window.errorWin.ErrMeg.innerText = '';
		g_DetailErrorFlag = 0;
	}

	return false;
}
















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �ü�ʸ��������ɥ�ɽ���ؿ�
*
* ���� : �ü�ʸ��������ɥ���ɽ�����롣
*
* �о� : �ü�ʸ���ܥ���
*
* @param [specialCnt] : [���ͷ�] . �ü�ʸ��������ɥ�ɽ���ե饰
*
* @event [onclick]      : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
var specialCnt = 0;

function fncSpecialChar()
{
	if( specialCnt == 0 )
	{
		SpecialCharFrame.style.visibility = 'visible';
		SpecialBt.innerHTML = specialButton3;
		specialCnt = 1;
	}
	else if( specialCnt == 1 )
	{
		SpecialCharFrame.style.visibility = 'hidden';
		SpecialBt.innerHTML = specialButton1;
		specialCnt = 0;
	}

	return false;
}


















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : tabindex�������
*
* ���� : tabindex�����Υ��֥������Ȥ˰�ư�����롣
*
* �о� : ���ϥե������
*
* @param [objName] : [���֥������ȷ�] . �����襪�֥�������̾
*
* @event [onblur] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncDefaultTabindex( objName )
{
	objName.focus();

	return false;
}
















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �ޥ�����������˥��֥������ȤΥ���ե��ͤ��ѹ�����
*
* �о� : �ܥ��󥪥֥�������
*
* @param [objName] : [���֥������ȷ�] . ���֥�������̾
*
* @event [onmousedown] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncAlphaOn( obj )
{
	obj.style.filter = 'alpha(opacity=50)' ;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �ޥ������å׻��˥��֥������ȤΥ���ե��ͤ��ѹ�����
*
* �о� : �ܥ��󥪥֥�������
*
* @param [objName] : [���֥������ȷ�] . ���֥�������̾
*
* @event [onmouseup] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncAlphaOff( obj )
{
	obj.style.filter = 'alpha(opacity=100)' ;
}
















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �������̾�ǡ�FROM - TO�פβս�ǤΥ��ԡ�����
*
* �о� : ���ϥե������
*
* @param [objFrom] : [���֥������ȷ�] . ���ԡ������֥�������̾
* @param [objTo]   : [���֥������ȷ�] . ���ԡ��襪�֥�������̾
*
* @event [onblur] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncCopyValue( objFrom , objTo )
{
	if( typeof(m_strErrorObjectName) != 'undefined' )
	{
		if( m_strErrorObjectName == '' )
		{
			objTo.value = objFrom.value;
		}
	}

	return false;
}

















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ���ơ������С���ɽ�����Ѳ������ʤ�
*
* �о� : ���٤�
*
* @event [window.document.onmouseover]
*/
//--------------------------------------------------------------------------------------------------------------------
defaultStatus = 'K.I.D.S.';

window.document.onmouseover=onMouseOver;

function onMouseOver(e)
{
	window.status = 'K.I.D.S.';
	return true
}

















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �������٥�Ƚ�������
*
* �о� : ���٤�
*
* @event [window.document.onkeydown]
*/
//--------------------------------------------------------------------------------------------------------------------
window.document.onkeydown=onKeyDown;


g_aryStaffKeyCode   = new Array(); // �����åե�����ɥ��������������������

g_aryRupinKeyCode   = new Array(); // ������ɥ������������������

g_aryUraKIDSKeyCode = new Array(); // ΢KIDS�������������������


var g_strStaffKey   = '38,38,40,40,37,39,37,39,66,65'; // �����åե������������

var g_strRupinKey   = '82,85,80,73,78,51';             // ������ɥ������������

var g_strUraKIDSKey = '85,82,65,75,73,68,83';    // ΢KIDS�������������


function onKeyDown(e)
{

	//------------------------------------------
	// ���� : [BACKSPACE]��������������
	//------------------------------------------
	if( window.event.keyCode == 8 )
	{
		//���Ǥ��Խ��Բ�ǽ�ΰ�ξ��
		if( window.event.srcElement.contentEditable == 'false' )
		{
			return false ;
		}
		//���Ǥ��Խ���ǽ�ΰ�ξ��
		else if( window.event.srcElement.contentEditable == 'true' )
		{
			//�������ä����
			if( document.selection.type == "Control") return false;

			return true ;
		}

		// ���Ǥ� [text][textarea][password][file] �ξ��
		if (window.event.srcElement.type == 'text'     ||
			window.event.srcElement.type  == 'file'     ||
			window.event.srcElement.type  == 'password' ||
			window.event.srcElement.type  == 'textarea' )
		{
			return true ;
		}
		else
		{
			return false ;
		}

		/*
		// ���Ǥ�̤����ξ��
		if (typeof(window.event.srcElement.name) == "undefined")
		{
			return false ;
		}

		// ���Ǥ�°���� [disabled] �ξ��
		if (window.event.srcElement.disabled == true)
		{
			return false ;
		}
		*/
	}



	//------------------------------------------
	// ���� : [FUNCTION]��������������
	//------------------------------------------
	//[F5]���������ɻ�(�����ػ�)
	if( window.event.keyCode == 116 )
	{
		event.keyCode = 0;
		 return false ;
	}

	//[F3]���������ɻ�(��������ɽ���ػ�)
	if( window.event.keyCode == 114 )
	{
		event.keyCode = 0;
		 return false ;
	}

	//[F11]���������ɻ�(������ɽ���ػ�)
	if( window.event.keyCode == 122 )
	{
		event.keyCode = 0;
		 return false ;
	}



	//------------------------------------------
	// ���� : [ctrl]������ʻ���Ƥβ���������
	//------------------------------------------
	// [ctrl] + [r]���������ɻ�(�����ػ�)
	//if( window.event.ctrlKey == true && window.event.keyCode == 82 ) return false ;

	// [ctrl] + [e]���������ɻ�(��������ɽ���ػ�)
	if( window.event.ctrlKey == true && window.event.keyCode == 69 ) return false ;

	// [ctrl] + [w]���������ɻ�(������ɥ��������ػ�)
	if( window.event.ctrlKey == true && window.event.keyCode == 87 ) return false ;

	// [ctrl] + [i]���������ɻ�(�������������ɽ���ػ�)
	if( window.event.ctrlKey == true && window.event.keyCode == 73 ) return false ;

	// [ctrl] + [o]���������ɻ�(�ե����륪���ץ���̶ػ�)
	if( window.event.ctrlKey == true && window.event.keyCode == 79 ) return false ;

	// [ctrl] + [l]���������ɻ�(�ե����륪���ץ���̶ػ�)
	if( window.event.ctrlKey == true && window.event.keyCode == 76 ) return false ;

	// [ctrl] + [n]���������ɻ�(����������ɥ������ץ�ػ�)
	if( window.event.ctrlKey == true && window.event.keyCode == 78 ) return false ;

	// [ctrl] + [b]���������ɻ�(�������������������ɽ���ػ�)
	if( window.event.ctrlKey == true && window.event.keyCode == 66 ) return false ;

	// [ctrl] + [p]���������ɻ�(�ץ��Ȳ��̶ػ�)
	if( window.event.ctrlKey == true && window.event.keyCode == 80 ) return false ;

	// [ctrl] + [h]���������ɻ�(������̶ػ�)
	if( window.event.ctrlKey == true && window.event.keyCode == 72 ) return false ;

	// [alt] + [��]���������ɻ�(���ػ�)
	if( window.event.altKey == true && window.event.keyCode == 37 ) return false ;

	// [alt] + [��]���������ɻ�(�ʤ�ػ�)
	if( window.event.altKey == true && window.event.keyCode == 39 ) return false ;



	//------------------------------------------
	// ���� : [ENTER]������������(�������������)
	//------------------------------------------
	if( typeof(LoginObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			// ������ܥ���Υ���ե����ѹ�
			fncAlphaOn( document.all.loginbutton );

			// ���������
			LoginCheck();
		}
	}





	// ���������ɤμ���
	var lngKeyCode = window.event.keyCode;



	//------------------------------------------
	// ���� : ΢KIDS����
	//------------------------------------------
	if( g_aryUraKIDSKeyCode.length <= 7 )
	{
		// ����������������˳�Ǽ
		g_aryUraKIDSKeyCode.push( lngKeyCode );

		// �ǥХå�
		//alert( lngKeyCode );
	}

	// [s]�����������ξ��
	if( window.event.keyCode == 83 )
	{
		// �������������ξ��
		if( g_aryUraKIDSKeyCode == g_strUraKIDSKey )
		{
			// ΢KIDS����
			alert( '���饭�å�' );

			// ����������������ν����
			g_aryUraKIDSKeyCode = new Array();
		}
		// �嵭���������ʳ��ξ��
		else
		{
			// ����������������ν����
			g_aryUraKIDSKeyCode = new Array();
		}
	}


/*
	//------------------------------------------
	// ���� : �����åե�����ɥ������ץ����
	//------------------------------------------
	if( g_aryStaffKeyCode.length <= 10 )
	{
		// ����������������˳�Ǽ
		g_aryStaffKeyCode.push( lngKeyCode );
	}

	// [a]�����������ξ��
	if( window.event.keyCode == 65 )
	{
		// �������������ξ��
		if( g_aryStaffKeyCode == g_strStaffKey )
		{
			// �����åեڡ��������ץ�
			fncStaffMatrix();

			// ����������������ν����
			g_aryStaffKeyCode = new Array();
		}
		// �嵭���������ʳ��ξ��
		else
		{
			// ����������������ν����
			g_aryStaffKeyCode = new Array();
		}
	}
*/


/*
	//------------------------------------------
	// ���� : ������ɺ�������
	//------------------------------------------
	if( g_aryRupinKeyCode.length <= 6 )
	{
		// ����������������˳�Ǽ
		g_aryRupinKeyCode.push( lngKeyCode );
	}

	// [3]�����������ξ��
	if( window.event.keyCode == 51 )
	{
		// �������������ξ��
		if( g_aryRupinKeyCode == g_strRupinKey )
		{
			if( typeof(BeepSound) != 'undefined' )
			{
				// ������ɺ���
				BeepSound.src = '/error/lupin.wav';
			}

			// ����������������ν����
			g_aryRupinKeyCode = new Array();
		}
		// �嵭���������ʳ��ξ��
		else
		{
			// ����������������ν����
			g_aryRupinKeyCode = new Array();
		}
	}
*/


	//------------------------------------------
	// ���� : ���ʸ���[ENTER]��������������
	//------------------------------------------
	if( typeof(PSearchObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			GoResult( window.PS , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
			setCookie( 'ProductSearch' , window.PS );
		}
	}
	//------------------------------------------
	// ���� : ���Ѹ�������[ENTER]��������������
	//------------------------------------------
	if( typeof(ESSearchObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			GoResult( window.PS , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
			setCookie( 'EstimateSearch' , window.PS );
		}
	}
	//------------------------------------------
	// ���� : ȯ����[ENTER]��������������
	//------------------------------------------
	if( typeof(POSearchObject) != 'undefined' )
	{
		if( m_lngErrorFlag == 0 )
		{
			if( window.event.keyCode == 13 || window.event.keyCode == 14 )
			{
				GoResult( window.PS , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
				setCookie( 'PurchaseSearch' , window.PS );
			}
		}
	}
	//------------------------------------------
	// ���� : ��������[ENTER]��������������
	//------------------------------------------
	if( typeof(PCSearchObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			GoResult( window.PS , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
			setCookie( 'PurchaseControlSearch' , window.PS );
		}
	}
	//------------------------------------------
	// ���� : ������[ENTER]��������������
	//------------------------------------------
	if( typeof(SOSearchObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			GoResult( window.PS , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
			setCookie( 'ReceiveSearch' , window.PS );
		}
	}
	//------------------------------------------
	// ���� : ��帡��[ENTER]��������������
	//------------------------------------------
	if( typeof(SCSearchObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			GoResult( window.PS , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
			setCookie( 'SalesSearch' , window.PS );
		}
	}
	//------------------------------------------
	// ���� : ����ե�����[ENTER]��������������
	//------------------------------------------
	if( typeof(WFSearchObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			GoResult( window.PS , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
			setCookie( 'WorkflowSearch' , window.PS );
		}
	}
	//------------------------------------------
	// ���� : ���ʲ����񸡺�[ENTER]��������������
	//------------------------------------------
	if( typeof(PListSearchObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			GoResult( window.PS , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
		}
	}
	//------------------------------------------
	// ���� : ȯ��񸡺�[ENTER]��������������
	//------------------------------------------
	if( typeof(POListSearchObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			GoResult( window.PS , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
		}
	}
	//------------------------------------------
	// ���� : ���Ѹ����񸡺�[ENTER]��������������
	//------------------------------------------
	if( typeof(ESListSearchObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			GoResult( window.PS , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
		}
	}
	//------------------------------------------
	// ���� : �桼��������[ENTER]��������������
	//------------------------------------------
	if( typeof(UCSearchObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			GoResult( window.PS , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
			setCookie( 'UserSearch' , window.PS );
		}
	}




	//------------------------------------------
	// ���� : ��ҥޥ�������[ENTER]��������������
	//------------------------------------------
	if( typeof(COMasterSearchObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			GoResult( window.form1 , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
		}
	}
	//------------------------------------------
	// ���� : ���롼�ץޥ�������[ENTER]��������������
	//------------------------------------------
	if( typeof(GMasterSearchObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			GoResult( window.form1 , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
		}
	}
	//------------------------------------------
	// ���� : �̲ߥ졼�ȥޥ�������[ENTER]��������������
	//------------------------------------------
	if( typeof(RMasterSearchObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			GoResult( window.form1 , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
		}
	}
	//------------------------------------------
	// ���� : ����ե�����ޥ�������[ENTER]��������������
	//------------------------------------------
	if( typeof(WFMasterSearchObject) != 'undefined' )
	{
		if( window.event.keyCode == 13 || window.event.keyCode == 14 )
		{
			GoResult( window.form1 , '/result/index.html' , '/result/ifrm.html' , 'ResultIframe' , 'YES' );
		}
	}
}














//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �ɥ�å�����ɥɥ�åפζػ�
*
* �о� : ���٤�
*
* @event [window.document.ondragstart]
*/
//--------------------------------------------------------------------------------------------------------------------
window.document.ondragstart=onDragStart;

function onDragStart(e)
{
	//�Խ���ǽ�ΰ�Τߥɥ�å�����ɥɥ�åפ��Ǥ���
	if ( document.selection.type == "Control" )
	{
		return true ;
	}
	//�嵭�ʳ��ϡ��ɥ�å�����ɥɥ�å׶ػ�
	else
	{
		return false;
	}
}













//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �������̥����å��ܥå����ͤΥ��å�����¸�����ؿ�
*
* ���� : �������̥����å��ܥå����ͤ򥯥å�������¸���롣
*
* �о� : �����å��ܥå���
*
* @param [strCookieName] : [ʸ����]       . ���å���̾
* @param [obj]           : [���֥������ȷ�] . �оݥե�����̾
*
* @event [onclick] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function setCookie( strCookieName, obj )
{
	aryCookie = new Array();
	var j = 0;
	var expdate = new Date ();
	expdate.setTime ( expdate.getTime() + (7*24*60*60*1000));

	for ( i = 0; i < obj.elements.length; i++ )
	{
		//if ( obj.elements[i].type == 'checkbox' )
		if ( obj.elements[i].name == 'ViewColumn[]' )
		{
			if ( obj.elements[i].checked == true )
			{
				aryCookie[j] = obj.elements[i].value + ':checked';
				j++;
			}
			else if ( obj.elements[i].checked == false )
			{
				aryCookie[j] = obj.elements[i].value + ':';
				j++;
			}
		}
	}
	strCookie = aryCookie.join("&");
	document.cookie = strCookieName + '=' + strCookie + ';expires=' + expdate.toGMTString() + ';path=/;';

}



















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �������̥����å��ܥå����Ͱ��ƥ��ꥢ�����ؿ�
*
* ���� : �������̥����å��ܥå����ͤ���Ƥ˥��ꥢ���롣
*
* �о� : �����å��ܥå���
*
* @event [onclick] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function CheckResetCnt()
{

	if( typeof(window.Pwin.CheckAll1) != 'undefined' )
	{
		window.Pwin.CheckAll1.innerHTML = offBt;
		window.Pwin.checkcount1 = 0;
	}

	if( typeof(window.Pwin.CheckAll2) != 'undefined' )
	{
		window.Pwin.CheckAll2.innerHTML = offBt;
		window.Pwin.checkcount2 = 0;
	}
}
















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ��Ͽ��ǧ�����ѥ�����쥯�ȴؿ�
*
* ���� : ��Ͽ��ǧ���˥���������ɽ�������뤿��Υ�����쥯���Ѵؿ���
*        [strCheckName]���ͤ�[1]�ξ���[fncGetRegistrationDataModule]��ƤӽФ���
*
* �о� : ��Ͽ�ѥƥ�ץ졼��
*
* �����ؿ� : [fncGetRegistrationDataModule] ��Ͽ��ǧ�����ѥ�������ɽ���⥸�塼��
*
* @param [objFrmA]         : [���֥������ȷ�] . �ե�������Υ��֥�������̾
* @param [objFrmB]         : [���֥������ȷ�] . �ե�����¤Υ��֥�������̾
* @param [strIfrmParent]   : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������URL
* @param [strIfrmChild]    : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������URL(Iframe)
* @param [strIfrmStyleId]  : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������ Iframe��ID
* @param [strScroll]       : [ʸ����]       . Iframe�ǥ�������ε��ġ��Ե���
* @param [lngLanguageCode] : [���ͷ�]         . ��󥲡�����������
* @param [strCheckName]    : [���ͷ�]         . [fncGetRegistrationDataModule]�μ¹�Ƚ����
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function fncRegistrationConfirm( objFrmA , objFrmB , strIfrmParent , strIfrmChild , strIfrmStyleId , strScroll , lngLanguageCode , strCheckName, strFunction )
{

	// ��Ͽ�ǡ���������ʾ��
	if( strCheckName == 1 )
	{
		// ���������ƽХ⥸�塼��μ¹�
		fncGetRegistrationDataModule( objFrmA , objFrmB , strIfrmParent , strIfrmChild , strIfrmStyleId , strScroll , lngLanguageCode, strFunction );
	}

	return false;

}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ��Ͽ��ǧ�����ѥ�������ɽ���⥸�塼��
*
* ���� : ��Ͽ��ǧ���˥���������ɽ�������뤿��Υ⥸�塼�롣
*        ����[aryFrm]�˥ե��������Ǥ��ͤ����������������ɽ�����롣
*
* @param [objFrmA]         : [���֥������ȷ�] . �ե�������Υ��֥�������̾
* @param [objFrmB]         : [���֥������ȷ�] . �ե�����¤Υ��֥�������̾
* @param [strIfrmParent]   : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������URL
* @param [strIfrmChild]    : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������URL(Iframe)
* @param [strIfrmStyleId]  : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������ Iframe��ID
* @param [strScroll]       : [ʸ����]       . Iframe�ǥ�������ε��ġ��Ե���
* @param [lngLanguageCode] : [���ͷ�]         . ��󥲡�����������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncGetRegistrationDataModule( objFrmA , objFrmB , strIfrmParent , strIfrmChild , strIfrmStyleId , strScroll , lngLanguageCode, strFunction )
{

	// �����������
	aryFrm    = new Array();
	aryFrm[0] = new Array(); // Iframe�Υѥ�᡼������Ǽ��
	aryFrm[1] = new Array(); // �ե��������NAME���ͳ�Ǽ��
	aryFrm[2] = new Array(); // �ե��������VALUE���ͳ�Ǽ��
	aryFrm[3] = new Array(); // �ե�����¡�NAME���ͳ�Ǽ��
	aryFrm[4] = new Array(); // �ե�����¡�VALUE���ͳ�Ǽ��


	// �����ͤ�����
	aryFrm[0][0] = strIfrmChild;
	aryFrm[0][1] = strIfrmStyleId;
	aryFrm[0][2] = strScroll;
	aryFrm[0][3] = lngLanguageCode;


	var j = 0;
	var k = 0;


	// �ե�����A�Υǡ������������
	if( objFrmA != '' )
	{
		for ( i = 0; i < objFrmA.elements.length; i++ )
		{

				aryFrm[1][j] = objFrmA.elements[i].name;
				aryFrm[2][j] = fncCheckReplaceString( objFrmA.elements[i].value );
				j++;

		}
	}

	// �ե�����B�Υǡ������������
	if( objFrmB != '' )
	{
		for ( i = 0; i < objFrmB.elements.length; i++ )
		{

				aryFrm[3][k] = objFrmB.elements[i].name;
				aryFrm[4][k] = fncCheckReplaceString( objFrmB.elements[i].value );
				k++;

		}
	}


	switch( strFunction )
	{
		case "ES":
			// ���������˽���
			retVal = window.showModalDialog( strIfrmParent , aryFrm , "dialogHeight:700px; dialogWidth:1011px; center:yes; status:no; edge:raised; help:no; scroll:no;" );
			break;

		default:
			// ���������˽���
			retVal = window.showModalDialog( strIfrmParent , aryFrm , "dialogHeight:580px; dialogWidth:970px; center:yes; status:no; edge:raised; help:no; scroll:no;" );
			break;
	}



	// Return Value����
	if( retVal )
	{
		var i;
		var strRef	= "";

		for( i=0; i<retVal.length; i++ )
		{
			strRef	+= retVal[i];
		}

		// ��ư
		location.href	= strRef;

		// ��ư
//		location.href = retVal[0] + retVal[1];
	}


	return false;

}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ��Ͽ��ǧ�����Ѽ����Ѥߥǡ���Ÿ���Ѵؿ�
*
* ���� : [fncGetRegistrationDataModule]���������줿����ǡ��������������Ǻ�Ÿ���������֥ߥåȤ򤹤롣
*
* �о� : ��������������쥯���ѥƥ�ץ졼��
*
* @param [objFrm]    : [���֥������ȷ�] . ������쥯�ȥե�����̾
* @param [strAction] : [ʸ����]       . ���֥ߥå���URL
* @param [objLayA]   : [���֥������ȷ�] . �����Ѥߥǡ���Ÿ���ѥ��֥�������̾��
* @param [objLayB]   : [���֥������ȷ�] . �����Ѥߥǡ���Ÿ���ѥ��֥�������̾��
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function fncSetArgsforRegistration( objFrm , strAction , objLayA , objLayB )
{

	var aryArgs = window.parent.g_aryArgs;
	var aryInner1 = new Array();
	var aryInner2 = new Array();


	if( window.parent.g_DialogLoadFlag ) return ;


	if( objLayA )
	{
		for( i = 0; i < aryArgs[1].length; i++ )
		{
			aryInner1[i] = '<input type="hidden" name="' + aryArgs[1][i] + '" value="' + aryArgs[2][i] + '">';
		}

		objLayA.innerHTML = aryInner1.join( '\n' );

	}


	if( objLayB )
	{
		for( i = 0; i < aryArgs[3].length; i++ )
		{
			aryInner2[i] = '<input type="hidden" name="' + aryArgs[3][i] + '" value="' + aryArgs[4][i] + '">';
		}

		objLayB.innerHTML = aryInner2.join( '\n' );

	}


	objFrm.action = strAction;

	objFrm.submit();

	// ���ǥ��󥰲��̤���ɽ��
	fncShowHidePreload( 1 );

	window.parent.g_DialogLoadFlag = true;

	return false;
}


















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ȯ����ֽ����ؿ�
*
* ���� : ȯ����֤�ʣ�������ǽ�ˤ��롣
*
* �о� : �����ѥƥ�ץ졼��
*
* @param [obj]    : [���֥������ȷ�] . �оݥꥹ�ȥܥå������֥�������
* @param [objHdn] : [���֥������ȷ�] . �Хåե��ѥ��֥�������
*
* @event [onchange] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncAryOrderStatusCode( obj , objBuffer )
{
	aryOrderStatus = new Array();

	for( i = 0; i < obj.options.length; i++ )
	{
		if( obj.options[i].selected )
		{
			aryOrderStatus.push( obj.options[i].value );
		}
	}

	objBuffer.value = aryOrderStatus;

	// �ǥХå�
	//alert( objBuffer.value );
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �������ɽ���Ѵؿ�
*
* ���� : ������̤����������ɽ�������뤿��δؿ���
*        ����[args]�˥ե��������Ǥ��ͤ����������������ɽ�����롣
*
* �о� : �����ѥƥ�ץ졼��
*
* @param [obj1]      : [���֥������ȷ�] . �ե�����Υ��֥�������̾
* @param [obj2]      : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������URL
* @param [strUrl]    : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������URL(Iframe)
* @param [strID]     : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������ Iframe��ID
* @param [strScroll] : [ʸ����]       . Iframe�ǥ�������ε��ġ��Ե���
*
* @event [onclick] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function GoResult( obj1 , obj2 , strUrl , strID , strScroll )
{
	var j = 0;
	var k = 0;

	args = new Array();
	args[0] = new Array();
	args[1] = new Array();
	args[2] = new Array();
	args[3] = new Array();
	args[4] = new Array();

	args[0][0] = strUrl;
	args[0][1] = strID;
	args[0][2] = strScroll;


	///// other name /////
	for ( i = 0; i < obj1.elements.length; i++ )
	{

		if( typeof(obj1.elements[i]) == 'undefined' )
		{
			continue;
		}

		if ( obj1.elements[i].type == 'checkbox' )
		{
			if ( obj1.elements[i].checked == true )
			{
				args[1][j] = obj1.elements[i].name;
				args[2][j] = obj1.elements[i].value;
				j++;
			}
			continue;
		}

		// �����ڡ���[����]���ܰʳ�
		if( obj1.elements[i].name != 'OrderStatusObject' )
		{
			args[3][k] = obj1.elements[i].name;
			args[4][k] = obj1.elements[i].value;
			k++;
		}

	}



	//alert(args[4].join('\n'));

	//alert(args[2][1]);return;


	//retVal = window.showModalDialog( obj2 , args , "dialogHeight:530px;dialogWidth:1011px;center:yes;status:no;edge:raised;help:no;scroll:no;" );
	retVal = window.showModalDialog( obj2 , args , "dialogHeight:700px;dialogWidth:1011px;center:yes;status:no;edge:raised;help:no;scroll:no;" );

	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �������ɽ���Ѽ����Ѥߥǡ���Ÿ���ؿ�
*
* ���� : [GoResult]���������줿����ǡ��������������Ǻ�Ÿ���������֥ߥåȤ򤹤롣
*
* �о� : ��������������쥯���ѥƥ�ץ졼��
*
* @param [objFrm] : [���֥������ȷ�] . ������쥯�ȥե�����̾
* @param [obj2]   : [���֥������ȷ�] . �����Ѥߥǡ���Ÿ���ѥ��֥�������̾��
* @param [obj3]   : [���֥������ȷ�] . �����Ѥߥǡ���Ÿ���ѥ��֥�������̾��
* @param [strUrl] : [ʸ����] . ���֥ߥå���URL
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function fncSetArgs( ObjFrm , obj2 , obj3 , strUrl )
{

	var aryArgs = window.parent.g_aryArgs;
	var aryInner1 = new Array();
	var aryInner2 = new Array();


	if( window.parent.g_DialogLoadFlag ) return ;


	for( i = 0; i < aryArgs[1].length; i++ )
	{
		aryInner1[i] = '<input type="hidden" name="' + aryArgs[1][i] + '" value="' + aryArgs[2][i] + '">';
	}

	obj2.innerHTML = aryInner1.join( '\n' );


	for( i = 0; i < aryArgs[3].length; i++ )
	{
		aryInner2[i] = '<input type="hidden" name="' + aryArgs[3][i] + '" value="' + aryArgs[4][i] + '">';
	}

	obj3.innerHTML = aryInner2.join( '\n' );


	//alert( obj2.innerHTML );
	//alert( obj3.innerHTML );


	ObjFrm.action = strUrl;
	//ObjFrm.target = '_blank';
	//alert(ObjFrm.name);

	//alert('['+ ObjFrm.action +']');

	ObjFrm.submit();

	//var doc = document.body.createTextRange();
	//alert(doc.htmlText);

	window.parent.g_DialogLoadFlag = true;

	return false;
}












//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ����ե��ܺٲ��̥�������ɽ���Ѵؿ�
*
* �о� : ��������ѥƥ�ץ졼��
*
* @param [strUrl]          : [ʸ����]       . ���֥ߥå���URL
* @param [ObjFrm]          : [���֥������ȷ�] . �ե�����Υ��֥�������̾
* @param [strID]           : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������ Iframe��ID
* @param [strScroll]       : [ʸ����]       . Iframe�ǥ�������ε��ġ��Ե���
* @param [lngLanguageCode] : [���ͷ�]         . ��󥲡�����������
* @param [strMode]         : [ʸ����]       . �����⡼�ɤ�ʸ����
* @param [width]           : [���ͷ�]         . ������ɥ��β���
* @param [height]          : [���ͷ�]         . ������ɥ��ν���
* @param [xops]            : [���ͷ�]         . ������ɥ���X��ɸ
* @param [ypos]            : [���ͷ�]         . ������ɥ���Y��ɸ
*
* @event [onclick] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncShowWfDialogCommon( strUrl, ObjFrm, strID, strScroll, lngLanguageCode, strMode, width, height, xpos, ypos )
{
	args    = new Array();
	args[0] = new Array();

	args[0][0] = strUrl;          // �¹���URL
	args[0][1] = strID;           // IFrame�Υ���������ID
	args[0][2] = strScroll;       // IFrame��������ε��ġ��Ե���
	args[0][3] = lngLanguageCode; // $lngLanguageCode
	args[0][4] = strMode;         // 'detail' �ޤ��� 'delete'

	// dialogWidth:696px; dialogHeight:679px;

	var status  = 'dialogWidth:' + width + 'px; dialogHeight:' + height + 'px;';
	status     += 'dialogLeft:'+ xpos + 'px;dialogTop:' + ypos + 'px;';
	status     += 'center:yes;status:no;edge:raised;help:no;scroll:no;';

	window.showModelessDialog( '/result/common.html' , args , status );
}










//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ������̲��̾夫��Υ�������ɽ���Ѷ��̴ؿ�
*
* ���� : ������̲��̾夫�����������ɽ�������뤿��δؿ���
*        ����[args]�˥ե��������Ǥ��ͤ����������������ɽ�����롣
*        [�ܺ�][���][̵����]����ɽ���˻��ѡ�
*
* �о� : ��������ѥƥ�ץ졼��
*
* @param [strUrl]          : [ʸ����]       . ���֥ߥå���URL
* @param [ObjFrm]          : [���֥������ȷ�] . �ե�����Υ��֥�������̾
* @param [strID]           : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������ Iframe��ID
* @param [strScroll]       : [ʸ����]       . Iframe�ǥ�������ε��ġ��Ե���
* @param [lngLanguageCode] : [���ͷ�]         . ��󥲡�����������
* @param [strMode]         : [ʸ����]       . �����⡼�ɤ�ʸ����
*
* @event [onclick] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncShowDialogCommon( strUrl , ObjFrm , strID , strScroll , lngLanguageCode , strMode )
{
	args = new Array();
	args[0] = new Array();

	args[0][0] = strUrl; // �¹���URL
	args[0][1] = strID; // IFrame�Υ���������ID
	args[0][2] = strScroll; // IFrame��������ε��ġ��Ե���
	args[0][3] = lngLanguageCode; // $lngLanguageCode
	args[0][4] = strMode; // 'detail' �ޤ��� 'delete'


	retval = window.showModalDialog( '/result/common.html' , args , "dialogHeight:679px;dialogWidth:696px;center:yes;status:no;edge:raised;help:no;scroll:no;" );

	if( retval )
	{
		// ���ǥ��󥰲��̤�ɽ��
		fncShowHidePreload( 0 );

		ObjFrm.submit();
	}
	//onunload="window.returnValue=true;" �����������
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ������̲��̾夫��ν�����������ɽ���Ѵؿ�
*
* ���� : ������̲��̾夫�齤������������ɽ�������뤿��δؿ���
*        ����[args]�˥ե��������Ǥ��ͤ����������������ɽ�����롣
*        [����]����ɽ���˻��ѡ�
*
* �о� : ��������ѥƥ�ץ졼��
*
* @param [strUrl]          : [ʸ����]       . ���֥ߥå���URL
* @param [ObjFrm]          : [���֥������ȷ�] . �ե�����Υ��֥�������̾
* @param [strID]           : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������ Iframe��ID
* @param [strScroll]       : [ʸ����]       . Iframe�ǥ�������ε��ġ��Ե���
* @param [lngLanguageCode] : [���ͷ�]         . ��󥲡�����������
*
* @event [onclick] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncShowDialogRenew( strUrl , ObjFrm , strID , strScroll , lngLanguageCode )
{
	args = new Array();
	args[0] = new Array();

	args[0][0] = strUrl;
	args[0][1] = strID;
	args[0][2] = strScroll;
	args[0][3] = lngLanguageCode;

	//retval = window.showModalDialog( '/result/renew.html' , args , "dialogHeight:580px;dialogWidth:970px;center:yes;status:no;edge:raised;help:no;scroll:no;" );
	retval = window.showModalDialog( '/result/renew.html' , args , "dialogHeight:600px;dialogWidth:970px;center:yes;status:no;edge:raised;help:no;scroll:no;" );

	if( retval )
	{
		// ���ǥ��󥰲��̤�ɽ��
		fncShowHidePreload( 0 );

		ObjFrm.submit();
	}

}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �ޥ��������Ǥθ�����̲��̾夫��Υ�������ɽ���Ѷ��̴ؿ�
*
* ���� : �ޥ��������Ǥθ�����̲��̾夫�����������ɽ�������뤿��δؿ���
*        ����[args]�˥ե��������Ǥ��ͤ����������������ɽ�����롣
*        [�ɲ�][����][���]����ɽ���˻��ѡ�
*
* �о� : ��������ѥƥ�ץ졼��
*
* @param [strUrl]          : [ʸ����]       . ���֥ߥå���URL
* @param [ObjFrm]          : [���֥������ȷ�] . �ե�����Υ��֥�������̾
* @param [strID]           : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������ Iframe��ID
* @param [strScroll]       : [ʸ����]       . Iframe�ǥ�������ε��ġ��Ե���
* @param [lngLanguageCode] : [���ͷ�]         . ��󥲡�����������
* @param [strMode]         : [ʸ����]       . �����⡼�ɤ�ʸ����
*
* @event [onclick] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncShowDialogCommonMaster( strUrl , ObjFrm , strID , strScroll , lngLanguageCode , strMode )
{
	args = new Array();
	args[0] = new Array();

	args[0][0] = strUrl; // �¹���URL
	args[0][1] = strID; // IFrame�Υ���������ID
	args[0][2] = strScroll; // IFrame��������ε��ġ��Ե���
	args[0][3] = lngLanguageCode; // $lngLanguageCode
	args[0][4] = strMode; // 'fix' , 'add' , 'delete'

	retval = window.showModalDialog( '/result/remove_master.html' , args , "dialogHeight:510px;dialogWidth:600px;center:yes;status:no;edge:raised;help:no;scroll:no;" );


	if( retval )
	{
		// ���ǥ��󥰲��̤�ɽ��
		//fncShowHidePreload( 0 );

		ObjFrm.submit();
	}
	//onunload="window.returnValue=true;" �����������
}


















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ���ԡ��ѥƥ����ȥ�󥸼����⥸�塼��
*
* ���� : ����Υ���(ID)��Υƥ����ȥ�󥸤�������롣
*        ����ͤ�[fncDoCopy]�ǸƤӽФ���

* @param [strID]   : [���֥������ȷ�] . �ϰ��ѥ��֥�������̾
*/
//--------------------------------------------------------------------------------------------------------------------
function fncDoCopyModule( strID )
{
	// ����Υ���(ID)��Υƥ����ȥ�󥸤��������
	var doc1 = document.body.createTextRange();

	doc1.moveToElementText(strID);

	return doc1.htmlText;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ����åץܡ��ɤإ��ԡ�����ؿ�
*
* ���� : ���꥿��(id)���ʸ����򥯥�åץܡ��ɤإ��ԡ�����ؿ���
*        [fncDoCopyModule]������ͤ�������ƥ���åץܡ��ɤ˥��ԡ����롣
*
* �о� : ��������ѥƥ�ץ졼��
*
* �����ؿ� : [fncDoCopyModule] ���ԡ��ѥƥ����ȥ�󥸼����⥸�塼��
*
* @param [objBuff1] : [���֥������ȷ�] . ���ԡ��ͳ�Ǽ�Хåե��ѥ��֥�������̾
* @param [strID1]   : [���֥������ȷ�] . �ϰϣ����֥�������̾
* @param [strID2]   : [���֥������ȷ�] . �ϰϣ¥��֥�������̾
*
* @event [onclick] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncDoCopy( objBuff1 , strID1 , strID2 )
{
	// �Хåե��ͤ���
	objBuff1.value = fncDoCopyModule( strID1 ) + fncDoCopyModule( strID2 );

	// �Хåե��ѤΥ��֥������Ȥ���ƥ����ȥ�󥸤����
	var docA = objBuff1.createTextRange();

	// �Хåե��Υƥ����ȥ�󥸤��饯��åץܡ��ɤ˥��ԡ�
	docA.moveStart('character',0);
	docA.moveEnd('character');
	docA.execCommand("copy");
	alert( '����åץܡ��ɤ˥��ԡ����ޤ�����' );

	return true;
}

/*
	����ץ륽����
	<input type="button" value="copy" onclick="fncDoCopy( 'COPYAREA1', copyhidden1 , 'COPYAREA2', copyhidden2 );">
	<!-- �Хåե��ѥ��֥�������1 --><input type="text" value="" name="copyhidden1" style="visibility:hidden">
	<!-- �Хåե��ѥ��֥�������2 --><input type="text" value="" name="copyhidden2" style="visibility:hidden">
	<SPAN ID=COPYAREA1></span>
	<SPAN ID=COPYAREA2></span>
*/


















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ��Ͽ���Υ��顼��å�����ɽ���ؿ�
*
* ���� : ��Ͽ���˥��顼�����ä����ˤ��βս�˥��顼��å�������ɽ������ؿ���
*
* �о� : ��Ͽ�ѥƥ�ץ졼��
*
* @param [obj1] : [���֥������ȷ�] . ���顼��å�����ɽ���ѥ��֥�������̾
* @param [obj2] : [ʸ����] . ���顼��å�����ʸ����
*
* @event [oumouseover] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function ShowComment( obj1 , obj2 )
{
	var nowX = window.event.clientX;
	var nowY = window.event.clientY;

	//alert(event.clientX);
	//alert(event.clientY);
	obj1.style.left = nowX + 10;
	obj1.style.top = nowY;

	if ( obj2 != '' )
	{
		obj1.innerHTML = 'ERROR: ' + obj2;
		obj1.style.visibility = 'visible';
	}

	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ��Ͽ���Υ��顼��å�����ɽ���ؿ�
*
* ���� : ��Ͽ���˥��顼��å�������ɽ������Ƥ�����ˤ������ɽ���ˤ���ؿ���
*
* �о� : ��Ͽ�ѥƥ�ץ졼��
*
* @param [obj1] : [���֥������ȷ�] . ���顼��å�����ɽ���ѥ��֥�������̾
*
* @event [oumouseout] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function HideComment( obj1 )
{
	obj1.style.visibility = 'hidden';

	return false;
}
















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : NEW : ����˥塼ɽ������ɽ���ؿ�
*
* ���� : ����˥塼��ɽ������ɽ���ˤ���ؿ���
*
* �о� : ���٤�
*
* @param [obj1] : [���֥������ȷ�] . ����˥塼�ѥ��֥�������̾(iframe)
* @param [obj2] : [���֥������ȷ�] . ����˥塼�ѥܥ��󥪥֥�������̾
*
* @event [oumouseover] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
var Navicnt = 0;

function fncNaviVisible( obj1 , obj2 )
{
	// ��ɽ��
	if( Navicnt )
	{
		obj1.style.visibility	= 'hidden';
		obj2.innerHTML			= naviButton1;

		Navicnt = 0;
	}
	// ɽ��
	else
	{
		obj1.style.visibility	= 'visible';
		obj2.innerHTML			= naviButton3;

		Navicnt = 1;
	}

	return false;
}



//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ����˥塼ɽ���ؿ�
*
* ���� : ����˥塼��ɽ������ؿ���
*
* �о� : ���٤�
*
* @param [obj1] : [���֥������ȷ�] . ����˥塼�ѥ��֥�������̾(iframe)
* @param [obj2] : [���֥������ȷ�] . ����˥塼�ѥܥ��󥪥֥�������̾
*
* @event [oumouseover] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function NavigationON( obj1 , obj2 )
{
	if ( Navicnt == 0 )
	{
		obj1.style.visibility = 'visible';
		obj2.innerHTML = naviButton3;
		Navicnt = 1;
	}
	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ����˥塼ɽ����ؿ�
*
* ���� : ����˥塼����ɽ���ˤ���ؿ���
*
* �о� : ���٤�
*
* @param [obj1] : [���֥������ȷ�] . ����˥塼�ѥ��֥�������̾(iframe)
* @param [obj2] : [���֥������ȷ�] . ����˥塼�ѥܥ��󥪥֥�������̾
*
* @event [oumouseover] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function NavigationOFF( obj1 , obj2 )
{
	return;

	if ( Navicnt == 1 )
	{
		obj1.style.visibility = 'hidden';
		obj2.innerHTML = naviButton1;
		Navicnt = 0;
	}
	return false;
}




















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ���֥ߥåȴؿ�
*
* ���� : �ե�����Υǡ����򥵥֥ߥåȤ��롣
*
* �о� : ���ϥե������
*
* @param [objFrm] : [���֥������ȷ�] . �ե�����Υ��֥�������̾
*
* @event [onclick] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncSubmitQuery( objFrm )
{
	objFrm.submit();
	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �ե����९�ꥢ�ؿ�
*
* ���� : �ե�����Υǡ����򥯥ꥢ���롣
*
* �о� : ���ϥե������
*
* @param [objFrm] : [���֥������ȷ�] . �ե�����Υ��֥�������̾
*
* @event [onclick] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncResetFrm( objFrm )
{
	objFrm.reset();
	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : iframe��Υե����९�ꥢ�ؿ�
*
* ���� : iframe��Υե�����Υǡ����򥯥ꥢ���롣
*
* �о� : ���ϥե������
*
* @param [obj1] : [���֥������ȷ�] . iframe�Υ��֥�������̾
* @param [obj2] : [���֥������ȷ�] . iframe�Υե����४�֥�������̾
*
* @event [onclick] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncClearIfrm( obj1 , obj2 )
{

	strFrm = 'window.'+ obj1.name + '.' + obj2.name + '.reset();';
	//alert(strFrm);

	window.execScript(strFrm, "JavaScript");

	//ErrMeg.style.visibility = 'hidden' ;
	//ERmark.style.visibility = 'hidden' ;

	return false;
}

















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ������ɻ��Υ����ȥե������������ؿ�
*
* ���� : ������ɻ��˼�ưŪ��Ǥ�դΥ��֥������Ȥ�ե������������롣
*
* �о� : ���ϥե������
*
* @param [obj] : [���֥������ȷ�] . �оݥ��֥�������
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function fncAutoFocus( obj )
{
	if( obj )
	{
		obj.focus();
	}
	else
	{
		return false;
	}
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �ե����������ο��ѹ�&���ϺѤ�ʸ����������ؿ�
*
* ���� : ����ե��������������ϥ��֥������Ȥ��طʿ����ѹ�������
*        ���ϺѤߤ�ʸ���󤬤��ä����Ϥ������������֤ˤ����롣
*
* �о� : ���ϥե������
*
* @param [obj] : [���֥������ȷ�] . �оݥ��֥�������
*
* @event [onfocus] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function chColorOn(obj)
{
	obj.style.backgroundColor = focuscolor;
	obj.select();
	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �ե����������ο��ѹ��ؿ�
*
* ���� : ����ե��������������ϥ��֥������Ȥ��طʿ����ѹ������롣
*
* �о� : ���ϥե������
*
* @param [obj] : [���֥������ȷ�] . �оݥ��֥�������
*
* @event [onblur] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function chColorOff(obj)
{
	obj.style.backgroundColor='#ffffff';
	return false;
}




















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �����åեڡ���������ɥ�ɽ���ؿ�
*
* ���� : �����åեڡ���������ɥ���ɽ�������롣
*
* �о� : ���٤�
*
* @event [onclick] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function fncStaffMatrix()
{
	retVal = window.showModalDialog( '/staff/index.html' , '1' , "dialogHeight:530px; dialogWidth:392px; dialogLeft:5px; dialogTop:5px; edge:raised; center:no; help:no; resizable:no; status:no; unadorned:yes;" );

	return false;
}



























//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �ܥ��󥪥֥������ȤΥ��٥�Ƚ����ؿ�
*
* ���� : �ܥ��󥪥֥������ȤǤγƼ磻�٥�Ȥ��������ؿ���
*
* �о� : �ܥ��󥪥֥�������
*
* @param [obj]     : [���֥������ȷ�] . �оݥ��֥�������
* @param [strMode] : [���֥������ȷ�] . �����⡼��ʸ����
*
* @event [onmouseover],[onmouseout],[onfocus],[onblur] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
///// LIST ADD-DEL BT /////
function AddOff( obj )
{
	obj.src = addbt1;
}

function AddOn( obj )
{
	obj.src = addbt2;
}

function DelOff( obj )
{
	obj.src = delbt1;
}

function DelOn( obj )
{
	obj.src = delbt2;
}




///// LIST UP-DOWN BT /////
function UpOff( obj )
{
	obj.src = upbt1;
}

function UpOn( obj )
{
	obj.src = upbt2;
}

function DownOff( obj )
{
	obj.src = downbt1;
}

function DownOn( obj )
{
	obj.src = downbt2;
}





////////// MSW BUTTON ROLLOVER //////////

function MswOff(obj)
{
	obj.src = mswbt1;
}
function MswOn(obj)
{
	obj.src = mswbt2;
}





///// WF LIST BUTTON /////
function WFlistJOff( obj )
{
	obj.src = listJ1;
}

function WFlistJOn( obj )
{
	obj.src = listJ2;
}

function WFlistEOff( obj )
{
	obj.src = listE1;
}

function WFlistEOn( obj )
{
	obj.src = listE2;
}





///// MAIN MENU BUTTON /////
function MainmenuOff( obj )
{
	obj.src = mainmenubt1;
}

function MainmenuOn( obj )
{
	obj.src = mainmenubt2;
}





///// ONLINE HELP BUTTON /////
function HelpOff( obj )
{
	obj.src = helpbt1;
}

function HelpOn( obj )
{
	obj.src = helpbt2;
}





///// NAVIGATION BUTTON /////
function NaviOff( obj )
{
	obj.src = navibt1;
}

function NaviOn( obj )
{
	obj.src = navibt2;
}









































//--------------------------------------------------
// ���� : [RELOAD]�ܥ���
//--------------------------------------------------
function fncReloadButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = reloadbt1;
			break;

		case 'on':
			obj.src = reloadbt2;
			break;

		default:
			break;
	}
}



//--------------------------------------------------
// ���� : [ENTER]�ܥ���
//--------------------------------------------------
function fncEnterButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = enterbt1;
			break;

		case 'on':
			obj.src = enterbt2;
			break;

		default:
			break;
	}
}






function LoginOff(obj)
{
	obj.src = login1;
}

function LoginOn(obj)
{
	obj.src = login2;
}







////////// [SEARCH]SEARCH BT //////////
function schSchJOff(obj)
{
	obj.src = schSchJ1;
}

function schSchJOn(obj)
{
	obj.src = schSchJ2;
}

function schSchEOff(obj)
{
	obj.src = schSchE1;
}

function schSchEOn(obj)
{
	obj.src = schSchE2;
}


function schClrJOff(obj)
{
	obj.src = schClrJ1;
}

function schClrJOn(obj)
{
	obj.src = schClrJ2;
}

function schClrEOff(obj)
{
	obj.src = schClrE1;
}

function schClrEOn(obj)
{
	obj.src = schClrE2;
}





////////// SELECTW BUTTON //////////

function SelectJOff(obj)
{
	obj.src = sltbtJ1;
}

function SelectJOn(obj)
{
	obj.src = sltbtJ2;
}

function SelectEOff(obj)
{
	obj.src = sltbtE1;
}

function SelectEOn(obj)
{
	obj.src = sltbtE2;
}







////////// OFF ON BT //////////
function OffBt(obj)
{
	obj.src = off;
}

function OnBt(obj)
{
	obj.src = offon;
}









// �ܥ��󥤥᡼���ѹ����̴ؿ�
function fncChangeBtImg( obj, img )
{
	obj.src = img;
}




////////// ADD ROW BUTTON //////////

function AddRJOff(obj)
{
	obj.src = addrowJ1;
}

function AddRJOn(obj)
{
	obj.src = addrowJ2;
}

function AddREOff(obj)
{
	obj.src = addrowE1;
}

function AddREOn(obj)
{
	obj.src = addrowE2;
}



////////// DEL ROW BUTTON //////////

function DelRJOff(obj)
{
	obj.src = delrowJ1;
}

function DelRJOn(obj)
{
	obj.src = delrowJ2;
}

function DelREOff(obj)
{
	obj.src = delrowE1;
}

function DelREOn(obj)
{
	obj.src = delrowE2;
}



////////// COMMIT BUTTON //////////

function CmtJOff(obj)
{
	obj.src = commitJ1;
}

function CmtJOn(obj)
{
	obj.src = commitJ2;
}

function CmtEOff(obj)
{
	obj.src = commitE1;
}

function CmtEOn(obj)
{
	obj.src = commitE2;
}



////////// GRAY REGIST BUTTON //////////

function GrayRegistJOff(obj)
{
	obj.src = grayregistJ1;
}

function GrayRegistJOn(obj)
{
	obj.src = grayregistJ2;
}

function GrayRegistEOff(obj)
{
	obj.src = grayregistE1;
}

function GrayRegistEOn(obj)
{
	obj.src = grayregistE2;
}





////////// BLUE REGIST BUTTON //////////

function BlueRegistJOff(obj)
{
	obj.src = blueregistJ1;
}

function BlueRegistJOn(obj)
{
	obj.src = blueregistJ2;
}

function BlueRegistEOff(obj)
{
	obj.src = blueregistE1;
}

function BlueRegistEOn(obj)
{
	obj.src = blueregistE2;
}


////////// BLUE BACK BUTTON //////////

function BlueBackJOff(obj)
{
	obj.src = bluebackJ1;
}

function BlueBackJOn(obj)
{
	obj.src = bluebackJ2;
}

function BlueBackEOff(obj)
{
	obj.src = bluebackE1;
}

function BlueBackEOn(obj)
{
	obj.src = bluebackE2;
}



////////// BLOWN BACK BUTTON //////////

function GrayBackJOff(obj)
{
	obj.src = graybackJ1;
}

function GrayBackJOn(obj)
{
	obj.src = graybackJ2;
}

function GrayBackEOff(obj)
{
	obj.src = graybackE1;
}

function GrayBackEOn(obj)
{
	obj.src = graybackE2;
}



////////// BLOWN BACK BUTTON //////////

function BlownBackJOff(obj)
{
	obj.src = blownbackJ1;
}

function BlownBackJOn(obj)
{
	obj.src = blownbackJ2;
}

function BlownBackEOff(obj)
{
	obj.src = blownbackE1;
}

function BlownBackEOn(obj)
{
	obj.src = blownbackE2;
}




////////// REGIST BUTTON //////////

function RegJOff(obj)
{
	obj.src = registJ1;
}

function RegJOn(obj)
{
	obj.src = registJ2;
}

function RegEOff(obj)
{
	obj.src = registE1;
}

function RegEOn(obj)
{
	obj.src = registE2;
}









////////// MASTER REGIST BUTTON //////////

function MasterRegJOff(obj)
{
	obj.src = mregistJ1;
}

function MasterRegJOn(obj)
{
	obj.src = mregistJ2;
}

function MasterRegEOff(obj)
{
	obj.src = mregistE1;
}

function MasterRegEOn(obj)
{
	obj.src = mregistE2;
}







////////// MASTER NAVI BUTTON //////////

function MAJOff(obj)
{
	obj.src = mAJ1;
}

function MAJOn(obj)
{
	obj.src = mAJ2;
}

function MAEOff(obj)
{
	obj.src = mAE1;
}

function MAEOn(obj)
{
	obj.src = mAE2;
}


function MBJOff(obj)
{
	obj.src = mBJ1;
}

function MBJOn(obj)
{
	obj.src = mBJ2;
}

function MBEOff(obj)
{
	obj.src = mBE1;
}

function MBEOn(obj)
{
	obj.src = mBE2;
}



////////// CLEAR BUTTON //////////

function ClearJOff(obj)
{
	obj.src = clearJ1;
}

function ClearJOn(obj)
{
	obj.src = clearJ2;
}

function ClearEOff(obj)
{
	obj.src = clearE1;
}

function ClearEOn(obj)
{
	obj.src = clearE2;
}





function fncGrayClearButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = grayclearJ1;
			break;

		case 'onJ':
			obj.src = grayclearJ2;
			break;

		case 'offE':
			obj.src = grayclearE1;
			break;

		case 'onE':
			obj.src = grayclearE2;
			break;
	}
}

////////// SEARCH BUTTON //////////

function SearchJOff(obj)
{
	obj.src = searchJ1;
}

function SearchJOn(obj)
{
	obj.src = searchJ2;
}

function SearchEOff(obj)
{
	obj.src = searchE1;
}

function SearchEOn(obj)
{
	obj.src = searchE2;
}







////////// NAVI USER INFO BUTTON //////////

function UInfoJOff(obj)
{
	obj.src = infoJ1;
}

function UInfoJOn(obj)
{
	obj.src = infoJ2;
}

function UInfoEOff(obj)
{
	obj.src = infoE1;
}

function UInfoEOn(obj)
{
	obj.src = infoE2;
}









////////// NAVI REGIST BUTTON //////////

function RegiJOff(obj)
{
	obj.src = regiJ1;
}

function RegiJOn(obj)
{
	obj.src = regiJ2;
}

function RegiEOff(obj)
{
	obj.src = regiE1;
}

function RegiEOn(obj)
{
	obj.src = regiE2;
}





//-----------------------------------------------------------------------------
function fncChangeBtnImg( objID, strPath )
{
	objID.src = strPath;
}
//-----------------------------------------------------------------------------







////////// NAVI SEARCH BUTTON //////////

function SchJOff(obj)
{
	obj.src = schJ1;
}

function SchJOn(obj)
{
	obj.src = schJ2;
}

function SchEOff(obj)
{
	obj.src = schE1;
}

function SchEOn(obj)
{
	obj.src = schE2;
}








//-----------------------------------------------------------------------------
// ���� : �ᥤ���˥塼�ܥ���Υ��륪���С��������ޥ�������������ؿ�
// ���� : �ƥܥ�����ˡ����ܸ�סֱѸ���Ǥ����
//-----------------------------------------------------------------------------

//---------------------------------------------------
// Ŭ�ѡ��־��ʴ����ץܥ���
//---------------------------------------------------
function fncPButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = pJ1;
			break;

		case 'onJ':
			obj.src = pJ2;
			break;

		case 'offE':
			obj.src = pE1;
			break;

		case 'onE':
			obj.src = pE2;
			break;

		case 'downJ':
			obj.src = pJ3;
			break;

		case 'downE':
			obj.src = pE3;
			break;

		default:
			break;
	}
}



//---------------------------------------------------
// Ŭ�ѡ��ּ�������ץܥ���
//---------------------------------------------------
function fncSOButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = soJ1;
			break;

		case 'onJ':
			obj.src = soJ2;
			break;

		case 'offE':
			obj.src = soE1;
			break;

		case 'onE':
			obj.src = soE2;
			break;

		case 'downJ':
			obj.src = soJ3;
			break;

		case 'downE':
			obj.src = soE3;
			break;

		default:
			break;
	}
}



//---------------------------------------------------
// Ŭ�ѡ���ȯ������ץܥ���
//---------------------------------------------------
function fncPOButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = poJ1;
			break;

		case 'onJ':
			obj.src = poJ2;
			break;

		case 'offE':
			obj.src = poE1;
			break;

		case 'onE':
			obj.src = poE2;
			break;

		case 'downJ':
			obj.src = poJ3;
			break;

		case 'downE':
			obj.src = poE3;
			break;

		default:
			break;
	}
}





//---------------------------------------------------
// Ŭ�ѡ��ֻ��������ץܥ���
//---------------------------------------------------
function fncPCButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = pcJ1;
			break;

		case 'onJ':
			obj.src = pcJ2;
			break;

		case 'offE':
			obj.src = pcE1;
			break;

		case 'onE':
			obj.src = pcE2;
			break;

		case 'downJ':
			obj.src = pcJ3;
			break;

		case 'downE':
			obj.src = pcE3;
			break;

		default:
			break;
	}
}




//---------------------------------------------------
// Ŭ�ѡ����������ץܥ���
//---------------------------------------------------
function fncSCButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = scJ1;
			break;

		case 'onJ':
			obj.src = scJ2;
			break;

		case 'offE':
			obj.src = scE1;
			break;

		case 'onE':
			obj.src = scE2;
			break;

		case 'downJ':
			obj.src = scJ3;
			break;

		case 'downE':
			obj.src = scE3;
			break;

		default:
			break;
	}
}




//---------------------------------------------------
// Ŭ�ѡ��֥���ե��ץܥ���
//---------------------------------------------------
function fncWFButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = wfJ1;
			break;

		case 'onJ':
			obj.src = wfJ2;
			break;

		case 'offE':
			obj.src = wfE1;
			break;

		case 'onE':
			obj.src = wfE2;
			break;

		case 'downJ':
			obj.src = wfJ3;
			break;

		case 'downE':
			obj.src = wfE3;
			break;

		default:
			break;
	}
}





//---------------------------------------------------
// Ŭ�ѡ��֥桼���������ץܥ���
//---------------------------------------------------
function fncUCButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = ucJ1;
			break;

		case 'onJ':
			obj.src = ucJ2;
			break;

		case 'offE':
			obj.src = ucE1;
			break;

		case 'onE':
			obj.src = ucE2;
			break;

		case 'downJ':
			obj.src = ucJ3;
			break;

		case 'downE':
			obj.src = ucE3;
			break;

		default:
			break;
	}
}





//---------------------------------------------------
// Ŭ�ѡ���Ģɼ���ϡץܥ���
//---------------------------------------------------
function fncListoutButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = blownlistoutJ1;
			break;

		case 'onJ':
			obj.src = blownlistoutJ2;
			break;

		case 'offE':
			obj.src = blownlistoutE1;
			break;

		case 'onE':
			obj.src = blownlistoutE2;
			break;

		default:
			break;
	}
}






//---------------------------------------------------
// Ŭ�ѡ���DATA OPEN�ץܥ���
//---------------------------------------------------
function fncDataOpenButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = dataopen1;
			break;

		case 'on':
			obj.src = dataopen2;
			break;

		default:
			break;
	}
}




////////// NAVI DATA EX BUTTON //////////

function DataExJOff(obj)
{
	obj.src = dataexJ1;
}

function DataExJOn(obj)
{
	obj.src = dataexJ2;
}

function DataExEOff(obj)
{
	obj.src = dataexE1;
}

function DataExEOn(obj)
{
	obj.src = dataexE2;
}



////////// NAVI LIST OUTPUT BUTTON //////////

function ListOutJOff(obj)
{
	obj.src = listoutJ1;
}

function ListOutJOn(obj)
{
	obj.src = listoutJ2;
}

function ListOutEOff(obj)
{
	obj.src = listoutE1;
}

function ListOutEOn(obj)
{
	obj.src = listoutE2;
}



//---------------------------------------------------
// Ŭ�ѡ���Ģɼ���ϡץܥ���
//---------------------------------------------------
function fncLISTButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = listoutJ1;
			break;

		case 'onJ':
			obj.src = listoutJ2;
			break;

		case 'offE':
			obj.src = listoutE1;
			break;

		case 'onE':
			obj.src = listoutE2;
			break;

		default:
			break;
	}
}


//---------------------------------------------------
// Ŭ�ѡ��֥ǡ����������ݡ��ȡץܥ���
//---------------------------------------------------
function fncDATAEXButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = dataexJ1;
			break;

		case 'onJ':
			obj.src = dataexJ2;
			break;

		case 'offE':
			obj.src = dataexE1;
			break;

		case 'onE':
			obj.src = dataexE2;
			break;

		default:
			break;
	}
}


//---------------------------------------------------
// Ŭ�ѡ��֥ޥ��������ץܥ���
//---------------------------------------------------
function fncMButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = mstJ1;
			break;

		case 'onJ':
			obj.src = mstJ2;
			break;

		case 'offE':
			obj.src = mstE1;
			break;

		case 'onE':
			obj.src = mstE2;
			break;

		case 'downJ':
			obj.src = mstJ3;
			break;

		case 'downE':
			obj.src = mstE3;
			break;

		default:
			break;
	}
}





//---------------------------------------------------
// Ŭ�ѡ��֥����ƥ�����ץܥ���
//---------------------------------------------------
function fncSYSButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = sys1;
			break;

		case 'on':
			obj.src = sys2;
			break;

		default:
			break;
	}
}





//---------------------------------------------------
// Ŭ�ѡ����������ץܥ���
//---------------------------------------------------
function fncDataClosedButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = dataclosed1;
			break;

		case 'on':
			obj.src = dataclosed2;
			break;

		default:
			break;
	}
}





//---------------------------------------------------
// Ŭ�ѡ�����������ʥӡץܥ���
//---------------------------------------------------
function fncClosedNaviButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = closednaviJ1;
			break;

		case 'onJ':
			obj.src = closednaviJ2;
			break;

		case 'offE':
			obj.src = closednaviE1;
			break;

		case 'onE':
			obj.src = closednaviE2;
			break;

		default:
			break;
	}
}




//---------------------------------------------------
// Ŭ�ѡ���Ģɼ�����ץܥ���
//---------------------------------------------------
function fncListAllButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = listallJ1;
			break;

		case 'on':
			obj.src = listallJ2;
			break;

		default:
			break;
	}
}



//---------------------------------------------------
// Ŭ�ѡ��֥ǡ��������ץܥ���
//---------------------------------------------------
function fncDataAllButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = dataallJ1;
			break;

		case 'on':
			obj.src = dataallJ2;
			break;

		default:
			break;
	}
}

//---------------------------------------------------
//Ŭ�ѡ��ֶⷿ�����ץܥ���
//---------------------------------------------------
function fncMMButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = mmJ1;
			break;

		case 'onJ':
			obj.src = mmJ2;
			break;

		case 'offE':
			obj.src = mmE1;
			break;

		case 'onE':
			obj.src = mmE2;
			break;

		case 'downJ':
			obj.src = mmJ3;
			break;

		case 'downE':
			obj.src = mmE3;
			break;

		default:
			break;
	}
}

//---------------------------------------------------
//Ŭ�ѡ��ֶⷿĢɼ�����ץܥ���
//---------------------------------------------------
function fncMRButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = mrJ1;
			break;

		case 'onJ':
			obj.src = mrJ2;
			break;

		case 'offE':
			obj.src = mrE1;
			break;

		case 'onE':
			obj.src = mrE2;
			break;

		case 'downJ':
			obj.src = mrJ3;
			break;

		case 'downE':
			obj.src = mrE3;
			break;

		default:
			break;
	}
}

////////// MAIN MENU NAVI BUTTON //////////

function MainJOff(obj)
{
	obj.src = mainJ1;
}

function MainJOn(obj)
{
	obj.src = mainJ2;
}

function MainEOff(obj)
{
	obj.src = mainE1;
}

function MainEOn(obj)
{
	obj.src = mainE2;
}



////////// LOGOUT BUTTON //////////

function LogoutOff(obj)
{
	obj.src = logout1;
}

function LogoutOn(obj)
{
	obj.src = logout2;
}



////////// ENGLISH JAPANESE BUTTON //////////

function EtoJ1EOff(obj)
{
	obj.src = etojE1;
}

function EtoJ1EOn(obj)
{
	obj.src = etojE2;
}

function EtoJ1JOff(obj)
{
	obj.src = etojJ1;
}

function EtoJ1JOn(obj)
{
	obj.src = etojJ2;
}



////////// TAB BUTTON //////////

function TabAOff(obj)
{
	obj.src = tabA1;
}

function TabAOn(obj)
{
	obj.src = tabA2;
}

function TabBOff(obj)
{
	obj.src = tabB1;
}

function TabBOn(obj)
{
	obj.src = tabB2;
}



////////// [PRODUCTS] TAB BUTTON //////////

function PTabAOff(obj)
{
	obj.src = ptabA1;
}

function PTabAOn(obj)
{
	obj.src = ptabA2;
}

function PTabBOff(obj)
{
	obj.src = ptabB1;
}

function PTabBOn(obj)
{
	obj.src = ptabB2;
}



////////// PACKING UNIT BUTTON //////////

function PunitOff(obj)
{
	obj.src = punit1;
}

function PunitOn(obj)
{
	obj.src = punit2;
}




function fncDarkGrayOpenButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = darkgrayopen1;
			break;

		case 'on':
			obj.src = darkgrayopen2;
			break;

		case 'down':
			obj.src = darkgrayopen3;
			break;

		default:
			break;
	}
}

////////// DATE BUTTON //////////

function DateOff(obj)
{
	obj.src = datebt1;
}

function DateOn(obj)
{
	obj.src = datebt2;
}






////////// GOODS PLAN BUTTON //////////
function GPOff(obj)
{
	obj.src = showwin1;
}

function GPOn(obj)
{
	obj.src = showwin2;
}





////////// DETAIL BUTTON //////////
function DetailOff(obj)
{
	obj.src = detail1;
}

function DetailOn(obj)
{
	obj.src = detail2;
}

////////// RENEW BUTTON //////////
function RenewOff(obj)
{
	obj.src = renew1;
}

function RenewOn(obj)
{
	obj.src = renew2;
}


////////// REMOVE BUTTON //////////
function RemoveOff(obj)
{
	obj.src = remove1;
}

function RemoveOn(obj)
{
	obj.src = remove2;
}

////////// COPY BUTTON //////////
function CopyOff(obj)
{
	obj.src = copy1;
}

function CopyOn(obj)
{
	obj.src = copy2;
}

////////// CLOSE BUTTON //////////
function CloseJOff(obj)
{
	obj.src = close1J;
}

function CloseJOn(obj)
{
	obj.src = close2J;
}

function CloseEOff(obj)
{
	obj.src = close1E;
}

function CloseEOn(obj)
{
	obj.src = close2E;
}


////////// BLOWN CLOSE BUTTON //////////
function BlownCloseJOff(obj)
{
	obj.src = blownclose1J;
}

function BlownCloseJOn(obj)
{
	obj.src = blownclose2J;
}

function BlownCloseEOff(obj)
{
	obj.src = blownclose1E;
}

function BlownCloseEOn(obj)
{
	obj.src = blownclose2E;
}


////////// DARK CLOSE BUTTON //////////
function DarkCloseJOff(obj)
{
	obj.src = darkclose1J;
}

function DarkCloseJOn(obj)
{
	obj.src = darkclose2J;
}

function DarkCloseEOff(obj)
{
	obj.src = darkclose1E;
}

function DarkCloseEOn(obj)
{
	obj.src = darkclose2E;
}




////////// DELETE BUTTON //////////
function DeleteJOff(obj)
{
	obj.src = delete1J;
}

function DeleteJOn(obj)
{
	obj.src = delete2J;
}

function DeleteEOff(obj)
{
	obj.src = delete1E;
}

function DeleteEOn(obj)
{
	obj.src = delete2E;
}







////////// DISPOSAL BUTTON //////////
function DispoOff(obj)
{
	obj.src = dispo1;
}

function DispoOn(obj)
{
	obj.src = dispo2;
}







////////// PREV-NEXT BUTTON //////////
function PrevOff(obj)
{
	obj.src = prev1;
}

function PrevOn(obj)
{
	obj.src = prev2;
}

function NextOff(obj)
{
	obj.src = next1;
}

function NextOn(obj)
{
	obj.src = next2;
}










//---------------------------------------------------
// Ŭ�ѡ���INVALID BIG�ץܥ���
//---------------------------------------------------
function fncInvalidBigButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = invalidbigbtJ1;
			break;

		case 'onJ':
			obj.src = invalidbigbtJ2;
			break;

		case 'offE':
			obj.src = invalidbigbtE1;
			break;

		case 'onE':
			obj.src = invalidbigbtE2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// Ŭ�ѡ���INVALID SMALL�ץܥ���
//---------------------------------------------------
function fncInvalidSmallButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = '/img/type01/cmn/querybt/invalid_small_off_bt.gif'; /* invalidsmallbt1 */
			break;

		case 'on':
			obj.src = '/img/type01/cmn/querybt/invalid_small_off_on_bt.gif'; /* invalidsmallbt2 */
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// Ŭ�ѡ����ü�ʸ���ץܥ���
//---------------------------------------------------
function fncSpecialButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = specialbt1;
			break;

		case 'on':
			obj.src = specialbt2;
			break;

		default:
			break;
	}

	return false;
}




//---------------------------------------------------
// Ŭ�ѡ���DARK LOGOUT�ץܥ���
//---------------------------------------------------
function fncDarkLogoutButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = darklogout1;
			break;

		case 'on':
			obj.src = darklogout2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// Ŭ�ѡ���DARK BACK�ץܥ���
//---------------------------------------------------
function fncDarkBackButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = darkback1;
			break;

		case 'on':
			obj.src = darkback2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// Ŭ�ѡ��֢��ץܥ���
//---------------------------------------------------
function fncDarkRAllowButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = ralowbt1;
			break;

		case 'on':
			obj.src = ralowbt2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// Ŭ�ѡ���SBMIT�ץܥ���
//---------------------------------------------------
function fncSubmitButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = submit1;
			break;

		case 'on':
			obj.src = submit2;
			break;

		default:
			break;
	}

	return false;
}




//---------------------------------------------------
// Ŭ�ѡ���INSERT�ץܥ���
//---------------------------------------------------
function fncInsertButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = insertbtJ1;
			break;

		case 'onJ':
			obj.src = insertbtJ2;
			break;

		case 'offE':
			obj.src = insertbtE1;
			break;

		case 'onE':
			obj.src = insertbtE2;
			break;

		default:
			break;
	}

	return false;
}





//---------------------------------------------------
// Ŭ�ѡ���COPY PREVIEW�ץܥ���
//---------------------------------------------------
function fncCopyPreviewButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = copybigbt1;
			break;

		case 'on':
			obj.src = copybigbt2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// Ŭ�ѡ���PREVIEW�ץܥ���
//---------------------------------------------------
function fncPreviewButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = previewbt1;
			break;

		case 'on':
			obj.src = previewbt2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// Ŭ�ѡ���BLOWN PREVIEW�ץܥ���
//---------------------------------------------------
function fncBlownPreviewButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = blownpreviewbt1;
			break;

		case 'on':
			obj.src = blownpreviewbt2;
			break;

		default:
			break;
	}

	return false;
}




//---------------------------------------------------
// Ŭ�ѡ���BLOWN REGIST�ץܥ���
//---------------------------------------------------
function fncBlownRegistButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = blownregistbtJ1;
			break;

		case 'onJ':
			obj.src = blownregistbtJ2;
			break;

		case 'offE':
			obj.src = blownregistbtE1;
			break;

		case 'onE':
			obj.src = blownregistbtE2;
			break;

		default:
			break;
	}

	return false;
}




//---------------------------------------------------
// Ŭ�ѡ���LOG�ץܥ���
//---------------------------------------------------
function fncLogButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = logbt1;
			break;

		case 'on':
			obj.src = logbt2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// Ŭ�ѡ���BACK SMALL�ץܥ���
//---------------------------------------------------
function fncBackSmallButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = backsmallbt1;
			break;

		case 'on':
			obj.src = backsmallbt2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// Ŭ�ѡ���RELOAD SMALL�ץܥ���
//---------------------------------------------------
function fncReloadSmallButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = reloadsmallbt1;
			break;

		case 'on':
			obj.src = reloadsmallbt2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// Ŭ�ѡ���NEXT SMALL�ץܥ���
//---------------------------------------------------
function fncNextSmallButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = blownnextbt1;
			break;

		case 'on':
			obj.src = blownnextbt2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// Ŭ�ѡ���PREV SMALL�ץܥ���
//---------------------------------------------------
function fncPrevSmallButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = blownprevbt1;
			break;

		case 'on':
			obj.src = blownprevbt2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// Ŭ�ѡ���BLOWN EXPORT�ץܥ���
//---------------------------------------------------
function fncBlownExportButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = blownexportbt1;
			break;

		case 'on':
			obj.src = blownexportbt2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// Ŭ�ѡ���GRAY PREVIEW�ץܥ���
//---------------------------------------------------
function fncGrayPreviewButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = graypreviewbt1;
			break;

		case 'on':
			obj.src = graypreviewbt2;
			break;

		default:
			break;
	}

	return false;
}




//---------------------------------------------------
// Ŭ�ѡ���RECOGNIZE�ץܥ���
//---------------------------------------------------
function fncRecognizeButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = recognwfJ1;
			break;

		case 'onJ':
			obj.src = recognwfJ2;
			break;

		case 'offE':
			obj.src = recognwfE1;
			break;

		case 'onE':
			obj.src = recognwfE2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// Ŭ�ѡ���DENY�ץܥ���
//---------------------------------------------------
function fncDenyButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = denywfJ1;
			break;

		case 'onJ':
			obj.src = denywfJ2;
			break;

		case 'offE':
			obj.src = denywfE1;
			break;

		case 'onE':
			obj.src = denywfE2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// Ŭ�ѡ���CANCEL�ץܥ���
//---------------------------------------------------
function fncCancelButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = cancelwfJ1;
			break;

		case 'onJ':
			obj.src = cancelwfJ2;
			break;

		case 'offE':
			obj.src = cancelwfE1;
			break;

		case 'onE':
			obj.src = cancelwfE2;
			break;

		default:
			break;
	}

	return false;
}


//---------------------------------------------------
// Ŭ�ѡ���PROCESS�ץܥ���
//---------------------------------------------------
function fncProcessButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = processwfJ1;
			break;

		case 'onJ':
			obj.src = processwfJ2;
			break;

		case 'offE':
			obj.src = processwfE1;
			break;

		case 'onE':
			obj.src = processwfE2;
			break;

		default:
			break;
	}

	return false;
}







//---------------------------------------------------
// Ŭ�ѡ���MESSAGE�ץܥ���
//---------------------------------------------------
function fncMessageButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = messageJ1;
			break;

		case 'onJ':
			obj.src = messageJ2;
			break;

		case 'offE':
			obj.src = messageE1;
			break;

		case 'onE':
			obj.src = messageE2;
			break;

		default:
			break;
	}

	return false;
}

//---------------------------------------------------
// Ŭ�ѡ���SERVER�ץܥ���
//---------------------------------------------------
function fncServerButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = serverJ1;
			break;

		case 'onJ':
			obj.src = serverJ2;
			break;

		case 'offE':
			obj.src = serverE1;
			break;

		case 'onE':
			obj.src = serverE2;
			break;

		default:
			break;
	}

	return false;
}

//---------------------------------------------------
// Ŭ�ѡ���EMAIL�ץܥ���
//---------------------------------------------------
function fncEmailButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = emailJ1;
			break;

		case 'onJ':
			obj.src = emailJ2;
			break;

		case 'offE':
			obj.src = emailE1;
			break;

		case 'onE':
			obj.src = emailE2;
			break;

		default:
			break;
	}

	return false;
}

//---------------------------------------------------
// Ŭ�ѡ���SESSION�ץܥ���
//---------------------------------------------------
function fncSessionButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = sessionJ1;
			break;

		case 'onJ':
			obj.src = sessionJ2;
			break;

		case 'offE':
			obj.src = sessionE1;
			break;

		case 'onE':
			obj.src = sessionE2;
			break;

		default:
			break;
	}

	return false;
}




//---------------------------------------------------
// Ŭ�ѡ���APACHE RESTART�ץܥ���
//---------------------------------------------------
function fncRestartButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = restartbtJ1;
			break;

		case 'onJ':
			obj.src = restartbtJ2;
			break;

		case 'offE':
			obj.src = restartbtE1;
			break;

		case 'onE':
			obj.src = restartbtE2;
			break;

		default:
			break;
	}

	return false;
}

//---------------------------------------------------
// Ŭ�ѡ���APACHE STOP�ץܥ���
//---------------------------------------------------
function fncStopButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = stopbtJ1;
			break;

		case 'onJ':
			obj.src = stopbtJ2;
			break;

		case 'offE':
			obj.src = stopbtE1;
			break;

		case 'onE':
			obj.src = stopbtE2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// Ŭ�ѡ���BLOWN PROCESS�ץܥ���
//---------------------------------------------------
function fncBlownProcessButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = blownprocessbtJ1;
			break;

		case 'onJ':
			obj.src = blownprocessbtJ2;
			break;

		case 'offE':
			obj.src = blownprocessbtE1;
			break;

		case 'onE':
			obj.src = blownprocessbtE2;
			break;

		default:
			break;
	}

	return false;
}



//---------------------------------------------------
// Ŭ�ѡ���BLOWN REVIVAL�ץܥ���
//---------------------------------------------------
function fncBlownRevivalButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = blownrevivalbtJ1;
			break;

		case 'onJ':
			obj.src = blownrevivalbtJ2;
			break;

		case 'offE':
			obj.src = blownrevivalbtE1;
			break;

		case 'onE':
			obj.src = blownrevivalbtE2;
			break;

		default:
			break;
	}

	return false;
}
