<!--
//: ----------------------------------------------------------------------------
//: �ե����복��: �����Ȥ�����ؿ���
//: ����        : ����������
//: ������      : 2003/11/06 �� 
//: ������      : Takafumi Tetsuka
//: ��������    : 
//: ----------------------------------------------------------------------------


//------------------------------------------------------------------------------
// �����Х��ѿ����
//------------------------------------------------------------------------------
var saveRecord = new Array();  //���ٹԤ�ñ�̤Ȥ�������
var index      = -1;           //����Ԥ��Ǽ�����ѿ�
var returnFlg  = 1 ;           //Detail�Υ��֤򲡤����Ȥ��ˡ���Ͽ����꤬���ä���ɽ��
var sentakufunouFlg = 0;       //���ٹ�����ν����������ޤǤۤ��������Ǥ��ʤ�����


//@*****************************************************************************
// ����   : �������ȡפˡ������ȡפ����Ƥ��ɲ�
// �о�   : �������ȡפ������Τ��٤�
// ����   : �������ȡפ��ͤ������aryRecord�פ˳�Ǽ��������������saveRecord�פ˳�Ǽ��
//           ���ΤȤ������ٹԤ����򤵤�Ƥ���С����򤵤줿����ξ���ɲä�������Ƥ��ʤ���С�
//           �Ǹ������ɲá�
//           ���θ塢�ؿ���fncDtDisplay()�פ�ƤӽФ��������Ȥ��ɽ����
//******************************************************************************
function fncDtAddRecord()
{
	//���ϥǡ����Υ����å�
	var addFlg = fncDtAddCheck();

	if( addFlg == false ) return false;

	//�����Ȥ��ͤ򿷵�������˳�Ǽ
	var aryRecord = fncDtNewAry();

	//���ٹԤ����򤵤�Ƥ��ʤ����
	if ( index == -1)
	{
		//�����Х�����κǸ���ɲ�
		saveRecord.push(aryRecord);
	}
	//���ٹԤ����򤵤�Ƥ�����
	else
	{
		//���򤵤줿����ξ�ˡ�������������ɲä���
		saveRecordLength = parseInt(saveRecord.length); 
		saveRecordLeft  = saveRecord.slice(0,index);
		saveRecordRigft = saveRecord.slice(index, saveRecordLength);
		saveRecord      = saveRecordLeft;
		saveRecord.push(aryRecord);
		saveRecord      = saveRecord.concat(saveRecordRigft);

		//����ǥå���������
		index      = -1;
	}

	//�����Ȥ��ɽ��
	fncDtDisplay();

	//�إå����̲�������ѹ��Ǥ��ʤ��褦�ˤ���
	fncHdMonetaryUnitCheck();
}

//@*****************************************************************************
// ����   : ���򤷤��Ԥ���
// �о�   : �������ȡפ������Τ��٤�
// ����   : ����Ԥ����������������
//          ���θ塢�ؿ���fncDtDisplay()�פ�ƤӽФ��������Ȥ��ɽ����
// ���   : �Ԥ����򤵤�Ƥ��ʤ����ˤϡ����顼��å���������ϡ�
//******************************************************************************
function fncDtDelRecord()
{
	//���ٹԤ����򤵤�Ƥ�����
	if( index != -1 )
	{
		saveRecordLength = parseInt(saveRecord.length);

		saveRecordLeft  = saveRecord.slice(0, index);
		saveRecordRigft = saveRecord.slice(index + 1, saveRecordLength);
		saveRecord      = new Array();
		saveRecord      = saveRecord.concat(saveRecordLeft, saveRecordRigft);

		index = -1;

		//�����Ȥ��ɽ��
		fncDtDisplay();
	}
	//���ٹԤ����򤵤�Ƥ��ʤ����
	else
	{
		alert("���ٹԤ����򤷤Ƥ�������");
	}

	//���ٹԤ��ʤ���硢�إå����̲�������ѹ���ǽ�ˤ���
	fncHdMonetaryUnitCheck();
}


//@*****************************************************************************
// ����   : ���ꥢ�ܥ��󤬲����줿�Ȥ��˽���
// �о�   : �������ȡפ������Τ��٤�
// ����   : 
//******************************************************************************
function fncDtClearRecord()
{
	//�������ʤ򥯥ꥢ
	window.parent.DSO.strStockItemCode.length = 0;

	//ñ���ꥹ�Ȥ򥯥ꥢ
	window.parent.DSO.lngGoodsPriceCode.length = 0;

	//���ٹԤ�����Ǥ���褦�ˤ���
	sentakufunouFlg = 0;

	fncDtGsChecked();
}


//@*****************************************************************************
// ����   : �����Ȥ��ͤ�����Ԥ��֤�������
// �о�   : �������ȡפ������Τ��٤�
// ����   : �����Ȥ��ͤ�����Ԥ��֤������롣
//          ���θ塢�ؿ���fncDtDisplay()�פ�ƤӽФ��������Ȥ��ɽ����
// ���    :�Ԥ����򤵤�Ƥ��ʤ����ˤϡ��إå���ʬ�˥��顼��å����������
//******************************************************************************
function fncDtCommitRecord()
{
	//���ٹԤ����򤵤�Ƥ�����
	if( index != -1)
	{
		//�����Ȥ��ͤ�����Ԥ��֤�����
		fncDtReplaceAry();

		//����ǥå���������
		index = -1;

		//�����Ȥ��ɽ��
		fncDtDisplay();
	}
	//���ٹԤ����򤵤�Ƥ��ʤ����
	else
	{
		alert("���ٹԤ����򤵤�Ƥ��ޤ���");
	}
}


//@*****************************************************************************
// ����   : �����Ȥ��ɽ��
// �о�   : �������ȡפ������Τ��٤�
// ����   : �����saveRecord�פ��顢�����ȤΥơ��֥���������ɽ��
//******************************************************************************
function fncDtDisplay()
{
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

	//��¸�ΰ����������ľ���������˽񤭴�����
	document.all.DetailList.innerHTML = strTableHtml;

	//���׶�ۤη׻�
	fncDtCalAllTotalPrice();

	//���ٹԤ�����Ǥ���褦�ˤ���
	sentakufunouFlg = 0;
}


