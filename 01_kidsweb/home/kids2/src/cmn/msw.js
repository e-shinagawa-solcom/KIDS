<!--


////////// [MSW]������ɥ������ץ���������襪�֥������ȼ��� //////////

// objIfrm	: ���֥�����ɥ�Iframe̾
// objCodeA	: [��]�������ѥƥ����ȥե������NAME
// objNameA	: [��]̾���ѥƥ����ȥե������NAME
// objCodeB	: [��]�������ѥƥ����ȥե������NAME
// objNameB	: [��]̾���ѥƥ����ȥե������NAME

function fncGetObjectName( objIfrm , objCodeA , objNameA , objCodeB , objNameB )
{

	// [��]�ƥ����ȥե�����ɥ��֥������Ȥ����
	objIfrm.g_aryElementName[0] = objCodeA;
	objIfrm.g_aryElementName[1] = objNameA;


	////////// �ʲ���[��]���� //////////
	if( objCodeB )
	{
		// [��]�ƥ����ȥե�����ɥ��֥������Ȥ����
		objIfrm.g_aryElementName[2] = objCodeB;
		objIfrm.g_aryElementName[3] = objNameB;
	}

	return true;
}




////////// [MSW]�����ȥ�С����֥륯��å����ν��� //////////

function fncTitlebar( objID , objID2 )
{


	if( objID.style.height != "18px" || objID.style.height == "")
	{
		objID.style.height = "18px";
		//�����������������ȡ�tab�����򲡤����Ȥ���ɽ�������������ʤ뤿�ᡢ
		//�ƥ�����ɥ��˥ե���������ܤ���
		window.parent.focus();
	}
	else if( objID.style.height == "18px" )
	{
		//MSW��DATE�ΤȤ�
		if( objID.name == "MDatewin"  || 
			objID.name == "MDateBwin" || 
			objID.name == "MDateCwin" )
		{
			objID.style.height = "245px";
		}
		//����ʳ���MSW�ΤȤ�
		else
		{
			objID.style.height = "381px";
		}

		//���֥������Ȥΰ��֤�����å�
		fncMswPositionCheck( objID , objID2 );
	}
	return false;
}




///// [DEPT & IN CHARGE NAME] �ɥ�å�����Selectbox ɽ��-��ɽ������ /////
function ShowHideValueDept( obj )
{
	if( obj.DeptValueFlg == 0 )
	{
		obj.VarsB01.style.visibility = 'visible';
	}

	if( obj.DeptValueFlg == 1 )
	{
		obj.VarsD01.style.visibility = 'visible';
	}
	return false;
}


///// [VENDOR & IN CHARGE NAME] �ɥ�å�����Selectbox ɽ��-��ɽ������ /////
function ShowHideValueVi( obj )
{
	if( obj.ViValueFlg == 0 )
	{
		obj.VarsB01.style.visibility = 'visible';
	}

	if( obj.ViValueFlg == 1 )
	{
		obj.VarsD01.style.visibility = 'visible';
	}
	return false;
}

///// [OTHER MSW] �ɥ�å�����Selectbox ɽ��-��ɽ������ /////
function ShowValue( obj )
{
	obj.VarsB01.style.visibility = 'visible';

	return false;
}



//@*****************************************************************************
// ����   : ���֥�����ɥ����Ȥγ��ˤϤ߽ФǤ��顢����Ū�����褦�ˤ���
// ����   
//        : selectedObject, �оݥ��֥�������̾�ι⤵���������뤿��˻���
//        : selectedLayer , �оݥ��֥�������̾�Υ�����ɥ����֤����뤿��˻���
// ������ : ���� ��ʸ
//******************************************************************************
function fncMswPositionCheck( selectedObject , selectedLayer )
{
	//onMouseUp������򥯥ꥢ����
	document.onmouseup = clearAll;

	////Y���ν���
	//ɽ�����Ƥ��륦����ɥ��ι⤵
	MAXoffsetY = parseInt(document.body.clientHeight);
	//���֥������Ȥȥ�����ɥ��Ȥι⤵
	offset_Y = selectedLayer.offsetTop;
	//���֥������Ȥι⤵
	layerHeight = selectedObject.offsetHeight;

	//���˹Ԥ��᤮����	
	if( (MAXoffsetY - offset_Y) <= layerHeight )
	{
		//��������
		var movetoY = MAXoffsetY - layerHeight;

		//���᤮�ʤ��褦�ˤ��뤿��ν���
		if(movetoY >= 0)
		{
			selectedLayer.style.pixelTop  = movetoY;
		}
		else
		{
			selectedLayer.style.pixelTop  = 0;
		}
	}
	//��˹Ԥ��᤮����
	else if( (offset_Y) <= 0 )
	{
		selectedLayer.style.pixelTop  = 0;
	}

	////X���ν���
	//ɽ�����Ƥ��륦����ɥ�����
	MAXoffsetX = parseInt(document.body.clientWidth);
	//���֥������Ȥȥ�����ɥ��Ȥ���
	offset_X = selectedLayer.offsetLeft;
	//���֥������Ȥ���
	layerWidth  = selectedObject.offsetWidth;

	//���˹Ԥ��᤮����	
	if( (MAXoffsetX - offset_X) <= layerWidth )
	{
		//��������
		var movetoX = MAXoffsetX - layerWidth;

		//���᤮�ʤ��褦�ˤ��뤿��ν���
		if(movetoX >= 0)
		{
			selectedLayer.style.pixelLeft = movetoX;
		}
		else
		{
			selectedLayer.style.pixelLeft =  0;
		}
	}
	//���˹Ԥ��᤮����
	else if( (offset_X) <= 0 )
	{
		selectedLayer.style.pixelLeft  = 0;
	}

//�ǥХå�������
/*
alert(   "MAXoffsetY = " + MAXoffsetY + "\n" +
		"offset_Y = " + offset_Y + "\n" +
		"layerHeight = " + layerHeight + "\n" +
		"MAXoffsetX = " + MAXoffsetX + "\n" +
		"offset_X = " + offset_X + "\n" +
		"layerWidth = " + layerWidth + "\n");
*/
}










//----------------------------------------------------------------------
// ���� : ���֥�����ɥ����������Υե�������������ؿ�
//----------------------------------------------------------------------
function fncFocusType( objIFrm , strType )
{
	objIFrm.g_FocusObject[0] = strType;

	return false;
}



