': -----------------------------------------------------------------------------
': �ե����복�ס�
':               VBScript ���̥��饹
': ���͡�
':       ���̤ǻ��Ѥ��륯�饹��
':
': ��������2003/10/09
': �����ԡ�Kazushi Saito
': ��������
':
': -----------------------------------------------------------------------------


' ------------------------------------------
' �����Х��ѿ����
' ------------------------------------------
Public Const c_lngAfterDecimal = 4	'�����оݤξ������ʲ��η���ʴݤ������


' ------------------------------------------
' ���ס������å����饹
' ���͡����͡����ա��Υ����å���Ԥʤ�
' ------------------------------------------
Class clsCheck

	' Script-Test
	Private m_TestMsg

	Public Property Let Test(strMsg)
		m_TestMsg = strMsg
	End Property

	Public Property Get Test()
		Test = m_TestMsg
	End Property
	
	
	
	Private m_strNumberCurrencySign		'�̲ߵ���
	Private m_strNumberConnectionChar	'�̲ߵ���ȶ�ۤ���³����ʸ��
	Private m_lngNumberAfterDecimal		'�������ʲ��η�
	Private m_lngStringMaxLength		'ʸ����κ����
	
	Private m_strFormatNumberValue		'fncComFormatNumberValue() �ˤ��ե����ޥåȤ��줿ľ�����
	
	' ------------------------------------------
	' ------------------------------------------
	Private Sub Class_Initialize()
	
	
		'���ͽ����ˤƾ������ʲ��η���򲿷�ˤ��ƽ������뤫��������
		NumberAfterDecimal = c_lngAfterDecimal
	
	End Sub

	' ------------------------------------------
	' ���ס��̲ߵ���
	' ������strValue ��
	' ------------------------------------------
	Public Property Let NumberCurrencySign(strValue)
		m_strNumberCurrencySign = strValue
	End Property

	Public Property Get NumberCurrencySign()
		NumberCurrencySign = m_strNumberCurrencySign
	End Property

	' ------------------------------------------
	' ���ס��̲ߵ���ȶ�ۤ���³����ʸ��
	' ������strValue ��
	' ------------------------------------------
	Public Property Let NumberConnectionChar(strValue)
		m_strNumberConnectionChar = strValue
	End Property

	Public Property Get NumberConnectionChar()
		NumberConnectionChar = m_strNumberConnectionChar
	End Property

	' ------------------------------------------
	' ���ס��������ʲ��η׻��򲿷�ˤ��뤫
	' ������lngValue ��
	' ------------------------------------------
	Public Property Let NumberAfterDecimal(lngValue)
		m_lngNumberAfterDecimal = lngValue
	End Property

	Public Property Get NumberAfterDecimal()
		NumberAfterDecimal = m_lngNumberAfterDecimal
	End Property


	' ------------------------------------------
	' ���ס�ʸ����κ����
	' ������lngValue ��
	' ------------------------------------------
	Public Property Let StringMaxLength(lngValue)
		m_lngStringMaxLength = lngValue
	End Property

	Public Property Get StringMaxLength()
		StringMaxLength = m_lngStringMaxLength
	End Property
	

	' ------------------------------------------
	' ���ס��̲ߵ���
	' ������strValue ��
	' ------------------------------------------
	Public Property Let FormatNumberValue(strValue)
		m_strFormatNumberValue = strValue
	End Property

	Public Property Get FormatNumberValue()
		FormatNumberValue = m_strFormatNumberValue
	End Property


	' ------------------------------------------
	' ���ס����ͤΥե����ޥå�
	' ������TextBox���֥�������
	' ����͡�True/False ����������
	' ���͡�
	' ------------------------------------------
