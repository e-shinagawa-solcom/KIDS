' -----------------------------------------------------------------------------
' ファイル概要：
'               VBScript 共通関数
' 備考：
'       数値フォーマット、日付フォーマットを行なうための関数群です。
'
' 作成日：2003/10/07
' 作成者：Kazushi Saito
' 修正履歴：
'			2003/10/23	fncVBSCheckText()追加　：Kazushi Saito
'			2004/03/29	K.S
'				fncVBSCheckNumberValue() 小数点以下切捨て処理の追加
'				
'
' -----------------------------------------------------------------------------


	' ------------------------------------------
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
	
	
	' ------------------------------------------
	' 概要：JavaScript 結果値返却
	' 引数：
	'		lngCheckID		-	チェックID
	'		objValue		-	値
	'
	' 戻り値：True/False 成功、失敗
	' 備考：
	'		結果値は、本ファイル内のそれぞれの関数でユニークである必要がある
	' ------------------------------------------
	Public Function fncVBCheckSetValueToJS(lngCheckID, objValue)

		' JavaScript の関数を実行する
		Call window.execScript("fncCheckSetValue(" & lngCheckID & ", '" & objValue & "'); ", "JavaScript")

		fncVBCheckSetValueToJS = True
	End Function
	
	
	' ------------------------------------------
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

	' ------------------------------------------
	' 概要：数値のチェック（実数値版）
	' 引数：
	'		strValue			実数値（String）
	'		lngAfterDecimal		小数点以下の桁数
	'		strCurrencySign		通貨記号
	'		lngDecimalCutPoint	小数点以下の切捨て位置（ -1 の場合、切捨てしない）
	'		lngCalcCode			計算方法種別
	'
	' 戻り値：True/False 成功、失敗
	' 備考：
	'		lngDecimalCutPoint を指定すると、それ以下は全て0でフォーマットされ切捨てされる
	'	この関数の計算結果は fncVBCheckSetValueToJS() を使用してJS側へ設定されます。
	'	結果ID = 1
	' ------------------------------------------
	Public Function fncVBSCheckNumberValue(strValue, lngAfterDecimal, strCurrencySign, lngDecimalCutPoint, lngCalcCode)

		'戻り値の初期化
		fncVBSCheckNumberValue = False
	
		Dim objCheck
		Dim lngSetID
		Dim strConnectionChar

		' 結果ID
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
		

		'小数点以下を切り捨て処理する位置を設定 
		Select Case VarType(lngDecimalCutPoint)
			' 整数型 (Integer)/長整数型 (Long)/単精度浮動小数点数型 (Single)/倍精度浮動小数点数型 (Double)/通貨型 (Currency)/文字列型 (String)
			Case 2,3,4,5,6,8
				objCheck.DecimalCutPoint = CInt(lngDecimalCutPoint)
			' その他/ VarType() = 1 : Null 値 (無効な値)
			' 小数点以下を全て有効にする
			Case Else
				lngDecimalPointCharPoint = InStr( strValue, ".")
				If( lngDecimalPointCharPoint > 0 ) Then
					lngDecimalCutPoint = Len(Mid(strValue, lngDecimalPointCharPoint+1))
				End If
				objCheck.DecimalCutPoint = lngDecimalCutPoint
		End Select
		
		
		' 計算方法種別
		objCheck.CalcClass = lngCalcCode
		
		
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


	' ------------------------------------------
	' 概要：日付のチェック
	' 引数：
	'		objObject	TextBoxオブジェクト
	' 戻り値：True/False 成功、失敗
	' 備考：
	' ------------------------------------------
	Public Function fncVBSCheckDate(objObject, lngFormatNo)
	
		'戻り値の初期化
		fncVBSCheckDate = False
	
		Dim objCheck
		Dim lngCheckID
		
		'エラー番号
		lngCheckID = 2

		'チェックオブジェクトの作成
		Set objCheck = New clsCheck

		'フォーマットの指定
		objCheck.DateFormatNo = lngFormatNo

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


	' ------------------------------------------
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


	' ------------------------------------------
	' 概要：数値計算（VBS）
	' 引数：
	' 		lngCalc1		計算値１
	'		strOperator		算術演算子 ( ^, *, /, \, Mod, +, - )
	' 		lngCalc2		計算値２
	' 戻り値：True 成功
	' 備考：
	'	この関数の計算結果は fncVBCheckSetValueToJS() を使用してJS側へ設定されます。
	' 	結果ID = 5
	' ------------------------------------------
	Public Function fncVBSNumberCalculation(lngCalc1, strOperator, lngCalc2)
		On Error Resume Next

		Dim aryValue		' 計算式
		Dim lngResult		' 結果数値
		Dim lngSetID		' このスクリプト結果のID
		Dim varResult		' 結果返却値
		
		
		' 結果ID
		lngSetID = 5

		If ( StrComp(strOperator, "^", vbTextCompare) = 0) Then
			lngResult = CDbl(lngCalc1) ^ CDbl(lngCalc2)
		End If
		If ( StrComp(strOperator, "*", vbTextCompare) = 0) Then
			lngResult = CDbl(lngCalc1) * CDbl(lngCalc2)
		End If
		If ( StrComp(strOperator, "/", vbTextCompare) = 0) Then
			lngResult = CDbl(lngCalc1) / CDbl(lngCalc2)
		End If
		If ( StrComp(strOperator, "\", vbTextCompare) = 0) Then
			lngResult = CDbl(lngCalc1) \ CDbl(lngCalc2)
		End If
		If ( StrComp(strOperator, "Mod", vbTextCompare) = 0) Then
			lngResult = CDbl(lngCalc1) Mod CDbl(lngCalc2)
		End If
		If ( StrComp(strOperator, "+", vbTextCompare) = 0) Then
			lngResult = CDbl(lngCalc1) + CDbl(lngCalc2)
		End If
		If ( StrComp(strOperator, "-", vbTextCompare) = 0) Then
			lngResult = CDbl(lngCalc1) - CDbl(lngCalc2)
		End If
		
		' 結果値の設定
		varResult = lngResult
		
		' もしエラーが起きていた場合
		If( Err.number > 0 ) Then
			varResult = "(Error)"
			Err.Clear
		End If

		' JavaScript 側へ保持
		Call fncVBCheckSetValueToJS(lngSetID, varResult)
	
		' 関数の戻り値（一応）
		fncVBSNumberCalculation = True
	
	End Function
	
