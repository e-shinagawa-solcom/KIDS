

//*******************************************************************
// �ޥ���������ˤ����򤵤줿�ޥ��������Υڡ����إꥯ�����Ȥ���ؿ�
// strTableName: �ޥ������ơ��֥�̾
// objForm     : FORM OBJECT
function fncRequestSearchMasterEdit( strTableName, objForm )
{
	// ����ξ��
	if ( !strTableName )
	{
	}

	// ��ҥޥ����ξ��
	else if ( strTableName == 'm_Company' )
	{
		objForm.action = '/m/search/co/search.php';
		objForm.submit();
	}

	// ���롼�ץޥ����ξ��
	else if  ( strTableName == 'm_Group' )
	{
		objForm.action = '/m/search/g/search.php';
		objForm.submit();
	}

	// �̲ߥ졼�ȥޥ����ξ��
	else if  ( strTableName == 'm_MonetaryRate' )
	{
		objForm.action = '/m/search/r/search.php';
		objForm.submit();
	}

	// ����¾�Υޥ����ξ��(���̥ޥ���������)
	else if ( strTableName != '' )
	{
		objForm.action = '/m/list/c/index.php';
		objForm.submit();
	}

	return false;
}



//*******************************************************************
// ���̥ޥ���������ˤ����򤵤줿�ޥ��������Υڡ����إꥯ�����Ȥ���ؿ�
// strTableName: �ޥ����ơ��֥�̾
// objForm     : FORM OBJECT
function fncRequestCommonMasterEdit( strTableName, objForm )
{
	document.all.strMasterTableName.value = strTableName;

	objForm.action = '/m/list/c/index.php';

	objForm.submit();

	return false;
}





/*********************************************************************************/
// objVars1 : [fncShowDialogCommonMaster]���裱����
// objVars2 : [fncShowDialogCommonMaster]���裲����

////////// ���������ƽдؿ�������쥯���Ѵؿ� //////////
function fncQueryDialog( objVars1 , objVars2 )
{

	// ���������ƽ�
	fncShowDialogCommonMaster( objVars1 ,
								window.Pwin.form1 ,
								'ResultIframeCommonMaster' ,
								'NO' ,
								objVars2 ,
								'add' );

}






/*********************************************************************************/
// objInputArray : �Ѵ�����ID������
// objStyleArray : �Ѵ��Ѥ�ID������

// �嵭��Ĥ�������ν��֤Ϥ��줾�죱�У��δط��ˤ��뤳�ȡ�
// ��) fncChangeObjectIdModule( Array( '�Ѵ���ID���Σ�' , '�Ѵ���ID���Σ�' ) , Array( '�Ѵ���ID���Σ�' , '�Ѵ���ID���Σ�' ) )

////////// ���֥������Ȥ�ID�Ѵ��ѥ⥸�塼�� //////////
function fncChangeObjectIdModule( objInputArray , objStyleArray )
{

	for( i = 0; i < objInputArray.length; i ++ )
	{
		// ID�����ؤ�
		document.all( objInputArray[i] ).id = objStyleArray[i];
	}

	return false;
}



////////// [�ɲ�][����]�ѡ����֥������Ȥμ�ư�쥤�������ѥ⥸�塼�� //////////
function fncInitLayoutObjectModule( obj1 , obj2 , lngXpos1 , lngXpos2 )
{
	Backs.style.background = '#f1f1f1'; //d6d0b1


	var initYpos1 = 30;  //TOP��ɸ�����������
	var initYpos2 = 30;  //TOP��ɸ���ե��������ǽ����

	var moveYpos  = 31;  //TOP��ɸ����ư��

	var segsXpos  = lngXpos1; //LEFT��ɸ������������
	var varsXpos  = lngXpos2; //LEFT��ɸ���ե��������Ǹ�����


	var segsWidth = 165; //�������������

	var FontColors    = '#666666'
	var BackColors1   = '#e8f0f1';
	var BackColors2   = '#f1f1f1';
	var BorderColors1 = '#798787 #e8f0f1 #798787 #798787';
	var BorderColors2 = '#798787 #798787 #798787 #e8f0f1';


	var lay1 = obj1.children; //�����
	var lay2 = obj2.children; //�ե���������


	var lngtabindex = 1; //TAB INDEX �����


	///// �����Ÿ�� /////
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

	///// �ե���������Ÿ�� /////
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


	///// TAB INDEX Ÿ�� /////
	for (i = 0; i < window.PS.elements.length; i++)
	{
		window.PS.elements[i].tabindex = lngtabindex + 1;
	}

	return false;
}



/************************************************************/

var g_offY1 = 0;

function fncStopHeader( ObjID1 )
{

	sy = document.body.scrollTop;

	//ObjID.style.visibility = 'hidden';

	ObjID1.style.top = sy + g_offY1;

	//ObjID.style.visibility = 'visible';

}