//----------------------------------------------------------------------
// ���� : ���֥�����ɥ����������Υե�������Ŭ�Ѵؿ�
//----------------------------------------------------------------------
function fncFocusObject( strMode )
{

	switch( strMode )
	{

		case 'vendorTop':
			if( document.all.InputA.style.visibility != 'hidden' )
			{
				document.all.vendorBt.focus();
			}
			break;

		case 'vendorIfrm':
			window.Pwin.fncIfrmFocusObject( 'vendor' );
			break;



		case 'creationTop':
			if( document.all.InputB.style.visibility != 'hidden' )
			{
				document.all.creationBt.focus();
			}

			break;

		case 'creationIfrm':
			window.Pwin.fncIfrmFocusObject( 'creation' );
			break;



		case 'assemblyTop':
			if( document.all.InputB.style.visibility != 'hidden' )
			{
				document.all.assemblyBt.focus();
			}
			break;

		case 'assemblyIfrm':
			window.Pwin.fncIfrmFocusObject( 'assembly' );
			break;



		case 'deptTop':
			if( document.all.InputA.style.visibility != 'hidden' )
			{
				document.all.deptBt.focus();
			}

			break;

		case 'deptIfrm':
			window.Pwin.fncIfrmFocusObject( 'dept' );
			break;



		case 'productsTop':
			if( document.all.InputB.style.visibility != 'hidden' )
			{
				document.all.productsBt.focus();
			}
			break;

		case 'productsIfrm':
			window.Pwin.fncIfrmFocusObject( 'products' );
			break;



		case 'locationTop':
			// ȯ������ξ��
			if( typeof( window.HSO ) == "object" )
			{
				if( document.all.InputA.style.visibility != 'hidden' )
				{
					document.all.locationBt.focus();
				}
			}
			// ���ʴ����ξ��
			else
			{
				if( document.all.InputB.style.visibility != 'hidden' )
				{
					document.all.locationBt.focus();
				}
			}
			break;



		case 'locationIfrm':
			window.Pwin.fncIfrmFocusObject( 'location' );
			break;



		case 'applicantTop':
			document.all.applicantBt.focus();
			break;

		case 'applicantIfrm':
			window.Pwin.fncIfrmFocusObject( 'applicant' );
			break;



		case 'wfinputTop':
			document.all.wfinputBt.focus();
			break;

		case 'wfinputIfrm':
			window.Pwin.fncIfrmFocusObject( 'wfinput' );
			break;



		case 'viTop':
			if( document.all.InputA.style.visibility != 'hidden' )
			{
				document.all.viBt.focus();
			}
			break;

		case 'viIfrm':
			window.Pwin.fncIfrmFocusObject( 'vi' );
			break;



		case 'supplierTop':
			if( document.all.InputA.style.visibility != 'hidden' )
			{
				document.all.supplierBt.focus();
			}
			break;

		case 'supplierIfrm':
			window.Pwin.fncIfrmFocusObject( 'supplier' );
			break;



		case 'inputTop':
			document.all.inputBt.focus();
			break;

		case 'inputIfrm':
			window.Pwin.fncIfrmFocusObject( 'input' );
			break;


		default:
			break;
	}


	return false;

}










////////////////////////////// [VENDOR] //////////////////////////////

// �쥤�䡼ɽ������ɽ���ե饰
var flagA = 0;

// ENTER���������ǥ�������������ե饰
var vendorEnterKeyCntDisplay = 0;

////////// �쥤�䡼ɽ������ɽ������ /////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : '' : �ǥե����
//														 : 1  : �������٥��
//
// objID	: ���֥�����ɥ�IFRAME̾
// objA		: [InputA]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾

/////////////////////////////////////////////////////////////////////////////////////
function DisplayerM01( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// ����ե饰�إ������٥��Ƚ���ͤ�����
		vendorEnterKeyCntDisplay = lngMSW;
	}


	// �̾�Υ���å����ξ��
	if( vendorEnterKeyCntDisplay == 0 )
	{

		if( lngMSW == '' && flagA == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame01.style.visibility = "visible";

			objA.focus();

			flagA = 1;
		}
		else if( flagA == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame01.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			flagA = 0;
		}

	}

	// �������٥�Ȥ�������ξ��
	else if( vendorEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame01.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// ������쥯���Ѥ��ͤ�����
		vendorEnterKeyCntDisplay = 2;

		flagA = 0;
	}

	// �������٥�ȤΥ�����쥯�Ƚ���
	else if( vendorEnterKeyCntDisplay == 2 )
	{
		// ����ե饰�ν����
		vendorEnterKeyCntDisplay = 0;
	}


	return false;
}


/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : 0 : �ǥե����
//														 : 1 : �������٥��
// obj		:	�ܥ������֥�����ɥ�̾(IFRAME��˥ܥ�������֤�����������Ū�ʻ���򤹤�)

////////// [MSWBt]�Υܥ������ؽ��� //////////////////////////////////////////////////

// ���᡼���Ѵ��ե饰
var countM1=0;

// ENTER���������ǥ��᡼���Ѵ���������ե饰
var vendorEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM01( lngMSW , obj )
{

	// ����ե饰�إ������٥��Ƚ���ͤ�����
	vendorEnterKeyCntExchange = lngMSW;


	// �̾�Υ���å����ξ��
	if( vendorEnterKeyCntExchange == 0 )
	{

		if( countM1 == 0 )
		{
			MSWBt01.innerHTML = mswbtA3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt01.innerHTML = mswbtA3;
			}

			countM1 = 1;
		}
		else if( countM1 == 1 )
		{
			MSWBt01.innerHTML = mswbtA1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt01.innerHTML = mswbtA1;
			}

			// �ܥ���إե����������᤹
			//document.all.vendorBt.focus();

			countM1 = 0;

		}

	}

	// �������٥�Ȥ�������ξ��
	else if( vendorEnterKeyCntExchange == 1 )
	{
		MSWBt01.innerHTML = mswbtA1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt01.innerHTML = mswbtA1;
		}

		// �ܥ���إե����������᤹
		//document.all.vendorBt.focus();
	}


	return false;
}






////////////////////////////// [CREATION FACTORY] //////////////////////////////

// �쥤�䡼ɽ������ɽ���ե饰
var flagA_2 = 0;

// ENTER���������ǥ�������������ե饰
var creationEnterKeyCntDisplay = 0;

////////// �쥤�䡼ɽ������ɽ������ /////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : '' : �ǥե����
//														 : 1  : �������٥��
//
// objID	: ���֥�����ɥ�IFRAME̾
// objA		: [InputA]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾
// objB		: [InputB]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾

/////////////////////////////////////////////////////////////////////////////////////

function DisplayerM01_2( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// ����ե饰�إ������٥��Ƚ���ͤ�����
		creationEnterKeyCntDisplay = lngMSW;
	}


	// �̾�Υ���å����ξ��
	if( creationEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && flagA_2 == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame01_2.style.visibility = "visible";

			objA.focus();

			flagA_2 = 1;
		}

		else if( flagA_2 == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame01_2.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			flagA_2 = 0;
		}
	}

	// �������٥�Ȥ�������ξ��
	else if( creationEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame01_2.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// ������쥯���Ѥ��ͤ�����
		creationEnterKeyCntDisplay = 2;
		flagA_2 = 0;

	}

	// �������٥�ȤΥ�����쥯�Ƚ���
	else if( creationEnterKeyCntDisplay == 2 )
	{
		// ����ե饰�ν����
		creationEnterKeyCntDisplay = 0;
	}

	return false;
}






/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : 0 : �ǥե����
//														 : 1 : �������٥��
// obj		:	�ܥ������֥�����ɥ�̾(IFRAME��˥ܥ�������֤�����������Ū�ʻ���򤹤�)

