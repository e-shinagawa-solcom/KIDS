<!--

//------------------------------------------------------------------------------
// �����Х��ѿ����
//------------------------------------------------------------------------------
var g_saveRecord = new Array(8);	// ��������ñ�̤Ȥ�������
g_saveRecord[0]  = new Array();		// ���������ⷿ���ѹ�
g_saveRecord[1]  = new Array();		// ���������ⷿ�������ѹ�
g_saveRecord[2]  = new Array();		// �������������ġ��������
g_saveRecord[3]  = new Array();		// ��������͢���ѡ��Ļ�����
g_saveRecord[4]  = new Array();		// �������������ѡ��Ļ�����
g_saveRecord[5]  = new Array();		// ������������ù���
g_saveRecord[6]  = new Array();		// �������������㡼��
g_saveRecord[7]  = new Array();		// ��������������

var g_displayNO                               // ���ߺ�Ȥ��Ƥ����������ֹ�(g_saveRecord��ź��)

var g_strStockSubjectCodeName = new Array(8); // ���ߺ�Ȥ��Ƥ���������̾�Ρʻ������ܥ����ɡܻ�������̾��
g_strStockSubjectCodeName[0] = "431  �ⷿ���ѹ�";
g_strStockSubjectCodeName[1] = "433  �ⷿ�������ѹ�" ;
g_strStockSubjectCodeName[2] = "403  �����ġ��������" ;
g_strStockSubjectCodeName[3] = "402  ͢���ѡ��Ļ�����" ;
g_strStockSubjectCodeName[4] = "401  �����ѡ��Ļ�����" ;
g_strStockSubjectCodeName[5] = "420  ����ù���" ;
g_strStockSubjectCodeName[6] = "1224  ���㡼��" ;
g_strStockSubjectCodeName[7] = "1230  ����" ;

var g_strStockSubjectCode  = new Array(8); // ���ߺ�Ȥ��Ƥ���������ܥ�����
g_strStockSubjectCode[0] = "431";  //"���������ⷿ���ѹ�
g_strStockSubjectCode[1] = "433";  //�ⷿ�������ѹ�
g_strStockSubjectCode[2] = "403";  // �������������ġ��������
g_strStockSubjectCode[3] = "402";  //��������͢���ѡ��Ļ�����
g_strStockSubjectCode[4] = "401";  //�������������ѡ��Ļ�����
g_strStockSubjectCode[5] = "420";  //������������ù���
g_strStockSubjectCode[6] = "1224"; //�������������㡼��
g_strStockSubjectCode[7] = "1230"; //����

var g_lngSelIndex        = -1;			// ����Ԥ��Ǽ�����ѿ�
var g_lngDecimalCutPoint =  2;			// �������ʲ����׻������ݥ���ȡʽ���͡��������ʲ�2��ǽ�����
var g_lngCalcCode        =  0;			// �׻���ˡ���̡�0:�ͼθ�����

var g_strJpnCurrencySign    = "\\";			// ���ܱ��̲ߵ���

var g_sub_all_curSubTotalPrice   = new Array( g_saveRecord.length ); // �إå����λ������ܤ��Ȥξ��פ�����
var g_sub_all_lngProductQuantity = new Array( g_saveRecord.length ); // �إå����λ������ܤ��Ȥηײ�Ŀ�
var g_TotalFixedCost      = 0; // ������ξ���
var g_TotalFixedQuantity  = 0; // ������ηײ�Ŀ�
var g_TotalMemberCost     = 0; // ������ξ���
var g_TotalMemberQuantity = 0; // ������ηײ�Ŀ�

// ---------------------------------------------------------------
/**
* ����   : �֥إå����פ�����������פ˲���ɽ�����ڤ��ؤ���
* �о�   : ���������פ������Τ��٤�
* ����   : 
*/
// ---------------------------------------------------------------	
function fncDtBlockOpen( displayNO )
{
// 2004.09.29 tomita update
	// ���ʥ��֥�����ɥ���ɽ������
	fncProductsClose();
// 2004.09.29 tomita update end

	// ��Ȥ����������ֹ���Ǽ
	g_displayNO = displayNO; 

	// �إå�������ɽ��
	//headerBlock.style.display = "none";

	// ��������ɽ��
	InputB.style.visibility      = "visible";
	detailBlock.style.visibility = "visible";

	InputA.style.visibility      = "hidden";
	headerBlock.style.visibility = "hidden";


	// ������̾�Τ򥻥å�(�������ܥץ饹��������̾��)
	document.DSO.strStockSubjectCodeName.value = g_strStockSubjectCodeName[ g_displayNO ] ;

	//�������ܤ��顢�������ʤΥ��ץ�����ͤ����
	fncDtGetStockSubjectOption();

	// ������ν����
	fncDtClearRecord();
}


// ---------------------------------------------------------------
/**
* ����   : ���������פ���֥إå����פ˲���ɽ�����ڤ��ؤ���
* �о�   : ���������פ������Τ��٤�
* ����   : 
*/
// ---------------------------------------------------------------	
function fncHdBlockOpen()
{
	// ����������ɽ��
	InputB.style.visibility      = "hidden";
	detailBlock.style.visibility = "hidden";

	InputA.style.visibility      = "visible";
	headerBlock.style.visibility = "visible";

	// ���פ�Ʒ׻�
	fncHdSub_all_curSubTotalPrice();
}


// ---------------------------------------------------------------
/**
* ����   : �������ܤ��顢�������ʤΥ��ץ�����ͤ����
* �о�   : ���������פ������Τ��٤�
* ����   : 
*/
// ---------------------------------------------------------------	
function fncDtGetStockSubjectOption()
{
		//�������ܤ��顢�������ʤΥ��ץ�����ͤ����
		subLoadMasterOption( 'cnStockItemEstimateRegist',
				 document.DSO.lngStockItemCode, 
				 document.DSO.lngStockItemCode,
				 Array(g_strStockSubjectCode[g_displayNO]),
				 window.document.objDataSourceSetting10,
				 10 );
		//�嵭�塢���ٹԤ�ɽ�����ٱ��к��Τ���parts.tmpl�˵��ҡ�
}


// ---------------------------------------------------------------
/**
* ����   : �̲ߤ�����
* �о�   : ���������פ������Τ��٤�
* ����   : ���������ⷿ�������ѹ�ȣ�������͢���ѡ��Ļ�����ϥǥե���Ȥǥɥ������
*          ����¾�����ܱ�
*/
// ---------------------------------------------------------------	
function fncDtMonetaryUnitCode()
{
	if( g_displayNO == "1" || g_displayNO == "3" )
	{
		// �ɥ�򥻥å�
		document.DSO.lngMonetaryUnitCode.value = "$";
	}
	else
	{
		// ���ܱߤ򥻥å�
		document.DSO.lngMonetaryUnitCode.value = "\\";
	}

	// �����å��ؿ��Ѥˡ��̲ߵ���򥰥��Х��ѿ��˥��åȤ���
	fncCheckNumberCurrencySign(document.DSO.lngMonetaryUnitCode.value);

	// �����졼�Ȥη׻�
	fncDtConversionRate();
}