var g_off3Y1 = 5;
var g_off3Y2 = 0;

function fncStopHeader3( ObjID1 , ObjID2 )
{

	sy = document.body.scrollTop;

	//ObjID.style.visibility = 'hidden';

	ObjID1.style.top = sy + g_off3Y1;
	ObjID2.style.top = sy + g_off3Y2;

	//ObjID.style.visibility = 'visible';

}
/************************************************************/




/************************************************************/

var g_off2Y1 = 20;
var g_off2Y2 = 0;
var g_off2Y3 = 0;

function fncStopHeader2( ObjID1 , ObjID2 , ObjID3 )
{

	sy = document.body.scrollTop;

	//ObjID.style.visibility = 'hidden';

	ObjID1.style.top = sy + g_off2Y1;
	ObjID2.style.top = sy + g_off2Y2;
	ObjID3.style.top = sy + g_off2Y3;

	//ObjID.style.visibility = 'visible';

}
/************************************************************/




/************************************************************/
var g_clickID;                  // ����Υ���å���
var g_lngTrNum;                 // �����TR�ο�
var g_DefaultColor = '';        // ����Υ���å��Ԥο�
var g_SelectColor  = '#bbbbbb'; // ���򤷤��Ȥ��ο�
var trClickFlg     = "on";      // ����å�������ȥե饰

/************************************************************/
// objID : 		����å��ԤΥ��֥�������

////////// TR����Ԥ��طʿ��ѹ��ؿ� //////////
function fncSelectTrColor( objID )
{

	//trClickFlg��off�ΤȤ��Ͻ�����λ
	if( trClickFlg == "off" ) return false;

	// ����Ԥ��Ǥˤ�����ˡ������ο����᤹
	if( g_DefaultColor != '')
	{
		g_clickID.style.background = g_DefaultColor;
	}

	// ������Ʊ������Ԥ����򤷤����ˡ������
	if(g_clickID == objID){
		g_clickID      = '';
		g_DefaultColor = '';
	}
	// ����Ԥο��Ⱦ�����¸����ȿž
	else
	{
		g_clickID      = objID;
		g_DefaultColor = objID.style.background;
		//����Ԥ�ȿž
		objID.style.background = g_SelectColor;
	}

	return false;
}


/************************************************************/
// objID : 		����å��ԤΥ��֥�������
// strIdName : 	TR�ο�����ʬ�Ͻ���ID̾( tda0 �� 'tda' )
// lngTrNum : 	���쥳���ɤ˻��Ѥ��Ƥ���TR�ο�( rowspan�ο� )

////////// TR����Ԥ��طʿ��ѹ��ؿ�[ʣ�����б���] //////////
function fncSelectSomeTrColor( objID , strIdName , lngTrNum )
{

	//trClickFlg��off�ΤȤ��Ͻ�����λ
	if( trClickFlg == "off" ) return false;


	// ����Ԥ��Ǥˤ�����ˡ������ο����᤹
	if( g_DefaultColor != '')
	{
		for ( i = 0; i < g_lngTrNum; i++ )
		{
			document.getElementById( g_clickID + i ).style.background = g_DefaultColor;
		}
	}

	// ������Ʊ������Ԥ����򤷤����ˡ������
	if( g_clickID == strIdName )
	{
		g_clickID      = '';
		g_lngTrNum     = '';
		g_DefaultColor = '';
	}

	// ����Ԥο��Ⱦ�����¸����ȿž
	else
	{
		g_clickID      = strIdName;
		g_lngTrNum     = lngTrNum;
		g_DefaultColor = objID.style.background;

		for ( i = 0; i < g_lngTrNum; i++ )
		{
			document.getElementById( g_clickID + i ).style.background = g_SelectColor;
		}
	}


	return false;
}
/*****************************************************************************/





function fncNoSelectSomeTrColor( objID , strIdName , lngTrNum )
{

	return false;

}



// ADDITION BUTTON
function MasterAddJOff( obj )
{
	obj.src = maddJ1;
}

function MasterAddJOn( obj )
{
	obj.src = maddJ2;
}

function MasterAddEOff( obj )
{
	obj.src = maddE1;
}

function MasterAddEOn( obj )
{
	obj.src = maddE2;
}




// Sort
function SortOn( obj )
{
	obj.style.background = '#bbbbbb'; //6d8aab
}

function SortOff( obj )
{
	obj.style.backgroundColor = '#799797'; /* 6d8aab */
}




//----------------------------------------------------------
// ����ե�����ޥ����Ǥ�ô�����ɲåܥ������
//----------------------------------------------------------
function fncWhiteAddButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = whiteadd1;
			break;

		case 'on':
			obj.src = whiteadd2;
			break;

		default:
			break;
	}
}



//-->