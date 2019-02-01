<!--
/**
* �����Ȥ�����ؿ���
*
* @package k.i.d.s.
* @license http://www.wiseknot.co.jp/
* @copyright Copyright &copy; 2004, Wiseknot
* @author tetsuka takafumi
* @version 0.1
*
* ��������
* 2004.03.02 ����������Ǽ�����ܤ�ɬ�ܤ�����
* 2004.03.02 ����ȯ����塢�����γ���Ͽ�ˤ����ٹԤˤ�ñ��������Ͽ���ǽ���ѹ�
* 2004.03.03 ��������Ǽ�����ܤ�ɬ�ܤ�����
* 2004.03.19 fncHdMonetaryUnitCode()
*            ��������塢���ܱ߰ʳ��ξ�硢�Ƕ�ʬ�������ǡפˤ���
* 2004.03.26 ����ȯ������κݤˤ��Ϥ��줿���ֹ�򵭲�����褦���ѹ��ʽ������б���
* 2004.03.29 fncDtCalTotalPrice��fncDtCalTaxPrice2()
*			�����������ܱߤξ�硢�ڼΤƽ���
* 2004.03.29 fncDtCheck()��fncDtNewAry()
* 			ȯ����������No.�פϿ����Ժ������˷Ѿ����ʤ�
* 2004.04.06 fncDtCalTotalPrice()
*			������������ܱߤξ�硢�ڼΤƽ���
* 2004.04.08 
*			�׻���ˡ���̤��ɲ�
* 2004.04.19
*			�����Х��ѿ�̾���ѹ���¾
* 2004.04.22
*			fncHdMonetaryRateCheck() �ؿ����ɲ�
* 2004.06.14
*			fncDtReplaceInput() �ؿ��ˤƻ�������������ȴ��ۡ������ǳ�����Ʒ׻�����褦�˽���
*
*/


//------------------------------------------------------------------------------
// �����Х��ѿ����
//	��ա������Υ����Х��ѿ��ϰʲ��Υ�����ץ���ǻ��Ѥ���Ƥ��ޤ�
//	dlist/record.js
//	dlist/po/index.html
//	dlist/so/index.html
//------------------------------------------------------------------------------
var saveRecord				= new Array();	// ���ٹԤ�ñ�̤Ȥ�������
var g_lngSelIndex			= -1;			// ����Ԥ��Ǽ�����ѿ�
var g_lngReturnFlg			= 1 ;			// Detail�Υ��֤򲡤����Ȥ��ˡ���Ͽ����꤬���ä���ɽ��
var g_lngSentakufunouFlg	= 0;			// ���ٹ�����ν����������ޤǤۤ��������Ǥ��ʤ�����
var g_curTax				= 0;			// ��Ψ�ʷ׾�������Ψ��Detail�Υ��֤򲡤����Ȥ��˽������
var g_lngTaxCode			= 1;			// �ǥ����ɡ�0.05���ݻ����Ƥ���DB��Υ����ɡˡʷ׾������ǥ����ɡ�Detail�Υ��֤򲡤����Ȥ��˽������
var g_lngTaxClassCode		= 0;			// �Ƕ�ʬ������
var g_lngDecimalCutPoint	= 2;			// �������ʲ����׻������ݥ���ȡʽ���͡��������ʲ�2��ǽ�����
var g_lngCalcCode			= 0;			// �׻���ˡ���̡�0:�ͼθ�����

var g_strJpnCurrencySign    = "\\";			// ���ܱ��̲ߵ���
var g_strFreeTaxClass       = "1";			// �Ƕ�ʬ�������
var g_strOutTaxClass        = "2";			// �Ƕ�ʬ������
var g_strInTaxClass         = "3";			// �Ƕ�ʬ������
var g_strNoneMonetaryRate   = "0";			// �졼�ȥ����ס�-
var g_strTtmMonetaryRate    = "1";			// �졼�ȥ����ס�TTM
var g_strDefMonetaryRate    = "2";			// �졼�ȥ����ס�����

var g_strSOKindOfManagement = "SO";			// �������
var g_strSCKindOfManagement = "SC";			// ������
var g_strPOKindOfManagement = "PO";			// ȯ�����
var g_strPCKindOfManagement = "PC";			// ��������

var g_bytDefaultCheckedFlag = 1;			// ���ɲû��оݥ����å����֥ե饰  0: �ʤ�  1: ����
var g_bytDebugFlag          = 0;			// �ǥХå��ѥե饰  0: �̾�  1: �ǥХå�



// ---------------------------------------------------------------
/**
* ����   : �������ȡפˡ������ȡפ����Ƥ��ɲ�
* �о�   : �������ȡפ������Τ��٤�
* ����   : �������ȡפ��ͤ������aryRecord�פ˳�Ǽ��������������saveRecord�פ˳�Ǽ��
*           ���ΤȤ������ٹԤ����򤵤�Ƥ���С����򤵤줿����ξ���ɲä�������Ƥ��ʤ���С�
*           �Ǹ������ɲá�
*           ���θ塢�ؿ���fncDtDisplay()�פ�ƤӽФ��������Ȥ��ɽ����
*/
// ---------------------------------------------------------------
function fncDtAddRecord()
{
	// ���ʥ������������å�
	var blnCheck = fncCheckDetailCode( saveRecord );

	if( !blnCheck )
	{
		//��������ξ��
		if( typeof( window.parent.HSO.SOFlg ) == "object" )
		{
			alert( "���ʤޤ��ϡ�����ʬ���㤤�ޤ���" );
		}
		// ����¾�δ���
		else
		{
			alert( "���ʤ��㤤�ޤ���" );
		}

		return false;
	}



	//���ϥǡ����Υ����å�
	var addFlg = fncDtAddCheck();

	if( addFlg == false ) return false;

	//�����Ȥ��ͤ򿷵�������˳�Ǽ
	var aryRecord = fncDtNewAry();

	//���ٹԤ����򤵤�Ƥ��ʤ����
	if ( g_lngSelIndex == -1)
	{
		//�����Х�����κǸ���ɲ�
		saveRecord.push(aryRecord);

		//����Ԥ��ɲä����Ȥ��˶���Ԥ����򤹤뤿��ˤ�ȤΥ���ǥå������ݻ�����
		var preindex = -1;
	}
	//���ٹԤ����򤵤�Ƥ�����
	else
	{
		//���򤵤줿����ξ�ˡ�������������ɲä���
		saveRecordLength = parseInt(saveRecord.length); 
		saveRecordLeft  = saveRecord.slice(0,g_lngSelIndex);
		saveRecordRigft = saveRecord.slice(g_lngSelIndex, saveRecordLength);
		saveRecord      = saveRecordLeft;
		saveRecord.push(aryRecord);
		saveRecord      = saveRecord.concat(saveRecordRigft);

		//����Ԥ��ɲä����Ȥ��˶���Ԥ����򤹤뤿��ˤ�ȤΥ���ǥå������ݻ�����
		var preindex = g_lngSelIndex;

		//����ǥå���������
		g_lngSelIndex      = -1;
	}

	//�����Ȥ��ɽ��
	fncDtDisplay( preindex );

	//�إå���[�̲�]���ѹ��Ǥ��ʤ��褦�ˤ���
	fncHdMonetaryUnitCheck();

	//�����ɲäξ��ˤϡ��ɲä�������Ԥ�����
	if( aryRecord[0] == "" )
	{
		//�����Х������Ĺ��
		saveRecordLength = saveRecord.length;

		//����ǥå������ɲä�������Ԥ������ֹ�򥻥å�
		//���򤵤줿�Ԥ������硢���ξ������
		if( preindex != -1 )
		{
			g_lngSelIndex = preindex;
		}
		//���򤵤줿�Ԥ��ʤ���硢�Ǹ������
		else
		{
			g_lngSelIndex = parseInt(saveRecordLength) - 1 ;
		}

		//�ɲä�������Ԥ�ȿž������
		document.getElementById("retsu" + g_lngSelIndex).style.backgroundColor="#bbbbbb";
	}

	//�����ȤκǸ�˰�ư����
	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		window.location.href = "/dlist/po/index.html#enddisplay";
	}
	//ȯ����������������ξ��
	else if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		window.location.href = "/dlist/so/index.html#enddisplay";
	}
}


// ---------------------------------------------------------------
/**
* ����   : ���򤷤��Ԥ���
* �о�   : �������ȡפ������Τ��٤�
* ����   : ����Ԥ����������������
*          ���θ塢�ؿ���fncDtDisplay()�פ�ƤӽФ��������Ȥ��ɽ����
* ���   : �Ԥ����򤵤�Ƥ��ʤ����ˤϡ����顼��å���������ϡ�
*/
// ---------------------------------------------------------------
function fncDtDelRecord()
{
	//���ٹԤ����򤵤�Ƥ��ʤ���硢���顼�Ȥ�Ф��ƽ�����λ
	if( g_lngSelIndex == -1 )
	{
		alert("���ٹԤ����򤷤Ƥ�������");
		return false;
	}

	if( res = confirm("����Ԥ������Ƥ������Ǥ�����") )
	{
		saveRecordLength = parseInt(saveRecord.length);
	
		saveRecordLeft  = saveRecord.slice(0, g_lngSelIndex);
		saveRecordRigft = saveRecord.slice(g_lngSelIndex + 1, saveRecordLength);
		saveRecord      = new Array();
		saveRecord      = saveRecord.concat(saveRecordLeft, saveRecordRigft);
	
		g_lngSelIndex = -1;
	
		//�����Ȥ��ɽ��
		fncDtDisplay();
	
		//���ٹԤ��ʤ���硢
		// �إå���[�̲�]���ѹ���ǽ�ˤ���
		fncHdMonetaryUnitCheck();
		// [�졼�ȥ�����]���ѹ���ǽ�ˤ���
		fncHdMonetaryRateCheck();
	}
}


// ---------------------------------------------------------------
/**
* ����   : �����Ȥ��ͤ�����Ԥ��֤�������
* �о�   : �������ȡפ������Τ��٤�
* ����   : �����Ȥ��ͤ�����Ԥ��֤������롣
*          ���θ塢�ؿ���fncDtDisplay()�פ�ƤӽФ��������Ȥ��ɽ����
* ���    :�Ԥ����򤵤�Ƥ��ʤ����ˤϡ��إå���ʬ�˥��顼��å����������
*/
// ---------------------------------------------------------------
function fncDtCommitRecord()
{
	// ���ʥ������������å�
	var blnCheck = fncCheckDetailCode( saveRecord );

	if( !blnCheck )
	{
		//��������ξ��
		if( typeof( window.parent.HSO.SOFlg ) == "object" )
		{
			alert( "���ʤޤ��ϡ�����ʬ���㤤�ޤ���" );
		}
		// ����¾�δ���
		else
		{
			alert( "���ʤ��㤤�ޤ���" );
		}

		return false;
	}



	//���ٹԤ����򤵤�Ƥ�����
	if( g_lngSelIndex != -1)
	{
		//���ϥ����å�
		if( fncDtAddCheck() == false) return false;

		fncDtReplaceAry();

		//����ǥå���������
		g_lngSelIndex = -1;

		//�����Ȥ��ɽ��
		fncDtDisplay();
	}
	//���ٹԤ����򤵤�Ƥ��ʤ����
	else
	{
		alert("���ٹԤ����򤵤�Ƥ��ޤ���");
	}
}


// ---------------------------------------------------------------
/**
* ����   : ���ꥢ�ܥ��󤬲����줿�Ȥ��˽���
* �о�   : �������ȡפ������Τ��٤�
* ����   : 
*/
// ---------------------------------------------------------------
function fncDtClearRecord()
{
	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		//�������ʤ򥯥ꥢ
		window.parent.DSO.strStockItemCode.length = 0;
	
		//�������ʤ�����Ǥ��ʤ��褦�ˤ���
		window.parent.DSO.strStockItemCode.disabled = true;
	}

	//ñ���ꥹ�Ȥ򥯥ꥢ
	window.parent.DSO.lngGoodsPriceCode.length = 0;

	//���׶�ۤޤǡ����ꥢ����Ƥ��ޤ��Τǡ����ٵ��ʤ���
	fncDtCalAllTotalPrice();

	//���ٹԤ�����Ǥ���褦�ˤ���
	g_lngSentakufunouFlg = 0;

	fncDtGsChecked();
}








// ---------------------------------------------------------------
/**
* ����   : �����Ȥ��ɽ��
* �о�   : �������ȡפ������Τ��٤�
* param  : preindex : ɽ��������֤���ꤹ�뤿��˻���
* ����   : �����saveRecord�פ��顢�����ȤΥơ��֥���������ɽ��
*/
// ---------------------------------------------------------------
function fncDtDisplay( preindex )
{
	// ���׶�ۤν����
	g_lngReSumTotalPrice = 0;


	//���ֹ�
	lngTrCount = 1;
	
	//�����̾�����
	strTableHtml = fncStrTableHtmlColumns();

	//���������
	for( i = 0; i < saveRecord.length; i++ )
	{
		strTableHtml = strTableHtml + 
					'<tr class="Lists01" id ="retsu' + i + '" onClick="fncDtSentaku(' + i + ');return false;"' + 'bgcolor="#ffffff"��>' + 
					'<td align="center">' + lngTrCount + '</td>' + 
					fncStrTableHtmlRows(i) + 
					'</tr>';
		lngTrCount++;
	}

	strTableHtml = strTableHtml + '</table>';

	//���ٹԤ����򤵤�Ƥ��ʤ����ˤΤߡ�ɽ������ֺǸ�ιԤˤ�������ν����򤹤�
	if( preindex == -1 )
	{
		strTableHtml = strTableHtml + '<a name="enddisplay"></a>';
	}

	//��¸�ΰ����������ľ���������˽񤭴�����
	document.all.DetailList.innerHTML = strTableHtml;

	//���׶�ۤη׻�
	fncDtCalAllTotalPrice();


	//���ٹԤ�����Ǥ���褦�ˤ���
	g_lngSentakufunouFlg = 0;

}


// ---------------------------------------------------------------
/**
* ����   : ���ٹԤ�������ν���
* �о�   : �������ȡפ������Τ��٤�
* ����   : ���ٹԤ����򤹤�Ȥ��ˡ����Ǥ����򤵤�Ƥ���Ԥ����ä����ϡ����ιԤ�ȿž�������롣
*          ��¸�����򤵤�Ƥ���Ԥ�⤦���ٲ��������ˤϡ�g_lngSelIndex���������롣
*          ����ʳ��ξ��ˤϡ�����Ԥ��ͤ������Ȥ�ȿ�Ǥ����롣
* ���   : ���ٹԤ����򤵤�Ƥ�����ǡ�����Ԥ��ѹ����褦�Ȥ������ˤϡ������Ȥ��ѹ����ʤ��������å�����
*          �ѹ�������С���å���������ϡ�
*/
// ---------------------------------------------------------------
function fncDtSentaku(i)
{

	//¾�����ٹԤν���������äƤʤ���С����򤵤��ʤ�
	if( g_lngSentakufunouFlg == 1 )
	{
		return null;
	}
	else
	{
		//������Υե饰��Ω�Ƥ�
		//(���ߤϻ������ʤν���������ä��Ȥ��˲�����Ƥ���)
		g_lngSentakufunouFlg = 1;
	}

	//���ٹԤ����򤵤�Ƥ�����Υ����å��ե饰(�����Ȥ˥��顼������ȥ��顼�ˤʤ�)
	var checkFlg = true;

	//���ٹԤ����򤵤�Ƥ�����
	if( g_lngSelIndex != -1 )
	{
		//�����Ȥ��ѹ����ʤ��������å�
		checkFlg = fncDtCheck();
	}

	if (checkFlg == true)
	{

		//��¸������Ԥ����ä����ˤϡ����ιԤ�ȿž����
		if( g_lngSelIndex != -1 )
		{
			document.getElementById("retsu" + g_lngSelIndex).style.backgroundColor="#ffffff";
		}

		//����������Ԥ�⤦���٥���å��������
		if (g_lngSelIndex == i)
		{
			//����ǥå���������
			g_lngSelIndex = -1;
			//���ٹԤ�����Ǥ���褦�ˤ���
			g_lngSentakufunouFlg = 0;
		}
		//�����Ȱ㤦����Ԥ򥯥�å��������
		else
		{
			//����ǥå���������Ԥ������ֹ�򥻥å�
			g_lngSelIndex = i;
	
			//������ԡפ�ȿž������
			document.getElementById("retsu" + g_lngSelIndex).style.backgroundColor="#bbbbbb";

			//�����Ȥ򤹤٤ƥ��ꥢ�ʶ��ԤΤȤ��Τ����
			window.parent.fncResetFrm( window.parent.DSO );
	
			//ȯ����������������ξ�硢�������ʤ򥯥ꥢ�ʶ��ԤΤȤ��Τ����
			if( typeof(window.parent.HSO.POFlg) == "object" || 
				typeof(window.parent.HSO.PCFlg) == "object" )
			{
				window.parent.DSO.strStockItemCode.length  = 0;
			}
	
			//ñ���ꥹ�Ȥ򥯥ꥢ�ʶ��ԤΤȤ��Τ����
			window.parent.DSO.lngGoodsPriceCode.length = 0;
	
			if( saveRecord[g_lngSelIndex][0] != "" )
			{
/*
				//���ʤ��顢����̾�����
				subLoadMasterValue('cnProduct',
						 saveRecord[g_lngSelIndex][0],
						 window.parent.DSO.strProductName,
						 Array(saveRecord[g_lngSelIndex][0]),
						 window.document.objDataSourceSetting,
						 0);
				//���ʤ��顢�ܵ����֤����
				subLoadMasterValue('cnGoodsCode',
						 saveRecord[g_lngSelIndex][0],
						 window.parent.DSO.strGoodsCode,
						 Array(saveRecord[g_lngSelIndex][0]),
						 window.document.objDataSourceSetting1,
						 1);
				//���ʤ��顢�����ȥ����������
				subLoadMasterValue('cnCartonQuantity',
						 saveRecord[g_lngSelIndex][0],
						 window.parent.DSO.lngCartonQuantity,
						 Array(saveRecord[g_lngSelIndex][0]),
						 window.document.objDataSourceSetting13,
						 13);
*/
				
				// ���ʾ���������subloadmastersettings.js �����ѡ�
				subLoadMasterValue('cnProductInfo', this, this, Array(saveRecord[g_lngSelIndex][0]), window.document.objDataSourceSettingProductInfo, 1);

				//ȯ����������������ξ��
				if( typeof(window.parent.HSO.POFlg) == "object" || 
					typeof(window.parent.HSO.PCFlg) == "object" )
				{
					//�������ܤ��顢�������ʤΥ��ץ�����ͤ����
					subLoadMasterOption( 'cnStockItem',
							 window.parent.DSO.strStockSubjectCode, 
							 window.parent.DSO.strStockItemCode,
							 Array(saveRecord[g_lngSelIndex][2]),
							 window.document.objDataSourceSetting10,
							 10);
				}

				//ñ���ꥹ�Ȥ����
				fncDtGoodsPriceList2();

			}
			//����Ԥλ������ٹԤ�����Ǥ���褦�ˤ���
			else
			{
				//���ٹԤ�����Ǥ���褦�ˤ���
				g_lngSentakufunouFlg = 0;
			}
	
			//�������ȡפ�����Ԥ�ȿ��
			fncDtReplaceInput();

			//����̲ߤ�ɽ��
			fncDtCalStdTotalPrice();

			//���׶�ۤη׻�
			fncDtCalAllTotalPrice();
		}
	}
	else
	{
		g_lngSentakufunouFlg = 0;
	}


	//����������������ξ��
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		//���ٹԤ�����Ǥ���褦�ˤ���
		g_lngSentakufunouFlg = 0;
	}
}