// ---------------------------------------------------------------
/**
* ����   : �̲ߤ����򤷤��顢�����졼�Ȥ�ȿ��
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtConversionRate()
{
	// �����졼�Ȥˡ�hidden���Ǥ��Ф��줿����ͤ�����
	document.DSO.curConversionRate.value = document.PRE_DSO.elements("lngMonetaryUnitCode[" + document.DSO.lngMonetaryUnitCode.value + "]").value;

	// �ײ踶���κƷ׻�
	fncDtSubTotalPrice();
}


// ---------------------------------------------------------------
/**
* ����   : ���ѥե饰������
* �о�   : ���������פ������Τ��٤�
* ����   : 
*/
// ---------------------------------------------------------------	
function fncDtPayOffTargetFlagChecked()
{
	if( g_displayNO == "0" || g_displayNO == "1" || g_displayNO == "2" )
	{
		// ������ξ��ˤϡ������å�ON
		document.DSO.bytPayOffTargetFlag.checked = true;
	}
	else
	{
		// ������ξ��ˤϡ������å�OFF
		document.DSO.bytPayOffTargetFlag.checked = false;
	}
}


// ---------------------------------------------------------------
/**
* ����   : �ѡ���������ϥե饰������
* �о�   : ���������פ������Τ��٤�
* ����   : 
*/
// ---------------------------------------------------------------	
function fncDtPercentInputFlagChecked()
{
	if( document.DSO.bytPercentInputFlag.checked == true )
	{
		// �̲ߤ�����Ǥ��ʤ�����
		document.DSO.lngMonetaryUnitCode.disabled = true;
		// �̲ߤ���Ū�����ܱߤ��ѹ�
		document.DSO.lngMonetaryUnitCode.value = g_strJpnCurrencySign;
		// ñ�������ϤǤ��ʤ�����
		document.DSO.curProductPrice.disabled = true;
		// ñ���򥯥ꥢ
		document.DSO.curProductPrice.value = "";
		// �ײ�Ψ�����ϤǤ���褦�ˤ���
		document.DSO.curProductRate.disabled = false;
		// �ײ�Ŀ��ʥǥե����������ͽ����ˤ�����
		document.DSO.lngProductQuantity.value     = document.HSO.lngProductionQuantity.value;

		// ñ���Υƥ����ȥܥå����ܡ��������ѹ�
		document.DSO.curProductPrice.style.borderColor = '#cdcdcd';
		// �ײ�Ψ�Υƥ����ȥܥå����ܡ��������ѹ�
		document.all.curProductRate.style.borderColor  = '#7f7f7f';
	}
	else
	{
		// �̲ߤ�����Ǥ���褦�ˤ���
		document.DSO.lngMonetaryUnitCode.disabled = false;
		// ñ�������ϤǤ���褦�ˤ���
		document.DSO.curProductPrice.disabled = false;
		// �ײ�Ψ�����ϤǤ��ʤ�����
		document.DSO.curProductRate.disabled = true;

		// ñ���Υƥ����ȥܥå����ܡ��������ѹ�
		document.DSO.curProductPrice.style.borderColor = '#7f7f7f';
		// �ײ�Ψ�Υƥ����ȥܥå����ܡ��������ѹ�
		document.all.curProductRate.style.borderColor  = '#cdcdcd';
	}

	// �ײ踶����Ʒ׻�
	fncDtSubTotalPrice();
}


// ---------------------------------------------------------------
/**
* ����   : �ѡ���������ϥե饰������(�Ԥ����򤷤����)
* �о�   : ���������פ������Τ��٤�
* ����   : 
*/
// ---------------------------------------------------------------	
function fncDtPercentInputFlagCheckedForSentaku()
{
	if( document.DSO.bytPercentInputFlag.checked == true )
	{
		// �̲ߤ�����Ǥ��ʤ�����
		document.DSO.lngMonetaryUnitCode.disabled = true;
		// ñ�������ϤǤ��ʤ�����
		document.DSO.curProductPrice.disabled = true;
		// �ײ�Ψ�����ϤǤ���褦�ˤ���
		document.DSO.curProductRate.disabled = false;

		// ñ���Υƥ����ȥܥå����ܡ��������ѹ�
		document.DSO.curProductPrice.style.borderColor = '#cdcdcd';
		// �ײ�Ψ�Υƥ����ȥܥå����ܡ��������ѹ�
		document.all.curProductRate.style.borderColor  = '#7f7f7f';
	}
	else
	{
		// �̲ߤ�����Ǥ���褦�ˤ���
		document.DSO.lngMonetaryUnitCode.disabled = false;
		// ñ�������ϤǤ���褦�ˤ���
		document.DSO.curProductPrice.disabled = false;
		// �ײ�Ψ�����ϤǤ��ʤ�����
		document.DSO.curProductRate.disabled = true;

		// ñ���Υƥ����ȥܥå����ܡ��������ѹ�
		document.DSO.curProductPrice.style.borderColor = '#7f7f7f';
		// �ײ�Ψ�Υƥ����ȥܥå����ܡ��������ѹ�
		document.all.curProductRate.style.borderColor  = '#cdcdcd';
	}
}