////////// [MSWBt]�Υܥ������ؽ��� //////////////////////////////////////////////////

// ���᡼���Ѵ��ե饰
var countM1_2 = 0;

// ENTER���������ǥ��᡼���Ѵ���������ե饰
var creationEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM01_2( lngMSW , obj , objFocus )
{

	// ����ե饰�إ������٥��Ƚ���ͤ�����
	creationEnterKeyCntExchange = lngMSW;


	// �̾�Υ���å����ξ��
	if( creationEnterKeyCntExchange == 0 )
	{

		if( countM1_2 == 0 )
		{
			MSWBt01_2.innerHTML = mswbtA3_2;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt01_2.innerHTML = mswbtA3_2;
			}

			countM1_2 = 1;
		}
		else if( countM1_2 == 1 )
		{
			MSWBt01_2.innerHTML = mswbtA1_2;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt01_2.innerHTML = mswbtA1_2;
			}

			// �ܥ���إե����������᤹
			//document.all.creationBt.focus();

			countM1_2 = 0;
		}

	}


	// �������٥�Ȥ�������ξ��
	else if( creationEnterKeyCntExchange == 1 )
	{
		MSWBt01_2.innerHTML = mswbtA1_2;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt01_2.innerHTML = mswbtA1_2;
		}

			// �ܥ���إե����������᤹
			//document.all.creationBt.focus();

	}

	return false;
}









////////////////////////////// [ASSEMBLY FACTORY] //////////////////////////////

// �쥤�䡼ɽ������ɽ���ե饰
var flagA_3 = 0;

// ENTER���������ǥ�������������ե饰
var assemblyEnterKeyCntDisplay = 0;

////////// �쥤�䡼ɽ������ɽ������ /////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : '' : �ǥե����
//														 : 1  : �������٥��
//
// objID	: ���֥�����ɥ�IFRAME̾
// objA		: [InputA]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾
// objB		: [InputB]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾

/////////////////////////////////////////////////////////////////////////////////////

function DisplayerM01_3( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// ����ե饰�إ������٥��Ƚ���ͤ�����
		assemblyEnterKeyCntDisplay = lngMSW;
	}


	// �̾�Υ���å����ξ��
	if( assemblyEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && flagA_3 == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame01_3.style.visibility = "visible";

			objA.focus();

			flagA_3 = 1;
		}

		else if( flagA_3 == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame01_3.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			flagA_3 = 0;
		}
	}

	// �������٥�Ȥ�������ξ��
	else if( assemblyEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame01_3.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// ������쥯���Ѥ��ͤ�����
		assemblyEnterKeyCntDisplay = 2;
		flagA_3 = 0;

	}

	// �������٥�ȤΥ�����쥯�Ƚ���
	else if( assemblyEnterKeyCntDisplay == 2 )
	{
		// ����ե饰�ν����
		assemblyEnterKeyCntDisplay = 0;
	}


	return false;
}




/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : 0 : �ǥե����
//														 : 1 : �������٥��
// obj		:	�ܥ������֥�����ɥ�̾(IFRAME��˥ܥ�������֤�����������Ū�ʻ���򤹤�)

////////// [MSWBt]�Υܥ������ؽ��� //////////////////////////////////////////////////

// ���᡼���Ѵ��ե饰
var countM1_3 = 0;

// ENTER���������ǥ��᡼���Ѵ���������ե饰
var assemblyEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM01_3( lngMSW , obj )
{

	// ����ե饰�إ������٥��Ƚ���ͤ�����
	assemblyEnterKeyCntExchange = lngMSW;


	// �̾�Υ���å����ξ��
	if( assemblyEnterKeyCntExchange == 0 )
	{

		if( countM1_3 == 0 )
		{
			MSWBt01_3.innerHTML = mswbtA3_3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt01_3.innerHTML = mswbtA3_3;
			}

			countM1_3 = 1;
		}
		else if( countM1_3 == 1 )
		{
			MSWBt01_3.innerHTML = mswbtA1_3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt01_3.innerHTML = mswbtA1_3;
			}

			// �ܥ���إե����������᤹
			//document.all.assemblyBt.focus();

			countM1_3 = 0;
		}

	}


	// �������٥�Ȥ�������ξ��
	else if( assemblyEnterKeyCntExchange == 1 )
	{
		MSWBt01_3.innerHTML = mswbtA1_3;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt01_3.innerHTML = mswbtA1_3;
		}

		// �ܥ���إե����������᤹
		//document.all.assemblyBt.focus();

	}

	return false;
}






////////////////////////////// [DEPT & IN CHARGE NAME] //////////////////////////////

// �쥤�䡼ɽ������ɽ���ե饰
var flagX = 0;

// ENTER���������ǥ�������������ե饰
var deptEnterKeyCntDisplay = 0;

////////// �쥤�䡼ɽ������ɽ������ /////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : '' : �ǥե����
//														 : 1  : �������٥��
//
// objID	: ���֥�����ɥ�IFRAME̾
// objA		: [InputA]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾
// objB		: [InputB]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾

/////////////////////////////////////////////////////////////////////////////////////

function DisplayerM02( lngMSW , objID , objA , objB )
{

	if( lngMSW != '' )
	{
		// ����ե饰�إ������٥��Ƚ���ͤ�����
		deptEnterKeyCntDisplay = lngMSW;
	}


	// �̾�Υ���å����ξ��
	if( deptEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && flagX == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame02.style.visibility = "visible";

			if( window.MDwin.DeptValueFlg == 0 )
			{
				objA.focus();
			}

			if( window.MDwin.DeptValueFlg == 1 )
			{
				objB.focus();
			}

			flagX = 1;
		}

		else if( flagX == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame02.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			flagX = 0;
		}
	}

	// �������٥�Ȥ�������ξ��
	else if( deptEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame02.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// ������쥯���Ѥ��ͤ�����
		deptEnterKeyCntDisplay = 2;
		flagX = 0;

	}

	// �������٥�ȤΥ�����쥯�Ƚ���
	else if( deptEnterKeyCntDisplay == 2 )
	{
		// ����ե饰�ν����
		deptEnterKeyCntDisplay = 0;
	}

	return false;
}




/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : 0 : �ǥե����
//														 : 1 : �������٥��
// obj		:	�ܥ������֥�����ɥ�̾(IFRAME��˥ܥ�������֤�����������Ū�ʻ���򤹤�)
////////// [MSWBt]�Υܥ������ؽ��� //////////////////////////////////////////////////

// ���᡼���Ѵ��ե饰
var countM2 = 0;

// ENTER���������ǥ��᡼���Ѵ���������ե饰
var deptEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////