// ---------------------------------------------------------------
/**
* ����   : �����Ȥ�����Ԥκ��ۤ�����å������㤤������С���ǧ����������ɽ��
* �о�   : �������ȡפ������Τ��٤�
* @return : [Boolean��] ����Ԥ��ư���Ƥ�褤���ϡ�true����ư���ʤ����ϡ�false
*/
// ---------------------------------------------------------------
function fncDtCheck()
{
	// ���ʥ������������å�
	var blnCheck = fncCheckDetailCode( saveRecord );

	if( !blnCheck )
	{
		//��������ξ��
		if( typeof( window.parent.HSO.SOFlg ) == "object" )
		{
			alert( "���ʤޤ��ϡ�����ʬ���㤤�ޤ���" );
		}
		// ����¾�δ���
		else
		{
			alert( "���ʤ��㤤�ޤ���" );
		}

		return false;
	}



	//�������ȡפ��ͤ�����˥��å�
	var aryRecord = fncDtNewAry();

	//�����Ĺ��
	var aryRecordLength = aryRecord.length;

	for( j = 0; j < aryRecordLength ; j++ )
	{
		//�����Ȥ�����Ԥ����
		if( aryRecord[j] != saveRecord[g_lngSelIndex][j] )
		{
			// ñ���ꥹ��,��������̾,��������̾,ñ�̡�̾�Ρ�,
			// ñ���ɲåꥹ��,���ֹ�ΤȤ������å�
			// No.�ʶⷿ�ֹ�˥����å� (Added by Kazushi Saito
			// �����̡������å�
			if (j==1 || j == 3 || j == 5 || j == 9 ||j == 14 || j==18 || j==22 || j==24 || j==25 || j==26 || j==27) continue;

//�ǥХå��� ��Ǿä�
//alert("�ѹ����줿�����ֹ� : " + j + "\n" +
//	  "�������ȡפ��� : " + aryRecord[j] + "\n" +
//	  "�����ٹԡפ��� : " + saveRecord[g_lngSelIndex][j]);

			if( res = confirm("�ѹ��ս꤬����ޤ����ѹ����Ƥ������Ǥ�����") )
			{
				//���ϥ����å�
				if( fncDtAddCheck() )
				{
					//�����Ȥ��ͤ�����Ԥ��֤�����
					fncDtReplaceAry();
					//�����Ȥ��ɽ��
					fncDtDisplay();

					return true;
				}
				else
				{
					return false;
				}

			}
			break;
		}
	}
	return true;
}


// ---------------------------------------------------------------
/**
* ����   : �ɲåܥ���򲡤����Ȥ����ͤΥ����å�
* �о�   : �������ȡפ������Τ��٤�
* ���   : ���꤬����Х��顼�Ȥ�Ф�
*/
// ---------------------------------------------------------------
function fncDtAddCheck()
{

	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		//�ͤ����٤Ƥ�����ä��顢���Ԥ��ɲäǤ���
		if( window.parent.DSO.strProductCode.value            == "" && //���ʥ�����
			window.parent.DSO.strStockSubjectCode.value       == 0  && //��������
			window.parent.DSO.strStockItemCode.selectedIndex  == -1 )  //��������
		{
			if( window.parent.DSO.lngConversionClassCode[0].checked )
			{
				if( window.parent.DSO.curProductPrice_gs.value  == "" && //����ñ�����ʤ�
					window.parent.DSO.lngGoodsQuantity_gs.value == "" )  //���ʿ��̤��ʤ�
				{
					return true;
				}
			}
			else if( window.parent.DSO.lngConversionClassCode[1].checked )
			{
				if( window.parent.DSO.curProductPrice_ps.value  == "" && //�ٻ�ñ�����ʤ�
					window.parent.DSO.lngGoodsQuantity_ps.value == "" )  //�ٻѿ��̤��ʤ�
				{
					return true;
				}
			}
		}
	}

	//����������������ξ��
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		//�ͤ����٤Ƥ�����ä��顢���Ԥ��ɲäǤ���
		if( window.parent.DSO.strProductCode.value            == "" && //���ʥ�����
			window.parent.DSO.lngSalesClassCode.value         == 0  )  //����ʬ
		{
			if( window.parent.DSO.lngConversionClassCode[0].checked )
			{
				if( window.parent.DSO.curProductPrice_gs.value  == "" && //����ñ�����ʤ�
					window.parent.DSO.lngGoodsQuantity_gs.value == "" )  //���ʿ��̤��ʤ�
				{
					return true;
				}
			}
			else if( window.parent.DSO.lngConversionClassCode[1].checked )
			{
				if( window.parent.DSO.curProductPrice_ps.value  == "" && //�ٻ�ñ�����ʤ�
					window.parent.DSO.lngGoodsQuantity_ps.value == "" )  //�ٻѿ��̤��ʤ�
				{
					return true;
				}
			}
		}
	}

	//���顼�����ä����˥�å��ݥ���ͤ�����ѿ�
	var alertList = "";

	//�ͤ����٤ƶ��ǤϤʤ����Υ����å�
	//���ʥ����ɤ����Ϥ��ʤ��ä����
	if( window.parent.DSO.strProductCode.value == "" )
	{
		alertList += "���ʥ����ɤ����Ϥ��Ƥ�������!\n";
	}
	//���ʥ����ɤ����Ϥ��������ä����
	if( isNaN(window.parent.DSO.strProductCode.value) )
	{
		alertList += "���ʥ����ɤ��ͤ������Ǥ�!\n";
	}

	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		//�������ܤ����򤵤�ʤ��ä����
		if( window.parent.DSO.strStockSubjectCode.value       == 0 )
		{
			alertList += "�������ܤ����򤷤Ƥ�������!\n";
		}
		//�������ʤ����򤵤�ʤ��ä����
		if( window.parent.DSO.strStockItemCode.selectedIndex == -1 ||
			window.parent.DSO.strStockItemCode.selectedIndex == 0  )
		{
			alertList += "�������ʤ����򤷤Ƥ�������!\n";
		}
//2004.03.02 suzukaze update start
		if( typeof(window.parent.HSO.POFlg) == "object" )
		{
			//Ǽ�������򤵤�ʤ��ä����
			if( window.parent.DSO.dtmDeliveryDate.value == "" )
			{
				alertList += "Ǽ�������򤷤Ƥ�������!\n";
			}
		}
//2004.03.02 suzukaze update end
	}

	//����������������ξ��
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		//����ʬ�����򤵤�ʤ��ä����
		if( window.parent.DSO.lngSalesClassCode.value == 0 )
		{
			alertList += "����ʬ�����򤷤Ƥ�������!\n";
		}
//2004.03.03 suzukaze update start
		if( typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//Ǽ�������Ϥ���ʤ��ä����
			if( window.parent.DSO.dtmDeliveryDate.value == "" )
			{
				alertList += "Ǽ�������Ϥ��Ƥ�������!\n";
			}
		}
//2004.03.03 suzukaze update end
	}

	//����ñ�̷׾夬���򤵤�Ƥ�����
	if (window.parent.DSO.lngConversionClassCode[0].checked)
	{
//2004.03.01 suzukaze update start
		//����ñ�������Ϥ���Ƥ��ʤ��ä����
//		if( window.parent.DSO.curProductPrice_gs.value == "" ||
//			fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value)) == 0 )
		if( window.parent.DSO.curProductPrice_gs.value == "" )
//2004.03.01 suzukaze update end
		{
			alertList += "����ñ�������Ϥ��Ƥ�������!\n";
		}
		//���ʿ��̤����Ϥ���Ƥ��ʤ��ä����
		if( window.parent.DSO.lngGoodsQuantity_gs.value == "" ||
			fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value) == 0 )
		{
			alertList += "���ʿ��̤����Ϥ��Ƥ�������!\n";
		}
//2004.03.01 suzukaze update start
		//ȯ���������������ξ��
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//����ñ�����ͤ��������ä����
			if( isNaN(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value))) || 
				fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value)) < 0    )
			{
				alertList += "����ñ�����ͤ������Ǥ�!\n";
			}
		}
		//�����������������ξ��
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//����ñ�����ͤ��������ä����
			if( isNaN(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value))) )
			{
				alertList += "����ñ�����ͤ������Ǥ�!\n";
			}
		}
//2004.03.01 suzukaze update end
//2004.03.17 suzukaze update start
		//ȯ���������������ξ��
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//���ʿ��̤��ͤ��������ä����
			if( isNaN(fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value)) || 
				fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value) < 0    )
			{
				alertList += "���ʿ��̤��ͤ������Ǥ�!\n";
			}
		}
		//�����������������ξ��
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//���ʿ��̤��ͤ��������ä����
			if( isNaN(fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value)) )
			{
				alertList += "���ʿ��̤��ͤ������Ǥ�!\n";
			}
		}
//2004.03.17 suzukaze update end
	}
	//�ٻ�ñ�̷׾夬���򤵤�Ƥ�����
	else if( window.parent.DSO.lngConversionClassCode[1].checked )
	{
//2004.03.01 suzukaze update start
		//�ٻ�ñ�������Ϥ���Ƥ��ʤ��ä����
//		if( window.parent.DSO.curProductPrice_ps.value == "" ||
//			fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value)) == 0  )
		if( window.parent.DSO.curProductPrice_ps.value == "" )
//2004.03.01 suzukaze update end
		{
			alertList += "�ٻ�ñ�������Ϥ��Ƥ�������!\n";
		}
		//�ٻѿ��̤����Ϥ���Ƥ��ʤ��ä����
		if( window.parent.DSO.lngGoodsQuantity_ps.value == "" ||
			fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value) == 0 )
		{
			alertList += "�ٻѿ��̤����Ϥ��Ƥ�������!\n";
		}
//2004.03.01 suzukaze update start
		//ȯ���������������ξ��
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//�ٻ�ñ�����ͤ��������ä����
			if( isNaN(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value))) || 
				fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value)) < 0 )
			{
				alertList += "�ٻ�ñ�����ͤ������Ǥ�!\n";
			}
		}
		//�����������������ξ��
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//�ٻ�ñ�����ͤ��������ä����
			if( isNaN(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value))) )
			{
				alertList += "�ٻ�ñ�����ͤ������Ǥ�!\n";
			}
		}
//2004.03.01 suzukaze update end
//2004.03.17 suzukaze update start
		//ȯ���������������ξ��
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//�ٻѿ��̤��ͤ��������ä����
		if( isNaN(fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value)) || 
			fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value) < 0    )
			{
				alertList += "�ٻѿ��̤��ͤ������Ǥ�!\n";
			}
		}
		//�����������������ξ��
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//�ٻ�ñ�����ͤ��������ä����
			if( isNaN(fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value)) )
			{
				alertList += "�ٻѿ��̤��ͤ������Ǥ�!\n";
			}
		}
//2004.03.17 suzukaze update end
	}

	//���顼�����ä����å����������
	if( alertList != "" )
	{
		alert(alertList);
		return false;
	}

	return true;
}


// ---------------------------------------------------------------
/**
* ����    : �����Ȥ��ͤ򿷵�������˳�Ǽ
* �о�    : �������ȡפ������Τ��٤�
* @retrun : aryRecord, [����], ����������
*/
// ---------------------------------------------------------------
function fncDtNewAry()
{
	var aryRecord = new Array();

	aryRecord[0]  = window.parent.DSO.strProductCode.value;            //���ʥ�����
	aryRecord[1]  = window.parent.DSO.lngGoodsPriceCode.value;         //ñ���ꥹ��

	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[2]  = window.parent.DSO.strStockSubjectCode.value;       //��������
		aryRecord[3]  = window.parent.DSO.strStockSubjectCode.options[window.parent.DSO.strStockSubjectCode.selectedIndex].text;     //�������ܡ�value + ̾�Ρ�
		aryRecord[4]  = window.parent.DSO.strStockItemCode.value;          //��������
		if( window.parent.DSO.strStockItemCode.selectedIndex != -1 )
		{
			aryRecord[5]  = window.parent.DSO.strStockItemCode.options[window.parent.DSO.strStockItemCode.selectedIndex].text;           //�������ʡ�value + ̾�Ρ�
		}else{
			aryRecord[5]  = "";
		}
	}

	if( window.parent.DSO.lngConversionClassCode[0].checked )
	{
		aryRecord[6]  = window.parent.DSO.lngConversionClassCode[0].value; // ������ʬ(����ñ�̷׾�)
		aryRecord[7]  = window.parent.DSO.curProductPrice_gs.value;        // ����ñ��
		aryRecord[8]  = window.parent.DSO.lngProductUnitCode_gs.value;     // ����ñ��
		aryRecord[9]  = window.parent.DSO.lngProductUnitCode_gs.options[window.parent.DSO.lngProductUnitCode_gs.selectedIndex].text; //����ñ�̡�̾�Ρ�
		aryRecord[10] = window.parent.DSO.lngGoodsQuantity_gs.value;       // ���ʿ���
		aryRecord[14] = window.parent.DSO.curProductPrice_gs.value;        // ñ���ꥹ���ɲåǡ���
	}
	else if( window.parent.DSO.lngConversionClassCode[1].checked )
	{
		aryRecord[6]  = window.parent.DSO.lngConversionClassCode[1].value; // ������ʬ(�ٻ�ñ�̷׾�)
		aryRecord[7]  = window.parent.DSO.curProductPrice_ps.value;        // �ٻ�ñ��
		aryRecord[8]  = window.parent.DSO.lngProductUnitCode_ps.value;     // �ٻ�ñ��
		aryRecord[9]  = window.parent.DSO.lngProductUnitCode_ps.options[window.parent.DSO.lngProductUnitCode_ps.selectedIndex].text; //�ٻ�ñ�̡�̾�Ρ�
		aryRecord[10] = window.parent.DSO.lngGoodsQuantity_ps.value;       // �ٻѿ���
		aryRecord[14] = fncProductPriceForList();                          // ñ���ꥹ���ɲåǡ���
	}
	aryRecord[11] = window.parent.DSO.curTotalPrice.value;             // ��ȴ���

	// ����������������
	if( typeof(window.parent.HSO.SCFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[24] = aryRecord[10];
	}
	
	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[12] = window.parent.DSO.lngCarrierCode.value;            // ������ˡ
	}

	aryRecord[13] = fncCheckReplaceString(window.parent.DSO.strDetailNote.value);             // ����

	//�����������������ξ��
	if( typeof(window.parent.HSO.PCFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		aryRecord[15] = window.parent.DSO.lngTaxClassCode.value;             // �����Ƕ�ʬ������
		aryRecord[16] = window.parent.DSO.lngTaxCode.value;                  // �����ǡ�Ψ��
		aryRecord[17] = window.parent.DSO.curTaxPrice.value;                 // �����ǳ�
		aryRecord[18] = "";            // ���ֹ�
	}

	//����������������ξ��
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		aryRecord[19] = window.parent.DSO.lngSalesClassCode.value;          // ����ʬ
		if( window.parent.DSO.lngSalesClassCode.selectedIndex != -1 )
		{
			aryRecord[20] = window.parent.DSO.lngSalesClassCode.options[window.parent.DSO.lngSalesClassCode.selectedIndex].text;           // ����ʬ��value + ̾�Ρ�
		}else{
			aryRecord[20] = "";
		}
		aryRecord[21] = window.parent.DSO.dtmDeliveryDate.value;          // Ǽ��
	}

	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[22] = ""; //window.parent.DSO.strSerialNo.value;         // No	// No.�Ͽ������ɲû��˷Ѿ����ʤ��褦�ˤ��� (Modifyed by Kazushi Saito
		aryRecord[23] = window.parent.DSO.dtmDeliveryDate.value;          // Ǽ��
	}




	// ������
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		aryRecord[24] = aryRecord[10];            // ������
		aryRecord[25] = "";                       // �����ֹ�
		aryRecord[26] = "";                       // ���ٹ��ֹ�
		aryRecord[27] = g_bytDefaultCheckedFlag;  // �о�
	}
	// ��������
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[25] = g_bytDefaultCheckedFlag; // �о�
	}


	return aryRecord;
}


