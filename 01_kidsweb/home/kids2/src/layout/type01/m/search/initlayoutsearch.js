<!--


//// [M] MSW BUTTON TAB INDEX NUM /////
var NumTabA1 = '';
var NumTabA1_2 = '';
var NumTabA1_3 = '';
var NumTabB1 = '';
var NumTabC1 = '';
var NumTabD1 = '';
var NumTabE1 = '';
var NumTabF1 = '';
var NumTabG1 = '';
var NumTabH1 = '';
var NumTabI1 = '';



//// [M] DATE BUTTON TAB INDEX NUM /////
var NumDateTabA = '';
var NumDateTabB = '';



// ��Ͽ�ܥ���
var RegistNum = '';

var AddRowNum = '';


var TabNumA = '';
var TabNumB = '';


///// VIEW SEARCH IMAGE /////
var vishImgJ = '<img src="' + vishJ + '" width="100" height="23" border="0">';
var vishImgE = '<img src="' + vishE + '" width="100" height="23" border="0">';

///// OFF ON BUTTON /////
var offBt = '<a href="#"><img onmouseover="OnBt(this);" onmouseout="OffBt(this);" src="' + off + '" width="19" height="19" border="0"></a>';
var onBt = '<a href="#"><img src="' + on + '" width="19" height="19" border="0"></a>';

function initLayoutMSearch()
{

	ViewSearch1.innerHTML = vishImgJ;

	CheckAll1.innerHTML = offBt;
	CheckAll2.innerHTML = offBt;

}


function initLayoutSegs( obj1 , obj2 , obj5 , obj6 )
{
	Backs.style.background = '#d6d0b1';


	var initYpos1 = 50;  //TOP��ɸ�����ʲ�������ܽ����
	var initYpos2 = 20; //TOP��ɸ�����ʴ������ܽ����

	var moveYpos = 31;   //TOP��ɸ����ư��


	var check1Xpos = 10;  //LEFT��ɸ�������å��ܥå���[ɽ��]������
	var check2Xpos = 58; //LEFT��ɸ�������å��ܥå���[����]������

	var segsXpos = 10;  //LEFT��ɸ������������
	var varsXpos = 166; //LEFT��ɸ���ե��������Ǹ�����


	var segsWidth = 157; //�������������

	var FontColors = '#666666'
	var BackColors1 = '#e8f0f1';
	var BackColors2 = '#f1f1f1';
	var BorderColors1 = '#798787 #e8f0f1 #798787 #798787';
	var BorderColors2 = '#798787 #798787 #798787 #e8f0f1';


	var lay1 = obj1.children; //���ʴ������ܥ����
	var lay2 = obj2.children; //���ʴ������ܥե���������
	//var lay3 = obj3.children; //���ʲ�������ܥ����
	//var lay4 = obj4.children; //���ʲ�������ܥե���������
	var lay5 = obj5.children; //���ʲ����񡦥����å��ܥå���[ɽ��]
	var lay6 = obj6.children; //���ʲ����񡦥����å��ܥå���[����]
	//var lay7 = obj7.children; //���ʴ����������å��ܥå���[ɽ��]
	//var lay8 = obj8.children; //���ʴ����������å��ܥå���[����]


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
	if ( obj6 != '' )
	{
		for (i = 0; i < lay6.length; i++)
		{
			lay6[i].style.top = initYpos1 + ( moveYpos * i );
			lay6[i].style.left = check2Xpos;
			lay6[i].style.background = BackColors2;
			lay6[i].style.borderColor = BorderColors2;
		}
	}


	///// TAB INDEX Ÿ�� /////
	for (i = 0; i < window.form1.elements.length; i++)
	{
		window.form1.elements[i].tabindex = lngtabindex + 1;
	}

	return false;
}


//-->