function ExchangeM02( lngMSW , obj )
{

	// ����ե饰�إ������٥��Ƚ���ͤ�����
	deptEnterKeyCntExchange = lngMSW;


	// �̾�Υ���å����ξ��
	if( deptEnterKeyCntExchange == 0 )
	{

		if( countM2 == 0 )
		{
			MSWBt02.innerHTML = mswbtB3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt02.innerHTML = mswbtB3;
			}

			countM2 = 1;
		}
		else if( countM2 == 1 )
		{
			MSWBt02.innerHTML = mswbtB1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt02.innerHTML = mswbtB1;
			}

			// �ܥ���إե����������᤹
			//document.all.deptBt.focus();

			countM2 = 0;
		}

	}


	// �������٥�Ȥ�������ξ��
	else if( deptEnterKeyCntExchange == 1 )
	{
		MSWBt02.innerHTML = mswbtB1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt02.innerHTML = mswbtB1;
		}

		// �ܥ���إե����������᤹
		//document.all.deptBt.focus();

	}

	return false;
}











////////////////////////////// [PRODUCTS] //////////////////////////////

// �쥤�䡼ɽ������ɽ���ե饰
var flagD = 0;

// ENTER���������ǥ�������������ե饰
var locationEnterKeyCntDisplay = 0;

////////// �쥤�䡼ɽ������ɽ������ /////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : '' : �ǥե����
//														 : 1  : �������٥��
//
// objID	: ���֥�����ɥ�IFRAME̾
// objA		: [InputA]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾

/////////////////////////////////////////////////////////////////////////////////////
function DisplayerM03( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// ����ե饰�إ������٥��Ƚ���ͤ�����
		locationEnterKeyCntDisplay = lngMSW;
	}


	// �̾�Υ���å����ξ��
	if( locationEnterKeyCntDisplay == 0 )
	{
		if( flagD == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame03.style.visibility = "visible";

			objA.focus();

			flagD = 1;
		}
		else if( flagD == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame03.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			flagD = 0;
		}
	}

	// �������٥�Ȥ�������ξ��
	else if( locationEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame03.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// ������쥯���Ѥ��ͤ�����
		locationEnterKeyCntDisplay = 2;

		flagD = 0;
	}

	// �������٥�ȤΥ�����쥯�Ƚ���
	else if( locationEnterKeyCntDisplay == 2 )
	{
		// ����ե饰�ν����
		locationEnterKeyCntDisplay = 0;
	}


	return false;
}


/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : 0 : �ǥե����
//														 : 1 : �������٥��
// obj		:	�ܥ������֥�����ɥ�̾(IFRAME��˥ܥ�������֤�����������Ū�ʻ���򤹤�)

////////// [MSWBt]�Υܥ������ؽ��� //////////////////////////////////////////////////

// ���᡼���Ѵ��ե饰
var countM3 = 0;

// ENTER���������ǥ��᡼���Ѵ���������ե饰
var locationEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM03( lngMSW , obj )
{

	// ����ե饰�إ������٥��Ƚ���ͤ�����
	locationEnterKeyCntExchange = lngMSW;


	// �̾�Υ���å����ξ��
	if( locationEnterKeyCntExchange == 0 )
	{

		if( countM3 == 0 )
		{
			MSWBt03.innerHTML = mswbtC3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt03.innerHTML = mswbtC3;
			}

			countM3 = 1;
		}
		else if( countM3 == 1 )
		{
			MSWBt03.innerHTML = mswbtC1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt03.innerHTML = mswbtC1;
			}

			// �ܥ���إե����������᤹
			//document.all.productsBt.focus();

			countM3 = 0;
		}

	}

	// �������٥�Ȥ�������ξ��
	else if( locationEnterKeyCntExchange == 1 )
	{
		MSWBt03.innerHTML = mswbtC1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt03.innerHTML = mswbtC1;
		}

		// �ܥ���إե����������᤹
		//document.all.productsBt.focus();
	}


	return false;
}








///////////////////////////////////// [LOCATION] ////////////////////////////////////

// �쥤�䡼ɽ������ɽ���ե饰
var flagG = 0;

// ENTER���������ǥ�������������ե饰
var locationEnterKeyCntDisplay = 0;

////////// �쥤�䡼ɽ������ɽ������ /////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : '' : �ǥե����
//														 : 1  : �������٥��
//
// objID	: ���֥�����ɥ�IFRAME̾
// objA		: [InputA]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾
// objB		: [InputB]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾

/////////////////////////////////////////////////////////////////////////////////////

function DisplayerM04( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// ����ե饰�إ������٥��Ƚ���ͤ�����
		locationEnterKeyCntDisplay = lngMSW;
	}


	// �̾�Υ���å����ξ��
	if( locationEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && flagG == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame04.style.visibility = "visible";

			objA.focus();

			flagG = 1;
		}

		else if( flagG == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame04.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			flagG = 0;
		}
	}

	// �������٥�Ȥ�������ξ��
	else if( locationEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame04.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// ������쥯���Ѥ��ͤ�����
		locationEnterKeyCntDisplay = 2;
		flagG = 0;

	}

	// �������٥�ȤΥ�����쥯�Ƚ���
	else if( locationEnterKeyCntDisplay == 2 )
	{
		// ����ե饰�ν����
		locationEnterKeyCntDisplay = 0;
	}


	return false;
}




/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : 0 : �ǥե����
//														 : 1 : �������٥��
// obj		:	�ܥ������֥�����ɥ�̾(IFRAME��˥ܥ�������֤�����������Ū�ʻ���򤹤�)

////////// [MSWBt]�Υܥ������ؽ��� //////////////////////////////////////////////////

// ���᡼���Ѵ��ե饰
var countM4 = 0;

// ENTER���������ǥ��᡼���Ѵ���������ե饰
var locationEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM04( lngMSW , obj )
{

	// ����ե饰�إ������٥��Ƚ���ͤ�����
	locationEnterKeyCntExchange = lngMSW;


	// �̾�Υ���å����ξ��
	if( locationEnterKeyCntExchange == 0 )
	{

		if( countM4 == 0 )
		{
			MSWBt04.innerHTML = mswbtD3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt04.innerHTML = mswbtD3;
			}

			countM4 = 1;
		}
		else if( countM4 == 1 )
		{
			MSWBt04.innerHTML = mswbtD1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt04.innerHTML = mswbtD1;
			}

			// �ܥ���إե����������᤹
			//document.all.locationBt.focus();

			countM4 = 0;
		}

	}


	// �������٥�Ȥ�������ξ��
	else if( locationEnterKeyCntExchange == 1 )
	{
		MSWBt04.innerHTML = mswbtD1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt04.innerHTML = mswbtD1;
		}

		// �ܥ���إե����������᤹
		//document.all.locationBt.focus();

	}

	return false;
}









////////////////////////////// [APPLICANT] //////////////////////////////

// �쥤�䡼ɽ������ɽ���ե饰
var Appcnt = 0;

// ENTER���������ǥ�������������ե饰
var applicantEnterKeyCntDisplay = 0;

////////// �쥤�䡼ɽ������ɽ������ /////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : '' : �ǥե����
//														 : 1  : �������٥��
//
// objID	: ���֥�����ɥ�IFRAME̾
// objA		: [InputA]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾

