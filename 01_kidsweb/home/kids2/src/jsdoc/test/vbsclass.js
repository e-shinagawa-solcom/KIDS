': -----------------------------------------------------------------------------
': ファイル概要：
':               VBScript 共通クラス
': 備考：
':       共通で使用するクラス群
':
': 作成日：2003/10/09
': 作成者：Kazushi Saito
': 修正履歴：
':
': -----------------------------------------------------------------------------


' ------------------------------------------
' グローバル変数定義
' ------------------------------------------
Public Const c_lngAfterDecimal = 4	'処理対象の小数点以下の桁数（丸め処理）


' ------------------------------------------
' 概要：チェッククラス
' 備考：数値、日付、のチェックを行なう
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
	
	
	
	Private m_strNumberCurrencySign		'通貨記号
	Private m_strNumberConnectionChar	'通貨記号と金額を接続する文字
	Private m_lngNumberAfterDecimal		'小数点以下の桁
	Private m_lngStringMaxLength		'文字列の最大数
	
	Private m_strFormatNumberValue		'fncComFormatNumberValue() によりフォーマットされた直後の値
	
	' ------------------------------------------
	' ------------------------------------------
	Private Sub Class_Initialize()
	
	
		'数値処理にて小数点以下の桁数を何桁にして処理するかを初期設定
		NumberAfterDecimal = c_lngAfterDecimal
	
	End Sub

	' ------------------------------------------
	' 概要：通貨記号
	' 引数：strValue 値
	' ------------------------------------------
	Public Property Let NumberCurrencySign(strValue)
		m_strNumberCurrencySign = strValue
	End Property

	Public Property Get NumberCurrencySign()
		NumberCurrencySign = m_strNumberCurrencySign
	End Property

	' ------------------------------------------
	' 概要：通貨記号と金額を接続する文字
	' 引数：strValue 値
	' ------------------------------------------
	Public Property Let NumberConnectionChar(strValue)
		m_strNumberConnectionChar = strValue
	End Property

	Public Property Get NumberConnectionChar()
		NumberConnectionChar = m_strNumberConnectionChar
	End Property

	' ------------------------------------------
	' 概要：小数点以下の計算を何桁にするか
	' 引数：lngValue 値
	' ------------------------------------------
	Public Property Let NumberAfterDecimal(lngValue)
		m_lngNumberAfterDecimal = lngValue
	End Property

	Public Property Get NumberAfterDecimal()
		NumberAfterDecimal = m_lngNumberAfterDecimal
	End Property


	' ------------------------------------------
	' 概要：文字列の最大数
	' 引数：lngValue 値
	' ------------------------------------------
	Public Property Let StringMaxLength(lngValue)
		m_lngStringMaxLength = lngValue
	End Property

	Public Property Get StringMaxLength()
		StringMaxLength = m_lngStringMaxLength
	End Property
	

	' ------------------------------------------
	' 概要：通貨記号
	' 引数：strValue 値
	' ------------------------------------------
	Public Property Let FormatNumberValue(strValue)
		m_strFormatNumberValue = strValue
	End Property

	Public Property Get FormatNumberValue()
		FormatNumberValue = m_strFormatNumberValue
	End Property


	' ------------------------------------------
	' 概要：数値のフォーマット
	' 引数：TextBoxオブジェクト
	' 戻り値：True/False 成功、失敗
	' 備考：
	' ------------------------------------------
