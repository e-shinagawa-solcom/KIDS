

//@-------------------------------------------------------------------------------------------------------------------
/**
* �ե����복�� : ���������ɽ����������Ͽ�����ؿ���
*
*
*
* @package k.i.d.s.
* @license http://www.wiseknot.co.jp/
* @copyright Copyright &copy; 2004, Wiseknot, Inc.
* @author Ryosuke Tomita <r-tomita@wiseknot.co.jp>
* @access public
* @version 1.0
*/ 
//--------------------------------------------------------------------------------------------------------------------




















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ���������ɽ�������ؿ�
*
* ���� : �����󥦥���ɥ�ɽ����ǧ���֥������Ȥ�̤����ξ�硢
*        �����󥦥���ɥ���ɽ�����롣
*
* �о� : �ȥåץڡ���
*
* @param [g_mainwindow] : [���֥������ȷ�] . �����󥦥���ɥ�ɽ����ǧ���֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
var g_mainwindow = new Object();

function GoLogin()
{
	if( !Object.keys(g_mainwindow).length ||
		 g_mainwindow.closed )
	{
		g_mainwindow = window.open( '/login/login.php?value=kids', 'mainWin','width=1001,height=649,status=yes,scrollbars=no,directories=no,menubar=no,resizable=no,location=no,toolbar=no,left=6,top=0' );
	}
	else
	{
		g_mainwindow.focus();
	}

	return false;
}
















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ��Ͽ���֥ߥåȽ����ؿ�
*
* �о� : ���ʴ���������ȯ����塦����
*
* @param [obj1] : [���֥������ȷ�] . �ե���������֥�������̾
* @param [obj2] : [���֥������ȷ�] . �ե�����¥��֥�������̾
*/
//--------------------------------------------------------------------------------------------------------------------
function SubmitRequest( obj1 , obj2 )
{
	for( i = 0; i < obj2.elements.length; i++ )
	{
		document.all.Record.innerHTML = document.all.Record.innerHTML + '<input type="hidden" name="' + obj2.elements[i].name + '" value="' + fncCheckReplaceString(obj2.elements[i].value) + '">';
	}

	//alert(document.all.Record.innerHTML);

	//var strObj1 = "";
	//for( i = 0; i < obj1.elements.length; i++ )
	//{
	//	strObj1 = strObj1 + '<input type="hidden" name="' + obj1.elements[i].name + '" value="' + fncCheckReplaceString(obj1.elements[i].value) + '">';
	//}
	// debug
	//alert(strObj1);

	obj1.submit();
}









//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : ʣ���ե������祵�֥ߥåȽ����ؿ�
*
* @param [buffer]     : [���֥������ȷ�] . �ƥե��������ǤޤȤ��ѥХåե����֥�������
* @param [action]     : [ʸ����]       . �ե����ॢ����������
* @param [submitform] : [���֥������ȷ�] . ���֥ߥåȥե����४�֥�������̾
* @param [aryform]    : [���֥������ȷ�] . �ƥե����४�֥�������̾(����)
*/
//--------------------------------------------------------------------------------------------------------------------
function fncSomeFormSubmitRequest( buffer, action, submitform, aryform )
{
	for( i=0; i < aryform.length; i++ )
	{
		for( j=0; j < aryform[i].elements.length; j++ )
		{
			buffer.innerHTML = buffer.innerHTML + '<input type="hidden" name="' + aryform[i].elements[j].name + '" value="' + fncCheckReplaceString( aryform[i].elements[j].value ) + '">';
		}
	}

	// �ǥХå�
	//alert( buffer.innerHTML );

	submitform.action = action;
	submitform.submit();
}











/*
function SbmitRequest( obj1 , obj2 )
{
	var aryA = new Array();

		for( ia = 0; ia < obj1.elements.length; ia++ )
		{
			aryA.push(obj1.elements[ia].value);
		}

		for( ib = 0; ib < obj2.elements.length; ib++ )
		{
			aryA.push(obj2.elements[ib].value);
		}

		document.all.Record.value = aryA;

		if (document.all.Record.value != '')
		{
			obj1.submit();
		}
		else
		{
	return false;
}
*/





/*
	function SubmitRequest(obj1 , obj2)
	{
		var aryA = new Array();

		for( ia = 0; ia < obj1.elements.length; ia++ )
		{
			aryA.push(obj1.elements[ia].value);
		}

		for( ib = 0; ib < obj2.elements.length; ib++ )
		{
			aryA.push(obj2.elements[ib].value);
		}

		var aryB = new Array();
		aryB.push(aryA);

		document.all.hidden.value = aryB;
		//alert(aryB);

		if (document.all.hidden1.value != '')
		{
			obj1.submit();
		}
		else
		{
			alert('NO VALUES');
		}
	}
*/

////////// HYPER LINKS //////////
/*
function GoLogin()
{
	mainw = window.open( '/login/index.html', 'loginWin','width=1012,height=689,status=yes,scrollbars=yes,directories=no,menubar=no,resizable=yes,location=no,toolbar=no,left=0,top=0');
	return false;
}
*/








// width=1012,height=689


/*
var g_mainwindow = new Object();

function GoLogin()
{

	if( typeof(g_mainwindow.name) == 'undefined' ||
		typeof(g_mainwindow.name) == 'unknown' )
	{
		g_mainwindow = window.open( '/login/login.php', 'mainWin','width=1012,height=689,status=yes,scrollbars=yes,directories=no,menubar=no,resizable=no,location=no,toolbar=no,left=0,top=0' );
	}
	else
	{
		g_mainwindow.focus();
	}

	return false;
}





function GoNavi()
{
	location.href = '/menu/index.html';
	return false;
}








function GoManage(pages)
{
	switch (pages)
	{
		case 1: //P TOP
			document.frmMenu.action = "/p/index.php";
			document.frmMenu.submit();
			break;

		case 2: //P REGISTRATION
			document.frmMenu.action = "/p/regist/index.php";
			document.frmMenu.submit();
			break;

		case 3: //P SEARCH
			document.frmMenu.action = "/p/search/index.php";
			document.frmMenu.submit();
			break;



		case 4: //SO TOP
			document.frmMenu.action = "/so/index.php";
			document.frmMenu.submit();
			break;

		case 5: //SO REGISTRATION
			document.frmMenu.action = "/so/regist/index.php";
			document.frmMenu.submit();
			break;

		case 6: //SO SEARCH
			document.frmMenu.action = "/so/search/index.php";
			document.frmMenu.submit();
			break;



		case 7: //PO TOP
			document.frmMenu.action = "/po/index.php";
			document.frmMenu.submit();
			break;

		case 8: //PO REGISTRATION
			document.frmMenu.action = "/po/regist/index.php";
			document.frmMenu.submit();
			break;

		case 9: //PO SEARCH
			document.frmMenu.action = "/so/search/index.php";
			document.frmMenu.submit();
			break;



		case 10: //WF TOP
			document.frmMenu.action = "/wf/index.php";
			document.frmMenu.submit();
			break;

		case 11: //WF REGISTRATION
			document.frmMenu.action = "/wf/list/index.php";
			document.frmMenu.submit();
			break;

		case 12: //WF SEARCH
			document.frmMenu.action = "/wf/search/index.php";
			document.frmMenu.submit();
			break;

		default:
			break;
	}
}
*/