/////////////////////////////////////////////////////////////////////////////////////
function DisplayerM05( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// ����ե饰�إ������٥��Ƚ���ͤ�����
		applicantEnterKeyCntDisplay = lngMSW;
	}


	// �̾�Υ���å����ξ��
	if( applicantEnterKeyCntDisplay == 0 )
	{

		if( lngMSW == '' && Appcnt == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame05.style.visibility = "visible";

			objA.focus();

			Appcnt = 1;
		}
		else if( Appcnt == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame05.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			Appcnt = 0;
		}

	}

	// �������٥�Ȥ�������ξ��
	else if( applicantEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame05.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// ������쥯���Ѥ��ͤ�����
		applicantEnterKeyCntDisplay = 2;

		Appcnt = 0;
	}

	// �������٥�ȤΥ�����쥯�Ƚ���
	else if( applicantEnterKeyCntDisplay == 2 )
	{
		// ����ե饰�ν����
		applicantEnterKeyCntDisplay = 0;
	}


	return false;
}


/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : 0 : �ǥե����
//														 : 1 : �������٥��
// obj		:	�ܥ������֥�����ɥ�̾(IFRAME��˥ܥ�������֤�����������Ū�ʻ���򤹤�)

////////// [MSWBt]�Υܥ������ؽ��� //////////////////////////////////////////////////

// ���᡼���Ѵ��ե饰
var countM5 = 0;

// ENTER���������ǥ��᡼���Ѵ���������ե饰
var applicantEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM05( lngMSW , obj )
{

	// ����ե饰�إ������٥��Ƚ���ͤ�����
	applicantEnterKeyCntExchange = lngMSW;


	// �̾�Υ���å����ξ��
	if( applicantEnterKeyCntExchange == 0 )
	{

		if( countM5 == 0 )
		{
			MSWBt05.innerHTML = mswbtE3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt05.innerHTML = mswbtE3;
			}

			countM5 = 1;
		}
		else if( countM5 == 1 )
		{
			MSWBt05.innerHTML = mswbtE1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt05.innerHTML = mswbtE1;
			}

			// �ܥ���إե����������᤹
			//document.all.applicantBt.focus();

			countM5 = 0;
		}

	}

	// �������٥�Ȥ�������ξ��
	else if( applicantEnterKeyCntExchange == 1 )
	{
		MSWBt05.innerHTML = mswbtE1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt05.innerHTML = mswbtE1;
		}

		// �ܥ���إե����������᤹
		//document.all.applicantBt.focus();

	}
	return false;
}









////////////////////////////// [WF INPUT] //////////////////////////////
// �쥤�䡼ɽ������ɽ���ե饰
var Inputcnt = 0;

// ENTER���������ǥ�������������ե饰
var wfinputEnterKeyCntDisplay = 0;

////////// �쥤�䡼ɽ������ɽ������ /////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : '' : �ǥե����
//														 : 1  : �������٥��
//
// objID	: ���֥�����ɥ�IFRAME̾
// objA		: [InputA]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾

/////////////////////////////////////////////////////////////////////////////////////
function DisplayerM06( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// ����ե饰�إ������٥��Ƚ���ͤ�����
		wfinputEnterKeyCntDisplay = lngMSW;
	}


	// �̾�Υ���å����ξ��
	if( wfinputEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && Inputcnt == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame06.style.visibility = "visible";

			objA.focus();

			Inputcnt = 1;
		}
		else if( Inputcnt == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame06.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			Inputcnt = 0;
		}
	}

	// �������٥�Ȥ�������ξ��
	else if( wfinputEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame06.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		wfinputEnterKeyCntDisplay = 2;

		Inputcnt = 0;
	}


	// �������٥�ȤΥ�����쥯�Ƚ���
	else if( wfinputEnterKeyCntDisplay == 2 )
	{
		// ����ե饰�ν����
		wfinputEnterKeyCntDisplay = 0;
	}


	return false;
}


/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : 0 : �ǥե����
//														 : 1 : �������٥��
// obj		:	�ܥ������֥�����ɥ�̾(IFRAME��˥ܥ�������֤�����������Ū�ʻ���򤹤�)

////////// [MSWBt]�Υܥ������ؽ��� //////////////////////////////////////////////////
// ���᡼���Ѵ��ե饰
var countM6 = 0;

// ENTER���������ǥ��᡼���Ѵ���������ե饰
var wfinputEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM06( lngMSW , obj )
{

	// ����ե饰�إ������٥��Ƚ���ͤ�����
	wfinputEnterKeyCntExchange = lngMSW;

	// �̾�Υ���å����ξ��
	if( wfinputEnterKeyCntExchange == 0 )
	{

		if( countM6 == 0 )
		{
			MSWBt06.innerHTML = mswbtF3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt06.innerHTML = mswbtF3;
			}

			countM6 = 1;
		}
		else if( countM6 == 1 )
		{
			MSWBt06.innerHTML = mswbtF1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt06.innerHTML = mswbtF1;
			}

			// �ܥ���إե����������᤹
			//document.all.wfinputBt.focus();

			countM6 = 0;
		}

	}


	// �������٥�Ȥ�������ξ��
	else if( wfinputEnterKeyCntExchange == 1 )
	{
		MSWBt06.innerHTML = mswbtF1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt06.innerHTML = mswbtF1;
		}

		// �ܥ���إե����������᤹
		//document.all.wfinputBt.focus();

	}
	return false;
}







///////////////////////////// [VENDOR & IN CHARGE NAME] /////////////////////////////

// �쥤�䡼ɽ������ɽ���ե饰
var VIcnt = 0;

// ENTER���������ǥ�������������ե饰
var viEnterKeyCntDisplay = 0;

////////// �쥤�䡼ɽ������ɽ������ /////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : '' : �ǥե����
//														 : 1  : �������٥��
//
// objID	: ���֥�����ɥ�IFRAME̾
// objA		: [InputA]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾
// objB		: [InputB]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾

/////////////////////////////////////////////////////////////////////////////////////

function DisplayerM07( lngMSW , objID , objA , objB )
{

	if( lngMSW != '' )
	{
		// ����ե饰�إ������٥��Ƚ���ͤ�����
		viEnterKeyCntDisplay = lngMSW;
	}


	// �̾�Υ���å����ξ��
	if( viEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && VIcnt == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame07.style.visibility = "visible";

			if( window.VIwin.ViValueFlg == 0 )
			{
				objA.focus();
			}

			if( window.VIwin.ViValueFlg == 1 )
			{
				objB.focus();
			}

			VIcnt = 1;
		}
		else if( VIcnt == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame07.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			VIcnt = 0;
		}
	}


	// �������٥�Ȥ�������ξ��
	else if( viEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame07.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// ������쥯���Ѥ��ͤ�����
		viEnterKeyCntDisplay = 2;
		VIcnt = 0;

	}

	// �������٥�ȤΥ�����쥯�Ƚ���
	else if( viEnterKeyCntDisplay == 2 )
	{
		// ����ե饰�ν����
		viEnterKeyCntDisplay = 0;
	}


	return false;
}





/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : 0 : �ǥե����
//														 : 1 : �������٥��
// obj		:	�ܥ������֥�����ɥ�̾(IFRAME��˥ܥ�������֤�����������Ū�ʻ���򤹤�)