'	Public Function fncComFormatNumber( objText )
'		On Error Resume Next
'
'		Dim lngValue		'テキスト値
'		Dim aryValue		'テキスト値（通貨記号＋金額）
'		Dim strCurrencySign	'通貨記号
'		Dim curAmount 		'金額 money
'		
'
'		'関数の戻り値を初期化
'		fncComFormatNumber = False
'
'		'オブジェクトの存在を確認。取得不可能な場合、失敗で返却
'		If IsEmpty( objText.value ) Then Exit Function
'
'		
'		'エラー判定
'		If Len(Trim(objText.value)) = 0 Then
'			'関数を成功で返却
'			fncComFormatNumber = True
'			Exit Function
'		End If
'		
'		' 接続文字がある場合
'		'プロパティ指定通貨記号と同じ通貨記号の場合、オブジェクトの値を変数に代入
'		If( (Instr(objText.value, NumberConnectionChar) > 0) AND (NumberConnectionChar <> "") ) Then
'			
'			'通貨記号の処理
'			aryValue = Split(objText.value, NumberConnectionChar)
'			strCurrencySign = aryValue(0)
'			curAmount = aryValue(1)
'			lngValue = curAmount
'			
'		'接続文字が無い場合
'		Else
'			'通貨記号があった場合、削除
'			lngValue = Replace( objText.value, NumberCurrencySign, "" )
'		End If
'
'		'数値への変換が可能かを調べる
''		If Not IsNumeric( lngValue ) Then
'			'関数を失敗で返却
'			Exit Function
'		End If
'		
'		'通貨型に変換
'		lngValue = CCur(lngValue)
'		'数値形式にフォーマットして、元のテキストボックスへ設定
'		objText.value = NumberCurrencySign & NumberConnectionChar & FormatNumber(lngValue, NumberAfterDecimal, True, False)
'		'objText.value = Round(lngValue, 4)
'		
'		
'		'成功を返却
'		fncComFormatNumber = True
'
'		'もしエラーが起きていた場合
'		If( Err.number > 0 ) Then
'			'fncComFormatNumber = False
'		End If
'	End Function

	' ------------------------------------------
	' 概要：数値のフォーマット（実数値版）
	' 引数：strValue			値（String）
	'		blnCurrencyEscape	通貨記号のエスケープ（\マーク）
	' 戻り値：フォーマットされた値
	' 備考：
	' ------------------------------------------
	Public Function fncComFormatNumberValue( strValue, blnCurrencyEscape )
		On Error Resume Next

		Dim lngValue		'テキスト値
		Dim aryValue		'テキスト値（通貨記号＋金額）
		Dim strCurrencySign	'通貨記号
		Dim curAmount 		'金額 money


		'関数の戻り値を初期化
		fncComFormatNumberValue = False

		' 存在を確認。取得不可能な場合、失敗で返却
		If IsEmpty( strValue ) Then Exit Function

		'エラー判定
		If Len(Trim(strValue)) = 0 Then
			'関数を成功で返却
			fncComFormatNumberValue = True
			Exit Function
		End If

		' 接続文字がある場合
		'プロパティ指定通貨記号と同じ通貨記号の場合、オブジェクトの値を変数に代入
		If( (Instr(strValue, NumberConnectionChar) > 0) AND (NumberConnectionChar <> "") ) Then
			
			'通貨記号の処理
			aryValue = Split(strValue, NumberConnectionChar)
			strCurrencySign = aryValue(0)
			curAmount = aryValue(1)
			lngValue = curAmount
			
		'接続文字が無い場合
		Else
			'通貨記号があった場合、削除
			lngValue = Replace( strValue, NumberCurrencySign, "" )
		End If

		'数値への変換が可能かを調べる
		If Not IsNumeric( lngValue ) Then
			'関数を失敗で返却
			Exit Function
		End If

		'通貨型に変換
		lngValue = CCur(lngValue)
		'数値形式にフォーマットして、元のテキストボックスへ設定
		strValue = NumberCurrencySign & NumberConnectionChar & FormatNumber(lngValue, NumberAfterDecimal, True, False)
		
		'値保持　\ がエスケープされてしまうため、\\ に変換
		If blnCurrencyEscape Then
			FormatNumberValue = Replace(strValue, "\", "\\")
		Else
			FormatNumberValue = strValue
		End If
		
		'成功を返却
		fncComFormatNumberValue = True

		'もしエラーが起きていた場合
		If( Err.number > 0 ) Then
			FormatNumberValue = "(Error)"
		End If

	End Function


	' ------------------------------------------
	' 概要：文字列から日付の作成
	' 引数：文字列
	' 戻り値：True/False 成功、失敗
	' 備考：文字列から日付が生成不可能な場合、本日日付を設定
	' ------------------------------------------
	Public Function fncComTextToDate( ByRef strValue )
		
		Dim strDate		'文字列日付
		fncComTextToDate = False
	
		'文字列分ループ処理
		For lngCnt = 1 To LenB(strValue)
			'一文字だけ取得
			strBuff = Mid( strValue, lngCnt, 1)
			'数値かどうかの判定
			If IsNumeric( strBuff ) Then
				strDate = strDate & strBuff
				'4,6文字目に/を設定
				Select Case lngCnt
					Case 4,6
						strDate = strDate & "/"
				End Select
				
			End If
		Next
		
		'2003/ の形式の場合
		If LenB(strDate) = 5 Then
			strDate = strDate & "01/01"
		End If
		'2003/10/ の形式の場合
		If LenB(strDate) = 8 Then
			strDate = strDate & "01"
		End If
		'日付に変換不可能な場合
		If Not IsDate(strDate) Then
			strDate = Date
		End If
		'引数値へ設定
		strValue = strDate
		
		'成功を返却
		fncComTextToDate = True
	End Function

	' ------------------------------------------
	' 概要：日時のフォーマット
	' 引数：TextBoxオブジェクト
	' 戻り値：True/False 成功、失敗
	' 備考：
	' ------------------------------------------
	Public Function fncComFormatDateTime( ByRef objText )

		Dim dtmValue	'テキスト値

		'関数の戻り値を初期化
		fncComFormatDateTime = False

		'オブジェクトの存在を確認。取得不可能な場合、失敗で返却
		If IsEmpty( objText.value ) Then Exit Function
		'オブジェクトの値を変数に代入
		dtmValue = objText.value

		'エラー判定
		If Len(Trim(dtmValue)) = 0 Then
			'関数を成功で返却
			fncComFormatDateTime = True
			Exit Function
		End If
		
		'エラー判定
		If Len(dtmValue) < 4 Then
			objText.Value = Date
			objText.select()
			'関数を失敗で返却
			Exit Function
		End If

		'日時への変換が可能かを調べる
		If Not IsDate( dtmValue ) Then
			'テキストから日付形式へ変換を挑戦
			If Not fncComTextToDate( dtmValue ) Then
				'数値への変換が不可能な場合、現在日付を元のテキストボックスへ設定
				objText.Value = Date
				objText.select()
				'関数を失敗で返却
				Exit Function
			End If
		End If

		'日付型に変換
		dtmValue = CDate(dtmValue)

		'Web用のチェック 1600年以下の場合無効
		if( DatePart("yyyy", dtmValue) <= 1600 ) Then
			dtmValue = Date
		End If

		'数値形式にフォーマットして、元のテキストボックスへ設定
		objText.Value = FormatDateTime(dtmValue, vbShortDate)

		'成功を返却
		fncComFormatDateTime = True

	End Function
	
	
	' ------------------------------------------
	' 概要：文字列の検査
	' 引数：TextBox or Textareaオブジェクト
	' 戻り値：True/False 成功、失敗
	' 備考：
	' ------------------------------------------
	Public Function fncComInspectionString( ByRef objText )
	
		strString = objText.value 'StrConv(objText.value, 1)
		
		MsgBox(LenB(strString) & ":" & strString)
		fncComInspectionString = True
		
	End Function

	' ------------------------------------------
	' 概要：オブジェクトのフォーマット
	' 引数：
	'       objObject	- TextBoxオブジェクト
	'       lngCheckID	- チェックID
	'                      1:数値処理
	'                      2:日付処理
	'
	' 戻り値：True/False 成功、失敗
	' 備考：
	'       この関数は、親WindowのJavaScript関数 fncErrorMessage() を呼び出します。
	'       同時に実装する必要があります。
	' ------------------------------------------
	Public Function fncCheckObjectValue( objObject, lngCheckID )
		On Error Resume Next
		
		'関数の戻り値を初期化
		fncCheckObjectValue = True
		
		Select Case lngCheckID
			' 数値処理
			Case 1
				If( fncComFormatNumberValue( objObject.value, False ) ) Then
					' フォーマット成功の場合、オブジェクトのValueへ直接指定
					objObject.value = FormatNumberValue
					Exit Function
				End If
			' 日付処理
			Case 2
				If( fncComFormatDateTime( objObject ) ) Then Exit Function
			' 文字列処理
			case 3
				If( fncComInspectionString( objObject ) ) Then Exit Function
		End Select
		
		'失敗を返却
		fncCheckObjectValue = False

	End Function

End Class
