// ---------------------------------------------------------------------------
// �ե����복�ס�
//	Web���֥������ȥ����å��Ѵؿ�
//
// ���͡�
//       JavaScript -> VBScript�ƤӽФ���Ԥ������顼�� fncErrorMessage() �򥭥å�����
// 
//		���ͥ����å�	��fncCheckNumber()
//		���ե����å�	��fncCheckDate()
// 		ʸ�������å�	��fncCheckString()
// 
// 		�̲ߵ����Ѵ�	��fncCheckNumberCurrencySign()
// 
// 		���顼���å�	��fncErrorMessage()
// 
// 
// 
// 
// ��������2003/10/09
// �����ԡ�Kazushi Saito
// ��������
//		2004/03/29 K.S
//			fncCheckNumberValue() �������ʲ��ڼΤƽ������ɲ�
//
// ---------------------------------------------------------------------------

	// ���顼�����������֥������Ȥ�̾��
	var m_strErrorObjectName = "";

	var m_lngErrorFlag = 0;

	// -------------------------------------------------------
	// ���顼����
	// ������
	//		lngCheckID	-	�����å�ID
	//		aryArgs		-	���������
	// -------------------------------------------------------
	function fncErrorMessage(lngCheckID, strErrorObjectName, aryArgs)
	{


		// ���Ȥ򻲾Ȥ�����
		if( typeof(ErrorMessageFrame) != 'undefined' )
		{
			objWindow1 = window.ErrorMessageFrame;
			objWindow2 = window.errorWin;
		}
		// Iframe���黲�Ȥ�����
		else
		{
			objWindow1 = parent.ErrorMessageFrame;
			objWindow2 = parent.errorWin;
		}


		switch (lngCheckID)
		{
			// ���ｪλ
			case 0:
				// ���٤Ǥ⥨�顼�����������֥������Ȥ�̾����Ʊ����ä���
				if( m_strErrorObjectName == strErrorObjectName )
				{
					// �����
					m_strErrorObjectName = "";

					// ���顼��å�������ɽ��
					objWindow1.style.visibility = 'hidden';
					objWindow2.ErrMeg.innerText = '';

					m_lngErrorFlag = 0;
					//window.status = "";
				}
				break;
				
			// ���ͥ��顼
			case 1:
				// ���顼���֥�������̾������
				m_strErrorObjectName = strErrorObjectName;

				// ���顼��å�����ɽ��
				objWindow1.style.visibility = 'visible';
				objWindow2.ErrMeg.innerText = '���������ͤǤ����Ϥ�ɬ�פǤ���';

				m_lngErrorFlag = 1;
				//window.status = ("���������ͤǤ����Ϥ�ɬ�פǤ���");
				break;
			
			// ���ե��顼
			case 2:
				// ���顼���֥�������̾������
				m_strErrorObjectName = strErrorObjectName;

				// ���顼��å�����ɽ��
				objWindow1.style.visibility = 'visible';
				objWindow2.ErrMeg.innerText = '���������դǤ����Ϥ�ɬ�פǤ���';

				m_lngErrorFlag = 1;
				//window.status = ("���������դǤ����Ϥ�ɬ�פǤ���");
				break;
			
			// ʸ�������顼
			case 3:
				// ���顼���֥�������̾������
				m_strErrorObjectName = strErrorObjectName;

				// ���顼��å�����ɽ��
				objWindow1.style.visibility = 'visible';
				objWindow2.ErrMeg.innerText = 'ʸ���������С� : ��' + (aryArgs[1]-aryArgs[0]) + '�Х��ȡϺ�����Ʋ�������';

				m_lngErrorFlag = 1;
				//window.status = ("ʸ�������¡�" + aryArgs[0] + "�Х��ȡϤ��Ф��ơ�" + aryArgs[1] + "�Х��ȡϤ�ʸ��������¿�����ޤ���\n��" + (aryArgs[1]-aryArgs[0]) + "�Х��ȡϺ�����Ʋ�������");
				break;
			default:
		}
		
	}
	
	
	// -------------------------------------------------------
	// �̲ߵ����ʸ������
	var m_strInitCurrencySign = "\\";
	var m_strSetingCurrencySign = m_strInitCurrencySign;
	var m_aryCheckSetValue = new Array();
	
	
	// -------------------------------------------------------
	// �̲ߵ��������
	// -------------------------------------------------------
	function fncCheckNumberCurrencySign(strCurrency)
	{
		// �ǥե�����̲ߵ��������
		m_strSetingCurrencySign = strCurrency;
	}
	
	
	// -------------------------------------------------------
	// m_aryCheckSetValue ���ꥢ���ͤ��ݻ�����
	// -------------------------------------------------------
	function fncCheckSetValue(lngSetID, objValue)
	{
		
		m_aryCheckSetValue[lngSetID] = objValue;

	}
	
	// -------------------------------------------------------
	// m_aryCheckSetValue ���ꥢ���ͤ��������
	// -------------------------------------------------------
	function fncCheckGetValue(lngSetID)
	{
		if( typeof(m_aryCheckSetValue[lngSetID]) == 'undefined' )
		{
			return "(Error) lngCheck.js";
		}
		
		return m_aryCheckSetValue[lngSetID];

	}


	// -------------------------------------------------------
	// ���ס����ͤΥ����å�
	// ������
	//		objObject			-	������Υ��֥�������
	//		lngAfterDecimal		-	�������ʲ�����ǻ��ꤹ�뤫
	//		blnCurrencySign		-	�̲ߵ�����ղä��뤫
	// ���͡�
	// -------------------------------------------------------
	function fncCheckNumber(objObject, lngAfterDecimal, blnCurrencySign)
	{
		// �̲ߵ���λ���
		strCurrencySign = m_strSetingCurrencySign;
		// �̲ߵ����ä����
		if( blnCurrencySign == false )
		{
			strCurrencySign = "";
		}
		
		/* window.execScript��Edge���б��Τ��ᡢfncVBSCheckNumber��Javascript�˼������ʤ�����eval�¹�
		// �оݥ��֥�������̾�μ���
		strObjectName = 'window.'+ objObject.form.name +'.'+ objObject.name;
		strParam = 'Call fncVBSCheckNumber('+ strObjectName +', '+ lngAfterDecimal + ', '+ '"' + strCurrencySign + '"' +')';

		// VBScript�μ¹�
		window.execScript(strParam, "VBScript");
		*/
		
		eval(strParam);

	}

	// -------------------------------------------------------
	// ���ס����ͤΥե����ޥå�
	// ������
	//		strValue			-	�¿���
	//		lngAfterDecimal		-	�������ʲ�����ǻ��ꤹ�뤫
	//		blnCurrencySign		-	�̲ߵ�����ղä��뤫
	//		lngDecimalCutPoint	-	�������ʲ��򲿷���ڤ�ΤƤ뤫�ʰ�������ꤷ�ʤ���� or null �ξ����ڼΤƤʤ���
	//		lngCalcCode			-	�׻���ˡ����
	//
	// ���͡����δؿ���Script��ǻ��Ѥ��뤳�ȡ�
	// -------------------------------------------------------
	function fncCheckNumberValue(strValue, lngAfterDecimal, blnCurrencySign, lngDecimalCutPoint, lngCalcCode)
	{
		
		// �̲ߵ���λ���
		strCurrencySign = m_strSetingCurrencySign;
		// �̲ߵ����ä����
		if( blnCurrencySign == false )
		{
			strCurrencySign = "";
		}
		//alert(lngDecimalCutPoint);
		// ����̵���ξ��ˡ�null ���Ϥ�
		if( typeof(lngDecimalCutPoint) == "undefined" )
		{
			lngDecimalCutPoint = null;
		}

		/* window.execScript��Edge���б��Τ���¹Դؿ���javascript���ִ�����ľ�ܼ¹�
		// �оݥ��֥�������̾�μ���
		//strParam = 'Call fncVBSCheckNumberValue("'+ strValue +'", '+ lngAfterDecimal + ', '+ '"' + strCurrencySign + '", ' + lngDecimalCutPoint + ', ' + lngCalcCode + ')';
		
		// VBScript�μ¹�
		//window.execScript(strParam, "VBScript");
		*/

		var ret = fncVBSCheckNumberValue(strValue,lngAfterDecimal,strCurrencySign,lngDecimalCutPoint,lngCalcCode);

		// ���������ե����ޥå��ͤ��ֵ�
		return fncCheckGetValue(1);

	}

	// -------------------------------------------------------
	// ���ס����դΥ����å�
	// ������
	//		objObject		Text���֥�������
	//		lngFormatNo		0 or ����̵��:YYYY/MM/DD
	//		                1:YYYY/MM
	// ����͡�
	// -------------------------------------------------------
	function fncCheckDate(objObject, lngFormatNo)
	{
		lngFormat = 0;	// 0:YYYY/MM/DD �ǤΥե����ޥå�
			
		// �оݥ��֥�������̾�μ���
		strObjectName = 'window.'+ objObject.form.name +'.'+ objObject.name;
		
		var strDate = objObject.value;
		
		// ��ʸ����̵��
	    if(strDate == ""){
	        fncErrorMessage(0, strObjectName);
	        return true;
	    }
		
		// �ֹ椬���ꤵ��Ƥ�����
		if( isNaN(lngFormatNo) == false )
		{
	        // ǯ/��η����Τߵ��Ƥ���
	        if(!strDate.match(/^\d{4}\/\d{1,2}$/)){
	            fncErrorMessage(2, strObjectName);
	            return false;
	        }
	        // �����Ѵ����줿���դ������ͤ�Ʊ�������ǧ
	        // new Date()�ΰ��������������դ����Ϥ��줿��硢�����������դ��Ѵ�����Ƥ��ޤ�����
	        // 
	        var date = new Date(strDate);  
	        if(date.getFullYear() !=  strDate.split("/")[0] 
	            || date.getMonth() != strDate.split("/")[1] - 1 
	        ){
	            fncErrorMessage(2, strObjectName);
	            return false;
	        }

	        if(date.getFullYear() <= 1600){
	    	    var today = new Date();  
	            objObject.value = today.getFullYear() + "/" + (today.getMonth() + 1);
	        }
		}
		else
		{
	        // ǯ/��/���η����Τߵ��Ƥ���
	        if(!strDate.match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)){
	            fncErrorMessage(2, strObjectName);
	            return false;
	        }
	        // �����Ѵ����줿���դ������ͤ�Ʊ�������ǧ
	        // new Date()�ΰ��������������դ����Ϥ��줿��硢�����������դ��Ѵ�����Ƥ��ޤ�����
	        // 
	        var date = new Date(strDate);  
	        if(date.getFullYear() !=  strDate.split("/")[0] 
	            || date.getMonth() != strDate.split("/")[1] - 1 
	            || date.getDate() != strDate.split("/")[2]
	        ){
	            fncErrorMessage(2, strObjectName);
	            return false;
	        }

	        if(date.getFullYear() <= 1600){
	    	    var today = new Date();  
	            objObject.value = today.getFullYear() + "/" + (today.getMonth() + 1) + "/" + today.getDate();
	        }
	    }


	    fncErrorMessage(0, strObjectName);
	    return true;
	}

	// -------------------------------------------------------
	// ʸ���Υ����å�
	// ������
	// 		objObject	Text���֥�������
	//		lngMaxLength	�����å�����������Byte��
	// -------------------------------------------------------
	function fncCheckString(objObject, lngMaxLength)
	{

		lngMaxLen = 0;

		// �����ͤ����
		if( isNaN(lngMaxLength) == false)
		{
			lngMaxLen = lngMaxLength;
		}

		// maxLength�����ꤵ��Ƥ��ʤ� && text���֥������Ȥξ�硢���֥������Ȥ�maxLength ������
		if( lngMaxLen == 0 && objObject.type == "text" )
		{
			lngMaxLen = objObject.maxLength;
		}
		
		// ����ʸ��Byte���μ���
		lngObjLen = getLength(objObject.value);
		
		// ����ʸ��Byte������ʸ��Byte�����
		if( lngObjLen > lngMaxLen )
		{
			var aryArgs = new Array;
			aryArgs[0] = lngMaxLen;
			aryArgs[1] = lngObjLen;
			// ���顼����
			fncErrorMessage(3, objObject.name ,aryArgs);
			objObject.select();
			return false;
		}
		
		// ���顼̵��
		fncErrorMessage(0, objObject.name);
		return true;
		
		// �оݥ��֥�������̾�μ���
		//strObjectName = 'window.'+ objObject.form.name +'.'+ objObject.name;
		// VBScript�μ¹�
		//window.execScript('Call fncVBSCheckString('+ strObjectName +', '+ lngMaxLength +')', "VBScript");
		
	}

	// -------------------------------------------------------
	// �ü�ʸ�����Ѵ�
	// -------------------------------------------------------
	function fncCheckReplaceString( strInString )
	{
		if( strInString == "" || typeof( strInString ) == "undefined" ) return "";
	
		var strValue = strInString;

		//strValue = strValue.replace( /</g , "&lt;" ).replace( />/g , "&gt;" );
		//strValue = strValue.replace( /&/g ,"&amp;" );
		//strValue = strValue.replace( /\'/g ,"&#039;" );

		strValue = strValue.replace( /\"/g ,"&quot;" );

		return strValue;
	}

	// -------------------------------------------------------
	// ʸ�����Byte��������
	// -------------------------------------------------------
	function getLength(strValue)
	{
		var i,cnt = 0;
		for(i=0; i<strValue.length; i++) if (escape(strValue.charAt(i)).length >= 4 ) cnt+=2; else cnt++;
		return cnt;
	}

	// -------------------------------------------------------
	// ʸ���Υ����å�
	// ������
	// 		lngCalc1		�׻��ͣ�
	//		strOperator		���ѱ黻�� ( ^, *, /, \, Mod, +, - )
	// 		lngCalc2		�׻��ͣ�
	// ���͡�
	//  ���ID = 5
	// -------------------------------------------------------
	function fncVBSNumCalc(lngCalc1, strOperator, lngCalc2)
	{
		// alert("[" + parseFloat(lngCalc1) + "]" + strOperator + "[" +  parseFloat(lngCalc2) + "]");
		// ���ͤǤ�̵����硢0���ֵ�
		if( isNaN(parseFloat(lngCalc1)) || isNaN(parseFloat(lngCalc2)) )
		{
			return 0;
		}

		// �оݥ��֥�������̾�μ���
		//strParam = 'Call fncVBSNumberCalculation('+ lngCalc1 + ', '+ '"' + strOperator + '", ' + lngCalc2 + ')';
		strParam = lngCalc1 + " " + strOperator + " " +  lngCalc2;
		//alert(strParam);
		// VBScript�μ¹�
		//window.execScript(strParam, "VBScript");
		eval(strParam);
		// ���������ͤ��ֵ�
		return fncCheckGetValue(5);

	}