// ---------------------------------------------------------------
/**
* ����   : �ײ踶��
* �о�   : ���������פ������Τ��٤�
* ����   : �����Ϥ��ʤ����ϡ��ײ�Ŀ� �� �ײ�ñ��
*          �����Ϥξ��ϡ�Ǽ�� �� �ײ�Ŀ� �� �ײ�Ψ�ˤƷ׻�����
*/
// ---------------------------------------------------------------	
function fncDtSubTotalPrice()
{
	
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	// added by k.saito  2005/02/16
	//
	
	// ��Ǽ���Ϥ�׻��оݤȤ���
	curMultiplicationPrice = fncDelCurrencySign(fncDelKannma(document.HSO.curProductPrice.value)) ;
	
	// 401�κ����ѡ��Ļ������1�ξڻ�� �ξ��
	if( g_strStockSubjectCode[ g_displayNO ] == g_strStockSubjectCode[4] && 
		document.DSO.lngStockItemCode.value == '1' )
	{
		// �׻��оݤ�ñ�����Ǽ���Ϥ���ξ���Ϥ��ѹ�����
		curMultiplicationPrice = fncDelCurrencySign(fncDelKannma(document.HSO.curRetailPrice.value)) ;
	}
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
	
	
	// �����Ϥξ��
	if( document.DSO.bytPercentInputFlag.checked == true )
	{
		// Ǽ���ȷײ�Ŀ��ȷײ�Ψ�����Ϥ���Ƥ��ʤ���н�����λ
		if( document.HSO.curProductPrice.value  == "" || 
			document.DSO.lngProductQuantity.value == "" || 
			document.DSO.curProductRate.value  == "" )
		{
			document.DSO.curSubTotalPrice.value = "";
			document.DSO.curSubTotalPriceJP.value ="";
			return false;
		}

		// Ǽ��
//		var nouka        = fncDelCurrencySign(fncDelKannma(document.HSO.curProductPrice.value)) ;
		var nouka        = curMultiplicationPrice ;	// added by k.saito
		
		// �ײ�Ŀ�
		var kaikakukosuu = fncDelKannma(document.DSO.lngProductQuantity.value) ;
		// �ײ�Ψ �� 100
		var kaikakuritsuv= document.DSO.curProductRate.value / 100 ;

		// Ǽ�� �� �ײ�Ŀ� �� �ײ�Ψ
		// �����ܱߤȤ���ɽ��
		document.DSO.curSubTotalPrice.value = g_strJpnCurrencySign + " " + fncAddKannma( nouka * kaikakukosuu * kaikakuritsuv );

		// �ײ踶�����ܱߤ�hidden�ͤ���¸
		document.DSO.curSubTotalPriceJP.value = nouka * kaikakukosuu * kaikakuritsuv;

	}
	//�����Ϥ��ʤ����
	else
	{
		// ñ���ȷײ�Ŀ������Ϥ���Ƥ��ʤ���н�����λ
		if( document.DSO.curProductPrice.value == "" ||
			document.DSO.lngProductQuantity.value  == "" )
		{
			document.DSO.curSubTotalPrice.value = "";
			document.DSO.curSubTotalPriceJP.value ="";
			return false;
		}

		// �ײ�Ŀ�
		var kaikakukosuu = fncDelKannma(document.DSO.lngProductQuantity.value) ;
		// �ײ�ñ��
		var tanka        = fncDelCurrencySign(fncDelKannma(document.DSO.curProductPrice.value)) ;
		// �ײ�ñ�����̲ߵ���������
		document.DSO.curProductPrice.value = fncAddCurrencySign(fncAddKannma( tanka ));

		// �ײ踶�� �� �ײ�Ŀ� �� �ײ�ñ��
		document.DSO.curSubTotalPrice.value = fncAddCurrencySign(fncAddKannma( kaikakukosuu * tanka ));

		// �ײ踶�����ܱߤ�hidden�ͤ���¸
		document.DSO.curSubTotalPriceJP.value = kaikakukosuu * tanka * document.DSO.curConversionRate.value;
	}
}


