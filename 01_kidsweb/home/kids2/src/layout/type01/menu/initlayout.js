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







//---------------------------------------------------------
// �����ȥ륤�᡼������
//---------------------------------------------------------
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="�ᥤ���˥塼">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="MAIN MENU">';



//---------------------------------------------------------
// ��˥塼�Хå��᡼������
//---------------------------------------------------------
var MenuBackImg = '<img src="' + menuback + '" width="978" height="170" border="0">';




//-----------------------------------------------------------------------------
// ���� : �ܥ��󥤥᡼������
// ���� : ���᡼�����������륪���С��������ޥ������������������
//-----------------------------------------------------------------------------

//---------------------------------------------------------
//Ŭ�� :�־��ʴ�����
//---------------------------------------------------------
var pNaviJ1 = '<a href="#"><img onmouseover="fncPButton( \'onJ\' , this );" onmouseout="fncPButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + pJ1 + '" width="151" height="25" border="0" alt="���ʴ���" tabindex="1"></a>';
var pNaviE1 = '<a href="#"><img onmouseover="fncPButton( \'onE\' , this );" onmouseout="fncPButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + pE1 + '" width="151" height="25" border="0" alt="PRODUCTS" tabindex="1"></a>';


//---------------------------------------------------------
//Ŭ�� :�ָ��Ѹ���������
//---------------------------------------------------------
var esNaviJ1 = '<a href="#"><img onmouseover="fncChangeBtImg( this, esJ2 );" onmouseout="fncChangeBtImg( this, esJ1 ); fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + esJ1 + '" width="151" height="25" border="0" alt="���Ѹ�������" tabindex="2"></a>';
var esNaviE1 = '<a href="#"><img onmouseover="fncChangeBtImg( this, esE2 );" onmouseout="fncChangeBtImg( this, esE1 ); fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + esE1 + '" width="151" height="25" border="0" alt="ESTIMATE" tabindex="2"></a>';


//---------------------------------------------------------
// Ŭ�� :�ּ��������
//---------------------------------------------------------
var soNaviJ1 = '<a href="#"><img onmouseover="fncSOButton( \'onJ\' , this );" onmouseout="fncSOButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + soJ1 + '" width="151" height="25" border="0" alt="�������" tabindex="3"></a>';
var soNaviE1 = '<a href="#"><img onmouseover="fncSOButton( \'onE\' , this );" onmouseout="fncSOButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + soE1 + '" width="151" height="25" border="0" alt="SALES ORDER" tabindex="3"></a>';


//---------------------------------------------------------
// Ŭ�� :��ȯ�������
//---------------------------------------------------------
var poNaviJ1 = '<a href="#"><img onmouseover="fncPOButton( \'onJ\' , this );" onmouseout="fncPOButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + poJ1 + '" width="151" height="25" border="0" alt="ȯ�����" tabindex="4"></a>';
var poNaviE1 = '<a href="#"><img onmouseover="fncPOButton( \'onE\' , this );" onmouseout="fncPOButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + poE1 + '" width="151" height="25" border="0" alt="PURCHASE ORDER" tabindex="4"></a>';


//---------------------------------------------------------
// Ŭ�� :����������
//---------------------------------------------------------
var scNaviJ1 = '<a href="#"><img onmouseover="fncSCButton( \'onJ\' , this );" onmouseout="fncSCButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + scJ1 + '" width="151" height="25" border="0" alt="������" tabindex="5"></a>';
var scNaviE1 = '<a href="#"><img onmouseover="fncSCButton( \'onE\' , this );" onmouseout="fncSCButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + scE1 + '" width="151" height="25" border="0" alt="SALES CONTROL" tabindex="5"></a>';


//---------------------------------------------------------
// Ŭ�� :�ֻ���������
//---------------------------------------------------------
var pcNaviJ1 = '<a href="#"><img onmouseover="fncPCButton( \'onJ\' , this );" onmouseout="fncPCButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + pcJ1 + '" width="151" height="25" border="0" alt="��������" tabindex="6"></a>';
var pcNaviE1 = '<a href="#"><img onmouseover="fncPCButton( \'onE\' , this );" onmouseout="fncPCButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + pcE1 + '" width="151" height="25" border="0" alt="PURCHASE CONTROL" tabindex="6"></a>';


//---------------------------------------------------------
// Ŭ�� :�֥���ե���
//---------------------------------------------------------
var wfNaviJ1 = '<a href="#"><img onmouseover="fncWFButton( \'onJ\' , this );" onmouseout="fncWFButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + wfJ1 + '" width="151" height="25" border="0" alt="����ե�" tabindex="7"></a>';
var wfNaviE1 = '<a href="#"><img onmouseover="fncWFButton( \'onE\' , this );" onmouseout="fncWFButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + wfE1 + '" width="151" height="25" border="0" alt="WORK FLOW" tabindex="7"></a>';


