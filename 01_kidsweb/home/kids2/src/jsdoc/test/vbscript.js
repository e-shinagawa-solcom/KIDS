': -----------------------------------------------------------------------------
': ファイル概要：
':               VBScript 共通関数
': 備考：
':       数値フォーマット、日付フォーマットを行なうための関数群です。
':
': 作成日：2003/10/07
': 作成者：Kazushi Saito
': 修正履歴：
':			2003/10/23 fncVBSCheckText()追加　：Kazushi Saito
':
':
': -----------------------------------------------------------------------------


'@ ------------------------------------------
' 概要：JavaScript エラー関数の呼び出し
' 引数：
'		lngCheckID		-	チェックID
'		strObjectName	-	エラーオブジェクト名
'
' 戻り値：True/False 成功、失敗
' 備考：
' ------------------------------------------
Public Function fncVBCheckErrorToJavaScript(lngCheckID, strObjectName)

	' JavaScript の関数を実行する
	Call window.execScript("fncErrorMessage(" & lngCheckID & ", '" & strObjectName & "'); ", "JavaScript")

	fncVBCheckErrorToJavaScript = True
End Function


'@ ------------------------------------------
' 概要：JavaScript の関数を実行する
' 引数：
'		lngCheckID		-	チェックID
'		objValue	-		オブジェクト名
'
' 戻り値：True/False 成功、失敗
' 備考：
' ------------------------------------------
Public Function fncVBCheckSetValueToJS(lngCheckID, objValue)

	' JavaScript の関数を実行する
	Call window.execScript("fncCheckSetValue(" & lngCheckID & ", '" & objValue & "'); ", "JavaScript")

	fncVBCheckSetValueToJS = True
End Function


'@ ----------------------------------------
' 概要：数値のチェック
' 引数：
'		objObject	TextBoxオブジェクト
'		lngAfterDecimal	小数点以下の桁数
'		strCurrencySign	通貨記号
'
' 戻り値：True/False 成功、失敗
' 備考：
' ------------------------------------------
Public Function fncVBSCheckNumber(objObject, lngAfterDecimal, strCurrencySign)


	'戻り値の初期化
	fncVBSCheckNumber = False

	Dim objCheck
	Dim lngCheckID
	Dim strConnectionChar
	
	
	'エラー番号
	lngCheckID = 1
	
	'チェックオブジェクトの作成
	Set objCheck = New clsCheck
	
	'通貨記号の指定
	objCheck.NumberCurrencySign = strCurrencySign

	'接続文字をスペースにする
	strConnectionChar = " "
	
	'通貨記号の指定が空だった場合、接続文字も空にする
	If( strCurrencySign = "" ) Then
		strConnectionChar = ""
	End If
	'通貨記号と金額を接続する文字
	objCheck.NumberConnectionChar = strConnectionChar

	'小数点以下の処理桁数の指定
	objCheck.NumberAfterDecimal = lngAfterDecimal
	'値のチェック
	blnRet =  objCheck.fncCheckObjectValue(objObject, 1)
	If( blnRet ) Then
		'チェック成功の場合
		fncVBSCheckNumber = True
		lngCheckID = 0
	Else
		' エラーを選択
		objObject.select()
	End If

	'エラーの場合
	Call fncVBCheckErrorToJavaScript( lngCheckID, objObject.name )


End Function

'@ -----------------------------------------
' 概要：数値のチェック（実数値版）
' 引数：
'		strValue		実数値（String）
'		lngAfterDecimal	小数点以下の桁数
'		strCurrencySign	通貨記号
'
' 戻り値：True/False 成功、失敗
' 備考：
' ------------------------------------------
Public Function fncVBSCheckNumberValue(strValue, lngAfterDecimal, strCurrencySign)

	'戻り値の初期化
	fncVBSCheckNumberValue = False

	Dim objCheck
	Dim lngSetID
	Dim strConnectionChar


	'エラー番号
	lngSetID = 1
	
	'チェックオブジェクトの作成
	Set objCheck = New clsCheck
	
	'通貨記号の指定
	objCheck.NumberCurrencySign = strCurrencySign

	'接続文字をスペースにする
	strConnectionChar = " "
	
	'通貨記号の指定が空だった場合、接続文字も空にする
	If( strCurrencySign = "" ) Then
		strConnectionChar = ""
	End If
	'通貨記号と金額を接続する文字
	objCheck.NumberConnectionChar = strConnectionChar

	'小数点以下の処理桁数の指定
	objCheck.NumberAfterDecimal = lngAfterDecimal
	
	'値のチェック
	If Not objCheck.fncComFormatNumberValue(strValue, True) Then
		' エラー
		Exit Function
	End If

	' 値を取得
	strRetValue = objCheck.FormatNumberValue
	
	' JavaScript 側へ保持
	Call fncVBCheckSetValueToJS(lngSetID, strRetValue)

	' 成功を返却
	fncVBSCheckNumberValue = True
	
End Function


'@ ------------------------------------------
' 概要：日付のチェック
' 引数：
'		objObject	TextBoxオブジェクト
' 戻り値：True/False 成功、失敗
' 備考：
' ------------------------------------------
Public Function fncVBSCheckDate(objObject)

	'戻り値の初期化
	fncVBSCheckDate = False

	Dim objCheck
	Dim lngCheckID
	
	'エラー番号
	lngCheckID = 2

	'チェックオブジェクトの作成
	Set objCheck = New clsCheck

	'値のチェック
	blnRet =  objCheck.fncCheckObjectValue(objObject, 2)
	If( blnRet ) Then
		'チェック成功の場合
		fncVBSCheckDate = True
		lngCheckID = 0
	End If

	'エラーの場合
	'メッセージの表示
	Call fncVBCheckErrorToJavaScript( lngCheckID, objObject.name )


End Function


'@ ------------------------------------------
' 概要：文字のチェック
' 引数：
'		objObject	TextBoxオブジェクト
' 戻り値：True/False 成功、失敗
' 備考：
' ------------------------------------------
Public Function fncVBSCheckString(objObject, lngMaxLength)

	'戻り値の初期化
	fncVBSCheckString = False

	Dim objCheck
	Dim lngCheckID
	
	'エラー番号
	lngCheckID = 3

	'チェックオブジェクトの作成
	Set objCheck = New clsCheck

	'文字列最大数の指定
	objCheck.StringMaxLength = lngMaxLength

	'値のチェック
	blnRet =  objCheck.fncCheckObjectValue(objObject, 3)
	If( blnRet ) Then
		'チェック成功の場合
		fncVBSCheckString = True
		Exit Function
	End If

	'aryArgs(0) = "100";

	'エラーの場合
	'メッセージの表示
	'window.parent.fncErrorMessage(3)
	Call fncVBCheckErrorToJavaScript( lngCheckID, objObject.name )
	
End Function