// ---------------------------------------------------------------
/**
* ����   : �����Τ�����Ǥ��Ф��줿hidden�ͤ������Ͽ�ܥ���򲡤������
*          ��äƤ���hidden�ͤ򿷵�������˳�Ǽ
* �о�   : ���������פ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncSetAryList()
{

	for( i=0 ; i < g_saveRecord.length ; i++)
	{
		// �롼�פν����
		var j = 0;

		while (document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][lngStockSubjectCode]") != null)
		{
			// �ե�����PRE_DSO����äƤ���hidden�ͤ򿷵�������˳�Ǽ
			var aryRecord = fncDtNewAryForReturn( i, j );

			// ����˳�Ǽ
			g_saveRecord[i].push(aryRecord);

			j++;
		}
	}

	// ���פ�׻�
	fncHdSub_all_curSubTotalPrice();

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
	// �����̾�����
	strTableHtml = fncStrTableHtmlColumns();

	// ���������
	for( i = 0; i < g_saveRecord[ g_displayNO ].length; i++ )
	{
		strTableHtml = strTableHtml + 
					'<tr class="Lists01" id ="retsu' + i + '" onClick="fncDtSentaku(' + i + ');return false;"' + 'bgcolor="#ffffff"��>' + 
					fncStrTableHtmlRows( i ) + 
					'</tr>';
	}

	strTableHtml = strTableHtml + '</table>';

	// ���ٹԤ����򤵤�Ƥ��ʤ����ˤΤߡ�ɽ������ֺǸ�ιԤˤ�������ν����򤹤�
	if( preindex == -1 )
	{
		strTableHtml = strTableHtml + '<a name="enddisplay"></a>';
	}

	// ��¸�ΰ����������ľ���������˽񤭴�����
	DetailList.innerHTML = strTableHtml;

	// �������ܾ��פ�Ʒ׻�
	fncDtSub_all_curSubTotalPrice();

	// �������ʤ˥ե����������ư
	document.all.lngStockItemCode.focus();

}


// ---------------------------------------------------------------
/**
* ����   : �������λ������ܾ��פ򻻽Ф���
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtSub_all_curSubTotalPrice()
{

	if( g_saveRecord[ g_displayNO ].length == 0 )
	{
		// ���ٹԤ��ʤ����ϻ������ܾ��פ����ˤ���
		document.DSO.sub_all_curSubTotalPrice.value = "";
	}
	else
	{
		// ���
		var AllTotalPrice = 0;
		//���ٹԤο�
		var saveSubRecordLength = g_saveRecord[ g_displayNO ].length;

		for( gyou = 0; gyou < saveSubRecordLength; gyou++ )
		{
			// ������ξ��
			if( g_displayNO == "0" || g_displayNO == "1" || g_displayNO == "2" )
			{
				// �����оݤΤ߲û�
				if( g_saveRecord[ g_displayNO ][ gyou ][2] == "true" )
				{
					AllTotalPrice += parseInt( 10000 * g_saveRecord[ g_displayNO ][ gyou ][11] );
				}
			// ������ξ��
			}
			else
			{
				// �����оݰʳ���û�
				if( g_saveRecord[ g_displayNO ][ gyou ][2] == "false" )
				{
					AllTotalPrice += parseInt( 10000 * g_saveRecord[ g_displayNO ][ gyou ][11] );
				}
			}
		}
		AllTotalPrice = AllTotalPrice / 10000 ;

		// ��פ�������ܾ��פ�ȿ��
		document.DSO.sub_all_curSubTotalPrice.value = AllTotalPrice;
		// �������ܾ��פ�ե����ޥåȤ���
		fncCheckNumber( document.DSO.sub_all_curSubTotalPrice , 2 );
	}
}


// ---------------------------------------------------------------
/**
* ����   : �إå������λ������ܾ��פ򻻽Ф���
* �о�   : �إå�������onload ������ӡ�����������إå����������ܤ�����
*/
// ---------------------------------------------------------------
function fncHdSub_all_curSubTotalPrice()
{
	// ����ν����
	for( n=0 ; n < g_saveRecord.length ; n++ )
	{
		g_sub_all_curSubTotalPrice[ n ]  = 0;
		g_sub_all_lngProductQuantity[ n ]= 0;
	}
	g_TotalFixedCost      = 0; // ������ξ���
	g_TotalFixedQuantity  = 0; // ������ηײ�Ŀ�
	g_TotalMemberCost     = 0; // ������ξ���
	g_TotalMemberQuantity = 0; // ������ηײ�Ŀ�

	// ���׽���
	for( i=0 ; i < g_saveRecord.length ; i++)
	{
		for( j=0 ; j < g_saveRecord[i].length ; j++  )
		{
			// ����Ԥξ��ˤϡ������å�
			if( g_saveRecord[ i ][ j ][11] == 0 || g_saveRecord[ i ][ j ][11] =="" ) continue;

			// ������ξ��
			if( i==0 || i==1 || i==2 )
			{
				// �����оݤξ��
//				if( g_saveRecord[ i ][ j ][2] == "true" )
//				{
					g_sub_all_curSubTotalPrice[ i ]   += parseInt( g_saveRecord[ i ][ j ][11] );
					g_sub_all_lngProductQuantity[ i ] += parseInt( g_saveRecord[ i ][ j ][5] );

					g_TotalFixedCost      += parseInt( g_saveRecord[ i ][ j ][11] ); // ������ξ���
					g_TotalFixedQuantity  += parseInt( g_saveRecord[ i ][ j ][5] );  // ������ηײ�Ŀ�
//				}
				//�����оݤǤʤ����
//				else
//				{
//					g_TotalMemberCost     += parseInt( g_saveRecord[ i ][ j ][11] ); // ������ξ���
//					g_TotalMemberQuantity += parseInt( g_saveRecord[ i ][ j ][5] );  // ������ηײ�Ŀ�
//				}
			// ������ξ��
			}
			else
			{
				// �����оݤξ��
				if( g_saveRecord[ i ][ j ][2] == "true" )
				{
					g_TotalFixedCost      += parseInt( g_saveRecord[ i ][ j ][11] ); // ������ξ���
					g_TotalFixedQuantity  += parseInt( g_saveRecord[ i ][ j ][5] );  // ������ηײ�Ŀ�
				}
				//�����оݤǤʤ����
				else
				{
					g_sub_all_curSubTotalPrice[ i ]   += parseInt( g_saveRecord[ i ][ j ][11] );
					//g_sub_all_lngProductQuantity[ i ] += parseInt( g_saveRecord[ i ][ j ][5] );
					g_TotalMemberCost     += parseInt( g_saveRecord[ i ][ j ][11] ); // ������ξ���
					//g_TotalMemberQuantity += parseInt( g_saveRecord[ i ][ j ][5] );  // ������ηײ�Ŀ�
				}
			}
		}
	}

	// �������ܤ��Ȥ�������ηײ�Ŀ��η׻�
	// �� �������ǤθĿ��˴ط��ʤ������԰ʾ�����٤����������̵�Ѥǡ�����ͽ����򥻥å�
	for( m=3 ; m < g_saveRecord.length ; m++ )
	{
		if( g_saveRecord[m].length > 0 )
		{
			g_sub_all_lngProductQuantity[ m ] = fncDelKannma( document.HSO.lngProductionQuantity.value ); 
			g_TotalMemberQuantity             = fncDelKannma( document.HSO.lngProductionQuantity.value );  // ������ηײ�Ŀ�
		}
	}




	// �إå�������ȿ��
	for( k=0 ; k < g_saveRecord.length ; k++ )
	{
		document.HSO.elements( "g_sub_all_curSubTotalPrice[" + k + "]" ).value   = g_strJpnCurrencySign + " " + fncAddKannma( g_sub_all_curSubTotalPrice[ k ] );
		document.HSO.elements( "g_sub_all_lngProductQuantity[" + k + "]" ).value = fncAddKannma( g_sub_all_lngProductQuantity[ k ] );
	}
	document.HSO.elements("g_TotalFixedCost").value      = g_strJpnCurrencySign + " " + fncAddKannma( g_TotalFixedCost );      // ������ξ���
	document.HSO.elements("g_TotalFixedQuantity").value  = fncAddKannma( g_TotalFixedQuantity );                               // ������ηײ�Ŀ�
	document.HSO.elements("g_TotalMemberCost").value     = g_strJpnCurrencySign + " " + fncAddKannma( g_TotalMemberCost );     // ������ξ���
	document.HSO.elements("g_TotalMemberQuantity").value = fncAddKannma( g_TotalMemberQuantity );                              // ������ηײ�Ŀ�
}


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
	//���ϥǡ����Υ����å�
	var addFlg = fncDtAddCheck();

	if( addFlg == false ) return false;

	//�����Ȥ��ͤ򿷵�������˳�Ǽ
	var aryRecord = fncDtNewAry();

	//���ٹԤ����򤵤�Ƥ��ʤ����
	if ( g_lngSelIndex == -1)
	{
		//�����Х�����κǸ���ɲ�
		g_saveRecord[ g_displayNO ].push(aryRecord);

		//����Ԥ��ɲä����Ȥ��˶���Ԥ����򤹤뤿��ˤ�ȤΥ���ǥå������ݻ�����
		var preindex = -1;
	}
	//���ٹԤ����򤵤�Ƥ�����
	else
	{
		//���򤵤줿����ξ�ˡ�������������ɲä���
		saveRecordLength = parseInt(g_saveRecord[ g_displayNO ].length); 
		saveRecordLeft  = g_saveRecord[ g_displayNO ].slice( 0 , g_lngSelIndex );
		saveRecordRigft = g_saveRecord[ g_displayNO ].slice( g_lngSelIndex , saveRecordLength );
		g_saveRecord[ g_displayNO ]      = saveRecordLeft;
		g_saveRecord[ g_displayNO ].push(aryRecord);
		g_saveRecord[ g_displayNO ]      = g_saveRecord[ g_displayNO ].concat(saveRecordRigft);

		//����Ԥ��ɲä����Ȥ��˶���Ԥ����򤹤뤿��ˤ�ȤΥ���ǥå������ݻ�����
		var preindex = g_lngSelIndex;

		//����ǥå���������
		g_lngSelIndex      = -1;
	}

	//�����Ȥ��ɽ��
	fncDtDisplay( preindex );

	//�����ɲäξ��ˤϡ��ɲä�������Ԥ�����(�ײ踶���������ɤ�����Ƚ��)
	if( aryRecord[8] == "" )
	{
		//�����Х������Ĺ��
		saveRecordLength = g_saveRecord[ g_displayNO ].length;

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
	//�ǥ��쥯�ȥ깽�������ޤä����ѹ����뤳��
	{
		window.location.href = "#enddisplay";
	}
}