////////// [MSWBt]�Υܥ������ؽ��� //////////////////////////////////////////////////

// ���᡼���Ѵ��ե饰
var countM7 = 0;

// ENTER���������ǥ��᡼���Ѵ���������ե饰
var viEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////

function ExchangeM07( lngMSW , obj )
{

	// ����ե饰�إ������٥��Ƚ���ͤ�����
	viEnterKeyCntExchange = lngMSW;


	// �̾�Υ���å����ξ��
	if( viEnterKeyCntExchange == 0 )
	{
		if( countM7 == 0 )
		{
			MSWBt07.innerHTML = mswbtG3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt07.innerHTML = mswbtG3;
			}

			countM7 = 1;
		}
		else if( countM7 == 1 )
		{
			MSWBt07.innerHTML = mswbtG1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt07.innerHTML = mswbtG1;
			}

			// �ܥ���إե����������᤹
			//document.all.viBt.focus();

			countM7 = 0;
		}
	}


	// �������٥�Ȥ�������ξ��
	else if( viEnterKeyCntExchange == 1 )
	{
		MSWBt07.innerHTML = mswbtG1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt07.innerHTML = mswbtG1;
		}

		// �ܥ���إե����������᤹
		//document.all.viBt.focus();

	}


	return false;
}







////////////////////////////// [SUPPLIER] //////////////////////////////
////////// �쥤�䡼ɽ������ɽ������ /////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : '' : �ǥե����
//														 : 1  : �������٥��
//
// objID	: ���֥�����ɥ�IFRAME̾
// objA		: [InputA]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾

/////////////////////////////////////////////////////////////////////////////////////

// �쥤�䡼ɽ������ɽ���ե饰
var SUPcnt = 0;

// ENTER���������ǥ�������������ե饰
var supplierEnterKeyCntDisplay = 0;

function DisplayerM08( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// ����ե饰�إ������٥��Ƚ���ͤ�����
		supplierEnterKeyCntDisplay = lngMSW;
	}


	// �̾�Υ���å����ξ��
	if( supplierEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && SUPcnt == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame08.style.visibility = "visible";

			objA.focus();

			SUPcnt = 1;
		}

		else if( SUPcnt == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame08.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			SUPcnt = 0;
		}
	}


	// �������٥�Ȥ�������ξ��
	else if( supplierEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame08.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// ������쥯���Ѥ��ͤ�����
		supplierEnterKeyCntDisplay = 2;
		SUPcnt = 0;
	}


	// �������٥�ȤΥ�����쥯�Ƚ���
	else if( supplierEnterKeyCntDisplay == 2 )
	{
		// ����ե饰�ν����
		supplierEnterKeyCntDisplay = 0;
	}


	return false;
}



/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : 0 : �ǥե����
//														 : 1 : �������٥��
// obj		:	�ܥ������֥�����ɥ�̾(IFRAME��˥ܥ�������֤�����������Ū�ʻ���򤹤�)

////////// [MSWBt]�Υܥ������ؽ��� //////////////////////////////////////////////////

// ���᡼���Ѵ��ե饰
var countM8 = 0;

// ENTER���������ǥ��᡼���Ѵ���������ե饰
var supplierEnterKeyCntExchange = 0;
/////////////////////////////////////////////////////////////////////////////////////

function ExchangeM08( lngMSW , obj )
{

	// ����ե饰�إ������٥��Ƚ���ͤ�����
	supplierEnterKeyCntExchange = lngMSW;


	// �̾�Υ���å����ξ��
	if( supplierEnterKeyCntExchange == 0 )
	{

		if( countM8 == 0 )
		{
			MSWBt08.innerHTML = mswbtH3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt08.innerHTML = mswbtH3;
			}

			countM8 = 1;
		}

		else if( countM8 == 1 )
		{
			MSWBt08.innerHTML = mswbtH1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt08.innerHTML = mswbtH1;
			}

			// �ܥ���إե����������᤹
			//document.all.supplierBt.focus();

			countM8 = 0;
		}

	}


	// �������٥�Ȥ�������ξ��
	else if( supplierEnterKeyCntExchange == 1 )
	{
		MSWBt08.innerHTML = mswbtH1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt08.innerHTML = mswbtH1;
		}

		// �ܥ���إե����������᤹
		//document.all.supplierBt.focus();
	}

	return false;
}
















/////////////////////////////////// [INPUT PERSON] //////////////////////////////////

// �쥤�䡼ɽ������ɽ���ե饰
var Input2cnt = 0;

// ENTER���������ǥ�������������ե饰
var inputEnterKeyCntDisplay = 0;

////////// �쥤�䡼ɽ������ɽ������ /////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : '' : �ǥե����
//														 : 1  : �������٥��
//
// objID	: ���֥�����ɥ�IFRAME̾
// objA		: [InputA]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾
// objB		: [InputB]���֥�����ɥ������ץ���˺ǽ�˥ե����������륪�֥�������̾

/////////////////////////////////////////////////////////////////////////////////////

function DisplayerM09( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// ����ե饰�إ������٥��Ƚ���ͤ�����
		inputEnterKeyCntDisplay = lngMSW;
	}


	// �̾�Υ���å����ξ��
	if( inputEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && Input2cnt == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame09.style.visibility = "visible";

			objA.focus();

			Input2cnt = 1;
		}

		else if( Input2cnt == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame09.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			Input2cnt = 0;
		}
	}

	// �������٥�Ȥ�������ξ��
	else if( inputEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame09.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// ������쥯���Ѥ��ͤ�����
		inputEnterKeyCntDisplay = 2;
		Input2cnt = 0;

	}

	// �������٥�ȤΥ�����쥯�Ƚ���
	else if( inputEnterKeyCntDisplay == 2 )
	{
		// ����ե饰�ν����
		inputEnterKeyCntDisplay = 0;
	}


	return false;
}




/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : 0 : �ǥե����
//														 : 1 : �������٥��
// obj		:	�ܥ������֥�����ɥ�̾(IFRAME��˥ܥ�������֤�����������Ū�ʻ���򤹤�)

////////// [MSWBt]�Υܥ������ؽ��� //////////////////////////////////////////////////

// ���᡼���Ѵ��ե饰
var countM9 = 0;

// ENTER���������ǥ��᡼���Ѵ���������ե饰
var inputEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM09( lngMSW , obj )
{

	// ����ե饰�إ������٥��Ƚ���ͤ�����
	inputEnterKeyCntExchange = lngMSW;


	// �̾�Υ���å����ξ��
	if( inputEnterKeyCntExchange == 0 )
	{

		if( countM9 == 0 )
		{
			MSWBt09.innerHTML = mswbtI3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt09.innerHTML = mswbtI3;
			}

			countM9 = 1;
		}
		else if( countM9 == 1 )
		{
			MSWBt09.innerHTML = mswbtI1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt09.innerHTML = mswbtI1;
			}

			// �ܥ���إե����������᤹
			//window.top.Pwin.document.all.inputBt.focus();

			countM9 = 0;
		}

	}


	// �������٥�Ȥ�������ξ��
	else if( inputEnterKeyCntExchange == 1 )
	{
		MSWBt09.innerHTML = mswbtI1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt09.innerHTML = mswbtI1;
		}

		// �ܥ���إե����������᤹
		//window.top.Pwin.document.all.inputBt.focus();

	}

	return false;
}


