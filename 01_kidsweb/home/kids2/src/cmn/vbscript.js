' -----------------------------------------------------------------------------
' �ե����복�ס�
'               VBScript ���̴ؿ�
' ���͡�
'       ���ͥե����ޥåȡ����եե����ޥåȤ�Ԥʤ�����δؿ����Ǥ���
'
' ��������2003/10/07
' �����ԡ�Kazushi Saito
' ��������
'			2003/10/23	fncVBSCheckText()�ɲá���Kazushi Saito
'			2004/03/29	K.S
'				fncVBSCheckNumberValue() �������ʲ��ڼΤƽ������ɲ�
'				
'
' -----------------------------------------------------------------------------

	/*
	' ------------------------------------------
	' ���ס�JavaScript ���顼�ؿ��θƤӽФ�
	' ������
	'		lngCheckID		-	�����å�ID
	'		strObjectName	-	���顼���֥�������̾
	'
	' ����͡�True/False ����������
	' ���͡�
	' ------------------------------------------
	*/
	function fncVBCheckErrorToJavaScript(lngCheckID, strObjectName) {
		fncErrorMessage(lngCheckID, strObjectName);
		return true;
	}
	/*
	'Public Function fncVBCheckErrorToJavaScript(lngCheckID, strObjectName)
	'
	'	' JavaScript �δؿ���¹Ԥ���
	'	Call window.execScript("fncErrorMessage(" & lngCheckID & ", '" & strObjectName & "'); ", "JavaScript")
	'
	'	fncVBCheckErrorToJavaScript = True
	'End Function
	*/
	/*
	' ------------------------------------------
	' ���ס�JavaScript ������ֵ�
	' ������
	'		lngCheckID		-	�����å�ID
	'		objValue		-	��
	'
	' ����͡�True/False ����������
	' ���͡�
	'		����ͤϡ��ܥե�������Τ��줾��δؿ��ǥ�ˡ����Ǥ���ɬ�פ�����
	' ------------------------------------------]
	*/
	function fncVBCheckSetValueToJS(lngCheckID, objValue) {
		fncCheckSetValue(lngCheckID, objValue);
		return true;
	}
	
	/*
	'Public Function fncVBCheckSetValueToJS(lngCheckID, objValue)

	'	' JavaScript �δؿ���¹Ԥ���
	'	Call window.execScript("fncCheckSetValue(" & lngCheckID & ", '" & objValue & "'); ", "JavaScript")

	'	fncVBCheckSetValueToJS = True
	'End Function
	*/
	/*
	' ------------------------------------------
	' ���ס����ͤΥ����å�
	' ������
	'		objObject	TextBox���֥�������
	'		lngAfterDecimal	�������ʲ��η��
	'		strCurrencySign	�̲ߵ���
	'
	' ����͡�True/False ����������
	' ���͡�
	' ------------------------------------------
	*/
	// window.execScript�����Τ���Javascript�ǺƼ���
	function fncVBSCheckNumber(objObject, lngAfterDecimal, strCurrencySign){
		// ����ͤν����
		var rtn = False;
	
		var objCheck;
		var lngCheckID;
		var strConnectionChar;
		
		
		// ���顼�ֹ�
		lngCheckID = 1;
		
		// �����å����֥������Ȥκ���
		objCheck = New clsCheck();
		
		// �̲ߵ���λ���
		objCheck.NumberCurrencySign = strCurrencySign;

		// ��³ʸ���򥹥ڡ����ˤ���
		strConnectionChar = " ";
		
		// �̲ߵ���λ��꤬�����ä���硢��³ʸ������ˤ���
		if( strCurrencySign = "" ) {
			strConnectionChar = "";
		}
		// �̲ߵ���ȶ�ۤ���³����ʸ��
		objCheck.NumberConnectionChar = strConnectionChar;

		// �������ʲ��ν�������λ���
		objCheck.NumberAfterDecimal = lngAfterDecimal;

		// �ͤΥ����å�
		blnRet =  objCheck.fncCheckObjectValue(objObject, 1);
		if( blnRet ){
			// �����å������ξ��
			rtn = True;
			lngCheckID = 0;
		}else{
			// ���顼������
			objObject.select();
		}

		// ���顼�ξ��
		fncVBCheckErrorToJavaScript( lngCheckID, objObject.name );
		return rtn;
	}
	/*
	'Public Function fncVBSCheckNumber(objObject, lngAfterDecimal, strCurrencySign)
	'
	'	'����ͤν����
	'	fncVBSCheckNumber = False
	'
	'	Dim objCheck
	'	Dim lngCheckID
	'	Dim strConnectionChar
	'	
	'	
	'	'���顼�ֹ�
	'	lngCheckID = 1
	'	
	'	'�����å����֥������Ȥκ���
	'	Set objCheck = New clsCheck
	'	
	'	'�̲ߵ���λ���
	'	objCheck.NumberCurrencySign = strCurrencySign
	'	
	'	'��³ʸ���򥹥ڡ����ˤ���
	'	strConnectionChar = " "
	'	
	'	'�̲ߵ���λ��꤬�����ä���硢��³ʸ������ˤ���
	'	If( strCurrencySign = "" ) Then
	'		strConnectionChar = ""
	'	End If
	'	'�̲ߵ���ȶ�ۤ���³����ʸ��
	'	objCheck.NumberConnectionChar = strConnectionChar
	''
	'	'�������ʲ��ν�������λ���
	'	objCheck.NumberAfterDecimal = lngAfterDecimal
	'
	'	'�ͤΥ����å�
	'	blnRet =  objCheck.fncCheckObjectValue(objObject, 1)
	'	If( blnRet ) Then
	'		'�����å������ξ��
	'		fncVBSCheckNumber = True
	'		lngCheckID = 0
	'	Else
	'		' ���顼������
	'		objObject.select()
	'	End If
	'
	'	'���顼�ξ��
	'	Call fncVBCheckErrorToJavaScript( lngCheckID, objObject.name )
	'
	'
	'End Function
	*/
	
	/*
	' ------------------------------------------
	' ���ס����ͤΥ����å��ʼ¿����ǡ�
	' ������
	'		strValue			�¿��͡�String��
	'		lngAfterDecimal		�������ʲ��η��
	'		strCurrencySign		�̲ߵ���
	'		lngDecimalCutPoint	�������ʲ����ڼΤư��֡� -1 �ξ�硢�ڼΤƤ��ʤ���
	'		lngCalcCode			�׻���ˡ����
	'
	' ����͡�True/False ����������
	' ���͡�
	'		lngDecimalCutPoint ����ꤹ��ȡ�����ʲ�������0�ǥե����ޥåȤ����ڼΤƤ����
	'	���δؿ��η׻���̤� fncVBCheckSetValueToJS() ����Ѥ���JS¦�����ꤵ��ޤ���
	'	���ID = 1
	' ------------------------------------------
	*/
	// window.execScript�����Τ���Javascript�ǺƼ���
	function fncVBSCheckNumberValue(strValue, lngAfterDecimal, strCurrencySign, lngDecimalCutPoint, lngCalcCode){
		// ����ͤν����
		var rtn = false;
	
		var objCheck;
		var lngSetID;
		var strConnectionChar;

		// ���ID
		lngSetID = 1;
		
		// �����å����֥������Ȥκ���
		
		objCheck = new clsCheck();
		// �̲ߵ���λ���
		objCheck.NumberCurrencySign = strCurrencySign;

		// ��³ʸ���򥹥ڡ����ˤ���
		strConnectionChar = " ";
		
		// �̲ߵ���λ��꤬�����ä���硢��³ʸ������ˤ���
		If( strCurrencySign = "" ){
			strConnectionChar = "";
		}
		// �̲ߵ���ȶ�ۤ���³����ʸ��
		objCheck.NumberConnectionChar = strConnectionChar;

		// �������ʲ��ν�������λ���
		objCheck.NumberAfterDecimal = lngAfterDecimal;
		

		// �������ʲ����ڤ�Τƽ���������֤����� 
		switch(lngDecimalCutPoint){
			// ������ (Integer)/Ĺ������ (Long)/ñ������ư���������� (Single)/��������ư���������� (Double)/�̲߷� (Currency)/ʸ���� (String)
			case 2:
			case 3:
			case 4:
			case 5:
			case 6:
			case 8:
				objCheck.DecimalCutPoint = new number(lngDecimalCutPoint);
			// ����¾/ VarType() = 1 : Null �� (̵������)
			// �������ʲ�������ͭ���ˤ���
			default:
				lngDecimalPointCharPoint = strValue.indexof(".");
				If( lngDecimalPointCharPoint > 0 ){
					lngDecimalCutPoint = strValue.substring(lngDecimalPointCharPoint+1).length
				}
				objCheck.DecimalCutPoint = lngDecimalCutPoint;
		}
		
		
		// �׻���ˡ����
		objCheck.CalcClass = lngCalcCode;
		
		
		// �ͤΥ����å�
		if(!objCheck.fncComFormatNumberValue(strValue, true)){
			// ���顼
			return false;
		}

		// �ͤ����
		strRetValue = objCheck.FormatNumberValue;
		
		// JavaScript ¦���ݻ�
		fncVBCheckSetValueToJS(lngSetID, strRetValue);
	
		// �������ֵ�
		return  true;
		
	}
	
	/*
	'Public Function fncVBSCheckNumberValue(strValue, lngAfterDecimal, strCurrencySign, lngDecimalCutPoint, lngCalcCode)
	'
	'	'����ͤν����
	'	fncVBSCheckNumberValue = False
	'
	'	Dim objCheck
	'	Dim lngSetID
	'	Dim strConnectionChar
	'
	'	' ���ID
	'	lngSetID = 1
	'	
	'	'�����å����֥������Ȥκ���
	'	Set objCheck = New clsCheck
	'	
	'	'�̲ߵ���λ���
	'	objCheck.NumberCurrencySign = strCurrencySign
	'
	'	'��³ʸ���򥹥ڡ����ˤ���
	'	strConnectionChar = " "
	'	
	'	'�̲ߵ���λ��꤬�����ä���硢��³ʸ������ˤ���
	'	If( strCurrencySign = "" ) Then
	'		strConnectionChar = ""
	'	End If
	'	'�̲ߵ���ȶ�ۤ���³����ʸ��
	'	objCheck.NumberConnectionChar = strConnectionChar
	'
	'	'�������ʲ��ν�������λ���
	'	objCheck.NumberAfterDecimal = lngAfterDecimal
	'	
	'
	'	'�������ʲ����ڤ�Τƽ���������֤����� 
	'	Select Case VarType(lngDecimalCutPoint)
	'		' ������ (Integer)/Ĺ������ (Long)/ñ������ư���������� (Single)/��������ư���������� (Double)/�̲߷� (Currency)/ʸ���� (String)
	'		Case 2,3,4,5,6,8
	'			objCheck.DecimalCutPoint = CInt(lngDecimalCutPoint)
	'		' ����¾/ VarType() = 1 : Null �� (̵������)
	'		' �������ʲ�������ͭ���ˤ���
	'		Case Else
	'			lngDecimalPointCharPoint = InStr( strValue, ".")
	'			If( lngDecimalPointCharPoint > 0 ) Then
	'				lngDecimalCutPoint = Len(Mid(strValue, lngDecimalPointCharPoint+1))
	'			End If
	'			objCheck.DecimalCutPoint = lngDecimalCutPoint
	'	End Select
	'	
	'	
	'	' �׻���ˡ����
	'	objCheck.CalcClass = lngCalcCode
	'	
	'	
	'	'�ͤΥ����å�
	'	If Not objCheck.fncComFormatNumberValue(strValue, True) Then
	'		' ���顼
	'		Exit Function
	'	End If
	'
	'	' �ͤ����
	'	strRetValue = objCheck.FormatNumberValue
	'	
	'	' JavaScript ¦���ݻ�
	'	Call fncVBCheckSetValueToJS(lngSetID, strRetValue)
	'
	'	' �������ֵ�
	'	fncVBSCheckNumberValue = True
	'	
	'End Function
	*/
	/*
	' ------------------------------------------
	' ���ס����դΥ����å�
	' ������
	'		objObject	TextBox���֥�������
	' ����͡�True/False ����������
	' ���͡�
	' ------------------------------------------
	*/
	// window.execScript�����ˤ��̤���ѤȤʤä�������
	/*
	'Public Function fncVBSCheckDate(objObject, lngFormatNo)
	'
	'	'����ͤν����
	'	fncVBSCheckDate = False
	'
	'	Dim objCheck
	'	Dim lngCheckID
	'	
	'	'���顼�ֹ�
	'	lngCheckID = 2
	'
	'	'�����å����֥������Ȥκ���
	'	Set objCheck = New clsCheck
	'
	'	'�ե����ޥåȤλ���
	'	objCheck.DateFormatNo = lngFormatNo
	'
	'	'�ͤΥ����å�
	'	blnRet =  objCheck.fncCheckObjectValue(objObject, 2)
	'	If( blnRet ) Then
	'		'�����å������ξ��
	'		fncVBSCheckDate = True
	'		lngCheckID = 0
	'	End If
	'
	'	'���顼�ξ��
	'	'��å�������ɽ��
	'	Call fncVBCheckErrorToJavaScript( lngCheckID, objObject.name )
	'
	'
	'End Function
	*/
	
	/*
	' ------------------------------------------
	' ���ס�ʸ���Υ����å�
	' ������
	'		objObject	TextBox���֥�������
	' ����͡�True/False ����������
	' ���͡�
	' ------------------------------------------
	*/
	//window.execScript�����ˤ���Ի��ѤȤʤä�������
	/*
	'Public Function fncVBSCheckString(objObject, lngMaxLength)
	'
	'	'����ͤν����
	'	fncVBSCheckString = False
	'
	'	Dim objCheck
	'	Dim lngCheckID
	'	
	'	'���顼�ֹ�
	'	lngCheckID = 3
	'
	'	'�����å����֥������Ȥκ���
	'	Set objCheck = New clsCheck
	'
	'	'ʸ���������λ���
	'	objCheck.StringMaxLength = lngMaxLength
	'
	'	'�ͤΥ����å�
	'	blnRet =  objCheck.fncCheckObjectValue(objObject, 3)
	'	If( blnRet ) Then
	'		'�����å������ξ��
	'		fncVBSCheckString = True
	'		Exit Function
	'	End If
	'
	'	'aryArgs(0) = "100";
	'
	'	'���顼�ξ��
	'	'��å�������ɽ��
	'	'window.parent.fncErrorMessage(3)
	'	Call fncVBCheckErrorToJavaScript( lngCheckID, objObject.name )
	'	
	'End Function
	*/
	
	/*
	' ------------------------------------------
	' ���ס����ͷ׻���VBS��
	' ������
	' 		lngCalc1		�׻��ͣ�
	'		strOperator		���ѱ黻�� ( ^, *, /, \, Mod, +, - )
	' 		lngCalc2		�׻��ͣ�
	' ����͡�True ����
	' ���͡�
	'	���δؿ��η׻���̤� fncVBCheckSetValueToJS() ����Ѥ���JS¦�����ꤵ��ޤ���
	' 	���ID = 5
	' ------------------------------------------
	*/
	// �ƤӽФ�����Javascript���ˤ�ꡢeval���б��������ᤳ�δؿ�����
	/*
	'Public Function fncVBSNumberCalculation(lngCalc1, strOperator, lngCalc2)
	'	On Error Resume Next
	'
	'	Dim aryValue		' �׻���
	'	Dim lngResult		' ��̿���
	'	Dim lngSetID		' ���Υ�����ץȷ�̤�ID
	'	Dim varResult		' ����ֵ���
	'	
	'	
	'	' ���ID
	'	lngSetID = 5
	'
	'	If ( StrComp(strOperator, "^", vbTextCompare) = 0) Then
	'		lngResult = CDbl(lngCalc1) ^ CDbl(lngCalc2)
	'	End If
	'	If ( StrComp(strOperator, "*", vbTextCompare) = 0) Then
	'		lngResult = CDbl(lngCalc1) * CDbl(lngCalc2)
	'	End If
	'	If ( StrComp(strOperator, "/", vbTextCompare) = 0) Then
	'		lngResult = CDbl(lngCalc1) / CDbl(lngCalc2)
	'	End If
	'	If ( StrComp(strOperator, "\", vbTextCompare) = 0) Then
	'		lngResult = CDbl(lngCalc1) \ CDbl(lngCalc2)
	'	End If
	'	If ( StrComp(strOperator, "Mod", vbTextCompare) = 0) Then
	'		lngResult = CDbl(lngCalc1) Mod CDbl(lngCalc2)
	'	End If
	'	If ( StrComp(strOperator, "+", vbTextCompare) = 0) Then
	'		lngResult = CDbl(lngCalc1) + CDbl(lngCalc2)
	'	End If
	'	If ( StrComp(strOperator, "-", vbTextCompare) = 0) Then
	'		lngResult = CDbl(lngCalc1) - CDbl(lngCalc2)
	'	End If
	'	
	'	' ����ͤ�����
	'	varResult = lngResult
	'	
	'	' �⤷���顼�������Ƥ������
	'	If( Err.number > 0 ) Then
	'		varResult = "(Error)"
	'		Err.Clear
	'	End If
	'
	'	' JavaScript ¦���ݻ�
	'	Call fncVBCheckSetValueToJS(lngSetID, varResult)
	'
	'	' �ؿ�������͡ʰ����
	'	fncVBSNumberCalculation = True
	'
	'End Function
	*/