// ---------------------------------------------------------------
/**
* ����   : ���򤷤��Ԥ���
* �о�   : �������ȡפ������Τ��٤�(�Ժ���ܥ��󲡲���)
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

		saveRecordLength = parseInt(g_saveRecord[ g_displayNO ].length);
	
		saveRecordLeft  = g_saveRecord[ g_displayNO ].slice( 0, g_lngSelIndex );
		saveRecordRigft = g_saveRecord[ g_displayNO ].slice( g_lngSelIndex + 1, saveRecordLength );
		g_saveRecord[ g_displayNO ] = new Array();
		g_saveRecord[ g_displayNO ] = g_saveRecord[ g_displayNO ].concat( saveRecordLeft, saveRecordRigft );
	
		g_lngSelIndex = -1;
	
		//�����Ȥ��ɽ��
		fncDtDisplay();
	}
}


// ---------------------------------------------------------------
/**
* ����   : �����Ȥ��ͤ�����Ԥ��֤�������
* �о�   : �������ȡפ������Τ��٤� (�Գ���ܥ��󲡲���˸ƤФ��)
* ����   : �����Ȥ��ͤ�����Ԥ��֤������롣
*          ���θ塢�ؿ���fncDtDisplay()�פ�ƤӽФ��������Ȥ��ɽ����
* ���    :�Ԥ����򤵤�Ƥ��ʤ����ˤϡ��إå���ʬ�˥��顼��å����������
*/
// ---------------------------------------------------------------
function fncDtCommitRecord()
{
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
	document.DSO.lngStockItemCode.value       = -1;    // ��������
	document.DSO.lngCustomerCompanyCode.value        = "";    // ������
	document.DSO.strCompanyDisplayName.value        = "";    // ������̾
	document.DSO.bytPercentInputFlag.checked  = false; // �ѡ���������ϥե饰
	document.DSO.curProductRate.value         = "";    // �ײ�Ψ
	document.DSO.curProductPrice.value        = "";    // ñ���ʡ���
	document.DSO.curSubTotalPrice.value       = "";    // �ײ踶��
	document.DSO.strNote.value                = "";    // ����
	document.DSO.curSubTotalPriceJP.value     = "";    // �ײ踶�����ܱ�
	document.DSO.curConversionRate.value      = "";    // �����졼��

	// �ײ�Ψ�����ϤǤ��ʤ�����
	document.DSO.curProductRate.disabled      = false; // �ײ�Ψ

	//��¸������Ԥ����ä����ˤϡ����ιԤ�ȿž����
	if( g_lngSelIndex != -1 )
	{
		document.getElementById("retsu" + g_lngSelIndex).style.backgroundColor="#ffffff";
	}

	// ����Ԥ򥯥ꥢ
	g_lngSelIndex        = -1;

	// �̲ߤ�����
	fncDtMonetaryUnitCode();

	// ���ѥե饰������
	fncDtPayOffTargetFlagChecked();

	// �����Ϥ�����
	fncDtPercentInputFlagChecked();

	// �ײ�Ŀ��ʥǥե����������ͽ����ˤ�����
	// ������ξ��
	if( g_displayNO == "0" || g_displayNO == "1" || g_displayNO == "2" )
	{
		document.DSO.lngProductQuantity.value = "";
	// ������ξ��
	}
	else
	{
		document.DSO.lngProductQuantity.value = document.HSO.lngProductionQuantity.value;
	}
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

	// ���ٹԤ����򤵤�Ƥ�����Υ����å��ե饰(�����Ȥ˥��顼������ȥ��顼�ˤʤ�)
	var checkFlg = true;

	// ���ٹԤ����򤵤�Ƥ�����
	if( g_lngSelIndex != -1 )
	{
		//�����Ȥ��ѹ����ʤ��������å�
		checkFlg = fncDtCheck();
	}

	if( checkFlg == false ) return false;


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
	}
	//�����Ȱ㤦����Ԥ򥯥�å��������
	else
	{
		//����ǥå���������Ԥ������ֹ�򥻥å�
		g_lngSelIndex = i;

		//������ԡפ�ȿž������
		document.getElementById("retsu" + g_lngSelIndex).style.backgroundColor="#bbbbbb";

		//�������ȡפ�����Ԥ�ȿ��
		fncDtReplaceInput();

		// �����Ϥ�����
		fncDtPercentInputFlagCheckedForSentaku();

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
	//�������ȡפ��ͤ�����˥��å�
	var aryRecord = fncDtNewAry();

	for( i = 1; i < aryRecord.length ; i++ )
	{
		//�����Ȥ�����Ԥ����
		if( aryRecord[i] != g_saveRecord[g_displayNO][g_lngSelIndex][i] )
		{

//�ǥХå�y��
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
	//�ͤ����٤Ƥ�����ä��顢���Ԥ��ɲäǤ���
	if( (document.DSO.lngStockItemCode.value == "0"  ||         // �������ʤ�0
		document.DSO.lngStockItemCode.selectedIndex  == -1) &&  // �ޤ���̤����
		document.DSO.lngCustomerCompanyCode.value == "" )       // �����褬̤����

		// �ѡ���������ϥե饰��true�ΤȤ�
		if( document.DSO.bytPercentInputFlag.checked == true )
		{
			if( document.DSO.curProductRate.value == "" ) //�ײ�Ψ��̤����
			{
				return true;
			}
		}
		// �ѡ���������ϥե饰��false�ΤȤ�
		else
		{
		
			if( document.DSO.curProductPrice.value == "" ) //ñ����̤����
			{
				return true;
			}
		}


	// ���顼�����ä����˥�å��ݥ���ͤ�����ѿ�
	var alertList = "";

	// �������ʤ�0�ޤ���̤������ä����
	if( document.DSO.lngStockItemCode.value == "0" || 
		document.DSO.lngStockItemCode.selectedIndex  == -1 )
	{
		alertList += "�������ʤ����򤷤Ƥ�������!\n";
	}
//========================================================================================
//�������ɬ�ܹ��ܤˤ���by�⡡05/02/17
	// �����襳���ɤ�̤���Ϥ��ä����
	if( document.DSO.lngCustomerCompanyCode.value == "")
	//�ǥե�������Ϥ��줿403��6�������Ѥ�1224��1��1230��1�Ͻ���
           {  if(  g_strStockSubjectCode[ g_displayNO ] ==  g_strStockSubjectCode[2] && document.DSO.lngStockItemCode.value == '6' || 
                   g_strStockSubjectCode[ g_displayNO ] ==  g_strStockSubjectCode[6] && document.DSO.lngStockItemCode.value == '1' ||
                   g_strStockSubjectCode[ g_displayNO ] ==  g_strStockSubjectCode[7] && document.DSO.lngStockItemCode.value == '1' )
            {  }  else{    
������		alertList += "����������Ϥ��Ƥ�������!\n";
	              }
          }
//========================================================================================

	// �����襳���ɤ��������ä����
	if( isNaN(document.DSO.lngCustomerCompanyCode.value) )
	{
		alertList += "�����襳���ɤ��ͤ������Ǥ�!\n";
	}

	// �ײ�Ŀ���̤���Ϥ��ä����
	if( document.DSO.lngProductQuantity.value == "" )
	{
		alertList += "�ײ�Ŀ������Ϥ��Ƥ�������!\n";
	}
	//�ײ�Ŀ����ͤ��������ä����
	if( isNaN(fncDelKannma(document.DSO.lngProductQuantity.value)) )
	{
		alertList += "�ײ�Ŀ����ͤ������Ǥ�!\n";
	}

	// �ѡ���������ϥե饰��true�ΤȤ�
	if( document.DSO.bytPercentInputFlag.checked == true )
	{
		//�ײ�Ψ��̤����
		if( document.DSO.curProductRate.value == "" )
		{
			alertList += "�ײ�Ψ�����Ϥ��Ƥ�������!\n";
		}
		//�ײ�Ψ���������ä����
		if( isNaN(document.DSO.curProductRate.value) )
		{
			alertList += "�ײ�Ψ���ͤ������Ǥ�!\n";
		}
	}
	// �ѡ���������ϥե饰��false�ΤȤ�
	else
	{
		//ñ����̤����
		if( document.DSO.curProductPrice.value == "" )
		{
			alertList += "ñ�������Ϥ��Ƥ�������!\n";
		}
		//ñ�����������ä����
		if( isNaN(fncDelKannma(fncDelCurrencySign(document.DSO.curProductPrice.value))) )
		{
			alertList += "ñ�����ͤ������Ǥ�!\n";
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


// ---------------------------------------------------------------
/**
* ����    : �����ȥơ��֥����̾�����
* �о�    : �������ȡפ������Τ��٤�
* @return : strTableHtml, [String��], �����Ȥ���̾
*/
// ---------------------------------------------------------------
function fncStrTableHtmlColumns()
{
	if( lngLanguageCode == 1 )
	{
		arytxt = [ '��������', '��������', '�����о�', '������', '�ײ�Ŀ�', 'ñ��/�ײ�Ψ', '�ײ踶��', '����' ];
	}
	if( lngLanguageCode == 0 )
	{
		arytxt = [ 'Stock subject', 'Stock item', 'Amortized', 'Supplier', 'Plan Qty', 'Price', 'Plan estimate', 'Remark' ];
	}

	strTableHtml ='<table width="100%" cellpadding="0" cellspacing="1" border="0"' + 
				  'bgcolor="#6f8180"><tr class="TrSegs">' + 
				  '<td id="dlStockSubject" nowrap>' + arytxt[0] + '</td>' +
				  '<td id="dlStockItem"    nowrap>' + arytxt[1] + '</td>' +
				  '<td id="dlAmortized"    nowrap>' + arytxt[2] + '</td>' +
				  '<td id="dlSupplier"     nowrap>' + arytxt[3] + '</td>' +
				  '<td id="dlPlanQty"      nowrap>' + arytxt[4] + '</td>' +
				  '<td id="dlprice"        nowrap>' + arytxt[5] + '</td>' +
				  '<td id="dlPlanEstimate" nowrap>' + arytxt[6] + '</td>' +
				  '<td id="dlRemark"       nowrap>' + arytxt[7] + '</td>' +
				  '</tr>';

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
	// �����оݤ�ɽ���Ѥ��Ѵ�
	if( g_saveRecord[g_displayNO][i][2] == "true" )
	{
		var syoukyakuDSP = "��";
	}
	else
	{
		var syoukyakuDSP = "";
	}

	// �����ϤΥ����å����֤ˤ��ñ�����ײ�Ψ��ɽ�����ڤ��ؤ���
	if( g_saveRecord[g_displayNO][i][4] == "true" )
	{
		// �ײ�Ψ�η׻� 100�ݤ���
		if( g_saveRecord[ g_displayNO ][ i ][6] == "" )
		{
			var tankaDSP = ""; // �ײ�Ψ
		}
		else
		{
			var tankaDSP = ( g_saveRecord[ g_displayNO ][ i ][6] * 100 ) + " %&nbsp;" // �ײ�Ψ
		}
	}
	else
	{
		var tankaDSP = fncAddCurrencySignForSentaku( g_saveRecord[g_displayNO][i][10] , fncAddKannma(g_saveRecord[g_displayNO][i][7]) ) + "&nbsp;"; // ñ��
	}

	// �������ʤ�ɽ���ѡʥ����� + "��"+ ̾�� )�ˤ��뤿�ᡢoption��value�ͤ���text�ͤ�̵��������
	if( g_saveRecord[g_displayNO][i][1] != "" )
	{
		for( optionNo = 0 ; optionNo < document.DSO.lngStockItemCode.length ; optionNo++ )
		{
			if( g_saveRecord[g_displayNO][i][1] == document.DSO.lngStockItemCode.options[ optionNo ].value )
			{
				var stockItemDSP = document.DSO.lngStockItemCode.options[ optionNo ].text;
				break;
			}
		}
	}
	else
	{
		var stockItemDSP = "";
	}


	// �������ɽ���ѡʥ����� + ̾�� �ˤ��ѹ�
	if( g_saveRecord[g_displayNO][i][3] != "" )
	{
		var customerCompanyDSP = g_saveRecord[g_displayNO][i][3] + " " + g_saveRecord[g_displayNO][i][13];
	}
	else
	{
		var customerCompanyDSP = "";
	}

	strTableHtml ='<td  nowrap>'                    + g_strStockSubjectCodeName[g_displayNO]  +        // ��������
				  '</td><td nowrap>'                + stockItemDSP  +                                  // ��������
				  '</td><td align="center" nowrap>' + syoukyakuDSP  +                                  // �����о�
				  '</td><td nowrap>'                + customerCompanyDSP  +                            // ������
				  '</td><td align="center" nowrap>' + fncAddKannma(g_saveRecord[g_displayNO][i][5])  + // �ײ�Ŀ�
				  '</td><td align="right" nowrap>'  + tankaDSP +                                       // ñ�� or �ײ�Ψ
				  '</td><td align="right" nowrap>'  + fncAddCurrencySignForSentaku( g_saveRecord[g_displayNO][i][10] , fncAddKannma(g_saveRecord[g_displayNO][i][8]) ) + "&nbsp;" +  // �ײ踶��
				  '</td><td nowrap>'                + g_saveRecord[g_displayNO][i][9] +                // ����
				  '</td>'

	return strTableHtml;
}


// ---------------------------------------------------------------
/**
* ����   : �����Τ�����Ǥ��Ф��줿hidden�ͤ������Ͽ�ܥ���򲡤������
*          ��äƤ���hidden�ͤ򿷵�������˳�Ǽ
* �о�   : ���������פ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtNewAryForReturn( i , j )
{
	var aryRecord = new Array();
	aryRecord[0]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][lngStockSubjectCode]").value;       // ��������
	aryRecord[1]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][lngStockItemCode]").value;          // ��������
	aryRecord[2]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][bytPayOffTargetFlag]").value;       // �����о�
	aryRecord[3]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][lngCustomerCompanyCode]").value;    // ������
	aryRecord[4]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][bytPercentInputFlag]").value;       // �ѡ���������ϥե饰
	aryRecord[5]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][lngProductQuantity]").value;        // �ײ�Ŀ�
	aryRecord[6]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][curProductRate]").value;            // �ײ�Ψ
	aryRecord[7]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][curProductPrice]").value;           // ñ���ʡ���
	aryRecord[8]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][curSubTotalPrice]").value;          // �ײ踶��
	aryRecord[9]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][strNote]").value;                   // ����
	aryRecord[10] = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][lngMonetaryUnitCode]").value;       // �̲�
	aryRecord[11] = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][curSubTotalPriceJP]").value;        // �ײ踶�����ܱ�
	aryRecord[12] = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][curConversionRate]").value;         // �����졼��
	aryRecord[13] = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][strCompanyDisplayName]").value;     // ������̾
	return aryRecord;
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

	aryRecord[0]   = g_strStockSubjectCode[ g_displayNO ];      // �������ܥ�����
	aryRecord[1]   = document.DSO.lngStockItemCode.value;       // ��������

	if( document.DSO.bytPayOffTargetFlag.checked == true )
	{
		aryRecord[2]   = "true";  // �����о�
	}
	else
	{
		aryRecord[2]   = "false";  // �����о�
	}

	aryRecord[3]   = document.DSO.lngCustomerCompanyCode.value; // ������

	if( document.DSO.bytPercentInputFlag.checked == true )
	{
		aryRecord[4]   = "true";  // �ѡ���������ϥե饰
	}
	else
	{
		aryRecord[4]   = "false";  // �ѡ���������ϥե饰
	}

	aryRecord[5]   = fncDelKannma(document.DSO.lngProductQuantity.value);                  // �ײ�Ŀ�

	// �ײ�Ψ�η׻� 100�ǳ��
	if( document.DSO.curProductRate.value == "" )
	{
		aryRecord[6] = "";                                                                 // �ײ�Ψ
	}
	else
	{
		aryRecord[6]   = document.DSO.curProductRate.value / 100;                          // �ײ�Ψ
	}

	aryRecord[7]   = fncDelCurrencySign(fncDelKannma(document.DSO.curProductPrice.value)); // ñ���ʡ���
	aryRecord[8]   = fncDelCurrencySign(fncDelKannma(document.DSO.curSubTotalPrice.value));// �ײ踶��
	aryRecord[9]   = fncCheckReplaceString(document.DSO.strNote.value);                    // ����
	aryRecord[10]  = document.DSO.lngMonetaryUnitCode.value;                               // �̲�
	aryRecord[11]  = document.DSO.curSubTotalPriceJP.value;                                // �ײ踶�����ܱ�
	aryRecord[12]  = document.DSO.curConversionRate.value;                                 // �����졼��
	aryRecord[13]  = document.DSO.strCompanyDisplayName.value;                             // ������̾

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
	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][1]  = document.DSO.lngStockItemCode.value ;       // ��������

	if( document.DSO.bytPayOffTargetFlag.checked == true )
	{
		g_saveRecord[ g_displayNO ][ g_lngSelIndex ][2]  = "true" ;  // �����о�
	}
	else
	{
		g_saveRecord[ g_displayNO ][ g_lngSelIndex ][2]  = "false";  // �����о�
	}

	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][3]  = document.DSO.lngCustomerCompanyCode.value ; // ������

	if( document.DSO.bytPercentInputFlag.checked == true )
	{
		g_saveRecord[ g_displayNO ][ g_lngSelIndex ][4]  = "true" ;  // �ѡ���������ϥե饰
	}
	else
	{
		g_saveRecord[ g_displayNO ][ g_lngSelIndex ][4]  = "false";  // �ѡ���������ϥե饰
	}

	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][5]  = fncDelKannma(document.DSO.lngProductQuantity.value) ; // �ײ�Ŀ�

	// �ײ�Ψ�η׻� 100�ǳ��
	if( document.DSO.curProductRate.value == "" )
	{
		g_saveRecord[ g_displayNO ][ g_lngSelIndex ][6] = ""; // �ײ�Ψ
	}
	else
	{
		g_saveRecord[ g_displayNO ][ g_lngSelIndex ][6] = document.DSO.curProductRate.value / 100; // �ײ�Ψ
	}

	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][7]  = fncDelCurrencySign(fncDelKannma(document.DSO.curProductPrice.value)) ;  // ñ���ʡ���
	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][8]  = fncDelCurrencySign(fncDelKannma(document.DSO.curSubTotalPrice.value)) ; // �ײ踶��
	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][9]  = document.DSO.strNote.value ;                // ����
	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][10] = document.DSO.lngMonetaryUnitCode.value ;    // �̲�
	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][11] = document.DSO.curSubTotalPriceJP.value ;     // �ײ踶�����ܱ�
	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][12] = document.DSO.curConversionRate.value ;      // �����졼��
	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][13] = document.DSO.strCompanyDisplayName.value ;  // ������̾
}


// ---------------------------------------------------------------
/**
* ����   : �����Ȥ�����Ԥ�ȿ��
* �о�   : �������ȡפ������Τ��٤�
*/
// ---------------------------------------------------------------
function fncDtReplaceInput()
{
	document.DSO.lngStockItemCode.value       = g_saveRecord[ g_displayNO ][ g_lngSelIndex ][1];  // ��������

	if( g_saveRecord[ g_displayNO ][ g_lngSelIndex ][2] == "true" )
	{
		document.DSO.bytPayOffTargetFlag.checked  = true;  // �����о�
	}
	else
	{
		document.DSO.bytPayOffTargetFlag.checked  = false;  // �����о�
	}

	document.DSO.lngCustomerCompanyCode.value = g_saveRecord[ g_displayNO ][ g_lngSelIndex ][3];  // ������

	if( g_saveRecord[ g_displayNO ][ g_lngSelIndex ][4] == "true" )
	{
		document.DSO.bytPercentInputFlag.checked  = true;  // �ѡ���������ϥե饰
	}
	else
	{
		document.DSO.bytPercentInputFlag.checked  = false;  // �ѡ���������ϥե饰
	}
	document.DSO.lngProductQuantity.value     = fncAddKannma(g_saveRecord[ g_displayNO ][ g_lngSelIndex ][5]); // �ײ�Ŀ�

	// �ײ�Ψ�η׻� 100�ݤ���
	if( g_saveRecord[ g_displayNO ][ g_lngSelIndex ][6] == "" )
	{
		document.DSO.curProductRate.value = ""; // �ײ�Ψ
	}
	else
	{
		document.DSO.curProductRate.value  = g_saveRecord[ g_displayNO ][ g_lngSelIndex ][6] * 100;  // �ײ�Ψ
	}

	document.DSO.curProductPrice.value        = fncAddCurrencySignForSentaku( g_saveRecord[ g_displayNO ][ g_lngSelIndex ][10] , fncAddKannma(g_saveRecord[ g_displayNO ][ g_lngSelIndex ][7]) ); // ñ���ʡ���
	document.DSO.curSubTotalPrice.value       = fncAddCurrencySignForSentaku( g_saveRecord[ g_displayNO ][ g_lngSelIndex ][10] , fncAddKannma(g_saveRecord[ g_displayNO ][ g_lngSelIndex ][8]) ); // �ײ踶��
	document.DSO.strNote.value                = fncCheckReplaceStringBack(g_saveRecord[ g_displayNO ][ g_lngSelIndex ][9]);        // ����
	document.DSO.lngMonetaryUnitCode.value    = g_saveRecord[ g_displayNO ][ g_lngSelIndex ][10]; // �̲�
	document.DSO.curSubTotalPriceJP.value     = g_saveRecord[ g_displayNO ][ g_lngSelIndex ][11]; // �ײ踶�����ܱ�
	document.DSO.curConversionRate.value      = g_saveRecord[ g_displayNO ][ g_lngSelIndex ][12]; // �����졼��
	document.DSO.strCompanyDisplayName.value  = g_saveRecord[ g_displayNO ][ g_lngSelIndex ][13];  // ������̾
}


// ---------------------------------------------------------------
/**
* ����   : �������Υǡ���(g_saveRecord)����Ͽ���뤿��Υǡ������Ѵ�
* �о�   : ���������פ������Τ��٤�
* return : strHiddenHtml, [string��], ���٤������������Ƥ�hidden���֤��������Ǥ��Ф�
*/
// ---------------------------------------------------------------
function fncDtHiddenHtml()
{
	var strHiddenHtml = "";

	for( i=0 ; i < g_saveRecord.length ; i++)
	{
		// �롼�פν����
		var j = 0;

		for(j=0; j< g_saveRecord[i].length; j++)
		{
			//���ԥ����å�
			if (g_saveRecord[i][j][8] == "") continue; // �ײ踶����������ä������ԤȤߤʤ����Ϥ��ʤ�
	
			strHiddenHtml += "<input type='hidden' name='aryDitail[" + i + "][" + j + "][lngStockSubjectCode]'    value='" + g_saveRecord[i][j][0]  + "' >\n"  + // ��������
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][lngStockItemCode]'       value='" + g_saveRecord[i][j][1]  + "' >\n"  + // ��������
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][bytPayOffTargetFlag]'    value='" + g_saveRecord[i][j][2]  + "' >\n"  + // �����о�
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][lngCustomerCompanyCode]' value='" + g_saveRecord[i][j][3]  + "' >\n"  + // ������
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][bytPercentInputFlag]'    value='" + g_saveRecord[i][j][4]  + "' >\n"  + // �ѡ���������ϥե饰
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][lngProductQuantity]'     value='" + g_saveRecord[i][j][5]  + "' >\n"  + // �ײ�Ŀ�
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][curProductRate]'         value='" + g_saveRecord[i][j][6]  + "' >\n"  + // �ײ�Ψ
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][curProductPrice]'        value='" + g_saveRecord[i][j][7]  + "' >\n"  + // ñ���ʡ���
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][curSubTotalPrice]'       value='" + g_saveRecord[i][j][8]  + "' >\n"  + // �ײ踶��
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][strNote]'                value='" + g_saveRecord[i][j][9]  + "' >\n"  + // ����
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][lngMonetaryUnitCode]'    value='" + g_saveRecord[i][j][10] + "' >\n"  + // �̲�
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][curSubTotalPriceJP]'     value='" + g_saveRecord[i][j][11] + "' >\n"  + // �ײ踶�����ܱ�
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][curConversionRate]'      value='" + g_saveRecord[i][j][12] + "' >\n"  + // �����졼��
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][strCompanyDisplayName]'  value='" + g_saveRecord[i][j][13] + "' >\n";   // ������̾
		}
	}
	return strHiddenHtml;
}