// ---------------------------------------------------------------
/**
* ����   : �����Ȥ��ͤ�����Ԥ��֤�����
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtReplaceAry()
{
	// �о��ͺ�����
	fncSetCheckValue();



	saveRecord[g_lngSelIndex][0]  = window.parent.DSO.strProductCode.value;         // ���ʥ�����
	saveRecord[g_lngSelIndex][1]  = window.parent.DSO.lngGoodsPriceCode.value;      // ñ���ꥹ��

	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		saveRecord[g_lngSelIndex][2]  = window.parent.DSO.strStockSubjectCode.value;    // ��������
		saveRecord[g_lngSelIndex][3]  = window.parent.DSO.strStockSubjectCode.options[window.parent.DSO.strStockSubjectCode.selectedIndex].text;     //�������ܡ�value + ̾�Ρ�
		saveRecord[g_lngSelIndex][4]  = window.parent.DSO.strStockItemCode.value;       // ��������
	
		if( window.parent.DSO.strStockItemCode.selectedIndex != -1 )
		{
		saveRecord[g_lngSelIndex][5]  = window.parent.DSO.strStockItemCode.options[window.parent.DSO.strStockItemCode.selectedIndex].text;           //�������ʡ�value + ̾�Ρ�
		}else{
		saveRecord[g_lngSelIndex][5]  = "";
		}
	}





	if( window.parent.DSO.lngConversionClassCode[0].checked )
	{
		saveRecord[g_lngSelIndex][6]  = window.parent.DSO.lngConversionClassCode[0].value; // ������ʬ(����ñ�̷׾�)
		saveRecord[g_lngSelIndex][7]  = window.parent.DSO.curProductPrice_gs.value;        // ����ñ��
		saveRecord[g_lngSelIndex][8]  = window.parent.DSO.lngProductUnitCode_gs.value;     // ����ñ��
		saveRecord[g_lngSelIndex][9]  = window.parent.DSO.lngProductUnitCode_gs.options[window.parent.DSO.lngProductUnitCode_gs.selectedIndex].text;     //����ñ�̡�̾�Ρ�
		saveRecord[g_lngSelIndex][10] = window.parent.DSO.lngGoodsQuantity_gs.value;       // ���ʿ���
		saveRecord[g_lngSelIndex][14] = window.parent.DSO.curProductPrice_gs.value;        // ñ���ꥹ���ɲåǡ���
	}
	else if(window.parent.DSO.lngConversionClassCode[1].checked )
	{
		saveRecord[g_lngSelIndex][6]  =  window.parent.DSO.lngConversionClassCode[1].value; // ������ʬ(�ٻ�ñ�̷׾�)
		saveRecord[g_lngSelIndex][7]  = window.parent.DSO.curProductPrice_ps.value;         // �ٻ�ñ��
		saveRecord[g_lngSelIndex][8]  = window.parent.DSO.lngProductUnitCode_ps.value;      // �ٻ�ñ��
		saveRecord[g_lngSelIndex][9]  = window.parent.DSO.lngProductUnitCode_ps.options[window.parent.DSO.lngProductUnitCode_ps.selectedIndex].text;     //�ٻ�ñ�̡�̾�Ρ�
		saveRecord[g_lngSelIndex][10] = window.parent.DSO.lngGoodsQuantity_ps.value;        // �ٻѿ���
		saveRecord[g_lngSelIndex][14] = fncProductPriceForList();                           // ñ���ꥹ���ɲåǡ���
	} 


	saveRecord[g_lngSelIndex][11] = window.parent.DSO.curTotalPrice.value;          // ��ȴ���

	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		saveRecord[g_lngSelIndex][12] = window.parent.DSO.lngCarrierCode.value;         // ������ˡ
	}

	saveRecord[g_lngSelIndex][13] = fncCheckReplaceString(window.parent.DSO.strDetailNote.value);          // ����




	//�����������������ξ��
	if( typeof(window.parent.HSO.PCFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		saveRecord[g_lngSelIndex][15] = window.parent.DSO.lngTaxClassCode.value;         // �����Ƕ�ʬ������
		saveRecord[g_lngSelIndex][16] = window.parent.DSO.lngTaxCode.value;              // �����ǡ�Ψ��
		saveRecord[g_lngSelIndex][17] = window.parent.DSO.curTaxPrice.value;             // �����ǳ�
	}

	//����������������ξ��
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		saveRecord[g_lngSelIndex][19] = window.parent.DSO.lngSalesClassCode.value;          // ����ʬ
		if( window.parent.DSO.lngSalesClassCode.selectedIndex != -1 )
		{
			saveRecord[g_lngSelIndex][20] = window.parent.DSO.lngSalesClassCode.options[window.parent.DSO.lngSalesClassCode.selectedIndex].text;           //����ʬ��value + ̾�Ρ�
		}else{
			saveRecord[g_lngSelIndex][20] = "";
		}
		saveRecord[g_lngSelIndex][21] = window.parent.DSO.dtmDeliveryDate.value;          // Ǽ��
	}

	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		saveRecord[g_lngSelIndex][22] = window.parent.DSO.strSerialNo.value;              // No
		saveRecord[g_lngSelIndex][23] = window.parent.DSO.dtmDeliveryDate.value;          // Ǽ��
	}
}


// ---------------------------------------------------------------
/**
* ����   : �����Ȥ�����Ԥ�ȿ��
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtReplaceInput()
{
	window.parent.DSO.strProductCode.value         = saveRecord[g_lngSelIndex][0];  //���ʥ�����
	//ñ���ꥹ��(saveRecord[g_lngSelIndex][1])�ϡ�hmtl��ľ�ܽ񤯡��ٱ�Τ����

	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		window.parent.DSO.strStockSubjectCode.value    = saveRecord[g_lngSelIndex][2];  //��������
		//��������(saveRecord[g_lngSelIndex][4])�ϡ�hmtl��ľ�ܽ񤯡��ٱ�Τ����
	}
//alert(saveRecord[g_lngSelIndex][7]);


	// ����
	if( saveRecord[g_lngSelIndex][6] == "gs" )
	{
		window.parent.DSO.lngConversionClassCode[0].checked = true;             //������ʬ(����ñ�̷׾�)
		window.parent.DSO.curProductPrice_gs.value     = saveRecord[g_lngSelIndex][7];  //����ñ��
		window.parent.DSO.lngProductUnitCode_gs.value  = saveRecord[g_lngSelIndex][8];  //����ñ��
		window.parent.DSO.lngGoodsQuantity_gs.value    = saveRecord[g_lngSelIndex][10]; //���ʿ���

		//[����ñ��][����ñ��][���ʿ���]�����ϡ�����Ǥ���褦�ˤ���
		window.parent.DSO.curProductPrice_gs.disabled    = false;
		window.parent.DSO.lngProductUnitCode_gs.disabled = false;
		window.parent.DSO.lngGoodsQuantity_gs.disabled   = false;

		//[�ٻ�ñ��][�ٻ�ñ��][�ٻѿ���]�����ϡ�����Ǥ��ʤ��褦�ˤ���
		window.parent.DSO.curProductPrice_ps.disabled    = true;
		window.parent.DSO.lngProductUnitCode_ps.disabled = true;
		window.parent.DSO.lngGoodsQuantity_ps.disabled   = true;

		// ���ʿ���
		fncDtGSGoodsQuantityForPC();
	}
	// �ٻ�
	else if( saveRecord[g_lngSelIndex][6] == "ps" )
	{
		window.parent.DSO.lngConversionClassCode[1].checked = true;             //������ʬ(�ٻ�ñ�̷׾�)
		window.parent.DSO.curProductPrice_ps.value     = saveRecord[g_lngSelIndex][7];  //�ٻ�ñ��
		window.parent.DSO.lngProductUnitCode_ps.value  = saveRecord[g_lngSelIndex][8];  //�ٻ�ñ��
		window.parent.DSO.lngGoodsQuantity_ps.value    = saveRecord[g_lngSelIndex][10]; //�ٻѿ���

		//[����ñ��][����ñ��][���ʿ���]�����ϡ�����Ǥ��ʤ��褦�ˤ���
		window.parent.DSO.curProductPrice_gs.disabled    = true;
		window.parent.DSO.lngProductUnitCode_gs.disabled = true;
		window.parent.DSO.lngGoodsQuantity_gs.disabled   = true;
		//[�ٻ�ñ��][�ٻ�ñ��][�ٻѿ���]�����ϡ�����Ǥ���褦�ˤ���
		window.parent.DSO.curProductPrice_ps.disabled    = false;
		window.parent.DSO.lngProductUnitCode_ps.disabled = false;
		window.parent.DSO.lngGoodsQuantity_ps.disabled   = false;

		// �ٻѿ���
		fncDtPSGoodsQuantityForPC();
	}


	// *v2* ��׶��
	fncDtCalTotalPrice();

	window.parent.DSO.curTotalPrice.value = saveRecord[g_lngSelIndex][11]; //��ȴ���




	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		window.parent.DSO.lngCarrierCode.value         = saveRecord[g_lngSelIndex][12]; //������ˡ
	}
	window.parent.DSO.strDetailNote.value          = fncCheckReplaceStringBack(saveRecord[g_lngSelIndex][13]); //����

	//�����������������ξ��
	if( typeof(window.parent.HSO.PCFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		window.parent.DSO.lngTaxClassCode.value = saveRecord[g_lngSelIndex][15];         //�����Ƕ�ʬ������
		window.parent.DSO.lngTaxCode.value      = saveRecord[g_lngSelIndex][16];         //�����ǡ�Ψ��
		//window.parent.DSO.curTaxPrice.value     = saveRecord[g_lngSelIndex][17];         //�����ǳ�
		saveRecord[g_lngSelIndex][17] = window.parent.DSO.curTaxPrice.value; //�����ǳ�

// 2004.06.14 suzukaze update start
		if( window.parent.DSO.lngTaxClassCode.value == g_strInTaxClass )
		{
			// �����Ȥ��������Ȥ�ȿ�ǻ��� ���ʡ����̡���ȴ��ܾۡ����ǳ� �ȤʤäƤ��ʤ����ٹԤ��Ф��ơ������ǳۤʤɤ�Ʒ׻�����褦�˽���

			var ProductPrice  = 0;	// ñ��
			var GoodsQuantity = 0;	// ����
			var TotalPrice    = 0;	// ��ȴ���
			var ComTotalPrice = 0;	// �����

			if( saveRecord[g_lngSelIndex][6] == "gs" )
			{
				//�����Ȥ�[����ñ��]�����ͤ����ơ��̲ߵ��桢����ޤ���
				ProductPrice  = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value));
				//�����Ȥ�[���ʿ���]�����ͤ����ơ�����ޤ���
				GoodsQuantity = fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value);
			}
			else if( saveRecord[g_lngSelIndex][6] == "ps" )
			{
				//�����Ȥβٻ�ñ�������ͤ����ơ��̲ߵ��桢����ޤ���
				ProductPrice  = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value));
				//�����Ȥβٻѿ��̤����ͤ����ơ�����ޤ���
				GoodsQuantity = fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value);
			}

			TotalPrice    = window.parent.fncVBSNumCalc(ProductPrice, "*", GoodsQuantity);
			ComTotalPrice = window.parent.DSO.curTotalPrice.value + window.parent.DSO.curTaxPrice.Value;

			if( TotalPrice != ComTotalPrice )
			{
				fncDtCalTotalPrice();
			}
		}
// 2004.06.14 suzukaze update end
	}

	//����������������ξ��
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		window.parent.DSO.lngSalesClassCode.value = saveRecord[g_lngSelIndex][19];          //����ʬ
		window.parent.DSO.dtmDeliveryDate.value   = saveRecord[g_lngSelIndex][21];          //Ǽ��
	}

	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		window.parent.DSO.strSerialNo.value     = saveRecord[g_lngSelIndex][22];              //No
		window.parent.DSO.dtmDeliveryDate.value = saveRecord[g_lngSelIndex][23];              //Ǽ��
	}

}


// ---------------------------------------------------------------
/**
* ����    : �����ȥơ��֥����̾�����
* �о�    : �������ȡפ������Τ��٤�
* @return : strTableHtml, [String��], �����Ȥ���̾
*/
// ---------------------------------------------------------------
function fncStrTableHtmlColumns()
{


	// ��������ξ��
	if( typeof(window.parent.HSO.SOFlg) == "object" )
	{
		if( window.parent.lngLanguageCode == 1 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">' + 
						  '<td nowrap>&nbsp;</td>'                  +
						  '<td nowrap id="ExStrDL02">����</td>' +
						  '<td nowrap id="ExStrDL01">����ʬ</td>'     +
						  '<td nowrap id="ExStrDL03">ñ��</td>' +
						  '<td nowrap id="ExStrDL04">ñ��</td>'     +
						  '<td nowrap id="ExStrDL05">����</td>'     +
						  '<td nowrap id="ExStrDL06">��ȴ���</td>'     +
						  '<td nowrap id="ExStrDL07">Ǽ��</td>' +
						  '<td nowrap id="ExStrDL08">����</td>'     +
						  '</tr>';
		}
		else if( window.parent.lngLanguageCode == 0 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">' + 
						  '<td>&nbsp;</td>'                  +
						  '<td nowrap id="ExStrDL02">Products</td>'    +
						  '<td nowrap id="ExStrDL01">Goods set code</td>'     +
						  '<td nowrap id="ExStrDL03">Price</td>'  +
						  '<td nowrap id="ExStrDL04">Unit</td>'        +
						  '<td nowrap id="ExStrDL05">Quantity</td>'         +
						  '<td nowrap id="ExStrDL06">Amt Bfr tax</td>'     +
						  '<td nowrap id="ExStrDL07">Delivery date</td>'  +
						  '<td nowrap id="ExStrDL08">Remark</td>'       +
						  '</tr>';
		}
	}

	//ȯ������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" )
	{
		if( window.parent.lngLanguageCode == 1 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">' + 
						  '<td nowrap>&nbsp;</td>'                  +
						  '<td nowrap id="ExStrDL01">����</td>'     +
						  '<td nowrap id="ExStrDL02">��������</td>' +
						  '<td nowrap id="ExStrDL03">��������</td>' +
						  '<td nowrap id="ExStrDL04">ñ��</td>'     +
						  '<td nowrap id="ExStrDL05">ñ��</td>'     +
						  '<td nowrap id="ExStrDL06">����</td>'     +
						  '<td nowrap id="ExStrDL07">��ȴ���</td>' +
						  '<td nowrap id="ExStrDL08">Ǽ��</td>'     +
						  '<td nowrap id="ExStrDL09">����</td>'     +
						  '</tr>';
		}
		else if( window.parent.lngLanguageCode == 0 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">' + 
						  '<td>&nbsp;</td>'                  +
						  '<td nowrap id="ExStrDL01">Products</td>'     +
						  '<td nowrap id="ExStrDL02">Goods set</td>'    +
						  '<td nowrap id="ExStrDL03">Goods parts</td>'  +
						  '<td nowrap id="ExStrDL04">Price</td>'        +
						  '<td nowrap id="ExStrDL05">Unit</td>'         +
						  '<td nowrap id="ExStrDL06">Quantity</td>'     +
						  '<td nowrap id="ExStrDL07">Amt Bfr tax</td>'  +
						  '<td nowrap id="ExStrDL08">Delivery date</td>'  +
						  '<td nowrap id="ExStrDL09">Remark</td>'       +
						  '</tr>';
		}
	}

	// �������ξ��
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		if( window.parent.lngLanguageCode == 1 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">'	+ 
						  '<td nowrap>&nbsp;</td>'                  +
						  '<td nowrap id="ExStrDL09">�о�</td>'		+
						  '<td nowrap id="ExStrDL02">����</td>'		+
						  '<td nowrap id="ExStrDL01">����ʬ</td>'	+
						  '<td nowrap id="ExStrDL03">ñ��</td>'		+
						  '<td nowrap id="ExStrDL04">ñ��</td>'		+
						  '<td nowrap id="ExStrDL05">����</td>'		+
						  '<td nowrap id="ExStrDL10">������</td>'	+
						  '<td nowrap id="ExStrDL06">��ȴ���</td>'	+
						  '<td nowrap id="ExStrDL07">Ǽ��</td>'		+
						  '<td nowrap id="ExStrDL08">����</td>'		+
						  '</tr>';
		}
		else if( window.parent.lngLanguageCode == 0 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">'		+ 
						  '<td>&nbsp;</td>'								+
						  '<td nowrap id="ExStrDL09">Target</td>'		+
						  '<td nowrap id="ExStrDL02">Products</td>'		+
						  '<td nowrap id="ExStrDL01">Goods set code</td>'	+
						  '<td nowrap id="ExStrDL03">Price</td>'		+
						  '<td nowrap id="ExStrDL04">Unit</td>'			+
						  '<td nowrap id="ExStrDL05">Quantity</td>'		+
						  '<td nowrap id="ExStrDL10">Org Quantity</td>'	+
						  '<td nowrap id="ExStrDL06">Amt Bfr tax</td>'	+
						  '<td nowrap id="ExStrDL07">Delivery date</td>'	+
						  '<td nowrap id="ExStrDL08">Remark</td>'		+
						  '</tr>';
		}
	}

	// ���������ξ��
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		if( window.parent.lngLanguageCode == 1 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">' + 
						  '<td nowrap>&nbsp;</td>'                  +
						  '<td nowrap id="ExStrDL10">�о�</td>'     +
						  '<td nowrap id="ExStrDL01">����</td>'     +
						  '<td nowrap id="ExStrDL02">��������</td>' +
						  '<td nowrap id="ExStrDL03">��������</td>' +
						  '<td nowrap id="ExStrDL04">ñ��</td>'     +
						  '<td nowrap id="ExStrDL05">ñ��</td>'     +
						  '<td nowrap id="ExStrDL06">����</td>'     +
						  '<td nowrap id="ExStrDL11">������</td>'   +
						  '<td nowrap id="ExStrDL07">��ȴ���</td>' +
						  '<td nowrap id="ExStrDL08">Ǽ��</td>'     +
						  '<td nowrap id="ExStrDL09">����</td>'     +
						  '</tr>';
		}
		else if( window.parent.lngLanguageCode == 0 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">'		+ 
						  '<td>&nbsp;</td>'								+
						  '<td nowrap id="ExStrDL10">Target</td>'		+
						  '<td nowrap id="ExStrDL01">Products</td>'		+
						  '<td nowrap id="ExStrDL02">Goods set</td>'	+
						  '<td nowrap id="ExStrDL03">Goods parts</td>'	+
						  '<td nowrap id="ExStrDL04">Price</td>'		+
						  '<td nowrap id="ExStrDL05">Unit</td>'			+
						  '<td nowrap id="ExStrDL06">Quantity</td>'		+
						  '<td nowrap id="ExStrDL11">Org Quantity</td>'	+
						  '<td nowrap id="ExStrDL07">Amt Bfr tax</td>'	+
						  '<td nowrap id="ExStrDL08">Delivery date</td>'	+
						  '<td nowrap id="ExStrDL09">Remark</td>'		+
						  '</tr>';
		}
	}

	return strTableHtml;
}


