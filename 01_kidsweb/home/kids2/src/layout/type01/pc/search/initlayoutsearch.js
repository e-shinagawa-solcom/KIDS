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
var TabNumA = ''; // �إå���
var TabNumB = ''; // ����


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
var NumPunitTab = '';




///// VIEW SEARCH IMAGE /////
var vishImgJ = '<img src="' + vishJ + '" width="100" height="23" border="0">';
var vishImgE = '<img src="' + vishE + '" width="100" height="23" border="0">';

///// OFF ON BUTTON /////
var offBt = '<a href="#"><img onmouseover="OnBt(this);" onmouseout="OffBt(this);" src="' + off + '" width="19" height="19" border="0"></a>';
var onBt = '<a href="#"><img src="' + on + '" width="19" height="19" border="0"></a>';

function initLayoutPCSearch()
{
	ViewSearch1.innerHTML = vishImgJ;

	CheckAll1.innerHTML = offBt;
	CheckAll2.innerHTML = offBt;
}


function initLayoutSegs( obj1 , obj2 , obj3 , obj4 , obj5 , obj6 , obj7 , obj8 , obj9 )
{
	Backs.style.background = '#d6d0b1';


	var initYpos1 = 50;  //TOP��ɸ�����ʲ�������ܽ����
	var initYpos2 = 50; //TOP��ɸ�����ʴ������ܽ����

	var moveYpos = 31;   //TOP��ɸ����ư��


	var check1Xpos = 10;  //LEFT��ɸ�������å��ܥå���[ɽ��]������
	var check2Xpos = 58; //LEFT��ɸ�������å��ܥå���[����]������

	var segsXpos = 110;  //LEFT��ɸ������������
	var varsXpos = 266; //LEFT��ɸ���ե��������Ǹ�����


	var segsWidth = 157; //�������������

	var FontColors = '#666666'
	var BackColors1 = '#e8f0f1';
	var BackColors2 = '#f1f1f1';
	var BorderColors1 = '#798787 #e8f0f1 #798787 #798787';
	var BorderColors2 = '#798787 #798787 #798787 #e8f0f1';


	var lay1 = obj1.children; //���ʴ������ܥ����
	var lay2 = obj2.children; //���ʴ������ܥե���������
	var lay3 = obj3.children; //���ʲ�������ܥ����
	var lay4 = obj4.children; //���ʲ�������ܥե���������
	var lay5 = obj5.children; //���ʲ����񡦥����å��ܥå���[ɽ��]
	var lay6 = obj6.children; //���ʲ����񡦥����å��ܥå���[����]
	var lay7 = obj7.children; //���ʴ����������å��ܥå���[ɽ��]
	var lay8 = obj8.children; //���ʴ����������å��ܥå���[����]


	var lngtabindex = 1; //TAB INDEX �����


	///// ���ʴ������ܥ����Ÿ�� /////
	if ( obj1 != '' )
	{
		for (i = 0; i < lay1.length; i++)
		{
			lay1[i].style.top = initYpos2 + ( moveYpos * i );
			lay1[i].style.left = segsXpos;
			lay1[i].style.width = segsWidth;
			lay1[i].style.background = BackColors1;
			lay1[i].style.borderColor = BorderColors1;
			lay1[i].style.color = FontColors;
		}
	}

	///// ���ʴ������ܥե���������Ÿ�� /////
	if ( obj2 != '' )
	{
		for (i = 0; i < lay2.length; i++)
		{
			lay2[i].style.top = initYpos2 + ( moveYpos * i );
			lay2[i].style.left = varsXpos;
			lay2[i].style.background = BackColors1;
			lay2[i].style.borderColor = BorderColors2;
		}
	}

	///// ���ʲ�������ܥ����Ÿ�� /////
	if ( obj3 != '' )
	{
		for (i = 0; i < lay3.length; i++)
		{
			lay3[i].style.top = initYpos1 + ( moveYpos * i );
			lay3[i].style.left = segsXpos;
			lay3[i].style.width = segsWidth;
			lay3[i].style.background = BackColors2;
			lay3[i].style.borderColor = BorderColors1;
			lay3[i].style.color = FontColors;
		}
	}

	///// ���ʲ�������ܥե���������Ÿ�� /////
	if ( obj4 != '' )
	{
		for (i = 0; i < lay4.length; i++)
		{
			lay4[i].style.top = initYpos1 + ( moveYpos * i );
			lay4[i].style.left = varsXpos;
			lay4[i].style.background = BackColors2;
			lay4[i].style.borderColor = BorderColors2;
		}
	}

	///// ���ʲ����񡦥����å��ܥå���[ɽ��]Ÿ�� /////
	if ( obj5 != '' )
	{
		for (i = 0; i < lay5.length; i++)
		{
			lay5[i].style.top = initYpos1 + ( moveYpos * i );
			lay5[i].style.left = check1Xpos;
			lay5[i].style.background = BackColors2;
			lay5[i].style.borderColor = BorderColors1;
		}
	}

	///// ���ʲ����񡦥����å��ܥå���[����]Ÿ�� /////
	if ( obj6 !='' )
	{
		for (i = 0; i < lay6.length; i++)
		{
			lay6[i].style.top = initYpos1 + ( moveYpos * i );
			lay6[i].style.left = check2Xpos;
			lay6[i].style.background = BackColors2;
			lay6[i].style.borderColor = BorderColors2;
		}
	}

	///// ���ʴ����������å��ܥå���[����]Ÿ�� /////
	if ( obj7 != '')
	{
		for (i = 0; i < lay7.length; i++)
		{
			lay7[i].style.top = initYpos2 + ( moveYpos * i );
			lay7[i].style.left = check1Xpos;
			lay7[i].style.background = BackColors1;
			lay7[i].style.borderColor = BorderColors1;
		}

		lay7[10].style.background  = '#f1f1f1';
		lay7[10].style.borderColor = '#798787 #e8f0f1 #798787 #798787';
	}

	///// ���ʴ����������å��ܥå���[����]Ÿ�� /////
	if ( obj8 != '' )
	{
		for (i = 0; i < lay8.length; i++)
		{
			lay8[i].style.top = initYpos2 + ( moveYpos * i );
			lay8[i].style.left = check2Xpos;
			lay8[i].style.background = BackColors1;
			lay8[i].style.borderColor = BorderColors2;
		}

		lay8[10].style.background  = '#f1f1f1';
		lay8[10].style.borderColor = '#798787 #798787 #798787 #e8f0f1';
	}

	///// TAB INDEX Ÿ�� /////
	for (i = 0; i < window.PS.elements.length; i++)
	{
		window.PS.elements[i].tabindex = lngtabindex + 1;
	}


	//-------------------------------------------------------------------------
	// v2 tomita update
	//-------------------------------------------------------------------------
	if( typeof(WFStatus) != 'undefined' )
	{
		/*
		WFStatus.outerHTML    = '';
		varWFStatus.outerHTML = '';
		*/
		WFStatus.style.background  = '#f1f1f1';
		WFStatus.style.borderColor = '#798787 #e8f0f1 #798787 #798787';

		varWFStatus.style.background  = '#f1f1f1';
		varWFStatus.style.borderColor = '#798787 #798787 #798787 #e8f0f1';
	}
	//-------------------------------------------------------------------------

	return false;
}





//----------------------------------------------------------------------
// ���� : ���֥�����ɥ����������Υե�������Ŭ�Ѵؿ�
//----------------------------------------------------------------------
function fncIfrmFocusObject( strMode )
{

	switch( strMode )
	{

		case 'dept':
			document.all.strInChargeUserName.focus();
			break;

		case 'products':
			document.all.strProductName.focus();
			break;

		case 'supplier':
			document.all.strCustomerName.focus();
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