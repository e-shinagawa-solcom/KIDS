' -----------------------------------------------------------------------------
' �ե����복�ס�
'               VBScript ���̴ؿ�
' ���͡�
'       ���ͥե����ޥåȡ����եե����ޥåȤ�Ԥʤ�����δؿ����Ǥ���
'
' ��������2003/10/07
' �����ԡ�Kazushi Saito
' ��������
'
' -----------------------------------------------------------------------------

	' ------------------------------------------
	' �����Х��ѿ����
	' ------------------------------------------
	Public Const c_lngAfterDecimal = 4	'�����оݤξ������ʲ��η���ʴݤ������

	' ------------------------------------------
	' ���ס����ͤΥե����ޥå�
	' ������TextBox���֥�������
	' ����͡�True/False ����������
	' ���͡�
	' ------------------------------------------
	Public Function fncComFormatNumber( objText )

		Dim lngValue	'�ƥ�������

		'�ؿ�������ͤ�����
		fncComFormatNumber = False

		'���֥������Ȥ�¸�ߤ��ǧ�������Բ�ǽ�ʾ�硢���Ԥ��ֵ�
		If IsEmpty( objText.value ) Then Exit Function

		'���֥������Ȥ��ͤ��ѿ�������
		lngValue = objText.value

		'���顼Ƚ��
		If Len(Trim(lngValue)) = 0 Then
			'�ؿ����������ֵ�
			fncComFormatNumber = True
			Exit Function
		End If

		'���ͤؤ��Ѵ�����ǽ����Ĵ�٤�
		If Not IsNumeric( lngValue ) Then
			'���ͤؤ��Ѵ����Բ�ǽ�ʾ�硢0�򸵤Υƥ����ȥܥå���������
			objText.Value = 0
			objText.select()
			'�ؿ����Ԥ��ֵ�
			Exit Function
		End If
		'msgbox(CStr(lngValue))
		'�̲߷����Ѵ�
		lngValue = CCur(lngValue)
		'���ͷ����˥ե����ޥåȤ��ơ����Υƥ����ȥܥå���������
		objText.value = FormatNumber(lngValue, c_lngAfterDecimal, True, False)
		'objText.value = Round(lngValue, 4)
		
		'�������ֵ�
		fncComFormatNumber = True
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
	' ���ס����ͤΥե����ޥå�
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
		
		'�ؿ�������ͤ�����
		fncCheckObjectValue = True
		
		Select Case lngCheckID
			' ���ͽ���
			Case 1
				If( fncComFormatNumber( objObject ) ) Then Exit Function
			' ���ս���
			Case 2
				If( fncComFormatDateTime( objObject ) ) Then Exit Function
		End Select
		
		'���Ԥ��ֵ�
		fncCheckObjectValue = False

		'��å�������ɽ��
		window.parent.fncErrorMessage(lngCheckID)

	End Function