// ---------------------------------------------------------------
/**
* ����    : �����ȥơ��֥�ιԤ����
* �о�    : �������ȡפ������Τ��٤�
* @return : strTableHtml, [String��], �����Ȥ�����
*/
// ---------------------------------------------------------------
function fncStrTableHtmlRows(i)
{
	var lngOffsetVal  = 0;

	var strChkOnPath  = "/img/type01/cmn/check_on.gif";
	var strChkOffPath = "/img/type01/cmn/check_off.gif";
	var strChkImgPath = strChkOffPath;


	if( typeof(window.parent.HSO.SCFlg) == "object" ||
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		// ľ��Ͽ�ǤϤʤ����
		if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
		{
			// ��塢�������Υ����å��ܥå������֤ν���ͤ���Ѥ�����
			if( typeof(eval("window.DL.blnOffset" + i)) != "undefined" )
			{
				if( eval("window.DL.blnOffset" + i + ".value") == 1 )
				{
					lngOffsetVal  = 1;
					strChkImgPath = strChkOnPath;
				}
			}
		}
		// ľ��Ͽ�ξ��
		else
		{
			lngOffsetVal  = 1;
			strChkImgPath = strChkOnPath;
		}
	}


	// ���
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		// ľ��Ͽ�ǤϤʤ����
		if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
		{
			if( saveRecord[i][27] == 1 )
			{
				lngOffsetVal  = 1;
				strChkImgPath = strChkOnPath;
			}
		}
		// ľ��Ͽ�ξ��
		else
		{
			lngOffsetVal  = 1;
			strChkImgPath = strChkOnPath;
		}
	}

	// ����
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		// ľ��Ͽ�ǤϤʤ����
		if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
		{
			if( saveRecord[i][25] == 1 )
			{
				lngOffsetVal  = 1;
				strChkImgPath = strChkOnPath;
			}
		}
		// ľ��Ͽ�ξ��
		else
		{
			lngOffsetVal  = 1;
			strChkImgPath = strChkOnPath;
		}
	}





	// ��������ξ��
	if( typeof(window.parent.HSO.SOFlg) == "object" )
	{
		strTableHtml ='<td align     ="center" nowrap>' + saveRecord[i][0]  +				// ����
					  '</td><td nowrap>'                + saveRecord[i][20] +				// ����ʬ
					  '</td><td align="right" nowrap>'  + saveRecord[i][7]  + "&nbsp;" +	// ñ��
					  '</td><td align="center" nowrap>' + saveRecord[i][9]  +				// ñ�̡�̾�Ρ�
					  '</td><td align="right" nowrap>'  + saveRecord[i][10] + "&nbsp;" +	// ����
					  '</td><td align="right" nowrap>'  + saveRecord[i][11] + "&nbsp;" +	// ��ȴ���
					  '</td><td align="center" nowrap>' + saveRecord[i][21] +				// Ǽ����
					  '</td><td nowrap>'                + saveRecord[i][13] +				// ����
					  '</td>';
		return strTableHtml;
	}

	// ȯ������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" )
	{
		strTableHtml ='<td align="center" nowrap>'      + saveRecord[i][0]  +				// ����
					  '</td><td nowrap>'                + saveRecord[i][3]  +				// �������ܡ�̾�Ρ�
					  '</td><td nowrap>'                + saveRecord[i][5]  +				// �������ʡ�̾�Ρ�
					  '</td><td align="right" nowrap>'  + saveRecord[i][7]  + "&nbsp;" +	// ñ��
					  '</td><td align="center" nowrap>' + saveRecord[i][9]  +				// ñ�̡�̾�Ρ�
					  '</td><td align="right" nowrap>'  + saveRecord[i][10] + "&nbsp;" +	// ����
					  '</td><td align="right" nowrap>'  + saveRecord[i][11] + "&nbsp;" +	// ��ȴ���
					  '</td><td align="center" nowrap>' + saveRecord[i][23] + "&nbsp;" +	// Ǽ��
					  '</td><td nowrap>'                + saveRecord[i][13] +				// ����
					  '</td>';
		return strTableHtml;
	}

	// �������ξ��
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		
		strTableHtml =''+
					  '<td align="center" valign="top">' +
					  '<img onclick="fncSetCheck( this, ' + i + ' );" src="' + strChkImgPath + '" width="12" height="12">' +

					  '</td><td align="center" nowrap>'	+ saveRecord[i][0]  +				// ����
					  '</td><td nowrap>'                + saveRecord[i][20] +				// ����ʬ
					  '</td><td align="right" nowrap>'  + saveRecord[i][7]  + "&nbsp;" +	// ñ��
					  '</td><td align="center" nowrap>' + saveRecord[i][9]  +				// ñ�̡�̾�Ρ�
					  '</td><td align="right" nowrap>'  + saveRecord[i][10] + "&nbsp;" +	// ����
					  '</td><td align="right" nowrap>'  + saveRecord[i][24] + "&nbsp;" +	// ������
					  '</td><td align="right" nowrap>'  + saveRecord[i][11] + "&nbsp;" +	// ��ȴ���
					  '</td><td align="center" nowrap>' + saveRecord[i][21] +				// Ǽ����
					  '</td><td nowrap>'                + saveRecord[i][13] +				// ����
					  '</td>' +
					  '<input type="hidden" name="blnOffset' + i + '" value="' + lngOffsetVal + '">';
		return strTableHtml;
	}
	
	// ���������ξ��
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		strTableHtml ='' +
					  '<td align="center" valign="top">' +
					  '<img onclick="fncSetCheck( this, ' + i + ' );" src="' + strChkImgPath + '" width="12" height="12">' +

					  '<td align="center" nowrap>'      + saveRecord[i][0]  +				// ����
					  '</td><td nowrap>'                + saveRecord[i][3]  +				// �������ܡ�̾�Ρ�
					  '</td><td nowrap>'                + saveRecord[i][5]  +				// �������ʡ�̾�Ρ�
					  '</td><td align="right" nowrap>'  + saveRecord[i][7]  + "&nbsp;" +	// ñ��
					  '</td><td align="center" nowrap>' + saveRecord[i][9]  +				// ñ�̡�̾�Ρ�
					  '</td><td align="right" nowrap>'  + saveRecord[i][10] + "&nbsp;" +	// ����
					  '</td><td align="right" nowrap>'  + saveRecord[i][24] + "&nbsp;" +	// ������
					  '</td><td align="right" nowrap>'  + saveRecord[i][11] + "&nbsp;" +	// ��ȴ���
					  '</td><td align="center" nowrap>' + saveRecord[i][23] + "&nbsp;" +	// Ǽ��
					  '</td><td nowrap>'                + saveRecord[i][13] +				// ����
					  '</td>' +
					  '<input type="hidden" name="blnOffset' + i + '" value="' + lngOffsetVal + '">';
		return strTableHtml;
	}

}


// ---------------------------------------------------------------
/**
* ����   : ��Ͽ�ܥ���򲡤����Ȥ��ˡ�header��ˡ������ȤΥǡ�����hidden���Ǥ��Ф�
* �о�   : �������ȡפ������Τ��٤�
* ����   : select�ܥå�����disabled�ϡ����ΤޤޤǤϡ�post����ʤ��Τǡ�
*          ����Ū��hidden��񤤤Ƥ��ޤ�
*/
// ---------------------------------------------------------------
function fncDtRegistRecord(){



	if( saveRecord.length <= 0 )
	{
		alert( "���ٹԤ�����ޤ���" );
		return;
	}


	// �о��ͺ�����
	fncSetCheckValue();





	// ���ٹ����ʡ�����ʬ�����å�
	var blnCheck = fncCheckDetailRecords( saveRecord );

	if( !blnCheck )
	{
		alert( "���ʤޤ��ϡ�����ʬ���㤤�ޤ���" );
		return false;
	}




	//���ٹԤ����򤵤�Ƥ�����Υ����å��ե饰(�����Ȥ˥��顼������ȥ��顼�ˤʤ�)
	var checkFlg = true;

	//���ٹԤ����򤵤�Ƥ�����
	if( g_lngSelIndex != -1 )
	{
		//�����Ȥ��ѹ����ʤ��������å�
		checkFlg = fncDtCheck();
	}

	if (checkFlg == true)
	{
		var strHiddenHtml = "";
	
		//hidden���Ǥ��Ф�Ϣ�֡ʶ��Ԥ�������Ƚ��֤�����뤿����ѡ�
		var hiddenNumber = 0 ;
	
		for( i = 0; i < saveRecord.length; i++ )
		{
			//���ԥ����å�
			if (saveRecord[i][0] == "") continue;


			// �껦�����å���ǧ�ʥ����å��ܥå����������å�����Ƥ���ԤΤߤ�����оݤȤ���� added by saito
			//alert( eval("window.DL.blnOffset" + i + ".value") );
			if( typeof(eval("window.DL.blnOffset" + i)) != "undefined" )
			{
				if( eval("window.DL.blnOffset" + i + ".value") != 1 ) continue;
			}


			strHiddenHtml = strHiddenHtml + fncDtHiddenHtml(i, hiddenNumber);
			hiddenNumber++; 
		}
	
		//��ǧ�롼�Ȥ��ɲ�
		strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngWorkflowOrderCode' value='" + window.parent.DSO.lngWorkflowOrderCode.value + "' >\n" ;

		// ���祳���ɤ��ɲ�
		strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngInChargeGroupCode' value='" + window.parent.DSO.lngInChargeGroupCode.value + "' >\n" ;

		// ô���ԥ����ɤ��ɲ�
		strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngInChargeUserCode' value='" + window.parent.DSO.lngInChargeUserCode.value + "' >\n" ;


		if( window.parent.HSO.strCustomerReceiveCode )
		{
			// �ܵҼ����ֹ���ɲ�
			strHiddenHtml = strHiddenHtml + "<input type='hidden' name='strCustomerReceiveCode' value='" + window.parent.HSO.strCustomerReceiveCode.value + "' >\n" ;
		}




		//�̲ߤ��ɲ�
		strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngMonetaryUnitCode' value='" + window.parent.HSO.lngMonetaryUnitCode.value + "' >\n" ;
	
		//�졼�ȥ����פ��ɲ�
		strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngMonetaryRateCode' value='" + window.parent.HSO.lngMonetaryRateCode.value + "' >\n" ;
	
		//���׶�ۡ���ȴ���ˤ��ɲ�
		strHiddenHtml = strHiddenHtml + "<input type='hidden' name='curAllTotalPrice' value='" + fncDelKannma(fncDelCurrencySign(window.parent.DSO.curAllTotalPrice.value)) + "' >\n" ;



		//�ǥХå���
		if( g_bytDebugFlag )
		{
			var blnRes = confirm( strHiddenHtml );

			if( !blnRes )
			{
				return false;
			}
		}



		//�ե�����(name="HSO")�������ȤΥǡ������Ϥ�
		window.parent.document.all.DtHiddenRecord.innerHTML = strHiddenHtml;
	
		//�ե�����HSO�򥵥֥ߥå�
		window.parent.document.HSO.submit();
	}
}


// ---------------------------------------------------------------
/**
* ����   : �����Ȥ�hidden���Ǥ��Ф��ǡ��������
* �о�   : �������ȡפ������Τ��٤�
* return : strHiddenHtml, [string��], �����Ȥ����Ƥ�hidden���֤��������Ǥ��Ф�
*/
// ---------------------------------------------------------------
function fncDtHiddenHtml(i, hiddenNumber){

	//�����������������ξ��Τߤ�hidden��
	var strPC = "";

	if( typeof(window.parent.HSO.PCFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		var zeicodevalue;
		//�ǥ�����(�ͤ��饳���ɤ��Ѵ�)
		//����ǤΤȤ�
		if( saveRecord[i][15] == g_strFreeTaxClass )
		{
			zeicodevalue = "";
		}
		//����ǰʳ��ΤȤ�
		else
		{
			zeicodevalue = g_lngTaxCode;
		}

	strPC = "<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngTaxClassCode]'  value='" + saveRecord[i][15] + "' >\n" +                                   //�����Ƕ�ʬ������
			"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngTaxCode]'       value='" + zeicodevalue + "' >\n"      +                                   //�����ǥ�����
			"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curTaxPrice]'      value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][17])) + "' >\n" + //�����ǳ�
			"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngOrderDetailNo]' value='" + saveRecord[i][18] + "' >\n" ;                                   //���ֹ�
	}
// 2004.03.26 suzukaze update start
//���������ȯ������ξ��ˤ���ֹ��Hidden�ͤǳФ��Ƥ����褦�˽���
	else
	{
	strPC = "<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngOrderDetailNo]' value='" + saveRecord[i][18] + "' >\n" ;                                   //���ֹ�
	}
// 2004.03.26 suzukaze update end


	// ��������ξ��
	if( typeof(window.parent.HSO.SOFlg) == "object" )
	{
		strHiddenHtml = "<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strProductCode]'          value='" + saveRecord[i][0] + "' >\n"  + //����
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsPriceCode]'       value='" + saveRecord[i][1] + "' >\n"  + //ñ���ꥹ��
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngConversionClassCode]'  value='" + saveRecord[i][6] + "' >\n"  + //������ʬ
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPrice]'         value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][7])) + "' >\n"  + //ñ��
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCode]'      value='" + saveRecord[i][8] + "' >\n"  + //ñ��
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCodeName]'  value='" + saveRecord[i][9] + "' >\n"  + //ñ�̡�̾�Ρ�
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsQuantity]'        value='" + fncDelKannma(saveRecord[i][10]) + "' >\n"  + //����
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curTotalPrice]'           value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][11])) + "' >\n" + //��ȴ���
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strDetailNote]'           value='" + saveRecord[i][13] + "' >\n" + //����
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPriceForList]'  value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][14])) + "' >\n" + //ñ���ꥹ���ɲåǡ���
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngSalesClassCode]'       value='" + saveRecord[i][19] + "' >\n" + //����ʬ
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngSalesClassCodeName]'   value='" + saveRecord[i][20] + "' >\n" + //����ʬ��value + ̾�Ρ�
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][dtmDeliveryDate]'         value='" + saveRecord[i][21] + "' >\n" + //Ǽ��
						strPC;
	}

	// ȯ������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" )
	{
		strHiddenHtml = "<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strProductCode]'          value='" + saveRecord[i][0] + "' >\n"  + //����
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsPriceCode]'       value='" + saveRecord[i][1] + "' >\n"  + //ñ���ꥹ��
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockSubjectCode]'     value='" + saveRecord[i][2] + "' >\n"  + //��������
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockSubjectCodeName]' value='" + saveRecord[i][3] + "' >\n"  + //�������ܡ�value + ̾�Ρ�
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockItemCode]'        value='" + saveRecord[i][4] + "' >\n"  + //��������
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockItemCodeName]'    value='" + saveRecord[i][5] + "' >\n"  + //�������ʡ�value + ̾�Ρ�
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngConversionClassCode]'  value='" + saveRecord[i][6] + "' >\n"  + //������ʬ
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPrice]'         value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][7])) + "' >\n"  + //ñ��
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCode]'      value='" + saveRecord[i][8] + "' >\n"  + //ñ��
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCodeName]'  value='" + saveRecord[i][9] + "' >\n"  + //ñ�̡�̾�Ρ�
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsQuantity]'        value='" + fncDelKannma(saveRecord[i][10]) + "' >\n"  + //����
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curTotalPrice]'           value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][11])) + "' >\n" + //��ȴ���
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngCarrierCode]'          value='" + saveRecord[i][12] + "' >\n" + //������ˡ
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strDetailNote]'           value='" + saveRecord[i][13] + "' >\n" + //����
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPriceForList]'  value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][14])) + "' >\n" + //ñ���ꥹ���ɲåǡ���
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strSerialNo]'             value='" + saveRecord[i][22] + "' >\n" + //No
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][dtmDeliveryDate]'         value='" + saveRecord[i][23] + "' >\n" + //Ǽ��
						strPC;
	}

	// �������ξ��
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		strHiddenHtml = "<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strProductCode]'          value='" + saveRecord[i][0] + "' >\n"  + //����
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsPriceCode]'       value='" + saveRecord[i][1] + "' >\n"  + //ñ���ꥹ��
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngConversionClassCode]'  value='" + saveRecord[i][6] + "' >\n"  + //������ʬ
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPrice]'         value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][7])) + "' >\n"  + //ñ��
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCode]'      value='" + saveRecord[i][8] + "' >\n"  + //ñ��
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCodeName]'  value='" + saveRecord[i][9] + "' >\n"  + //ñ�̡�̾�Ρ�
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsQuantity]'        value='" + fncDelKannma(saveRecord[i][10]) + "' >\n"  + //����
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][org_lngGoodsQuantity]'    value='" + fncDelKannma(saveRecord[i][24]) + "' >\n"  + // ������
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curTotalPrice]'           value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][11])) + "' >\n" + //��ȴ���
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strDetailNote]'           value='" + saveRecord[i][13] + "' >\n" + //����
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPriceForList]'  value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][14])) + "' >\n" + //ñ���ꥹ���ɲåǡ���
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngSalesClassCode]'       value='" + saveRecord[i][19] + "' >\n" + //����ʬ
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngSalesClassCodeName]'   value='" + saveRecord[i][20] + "' >\n" + //����ʬ��value + ̾�Ρ�
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][dtmDeliveryDate]'         value='" + saveRecord[i][21] + "' >\n" + //Ǽ��
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngReceiveNo]'            value='" + saveRecord[i][25] + "' >\n"  + //�����ֹ�
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngReceiveDetailNo]'      value='" + saveRecord[i][26] + "' >\n"  + //���ٹ��ֹ�

						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngChkVal]'               value='" + saveRecord[i][27] + "' >\n"  + //�о�

						strPC;
	}

	// ���������ξ��
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		strHiddenHtml = "<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strProductCode]'          value='" + saveRecord[i][0] + "' >\n"  + //����
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsPriceCode]'       value='" + saveRecord[i][1] + "' >\n"  + //ñ���ꥹ��
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockSubjectCode]'     value='" + saveRecord[i][2] + "' >\n"  + //��������
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockSubjectCodeName]' value='" + saveRecord[i][3] + "' >\n"  + //�������ܡ�value + ̾�Ρ�
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockItemCode]'        value='" + saveRecord[i][4] + "' >\n"  + //��������
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockItemCodeName]'    value='" + saveRecord[i][5] + "' >\n"  + //�������ʡ�value + ̾�Ρ�
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngConversionClassCode]'  value='" + saveRecord[i][6] + "' >\n"  + //������ʬ
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPrice]'         value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][7])) + "' >\n"  + //ñ��
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCode]'      value='" + saveRecord[i][8] + "' >\n"  + //ñ��
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCodeName]'  value='" + saveRecord[i][9] + "' >\n"  + //ñ�̡�̾�Ρ�
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsQuantity]'        value='" + fncDelKannma(saveRecord[i][10]) + "' >\n"  + //����
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][org_lngGoodsQuantity]'    value='" + fncDelKannma(saveRecord[i][24]) + "' >\n"  + // ������
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curTotalPrice]'           value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][11])) + "' >\n" + //��ȴ���
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngCarrierCode]'          value='" + saveRecord[i][12] + "' >\n" + //������ˡ
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strDetailNote]'           value='" + saveRecord[i][13] + "' >\n" + //����
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPriceForList]'  value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][14])) + "' >\n" + //ñ���ꥹ���ɲåǡ���
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strSerialNo]'             value='" + saveRecord[i][22] + "' >\n" + //No
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][dtmDeliveryDate]'         value='" + saveRecord[i][23] + "' >\n" + //Ǽ��

						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngChkVal]'               value='" + saveRecord[i][25] + "' >\n" + //�о�

						strPC;
	}
	
	return strHiddenHtml;
}