//@*****************************************************************************
// ����   : ���ٹԤ�������ν���
// �о�   : �������ȡפ������Τ��٤�
// ����   : ���ٹԤ����򤹤�Ȥ��ˡ����Ǥ����򤵤�Ƥ���Ԥ����ä����ϡ����ιԤ�ȿž�������롣
//          ��¸�����򤵤�Ƥ���Ԥ�⤦���ٲ��������ˤϡ�index���������롣
//          ����ʳ��ξ��ˤϡ�����Ԥ��ͤ������Ȥ�ȿ�Ǥ����롣
// ���   : ���ٹԤ����򤵤�Ƥ�����ǡ�����Ԥ��ѹ����褦�Ȥ������ˤϡ������Ȥ��ѹ����ʤ��������å�����
//          �ѹ�������С���å���������ϡ�
//******************************************************************************
function fncDtSentaku(i)
{
	//¾�����ٹԤν���������äƤʤ���С����򤵤��ʤ�
	if( sentakufunouFlg == 1 )
	{
		return null;
	}
	else
	{
		//������Υե饰��Ω�Ƥ�
		//(���ߤϻ������ʤν���������ä��Ȥ��˲�����Ƥ���)
		sentakufunouFlg = 1;
	}

	//�����ȤΥ����å��ե饰
	res = true;

	//���ٹԤ����򤵤�Ƥ�����
	if( index != -1 )
	{
		//�����Ȥ��ѹ����ʤ��������å�
		res = fncDtCheck();
	}

	//�����Ȥ��ѹ����ʤ����⤷��������Ԥ��ѹ����Ƥ�����Τʤ����
	if(res == true)
	{
		//��¸������Ԥ����ä����ˤϡ����ιԤ�ȿž����
		if( index != -1 )
		{
			document.getElementById("retsu" + index).style.backgroundColor="#ffffff";
		}

		//����������Ԥ�⤦���٥���å��������
		if (index == i)
		{
			//����ǥå���������
			index = -1;
			//���ٹԤ�����Ǥ���褦�ˤ���
			sentakufunouFlg = 0;
		}
		//�����Ȱ㤦����Ԥ򥯥�å��������
		else
		{
			//����ǥå���������Ԥ������ֹ�򥻥å�
			index = i;

			//������ԡפ�ȿž������
			document.getElementById("retsu" + index).style.backgroundColor="#bbbbbb";

			//�����Ȥ򤹤٤ƥ��ꥢ�ʶ��ԤΤȤ��Τ����
			window.parent.fncResetFrm( window.parent.DSO );
			//�������ʤ򥯥ꥢ�ʶ��ԤΤȤ��Τ����
			window.parent.DSO.strStockItemCode.length  = 0;
			//ñ���ꥹ�Ȥ򥯥ꥢ�ʶ��ԤΤȤ��Τ����
			window.parent.DSO.lngGoodsPriceCode.length = 0;

			if( saveRecord[index][0] != "" )
			{
				//���ʤ��顢����̾�����
				subLoadMasterValue('cnProduct',
						 saveRecord[index][0],
						 window.parent.DSO.strProductName,
						 Array(saveRecord[index][0]),
						 window.document.objDataSourceSetting,
						 0);
				//���ʤ��顢�ܵ����֤����
				subLoadMasterValue('cnGoodsCode',
						 saveRecord[index][0],
						 window.parent.DSO.strGoodsCode,
						 Array(saveRecord[index][0]),
						 window.document.objDataSourceSetting1,
						 1);
				//���ʤ��顢�����ȥ����������
				subLoadMasterValue('cnCartonQuantity',
						 saveRecord[index][0],
						 window.parent.DSO.lngCartonQuantity,
						 Array(saveRecord[index][0]),
						 window.document.objDataSourceSetting13,
						 13);
				//�������ܤ��顢�������ʤΥ��ץ�����ͤ����
				subLoadMasterOption( 'cnStockItem',
						 window.parent.DSO.strStockSubjectCode, 
						 window.parent.DSO.strStockItemCode,
						 Array(saveRecord[index][2]),
						 window.document.objDataSourceSetting10,
						 10);
				//ñ���ꥹ�Ȥ����
				fncDtGoodsPriceList2();
			}
			//����Ԥλ������ٹԤ�����Ǥ���褦�ˤ���
			else
			{
				//���ٹԤ�����Ǥ���褦�ˤ���
				sentakufunouFlg = 0;
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
		//���ٹԤ�����Ǥ���褦�ˤ���
		sentakufunouFlg = 0;
	}
}


//@*****************************************************************************
// ����   : �����Ȥ�����Ԥκ��ۤ�����å������㤤������С���ǧ����������ɽ��
// �о�   : �������ȡפ������Τ��٤�
// ����   : 
// ����� : [Boolean��] ����Ԥ��ư���Ƥ�褤���ϡ�true����ư���ʤ����ϡ�false
// ����   : 
//******************************************************************************
function fncDtCheck()
{
	//�����å��ե饰
	var res = true;

	//�������ȡפ��ͤ�����˥��å�
	var aryRecord = fncDtNewAry();

	//�����Ĺ��
	var aryRecordLength = aryRecord.length;

	for( j = 0; j < aryRecordLength ; j++ )
	{
		//�����Ȥ�����Ԥ����
		if( aryRecord[j] != saveRecord[index][j] )
		{
			//ñ���ꥹ��,��������̾,��������̾,ñ�̡�̾�Ρ�,
			//ñ���ɲåꥹ��,���ֹ�ΤȤ������å�
			if (j==1 || j == 3 || j == 5 || j == 9 ||j == 14 || j==18) continue;

//�ǥХå��� ��Ǿä�
//alert("�ѹ����줿�����ֹ� : " + j + "\n" +
//	  "�������ȡפ��� : " + aryRecord[j] + "\n" +
//	  "�����ٹԡפ��� : " + saveRecord[index][j]);

			res = confirm("�ѹ��ս꤬����ޤ����ѹ����ʤ��Ƥ������Ǥ�����")
			break;
		}
	}
	return res;

}


//@*****************************************************************************
// ����   : �ɲåܥ���򲡤����Ȥ����ͤΥ����å�
// �о�   : �������ȡפ������Τ��٤�
// ���   : ���꤬����Х��顼�Ȥ�Ф�
//******************************************************************************
function fncDtAddCheck()
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
	//�������ܤ����򤵤�ʤ��ä����
	if( window.parent.DSO.strStockSubjectCode.value       == 0 )
	{
		alertList += "�������ܤ����򤷤Ƥ�������!\n";
	}
	//�������ʤ����򤵤�ʤ��ä����
	if( window.parent.DSO.strStockItemCode.selectedIndex  == -1 ||
		window.parent.DSO.strStockItemCode.selectedIndex  == 0  )
	{
		alertList += "�������ʤ����򤷤Ƥ�������!\n";
	}

	//����ñ�̷׾夬���򤵤�Ƥ�����
	if (window.parent.DSO.lngConversionClassCode[0].checked)
	{
		//����ñ�������Ϥ���Ƥ��ʤ��ä����
		if( window.parent.DSO.curProductPrice_gs.value == "" ||
			window.parent.DSO.curProductPrice_gs.value == 0  )
		{
			alertList += "����ñ�������Ϥ��Ƥ�������!\n";
		}
		//���ʿ��̤����Ϥ���Ƥ��ʤ��ä����
		if( window.parent.DSO.lngGoodsQuantity_gs.value == "" ||
			window.parent.DSO.lngGoodsQuantity_gs.value == 0  )
		{
			alertList += "���ʿ��̤����Ϥ��Ƥ�������!\n";
		}
		//����ñ�����ͤ��������ä����
		if( isNaN(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value))) )
		{
			alertList += "����ñ�����ͤ������Ǥ�!\n";
		}
		//���ʿ��̤��ͤ��������ä����
		if( isNaN(fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value)) )
		{
			alertList += "���ʿ��̤��ͤ������Ǥ�!\n";
		}
	}
	//�ٻ�ñ�̷׾夬���򤵤�Ƥ�����
	else if( window.parent.DSO.lngConversionClassCode[1].checked )
	{
		//�ٻ�ñ�������Ϥ���Ƥ��ʤ��ä����
		if( window.parent.DSO.curProductPrice_ps.value == "" ||
			window.parent.DSO.curProductPrice_ps.value == 0  )
		{
			alertList += "����ñ�������Ϥ��Ƥ�������!\n";
		}
		//�ٻѿ��̤����Ϥ���Ƥ��ʤ��ä����
		if( window.parent.DSO.lngGoodsQuantity_ps.value == "" ||
			window.parent.DSO.lngGoodsQuantity_ps.value == 0  )
		{
			alertList += "���ʿ��̤����Ϥ��Ƥ�������!\n";
		}
		//�ٻ�ñ�����ͤ��������ä����
		if( isNaN(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value))) )
		{
			alertList += "����ñ�����ͤ������Ǥ�!\n";
		}
		//�ٻѿ��̤��ͤ��������ä����
		if( isNaN(fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value)) )
		{
			alertList += "���ʿ��̤��ͤ������Ǥ�!\n";
		}
	}

	//���顼�����ä����å����������
	if( alertList != "" )
	{
		alert(alertList);
		return false;
	}

	return true;
}