//---------------------------------------------------------
// Ŭ�� :��Ģɼ���ϡ�
//---------------------------------------------------------
var listNaviJ1 = '<a href="#"><img onmouseover="fncLISTButton( \'onJ\' , this );" onmouseout="fncLISTButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + listoutJ1 + '" width="151" height="25" border="0" alt="Ģɼ����" tabindex="8"></a>';
var listNaviE1 = '<a href="#"><img onmouseover="fncLISTButton( \'onE\' , this );" onmouseout="fncLISTButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + listoutE1 + '" width="151" height="25" border="0" alt="LIST OUTPUT" tabindex="8"></a>';


//---------------------------------------------------------
// Ŭ�� :�֥ǡ����������ݡ��ȡ�
//---------------------------------------------------------
var dataexNaviJ1 = '<a href="#"><img onmouseover="fncDATAEXButton( \'onJ\' , this );" onmouseout="fncDATAEXButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + dataexJ1 + '" width="151" height="25" border="0" alt="�ǡ����������ݡ���" tabindex="9"></a>';
var dataexNaviE1 = '<a href="#"><img onmouseover="fncDATAEXButton( \'onE\' , this );" onmouseout="fncDATAEXButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + dataexE1 + '" width="151" height="25" border="0" alt="DATA EXPORT" tabindex="9"></a>';

//---------------------------------------------------------
//Ŭ�� :�֥��åץ��ɡ�
//---------------------------------------------------------
var uploadNaviJ1 = '<a href="#"><img onmouseover="fncChangeBtImg( this, uploadJ2 );" onmouseout="fncChangeBtImg( this, uploadJ1 ); fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + uploadJ1 + '" width="151" height="25" border="0" alt="���åץ���" tabindex="10"></a>';
var uploadNaviE1 = '<a href="#"><img onmouseover="fncChangeBtImg( this, uploadE2 );" onmouseout="fncChangeBtImg( this, uploadE1 ); fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + uploadE1 + '" width="151" height="25" border="0" alt="UPLOAD" tabindex="10"></a>';


//---------------------------------------------------------
//Ŭ�� :�ֶⷿ���������
//---------------------------------------------------------
var mmNaviJ1 = '<a href="#"><img onmouseover="fncMMButton( \'onJ\' , this );" onmouseout="fncMMButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + mmJ1 + '" width="151" height="25" border="0" alt="�ⷿ�������" tabindex="11"></a>';
var mmNaviE1 = '<a href="#"><img onmouseover="fncMMButton( \'onE\' , this );" onmouseout="fncMMButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + mmE1 + '" width="151" height="25" border="0" alt="MOLD HISTORY" tabindex="11"></a>';

//---------------------------------------------------------
//Ŭ�� :�ֶⷿĢɼ������
//---------------------------------------------------------
var mrNaviJ1 = '<a href="#"><img onmouseover="fncMRButton( \'onJ\' , this );" onmouseout="fncMRButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + mrJ1 + '" width="151" height="25" border="0" alt="�ⷿĢɼ����" tabindex="11"></a>';
var mrNaviE1 = '<a href="#"><img onmouseover="fncMRButton( \'onE\' , this );" onmouseout="fncMRButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + mrE1 + '" width="151" height="25" border="0" alt="MOLD REPORT" tabindex="11"></a>';


//---------------------------------------------------------
// Ŭ�� :�֥桼����������
//---------------------------------------------------------
var ucNavi1 = '<a href="#"><img onmouseover="fncChangeBtImg( this, uc2 );" onmouseout="fncChangeBtImg( this, uc1 ); fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + uc1 + '" width="72" height="20" border="0" alt="USER" tabindex="12"></a>';

var ucNaviJ1 = '<a href="#"><img onmouseover="fncUCButton( \'onJ\' , this );" onmouseout="fncUCButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + ucJ1 + '" width="151" height="25" border="0" alt="�桼��������" tabindex="12"></a>';
var ucNaviE1 = '<a href="#"><img onmouseover="fncUCButton( \'onE\' , this );" onmouseout="fncUCButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + ucE1 + '" width="151" height="25" border="0" alt="USER CONTROL" tabindex="12"></a>';


//---------------------------------------------------------
// Ŭ�� :�֥ޥ���������
//---------------------------------------------------------
var mNavi1 = '<a href="#"><img onmouseover="fncChangeBtImg( this, mst2 );" onmouseout="fncChangeBtImg( this, mst1 ); fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + mst1 + '" width="72" height="20" border="0" alt="MASTER" tabindex="13"></a>';