// ---------------------------------------------------------------
/**
* ����   : �����Τ�����Ǥ��Ф��줿hidden�ͤ������Ͽ�ܥ���򲡤������
*          ��äƤ���hidden�ͤ򿷵�������˳�Ǽ
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtNewAryForReturn(i)
{
	var aryRecord = new Array();
	aryRecord[0]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strProductCode]").value;          //���ʥ�����
	aryRecord[1]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngGoodsPriceCode]").value;       //ñ���ꥹ��

	// Added by Kazushi Saito
	// �׻���ˡ���̤μ���
	if( typeof(window.parent.DSO.lngCalcCode) != "undefined" )
	{
		g_lngCalcCode = window.parent.DSO.lngCalcCode.value;
	}
	// Added by Kazushi Saito
	// �������ʲ��ν������
	g_lngDecimalCutPoint = 2;
	// ���ܱߤξ�硢�������ʲ��ν���������ѹ�
	if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign )
	{
		g_lngDecimalCutPoint = 0;
	}

	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[2]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockSubjectCode]").value;     //��������
		aryRecord[3]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockSubjectCodeName]").value; //�������ܡ�value + ̾�Ρ�
		aryRecord[4]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockItemCode]").value;        //��������
		aryRecord[5]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockItemCodeName]").value;    //�������ʡ�value + ̾�Ρ�
	}

	aryRecord[6]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngConversionClassCode]").value;  //������ʬ(����ñ�̷׾�)
	aryRecord[7]  = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][curProductPrice]").value, 4); //ñ��
	aryRecord[8]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngProductUnitCode]").value;      //ñ��
	aryRecord[9]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngProductUnitCodeName]").value;  //ñ�̡�̾�Ρ�
	aryRecord[10] = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][lngGoodsQuantity]").value, 0, false); //����
	aryRecord[11] = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][curTotalPrice]").value, 2); //��ȴ���

	// ����������������
	if( typeof(window.parent.HSO.SCFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[24] = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][org_lngGoodsQuantity]").value, 0, false); // ������
	}

	// ������
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		aryRecord[25] = window.parent.DSO.elements( "aryPoDitail[" + i + "][lngReceiveNo]" ).value;       // �����ֹ�
		aryRecord[26] = window.parent.DSO.elements( "aryPoDitail[" + i + "][lngReceiveDetailNo]" ).value; // ���ٹ��ֹ�
		aryRecord[27] = window.parent.DSO.elements( "aryPoDitail[" + i + "][lngChkVal]" ).value;          // �о�
	}
	// ��������
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[25] = window.parent.DSO.elements( "aryPoDitail[" + i + "][lngChkVal]" ).value; // �о�
	}



	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[12] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngCarrierCode]").value;          //������ˡ
	}

	aryRecord[13] = fncCheckReplaceString(window.parent.DSO.elements("aryPoDitail[" + i + "][strDetailNote]").value);           //����
	aryRecord[14] = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][curProductPriceForList]").value, 4, false); //ñ���ꥹ���ɲåǡ���

	//�����������������ξ��
	if( typeof(window.parent.HSO.PCFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		//�����Ƕ�ʬ������
		aryRecord[15] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngTaxClassCode]").value;

		//�����Ƕ�ʬ�����ɤ����ꤵ��Ƥʤ��ä���ǥե���ȳ���
		if( aryRecord[15] == "" )
		{
			aryRecord[15] = g_strOutTaxClass;
		}

		//�����Ƕ�ʬ�����ɤ�����ǤΤȤ�
		if( aryRecord[15] == g_strFreeTaxClass )
		{
			//�����ǥ�����
			aryRecord[16] = "";
			//�����ǳ�
			aryRecord[17] = "";
		}
		//�����Ƕ�ʬ�����ɤ�����ǰʳ��ΤȤ�
		else
		{
			//�����ǥ�����
			aryRecord[16] = g_curTax;
//2004.07.09 suzukaze update start
//���������ǳۤ�Hidden�ͤ򤽤Τޤޤˤ��롡�Ʒ׻�������ȴ��ۤ򸵤˷׻����Ƥ��뤿�������ͤ����������ʤ뤿��
//			aryRecord[17] = fncDtCalTaxPrice(aryRecord[11],aryRecord[15]);
			aryRecord[17] = window.parent.DSO.elements("aryPoDitail[" + i + "][curTaxPrice]").value;  //�ǳ�;
//2004.07.09 suzukaze update end

			// �ǳۤΥե����ޥå�
			aryRecord[17]   = window.parent.fncCheckNumberValue(aryRecord[17], 2, true, g_lngDecimalCutPoint, g_lngCalcCode);
			//alert("�Ƕ�ʬ�����ɡ�"+aryRecord[15]+"��Ψ��"+aryRecord[16]+"�ǳۡ�"+aryRecord[17]);
		}

		aryRecord[18] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngorderdetailno]").value; //���ֹ�
	}
// 2004.03.26 suzukaze update start
//���������ȯ������ξ��ˤ���ֹ��Hidden�ͤǳФ��Ƥ����褦�˽���
	else
	{
		aryRecord[18] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngorderdetailno]").value; //���ֹ�
	}
// 2004.03.26 suzukaze update end

	//����������������ξ��
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		aryRecord[19] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngSalesClassCode]").value; //����ʬ
		aryRecord[20] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngSalesClassCodeName]").value; //����ʬ��value + ̾�Ρ�
		aryRecord[21] = window.parent.DSO.elements("aryPoDitail[" + i + "][dtmDeliveryDate]").value; //Ǽ��
	}

	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[22] = window.parent.DSO.elements("aryPoDitail[" + i + "][strSerialNo]").value; //No
		aryRecord[23] = window.parent.DSO.elements("aryPoDitail[" + i + "][dtmDeliveryDate]").value; //Ǽ��
	}

	return aryRecord;
}


// ---------------------------------------------------------------
/**
* ����   : ��ȴ����פ򻻽Ф���
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtCalTotalPrice()
{
	var ProductPrice  = 0;	// ñ��
	var GoodsQuantity = 0;	// ����
	var TotalPrice    = 0;	// ��ȴ���


	// Added by Kazushi Saito
	// �׻���ˡ���̤μ���
	if( typeof(window.parent.DSO.lngCalcCode) != "undefined" )
	{
		g_lngCalcCode = window.parent.DSO.lngCalcCode.value;
	}

	
	//������ʬ(����ñ�̷׾�)
	if( window.parent.DSO.lngConversionClassCode[0].checked )
	{
		//��������å�
		if( window.parent.DSO.curProductPrice_gs.value  == "" || 
			window.parent.DSO.lngGoodsQuantity_gs.value == "" )
		{
			//��ȴ��ۤ����ˤ���
			window.parent.DSO.curTotalPrice.value = "";
			return false;
		}
		
		//�����Ȥ�[����ñ��]�����ͤ����ơ��̲ߵ��桢����ޤ���
		ProductPrice  = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value));
		//�����Ȥ�[���ʿ���]�����ͤ����ơ�����ޤ���
		GoodsQuantity = fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value);
	}
	//�ٻ�ñ�̷׾�ξ��
	else if( window.parent.DSO.lngConversionClassCode[1].checked )
	{
		//[���ʿ���]��[�����ȥ��]��[�ٻѿ���]��ȿ�Ǥ�����
		if( window.parent.DSO.lngGoodsQuantity_ps.value == "" )
		{
			//[�ٻѿ���]������ξ��ˤϡ������ȿ��
			window.parent.DSO.lngGoodsQuantity_gs.value = "";
		}
		else
		{
			//�����Ȥβٻѿ��̤����ͤ����ơ�����ޤ���
			GoodsQuantity = fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value);

			var CartonQuantity   = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);
			var GoodsQuantity_gs = CartonQuantity * GoodsQuantity;
			window.parent.DSO.lngGoodsQuantity_gs.value = GoodsQuantity_gs;
			window.parent.fncCheckNumber( window.parent.DSO.lngGoodsQuantity_gs , 0 , false );
		}

		//[�ٻ�ñ��]�ޤ���[�ٻѿ���]������ξ��
		if( window.parent.DSO.curProductPrice_ps.value  == "" || 
			window.parent.DSO.lngGoodsQuantity_ps.value == "" )
		{
			//[��ȴ���]�����ˤ���
			window.parent.DSO.curTotalPrice.value = "";
			return false;
		}
		else
		{
			//�����Ȥβٻ�ñ�������ͤ����ơ��̲ߵ��桢����ޤ���
			ProductPrice  = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value));
		}
	}


	//��ȴ���
//	TotalPrice    = ProductPrice * GoodsQuantity;
	TotalPrice    = window.parent.fncVBSNumCalc(ProductPrice, "*", GoodsQuantity);

	//��ȴ��ۤ������Ȥ�ȿ��
	window.parent.DSO.curTotalPrice.value = TotalPrice;
	//��ȴ��ۤ򾮿����ʲ�2��ǥե����ޥåȤ���
//	window.parent.fncCheckNumber( window.parent.DSO.curTotalPrice , 2);
	window.parent.DSO.curTotalPrice.value = window.parent.fncCheckNumberValue( TotalPrice, 2, true, 2, g_lngCalcCode);

	//��������(PCFlg)��������(SCFlg)�ξ�硢�ǳۤ�׻�
	if( typeof(window.parent.HSO.PCFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		window.parent.DSO.curTaxPrice.value = fncDtCalTaxPrice(window.parent.DSO.curTotalPrice.value,
																window.parent.DSO.lngTaxClassCode.value,
																window.parent.DSO.lngTaxCode.value); //�����ǳ�

	}

	// Added by Kazushi Saito
	// �������ʲ��ν������
	g_lngDecimalCutPoint = 2;
	// ���ܱߤξ�硢�������ʲ��ν���������ѹ�
	if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign )
	{
		g_lngDecimalCutPoint = 0;
	}

	// Added by Kazushi Saito
	// ������(SCFlg)
	// ��������(PCFlg)
	//����ȴ��ۡס����ǳۡפ򾮿����ʲ���2��˷׻�����
	if( typeof(window.parent.HSO.SCFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{

		// ��ȴ���
		window.parent.DSO.curTotalPrice.value = window.parent.fncCheckNumberValue(window.parent.DSO.curTotalPrice.value, 2, true, g_lngDecimalCutPoint, g_lngCalcCode);
		// �ǳ�
		window.parent.DSO.curTaxPrice.value   = window.parent.fncCheckNumberValue(window.parent.DSO.curTaxPrice.value, 2, true, g_lngDecimalCutPoint, g_lngCalcCode);


		// ���Ǥξ��κƷ׻�
		if( window.parent.DSO.lngTaxClassCode.value == g_strInTaxClass )
		{
			// �̲ߵ��桦����ޤ����
			curTotalPrice = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curTotalPrice.value));
			curTaxPrice   = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curTaxPrice.value));
			// ��ȴ���
			curCalcTotalPrice = window.parent.fncVBSNumCalc(curTotalPrice, "-", curTaxPrice);
			window.parent.DSO.curTotalPrice.value = window.parent.fncCheckNumberValue(curCalcTotalPrice, 2, true, g_lngDecimalCutPoint, g_lngCalcCode);
		}
	}


	// Added by Kazushi Saito
	// �������(SOFlg)
	// ȯ�����(POFlg)
	// �����ܱߤξ��Τߡ�
	//����ȴ��ۡפ򾮿����ʲ���2��˷׻�����
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.POFlg) == "object")
	{
		// ��ȴ���
		window.parent.DSO.curTotalPrice.value = window.parent.fncCheckNumberValue(window.parent.DSO.curTotalPrice.value, 2, true, g_lngDecimalCutPoint, g_lngCalcCode);
	}

	//����̲ߤ�ɽ��
	fncDtCalStdTotalPrice();
}


// ---------------------------------------------------------------
/**
* ����   : ����ñ�̷׾�Υ饸���ܥ��󤬲����줿�Ȥ��ν���
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtGsChecked()
{
	//ñ���ꥹ�Ȥ��ʤ��ä�������Ǥ��ʤ�
	if( window.parent.DSO.lngGoodsPriceCode.length == 0  ||
		window.parent.DSO.lngGoodsPriceCode.options[0].text == "(No Data)" )
	{
		window.parent.DSO.lngGoodsPriceCode.disabled = true;
	}
	//ñ���ꥹ�Ȥ����ä�������Ǥ���褦�ˤ���
	else
	{
		window.parent.DSO.lngGoodsPriceCode.disabled = false;
	}

	//[����ñ��][����ñ��][���ʿ���]�����ϡ�����Ǥ���褦�ˤ��롣
	window.parent.DSO.curProductPrice_gs.disabled    = false;
	window.parent.DSO.lngProductUnitCode_gs.disabled = false;
	window.parent.DSO.lngGoodsQuantity_gs.disabled   = false;

	//[�ٻ�ñ��][�ٻ�ñ��][�ٻѿ���]�����ϡ�����Ǥ��ʤ��褦�ˤ���
	window.parent.DSO.curProductPrice_ps.disabled    = true;
	window.parent.DSO.lngProductUnitCode_ps.disabled = true;
	window.parent.DSO.lngGoodsQuantity_ps.disabled   = true;

	//��ȴ��ۤ�Ʒ׻�
	fncDtCalTotalPrice();
}


// ---------------------------------------------------------------
/**
* ����   : �ٻ�ñ�̷׾�Υ饸���ܥ��󤬲����줿�Ȥ��ν���
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtPsChecked()
{
	//�ܥ�����ư���Ƥ�褤���ɤ����Υե饰
	var checkFlg = true ;


	//�����������������ξ��ǡ����ֹ�Τ�����ˡ��ٻ�ñ�̷׾�����򤷤Ƥ�褤���ɤ����Υ����å�
	if( (typeof(window.parent.HSO.PCFlg) == "object"   || 
		 typeof(window.parent.HSO.SCFlg) == "object" ) && 
				g_lngSelIndex != -1                            && 
				saveRecord[g_lngSelIndex][18] != ""            )
	{
		checkFlg = fncDtPsCheckedForPC();
	}

	//����Ǥ��ʤ����ˤϡ����顼�Ȥ�Ф��ƽ�����ȴ����
	if( checkFlg == false )
	{
		//������ʬ(����ñ�̷׾�)�˥����å����᤹
		window.parent.DSO.lngConversionClassCode[0].checked = true;

		alert( "[���ʿ���]��[�����ȥ�����]������ڤ�ʤ�����\n����Ǥ��ޤ���");

		return false;
	}

	//[����ñ��]������Ǥʤ����ˡ�[�ٻ�ñ��]�ˡ�[����ñ��]��[�����ȥ�����]�򥻥åȤ���
	if( window.parent.DSO.curProductPrice_gs.value != "" )
	{
		var ProductPrice_gs  = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value));
		var CartonQuantity   = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);
		var ProductPrice_ps  = ProductPrice_gs * CartonQuantity;
		window.parent.DSO.curProductPrice_ps.value = ProductPrice_ps;
		window.parent.fncCheckNumber(window.parent.DSO.curProductPrice_ps , 4, true);
	}

	//[�ٻѿ���]������ξ��ˡ�1�פ򥻥åȤ���
	if( window.parent.DSO.lngGoodsQuantity_ps.value == "" )
	{
		window.parent.DSO.lngGoodsQuantity_ps.value = 1;
	}

	//ñ���ꥹ�Ȥ�����Ǥ��ʤ��褦�ˤ���
	window.parent.DSO.lngGoodsPriceCode.disabled = true;

	//[����ñ��][����ñ��][���ʿ���]�����ϡ�����Ǥ��ʤ��褦�ˤ���
	window.parent.DSO.curProductPrice_gs.disabled    = true;
	window.parent.DSO.lngProductUnitCode_gs.disabled = true;
	window.parent.DSO.lngGoodsQuantity_gs.disabled   = true;

	//[�ٻ�ñ��][�ٻ�ñ��][�ٻѿ���]�����ϡ�����Ǥ���褦�ˤ���
	window.parent.DSO.curProductPrice_ps.disabled    = false;
	window.parent.DSO.lngProductUnitCode_ps.disabled = false;
	window.parent.DSO.lngGoodsQuantity_ps.disabled   = false;

	//��ȴ��ۤ�Ʒ׻�
	fncDtCalTotalPrice();
}


// ---------------------------------------------------------------
/**
*  ����   :�ٻ�ñ�̷׾������Ǥ��뤫�ɤ����Υ����å�
*  �о�   :�����������������ǹ��ֹ�Τ�����
*/
// ---------------------------------------------------------------
function fncDtPsCheckedForPC()
{
	//�����ȥ�����
	var CartonQuantity   = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);

	//�ǥե���Ȥ����ʿ���
	var GoodsQuantity_gs_defalt = fncDtGSGoodsQuantityDefalt();

	//�����ȥ���̤�0�ޤ��϶��ΤȤ�����Ǥ��ʤ�
	if( CartonQuantity == "" || CartonQuantity == 0 )
	{
		return false;
	}

	//�ٻѿ���(���ʿ��� / �����ȥ������)
	var GoodsQuantity_ps = parseInt( GoodsQuantity_gs_defalt / CartonQuantity );


	//�ǥե���Ȥ����ʿ��̤ȥ����ȥ���̤������ǳ���ڤ�������Ǥ���(���ʿ��� == �ٻѿ��̡ߥ����ȥ�����)
	if( GoodsQuantity_gs_defalt == (GoodsQuantity_ps * CartonQuantity ) )
	{
		//[�ٻѿ���]��[���ʿ���]��[�����ȥ�����]�򥻥å�
		window.parent.DSO.lngGoodsQuantity_ps.value = fncAddKannma( GoodsQuantity_ps );
		return true;
	}
	else
	{
		return false;
	}
}


