<!--
//: ----------------------------------------------------------------------------
//: �ե����복�ס�
//:               ���̴ؿ�
//: ����        ��
//:               
//:
//: ������      ��YYYY/MM/MM
//: ������      ��** **
//: ��������    ��
//: ----------------------------------------------------------------------------


//@*****************************************************************************
// ����   ��BEEP SOUND
//******************************************************************************
function beep()
{
	BeepSound.src='/error/lupin.wav';
}


//@*****************************************************************************
// ����   ��ʸ�������ʸ���������֤�������
// �ʎߎ׎Ҏ�������strIn,  [String��], ʸ����
//          strExp, [String��], �֤���������ʸ����
//          strNew, [String��], �֤��������ʸ����
// ����� ���֤��������ʸ����  [String��]
// ����   ��1ʸ���ʾ夢�� strIn �� strExp ������ strNew ���֤�������
//******************************************************************************
function ComfncStringReplace(strIn, strExp, strNew)
{
	var strOut = new String(strIn);
	var i      = 0;

	if( strIn.length == 0 || strIn == "" ) return strOut;
  	while( strOut.search(strExp) != -1 )
	{
	    strOut = strOut.replace( strExp, strNew );
	    i++;
	    if( i >= strIn.length ) break;
	}
    return strOut;
}


//@*****************************************************************************
// ����   ��ʸ������λ�����ػ���ʸ���򎾎��Ĥ���
// �ʎߎ׎Ҏ�������sValue, [String],  �о�ʸ����
//          nPnt,   [Number],  ʬ��Ύߎ��ݎ�
//          sSet,   [String],  ������ʸ��
//          nFlg,   [Number],  sValue���¸�ߤ���sSet�ν��� true-��������� false-�Ĥ�
//
// ����� ������ʸ���������Ĥ��줿ʸ����
//
// ����   ��
//        ��
// ���   ���׎��̎ގ׎�����ƽФ���Ƥ���ؿ�
//            ComfncHyphenFormat, 
//            ComfncYMDFormat
//******************************************************************************
function ComfncSplitSetString( sValue, nPnt, sSet, nFlg )
{
	var sVal = new String(sValue);
	var sIn  = "";
	if( sVal.length == 0 || sVal == "" ) return "";
	if( nFlg )
	{
		//var re  = new RegExp("\\x2d"); // -
		var re  = sSet;
		sIn     = new String(ComfncStringReplace(sVal, re, ""));
	}
	else
	{
		sIn     = sVal;
	}

	var sFront = new String(sIn.substr(0,nPnt));
	var sBack  = new String(sIn.substr(nPnt));

	var sRet   = "";

	if( sFront != "" && sBack != "" )
	{
		sRet = sFront + sSet + sBack;
	}
	else
	{
		sRet = sFront;
	}

	return sRet;
}


//@*****************************************************************************
//* ����   ��ComfncMoneyFormat
//******************************************************************************
function ComfncMoneyFormat(oTxt)
{
	var s = oTxt.value;

//alert("�֤������оݤ�ʸ����->" + oTxt.value );
	var re = new RegExp("\\x2e");
	var sIn = ComfncStringReplace(oTxt.value, re, ",");
//alert("�ִ����ޤ���" + sIn);

	var b = ComfncStringReplace(sIn, ",", "");
	sIn = String(Number(b));
	if( isNaN(b) )
	{
		ErrMeg.style.visibility = 'visible' ;
		ERmark.style.visibility = 'visible' ;
		beep();
	//MegWin.style.visibility = 'visible' ;
		ErrMeg.innerText = '���ͤ����Ϥ��Ʋ�����' ;
	//alert("���ͤ����Ϥ��Ʋ�����\n" + b);
	//oTxt.focus();
		return false;
	}
	else 
	{
		ErrMeg.style.visibility = 'hidden' ;
		ERmark.style.visibility = 'hidden' ;
	//MegWin.style.visibility = 'hidden' ;
	}

	var nlen = sIn.length;
	var cnt  = 0;
	var ary  = new Array;
	var aryr = new Array;
	var s    = "";

	ary = sIn.split("");
	for( i = (nlen-1); i >= 0; i-- )
	{
	    //if( cnt == 3 || cnt == 6 || cnt == 9) s += ',';
	    if( cnt != 0 && (cnt % 3) == 0 ) s += ',';
	    s += String(ary[i]);
	    cnt++;
	}
	ary = s.split("");
	aryr = ary.reverse();
	s = String(aryr.join(""));
	oTxt.value = s;
}


//@*****************************************************************************
//* ����   �����եե����ޥå� YYYY/MM/DD ���Ѵ�����
//* �ʎߎ׎Ҏ�������sValue,  [String],  ʸ�������ա�YYYYMMDD)
//*
//* ����� ��YYYY/MM/DD
//*
//* ����   ��sValue(YYYYMMDD) �� YYYY/MM/DD ���Ѵ�����
//*        ��
//* ���   ���������Ϸ������ξ��� alert��ɽ������롣
//******************************************************************************
function ComfncYMDFormat(sValue)
{
	if( sValue == "" ) return "";

	var re   = new RegExp("\\x2f");
	var sVal = new String(ComfncStringReplace(sValue, re, ""));

	var sYMD = "";

	if( sVal.length == 8 )
	{
		ErrMeg.style.visibility = 'hidden' ;
		ERmark.style.visibility = 'hidden' ;
	//MegWin.style.visibility = 'hidden' ;
		sYMD = new String(ComfncSplitSetString( sVal, 4, '/', false ));
		sYMD = new String(ComfncSplitSetString( sYMD, 7, '/', false ));
	}
	else
	{
		ErrMeg.style.visibility = 'visible' ;
		ERmark.style.visibility = 'visible' ;
		beep();
	//MegWin.style.visibility = 'visible' ;
		ErrMeg.innerText = '���դϡ�YYYYMMDD�����ϡ�YYYY/MM/DD�פη��������Ϥ��Ʋ�����' ;
	//alert("���դ� 'YYYYMMDD' ���� 'YYYY/MM/DD' �η��������Ϥ��Ʋ�����");
		sYMD = sValue;
	}
	var dDate = false;
	if( (dDate = ComfncDateValidity(sYMD)) == "" )
	{
		//alert('̵�������դǤ�');
		return sValue;
	}
	else
	{
		sYMD = dDate;
	}
	return sYMD;
}

//-->