//@*****************************************************************************
// ����   : �����Ȥ��ͤ򿷵�������˳�Ǽ
// �о�   : �������ȡפ������Τ��٤�
// ����� : aryRecord, [����], ����������
//******************************************************************************
function fncDtNewAry()
{
	var aryRecord = new Array();

	aryRecord[0]  = window.parent.DSO.strProductCode.value;            //���ʥ�����
	aryRecord[1]  = window.parent.DSO.lngGoodsPriceCode.value;         //ñ���ꥹ��
	aryRecord[2]  = window.parent.DSO.strStockSubjectCode.value;       //��������
	aryRecord[3]  = window.parent.DSO.strStockSubjectCode.options[window.parent.DSO.strStockSubjectCode.selectedIndex].text;     //�������ܡ�value + ̾�Ρ�
	aryRecord[4]  = window.parent.DSO.strStockItemCode.value;          //��������
	if( window.parent.DSO.strStockItemCode.selectedIndex != -1 )
	{
	aryRecord[5]  = window.parent.DSO.strStockItemCode.options[window.parent.DSO.strStockItemCode.selectedIndex].text;           //�������ʡ�value + ̾�Ρ�
	}else{
	aryRecord[5]  = "";
	}

	if( window.parent.DSO.lngConversionClassCode[0].checked )
	{
	aryRecord[6]  = window.parent.DSO.lngConversionClassCode[0].value; //������ʬ(����ñ�̷׾�)
	aryRecord[7]  = window.parent.DSO.curProductPrice_gs.value;        //����ñ��
	aryRecord[8]  = window.parent.DSO.lngProductUnitCode_gs.value;     //����ñ��
	aryRecord[9]  = window.parent.DSO.lngProductUnitCode_gs.options[window.parent.DSO.lngProductUnitCode_gs.selectedIndex].text; //����ñ�̡�̾�Ρ�
	aryRecord[10] = window.parent.DSO.lngGoodsQuantity_gs.value;       //���ʿ���
	aryRecord[14] = window.parent.DSO.curProductPrice_gs.value;        //ñ���ꥹ���ɲåǡ���
	}
	else if( window.parent.DSO.lngConversionClassCode[1].checked )
	{
	aryRecord[6]  = window.parent.DSO.lngConversionClassCode[1].value; //������ʬ(�ٻ�ñ�̷׾�)
	aryRecord[7]  = window.parent.DSO.curProductPrice_ps.value;        //�ٻ�ñ��
	aryRecord[8]  = window.parent.DSO.lngProductUnitCode_ps.value;     //�ٻ�ñ��
	aryRecord[9]  = window.parent.DSO.lngProductUnitCode_ps.options[window.parent.DSO.lngProductUnitCode_ps.selectedIndex].text; //�ٻ�ñ�̡�̾�Ρ�
	aryRecord[10] = window.parent.DSO.lngGoodsQuantity_ps.value;       //�ٻѿ���
	aryRecord[14] = fncProductPriceForList();                          //ñ���ꥹ���ɲåǡ���
	}
	aryRecord[11] = window.parent.DSO.curTotalPrice.value;             //��ȴ���
	aryRecord[12] = window.parent.DSO.lngCarrierCode.value;            //������ˡ
	aryRecord[13] = window.parent.DSO.strDetailNote.value;             //����

	//���������ξ��
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
	aryRecord[15] = window.parent.DSO.lngTaxClassCode.value;             //�����Ƕ�ʬ������
	aryRecord[16] = window.parent.DSO.lngTaxCode.value;                  //������
	aryRecord[17] = window.parent.DSO.curTaxPrice.value;                 //�����ǳ�
	aryRecord[18] = "";            //���ֹ�
	}

	return aryRecord;
}


//@*****************************************************************************
// ����   : �����Ȥ��ͤ�����Ԥ��֤�����
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncDtReplaceAry()
{
	saveRecord[index][0]  = window.parent.DSO.strProductCode.value;         //���ʥ�����
	saveRecord[index][1]  = window.parent.DSO.lngGoodsPriceCode.value;      //ñ���ꥹ��
	saveRecord[index][2]  = window.parent.DSO.strStockSubjectCode.value;    //��������
	saveRecord[index][3]  = window.parent.DSO.strStockSubjectCode.options[window.parent.DSO.strStockSubjectCode.selectedIndex].text;     //�������ܡ�value + ̾�Ρ�
	saveRecord[index][4]  = window.parent.DSO.strStockItemCode.value;       //��������
	saveRecord[index][5]  = window.parent.DSO.strStockItemCode.options[window.parent.DSO.strStockItemCode.selectedIndex].text;           //�������ʡ�value + ̾�Ρ�

	if( window.parent.DSO.lngConversionClassCode[0].checked )
	{
	saveRecord[index][6]  = window.parent.DSO.lngConversionClassCode[0].value; //������ʬ(����ñ�̷׾�)
	saveRecord[index][7]  = window.parent.DSO.curProductPrice_gs.value;        //����ñ��
	saveRecord[index][8]  = window.parent.DSO.lngProductUnitCode_gs.value;     //����ñ��
	saveRecord[index][9]  = window.parent.DSO.lngProductUnitCode_gs.options[window.parent.DSO.lngProductUnitCode_gs.selectedIndex].text;     //����ñ�̡�̾�Ρ�
	saveRecord[index][10] = window.parent.DSO.lngGoodsQuantity_gs.value;       //���ʿ���
	saveRecord[index][14] = window.parent.DSO.curProductPrice_gs.value;        //ñ���ꥹ���ɲåǡ���
	}
	else if(window.parent.DSO.lngConversionClassCode[1].checked )
	{
	saveRecord[index][6]  =  window.parent.DSO.lngConversionClassCode[1].value; //������ʬ(�ٻ�ñ�̷׾�)
	saveRecord[index][7]  = window.parent.DSO.curProductPrice_ps.value;         //�ٻ�ñ��
	saveRecord[index][8]  = window.parent.DSO.lngProductUnitCode_ps.value;      //�ٻ�ñ��
	saveRecord[index][9]  = window.parent.DSO.lngProductUnitCode_ps.options[window.parent.DSO.lngProductUnitCode_ps.selectedIndex].text;     //�ٻ�ñ�̡�̾�Ρ�
	saveRecord[index][10] = window.parent.DSO.lngGoodsQuantity_ps.value;        //�ٻѿ���
	saveRecord[index][14] = fncProductPriceForList();                           //ñ���ꥹ���ɲåǡ���
	} 

	saveRecord[index][11] = window.parent.DSO.curTotalPrice.value;          //��ȴ���
	saveRecord[index][12] = window.parent.DSO.lngCarrierCode.value;         //������ˡ
	saveRecord[index][13] = window.parent.DSO.strDetailNote.value;          //����

	//���������ξ��
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
	saveRecord[index][15] = window.parent.DSO.lngTaxClassCode.value;         //�����Ƕ�ʬ������
	saveRecord[index][16] = window.parent.DSO.lngTaxCode.value;              //������
	saveRecord[index][17] = window.parent.DSO.curTaxPrice.value;             //�����ǳ�
	}
}


