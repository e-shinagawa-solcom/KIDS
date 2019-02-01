' -----------------------------------------------------------------------------
' �ե����복�ס�
'               VBScript ���̥��饹
' ���͡�
'       ���̤ǻ��Ѥ��륯�饹��
'
' ��������2003/10/09
' �����ԡ�Kazushi Saito
' ��������
'	2004/03/29 K.S
'		fncComFormatNumberValue() �������ʲ��ڼΤƽ������ɲ�
'		fncComDecimalCut() �᥽�åɤ��ɲ�
'
'
'
' -----------------------------------------------------------------------------


' ------------------------------------------
' �����Х��ѿ����
' ------------------------------------------
Public Const c_lngAfterDecimal     	= 4			' �����оݤξ������ʲ��η���ʴݤ������
Public Const c_strFormatNumberErr  	= "(Error)"	' fncComFormatNumberValue() ���顼����ʸ����
Public Const c_strDecimalPointChar 	= "."		' ���������֤�ʸ��

' ------------------------------------------
' ���ס������å����饹
' ���͡����͡����ա��Υ����å���Ԥʤ�
' ------------------------------------------
Class clsCheck
	
	
	Private m_strNumberCurrencySign		' �̲ߵ���
	Private m_strNumberConnectionChar	' �̲ߵ���ȶ�ۤ���³����ʸ��
	Private m_lngNumberAfterDecimal		' �������ʲ��η�
	Private m_lngStringMaxLength		' ʸ����κ����
	Private m_lngDateFormatNo			' ���եե����ޥåȡ�0=YYYY/MM/DD or 1=YYYY/MM��
	
	Private m_strFormatNumberValue		' fncComFormatNumberValue() �ˤ��ե����ޥåȤ��줿ľ�����
	Private m_lngDecimalCutPoint		' �������ʲ��ڼΤư���
	Private m_lngCalcClass				' �׻���ˡ����
	
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
	' ���ס����եե����ޥå�No
	' ������lngValue ��
	' ------------------------------------------
	Public Property Let DateFormatNo(lngValue)
		m_lngDateFormatNo = lngValue
	End Property

	Public Property Get DateFormatNo()
		DateFormatNo = m_lngDateFormatNo
	End Property

	' ------------------------------------------
	' ���ס��������ʲ��ڼΤư���
	' ������lngValue ��
	' ------------------------------------------
	Public Property Let DecimalCutPoint(lngValue)
		m_lngDecimalCutPoint = lngValue
	End Property

	Public Property Get DecimalCutPoint()
		DecimalCutPoint = m_lngDecimalCutPoint
	End Property

	' ------------------------------------------
	' ���ס��׻���ˡ����
	' ������lngValue ��
	' ------------------------------------------
	Public Property Let CalcClass(lngValue)
		m_lngCalcClass = lngValue
	End Property

	Public Property Get CalcClass()
		CalcClass = m_lngCalcClass
	End Property



	' ------------------------------------------
	' ���ס����ͤΥե����ޥåȡʼ¿����ǡ�
	' ������strValue			�͡�String��
	'		blnCurrencyEscape	�̲ߵ���Υ��������ײ��ݡ�\�ޡ�����
	'
	' ����͡��ե����ޥåȤ��줿��
	' ���͡�
	' ------------------------------------------
	Public Function fncComFormatNumberValue( strValue, blnCurrencyEscape )
		On Error Resume Next

		Dim lngValue		' �ƥ�������
		Dim curValue		' �Ѵ���ο���
		Dim aryValue		' �ƥ������͡��̲ߵ���ܶ�ۡ�
		Dim strAfValue		' �ե����ޥåȸ����
		Dim strCurrencySign	' �̲ߵ���
		Dim curAmount 		' ��� money


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