////////////////////////////// [DATE] //////////////////////////////

// �쥤�䡼ɽ������ɽ���ե饰
var flagDate = 0;

// ENTER���������ǥ�������������ե饰
var dateEnterKeyCntDisplay = 0;

////////// �쥤�䡼ɽ������ɽ������ /////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : '' : �ǥե����
//														 : 1  : �������٥��
//
// objID	: ���֥�����ɥ�IFRAME̾

/////////////////////////////////////////////////////////////////////////////////////
function DisplayerM10( lngMSW , objID )
{

	if( lngMSW != '' )
	{
		// ����ե饰�إ������٥��Ƚ���ͤ�����
		dateEnterKeyCntDisplay = lngMSW;
	}


	// �̾�Υ���å����ξ��
	if( dateEnterKeyCntDisplay == 0 )
	{

		if( lngMSW == '' && flagDate == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame10.style.visibility = "visible";

			flagDate = 1;
		}
		else if( flagDate == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame10.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 245;
			}

			flagDate = 0;
		}
	}

	// �������٥�Ȥ�������ξ��
	else if( dateEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame10.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 245;
		}

		// ������쥯���Ѥ��ͤ�����
		dateEnterKeyCntDisplay = 2;

		flagDate = 0;
	}

	// �������٥�ȤΥ�����쥯�Ƚ���
	else if( dateEnterKeyCntDisplay == 2 )
	{
		// ����ե饰�ν����
		dateEnterKeyCntDisplay = 0;
	}

	return false;
}


/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : 0 : �ǥե����
//														 : 1 : �������٥��
// obj		:	�ܥ������֥�����ɥ�̾(IFRAME��˥ܥ�������֤�����������Ū�ʻ���򤹤�)

////////// [MSWBt]�Υܥ������ؽ��� //////////////////////////////////////////////////
// ���᡼���Ѵ��ե饰
var countM10 = 0;

// ENTER���������ǥ��᡼���Ѵ���������ե饰
var dateEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM10( lngMSW , obj )
{

	// ����ե饰�إ������٥��Ƚ���ͤ�����
	dateEnterKeyCntExchange = lngMSW;

	// �̾�Υ���å����ξ��
	if( dateEnterKeyCntExchange == 0 )
	{

		if( countM10 == 0 )
		{
			DateBtA.innerHTML = datebuttonA3;

			if( typeof(obj) != 'undefined' )
			{
				obj.DateBtA.innerHTML = datebuttonA3;
			}

			countM10 = 1;
		}
		else if( countM10 == 1 )
		{
			DateBtA.innerHTML = datebuttonA;

			if( typeof(obj) != 'undefined' )
			{
				obj.DateBtA.innerHTML = datebuttonA;
			}

			// �ܥ���إե����������᤹
			//document.all.DateBtA.focus();

			countM10 = 0;
		}

	}


	// �������٥�Ȥ�������ξ��
	else if( dateEnterKeyCntExchange == 1 )
	{
		DateBtA.innerHTML = datebuttonA;

		if( typeof(obj) != 'undefined' )
		{
			obj.DateBtA.innerHTML = datebuttonA;
		}

		// �ܥ���إե����������᤹
		//document.all.DateBtA.focus();

	}


	return false;
}











////////////////////////////// [DATEB] //////////////////////////////

// �쥤�䡼ɽ������ɽ���ե饰
var flagDateB = 0;

// ENTER���������ǥ�������������ե饰
var dateBEnterKeyCntDisplay = 0;

////////// �쥤�䡼ɽ������ɽ������ /////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : '' : �ǥե����
//														 : 1  : �������٥��
//
// objID	: ���֥�����ɥ�IFRAME̾

/////////////////////////////////////////////////////////////////////////////////////
function DisplayerM10_2( lngMSW , objID )
{

	if( lngMSW != '' )
	{
		// ����ե饰�إ������٥��Ƚ���ͤ�����
		dateBEnterKeyCntDisplay = lngMSW;
	}


	// �̾�Υ���å����ξ��
	if( dateBEnterKeyCntDisplay == 0 )
	{

		if( lngMSW == '' && flagDateB == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame10_2.style.visibility = "visible";

			flagDateB = 1;
		}
		else if( flagDateB == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame10_2.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 245;
			}

			flagDateB = 0;
		}
	}

	// �������٥�Ȥ�������ξ��
	else if( dateBEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame10_2.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 245;
		}

		// ������쥯���Ѥ��ͤ�����
		dateBEnterKeyCntDisplay = 2;

		flagDateB = 0;
	}

	// �������٥�ȤΥ�����쥯�Ƚ���
	else if( dateBEnterKeyCntDisplay == 2 )
	{
		// ����ե饰�ν����
		dateBEnterKeyCntDisplay = 0;
	}

	return false;
}


/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : 0 : �ǥե����
//														 : 1 : �������٥��
// obj		:	�ܥ������֥�����ɥ�̾(IFRAME��˥ܥ�������֤�����������Ū�ʻ���򤹤�)

////////// [MSWBt]�Υܥ������ؽ��� //////////////////////////////////////////////////
// ���᡼���Ѵ��ե饰
var countM10_2 = 0;

// ENTER���������ǥ��᡼���Ѵ���������ե饰
var dateBEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM10_2( lngMSW , obj )
{

	// ����ե饰�إ������٥��Ƚ���ͤ�����
	dateBEnterKeyCntExchange = lngMSW;

	// �̾�Υ���å����ξ��
	if( dateBEnterKeyCntExchange == 0 )
	{

		if( countM10_2 == 0 )
		{
			DateBtB.innerHTML = datebuttonB3;

			if( typeof(obj) != 'undefined' )
			{
				obj.DateBtB.innerHTML = datebuttonB3;
			}

			countM10_2 = 1;
		}
		else if( countM10_2 == 1 )
		{
			DateBtB.innerHTML = datebuttonB;

			if( typeof(obj) != 'undefined' )
			{
				obj.DateBtB.innerHTML = datebuttonB;
			}

			// �ܥ���إե����������᤹
			//document.all.DateBtB.focus();

			countM10_2 = 0;
		}

	}


	// �������٥�Ȥ�������ξ��
	else if( dateBEnterKeyCntExchange == 1 )
	{
		DateBtB.innerHTML = datebuttonB;

		if( typeof(obj) != 'undefined' )
		{
			obj.DateBtB.innerHTML = datebuttonB;
		}

		// �ܥ���إե����������᤹
		//document.all.DateBtB.focus();

	}


	return false;
}




////////////////////////////// [DATEC] //////////////////////////////

// �쥤�䡼ɽ������ɽ���ե饰
var flagDateC = 0;

// ENTER���������ǥ�������������ե饰
var dateCEnterKeyCntDisplay = 0;