// ---------------------------------------------------------------
/**
*  ����   :�ٻѿ��̤����ϥ����å�
*  �о�   :����������������
*			���ֹ�Τ�����ǡ��ٻѿ��̤˥����å�����Ƥ�����
*/
// ---------------------------------------------------------------
function fncDtPSGoodsQuantityForPC()
{

	// ���򤵤�Ƥ��ʤ����Ͻ������ʤ�
	if( g_lngSelIndex == -1 )
	{
		return false;
	}
	// ���ֹ椬̵�����Ͻ������ʤ�
	if( typeof(saveRecord[g_lngSelIndex][18]) == "undefined" ||	saveRecord[g_lngSelIndex][18] == "" )
	{
		return false;
	}

	// ����������
	switch (fncGetKindOfManagement())
	{
		// �������ξ��
		case g_strSCKindOfManagement:
			strCheckCode = window.parent.HSO.strReceiveCode.value;	// �ּ���No.�פ����
			break;
		// ���������ξ��
		case g_strPCKindOfManagement:
			strCheckCode = window.parent.HSO.strOrderCode.value;	// ��ȯ��No.�פ����
			break;
		default:
			return false;
	}
	// ����No.��ȯ��No. ��̵���ǡ����ξ��ϥ����å����ʤ�
	if( window.parent.HSO.strPageCondition.value == "regist" && strCheckCode == "" )
	{
		return false;
	}
	
	//�����ȥ������
	var CartonQuantity   = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);
	//�ǥե���Ȥ����ʿ���
	var GoodsQuantity_gs_defalt = fncDtGSGoodsQuantityDefalt();
	//���Ϥ��줿�˲ٻѿ���
	var GoodsQuantity_ps = parseInt(fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value));

	//�����ȥ���̤�0�ޤ��϶��ΤȤ������å���λ
	if( CartonQuantity == "" || CartonQuantity == 0 )
	{
		return false;
	}

	//�ٻѿ��̤����ϤǤ�����
	var GoodsQuantity_ps_max = parseInt(GoodsQuantity_gs_defalt / CartonQuantity) ;

	//���Ϥ��줿�ٻѿ��̤������Ͱʲ��ξ�硢���ʥ��󥹤��ʤ�
	if( GoodsQuantity_ps <= GoodsQuantity_ps_max )
	{
		return false;
	}
	
	// ������Ͽ��
	if( window.parent.HSO.strPageCondition.value == "regist" )
	{
		// ����ͤ�����
		window.parent.DSO.lngGoodsQuantity_ps.value = fncAddKannma(GoodsQuantity_ps_max);
		
		// ���顼��å����������
		alert("�ٻѿ��̤����ϤǤ����¤ϡ�" + GoodsQuantity_ps_max + "�Ǥ�");
		
		//
		window.parent.DSO.lngGoodsQuantity_ps.select();

	}
	// ������
	else
	{
		retVal = confirm("��Ͽ�Ѥߤβٻѿ��̤� " + GoodsQuantity_ps_max + " �Ǥ�\nǤ�դ� " + GoodsQuantity_ps + " ���ѹ����ޤ�����");
		if( retVal == false )
		{
			// Ǥ���ͤ򥻥å�
			window.parent.DSO.lngGoodsQuantity_ps.value = fncAddKannma(GoodsQuantity_ps_max);
		}
	}
	
}


// ---------------------------------------------------------------
/**
*  ����   :���ʿ��̤����ϥ����å�
*  �о�   :����������������
*			���ֹ�Τ�����ǡ����ʿ��̤˥����å�����Ƥ�����
*/
// ---------------------------------------------------------------
function fncDtGSGoodsQuantityForPC()
{
	
	// ���򤵤�Ƥ��ʤ����Ͻ������ʤ�
	if( g_lngSelIndex == -1 )
	{
		return false;
	}
	// ���ֹ椬̵�����Ͻ������ʤ�
	if( typeof(saveRecord[g_lngSelIndex][18]) == "undefined" ||	saveRecord[g_lngSelIndex][18] == "" )
	{
		return false;
	}

	// ����������
	switch (fncGetKindOfManagement())
	{
		// �������ξ��
		case g_strSCKindOfManagement:
			strCheckCode = window.parent.HSO.strReceiveCode.value;	// �ּ���No.�פ����
			break;
		// ���������ξ��
		case g_strPCKindOfManagement:
			strCheckCode = window.parent.HSO.strOrderCode.value;	// ��ȯ��No.�פ����
			break;
		default:
			return false;
	}
	// ����No.��ȯ��No. ��̵���ǡ����ξ��ϥ����å����ʤ�
	if( window.parent.HSO.strPageCondition.value == "regist" && strCheckCode == "" )
	{
		return false;
	}

	// �ǥե���Ȥ����ʿ���
	var GoodsQuantity_gs_defalt = parseInt(fncDtGSGoodsQuantityDefalt());
	// ���Ϥ��줿�����ʿ���
	var GoodsQuantity_gs = parseInt(fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value));

	// ���Ϥ��줿���ʿ��̤��ǥե�����Ͱʲ��ξ�硢���ʥ��󥹤��ʤ�
	if( GoodsQuantity_gs <= GoodsQuantity_gs_defalt )
	{
		return false;
	}

	// ������Ͽ��
	if( window.parent.HSO.strPageCondition.value == "regist" )
	{
		// ����ͤ�����
		window.parent.DSO.lngGoodsQuantity_gs.value = fncAddKannma(GoodsQuantity_gs_defalt);

		// ���顼��å����������
		alert("���ʿ��̤����ϤǤ����¤ϡ�" + GoodsQuantity_gs_defalt + " �Ǥ�");
		
		//
		window.parent.DSO.lngGoodsQuantity_gs.select();

	}
	// ������
	else
	{
		retVal = confirm("��Ͽ�Ѥߤ����ʿ��̤� " + GoodsQuantity_gs_defalt + " �Ǥ�\nǤ�դ� " + GoodsQuantity_gs + " ���ѹ����ޤ�����");
		if( retVal == false )
		{
			// Ǥ���ͤ򥻥å�
			window.parent.DSO.lngGoodsQuantity_gs.value = fncAddKannma(GoodsQuantity_gs_defalt);
		}
	}

}


// ---------------------------------------------------------------
/**
*  ����   :�������̤μ���Ƚ��
*  �о�   :����
*/
// ---------------------------------------------------------------
function fncGetKindOfManagement()
{
	// ��������
	var strCheckStatus = "";
	
	// �������
	if( typeof(window.parent.HSO.SOFlg) == "object" )
	{
		strCheckStatus = g_strSOKindOfManagement;
	}
	// ������
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		strCheckStatus = g_strSCKindOfManagement;
	}
	// ȯ�����
	if( typeof(window.parent.HSO.POFlg) == "object" )
	{
		strCheckStatus = g_strPOKindOfManagement;
	}
	// ��������
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		strCheckStatus = g_strPCKindOfManagement;
	}

	return strCheckStatus;
}


// ---------------------------------------------------------------
/**
*  ����   :���ֹ椫��ǥե���Ȥ����ʿ��̤�����
*  �о�   :���������ǹ��ֹ�Τ�����
*/
// ---------------------------------------------------------------
function fncDtGSGoodsQuantityDefalt()
{
	//�ǥե���Ȥ����ʿ���
	var GoodsQuantity_gs_defalt = 0;

	//�롼�׽����Υ���ǥå���
	var i = 0;

	//���ʥ����ɤ����뤫����롼��
	while( window.parent.DSO.elements("aryPoDitail[" + i + "][strProductCode]") != null )
	{
		//Hidden���Ǥ��Ф��줿���ֹ�����򤵤줿���ֹ椬Ʊ��ξ������ʿ��̤�����
		if( window.parent.DSO.elements("aryPoDitail[" + i + "][lngorderdetailno]").value != saveRecord[g_lngSelIndex][18] )
		{
			i++;
			continue;
		}

		// ���ξ��Τ�
		if( typeof(window.parent.HSO.SCFlg) == "object" )
		{
			// �����ֹ����פ��Ƥ��뤫���ǧ�����ʣ�������ֹ椬�ߤ�����١�
			if( window.parent.DSO.elements("aryPoDitail[" + i + "][lngReceiveNo]").value != saveRecord[g_lngSelIndex][25] )
			{
				i++;
				continue;
			}
		}



		//������ʬ������ñ�̷׾�ξ��
		if( window.parent.DSO.elements("aryPoDitail[" + i + "][lngConversionClassCode]").value == "gs" )
		{
			//�ǥե���Ȥ����ʿ���
			var GoodsQuantity_gs_defalt = window.parent.DSO.elements("aryPoDitail[" + i + "][lngGoodsQuantity]").value
		}
		//������ʬ���ٻ�ñ�̷׾�ξ��
		else if( window.parent.DSO.elements("aryPoDitail[" + i + "][lngConversionClassCode]").value == "ps" )
		{
			//�ǥե���Ȥβٻѿ���
			var GoodsQuantity_ps_defalt = window.parent.DSO.elements("aryPoDitail[" + i + "][lngGoodsQuantity]").value
			//�����ȥ�����
			var CartonQuantity   = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);
			//�ǥե���Ȥ����ʿ���(�ǥե���Ȥβٻѿ��̡ߥ����ȥ�����)
			var GoodsQuantity_gs_defalt = GoodsQuantity_ps_defalt * CartonQuantity;
		}
		//�롼�׽�����λ
		break;

	}

	return GoodsQuantity_gs_defalt;
}


// ---------------------------------------------------------------
/**
*  ����   : [����ñ��]��[�ٻ�ñ��]��[�����ȥ�����]�򥻥å�
*  �о�   : ������塢ȯ������������[�ٻ�ñ�̷׾�]�ΤȤ���[�ٻ�ñ��]����onBlur�����Ȥ�
*           �����ٹԤ����򤷤��Ȥ�
*/
// ---------------------------------------------------------------
function fncDtPSProductPrice()
{
	//�ٻ�ñ��
	var ProductPrice_ps = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value));
	//�����ȥ������
	var CartonQuantity  = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);

	//�����ȥ���̤�0�ޤ��϶��ΤȤ�������λ
	if( CartonQuantity == "" || CartonQuantity == 0 )
	{
		return false;
	}

	//[����ñ��]��[�ٻ�ñ��]��[�����ȥ�����]�򥻥å�
	window.parent.DSO.curProductPrice_gs.value = window.parent.fncCheckNumberValue((ProductPrice_ps / CartonQuantity), 4);
}


// ---------------------------------------------------------------
/**
* ����   : ���׶�ۤ򻻽Ф���
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtCalAllTotalPrice()
{
	if( saveRecord.length == 0 )
	{
		//���ٹԤ��ʤ��������׶�ۤ򤫤�ˤ���
		window.parent.DSO.curAllTotalPrice.value = "";
	}
	else
	{
		//���׶��
		var AllTotalPrice = 0;
		//���ٹԤο�
		var saveRecordLength = saveRecord.length;

		for( i = 0; i < saveRecordLength; i++ )
		{
			// ���
			if( typeof(window.parent.HSO.SCFlg) == "object" )
			{
				// ľ��Ͽ�ǤϤʤ����
				if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
				{
					if( saveRecord[i][27] == 1 )
					{
						AllTotalPrice += parseInt( 10000 * fncDelKannma( fncDelCurrencySign( saveRecord[i][11] ) ) );
					}
				}
				// ľ��Ͽ�ξ��
				else
				{
					AllTotalPrice += parseInt( 10000 * fncDelKannma( fncDelCurrencySign( saveRecord[i][11] ) ) );
				}
			}

			// ����
			if( typeof(window.parent.HSO.PCFlg) == "object" )
			{
				// ľ��Ͽ�ǤϤʤ����
				if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
				{
					if( saveRecord[i][25] == 1 )
					{
						AllTotalPrice += parseInt( 10000 * fncDelKannma( fncDelCurrencySign( saveRecord[i][11] ) ) );
					}
				}
				// ľ��Ͽ�ξ��
				else
				{
					AllTotalPrice += parseInt( 10000 * fncDelKannma( fncDelCurrencySign( saveRecord[i][11] ) ) );
				}
			}

			// ����ȯ��
			if( typeof(window.parent.HSO.SOFlg) == "object" ||
				typeof(window.parent.HSO.POFlg) == "object" )
			{
				AllTotalPrice += parseInt( 10000 * fncDelKannma( fncDelCurrencySign( saveRecord[i][11] ) ) );
			}
		}

		AllTotalPrice = AllTotalPrice / 10000 ;


		//���׶�ۤ������Ȥ�ȿ��
		window.parent.DSO.curAllTotalPrice.value = AllTotalPrice;
		//���׶�ۤ�ե����ޥåȤ���
		window.parent.fncCheckNumber( window.parent.DSO.curAllTotalPrice , 2 );
	}
}


// ---------------------------------------------------------------
/**
* ����   : ����̲ߤ򻻽Ф���
* �о�   : ȯ����������������ξ����̲ߤ����ܱ߰ʳ�
*/
// ---------------------------------------------------------------
function fncDtCalStdTotalPrice()
{
	//ȯ����������������ξ��Τ�
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		//���ܱߤΤȤ���ɽ�������ʤ�
		if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign )
		{
			//[����̲�]�����ˤ���
			window.parent.DSO.curStdTotalPrice.value = "";
			//���֥�����ɥ���[�ǳ�]�����ˤ���
			window.parent.DSO.curSubTaxPrice.value = "";
			//���֥�����ɥ���[��׶��]�����ˤ���
			window.parent.DSO.curTotalStdAmt.value = "";
			return false;
		}

		//[��ȴ���]�����
		var TotalPrice = parseFloat(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curTotalPrice.value)));

		//[��ȴ���]���ʤ��ä��������λ
		if( TotalPrice == "" || TotalPrice == 0 || isNaN(TotalPrice) )
		{
			//[����̲�]�����ˤ���
			window.parent.DSO.curStdTotalPrice.value = "";
			//���֥�����ɥ���[�ǳ�]�����ˤ���
			window.parent.DSO.curSubTaxPrice.value = "";
			//���֥�����ɥ���[��׶��]�����ˤ���
			window.parent.DSO.curTotalStdAmt.value = "";
	
			return false;
		}

		//[�����졼��]�����
		var ConversionRate = fncDelKannma(fncDelCurrencySign(window.parent.HSO.curConversionRate.value));
	
		//[����̲�]����ƥե����ޥå�
		var StdTotalPrice = window.parent.fncCheckNumberValue(TotalPrice * ConversionRate, 2 ,false);
	
		//[����̲�]�˱ߥޡ�����Ĥ���
		window.parent.DSO.curStdTotalPrice.value = g_strJpnCurrencySign + " " + StdTotalPrice;
	
		//���֥�����ɤ�ɽ��
		//ȯ������ξ��
		if( typeof(window.parent.HSO.POFlg) == "object" )
		{
			window.parent.DSO.curTotalStdAmt.value = window.parent.DSO.curStdTotalPrice.value; //[��׶��]
		}
		//���������ξ��
		else if( typeof(window.parent.HSO.PCFlg) == "object" )
		{
			//�����ǳ�
			var TaxPrice = parseFloat(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curTaxPrice.value)));
	
			//����Ǥξ��
			if( window.parent.DSO.lngTaxClassCode.value == 1 )
			{
				window.parent.DSO.curSubTaxPrice.value = "" ;                                    //[�ǳ�]
				//[��׶��]=����̲ߤ�Ʊ��
				window.parent.DSO.curTotalStdAmt.value = window.parent.DSO.curStdTotalPrice.value ; //[��׶��]
			}
			//���Ǥξ��
			else if( window.parent.DSO.lngTaxClassCode.value == 2 )
			{
				//[�ǳ�]=[�����ǳ�]��[�����졼��]
				var SubTaxPrice = window.parent.fncCheckNumberValue(TaxPrice * ConversionRate, 2 ,false);
				window.parent.DSO.curSubTaxPrice.value = g_strJpnCurrencySign + " " + SubTaxPrice ; //[�ǳ�]
				//[��׶��]=[��ȴ����]+[�����ǳ�]
				var TotalStdAmt = window.parent.fncCheckNumberValue(((TotalPrice + TaxPrice) * ConversionRate), 2 ,false);
				window.parent.DSO.curTotalStdAmt.value = g_strJpnCurrencySign + " " + TotalStdAmt ; //[��׶��]
			}
			//���Ǥξ��
			else if( window.parent.DSO.lngTaxClassCode.value == 3 )
			{
				//[�ǳ�]=[�����ǳ�]��[�����졼��]
				var SubTaxPrice = window.parent.fncCheckNumberValue(TaxPrice * ConversionRate, 2 ,false);
				window.parent.DSO.curSubTaxPrice.value = g_strJpnCurrencySign + " " + SubTaxPrice ;	//[�ǳ�]
				//[��׶��]=����̲ߤ�Ʊ��
				window.parent.DSO.curTotalStdAmt.value = window.parent.DSO.curStdTotalPrice.value ;	//[��׶��]
			}
		}
	}
}