//@*****************************************************************************
// ����   : �����Ȥ�����Ԥ�ȿ��
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncDtReplaceInput()
{
	window.parent.DSO.strProductCode.value         = saveRecord[index][0];  //���ʥ�����
	//ñ���ꥹ��(saveRecord[index][1])�ϡ�hmtl��ľ�ܽ񤯡��ٱ�Τ����
	window.parent.DSO.strStockSubjectCode.value    = saveRecord[index][2];  //��������
	//��������(saveRecord[index][4])�ϡ�hmtl��ľ�ܽ񤯡��ٱ�Τ����

	if( saveRecord[index][6] == "gs" )
	{
	window.parent.DSO.lngConversionClassCode[0].checked = true;             //������ʬ(����ñ�̷׾�)
	window.parent.DSO.curProductPrice_gs.value     = saveRecord[index][7];  //����ñ��
	window.parent.DSO.lngProductUnitCode_gs.value  = saveRecord[index][8];  //����ñ��
	window.parent.DSO.lngGoodsQuantity_gs.value    = saveRecord[index][10]; //���ʿ���

	//[����ñ��][����ñ��][���ʿ���]�����ϡ�����Ǥ���褦�ˤ���
	window.parent.DSO.curProductPrice_gs.disabled    = false;
	window.parent.DSO.lngProductUnitCode_gs.disabled = false;
	window.parent.DSO.lngGoodsQuantity_gs.disabled   = false;

	//[�ٻ�ñ��][�ٻ�ñ��][�ٻѿ���]�����ϡ�����Ǥ��ʤ��褦�ˤ���
	window.parent.DSO.curProductPrice_ps.disabled    = true;
	window.parent.DSO.lngProductUnitCode_ps.disabled = true;
	window.parent.DSO.lngGoodsQuantity_ps.disabled   = true;
	}
	else if( saveRecord[index][6] == "ps" )
	{
	window.parent.DSO.lngConversionClassCode[1].checked = true;             //������ʬ(�ٻ�ñ�̷׾�)
	window.parent.DSO.curProductPrice_ps.value     = saveRecord[index][7];  //�ٻ�ñ��
	window.parent.DSO.lngProductUnitCode_ps.value  = saveRecord[index][8];  //�ٻ�ñ��
	window.parent.DSO.lngGoodsQuantity_ps.value    = saveRecord[index][10]; //�ٻѿ���
	window.parent.DSO.curProductPrice_gs.value     = saveRecord[index][14]; //ñ���ꥹ���ɲåǡ���

	//[����ñ��][����ñ��][���ʿ���]�����ϡ�����Ǥ��ʤ��褦�ˤ���
	window.parent.DSO.curProductPrice_gs.disabled    = true;
	window.parent.DSO.lngProductUnitCode_gs.disabled = true;
	window.parent.DSO.lngGoodsQuantity_gs.disabled   = true;
	//[�ٻ�ñ��][�ٻ�ñ��][�ٻѿ���]�����ϡ�����Ǥ���褦�ˤ���
	window.parent.DSO.curProductPrice_ps.disabled    = false;
	window.parent.DSO.lngProductUnitCode_ps.disabled = false;
	window.parent.DSO.lngGoodsQuantity_ps.disabled   = false;
	}

	window.parent.DSO.curTotalPrice.value          = saveRecord[index][11]; //��ȴ���
	window.parent.DSO.lngCarrierCode.value         = saveRecord[index][12]; //������ˡ
	window.parent.DSO.strDetailNote.value          = saveRecord[index][13]; //����

	//���������ξ��
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
	window.parent.DSO.lngTaxClassCode.value = saveRecord[index][15];         //�����Ƕ�ʬ������
	window.parent.DSO.lngTaxCode.value      = saveRecord[index][16];         //������
	window.parent.DSO.curTaxPrice.value     = saveRecord[index][17];         //�����ǳ�
	}
}


//@*****************************************************************************
// ����   : �����ȥơ��֥����̾�����
// �о�   : �������ȡפ������Τ��٤�
// ����� : strTableHtml, [String��], �����Ȥ���̾
//******************************************************************************
function fncStrTableHtmlColumns()
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
					  '<td nowrap id="ExStrDL09">Remark</td>'       +
					  '</tr>';
	}

	return strTableHtml;
}


//@*****************************************************************************
// ����   : �����ȥơ��֥�ιԤ����
// �о�   : �������ȡפ������Τ��٤�
// ����� : strTableHtml, [String��], �����Ȥ�����
//******************************************************************************
function fncStrTableHtmlRows(i)
{
	strTableHtml ='<td align="center" nowrap>'      + saveRecord[i][0]  +             //����
				  '</td><td nowrap>'                + saveRecord[i][3]  +             //�������ܡ�̾�Ρ�
				  '</td><td nowrap>'                + saveRecord[i][5]  +             //�������ʡ�̾�Ρ�
				  '</td><td align="right" nowrap>'  + saveRecord[i][7]  + "&nbsp;" +  //ñ��
				  '</td><td align="center" nowrap>' + saveRecord[i][9]  +             //ñ�̡�̾�Ρ�
				  '</td><td align="right" nowrap>'  + saveRecord[i][10]  + "&nbsp;" + //����
				  '</td><td align="right" nowrap>'  + saveRecord[i][11] + "&nbsp;" +  //��ȴ���
				  '</td><td nowrap>'                + saveRecord[i][13] +             //����
				  '</td>'

	return strTableHtml;
}