var mNaviJ1 = '<a href="#"><img onmouseover="fncMButton( \'onJ\' , this );" onmouseout="fncMButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + mstJ1 + '" width="151" height="25" border="0" alt="�ޥ���������" tabindex="13"></a>';
var mNaviE1 = '<a href="#"><img onmouseover="fncMButton( \'onE\' , this );" onmouseout="fncMButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + mstE1 + '" width="151" height="25" border="0" alt="MASTER" tabindex="13"></a>';


//---------------------------------------------------------
// Ŭ�� :����������
//---------------------------------------------------------
var dataclosedNavi1 = '<a href="#"><img onmouseover="fncDataClosedButton( \'on\' , this );" onmouseout="fncDataClosedButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + dataclosed1 + '" width="72" height="20" border="0" alt="DATA CLOSED" tabindex="14"></a>';


//---------------------------------------------------------
// Ŭ�� :�֥����ƥ������
//---------------------------------------------------------
var sysNavi1 = '<a href="#"><img onmouseover="fncSYSButton( \'on\' , this );" onmouseout="fncSYSButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + sys1 + '" width="72" height="20" border="0" alt="SYSTEM" tabindex="15"></a>';







function initLayoutSegs( obj1 , obj2 )
{

	var initXpos1 = 24;  //LEFT��ɸ�������1
	var initXpos2 = 24;  //LEFT��ɸ�������2 185

	var moveXpos = 160;   //LEFT��ɸ����ư��



	var navi1Ypos = 330;  //TOP��ɸ��������1
	var navi2Ypos = 400;  //TOP��ɸ��������2



	var lay1 = obj1.children; //��˥塼������
	var lay2 = obj2.children; //��˥塼������


	///// ��˥塼������Ÿ�� /////
	if (typeof(obj1)!='undefined')
	{
		for (i = 0; i < lay1.length; i++)
		{
			lay1[i].style.left = initXpos1 + ( moveXpos * i );
			lay1[i].style.top = navi1Ypos;
			//lay1[i].style.background = BackColors1;
			//lay1[i].style.borderColor = BorderColors1;
			//lay1[i].style.color = FontColors;
		}
	}


	///// ��˥塼������Ÿ�� /////
	if (typeof(obj2)!='undefined')
	{
		for (i = 0; i < lay2.length; i++)
		{
			lay2[i].style.left = initXpos2 + ( moveXpos * i );
			lay2[i].style.top = navi2Ypos;
			//lay1[i].style.background = BackColors1;
			//lay1[i].style.borderColor = BorderColors1;
			//lay1[i].style.color = FontColors;
		}
	}

	return false;
}





function initLayoutNavi()
{

	if (typeof(MainTitle)!='undefined')
	{
		MainTitle.innerHTML = maintitleJ;
	}

	if (typeof(MenuBacksImg)!='undefined')
	{
		MenuBacksImg.innerHTML = MenuBackImg;
	}


	if (typeof(ESnavi)!='undefined')
	{
		ESnavi.innerHTML = esNaviJ1;
	}

	if (typeof(Pnavi)!='undefined')
	{
		Pnavi.innerHTML = pNaviJ1;
	}

	if (typeof(SOnavi)!='undefined')
	{
		SOnavi.innerHTML = soNaviJ1;
	}

	if (typeof(POnavi)!='undefined')
	{
		POnavi.innerHTML = poNaviJ1;
	}

	if (typeof(SCnavi)!='undefined')
	{
		SCnavi.innerHTML = scNaviJ1;
	}

	if (typeof(PCnavi)!='undefined')
	{
		PCnavi.innerHTML = pcNaviJ1;
	}

	if (typeof(WFnavi)!='undefined')
	{
		WFnavi.innerHTML = wfNaviJ1;
	}

	if (typeof(UCnavi)!='undefined')
	{
		UCnavi.innerHTML = ucNavi1;
	}

	if (typeof(LISTnavi)!='undefined')
	{
		LISTnavi.innerHTML = listNaviJ1;
	}

	if (typeof(DATAEXnavi)!='undefined')
	{
		DATAEXnavi.innerHTML = dataexNaviJ1;
	}

	if (typeof(UPLOADnavi)!='undefined')
	{
		UPLOADnavi.innerHTML = uploadNaviJ1;
	}

	if (typeof(Mnavi)!='undefined')
	{
		Mnavi.innerHTML = mNavi1;
	}

	if (typeof(SYSnavi)!='undefined')
	{
		SYSnavi.innerHTML = sysNavi1;
	}

	if (typeof(DATACLOSEDnavi)!='undefined')
	{
		DATACLOSEDnavi.innerHTML = dataclosedNavi1;
	}

	if (typeof(MMnavi)!='undefined')
	{
		MMnavi.innerHTML = mmNaviJ1;
	}

	if (typeof(MRnavi)!='undefined')
	{
		MRnavi.innerHTML = mrNaviJ1;
	}

	return false;
}


//-->