// ---------------------------------------------------------------
/**
* ����   : ��Ͽ�ܥ���򲡤����Ȥ��ˡ�header��ˡ������ȤΥǡ�����hidden���Ǥ��Ф��Ƥ��饵�֥ߥå�
* �о�   : Header�� ��Ͽ�ܥ���
* ����   : 
*/
// ---------------------------------------------------------------
function fncDtRegistRecord()
{
		// �Ǥ��Ф�HTML���Ǽ�����ѿ�
		var strHiddenHtml = "";

		// ������������(g_saveRecord)��html���Ѵ�
		strHiddenHtml += fncDtHiddenHtml();
	
		// �ե�����HSO��������������ǡ������Ϥ�
		DtHiddenRecord.innerHTML = strHiddenHtml;

//�ǥХå���
//alert(strHiddenHtml);

		//�ե�����HSO�򥵥֥ߥå�
		document.HSO.submit();
}


// ---------------------------------------------------------------
/**
* ����   : ��Ͽ�ܥ���򲡤����Ȥ��ˡ�header��ˡ������ȤΥǡ�����hidden���Ǥ��Ф�
* �о�   : Header�� ��Ͽ�ܥ���
* ����   : 
*/
// ---------------------------------------------------------------
function fncDtRegistRecordNoSubmit()
{
		// �Ǥ��Ф�HTML���Ǽ�����ѿ�
		var strHiddenHtml = "";

		// ������������(g_saveRecord)��html���Ѵ�
		strHiddenHtml += fncDtHiddenHtml();
	
		// �ե�����HSO��������������ǡ������Ϥ�
		DtHiddenRecord.innerHTML = strHiddenHtml;

//�ǥХå���
//alert(strHiddenHtml);
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
function fncAddCurrencySign( num )
{
	var str = num.toString();

	var CurrencySign = document.DSO.lngMonetaryUnitCode.value;

	//����ʳ��ξ����̲ߵ����Ĥ���
	if( str != "" )
	{
		str = CurrencySign + " " + str;
	}

	return str;
}


// ---------------------------------------------------------------
/**
* ����    : �̲ߵ�����դ���
* �о�    : ���ٹԤ�ɽ�������ٹԤ����򤷤����
* @param  : num, [string��], �̲ߵ�����դ������� (��   1,000.0000)
* @return : str, [string��], �̲ߵ�����դ�����   (�� \ 1,000.0000)
*/
// ---------------------------------------------------------------
function fncAddCurrencySignForSentaku( CurrencySign , num )
{
	var str = num.toString();

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




//-->