//@*****************************************************************************
// ����   : ��Ͽ�ܥ���򲡤����Ȥ��ˡ�header��ˡ������ȤΥǡ�����hidden���Ǥ��Ф�
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncDtRegistRecord(){

	var strHiddenHtml = "";

	//hidden���Ǥ��Ф�Ϣ�֡ʶ��Ԥ�������Ƚ��֤�����뤿����ѡ�
	var hiddenNumber = 0 ;

	for( i = 0; i < saveRecord.length; i++ )
	{
		//���ԥ����å�
		if (saveRecord[i][0] == "") continue;

		strHiddenHtml = strHiddenHtml + fncDtHiddenHtml(i, hiddenNumber);
		hiddenNumber++; 
	}

	//��ǧ�롼�Ȥ��ɲá�ȯ������ξ���
	if( typeof(window.parent.HSO.POFlg) == "object" )
	{
		strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngWorkflowOrderCode' value='" + window.parent.DSO.lngWorkflowOrderCode.value + "' >\n" ;
	}

	//ȯ��ΣϤ��ɲ�
	strHiddenHtml = strHiddenHtml + "<input type='hidden' name='strOrderCode' value='" + window.parent.HSO.strOrderCode.value + "' >\n" ;

	//��ӥ���󥳡��ɤ��ɲ�
	strHiddenHtml = strHiddenHtml + "<input type='hidden' name='strReviseCode' value='" + window.parent.HSO.strReviseCode.value + "' >\n" ;

	//�̲ߤ��ɲ�
	strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngMonetaryUnitCode' value='" + window.parent.HSO.lngMonetaryUnitCode.value + "' >\n" ;

	//�졼�ȥ����פ��ɲ�
	strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngMonetaryRateCode' value='" + window.parent.HSO.lngMonetaryRateCode.value + "' >\n" ;

	//�����졼�Ȥ��ɲ�
	strHiddenHtml = strHiddenHtml + "<input type='hidden' name='curConversionRate' value='" + window.parent.HSO.curConversionRate.value + "' >\n" ;

	//��ʧ�����ɲ�
	strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngPayConditionCode' value='" + window.parent.HSO.lngPayConditionCode.value + "' >\n" ;

	//���׶�ۡ���ȴ���ˤ��ɲ�
	strHiddenHtml = strHiddenHtml + "<input type='hidden' name='curAllTotalPrice' value='" + fncDelKannma(fncDelCurrencySign(window.parent.DSO.curAllTotalPrice.value)) + "' >\n" ;

//�ǥХå���
alert(strHiddenHtml);

	//�ե�����(name="HSO")�������ȤΥǡ������Ϥ�
	window.parent.document.all.DtHiddenRecord.innerHTML = strHiddenHtml;


	//�ե�����HSO�򥵥֥ߥå�
	window.parent.document.HSO.submit();
}


//@*****************************************************************************
// ����   : �����Ȥ�hidden���Ǥ��Ф��ǡ��������
// �о�   : �������ȡפ������Τ��٤�
// ����� : strHiddenHtml, [string��], �����Ȥ����Ƥ�hidden���֤��������Ǥ��Ф�
// ����   : 
//******************************************************************************

function fncDtHiddenHtml(i, hiddenNumber){

	//���������ξ��Τߤ�hidden��
	var strPC = "";

	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
	strPC = "<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngTaxClassCode]'  value='" + saveRecord[i][15] + "' >\n" +                                   //�����Ƕ�ʬ������
			"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngTaxCode]'       value='" + saveRecord[i][16] + "' >\n"     +                               //������
			"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curTaxPrice]'      value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][17])) + "' >\n" + //�����ǳ�
			"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngorderdetailno]' value='" + saveRecord[i][18] + "' >\n" ;                                   //���ֹ�
	}

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
					strPC;

	return strHiddenHtml;
}


//@*****************************************************************************
// ����   : �����Τ�����Ǥ��Ф��줿hidden�ͤ������Ͽ�ܥ���򲡤������
//          ��äƤ���hidden�ͤ򿷵�������˳�Ǽ
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncDtNewAryForReturn(i)
{
	var aryRecord = new Array();
	aryRecord[0]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strProductCode]").value;          //���ʥ�����
	aryRecord[1]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngGoodsPriceCode]").value;       //ñ���ꥹ��
	aryRecord[2]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockSubjectCode]").value;     //��������
	aryRecord[3]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockSubjectCodeName]").value; //�������ܡ�value + ̾�Ρ�
	aryRecord[4]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockItemCode]").value;        //��������
	aryRecord[5]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockItemCodeName]").value;    //�������ʡ�value + ̾�Ρ�
	aryRecord[6]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngConversionClassCode]").value;  //������ʬ(����ñ�̷׾�)
	aryRecord[7]  = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][curProductPrice]").value, 4); //ñ��
	aryRecord[8]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngProductUnitCode]").value;      //ñ��
	aryRecord[9]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngProductUnitCodeName]").value;  //ñ�̡�̾�Ρ�
	aryRecord[10]  = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][lngGoodsQuantity]").value, 0, false); //����
	aryRecord[11] = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][curTotalPrice]").value, 2); //��ȴ���
	aryRecord[12] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngCarrierCode]").value;          //������ˡ
	aryRecord[13] = window.parent.DSO.elements("aryPoDitail[" + i + "][strDetailNote]").value;           //����
	aryRecord[14] = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][curProductPriceForList]").value, 4); //ñ���ꥹ���ɲåǡ���

	//���������ξ��
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
	aryRecord[15] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngTaxClassCode]").value;  //�����Ƕ�ʬ������
	aryRecord[16] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngTaxCode]").value;       //������
		if (window.parent.DSO.elements("aryPoDitail[" + i + "][curTaxPrice]").value == "")
		{
		aryRecord[17] = fncDtCalTaxPrice(aryRecord[11],aryRecord[15], aryRecord[16]);
		}else{
		aryRecord[17] = window.parent.DSO.elements("aryPoDitail[" + i + "][curTaxPrice]").value;      //�����ǳ�
		}
	aryRecord[18] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngorderdetailno]").value; //���ֹ�
	}

	return aryRecord;
}


//@*****************************************************************************
// ����   : ��ȴ����פ򻻽Ф���
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncDtCalTotalPrice()
{
 var ProductPrice  = 0; //ñ��
 var GoodsQuantity = 0; //����
 var TotalPrice    = 0; //��ȴ���

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
	TotalPrice    = ProductPrice * GoodsQuantity;
	//��ȴ��ۤ������Ȥ�ȿ��
	window.parent.DSO.curTotalPrice.value = TotalPrice;
	//��ȴ��ۤ�ե����ޥåȤ���
	window.parent.fncCheckNumber( window.parent.DSO.curTotalPrice , 2);

	//���������ξ�硢�ǳۤ�׻�
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		window.parent.DSO.curTaxPrice.value     = fncDtCalTaxPrice(window.parent.DSO.curTotalPrice.value,
																window.parent.DSO.lngTaxClassCode.value,
																window.parent.DSO.lngTaxCode.value); //�����ǳ�
	}

	//����̲ߤ�ɽ��
	fncDtCalStdTotalPrice();
}


//@*****************************************************************************
// ����   : ����ñ�̷׾�Υ饸���ܥ��󤬲����줿�Ȥ��ν���
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncDtGsChecked()
{
	//ñ���ꥹ�Ȥ�����Ǥ���褦�ˤ���
	window.parent.DSO.lngGoodsPriceCode.disabled = false;

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


//@*****************************************************************************
// ����   : �ٻ�ñ�̷׾�Υ饸���ܥ��󤬲����줿�Ȥ��ν���
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncDtPsChecked()
{
	//�ܥ�����ư���Ƥ�褤���ɤ����Υե饰
	var checkFlg = true ;

	//���������ξ��ǡ����ֹ�Τ�����ˡ��ٻ�ñ�̷׾�����򤷤Ƥ�褤���ɤ����Υ����å�
	if( typeof(window.parent.HSO.PCFlg) =="object" && 
			   index != -1 && 
			   saveRecord[index][18] != "" )
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
		window.parent.fncCheckNumber(window.parent.DSO.curProductPrice_ps , 4 );
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


//@*****************************************************************************
//  ����   :�ٻ�ñ�̷׾������Ǥ��뤫�ɤ����Υ����å�
//  �о�   :���������ǹ��ֹ�Τ�����
//******************************************************************************
function fncDtPsCheckedForPC()
{
	//�����ȥ������
	var CartonQuantity   = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);
	//���ʿ���
	var GoodsQuantity_gs = fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value);

	//�����ȥ���̤�0�ޤ��϶��ΤȤ�����Ǥ��ʤ�
	if( CartonQuantity == "" || CartonQuantity == 0 )
	{
		return false;
	}

	//���ʿ��̤ȥ����ȥ���̤�����ڤ�������Ǥ���
	if( (GoodsQuantity_gs % CartonQuantity) == 0 )
	{
		return true;
	}
	else
	{
		return false;
	}
}


