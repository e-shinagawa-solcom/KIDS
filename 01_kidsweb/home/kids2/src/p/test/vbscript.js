' -----------------------------------------------------------------------------
' ファイル概要：
'               VBScript 共通関数
' 備考：
'       数値フォーマット、日付フォーマットを行なうための関数群です。
'
' 作成日：2003/10/07
' 作成者：Kazushi Saito
' 修正履歴：
'
' -----------------------------------------------------------------------------

	' ------------------------------------------
	' グローバル変数定義
	' ------------------------------------------
	Public Const c_lngAfterDecimal = 4	'処理対象の小数点以下の桁数（丸め処理）

	' ------------------------------------------
	' 概要：数値のフォーマット
	' 引数：TextBoxオブジェクト
	' 戻り値：True/False 成功、失敗
	' 備考：
	' ------------------------------------------
	Public Function fncComFormatNumber( objText )

		Dim lngValue	'テキスト値

		'関数の戻り値を初期化
		fncComFormatNumber = False

		'オブジェクトの存在を確認。取得不可能な場合、失敗で返却
		If IsEmpty( objText.value ) Then Exit Function

		'オブジェクトの値を変数に代入
		lngValue = objText.value

		'エラー判定
		If Len(Trim(lngValue)) = 0 Then
			'関数を成功で返却
			fncComFormatNumber = True
			Exit Function
		End If

		'数値への変換が可能かを調べる
		If Not IsNumeric( lngValue ) Then
			'数値への変換が不可能な場合、0を元のテキストボックスへ設定
			objText.Value = 0
			objText.select()
			'関数を失敗で返却
			Exit Function
		End If
		'msgbox(CStr(lngValue))
		'通貨型に変換
		lngValue = CCur(lngValue)
		'数値形式にフォーマットして、元のテキストボックスへ設定
		objText.value = FormatNumber(lngValue, c_lngAfterDecimal, True, False)
		'objText.value = Round(lngValue, 4)
		
		'成功を返却
		fncComFormatNumber = True
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
	' 概要：数値のフォーマット
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
		
		'関数の戻り値を初期化
		fncCheckObjectValue = True
		
		Select Case lngCheckID
			' 数値処理
			Case 1
				If( fncComFormatNumber( objObject ) ) Then Exit Function
			' 日付処理
			Case 2
				If( fncComFormatDateTime( objObject ) ) Then Exit Function
		End Select
		
		'失敗を返却
		fncCheckObjectValue = False

		'メッセージの表示
		window.parent.fncErrorMessage(lngCheckID)

	End Function
