<!--


//-----------------------------------------------------------------------------
// ���� : �������ѿ����
// ���� :��TabIndex���ͤ�����
//-----------------------------------------------------------------------------

//------------------------------------------
// Ŭ�Ѳս� :�֥��֥�����ɥ��ܥ����
//------------------------------------------
var NumTabA1   = '' ; // vendor
var NumTabA1_2 = '' ; // creation
var NumTabA1_3 = '' ; // assembly
var NumTabB1   = '' ; // dept
var NumTabC1   = '' ; // products
var NumTabD1   = '' ; // location
var NumTabE1   = '' ; // applicant
var NumTabF1   = '' ; // wfinput
var NumTabG1   = '' ; // vi
var NumTabH1   = '' ; // supplier
var NumTabI1   = '' ; // input


//------------------------------------------
// Ŭ�Ѳս� :�־��ʴ����ץ���
//------------------------------------------
var PTabNumA = '' ; // A
var PTabNumB = '' ; // B


//------------------------------------------
// Ŭ�Ѳս� :�ּ���ȯ����塦�����ץ���
//------------------------------------------
var TabNumA = '' ; // �إå���
var TabNumB = '' ; // ����


//------------------------------------------
// Ŭ�Ѳս� :����Ͽ�ܥ����
//------------------------------------------
var RegistNum = '' ;


//------------------------------------------
// Ŭ�Ѳս� :�ֹ��ɲåܥ����
//------------------------------------------
var AddRowNum = '' ;


//------------------------------------------
// Ŭ�Ѳս� :�֥��������ܥ����
//------------------------------------------
var NumDateTabA = '' ;
var NumDateTabB = '' ;
var NumDateTabC = '' ;


//------------------------------------------
// Ŭ�Ѳս� :�����ʿ��̥ܥ����
//------------------------------------------
var NumPunitTab = '' ;






///// VIEW SEARCH IMAGE /////
var vishImgJ = '<img src="' + vishJ + '" width="100" height="23" border="0">';
var vishImgE = '<img src="' + vishE + '" width="100" height="23" border="0">';

///// OFf ON BUTTON /////
var offBt = '<a href="#"><img onmouseover="OnBt(this);" onmouseout="OffBt(this);" src="' + off + '" width="19" height="19" border="0"></a>';
var onBt = '<a href="#"><img src="' + on + '" width="19" height="19" border="0"></a>';

function initLayoutESSearch()
{
	ViewSearch1.innerHTML = vishImgJ;

	CheckAll1.innerHTML = offBt;
	CheckAll2.innerHTML = offBt;
}


function initLayoutSegs( obj1, obj2, obj3, obj4 )
{
	Backs.style.background = '#d6d0b1';


	var initYpos1 = 50; //TOP��ɸ�����ʲ�������ܽ����
	var initYpos2 = 50; //TOP��ɸ�����ʴ������ܽ����

	var moveYpos = 31;  //TOP��ɸ����ư��


	var check1Xpos = 10; //LEFT��ɸ�������å��ܥå���[ɽ��]������
	var check2Xpos = 58; //LEFT��ɸ�������å��ܥå���[����]������

	var segsXpos = 110; //LEFT��ɸ������������
	var varsXpos = 266; //LEFT��ɸ���ե��������Ǹ�����


	var segsWidth = 157; //�������������

	var FontColors    = '#666666';
	var BackColors1   = '#e8f0f1';
	var BackColors2   = '#f1f1f1';
	var BorderColors1 = '#798787 #e8f0f1 #798787 #798787';
	var BorderColors2 = '#798787 #798787 #798787 #e8f0f1';


	var lay1 = obj1.children; //�����
	var lay2 = obj2.children; //�ե���������
	var lay3 = obj3.children; //�����å��ܥå���[ɽ��]
	var lay4 = obj4.children; //�����å��ܥå���[����]


	var lngtabindex = 1; //TAB INDEX �����


	///// �����Ÿ�� /////
	if ( obj1 != '' )
	{
		for (i = 0; i < lay1.length; i++)
		{
			lay1[i].style.top         = initYpos2 + ( moveYpos * i );
			lay1[i].style.left        = segsXpos;
			lay1[i].style.width       = segsWidth;
			lay1[i].style.background  = BackColors1;
			lay1[i].style.borderColor = BorderColors1;
			lay1[i].style.color       = FontColors;
		}
	}

	///// �ե���������Ÿ�� /////
	if ( obj2 != '' )
	{
		for (i = 0; i < lay2.length; i++)
		{
			lay2[i].style.top         = initYpos2 + ( moveYpos * i );
			lay2[i].style.left        = varsXpos;
			lay2[i].style.background  = BackColors1;
			lay2[i].style.borderColor = BorderColors2;
		}
	}

	///// �����å��ܥå���[����]Ÿ�� /////
	if ( obj3 != '')
	{
		for (i = 0; i < lay3.length; i++)
		{
			lay3[i].style.top         = initYpos2 + ( moveYpos * i );
			lay3[i].style.left        = check1Xpos;
			lay3[i].style.background  = BackColors1;
			lay3[i].style.borderColor = BorderColors1;

			lay3[24].style.background  = '#f1f1f1';
			lay3[24].style.borderColor = '#798787 #e8f0f1 #798787 #798787';
		}
	}

	///// �����å��ܥå���[ɽ��]Ÿ�� /////
	if ( obj4 != '' )
	{
		for (i = 0; i < lay4.length; i++)
		{
			lay4[i].style.top         = initYpos2 + ( moveYpos * i );
			lay4[i].style.left        = check2Xpos;
			lay4[i].style.background  = BackColors1;
			lay4[i].style.borderColor = BorderColors2;

			lay4[24].style.background  = '#f1f1f1';
			lay4[24].style.borderColor = '#798787 #798787 #798787 #e8f0f1';
		}
	}


	///// TAB INDEX Ÿ�� /////
	for (i = 0; i < window.PS.elements.length; i++)
	{
		window.PS.elements[i].tabindex = lngtabindex + 1;
	}


	if( typeof(WFStatus) != 'undefined' )
	{
		WFStatus.style.background  = '#f1f1f1';
		WFStatus.style.borderColor = '#798787 #e8f0f1 #798787 #798787';

		varWFStatus.style.background  = '#f1f1f1';
		varWFStatus.style.borderColor = '#798787 #798787 #798787 #e8f0f1';
	}


	return false;
}






//----------------------------------------------------------------------
// ���� : ���֥�����ɥ����������Υե�������Ŭ�Ѵؿ�
//----------------------------------------------------------------------
function fncIfrmFocusObject( strMode )
{

	switch( strMode )
	{

		case 'creation':
			document.all.strFactoryName.focus();
			break;

		case 'assembly':
			document.all.strAssemblyFactoryName.focus();
			break;

		case 'dept':
			document.all.strInChargeUserName.focus();
			break;

		case 'location':
			document.all.strDeliveryPlaceName.focus();
			break;

		case 'vi':
			document.all.strCustomerUserName.focus();
			break;

		case 'input':
			document.all.strInputUserName.focus();
			break;

		default:
			break;

	}

	return false;
}




//-->