//@*****************************************************************************
//  ����   :�ٻѿ��̤����ϥ����å�
//  �о�   :���������ǹ��ֹ�Τ�����ǡ��ٻѿ��̤˥����å�����Ƥ�����
//******************************************************************************
function fncDtPSGoodsQuantityForPC()
{
	//���ֹ椬���ꡢ���򤵤�Ƥ���Ȥ��Τߥ����å�
	if( index != -1 && saveRecord[index][18] != "" )
	{
		//�����ȥ������
		var CartonQuantity   = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);
		//�ǥե���Ȥ����ʿ���
		var GoodsQuantity_gs_defalt = fncDtGSGoodsQuantityDefalt();
		//���Ϥ��줿�˲ٻѿ���
		var GoodsQuantity_ps = fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value);
	
		//�����ȥ���̤�0�ޤ��϶��ΤȤ������å���λ
		if( CartonQuantity == "" || CartonQuantity == 0 )
		{
			return false;
		}
	
		//�ٻѿ��̤����ϤǤ�����
		var GoodsQuantity_ps_max = parseInt(GoodsQuantity_gs_defalt / CartonQuantity) ;
	
		//���Ϥ��줿�ٻѿ��̤���¤�Ķ�������
		if( GoodsQuantity_ps > GoodsQuantity_ps_max )
		{
			//��¤�Ķ����������ͤ򥻥å�
			window.parent.DSO.lngGoodsQuantity_ps.value = fncAddKannma(GoodsQuantity_ps_max);
			//���顼��å����������
			alert("�ٻѿ��̤����ϤǤ����¤ϡ�" + GoodsQuantity_ps_max + "�Ǥ�");
		}
	}
}


//@*****************************************************************************
//  ����   :���ʿ��̤����ϥ����å�
//  �о�   :���������ǹ��ֹ�Τ�����ǡ����ʿ��̤˥����å�����Ƥ�����
//******************************************************************************
function fncDtGSGoodsQuantityForPC()
{
	//���ֹ椬���ꡢ���򤵤�Ƥ���Ȥ��Τߥ����å�
	if( index != -1 && saveRecord[index][18] != "" )
	{
		//�ǥե���Ȥ����ʿ���
		var GoodsQuantity_gs_defalt = fncDtGSGoodsQuantityDefalt();
		//���Ϥ��줿�����ʿ���
		var GoodsQuantity_gs = fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value);
	
		//���Ϥ��줿���ʿ��̤��ǥե�����ͤ�Ķ�������
		if( GoodsQuantity_gs > GoodsQuantity_gs_defalt )
		{
			//��¤�Ķ����������ͤ򥻥å�
			window.parent.DSO.lngGoodsQuantity_gs.value = fncAddKannma(GoodsQuantity_gs_defalt);
			//���顼��å����������
			alert("���ʿ��̤����ϤǤ����¤ϡ�" + GoodsQuantity_gs_defalt + "�Ǥ�");
		}
	}
}


//@*****************************************************************************
//  ����   :���ֹ椫��ǥե���Ȥ����ʿ��̤�����
//  �о�   :���������ǹ��ֹ�Τ�����
//******************************************************************************
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
		if( window.parent.DSO.elements("aryPoDitail[" + i + "][lngorderdetailno]").value == saveRecord[index][18] )
		{
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
		i++;
	}

	return GoodsQuantity_gs_defalt;
}


//@*****************************************************************************
// ����   : ���׶�ۤ򻻽Ф���
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
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
		AllTotalPrice += parseInt(10000 * fncDelKannma(fncDelCurrencySign(saveRecord[i][11])));

	}
		AllTotalPrice = AllTotalPrice / 10000 ;

	//���׶�ۤ������Ȥ�ȿ��
	window.parent.DSO.curAllTotalPrice.value = AllTotalPrice;
	//���׶�ۤ�ե����ޥåȤ���
	window.parent.fncCheckNumber( window.parent.DSO.curAllTotalPrice , 2 );
	}
}


//@*****************************************************************************
// ����   : ����̲ߤ򻻽Ф���
// �о�   : �������ȡפ������Τ��̲ߤ����ܱ߰ʳ�
//******************************************************************************
function fncDtCalStdTotalPrice()
{
	//���ܱߤΤȤ���ɽ�������ʤ�
	if( window.parent.HSO.lngMonetaryUnitCode.value == "\\" )
	{
		//[����̲�]�����ˤ���
		window.parent.DSO.curStdTotalPrice.value = "";
		return false;
	}

	//[��ȴ���]�����
	var TotalPrice = parseFloat(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curTotalPrice.value)));

	//[�����졼��]�����
	var ConversionRate = fncDelKannma(fncDelCurrencySign(window.parent.HSO.curConversionRate.value));

	//[����̲�]����ƥե����ޥå�
	var StdTotalPrice = window.parent.fncCheckNumberValue(TotalPrice * ConversionRate, 2 ,false);

	//[����̲�]�˱ߥޡ�����Ĥ���
	window.parent.DSO.curStdTotalPrice.value = "\\ " + StdTotalPrice;

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
			window.parent.DSO.curSubTaxPrice.value = "\\ " + SubTaxPrice ; //[�ǳ�]
			//[��׶��]=[��ȴ����]+[�����ǳ�]
			var TotalStdAmt = window.parent.fncCheckNumberValue(((TotalPrice + TaxPrice) * ConversionRate), 2 ,false);
			window.parent.DSO.curTotalStdAmt.value = "\\ " + TotalStdAmt ; //[��׶��]
		}
		//���Ǥξ��
		else if( window.parent.DSO.lngTaxClassCode.value == 3 )
		{
			//[�ǳ�]=[�����ǳ�]��[�����졼��]
			var SubTaxPrice = window.parent.fncCheckNumberValue(TaxPrice * ConversionRate, 2 ,false);
			window.parent.DSO.curSubTaxPrice.value = "\\ " + SubTaxPrice ;                   //[�ǳ�]
			//[��׶��]=����̲ߤ�Ʊ��
			window.parent.DSO.curTotalStdAmt.value = window.parent.DSO.curStdTotalPrice.value ; //[��׶��]
		}
	}
}