'		lngValue = Left(CStr(lngValue), NumberAfterDecimal)
		'msgbox "�����оݡ�" & curValue & vbcrlf & "���ͽ�����" & CalcClass & vbcrlf & "�ڼΤư��֡�" & DecimalCutPoint & vbcrlf & "�ե����ޥåȰ��֡�" & NumberAfterDecimal

		'��������ư���������� (Double) ���Ѵ�
		curValue = CDbl(lngValue)

		Select Case CalcClass
			' �������ʲ��ڼΤƽ���
			Case 1
				curValue = fncComDecimalCut( curValue, DecimalCutPoint , NumberAfterDecimal )
			' �������ʲ��ھ夲����
			Case 2
				curValue = fncComDecimalUp( curValue, DecimalCutPoint , NumberAfterDecimal )
			Case Else
				curValue = FormatNumber(curValue, NumberAfterDecimal, True, False)
		End Select
		
		strCalcValue = FormatNumber(curValue, NumberAfterDecimal, True, False)


		'���ͷ����˥ե����ޥåȤ��ơ����Υƥ����ȥܥå���������
		strAfValue = NumberCurrencySign & NumberConnectionChar & strCalcValue


		'���ݻ���\ �����������פ���Ƥ��ޤ����ᡢ\\ ���Ѵ�
		If blnCurrencyEscape Then
			FormatNumberValue = Replace(strAfValue, "\", "\\")
		Else
			FormatNumberValue = strAfValue
		End If

		'�������ֵ�
		fncComFormatNumberValue = True

		'�⤷���顼�������Ƥ������
		If( Err.number > 0 ) Then
			FormatNumberValue = c_strFormatNumberErr
		End If

	End Function

	' ------------------------------------------
	' ���ס����ͤ��ڼΤƽ���
	' ������
	'		curValue		-	�ڼΤ��оݤο���
	'		lngPoint		-	�ڼΤ��оݤξ���������ΰ���
	'		lngAfterDecimal	-	�ڼΤƸ�ο��ͤξ������ʲ���ʸ��0�����뤫
	'
	' ����͡��ڼΤƤ����͡����顼�ξ�� 0
	' ���͡��������ʲ��Τߤ��б������������ڼΤƽ����ˤ�̤�б���
	' ------------------------------------------
	Public Function fncComDecimalCut( curValue , lngPoint, lngAfterDecimal )

		Dim strValue		' �ڤ�Τ��оݤο��ͤ�ʸ�����Ѵ��������
		Dim aryValue
		Dim strNumber
		Dim strDecimal
		Dim curAfValue		' �ڼΤƸ�ο���
		Dim strZeroFormat	' 0��������

		'ʸ������Ѵ�
		strValue = CStr(curValue)
		
		If (InStr(strValue, c_strDecimalPointChar) > 0) Then
			'�̲ߵ���ν���
			aryValue = Split(strValue, c_strDecimalPointChar)
			strNumber  = aryValue(0)
			strDecimal = aryValue(1)
		Else
			strNumber  = strValue
			strDecimal = String( lngAfterDecimal, "0")
		End If

		' �ڼΤ��оݤξ��������֤ˤ��ե����ޥå�
		If (lngPoint = 0 ) Then
			curAfValue = strNumber & c_strDecimalPointChar & String( lngAfterDecimal, "0")
		ElseIf (lngPoint > 0 ) Then
			' �ե����ޥåȰ��֤������ڼΤư��֤���礭�����Τ�0���
			If( lngAfterDecimal > lngPoint ) Then
				strZeroFormat = String( lngAfterDecimal - lngPoint, "0")
			End If
			curAfValue = strNumber & c_strDecimalPointChar & Left(strDecimal, lngPoint) & strZeroFormat
		Else
			curAfValue = strValue
		End If

		' �ե����ޥåȤ������ͤ��ֵ�
		fncComDecimalCut = (curAfValue)

	End Function

	' ------------------------------------------
	' ���ס����ͤ��ھ夲����
	' ������
	'		curValue		-	�ھ夲�оݤο���
	'		lngPoint		-	�ھ夲�оݤξ���������ΰ���
	'		lngAfterDecimal	-	�ھ夲��ο��ͤξ������ʲ���ʸ��0�����뤫
	'
	' ����͡��ڼΤƤ����͡����顼�ξ�� 0
	' ���͡��������ʲ��Τߤ��б������������ھ夲�����ˤ�̤�б���
	' ------------------------------------------
	Public Function fncComDecimalUp( curValue , lngPoint, lngAfterDecimal )

		Dim strValue		' �ڤ�Τ��оݤο��ͤ�ʸ�����Ѵ��������
		Dim aryValue
		Dim strNumber
		Dim strDecimal
		Dim curAfValue		' �ڼΤƸ�ο���
		Dim strZeroFormat	' 0��������
		
		'ʸ������Ѵ�
		strValue = CStr(curValue)
		
		
		If (Instr(strValue, c_strDecimalPointChar) > 0) Then
			'�̲ߵ���ν���
			aryValue = Split(strValue, c_strDecimalPointChar)
			strNumber  = aryValue(0)
			strDecimal = aryValue(1)
		Else
			strNumber  = strValue
			strDecimal = String( lngAfterDecimal, "0")
		End If

		' �ڼΤ��оݤξ��������֤ˤ��ե����ޥå�
		If (lngPoint = 0 ) Then
			' �ڼΤưʲ��� 0 �Ǥ�̵����硢strNumber ���ھ夲
			If(  CInt(strDecimal) <> 0 ) Then
				strNumber = CStr(CInt(strNumber) + 1)
			End If
			' �ե����ޥåȡ�ex: 12345.00��
			curAfValue = strNumber & c_strDecimalPointChar & String( lngAfterDecimal, "0")
		ElseIf (lngPoint > 0 ) Then
			' �ڼΤưʲ���strDecimal��POINT���աˤ� 0 �Ǥ�̵����硢strDecimal��POINT���աˤ��ھ夲
			If(  CInt(Mid(strDecimal, lngPoint)) <> 0 ) Then
				strDecimal = CStr(CInt(Left(strDecimal, lngPoint)) + 1)
			End If
			' �ե����ޥåȰ��֤������ڼΤư��֤���礭�����Τ�0���
			If( lngAfterDecimal > lngPoint ) Then
				strZeroFormat = String( lngAfterDecimal - lngPoint, "0")
			End If
			curAfValue = strNumber & c_strDecimalPointChar & strDecimal & strZeroFormat
		Else
			curAfValue = strValue
		End If

		' �ե����ޥåȤ������ͤ��ֵ�
		fncComDecimalUp = (curAfValue)

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
		For lngCnt = 1 To Len(strValue)
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
		If Len(strDate) = 5 Then
			strDate = strDate & "01/01"
		End If
		'2003/10/ �η����ξ��
		If Len(strDate) = 8 Then
			strDate = strDate & "01"
		End If

		'���դ��Ѵ��Բ�ǽ�ʾ��
		If Not IsDate(strDate) Then
			fncComTextToDate = False
			Exit Function
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
			' objText.Value = fncComDate(Date, DateFormatNo )
			objText.select()
			'�ؿ����Ԥ��ֵ�
			Exit Function
		End If

		'�����ؤ��Ѵ�����ǽ����Ĵ�٤�
		If Not IsDate( dtmValue ) Then
			'�ƥ����Ȥ������շ������Ѵ���ĩ��
			If Not fncComTextToDate( dtmValue ) Then
				'���ͤؤ��Ѵ����Բ�ǽ�ʾ�硢�������դ򸵤Υƥ����ȥܥå���������
				' objText.Value = fncComDate(Date, DateFormatNo )
				objText.select()
				'�ؿ����Ԥ��ֵ�
				Exit Function
			End If
		End If

		'���շ����Ѵ�
		dtmValue = CDate(dtmValue)

		'Web�ѤΥ����å� 1600ǯ�ʲ��ξ��̵��
		if( DatePart("yyyy", dtmValue) <= 1600 ) Then
			dtmValue = fncComDate(Date, DateFormatNo )
		End If

		'���ͷ����˥ե����ޥåȤ��ơ����Υƥ����ȥܥå���������
		objText.Value = fncComDate(dtmValue, DateFormatNo )
		
		'�������ֵ�
		fncComFormatDateTime = True

	End Function


	' ------------------------------------------
	' ���ס����դΥե����ޥåȡ�OS��¸�ʤ��ǡ�
	' ������dtmValue	������
	'		lngFormatNo YYYY/MM�ξ�� 1
	'					YYYY/MM/DD �Ϥ���¾
	' ����͡��ե����ޥåȤ��줿����ʸ����
	' ���͡�
	' ------------------------------------------
	Public Function fncComDate( dtmValue, lngFormatNo )

		dtmDate  = FormatDateTime(dtmValue, vbGeneralDate)
 		strYear  = Year(dtmDate)
 		strMonth = Month(dtmDate)
 		strDay   = Day(dtmDate)

		' ������Τߡ��������å������ξ�����Ƭ�� 0 ���ղä��롣
		If Len(strMonth) = 1 Then
			strMonth = "0" & strMonth
		End If
		
		If Len(strDay) = 1 Then
			strDay = "0" & strDay
		End If

		' �ե����ޥåȥե饰�����ξ�� YYYY/MM �ե����ޥå�
		If lngFormatNo = 1 Then
			strDate = strYear & "/" & strMonth
		Else
			strDate = strYear & "/" & strMonth & "/" & strDay
		End If

		fncComDate = strDate

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
