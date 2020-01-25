<?php
/**
*       発注管理　関数群
*
*       @package   kuwagata
*       @license   http://www.wiseknot.co.jp/
*       @copyright Copyright &copy; 2003, Wiseknot
*       @author    Hiroki Watanabe <h-watanabe@wiseknot.co.jp>
*       @access    public
*       @version   1.00
*
*       処理概要
*       
*	更新履歴
*
*	2004.03.02	明細行のチェック関数から単価、税抜き金額の 0円 計上、マイナス値計上を認めるように修正
*	2004.03.25	fncDetailHidden関数で明細行番号についても渡すように処理
*
*/

/*

fncDiscodeToCodeのcase3の処理をしていない
fncDiscodeToCode値がない場合のnull値(テスト用なので終わったら消す）
fncCheckData荷姿単価と荷姿単位のチェックをしてない、option値が生成されていないため
fnccheckの換算レートdisableからpostされてないのでこめんと

*/
	// 読み込み
	// -----------------------------------------------------------------
	/**		fncDetailError()関数
	*
	*
	*		@param String	bytErrorFlag2		// 処理番号
	*		@return	STring	$strDetailErrorMessage			//
	*/
	// -----------------------------------------------------------------
	
	function fncDetailError( $bytErrorFlag2 )
	{
	
		// エラーがあれば「TRUE」が返ってくるのでその行数番号を記録
		for( $i = 0; $i < count( $bytErrorFlag2 ); $i++ ) 
		{
			if( $bytErrorFlag2[$i] == TRUE )
			{
				$aryNumber[] = $i+1;
			}
		}
		
		
		if( is_array( $aryNumber ) )
		{
			
			for( $i = 0; $i < count( $aryNumber ); $i++ )
			{
				$aryDetailErrorMessage[] = "明細行 ".$aryNumber[$i]."行目：エラー ";
			}
		}
		
		if( is_array( $aryDetailErrorMessage ) )
		{
			$strDetailErrorMessage = implode( " : ", $aryDetailErrorMessage );
			//echo "strDetailErrorMessage : $strDetailErrorMessage<br>";
			
		}
		else
		{
			$strDetailErrorMessage = "";
		}
		
		return $strDetailErrorMessage;
	
	}
	
	
	
	
	
	
	// -----------------------------------------------------------------
	/**		funPulldownMenu()関数
	*
	*		プルダウンメニューの生成
	*
	*		@param Long		$lngProcessNo		// 処理番号
	*		@param Long		$lngValueCode		// value値
	*		@param String	$strWhere			// 条件
	*		@param Object	$objDB				// DB接続オブジェクト
	*		@return	Array	$strPulldownMenu
	*/
	// -----------------------------------------------------------------
	
	
	function fncPulldownMenu ( $lngProcessNo, $lngValueCode , $strWhere, $objDB )
	{

		switch ( $lngProcessNo )
		{
			case 0:		// 通貨
				$strPulldownMenu = fncGetPulldown3( "m_monetaryunit", "strmonetaryunitsign", "strmonetaryunitname", $lngValueCode, $strWhere, $objDB );
				break;
			case 1:		// レートタイプ
				$strPulldownMenu = fncGetPulldown( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $lngValueCode, '', $objDB );
				break;
			case 2:		// 支払条件
				$strPulldownMenu = fncGetPulldown( "m_paycondition", "lngpayconditioncode", "strpayconditionname", $lngValueCode, $strWhere, $objDB );
				break;
			case 3:		// 仕入科目
				$strPulldownMenu = fncGetPulldown2( "m_stocksubject", "lngstocksubjectcode", "strstocksubjectname", $lngValueCode, 'WHERE bytdisplayflag = TRUE AND bytinvalidflag = FALSE', $objDB );
				break;
			case 4:		// 仕入部品
				$strPulldownMenu = "";
				break;
			case 5:		// 単価リスト
				$strPulldownMenu = "";
				break;
			case 6:		// 運搬方法
				$strPulldownMenu = fncGetPulldown( "m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", $lngValueCode,'', $objDB );
				break;
			case 7:		// 製品単位
				$strPulldownMenu = fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $lngValueCode, "WHERE bytproductconversionflag =true", $objDB );
				break;
			case 8:		// 製品単位
				$strPulldownMenu = fncGetPulldown("m_productunit", "lngProductUnitCode", "strProductUnitName", $lngValueCode, "WHERE bytpackingconversionflag=true", $objDB );
				break;
			case 9:		// 発注状態
				$strPulldownMenu = fncGetPulldown( "m_OrderStatus", "lngorderstatuscode", "strorderstatusname", $lngValueCode,'', $objDB );
				break;
			case 10:	// 売上区分(受注）
				$strPulldownMenu = fncGetPulldown( "m_salesclass", "lngsalesclasscode", "strsalesclassname", $lngValueCode,'', $objDB );
				break;
		}
		return $strPulldownMenu;
	}
	
	
	
	// -----------------------------------------------------------------
	/**		fncGetPulldown2()関数　lib/lib.phpをコピーして使用に・・・
	*		lib/lib.phpを汎用性にすると負荷かかりそうなのでここに特別に作りました。
	*		プルダウンの表示が nameだけでなくcodeも一緒に表示
	*		プルダウンメニューの生成
	*
	*/
	// -----------------------------------------------------------------
	function fncGetPulldown2( $strTable, $strValueFieldName, $strDisplayFieldName, $lngDefaultValue, $strQueryWhere, $objDB )
	{
	        // 全ページIDのリストを取得
	        $strQuery = "SELECT $strValueFieldName, $strDisplayFieldName FROM $strTable $strQueryWhere ORDER BY $strValueFieldName";
	        
	        
			// クエリー実行 =====================================
			if ( !$lngResultID = $objDB->execute( $strQuery ) )
			{
				echo "クエリーエラー";
				//fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
			}
			
			
			// 値をとる =====================================
			if( $lngResultNum = pg_num_rows ( $lngResultID ) )
			{
				for( $i = 0; $i < $lngResultNum; $i++ )
				{
					$aryResut[] = pg_fetch_array( $lngResultID, $i, PGSQL_ASSOC );
				}
			}
			

	        if ( !$lngResultNum )
	        {
	                return FALSE;
	        }


	        // <OPTION>生成
	        for ( $count = 0; $count < $lngResultNum; $count++ )
	        {
	                $aryResult = $objDB->fetchArray( $lngResultID, $count );

	                // HTML出力
	                if ( $lngDefaultValue == $aryResult[0] )
	                {
	                        //$strHtml .= "<OPTION VALUE=\"$aryResult[$strValueFieldName]&nbsp;$aryResult[$strDisplayFieldName]\" SELECTED>".$aryResult[$strValueFieldName]."&nbsp;&nbsp;".$aryResult[$strDisplayFieldName]."</OPTION>\n";
	                        $strHtml .= "<OPTION VALUE=\"$aryResult[$strValueFieldName]\" SELECTED>".$aryResult[$strValueFieldName]."&nbsp;".$aryResult[$strDisplayFieldName]."</OPTION>\n";
	                }
	                else
	                {
	                        //$strHtml .= "<OPTION VALUE=\"$aryResult[$strValueFieldName]&nbsp;$aryResult[$strDisplayFieldName]\">".$aryResult[$strValueFieldName]."&nbsp;&nbsp;".$aryResult[$strDisplayFieldName]."</OPTION>\n";
	                        $strHtml .= "<OPTION VALUE=\"$aryResult[$strValueFieldName]\">".$aryResult[$strValueFieldName]."&nbsp;".$aryResult[$strDisplayFieldName]."</OPTION>\n";
	                }
	        }

	        $objDB->freeResult( $lngResultID );
	        return $strHtml;
	}


	// -----------------------------------------------------------------
	/**		fncGetPulldown3()関数　lib/lib.phpをコピーして使用に・・・（通貨専用）
	*		lib/lib.phpを汎用性にすると負荷かかりそうなのでここに特別に作りました。
	*		通貨のプルダウンだけvalue値がcodeではない<option value="\">日本円</option>
	*		その時にcodeでソートしたい。
	*		プルダウンメニューの生成
	*
	*/
	// -----------------------------------------------------------------
	function fncGetPulldown3( $strTable, $strValueFieldName, $strDisplayFieldName, $lngDefaultValue, $strQueryWhere, $objDB )
	{
		// 全ページIDのリストを取得
		$strQuery = "SELECT $strValueFieldName, $strDisplayFieldName FROM $strTable $strQueryWhere ORDER BY lngmonetaryunitcode";

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( !$lngResultNum )
		{
			return FALSE;
		}

		$lngFieldsCount = $objDB->getFieldsCount( $lngResultID );
		// <OPTION>生成
		for ( $count = 0; $count < $lngResultNum; $count++ ) {
			$aryResult = $objDB->fetchArray( $lngResultID, $count );

			$strDisplayValue = "";
			for ( $i = 1; $i < $lngFieldsCount; $i++ )
			{
				$strDisplayValue .= "$aryResult[$i]";
			}

			// HTML出力

			if ( $lngDefaultValue == $aryResult[0] )
			{
				$strHtml .= "<OPTION VALUE=\"$aryResult[0]\" SELECTED>$strDisplayValue</OPTION>\n";
			}
			else
			{
				$strHtml .= "<OPTION VALUE=\"$aryResult[0]\">$strDisplayValue</OPTION>\n";
			}
		}

		$objDB->freeResult( $lngResultID );
		return $strHtml;
	}


	// -----------------------------------------------------------------
	/**		fncDiscodeToCode()関数
	*
	*		displayCode→Code
	*
	*		@param Long		$strColumnName		// カラム名
	*		@param Long		$lngValueCode		// value値
	*		@param Object	$objDB				// DB接続オブジェクト
	*		@return	long	$lngCode			//
	*/
	// -----------------------------------------------------------------
	
	function fncDiscodeToCode ( $strColumnName, $strDisplayCode , $objDB )
	{
		switch ( $strColumnName )
		{
			case 'lnginchargegroupcode':	// グループコード（部門）
				$lngCode = fncGetMasterValue("m_group", "strgroupdisplaycode", "lnggroupcode", $strDisplayCode . ":str",'',$objDB);
				$lngCode = ( $lngCode != "") ? $lngCode : "null";
				break;
			case 'lnginchargeusercode':		// ユーザコード（担当者）
				$lngCode = fncGetMasterValue("m_user", "struserdisplaycode" ,"lngusercode" , $strDisplayCode . ":str",'',$objDB);
				$lngCode = ( $lngCode != "" ) ? $lngCode : "null";
				break;
			case 'lnglocationcode':			// 納品場所
				$lngCode = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $strDisplayCode . ":str", '',$objDB);
				$lngCode = ( $lngCode != "" ) ? $lngCode : "null";
				break;
			case 'lngcustomercode':			//会社コード(仕入先)
				$lngCode = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $strDisplayCode . ":str", '',$objDB);
				$lngCode = ( $lngCode != "" ) ? $lngCode : "null";
				break;
		}
		
		return $lngCode;
	
	}



	// -----------------------------------------------------------------
	/**		fncCodeToDisplayCode()関数
	*
	*		Code→displayCode
	*
	*		@param String	$strValue			// codeカラム名
	*		@param Long		$lngCode			// code値
	*		@param Object	$objDB				// DB接続オブジェクト
	*		@return	String	$strDisplayCode		//
	*/
	// -----------------------------------------------------------------
	
	function fncCodeToDisplayCode ( $strValue , $lngCode , $objDB )
	{
		if( $strValue == "lnginchargegroupcode" )
		{
			// グループコード（部門）
			$strDisplayCode = fncGetMasterValue("m_group", "lnggroupcode", "strgroupdisplaycode || ',' || strgroupdisplayname", $lngCode,'',$objDB);
		}
		elseif( $strValue == "lnginchargeusercode")
		{
			// ユーザコード（担当者）
			$strDisplayCode = fncGetMasterValue("m_user", "lngusercode", "struserdisplaycode || ',' || struserdisplayname", $lngCode,'',$objDB);
		}
		elseif( $strValue == "lnglocationcode" )
		{
			// 納品場所
			$strDisplayCode = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode || ',' || strcompanydisplayname", $lngCode, '',$objDB);
		}
		else
		{
			//会社コード(仕入先)
			$strDisplayCode = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode || ',' || strcompanydisplayname", $lngCode, '',$objDB);
		}

		return $strDisplayCode;
	
	}

	// -----------------------------------------------------------------
	/**		fncCodeToDisplayCode()関数
	*
	*		Code→displayCode
	*
	*		@param String	$strValue			// codeカラム名
	*		@param Long		$lngCode			// code値
	*		@param Object	$objDB				// DB接続オブジェクト
	*		@return	String	$strDisplayCode		//
	*/
	// -----------------------------------------------------------------
	
	function fncDisCodeToDisplayName ( $strValue , $strCode , $objDB )
	{
		if( $strValue == "lnginchargegroupcode" )
		{
			// グループコード（部門）
			$strDisplayCode = fncGetMasterValue("m_group", "strgroupdisplaycode", "strgroupdisplayname", $strCode. ":str",'',$objDB);
		}
		elseif( $strValue == "lnginchargeusercode")
		{
			// ユーザコード（担当者）
			$strDisplayCode = fncGetMasterValue("m_user", "struserdisplaycode", "struserdisplayname", $strCode. ":str",'',$objDB);
		}
		elseif( $strValue == "lnglocationcode" )
		{
			// 納品場所
			$strDisplayCode = fncGetMasterValue("m_company", "strcompanydisplaycode", "strcompanydisplayname", $strCode. ":str", '',$objDB);
		}
		else
		{
			//会社コード(仕入先)
			$strDisplayCode = fncGetMasterValue("m_company", "strcompanydisplaycode", "strcompanydisplayname", $strCode. ":str", '',$objDB);
		}

		return $strDisplayCode;
	
	}



	// -----------------------------------------------------------------
	/**		fncChangeData()関数
	*
	*		$_POSTのデータにdisplayCodeがはいっていたらCodeに変換し
	*		配列を作り直す
	*
	*		インサート用 空データはnullを代入する。
	*		
	*		配列$aryDisplayCodeの番号はfncDiscodeToCodeのプロセスIDと同じ
	*
	*		@param	Array	$aryData			// value値
	*		@param	String	$strNull			// 値がない場合にnullに変換させるか
	*		@param	Object	$objDB				// DB接続オブジェクト
	*		@return	long	$lngCode			//
	*/
	// -----------------------------------------------------------------
	
	
	function fncChangeData( $aryData, $objDB  )
	{
	
//		$aryDisplayCode[0] = "lngInChargeGroupCode";		// グループコード（部門）
//		$aryDisplayCode[1] = "lngInChargeUserCode";			// ユーザコード（担当者）
//		$aryDisplayCode[3] = "lngLocationCode";				// 納品場所
//		$aryDisplayCode[4] = "lngCustomerCode";				// 会社コード(仕入先)
		$aryDisplayCode["lnglocationcode"]			= "strLocationName";			// 納品場所
		$aryDisplayCode["lngcustomercode"]			= "strCustomerName";			// 会社コード(仕入先)

		for( $i = 0; $i < count( $aryData ); $i++ )
		{
			list( $strKeys, $strValues ) = each( $aryData );
			
			reset( $aryDisplayCode );
			for ( $j = 0; $j < count( $aryDisplayCode ); $j++ )
			{
				if( $strKeys == $aryDisplayCode[$j] )
				{
					$aryNewData[$strKeys] = fncDiscodeToCode( $strKeys, $strValues , $objDB );
					break;
				}
			}
			
			if( $j == count( $aryDisplayCode ) )
			{
				$strValues = ($strValues == "") ? "null" : $strValues;
				$aryNewData[$strKeys] = $strValues;
			}
		}
		
		return $aryNewData;
	}



	// -----------------------------------------------------------------
	/**		fncChangeData2()関数
	*
	*		修正画面用のデータ変換
	*		SQLから値をとってきたときに使う
	*		codeからdispalaycodeとdisplayNameを求める
	*		POST値が空白の場合はそのまま
	*		
	*		配列$aryDisplayCodeの番号はfncDiscodeToCodeのプロセスIDと同じ
	*
	*		@param	Array	$aryData			// value値
	*		@param	String	$strNull			// 値がない場合にnullに変換させるか
	*		@param	Object	$objDB				// DB接続オブジェクト
	*		@return	long	$lngCode			//
	*/
	// -----------------------------------------------------------------
	
	
	// 修正用 displaycodeがあったらdispalaynameも生成
	function fncChangeData2( $aryData, $objDB )
	{
	
//		$aryDisplayCode["lnginchargegroupcode"]		= "strInChargeGroupName";		// グループコード（部門）
//		$aryDisplayCode["lnginchargeusercode"]		= "strInChargeUserName";		// ユーザコード（担当者）
		$aryDisplayCode["lnglocationcode"]			= "strLocationName";			// 納品場所
		$aryDisplayCode["lngcustomercode"]			= "strCustomerName";			// 会社コード(仕入先)
		
		for( $i = 0; $i < count( $aryData ); $i++ )
		{
			list( $strKeys, $strValues ) = each( $aryData );
			
			reset( $aryDisplayCode );
			for ( $j = 0; $j < count( $aryDisplayCode ); $j++ )
			{
				list ( $strKeys2, $strValues2 ) = each( $aryDisplayCode );
				
				if( strcasecmp($strKeys, $strKeys2) == 0 )
				{
					$strDisplayValue = fncCodeToDisplayCode($strKeys2, $strValues, $objDB );
					
					$aryDisplayValue = array();
					$aryDispalyValue = explode(',', $strDisplayValue);
			
					$aryNewData[$strKeys2] = $aryDispalyValue[0];
					$aryNewData[$strValues2] = $aryDispalyValue[1];
					break;
				}
			}
			
			if( $j == count( $aryDisplayCode ) )
			{
				// 修正用
				$aryNewData[$strKeys] = $strValues;
			}
		}
		
		return $aryNewData;
	}
		
	// -----------------------------------------------------------------
	/**		fncChangeData3()関数
	*
	*		エラーで戻った場合・「戻る」ボタンで戻った時に使う
	*		dispalaycodeからdisplayNameを求める
	*		POST値が空白の場合はそのまま
	*		
	*		配列$aryDisplayCodeの番号はfncDiscodeToCodeのプロセスIDと同じ
	*
	*		@param	Array	$aryData			// value値
	*		@param	String	$strNull			// 値がない場合にnullに変換させるか
	*		@param	Object	$objDB				// DB接続オブジェクト
	*		@return	long	$lngCode			//
	*/
	// -----------------------------------------------------------------
	
	
	// 修正用 displaycodeがあったらdispalaynameも生成
	function fncChangeData3( $aryData, $objDB  )
	{
	
//		$aryDisplayCode["lnginchargegroupcode"]		= "strInChargeGroupName";		// グループコード（部門）
//		$aryDisplayCode["lnginchargeusercode"]		= "strInChargeUserName";		// ユーザコード（担当者）
		$aryDisplayCode["lnglocationcode"]			= "strLocationName";			// 納品場所
		$aryDisplayCode["lngcustomercode"]			= "strCustomerName";			// 会社コード(仕入先)
		
		for( $i = 0; $i < count( $aryData ); $i++ )
		{
			list( $strKeys, $strValues ) = each( $aryData );
			
			reset( $aryDisplayCode );
			for ( $j = 0; $j < count( $aryDisplayCode ); $j++ )
			{
				list ( $strKeys2, $strValues2 ) = each( $aryDisplayCode );
				
				if( strcasecmp($strKeys, $strKeys2) == 0 )
				{
					$strDisplayName = fncDisCodeToDisplayName($strKeys2, $strValues, $objDB );
			
					//2007.08.10 matsuki update start
					/*
					strcasecmpで$aryDataの配列要素と$aryDisplayCodeの配列要素のマッチングを大文字小文字無視で行い、
					マッチしていればfncDisCodeToDisplayName関数で、コードに該当する文字列をDBから参照している。
					ここで、fncDisCodeToDisplayName内では第一引数$strKeys2をcase文で利用して分岐を行っているので、
					第一引数が小文字のみの文字列(lngcustomercodeなど)でないと関数が成り立たない。
					ここまでは良いのだが…
					
					旧文言では以上に続いて
					
					$aryNewData[$strKeys2] = $strValues;
					$aryNewData[$strValues2] = $strDisplayName;
					
					となっている。
					つまり
					$aryNewData["lnglocationcode"]と$aryNewData["lngcustomercode"]に値が収納される。
					元の$aryDataでは大文字を含む表記法であり、
					$aryData["lngCustomerCode"]は存在しないものとなってしまう。
					なので以下のように修正。
					*/
					$aryNewData[$strKeys] = $strValues;
					$aryNewData[$strValues2] = $strDisplayName;
					//2007.08.10 matsuki update end
					

					break;
				}
			}
			
			if( $j == count( $aryDisplayCode ) )
			{
				
				$aryNewData[$strKeys] = $strValues;
			}
		}
		
		return $aryNewData;
	}

	// -----------------------------------------------------------------
	/**		fncCheckData_po()関数
	*
	*		submitされたデータをチェックする
	*
	*		@param Array	$aryData			// submitされた値
	*		@param Object	$objDB				// DB接続オブジェクト
	*		@return	Array	$aryError
	*/
	// -----------------------------------------------------------------

	function fncCheckData_po( $aryData, $strPart, $objDB )
	{
		if($strPart == "header")
		{
		
			$aryCheck["dtmOrderAppDate"]				= "null:date";			// 計上日
			$aryCheck["strOrderCode"]					= "";				// 発注No
			$aryCheck["lngCustomerCode"]				= "null";			// 仕入先
			//$aryCheck["lngInChargeGroupCode"]			= "null";			// 部門コード
			//$aryCheck["lngInChargeUserCode"]			= "null";			// 担当者
			$aryCheck["lngLocationCode"]				= "null";			// 納品場所
			$aryCheck["dtmExpirationDate"]				= "null:date";		// 発注有効期限日
			$aryCheck["lngOrderStatusCode"]				= "";				// 状態(オプション値)
			$aryCheck["lngMonetaryUnitCode"]			= "null";			// 通貨


			if( $aryData["lngMonetaryUnitCode"] != DEF_MONETARY_CODE_YEN )	//通貨が日本以外
			{
				//$aryCheck["lngMonetaryRateCode"]		= "number(0,99)";	// レートタイプ
				//$aryCheck["curConversionRate"]			= "null";			// 換算レート

				$aryCheck["lngPayConditionCode"]	= "number(1,99,The list has not been selected.)";	// 支払条件

				if($_COOKIE["lngLanguageCode"])
				{
					$aryCheck["lngPayConditionCode"] = "number(1,99,リストが選択されていません。)";
				}
			}
		}
		else
		{
			$aryCheck["strProductCode"]					= "null";				// 製品
			$aryCheck["strStockSubjectCode"]			= "number(1,999999999)";				// 仕入科目
			$aryCheck["strStockItemCode"]				= "number(1,999999999)";				// 仕入部品
			$aryCheck["lngConversionClassCode"]			= "null";				// 製品単位計上
			$aryCheck["lngProductUnitCode"]				= "null";				// 荷姿単位
			$aryCheck["lngGoodsQuantity"]				= "null";				// 製品数量
			$aryCheck["curTotalPrice"]					= "null:money(0,99999999999999)";				// 税抜金額
			$aryCheck["dtmDeliveryDate"]				= "null";

		}
		
		// チェック関数呼び出し
		$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
		
		// print_r( $aryCheckResult );
		list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );
		return array ( $aryData, $bytErrorFlag );
	
	}
	
	
	
	// -----------------------------------------------------------------
	/**		fncWorkFlow()関数
	*
	*		承認ルートの検索
	*
	*		@param String	$strUserCode		// ログインユーザコード
	*		@param Long		$lngSelectNumber	// 戻った時のvalue値(selected)
	*		@param Object	$objDB				// DB接続オブジェクト
	*		@return	Array	$aryError
	*/
	// -----------------------------------------------------------------
	
	
	function fncWorkFlow( $lngUserCode, $objDB ,$lngSelectNumber)
	{

		$aryQuery[] = "SELECT DISTINCT ON ( w.lngWorkflowOrderCode ) w.lngWorkflowOrderCode ";
		$aryQuery[] = "FROM m_WorkflowOrder w, m_GroupRelation gr ";
		$aryQuery[] = "WHERE gr.lngUserCode = $lngUserCode ";
		$aryQuery[] = " AND w.lngWorkflowOrderGroupCode = gr.lngGroupCode ";
		$aryQuery[] = " AND w.bytWorkflowOrderDisplayFlag = true ";
		$aryQuery[] = "EXCEPT ";
		$aryQuery[] = "SELECT DISTINCT ON ( w.lngWorkflowOrderCode ) w.lngWorkflowOrderCode ";
		$aryQuery[] = "FROM m_WorkflowOrder w, m_User u, m_AuthorityGroup ag ";
		$aryQuery[] = "WHERE w.lngInChargeCode = $lngUserCode ";
		$aryQuery[] = " OR ag.lngAuthorityLevel > ";
		$aryQuery[] = "(";
		$aryQuery[] = "  SELECT ag2.lngAuthorityLevel";
		$aryQuery[] = "  FROM m_User u2, m_AuthorityGroup ag2";
		$aryQuery[] = "  WHERE u2.lngUserCode = $lngUserCode";
		$aryQuery[] = "   AND u2.lngAuthorityGroupCode = ag2.lngAuthorityGroupCode";
		$aryQuery[] = ")";
		$aryQuery[] = " AND w.lngInChargeCode = u.lngUserCode";
		$aryQuery[] = " AND w.bytWorkflowOrderDisplayFlag = true ";
		$aryQuery[] = " AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode";
		$aryQuery[] = "GROUP BY w.lngworkflowordercode ";
		$aryQuery[] = "ORDER BY lngworkflowordercode ";

		$strQuery = implode("\n", $aryQuery );
		// echo "$strQuery<br>";
			
		// クエリー実行 =====================================
		
		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			echo "クエリーエラー";
			//fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
		
		$lngCount = pg_num_rows( $lngResultID );
		// 承認ルートがない場合
		if ( $lngCount == 0 )
		{
			$strOptionValue = "<option value=\"0\">承認なし</option>";
		}
		else
		{
			// echo "承認ルートあり<br>";
			// echo "count : $lngCount<br>";
			// lngworkflowordercodeから承認者を割り出す =====================================
			for( $i = 0; $i < $lngCount; $i++ )
			{
				$aryResult = pg_fetch_array( $lngResultID, $i, PGSQL_ASSOC );
		
				$strWorkflowOrderName = fncGetMasterValue( "m_workfloworder", "lngworkflowordercode", "strworkflowordername", $aryResult["lngworkflowordercode"] . ":str", 'lngWorkflowOrderNo = 1', $objDB);

				unset( $strSelect );
				// 戻った時に・・・
				if ( strcmp($lngSelectNumber ,"" ) != 0 and $aryResult["lngworkflowordercode"] == $lngSelectNumber )
				{
					$strSelect = " selected";
				}
				
				$strOptionValue .= "<option value=\"" . $aryResult["lngworkflowordercode"] . "\"$strSelect>" 
					. $strWorkflowOrderName . "</option>";
			}
		}


		return $strOptionValue;
	}
	
	
	
	// -----------------------------------------------------------------
	/**		fncDe. tailHidden()
	*
	*		受注登録・修正の明細行をhidden値に変換する
	*
	*		@param Array	$aryData			// 明細行のデータ
	*		@param String	$strMode			// 登録と修正の判定(大文字小文字の違いだけ）登録・戻るは大文字、DBから引く時は小文字
	*		@return	Array	$aryJScript			//
	*/
	// -----------------------------------------------------------------
	
	
	
	
	function fncDetailHidden( $aryData, $strMode, $objDB)
	{
		
		if( $strMode == "insert" )
		{
			for ($i = 0; $i < count( $aryData ); $i++ )
			{
				// 明細行番号
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngPurchaseOrderNo]\" value=\"".$aryData[$i]["lngPurchaseOrderNo"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngPurchaseOrderDetailNo]\" value=\"".$aryData[$i]["lngPurchaseOrderDetailNo"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngOrderNo]\" value=\"".$aryData[$i]["lngOrderNo"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngOrderDetailNo]\" value=\"".$aryData[$i]["lngOrderDetailNo"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngSortKey]\" value=\"".$aryData[$i]["lngSortKey"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngRevisionNo]\" value=\"".$aryData[$i]["lngRevisionNo"]."\">";
				// 運搬方法
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngCarrierCode]\" value=\"".$aryData[$i]["lngDeliveryMethodCode"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strDeliveryMethodName]\" value=\"". $aryData[$i]["strDeliveryMethodName"] ."\">";
				// 単位
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCode]\" value=\"".$aryData[$i]["lngProductUnitCode"]."\">";
				// 仕入科目
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngStockSubjectCode]\" value=\"".$aryData[$i]["lngStockSubjectCode"]."\">";
				// 仕入部品
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strStockItemCode]\" value=\"".$aryData[$i]["strStockItemCode"]."\">";
				// 通貨
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngMonetaryUnitCode]\" value=\"".$aryData[$i]["lngMonetaryUnitCode"]."\">";
				// 仕入先
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngCustomerCompanyCode]\" value=\"".$aryData[$i]["lngCustomerCompanyCode"]."\">";
				// 単価
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPrice]\" value=\"".$aryData[$i]["curProductPrice"]."\">";
				// 数量
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductQuantity]\" value=\"".$aryData[$i]["lngProductQuantity"]."\">";
				// 税抜金額
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curSubtotalPrice]\" value=\"".$aryData[$i]["curSubtotalPrice"]."\">";
				// 納期
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][dtmDeliveryDate]\" value=\"".$aryData[$i]["dtmDeliveryDate"]."\">";
				// 備考
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strDetailNote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strDetailNote"])."\">";
				
				
				
				// 製品
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strProductCode]\" value=\"".$aryData[$i]["strProductCode"]."\">";
				//$strStockSubjectName = "";
				//$strStockSubjectName = fncGetMasterValue( "m_stocksubject", "lngstocksubjectcode", "strstocksubjectname", $aryData[$i]["strStockSubjectCode"],'', $objDB );
				//$strStockItemName = "";
				//$strStockItemName = fncGetMasterValue( "m_stockitem", "lngstockitemcode","strstockitemname" , $aryData[$i]["strStockItemCode"], "lngstocksubjectcode = ".$aryData[$i]["strStockSubjectCode"],$objDB );
				// 換算区分コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngConversionClassCode]\" value=\"".$aryData[$i]["lngConversionClassCode"]."\">";
				// ForList
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPriceForList]\" value=\"".$aryData[$i]["curProductPriceForList"]."\">";
				// 単価リスト
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngGoodsPriceCode]\" value=\"".$aryData[$i]["lngGoodsPriceCode"]."\">";
				// シリアルNO
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strSerialNo]\" value=\"".$aryData[$i]["strSerialNo"]."\">";
				
				// 単価リストの表示用value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCodeName]\" value=\"".$aryData[$i]["lngProductUnitCodeName"]."\">";
				// 仕入科目の表示用value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strStockSubjectCodeName]\" value=\"".$aryData[$i]["strStockSubjectCodeName"]."\">";
				// 仕入部品の表示用value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strStockItemCodeName]\" value=\"".$aryData[$i]["strStockItemCodeName"]."\">";
			
			}
		}
		else
		{
		// DBから値をとってきた時。カラム名とhiddenのname属性が違う個所がある
			for ($i = 0; $i < count( $aryData ); $i++ )
			{
				$lngConversionClassCode = ( $aryData[$i]["lngconversionclasscode"] == 1 ) ? "gs" : "ps";
				
// 2004.03.25 suzukaze update start
				// 明細行番号
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngorderdetailno]\" value=\"".$aryData[$i]["lngorderdetailno"]."\">";
// 2004.03.25 suzukaze update end

				// 製品コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strproductcode]\" value=\"".$aryData[$i]["strproductcode"]."\">";
				// 仕入科目コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstocksubjectcode]\" value=\"".$aryData[$i]["lngstocksubjectcode"]."\">";
				// 仕入部品コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstockitemcode]\" value=\"".$aryData[$i]["lngstockitemcode"]."\">";
				// 換算区分コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngconversionclasscode]\" value=\"$lngConversionClassCode\">";
				// ForList
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curproductpriceforlist]\" value=\"".$aryData[$i]["curproductpriceforlist"]."\">";
				// 単価
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curproductprice]\" value=\"".$aryData[$i]["curproductprice"]."\">";
				// 単位
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngproductunitcode]\" value=\"".$aryData[$i]["lngproductunitcode"]."\">";
				// 数量
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lnggoodsquantity]\" value=\"".$aryData[$i]["lngproductquantity"]."\">";
				// 税抜金額
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curtotalprice]\" value=\"".$aryData[$i]["cursubtotalprice"]."\">";
				// 運搬方法
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngcarriercode]\" value=\"".$aryData[$i]["lngdeliverymethodcode"]."\">";
				// 備考
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strdetailnote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strnote"])."\">";
				// 単価リスト
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lnggoodspricecode]\" value=\"".$aryData[$i]["lnggoodspricecode"]."\">";
				// シリアルNO
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strserialno]\" value=\"".$aryData[$i]["strserialno"]."\">";
				// 納期
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][dtmdeliverydate]\" value=\"".$aryData[$i]["dtmdeliverydate"]."\">";
				
				$strStockSubjectName = "";
				$strStockSubjectName = fncGetMasterValue( "m_stocksubject", "lngstocksubjectcode", "strstocksubjectname", $aryData[$i]["lngstocksubjectcode"],'', $objDB );
				$strStockItemName = "";
				$strStockItemName = fncGetMasterValue( "m_stockitem", "lngstockitemcode","strstockitemname" , $aryData[$i]["lngstockitemcode"], "lngstocksubjectcode = ".$aryData[$i]["lngstocksubjectcode"],$objDB );
				
				$strProductUnitCodeName = fncGetMasterValue("m_productunit", "lngproductunitcode", "strProductUnitName", $aryData[$i]["lngproductunitcode"], "", $objDB );


				// 単価リストの表示用value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngproductunitcodename]\" value=\"$strProductUnitCodeName\">";
				// 仕入科目の表示用value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstocksubjectcodename]\" value=\"".$aryData[$i]["lngstocksubjectcode"]." $strStockSubjectName\">";
				// 仕入部品の表示用value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstockitemcodename]\" value=\"".$aryData[$i]["lngstockitemcode"]." $strStockItemName\">";
				
				
			}
		}

		
		$strDetailHidden = implode( "\n", $aryDetailHidden );

		return $strDetailHidden;
	}


?>