//@*****************************************************************************
// ����   : [�̲�]�������ѹ��ˤ�����
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncHdMonetaryUnitCode()
{
	//���ܱߤξ��	
	if( window.parent.HSO.lngMonetaryUnitCode.value == "\\" )
	{
		//[�졼�ȥ�����]������Ǥ��ʤ��褦�ˤ���
		window.parent.HSO.lngMonetaryRateCode.disabled = true;
		//[��ʧ���]������Ǥ��ʤ��褦�ˤ���
		window.parent.HSO.lngPayConditionCode.disabled = true;

		//[�졼�ȥ�����]�����ˤ���
		window.parent.HSO.lngMonetaryRateCode.value = "0";

		//[�����졼��]�򥯥ꥢ����
		window.parent.HSO.curConversionRate.value = "1.000000";

		//[��ʧ���]��̤��ˤ���
		window.parent.HSO.lngPayConditionCode.value = "0";

	}
	//���ܱ߰ʳ��ξ��
	else
	{
		//[�졼�ȥ�����]������Ǥ���褦�ˤ���
		window.parent.HSO.lngMonetaryRateCode.disabled = false;

		//[�졼�ȥ�����]�Υǥե���Ȥ�ּ���졼�Ȥˤ���פˤ���
		window.parent.HSO.lngMonetaryRateCode.value = "2";

		//[��ʧ���]������Ǥ���褦�ˤ���
		window.parent.HSO.lngPayConditionCode.disabled = false;

	}


	//[����ñ��][�ٻ�ñ��]�򥯥ꥢ
	window.parent.DSO.curProductPrice_gs.value = "" ;
	window.parent.DSO.curProductPrice_ps.value = "" ;

	//��ȴ����פ򥯥ꥢ
	window.parent.DSO.curTotalPrice.value = "" ;

	//����̲ߤ򥯥ꥢ
	window.parent.DSO.curStdTotalPrice.value = "" ;

}


//@*****************************************************************************
// ����   : ���ٹԤ��ɲä�������[�̲�][�졼�ȥ�����]������Ǥ��ʤ�����
//           ���ٹԤ��ʤ�����[�̲�][�졼�ȥ�����]������Ǥ���褦�ˤ��롣
//           ��������[�졼�ȥ�����]������Ǥ���Τϡ����ܱ߰ʳ��ΤȤ���
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncHdMonetaryUnitCheck()
{
	if (saveRecord.length == 0)
	{
		//[�̲�]������Ǥ���褦�ˤ���
		window.parent.HSO.lngMonetaryUnitCode.disabled = false;
		//[�졼�ȥ�����]������Ǥ���褦�ˤ���
		window.parent.HSO.lngMonetaryRateCode.disabled = false;
	}
	else
	{
		//[�̲�]������Ǥ��ʤ��褦�ˤ���
		window.parent.HSO.lngMonetaryUnitCode.disabled = true;
		//[�졼�ȥ�����]������Ǥ��ʤ��褦�ˤ���
		window.parent.HSO.lngMonetaryRateCode.disabled = true;
	}
}


//@*****************************************************************************
// ����   : �̲ߤ����򤷤��顢�����졼�Ȥ�ȿ��
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncCalConversionRate()
{
	//[�̲�]�����ܱߤ��ä��顢����󥻥�
	if( window.parent.HSO.lngMonetaryUnitCode.value == "\\" ) return false;

	//[�졼�ȥ�����]�ζ�������򤷤��顢�Ұ������򤷤����Ȥˤ���
	if( window.parent.HSO.lngMonetaryRateCode.value == "0" )
	{
		window.parent.HSO.lngMonetaryRateCode.value = "2";
	}

	//[�׾���]�����ξ��ˡ����ߤ����դ���ȿ��
	if( window.parent.HSO.dtmOrderAppDate.value == "" )
	{
		window.parent.HSO.dtmOrderAppDate.value = fncYYMMDD();
	}

	//[�����졼��]��[�졼�ȥ�����][�̲�][�׾���]���Ȥ�ȿ��
	subLoadMasterValue(23,
					 window.parent.HSO.lngMonetaryRateCode,
					 window.parent.HSO.curConversionRate,
					 Array(window.parent.HSO.lngMonetaryRateCode.value,
						   window.parent.HSO.lngMonetaryUnitCode.value,
						   window.parent.HSO.dtmOrderAppDate.value),
						   window.document.objDataSourceSetting);
}


//@*****************************************************************************
// ����    �� �ե�����DSO���Ǥ��Ф��줿hidden�ͤ��������ȤΥǡ��������
//            Detail�Υ��֤򲡤����Ȥ��˼¹Ԥ����
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncDtHtml()
{
	//�̲ߴ���Ʒ׻�
	fncDtCalStdTotalPrice();

	//
	if( returnFlg == -1 || typeof(window.parent.DSO.elements("aryPoDitail[0][strProductCode]")) == "undefined" ) return null;

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
	returnFlg = -1;

	//�����Ȥ��ɽ��
	fncDtDisplay();//
}


//@*****************************************************************************
// ����    �� ñ���ꥹ�Ȥ�ɽ��
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncDtGoodsPriceList()
{
	//���ʥ����ɤ����򤵤�Ƥ��ʤ���С���λ
	if( window.parent.DSO.strProductCode.value == ""            ||
		isNaN(window.parent.DSO.strProductCode.value)           ||
		window.parent.DSO.strStockSubjectCode.value       == 0  ||
		window.parent.DSO.strStockItemCode.selectedIndex  == 0  ||
		window.parent.DSO.strStockItemCode.selectedIndex  == -1 ||
		isNaN(window.parent.DSO.strStockItemCode.value)         ) return false;

	subLoadMasterOption( 26,
		 window.parent.DSO.strStockItemCode, 
		 window.parent.DSO.lngGoodsPriceCode,
		 Array(window.parent.DSO.strProductCode.value,
			   window.parent.DSO.strStockSubjectCode.value,
			   window.parent.DSO.strStockItemCode.value,
			   window.parent.HSO.lngMonetaryUnitCode.value),
		 window.document.objDataSourceSetting11,11);

}


//@*****************************************************************************
// ����    �� ñ���ꥹ�Ȥ�ɽ��(���ٹԤ����򤷤����)
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncDtGoodsPriceList2()
{
	//���ʥ����ɤ����򤵤�Ƥ��ʤ���С���λ
	if (saveRecord[index][0] == ""           ||
		saveRecord[index][2]       == 0 ||
		saveRecord[index][4]  == -1 ) return false;

	subLoadMasterOption( 26,
		 window.parent.DSO.strStockItemCode, 
		 window.parent.DSO.lngGoodsPriceCode,
		 Array(saveRecord[index][0],
			   saveRecord[index][2],
			   saveRecord[index][4],
			   window.parent.HSO.lngMonetaryUnitCode.value),
		 window.document.objDataSourceSetting12,12);
}


//@*****************************************************************************
// ����    �� ñ���ꥹ�Ȥ����򤷤��顢����ñ����ȿ��
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncDtGoodsPriceToProductPrice()
{
	//ñ���ꥹ�Ȥ��ʤ��ä��顢EXIT
	if( window.parent.DSO.lngGoodsPriceCode.selectedIndex == -1 ) return false;

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


//@*****************************************************************************
// ����    �� [ñ���ꥹ��]�ɲåǡ����Υ����å�
// �о�   : �������ȡפ������Τ��٤�
//******************************************************************************
function fncProductPriceForList()
{
	//����ñ��
	var productPrice_gs = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value));

	//�ٻ�ñ��
	var productPrice_ps = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value));

	//�����ȥ�����
	var cartonQuantity  = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);

	//
	var productPriceForList = productPrice_ps / cartonQuantity;


	if (productPrice_gs != productPriceForList)
	{
		//����ñ���Ȳٻ�ñ��/�����ȥ����� ���㤦���
		productPriceForList = "";
	}
	else
	{
		productPriceForList = window.parent.DSO.curProductPrice_gs.value;
	}

	return productPriceForList;
}


