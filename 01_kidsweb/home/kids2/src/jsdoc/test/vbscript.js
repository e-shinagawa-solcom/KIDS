': -----------------------------------------------------------------------------
': �ե����복�ס�
':               VBScript ���̴ؿ�
': ���͡�
':       ���ͥե����ޥåȡ����եե����ޥåȤ�Ԥʤ�����δؿ����Ǥ���
':
': ��������2003/10/07
': �����ԡ�Kazushi Saito
': ��������
':			2003/10/23 fncVBSCheckText()�ɲá���Kazushi Saito
':
':
': -----------------------------------------------------------------------------


'@ ------------------------------------------
' ���ס�JavaScript ���顼�ؿ��θƤӽФ�
' ������
'		lngCheckID		-	�����å�ID
'		strObjectName	-	���顼���֥�������̾
'
' ����͡�True/False ����������
' ���͡�
' ------------------------------------------
Public Function fncVBCheckErrorToJavaScript(lngCheckID, strObjectName)

	' JavaScript �δؿ���¹Ԥ���
	Call window.execScript("fncErrorMessage(" & lngCheckID & ", '" & strObjectName & "'); ", "JavaScript")

	fncVBCheckErrorToJavaScript = True
End Function


'@ ------------------------------------------
' ���ס�JavaScript �δؿ���¹Ԥ���
' ������
'		lngCheckID		-	�����å�ID
'		objValue	-		���֥�������̾
'
' ����͡�True/False ����������
' ���͡�
' ------------------------------------------
Public Function fncVBCheckSetValueToJS(lngCheckID, objValue)

	' JavaScript �δؿ���¹Ԥ���
	Call window.execScript("fncCheckSetValue(" & lngCheckID & ", '" & objValue & "'); ", "JavaScript")

	fncVBCheckSetValueToJS = True
End Function


'@ ----------------------------------------
' ���ס����ͤΥ����å�
' ������
'		objObject	TextBox���֥�������
'		lngAfterDecimal	�������ʲ��η��
'		strCurrencySign	�̲ߵ���
'
' ����͡�True/False ����������
' ���͡�
' ------------------------------------------
Public Function fncVBSCheckNumber(objObject, lngAfterDecimal, strCurrencySign)


	'����ͤν����
	fncVBSCheckNumber = False

	Dim objCheck
	Dim lngCheckID
	Dim strConnectionChar
	
	
	'���顼�ֹ�
	lngCheckID = 1
	
	'�����å����֥������Ȥκ���
	Set objCheck = New clsCheck
	
	'�̲ߵ���λ���
	objCheck.NumberCurrencySign = strCurrencySign

	'��³ʸ���򥹥ڡ����ˤ���
	strConnectionChar = " "
	
	'�̲ߵ���λ��꤬�����ä���硢��³ʸ������ˤ���
	If( strCurrencySign = "" ) Then
		strConnectionChar = ""
	End If
	'�̲ߵ���ȶ�ۤ���³����ʸ��
	objCheck.NumberConnectionChar = strConnectionChar

	'�������ʲ��ν�������λ���
	objCheck.NumberAfterDecimal = lngAfterDecimal
	'�ͤΥ����å�
	blnRet =  objCheck.fncCheckObjectValue(objObject, 1)
	If( blnRet ) Then
		'�����å������ξ��
		fncVBSCheckNumber = True
		lngCheckID = 0
	Else
		' ���顼������
		objObject.select()
	End If

	'���顼�ξ��
	Call fncVBCheckErrorToJavaScript( lngCheckID, objObject.name )


End Function

'@ -----------------------------------------
' ���ס����ͤΥ����å��ʼ¿����ǡ�
' ������
'		strValue		�¿��͡�String��
'		lngAfterDecimal	�������ʲ��η��
'		strCurrencySign	�̲ߵ���
'
' ����͡�True/False ����������
' ���͡�
' ------------------------------------------
Public Function fncVBSCheckNumberValue(strValue, lngAfterDecimal, strCurrencySign)

	'����ͤν����
	fncVBSCheckNumberValue = False

	Dim objCheck
	Dim lngSetID
	Dim strConnectionChar


	'���顼�ֹ�
	lngSetID = 1
	
	'�����å����֥������Ȥκ���
	Set objCheck = New clsCheck
	
	'�̲ߵ���λ���
	objCheck.NumberCurrencySign = strCurrencySign

	'��³ʸ���򥹥ڡ����ˤ���
	strConnectionChar = " "
	
	'�̲ߵ���λ��꤬�����ä���硢��³ʸ������ˤ���
	If( strCurrencySign = "" ) Then
		strConnectionChar = ""
	End If
	'�̲ߵ���ȶ�ۤ���³����ʸ��
	objCheck.NumberConnectionChar = strConnectionChar

	'�������ʲ��ν�������λ���
	objCheck.NumberAfterDecimal = lngAfterDecimal
	
	'�ͤΥ����å�
	If Not objCheck.fncComFormatNumberValue(strValue, True) Then
		' ���顼
		Exit Function
	End If

	' �ͤ����
	strRetValue = objCheck.FormatNumberValue
	
	' JavaScript ¦���ݻ�
	Call fncVBCheckSetValueToJS(lngSetID, strRetValue)

	' �������ֵ�
	fncVBSCheckNumberValue = True
	
End Function


'@ ------------------------------------------
' ���ס����դΥ����å�
' ������
'		objObject	TextBox���֥�������
' ����͡�True/False ����������
' ���͡�
' ------------------------------------------
Public Function fncVBSCheckDate(objObject)

	'����ͤν����
	fncVBSCheckDate = False

	Dim objCheck
	Dim lngCheckID
	
	'���顼�ֹ�
	lngCheckID = 2

	'�����å����֥������Ȥκ���
	Set objCheck = New clsCheck

	'�ͤΥ����å�
	blnRet =  objCheck.fncCheckObjectValue(objObject, 2)
	If( blnRet ) Then
		'�����å������ξ��
		fncVBSCheckDate = True
		lngCheckID = 0
	End If

	'���顼�ξ��
	'��å�������ɽ��
	Call fncVBCheckErrorToJavaScript( lngCheckID, objObject.name )


End Function


'@ ------------------------------------------
' ���ס�ʸ���Υ����å�
' ������
'		objObject	TextBox���֥�������
' ����͡�True/False ����������
' ���͡�
' ------------------------------------------
Public Function fncVBSCheckString(objObject, lngMaxLength)

	'����ͤν����
	fncVBSCheckString = False

	Dim objCheck
	Dim lngCheckID
	
	'���顼�ֹ�
	lngCheckID = 3

	'�����å����֥������Ȥκ���
	Set objCheck = New clsCheck

	'ʸ���������λ���
	objCheck.StringMaxLength = lngMaxLength

	'�ͤΥ����å�
	blnRet =  objCheck.fncCheckObjectValue(objObject, 3)
	If( blnRet ) Then
		'�����å������ξ��
		fncVBSCheckString = True
		Exit Function
	End If

	'aryArgs(0) = "100";

	'���顼�ξ��
	'��å�������ɽ��
	'window.parent.fncErrorMessage(3)
	Call fncVBCheckErrorToJavaScript( lngCheckID, objObject.name )
	
End Function