'	Public Function fncComFormatNumber( objText )
'		On Error Resume Next
'
'		Dim lngValue		'�ƥ�������
'		Dim aryValue		'�ƥ������͡��̲ߵ���ܶ�ۡ�
'		Dim strCurrencySign	'�̲ߵ���
'		Dim curAmount 		'��� money
'		
'
'		'�ؿ�������ͤ�����
'		fncComFormatNumber = False
'
'		'���֥������Ȥ�¸�ߤ��ǧ�������Բ�ǽ�ʾ�硢���Ԥ��ֵ�
'		If IsEmpty( objText.value ) Then Exit Function
'
'		
'		'���顼Ƚ��
'		If Len(Trim(objText.value)) = 0 Then
'			'�ؿ����������ֵ�
'			fncComFormatNumber = True
'			Exit Function
'		End If
'		
'		' ��³ʸ����������
'		'�ץ�ѥƥ������̲ߵ����Ʊ���̲ߵ���ξ�硢���֥������Ȥ��ͤ��ѿ�������
'		If( (Instr(objText.value, NumberConnectionChar) > 0) AND (NumberConnectionChar <> "") ) Then
'			
'			'�̲ߵ���ν���
'			aryValue = Split(objText.value, NumberConnectionChar)
'			strCurrencySign = aryValue(0)
'			curAmount = aryValue(1)
'			lngValue = curAmount
'			
'		'��³ʸ����̵�����
'		Else
'			'�̲ߵ��椬���ä���硢���
'			lngValue = Replace( objText.value, NumberCurrencySign, "" )
'		End If
'
'		'���ͤؤ��Ѵ�����ǽ����Ĵ�٤�
''		If Not IsNumeric( lngValue ) Then
'			'�ؿ����Ԥ��ֵ�
'			Exit Function
'		End If
'		
'		'�̲߷����Ѵ�
'		lngValue = CCur(lngValue)
'		'���ͷ����˥ե����ޥåȤ��ơ����Υƥ����ȥܥå���������
'		objText.value = NumberCurrencySign & NumberConnectionChar & FormatNumber(lngValue, NumberAfterDecimal, True, False)
'		'objText.value = Round(lngValue, 4)
'		
'		
'		'�������ֵ�
'		fncComFormatNumber = True
'
'		'�⤷���顼�������Ƥ������
'		If( Err.number > 0 ) Then
'			'fncComFormatNumber = False
'		End If
'	End Function

	' ------------------------------------------
	' ���ס����ͤΥե����ޥåȡʼ¿����ǡ�
	' ������strValue			�͡�String��
	'		blnCurrencyEscape	�̲ߵ���Υ��������ס�\�ޡ�����
	' ����͡��ե����ޥåȤ��줿��
	' ���͡�
	' ------------------------------------------
	Public Function fncComFormatNumberValue( strValue, blnCurrencyEscape )
		On Error Resume Next

		Dim lngValue		'�ƥ�������
		Dim aryValue		'�ƥ������͡��̲ߵ���ܶ�ۡ�
		Dim strCurrencySign	'�̲ߵ���
		Dim curAmount 		'��� money


		'�ؿ�������ͤ�����
		fncComFormatNumberValue = False

		' ¸�ߤ��ǧ�������Բ�ǽ�ʾ�硢���Ԥ��ֵ�
		If IsEmpty( strValue ) Then Exit Function

		'���顼Ƚ��
		If Len(Trim(strValue)) = 0 Then
			'�ؿ����������ֵ�
			fncComFormatNumberValue = True
			Exit Function
		End If

		' ��³ʸ����������
		'�ץ�ѥƥ������̲ߵ����Ʊ���̲ߵ���ξ�硢���֥������Ȥ��ͤ��ѿ�������
		If( (Instr(strValue, NumberConnectionChar) > 0) AND (NumberConnectionChar <> "") ) Then
			
			'�̲ߵ���ν���
			aryValue = Split(strValue, NumberConnectionChar)
			strCurrencySign = aryValue(0)
			curAmount = aryValue(1)
			lngValue = curAmount
			
		'��³ʸ����̵�����
		Else
			'�̲ߵ��椬���ä���硢���
			lngValue = Replace( strValue, NumberCurrencySign, "" )
		End If

		'���ͤؤ��Ѵ�����ǽ����Ĵ�٤�
		If Not IsNumeric( lngValue ) Then
			'�ؿ����Ԥ��ֵ�
			Exit Function
		End If

		'�̲߷����Ѵ�
		lngValue = CCur(lngValue)
		'���ͷ����˥ե����ޥåȤ��ơ����Υƥ����ȥܥå���������
		strValue = NumberCurrencySign & NumberConnectionChar & FormatNumber(lngValue, NumberAfterDecimal, True, False)
		
		'���ݻ���\ �����������פ���Ƥ��ޤ����ᡢ\\ ���Ѵ�
		If blnCurrencyEscape Then
			FormatNumberValue = Replace(strValue, "\", "\\")
		Else
			FormatNumberValue = strValue
		End If
		
		'�������ֵ�
		fncComFormatNumberValue = True

		'�⤷���顼�������Ƥ������
		If( Err.number > 0 ) Then
			FormatNumberValue = "(Error)"
		End If

	End Function


	' ------------------------------------------
	' ���ס�ʸ���󤫤����դκ���
	' ������ʸ����
	' ����͡�True/False ����������
	' ���͡�ʸ���󤫤����դ������Բ�ǽ�ʾ�硢�������դ�����
	' ------------------------------------------
	Public Function fncComTextToDate( ByRef strValue )
		
		Dim strDate		'ʸ��������
		fncComTextToDate = False
	
		'ʸ����ʬ�롼�׽���
		For lngCnt = 1 To LenB(strValue)
			'��ʸ����������
			strBuff = Mid( strValue, lngCnt, 1)
			'���ͤ��ɤ�����Ƚ��
			If IsNumeric( strBuff ) Then
				strDate = strDate & strBuff
				'4,6ʸ���ܤ�/������
				Select Case lngCnt
					Case 4,6
						strDate = strDate & "/"
				End Select
				
			End If
		Next
		
		'2003/ �η����ξ��
		If LenB(strDate) = 5 Then
			strDate = strDate & "01/01"
		End If
		'2003/10/ �η����ξ��
		If LenB(strDate) = 8 Then
			strDate = strDate & "01"
		End If
		'���դ��Ѵ��Բ�ǽ�ʾ��
		If Not IsDate(strDate) Then
			strDate = Date
		End If
		'�����ͤ�����
		strValue = strDate
		
		'�������ֵ�
		fncComTextToDate = True
	End Function

	' ------------------------------------------
	' ���ס������Υե����ޥå�
	' ������TextBox���֥�������
	' ����͡�True/False ����������
	' ���͡�
	' ------------------------------------------
	Public Function fncComFormatDateTime( ByRef objText )

		Dim dtmValue	'�ƥ�������

		'�ؿ�������ͤ�����
		fncComFormatDateTime = False

		'���֥������Ȥ�¸�ߤ��ǧ�������Բ�ǽ�ʾ�硢���Ԥ��ֵ�
		If IsEmpty( objText.value ) Then Exit Function
		'���֥������Ȥ��ͤ��ѿ�������
		dtmValue = objText.value

		'���顼Ƚ��
		If Len(Trim(dtmValue)) = 0 Then
			'�ؿ����������ֵ�
			fncComFormatDateTime = True
			Exit Function
		End If
		
		'���顼Ƚ��
		If Len(dtmValue) < 4 Then
			objText.Value = Date
			objText.select()
			'�ؿ����Ԥ��ֵ�
			Exit Function
		End If

		'�����ؤ��Ѵ�����ǽ����Ĵ�٤�
		If Not IsDate( dtmValue ) Then
			'�ƥ����Ȥ������շ������Ѵ���ĩ��
			If Not fncComTextToDate( dtmValue ) Then
				'���ͤؤ��Ѵ����Բ�ǽ�ʾ�硢�������դ򸵤Υƥ����ȥܥå���������
				objText.Value = Date
				objText.select()
				'�ؿ����Ԥ��ֵ�
				Exit Function
			End If
		End If

		'���շ����Ѵ�
		dtmValue = CDate(dtmValue)

		'Web�ѤΥ����å� 1600ǯ�ʲ��ξ��̵��
		if( DatePart("yyyy", dtmValue) <= 1600 ) Then
			dtmValue = Date
		End If

		'���ͷ����˥ե����ޥåȤ��ơ����Υƥ����ȥܥå���������
		objText.Value = FormatDateTime(dtmValue, vbShortDate)

		'�������ֵ�
		fncComFormatDateTime = True

	End Function
	
	
	' ------------------------------------------
	' ���ס�ʸ����θ���
	' ������TextBox or Textarea���֥�������
	' ����͡�True/False ����������
	' ���͡�
	' ------------------------------------------
	Public Function fncComInspectionString( ByRef objText )
	
		strString = objText.value 'StrConv(objText.value, 1)
		
		MsgBox(LenB(strString) & ":" & strString)
		fncComInspectionString = True
		
	End Function

	' ------------------------------------------
	' ���ס����֥������ȤΥե����ޥå�
	' ������
	'       objObject	- TextBox���֥�������
	'       lngCheckID	- �����å�ID
	'                      1:���ͽ���
	'                      2:���ս���
	'
	' ����͡�True/False ����������
	' ���͡�
	'       ���δؿ��ϡ���Window��JavaScript�ؿ� fncErrorMessage() ��ƤӽФ��ޤ���
	'       Ʊ���˼�������ɬ�פ�����ޤ���
	' ------------------------------------------
	Public Function fncCheckObjectValue( objObject, lngCheckID )
		On Error Resume Next
		
		'�ؿ�������ͤ�����
		fncCheckObjectValue = True
		
		Select Case lngCheckID
			' ���ͽ���
			Case 1
				If( fncComFormatNumberValue( objObject.value, False ) ) Then
					' �ե����ޥå������ξ�硢���֥������Ȥ�Value��ľ�ܻ���
					objObject.value = FormatNumberValue
					Exit Function
				End If
			' ���ս���
			Case 2
				If( fncComFormatDateTime( objObject ) ) Then Exit Function
			' ʸ�������
			case 3
				If( fncComInspectionString( objObject ) ) Then Exit Function
		End Select
		
		'���Ԥ��ֵ�
		fncCheckObjectValue = False

	End Function

End Class