////////// �쥤�䡼ɽ������ɽ������ /////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : '' : �ǥե����
//														 : 1  : �������٥��
//
// objID	: ���֥�����ɥ�IFRAME̾

/////////////////////////////////////////////////////////////////////////////////////
function DisplayerM10_3( lngMSW , objID )
{

	if( lngMSW != '' )
	{
		// ����ե饰�إ������٥��Ƚ���ͤ�����
		dateCEnterKeyCntDisplay = lngMSW;
	}


	// �̾�Υ���å����ξ��
	if( dateCEnterKeyCntDisplay == 0 )
	{

		if( lngMSW == '' && flagDateC == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame10_3.style.visibility = "visible";

			flagDateC = 1;
		}
		else if( flagDateC == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame10_3.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 245;
			}

			flagDateC = 0;
		}
	}

	// �������٥�Ȥ�������ξ��
	else if( dateCEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame10_3.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 245;
		}

		// ������쥯���Ѥ��ͤ�����
		dateCEnterKeyCntDisplay = 2;

		flagDateC = 0;
	}

	// �������٥�ȤΥ�����쥯�Ƚ���
	else if( dateCEnterKeyCntDisplay == 2 )
	{
		// ����ե饰�ν����
		dateCEnterKeyCntDisplay = 0;
	}

	return false;
}


/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	���֥�����ɥ��夫��Υ������٥��Ƚ���� : 0 : �ǥե����
//														 : 1 : �������٥��
// obj		:	�ܥ������֥�����ɥ�̾(IFRAME��˥ܥ�������֤�����������Ū�ʻ���򤹤�)

////////// [MSWBt]�Υܥ������ؽ��� //////////////////////////////////////////////////
// ���᡼���Ѵ��ե饰
var countM10_3 = 0;

// ENTER���������ǥ��᡼���Ѵ���������ե饰
var dateCEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM10_3( lngMSW , obj )
{

	// ����ե饰�إ������٥��Ƚ���ͤ�����
	dateCEnterKeyCntExchange = lngMSW;

	// �̾�Υ���å����ξ��
	if( dateCEnterKeyCntExchange == 0 )
	{

		if( countM10_3 == 0 )
		{
			DateBtC.innerHTML = datebuttonC3;

			if( typeof(obj) != 'undefined' )
			{
				obj.DateBtC.innerHTML = datebuttonC3;
			}

			countM10_3 = 1;
		}
		else if( countM10_3 == 1 )
		{
			DateBtC.innerHTML = datebuttonC;

			if( typeof(obj) != 'undefined' )
			{
				obj.DateBtC.innerHTML = datebuttonC;
			}

			// �ܥ���إե����������᤹
			//document.all.DateBtC.focus();

			countM10_3 = 0;
		}

	}


	// �������٥�Ȥ�������ξ��
	else if( dateCEnterKeyCntExchange == 1 )
	{
		DateBtC.innerHTML = datebuttonC;

		if( typeof(obj) != 'undefined' )
		{
			obj.DateBtC.innerHTML = datebuttonC;
		}

		// �ܥ���إե����������᤹
		//document.all.DateBtC.focus();

	}


	return false;
}





///////////////////////////////////////////////////////////////////////////
/* Drag Layer */

var selected = null;
var offsetX = 0;
var offsetY = 0;

function getDivLeft(div){
	return div.offsetLeft;
}

function getDivTop(div){
	return div.offsetTop;
}

function getPageX(e){
	return document.body.scrollLeft + window.event.clientX; // document.body.clientWidth;
}

function getPageY(e){
	return document.body.scrollTop + window.event.clientY; // document.body.clientHeight;
}

////////// PICKUP //////////
function pickup01(layerID,e){ // [VENDOR]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE01;
}

function pickup01_2(layerID,e){ // [CREATION FACTORY]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE01_2;
}

function pickup01_3(layerID,e){ // [ASSEMBLY FACTORY]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE01_3;
}

function pickup02(layerID,e){ // [DEPT & IN CHARGE NAME]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE02;
}

function pickup03(layerID,e){ // [PRODUCTS]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE03;
}

function pickup04(layerID,e){ // [LOCATION]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE04;
}

function pickup05(layerID,e){ // [APPLICANT]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE05;
}

function pickup06(layerID,e){ // [INPUT]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE06;
}

function pickup07(layerID,e){ // [VENDOR & IN CHARGE NAME]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE07;
}

function pickup08(layerID,e){ // [SUPPLIER]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE08;
}
function pickup08_2(layerID,e){ // [SUPPLIER 2]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE08_2;
}

function pickup09(layerID,e){ // [INPUT2]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE09;
}

function pickup10(layerID,e){ // [DATE]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE10;
}


function pickup10_2(layerID,e){ // [DATEB]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE10_2;
}


function pickup10_3(layerID,e){ // [DATEC]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE10_3;
}

////////// DRAG & MOVING //////////
function dragIE01(){ // [VENDOR]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.MVwin.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE01_2(){ // [CREATION FACTORY]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.MVwin_2.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE01_3(){ // [ASSEMBLY FACTORY]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.MVwin_3.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE02(){ // [DEPT & IN CHARGE NAME]
	var movetoX = event.clientX + document.body.scrollLeft - offsetX;
	var movetoY = event.clientY + document.body.scrollTop  - offsetY;

	selected.style.pixelLeft = movetoX;

	selected.style.pixelTop  = movetoY;

	
	//alert(document.body.clientWidth+","+document.body.clientHeight);
	//window.status = 'LEFT: ' + selected.style.pixelLeft + 'px TOP: ' + selected.style.pixelTop + 'px';

	if( window.MDwin.DeptValueFlg == 0 )
	{
		window.MDwin.VarsB01.style.visibility = 'hidden';
	}

	if( window.MDwin.DeptValueFlg == 1 )
	{
		window.MDwin.VarsD01.style.visibility = 'hidden';
	}


	return false;
}

function dragIE03(){ // [PRODUCTS]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.MGwin.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE04(){ // [LOCATION]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.MLwin.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE05(){ // [APPLICANT]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.APPwin.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE06(){ // [INPUT]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.INPUTwin.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE07(){ // [VENDOR & IN CHARGE NAME]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	if( window.VIwin.ViValueFlg == 0 )
	{
		window.VIwin.VarsB01.style.visibility = 'hidden';
	}

	if( window.VIwin.ViValueFlg == 1 )
	{
		window.VIwin.VarsD01.style.visibility = 'hidden';
	}

	return false;
}

function dragIE08(){ // [SUPPLIER]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.SUPwin.VarsB01.style.visibility = 'hidden';

	return false;
}
function dragIE08_2(){ // [SUPPLIER]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.SUPwin.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE09(){ // [INPUT2]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.INPUT2win.VarsB01.style.visibility = 'hidden';

	return false;
}


function dragIE10(){ // [DATE]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	return false;
}


function dragIE10_2(){ // [DATEB]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	return false;
}


function dragIE10_3(){ // [DATEC]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	return false;
}

////////// CLEAR ALL //////////
function clearAll() {
	document.onmousemove = '';
	selected = null;
	offsetX = 0;
	offsetY = 0;
}


//-->