// ---------------------------------------------------------------
/**
* ����   : [�̲�]�������ѹ��ˤ�����
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncHdMonetaryUnitCode()
{
	//���ܱߤξ��	
	if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign )
	{
		//[�졼�ȥ�����]������Ǥ��ʤ��褦�ˤ���
		window.parent.HSO.lngMonetaryRateCode.disabled = true;

		//[�졼�ȥ�����]�����ˤ���
		window.parent.HSO.lngMonetaryRateCode.value = g_strNoneMonetaryRate;

		//[�����졼��]���Խ��Ǥ��ʤ��褦�ˤ���
		window.parent.HSO.curConversionRate.contentEditable = 'false';

		//[�����졼��]�򥯥ꥢ����
		window.parent.HSO.curConversionRate.value = "1.000000";
	}
	//���ܱ߰ʳ��ξ��
	else
	{
		//[�졼�ȥ�����]������Ǥ���褦�ˤ���
		window.parent.HSO.lngMonetaryRateCode.disabled = false;

		//�����������������ξ��
		if( (typeof(window.parent.HSO.PCFlg) == "object"   || 
			 typeof(window.parent.HSO.SCFlg) == "object" ) )
		{
			//[�졼�ȥ�����]�Υǥե���Ȥ��TTM�פˤ���
			window.parent.HSO.lngMonetaryRateCode.value = g_strTtmMonetaryRate;
			// �Ƕ�ʬ�������ǡפˤ���
			window.parent.DSO.lngTaxClassCode.value = g_strFreeTaxClass;
	
		}
		//ȯ������ȼ�������ξ��
		else
		{
			//[�졼�ȥ�����]�Υǥե���Ȥ�ּ���졼�ȡפˤ���
			window.parent.HSO.lngMonetaryRateCode.value = g_strOutTaxClass;
		}

		//[�����졼��]���Խ��Ǥ���褦�ˤ���
		window.parent.HSO.curConversionRate.contentEditable = 'true';
	}

	//[����ñ��][�ٻ�ñ��]�򥯥ꥢ
	window.parent.DSO.curProductPrice_gs.value = "" ;
	window.parent.DSO.curProductPrice_ps.value = "" ;

	//��ȴ����פ򥯥ꥢ
	window.parent.DSO.curTotalPrice.value = "" ;

	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		//����̲ߤ򥯥ꥢ
		window.parent.DSO.curStdTotalPrice.value = "" ;
	}
}


// ---------------------------------------------------------------
/**
* ����   : ���ٹԤ��ɲä�������[�̲�][�졼�ȥ�����]������Ǥ��ʤ�����
*           ���ٹԤ��ʤ�����[�̲�][�졼�ȥ�����]������Ǥ���褦�ˤ��롣
*           ��������[�졼�ȥ�����]������Ǥ���Τϡ����ܱ߰ʳ��ΤȤ���
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncHdMonetaryUnitCheck()
{
	if (saveRecord.length == 0)
	{
		//[�̲�]���Խ��Ǥ���褦�ˤ���
		window.parent.HSO.lngMonetaryUnitCode.disabled = false;
	}
	else
	{
		//[�̲�]���Խ��Ǥ��ʤ��褦�ˤ���
		window.parent.HSO.lngMonetaryUnitCode.disabled = true;
	}
}

// ---------------------------------------------------------------
/**
* ����	���졼�ȥ����פξ����Ѳ�
*
* �о�	��
*/
// ---------------------------------------------------------------
function fncHdMonetaryRateCheck()
{
	// [�졼�ȥ�����]��������֤��ѹ�
	if (saveRecord.length == 0)
	{
		// [�̲�]�����ܱߤξ�硢̵�뤹��
		if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign )
		{
			return false;
		}
		// [�졼�ȥ�����]������Ǥ���褦��
		window.parent.HSO.lngMonetaryRateCode.disabled = false;
	}
	else
	{
		// [�졼�ȥ�����]������Ǥ��ʤ��褦��
		window.parent.HSO.lngMonetaryRateCode.disabled = true;
	}
}


// ---------------------------------------------------------------
/**
* ����   : �̲ߤ����򤷤��顢�����졼�Ȥ�ȿ��
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncCalConversionRate()
{
	//[�̲�]�����ܱߤ��ä��顢����󥻥�
	if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign ) return false;

	//[�졼�ȥ�����]�ζ�������򤷤��顢�ּ���פ����򤷤����Ȥˤ���
	if( window.parent.HSO.lngMonetaryRateCode.value == g_strNoneMonetaryRate )
	{
		window.parent.HSO.lngMonetaryRateCode.value = g_strDefMonetaryRate;
	}

	//[�׾���]�����ξ��ˡ����ߤ����դ���ȿ��
	if( window.parent.HSO.dtmOrderAppDate.value == "" )
	{
		window.parent.HSO.dtmOrderAppDate.value = fncYYMMDD();
	}

	//[�����졼��]��[�졼�ȥ�����][�̲�][�׾���]���Ȥ�ȿ��
	subLoadMasterValue('cnConversionRate',
					 window.parent.HSO.lngMonetaryRateCode,
					 window.parent.HSO.curConversionRate,
					 Array(window.parent.HSO.lngMonetaryRateCode.value,
						   window.parent.HSO.lngMonetaryUnitCode.value,
						   window.parent.HSO.dtmOrderAppDate.value),
						   window.document.objDataSourceSetting);
}


// ---------------------------------------------------------------
/**
* ����    �� �ե�����DSO���Ǥ��Ф��줿hidden�ͤ��������ȤΥǡ��������
*            Detail�Υ��֤򲡤����Ȥ��˼¹Ԥ����
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtHtml()
{

	//�̲ߵ��������
	window.parent.fncCheckNumberCurrencySign( window.parent.HSO.lngMonetaryUnitCode.value );

	//�����������������ξ��
	if( typeof(window.parent.HSO.PCFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		//[�׾���]�����ξ��ˡ����ߤ����դ���ȿ��
		if( window.parent.HSO.dtmOrderAppDate.value == "" )
		{
			window.parent.HSO.dtmOrderAppDate.value = fncYYMMDD();
		}

		//[�ǥ�����]��[�׾���]���Ȥ�ȿ��
		subLoadMasterValue('cnTaxCode',
						 null,
						 window.parent.DSO.TaxCode,
						 Array(window.parent.HSO.dtmOrderAppDate.value),
						 window.document.objDataSourceSetting );

		//[��Ψ]��[�׾���]���Ȥ�ȿ��
		subLoadMasterValue('cnTaxCodeValue',
						 null,
						 window.parent.DSO.zeiritsu,
						 Array(window.parent.HSO.dtmOrderAppDate.value),
						 window.document.objDataSourceSetting14,
						 14 );
	}
	//���������ʳ��ΤȤ�
	else
	{
	//���֤򲡤����Ȥ��ΤĤŤ�
	//���������ξ��ˤϡ�subLoadMasterValue('cnTaxCodeValue',...)��fncDtHtmlForPC()�Τ��ȼ»�
	fncDtHtml2();
	}
	
	//alert(window.parent.DSO.TaxCode.value);
}


// ---------------------------------------------------------------
/**
* ����    �� �ե�����DSO���Ǥ��Ф��줿hidden�ͤ��������ȤΥǡ���������ĤŤ�
*            Detail�Υ��֤򲡤����Ȥ��˼¹Ԥ����
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtHtml2()
{
	//�̲ߴ���Ʒ׻�
	fncDtCalStdTotalPrice();

	//
	if( g_lngReturnFlg == -1 || typeof(window.parent.DSO.elements("aryPoDitail[0][strProductCode]")) == "undefined" ) return null;

	//�롼�פν����
	var i = 0;

	while (window.parent.DSO.elements("aryPoDitail[" + i + "][strProductCode]") != null)
	{
		//�ե�����DSO����äƤ���hidden�ͤ򿷵�������˳�Ǽ
		var aryRecord = fncDtNewAryForReturn(i);

		//����˳�Ǽ
		saveRecord.push(aryRecord);

		i++;
	}

	//�����򤹤�Τϰ��٤����Τ��ᡢ�ե饰������
	g_lngReturnFlg = -1;

	//�����Ȥ��ɽ��
	fncDtDisplay();//
}


// ---------------------------------------------------------------
/**
* ����    : Detail���֤򤪤����Ȥ��ν���
* �о�    : ����������������
* ����    : �ٱ䤬�����뤿�ᡢ���֥��ɴؿ��ν���������ä����Ȥ˽�������
*/
// ---------------------------------------------------------------
function fncDtHtmlForPC()
{
	//�ǥ����ɤ򥰥��Х��ѿ��˳�Ǽ
	// g_lngTaxClassCode = window.parent.DSO.zeicode.value;
	g_lngTaxCode = window.parent.DSO.TaxCode.value;

	//��Ψ�򥰥��Х��ѿ��˳�Ǽ
	g_curTax = window.parent.fncCheckNumberValue(window.parent.DSO.zeiritsu.value, 3, false);
	//alert("�ǥ����ɡ�"+g_lngTaxCode+"��Ψ��"+g_curTax);

	// Added by Kazushi Saito
	// �׻���ˡ���̤μ���
	if( typeof(window.parent.DSO.lngCalcCode) != "undefined" )
	{
		g_lngCalcCode = window.parent.DSO.lngCalcCode.value;
	}
	// Added by Kazushi Saito
	// �������ʲ��ν������
	g_lngDecimalCutPoint = 2;
	// ���ܱߤξ�硢�������ʲ��ν���������ѹ�
	if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign )
	{
		g_lngDecimalCutPoint = 0;
	}

	//���֤򲡤��Τ����ǤϤʤ������ٹԤ�����Ȥ�
	//������Ψ���ѹ����줿��ǽ�������뤿����Ψ���Ƕ��Ʒ׻�
	if( g_lngReturnFlg == -1 && saveRecord.length > 0 )
	{
		for( i=0 ; i < saveRecord.length ; i++ )
		{
			//�����Ƕ�ʬ�����ɤ�����ǰʳ��ΤȤ�
			if( saveRecord[i][15] != g_strFreeTaxClass )
			{
				saveRecord[i][16] = g_curTax;
				saveRecord[i][17] = fncDtCalTaxPrice(saveRecord[i][11], saveRecord[i][15]);

				// �ǳۤΥե����ޥå�
				saveRecord[i][17]   = window.parent.fncCheckNumberValue(saveRecord[i][17], 2, true, g_lngDecimalCutPoint, g_lngCalcCode);

			}
		}
	}
	//�����Ȥ��Ƕ��Ʒ׻�
	fncDtCalTaxPrice2();

	//���֤򲡤����Ȥ��ΤĤŤ�
	fncDtHtml2();
}


// ---------------------------------------------------------------
/**
* ����    �� ñ���ꥹ�Ȥ�ɽ��
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtGoodsPriceList()
{
	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		//���ʥ����ɤ����򤵤�Ƥ��ʤ���С���λ
		if( window.parent.DSO.strProductCode.value == ""            ||
			isNaN(window.parent.DSO.strProductCode.value)           ||
			window.parent.DSO.strStockSubjectCode.value       == 0  ||
			window.parent.DSO.strStockItemCode.selectedIndex  == 0  ||
			window.parent.DSO.strStockItemCode.selectedIndex  == -1 ||
			isNaN(window.parent.DSO.strStockItemCode.value)         ) return false;
	
		subLoadMasterOption( "cnProductPrice",
			 window.parent.DSO.strStockItemCode, 
			 window.parent.DSO.lngGoodsPriceCode,
			 Array(window.parent.DSO.strProductCode.value,
				   window.parent.DSO.strStockSubjectCode.value,
				   window.parent.DSO.strStockItemCode.value,
				   window.parent.HSO.lngMonetaryUnitCode.value),
			 window.document.objDataSourceSetting11,11);
	}

	//����������������ξ��
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		//���ʥ����ɡ�����ʬ�����򤵤�Ƥ��ʤ���С���λ
		if( window.parent.DSO.strProductCode.value == ""            ||
			isNaN(window.parent.DSO.strProductCode.value)           ||
			window.parent.DSO.lngSalesClassCode.value == 0 ) return false;
	
		subLoadMasterOption( "cnProductPriceForSO",
			 window.parent.DSO.lngSalesClassCode, 
			 window.parent.DSO.lngGoodsPriceCode,
			 Array(window.parent.DSO.strProductCode.value,
				   window.parent.DSO.lngSalesClassCode.value,
				   window.parent.HSO.lngMonetaryUnitCode.value),
			 window.document.objDataSourceSetting11,
			 11);
	}

}


// ---------------------------------------------------------------
/**
* ����    �� ñ���ꥹ�Ȥ�ɽ��(���ٹԤ����򤷤����)
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtGoodsPriceList2()
{
	//ȯ����������������ξ��
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		//���ʥ����ɤ����򤵤�Ƥ��ʤ���С���λ
		if (saveRecord[g_lngSelIndex][0] == ""           ||
			saveRecord[g_lngSelIndex][2]       == 0 ||
			saveRecord[g_lngSelIndex][4]  == -1 ) return false;
	
		subLoadMasterOption( "cnProductPrice",
			 window.parent.DSO.strStockItemCode, 
			 window.parent.DSO.lngGoodsPriceCode,
			 Array(saveRecord[g_lngSelIndex][0],
				   saveRecord[g_lngSelIndex][2],
				   saveRecord[g_lngSelIndex][4],
				   window.parent.HSO.lngMonetaryUnitCode.value),
			 window.document.objDataSourceSetting12,12);
	}

	//����������������ξ��
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		//���ʥ����ɡ�����ʬ�����򤵤�Ƥ��ʤ���С���λ
		if( saveRecord[g_lngSelIndex][0] == ""            ||
			saveRecord[g_lngSelIndex][19] == 0 ) return false;
	
		subLoadMasterOption( "cnProductPriceForSO",
			 window.parent.DSO.lngSalesClassCode, 
			 window.parent.DSO.lngGoodsPriceCode,
			 Array(saveRecord[g_lngSelIndex][0],
				   saveRecord[g_lngSelIndex][19],
				   window.parent.HSO.lngMonetaryUnitCode.value),
			 window.document.objDataSourceSetting12,
			 12);
	}
}


// ---------------------------------------------------------------
/**
* ����    �� ñ���ꥹ�Ȥ����򤷤��顢����ñ����ȿ��
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtGoodsPriceToProductPrice()
{
	//ñ���ꥹ�Ȥ��ʤ��ä��顢EXIT
	if( window.parent.DSO.lngGoodsPriceCode.selectedIndex == -1 ) return false;

	//�ٻ�ñ�̷׾�ξ�硢EXIT
	if( window.parent.DSO.lngConversionClassCode[1].checked ) return false;

	//ñ���ꥹ�Ȥ��ͤ����
	var GoodsPrice = window.parent.DSO.lngGoodsPriceCode[window.parent.DSO.lngGoodsPriceCode.selectedIndex].text;

	//(No Data)���ä����
	if( isNaN(GoodsPrice) )
	{
		window.parent.DSO.curProductPrice_gs.value = 0;
	}

	//�ͤ�������
	else
	{
		window.parent.DSO.curProductPrice_gs.value = GoodsPrice;
	}

	//����ñ����ե����ޥåȤ���
	window.parent.fncCheckNumber(window.parent.DSO.curProductPrice_gs, 4);

	//��ȴ��ۤ�Ʒ׻�
	fncDtCalTotalPrice();
}


// ---------------------------------------------------------------
/**
// ����    �� [ñ���ꥹ��]�ɲåǡ����Υ����å�
// �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncProductPriceForList()
{
	//����ñ��
	var productPrice_gs = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value));

	//�ٻ�ñ��
	var productPrice_ps = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value));

	//�����ȥ�����
	var cartonQuantity  = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);

	//�����ȥ���̤�0�ޤ��϶��ΤȤ�ñ���ꥹ�Ȥ���Ͽ���ʤ�
	if( cartonQuantity == "" || cartonQuantity == 0 )
	{
		productPriceForList = "";
	}
	//�ٻ�ñ���५���ȥ�����������ñ�����������Ȥ�ñ���ꥹ�Ȥ���Ͽ����
	else if( (productPrice_ps / cartonQuantity) == productPrice_gs )
	{
		productPriceForList = window.parent.DSO.curProductPrice_gs.value;
	}
	//����¾�ξ�硢ñ���ꥹ�Ȥ���Ͽ���ʤ�
	else
	{
		productPriceForList = "";
	}

	return productPriceForList;
}


// ---------------------------------------------------------------
/**
* ����       : �ǳۤη׻�
* �о�       : ����������������
* @param     : zeinuki,  [string��], ��ȴ���
*             TaxClassCode,  [int��]   , �ǥ�����
* @return    : str, [string��], �ǳ�
*/
// ---------------------------------------------------------------
function fncDtCalTaxPrice(zeinuki, TaxClassCode)
{
	var str="";

	//����ǰʳ��ǰ��������٤Ƥ�������ǳۤ�׻�

	if (zeinuki != "" && TaxClassCode != 1)
	{
		//��ȴ��פ��饫��ޤ��̲ߵ������
		str = fncDelCurrencySign(fncDelKannma(zeinuki));

		//�Ƕ�ʬ�����ǤΤȤ�
		if (TaxClassCode == 2 )
		{
			str = str * g_curTax;
		}
		//�Ƕ�ʬ�����ǤΤȤ�
		else if (TaxClassCode == 3 )
		{
			str = (str * g_curTax)/(1 + parseFloat(g_curTax));
		}

		//�ǳۤ���ƥե����ޥå�
		str = window.parent.fncCheckNumberValue(str, 2);

	}

	return str;
}