//@*****************************************************************************
// ����      : �ǳۤη׻�
// �о�      : ��������
// ����      : zeinuki,  [string��], ��ȴ���
//             zeicode,  [int��]   , �ǥ�����
//             zeiritsu, [int��]   , ������Ψ
// �����    : str, [string��], �ǳ�
//******************************************************************************
function fncDtCalTaxPrice(zeinuki, zeicode, zeiritsu)
{
	var str="";

	//����ǰʳ��ǰ��������٤Ƥ�������ǳۤ�׻�

	if (zeinuki != "" && zeicode != 1 && zeiritsu != "" )
	{
		//��ȴ��פ��饫��ޤ��̲ߵ������
		str = fncDelCurrencySign(fncDelKannma(zeinuki));

		//�Ƕ�ʬ�����ǤΤȤ�
		if (zeicode == 2 )
		{
			str = str * zeiritsu;
		}
		//�Ƕ�ʬ�����ǤΤȤ�
		else if (zeicode == 3 )
		{
			str = (str * zeiritsu)/(1 + parseFloat(zeiritsu));
		}

		//�ǳۤ���ƥե����ޥå�
		str = window.parent.fncCheckNumberValue(str, 2);

	}

	return str;
}


//@*****************************************************************************
// ����   : �ǳۤη׻�����[�Ƕ�ʬ]���ѹ������Ȥ���
// �о�   : ��������
// ����   : object, [object��], �Ƕ�ʬ
// ���   : �Ƕ�ʬ���ѹ������Ȥ��ˤϥ��顼�Ȥ�Ф�
//******************************************************************************
function fncDtCalTaxPrice2(object)
{
	//[��ȴ���]
	var zeinuki  = window.parent.DSO.curTotalPrice.value;
	//[�Ƕ�ʬ]
	var zeicode  = object.value;


	//����ǰʳ��ǰ��������٤Ƥ�������ǳۤ�׻�
	if (zeinuki != "" && zeiritsu != "" )
	{
		//��ȴ��פ��饫��ޤ��̲ߵ������
		var str = fncDelCurrencySign(fncDelKannma(zeinuki));

		//[��Ψ]
		var zeiritsu = 0.05; //�׽�����DB����������褦�˽������뤳�ȡ�

		//�Ƕ�ʬ������ǤΤȤ�
		if (zeicode == 1 )
		{
			window.parent.DSO.lngTaxCode.value  = ""; //��Ψ
			window.parent.DSO.curTaxPrice.value = ""; //�ǳ�
		}
		//�Ƕ�ʬ�����ǤΤȤ�
		else if (zeicode == 2 )
		{
			window.parent.DSO.lngTaxCode.value  = zeiritsu; //��Ψ
			str = str * zeiritsu;
			window.parent.DSO.curTaxPrice.value = window.parent.fncCheckNumberValue(str, 2); //�ǳ�
		}
		//�Ƕ�ʬ�����ǤΤȤ�
		else if (zeicode == 3 )
		{
			window.parent.DSO.lngTaxCode.value  = zeiritsu; //��Ψ
			str = (str * zeiritsu)/(1 + zeiritsu);
			window.parent.DSO.curTaxPrice.value = window.parent.fncCheckNumberValue(str, 2); //�ǳ�
		}
	}
	//�Ƕ�ʬ���ѹ����줿�Ȥ��ˤϷٹ��Ф���
	alert("�Ƕ�ʬ���ѹ�����ޤ���");
}


//@*****************************************************************************
// ����   : �������ܤ����򤷤��顢�Ƕ�ʬ����Ψ�����
// ����   : object, [object��], ��������
// �о�   : ��������
//******************************************************************************
function fncDtTaxClassCode(object)
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
		window.parent.DSO.lngTaxClassCode.value = 2;    //�����Ƕ�ʬ������
		window.parent.DSO.lngTaxCode.value      = 0.05; //������Ψ				�׽�����DB����������褦�˽������뤳�ȡ�
		window.parent.DSO.curTaxPrice.value     = fncDtCalTaxPrice(window.parent.DSO.curTotalPrice.value,
																window.parent.DSO.lngTaxClassCode.value,
																window.parent.DSO.lngTaxCode.value); //�����ǳ�
	}
}


//@*****************************************************************************
// ����   : ����ޤ�Ȥ�
// �о�   : ���٤�
// ����   : num, [string��], ����ޤ��ꤿ����
// ����� : str, [string��], ����ޤ����������
//******************************************************************************
function fncDelKannma(num)
{
	var i = 0;;
	var str = num.toString();

	while( str.indexOf(',',i) != -1 )
	{
		i = str.indexOf(',',i);
		str = "" + str.substring(0,i) + str.substring(i+1,str.length);
	}
	return str;
}


//@*****************************************************************************
// ����   : ����ޤ��դ���
// �о�   : ���٤�
// ����   : num, [string��], ����ޤ��դ�������
// ����� : str, [string��], ������դ���
//******************************************************************************
function fncAddKannma(num)
{
	var i;
	var max;

	var str = num.toString();
	//�������ΰ���
	var strTen = str.indexOf('.');
	//�����������
	var strSeisuu = str.substring(0,strTen);
	//����������
	var strShousuu = str.substring(strTen,str.length);

	max = Math.floor(strSeisuu.length/3);
	for( i=max; i>0 ;i-- )
	{
		if( strSeisuu.length-3*i != 0 )
		{
			strSeisuu = "" + strSeisuu.substring(0,strSeisuu.length-3*i) + ',' + strSeisuu.substring(strSeisuu.length-3*i,strSeisuu.length)
		}
	}
	str = strSeisuu + strShousuu;
	return str;
}


//@*****************************************************************************
// ����   : �̲ߵ������ʶ��򤫤餢�Ȥ���ʬ��ȴ���Ф���
// �о�   : ���٤�
// ����   : num, [string��], �̲ߵ�����ꤿ����  (�� \ 1,000.0000)
// ����� : str, [string��], �̲ߵ�������������(��   1,000.0000)
//******************************************************************************
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


//@*****************************************************************************
// ����   : �̲ߵ�����դ���
// �о�   : ���٤�
// ����   : num, [string��], �̲ߵ�����դ������� (��   1,000.0000)
// ����� : str, [string��], �̲ߵ�����դ�����   (�� \ 1,000.0000)
//******************************************************************************
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


//@*****************************************************************************
// ����   : ���������դ����֤�
// �о�   : ���٤�
// ����� : YYYYMMDD, [string��], YYYY/MM/DD
//******************************************************************************
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


//@*****************************************************************************
// ����   : YYYY/MM/DD �� YYYY/MM ���֤�
// ����   : objObject, [object��], YYYY/MM/DD���ͤ����Ϥ���Ƥ��륪�֥�������
// �о�   : ���ʴ���
//******************************************************************************
function fncYYMM(objObject)
{
	strDate = new Date(objObject.value);

	var strYY = strDate.getFullYear();
	var strMM = strDate.getMonth() + 1;

	if (strMM < 10) { strMM = "0" + strMM; }

	objObject.value = strYY + "/" + strMM;
}

//-->