// ---------------------------------------------------------------
/**
* ����   : �ǳۤη׻�����[�Ƕ�ʬ]���ѹ������Ȥ���
* �о�   : ����������������
* ���   : ����������Х��顼�Ȥ�Ф�
* @param   : object, [object��], �Ƕ�ʬ
*/
// ---------------------------------------------------------------
function fncDtCalTaxPrice2(object)
{
	
	//[��ȴ���]
	var zeinuki  = window.parent.DSO.curTotalPrice.value;

	//[�Ƕ�ʬ]
	g_lngTaxClassCode  = window.parent.DSO.lngTaxClassCode.value;

	// Added by Kazushi Saito
	// �׻���ˡ���̤μ���
	if( typeof(window.parent.DSO.lngCalcCode) != "undefined" )
	{
		g_lngCalcCode = window.parent.DSO.lngCalcCode.value;
	}

	//����ǰʳ��ǰ��������٤Ƥ�������ǳۤ�׻�
	if (zeinuki != "" && g_curTax != "" )
	{
		//��ȴ��פ��饫��ޤ��̲ߵ������
		var str = fncDelCurrencySign(fncDelKannma(zeinuki));

		//�Ƕ�ʬ������ǤΤȤ�
		if (g_lngTaxClassCode == 1 )
		{
			window.parent.DSO.lngTaxCode.value  = ""; //��Ψ
			window.parent.DSO.curTaxPrice.value = ""; //�ǳ�
		}
		//�Ƕ�ʬ�����ǤΤȤ�
		else if (g_lngTaxClassCode == 2 )
		{
			window.parent.DSO.lngTaxCode.value  = g_curTax; //��Ψ
			str = str * g_curTax;
			window.parent.DSO.curTaxPrice.value = window.parent.fncCheckNumberValue(str, 2); //�ǳ�
		}
		//�Ƕ�ʬ�����ǤΤȤ�
		else if (g_lngTaxClassCode == 3 )
		{
			window.parent.DSO.lngTaxCode.value  = g_curTax; //��Ψ
			str = (str * g_curTax)/(1 + parseFloat(g_curTax));
			window.parent.DSO.curTaxPrice.value = window.parent.fncCheckNumberValue(str, 2); //�ǳ�
		}

		// Added by Kazushi Saito
		// �������ʲ��ν������
		g_lngDecimalCutPoint = 2;
		// ���ܱߤξ�硢�������ʲ��ν���������ѹ�
		if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign )
		{
			g_lngDecimalCutPoint = 0;
		}

		// Added by Kazushi Saito
		// ������(SCFlg)
		// ��������(PCFlg)
		//�����׶�ۡפ򾮿����ʲ����ڼΤƽ�����2��0����
		if( typeof(window.parent.HSO.SCFlg) == "object" ||
			typeof(window.parent.HSO.PCFlg) == "object")
		{
			
			// ���׶��
			window.parent.DSO.curTaxPrice.value   = window.parent.fncCheckNumberValue(window.parent.DSO.curTaxPrice.value, 2, true, g_lngDecimalCutPoint, g_lngCalcCode);
			// ��ȴ��ۤκƷ׻�
			fncDtCalTotalPrice();
		}

	}
	//����̲ߤη׻�
	fncDtCalStdTotalPrice();

	//�Ƕ�ʬ���ѹ����줿�Ȥ��ˤϷٹ��Ф��ʰ�����������Τߡ�
	if( typeof(object) != "undefined" )
	{
		alert("�Ƕ�ʬ���ѹ�����ޤ���");
	}
}


// ---------------------------------------------------------------
/**
* ����     : �������ܤ����򤷤��顢�Ƕ�ʬ����Ψ�����
* �о�     : ��������
* @param   : object, [object��], ��������
*/
// ---------------------------------------------------------------
function fncDtTaxClassCode( object )
{
	//[��������]����402 ͢���ѡ��Ļ�����ס�433 �ⷿ�������Ѥξ��פ�
	//�Ƕ�ʬ�������ǡפ�����
	if (object.value == "402" || object.value == "433")
	{
		window.parent.DSO.lngTaxClassCode.value = 1 ; //�����Ƕ�ʬ������
		window.parent.DSO.lngTaxCode.value      ="" ; //������Ψ
		window.parent.DSO.curTaxPrice.value     ="" ; //�����ǳ�
	}
	//�嵭�ʳ��ξ�硢���Ǥ�����
	else
	{
		window.parent.DSO.lngTaxClassCode.value = 2;        //�����Ƕ�ʬ������
		window.parent.DSO.lngTaxCode.value      = g_curTax; //��Ψ
		window.parent.DSO.curTaxPrice.value     = fncDtCalTaxPrice(window.parent.DSO.curTotalPrice.value,
																window.parent.DSO.lngTaxClassCode.value); //�����ǳ�
	}
}


// ---------------------------------------------------------------
/**
* ����     : ���ʤ�MSW�����ͤ���������Ȥ��ν���
* �о�     : ���ʤ�MSW��������
* @param   : strProductCode, [str��], ���ʥ�����
*/
// ---------------------------------------------------------------
function fncDtProductCodeForMSW(strProductCode)
{

	//���ʤ��顢����̾�����
	subLoadMasterValue('cnProduct',
			 window.parent.DSO.strProductCode,
			 window.parent.DSO.strProductName,
			 Array(strProductCode),
			 window.document.objDataSourceSetting,
			 0);

	//���ʤ��顢�ܵ����֤����
	subLoadMasterValue('cnGoodsCode',
			 window.parent.DSO.strProductCode,
			 window.parent.DSO.strGoodsCode,
			 Array(strProductCode),
			 window.document.objDataSourceSetting1,
			 1);
	//���ʤ��顢�����ȥ����������
	subLoadMasterValue('cnCartonQuantity',
			 window.parent.DSO.strProductCode,
			 window.parent.DSO.lngCartonQuantity,
			 Array(strProductCode),
			 window.document.objDataSourceSetting15,
			 15);

	//ñ���ꥹ�Ȥ����
	fncDtGoodsPriceList();
	fncDtGoodsPriceToProductPrice();
}


// ---------------------------------------------------------------
/**
* ����     : ���ʽ������̤�ƤӽФ������Ȥ����ʴ�Ϣ���ܤ�Ƽ�������
* �о�     : ȯ�����, �������
* @param   : strProductCode, [str��], ���ʥ�����
*/
// ---------------------------------------------------------------
function fncDtProductCodeForP(strProductCode)
{

	//���ʤ��顢����̾�����
	subLoadMasterValue('cnProduct',
			 window.parent.DSO.strProductCode,
			 window.parent.DSO.strProductName,
			 Array(strProductCode),
			 window.document.objDataSourceSetting,
			 0);

	//���ʤ��顢�ܵ����֤����
	subLoadMasterValue('cnGoodsCode',
			 window.parent.DSO.strProductCode,
			 window.parent.DSO.strGoodsCode,
			 Array(strProductCode),
			 window.document.objDataSourceSetting1,
			 1);
	//���ʤ��顢�����ȥ����������
	subLoadMasterValue('cnCartonQuantity',
			 window.parent.DSO.strProductCode,
			 window.parent.DSO.lngCartonQuantity,
			 Array(strProductCode),
			 window.document.objDataSourceSetting15,
			 15);

	//��ȴ��ۤ�Ʒ׻�([���ʿ���]��[�����ȥ�����]��[�ٻѿ���]��ȿ�Ǥ����뤿��)
	fncDtCalTotalPrice();
}


// ---------------------------------------------------------------
/**
* ����     : ���ʽ������̤�ƤӽФ����ѹ����줿���Ϣ�������ܤ�Ƽ���
* �о�     : ȯ�����, �������
* @param   : strSessionID,    [string��], ���å����ID
* @param   : lngLanguageCode, [string��], ���쥳����
*/
// ---------------------------------------------------------------
function fncShowDialogRenewCheck( strSessionID , lngLanguageCode)
{
	//�����Ȥ�[���ʥ�����]
	var ProductCode = window.parent.trim( window.parent.DSO.strProductCode.value );

	//[���ʥ�����]�����Ϥ���Ƥ��ʤ���硢������λ
	if( ProductCode == "" )
	{
		var strComment = ( lngLanguageCode == "0" ) ? "Please specify the product." : "���ʤ���ꤷ�Ƥ���������";

		alert( strComment );

		return null;
	}

	args    = new Array();
	args[0] = new Array();

	var strUrl = "/p/regist/renew.php?strProductCode=" + ProductCode + "&strSessionID=" + strSessionID ;

	args[0][0] = strUrl;               // �¹���URL
	args[0][1] = 'ResultIframeRenew';  //IFrame�Υ���������ID
	args[0][2] = 'NO';                 // IFrame��������ε��ġ��Ե���
	args[0][3] = lngLanguageCode;      // $lngLanguageCode

	retval = window.showModalDialog( '/result/renew.html' , args , "dialogHeight:600px;dialogWidth:970px;center:yes;status:no;edge:raised;help:no;" );

	if( typeof(retval) != "undefined" )
	{
		//���ʾ����Ϣ��Ƽ���
		fncDtProductCodeForP(ProductCode );
		alert("���ʾ��󤬹�������ޤ���");
	}
}


// ---------------------------------------------------------------
/**
* ����     : ����ޤ�Ȥ�
* �о�     : ���٤�
* @param   : num, [string��], ����ޤ��ꤿ����
* @return  : str, [string��], ����ޤ����������
*/
// ---------------------------------------------------------------
function fncDelKannma( num )
{

	var str = num.replace(/,/g,"");

	return str;
}


// ---------------------------------------------------------------
/**
* ����     : ����ޤ��դ���
* �о�     : ���٤�
* @param   : num, [string��], ����ޤ��դ�������
* @return  : str, [string��], ������դ���
*/
// ---------------------------------------------------------------
function fncAddKannma(num)
{

	var str = num.toString();
	var tmpStr = "";

	while( str != (tmpStr = str.replace(/^([+-]?\d+)(\d\d\d)/,"$1,$2")) )
	{
		str = tmpStr;
	}

	return str;
}


// ---------------------------------------------------------------
/**
* ����    : �̲ߵ������ʶ��򤫤餢�Ȥ���ʬ��ȴ���Ф���
* �о�    : ���٤�
* @param  : num, [string��], �̲ߵ�����ꤿ����  (�� \ 1,000.0000)
* @return : str, [string��], �̲ߵ�������������(��   1,000.0000)
*/
// ---------------------------------------------------------------
function fncDelCurrencySign(num)
{
	var i;
	var str;
	str = num.toString();

	if( str != "" )
	{
		if(str.indexOf(' ') != -1){
		i   = str.indexOf(' ');
		str = str.substring(i+1,str.length);
		}
	}

	return str;
}


// ---------------------------------------------------------------
/**
* ����    : �̲ߵ�����դ���
* �о�    : ���٤�
* @param  : num, [string��], �̲ߵ�����դ������� (��   1,000.0000)
* @return : str, [string��], �̲ߵ�����դ�����   (�� \ 1,000.0000)
*/
// ---------------------------------------------------------------
function fncAddCurrencySign(num)
{
	var str = num.toString();
	var CurrencySign = window.parent.HSO.lngMonetaryUnitCode.value;

	//����ʳ��ξ����̲ߵ����Ĥ���
	if( str != "" )
	{
		str = CurrencySign + " " + str;
	}

	return str;
}


// ---------------------------------------------------------------
/**
* ����    : ���������դ����֤�
* �о�    : ���٤�
* @return : YYYYMMDD, [string��], YYYY/MM/DD
*/
// ---------------------------------------------------------------
function fncYYMMDD()
{
	var strDate = new Date();
	var strYY = strDate.getYear();
	var strMM = strDate.getMonth() + 1;
	var strDD = strDate.getDate();

	if (strYY < 2000) { strYY += 1900; }
	if (strMM < 10) { strMM = "0" + strMM; }
	if (strDD < 10) { strDD = "0" + strDD; }

	var YYMMDD = strYY + "/" + strMM + "/" + strDD;

	return YYMMDD;
}


// ---------------------------------------------------------------
/**
* ����    : �ü�ʸ�����Ѵ�
* �о�    : ���٤�
* @return : �Ѵ����줿ʸ����
*/
// ---------------------------------------------------------------
function fncCheckReplaceString( strInString )
{
	strValue = strInString;

	strValue = strValue.replace( /&/g ,"&amp;" );
	strValue = strValue.replace( /\"/g ,"&quot;" );
	strValue = strValue.replace( /</g , "&lt;" );
	strValue = strValue.replace( />/g , "&gt;" );
	strValue = strValue.replace( /,/g , "&#44;" );
	strValue = strValue.replace( /\'/g , "&#39;" );
	strValue = strValue.replace( /\r\n/g , "\n" );
	strValue = strValue.replace( /\r/g , "\n" );
//	strValue = strValue.replace( / /g , "&nbsp;" );
	strValue = strValue.replace( /\n/g , "<br>" );

	return strValue;
}


// ---------------------------------------------------------------
/**
* ����    : �ü�ʸ���Ѵ����줿ʸ������Ȥ������ͤ��᤹
* �о�    : ���٤�
* @return : ��Ȥ�������
*/
// ---------------------------------------------------------------
function fncCheckReplaceStringBack( strInString )
{

	strValue = strInString;

	strValue = strValue.replace( /&amp;/g ,"&" );
	strValue = strValue.replace( /&quot;/g ,"\"" );
	strValue = strValue.replace( /&lt;/g , "<" );
	strValue = strValue.replace( /&gt;/g , ">" );
	strValue = strValue.replace( /&#44;/g , "," );
	strValue = strValue.replace( /&#39;/g , "\'" );
//	strValue = strValue.replace( /&nbsp;/g , " " );
	strValue = strValue.replace( /<br>/g , "\n" );

	return strValue;
}





// �о��ͺ�����
function fncSetCheckValue()
{
	// ���
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		// ľ��Ͽ�ǤϤʤ����
		if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
		{
			for( var i = 0; i < saveRecord.length; i++ )
			{
				saveRecord[i][27] = eval( "document.all.blnOffset" + i ).value;
			}
		}
	}

	// ����
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		// ľ��Ͽ�ǤϤʤ����
		if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
		{
			for( var i = 0; i < saveRecord.length; i++ )
			{
				saveRecord[i][25] = eval( "document.all.blnOffset" + i ).value;
			}
		}
	}
}

// �����å��ܥå�������
function fncSetCheck( obj, i )
{
	var objHidden = eval( "document.all.blnOffset" + i );
	var strValue  = objHidden.value;

	var imgOff    = '/img/type01/cmn/check_off.gif';
	var imgOn     = '/img/type01/cmn/check_on.gif';


	// ���
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		// ľ��Ͽ�ǤϤʤ����
		if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
		{
			if( strValue == '0' )
			{
				obj.src         = imgOn;
				objHidden.value = '1';

				saveRecord[i][27] = 1;
			}
			else
			{
				obj.src         = imgOff;
				objHidden.value = '0';

				saveRecord[i][27] = 0;
			}
		}
	}

	// ����
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		// ľ��Ͽ�ǤϤʤ����
		if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
		{
			if( strValue == '0' )
			{
				obj.src         = imgOn;
				objHidden.value = '1';

				saveRecord[i][25] = 1;
			}
			else
			{
				obj.src         = imgOff;
				objHidden.value = '0';

				saveRecord[i][25] = 0;
			}
		}
	}

	// ���׶�ۤκƷ׻�
	fncDtCalAllTotalPrice();
}





// ���ٹԥ����å�
// ���ٹ���1�쥳���ɤȡ�����ʹߤΥ쥳���ɤ���Ӥ���
function fncCheckDetailRecords( saveRecord )
{
	var i;
	var blnCheck       = false;
	var strCodeRecord  = saveRecord[0][0];	// ���ٹ����ʥ�����
	var strClassRecord = saveRecord[0][19];	// ���ٹԷ׾��ʬ


	//��������ξ��
	if( typeof( window.parent.HSO.SOFlg ) == "object" )
	{
		for( i=0; i<saveRecord.length; i++ )
		{
			blnCheck = fncCheckTargetDetail( strCodeRecord, strClassRecord, saveRecord[i][0], saveRecord[i][19] );

			if( !blnCheck ) break;
		}
	}
	// ����¾�δ���
	else
	{
		blnCheck = true;
	}

	return blnCheck;
}

// ���ʥ����ɡ��׾��ʬ�������å�
function fncCheckDetailCode( saveRecord )
{
	var blnCheck       = false;
	var strPCode       = '';	// ���ʥ�����
	var strPClass      = '';	// �׾��ʬ
	var strCodeRecord  = '';	// ���ٹ����ʥ�����
	var strClassRecord = '';	// ���ٹԷ׾��ʬ

	// ���ٹԤ�1�԰ʾ�¸�ߤ�����
	if( typeof( saveRecord[0] ) != 'undefined' )
	{
		if( g_lngSelIndex != 0 )
		{
			// ���ʥ����ɤμ���
			strPCode = window.parent.trim( window.parent.DSO.strProductCode.value );

			// ���ٹ����ʥ����ɤμ���
			strCodeRecord = saveRecord[0][0];


			//��������ξ��
			if( typeof( window.parent.HSO.SOFlg ) == "object" )
			{
				strPClass      = window.parent.DSO.lngSalesClassCode.value;
				strClassRecord = saveRecord[0][19];

				// �оݥ����ɤ����
				blnCheck = fncCheckTargetDetail( strCodeRecord, strClassRecord, strPCode, strPClass );
			}
			// ����¾�δ���
			else
			{
				//blnCheck = ( strCodeRecord == strPCode ) ? true : false;
				blnCheck = true;
			}
		}
		else
		{
			blnCheck = true;
		}
	}
	else
	{
		blnCheck = true;
	}


	return blnCheck;
}

// ������� �оݥ����ɤ����
function fncCheckTargetDetail( pRecord, cRecord, pcode, pclass )
{
	var blnCheck = false;

	if( pRecord == pcode && cRecord == pclass )
	{
		blnCheck = true;
	}
	else
	{
		blnCheck = false;
	}

	return blnCheck;
}


//-->
