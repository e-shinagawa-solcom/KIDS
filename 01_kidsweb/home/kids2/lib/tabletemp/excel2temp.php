<?

/**
*
*	@charset	: euc-jp
*/


	/*
		include_once('conf.inc');
		require (LIB_FILE);
		include_once('clstabletemp.php');

		// DB接続
		$objDB   = new clsDB();
		$objAuth = new clsAuth();
		$objDB->open( "", "", "", "" );
	*/

	/*
		$aryA = array();
		$aryA['curproductprice']		= '99.0000';			// 上代
		$aryA['curretailprice']			= '300.0000';			// 納価
		$aryA['lngestimateno']			= '386';				// 見積原価No
		$aryA['lnginchargeusercode']	= '243';				// 担当者コード
		$aryA['lngproductionquantity']	= '400000';				// 生産予定数
		$aryA['strproductcode']			= '2009';				// 製品コード
		$aryA['strproductname']			= 'EFコレクション６';	// 製品名称
	*/



	// ------------------------------------------------------------------------
	/**
	*   fncExcel2Temp() 関数
	*
	*   処理概要
	*     ・配列（ハッシュ=値）で持っているデータをテンポラリテーブルへ登録する
	*
	*   @param   $objDB			[Object]	データベースオブジェクト
	*   @param   $aryIn			[Array]		$ary["ハッシュ"]=値、で保持されている情報
	*   @return  $lngTempNo  	[integer]	テンポラリテーブルへ登録した際のNo（lngTempNo）
	*/
	// ------------------------------------------------------------------------
	function fncArray2Temp($objDB, $aryIn)
	{
		// テンポラリテーブルオブジェクト生成
		$objTT = new clsTableTemp;
		$objTT->objDB = $objDB;

		// テンポラリテーブルへ登録、登録したlngTempNoを取得
		$lngTempNo = $objTT->fncInsert($aryIn);

		return $lngTempNo;
	}

	// ------------------------------------------------------------------------
	/**
	*   fncTemp2ProductUpdate() 関数
	*
	*   処理概要
	*     ・テンポラリテーブルの内容を用いて商品マスタを更新する
	*
	*   @param   $objDB			[Object]	データベースオブジェクト
	*   @param   $lngTempNo		[integer]	用いるテンポラリテーブルのNo（lngTempNo）
	*   @return  true/false  	[boolean]	成功／失敗
	*
	*	注意
	*	テンポラリテーブルの strKey には strproductcode が存在しなければならない。
	*	テンポラリテーブルの strKey に、m_product に存在しないカラム名がある場合処理不能。
	*	（lngestimateno は意図的に対象外にしている）
	*/
	// ------------------------------------------------------------------------
	function fncTemp2ProductUpdate($objDB, $lngTempNo)
	{
		require_once ( LIB_DEBUGFILE );


		// テンポラリテーブルオブジェクト生成
		$objTT = new clsTableTemp;
		$objTT->objDB = $objDB;


		// テンポラリテーブルから取得
		$aryTempInfo = $objTT->fncSelect($lngTempNo);

		// データが取得出来ない場合
		if(!isset($aryTempInfo)) return false;

		// 取得したデータを基に商品マスタを更新
		$arySql = array();
		$arySql[] = "update m_product";
		$arySql[] = "set";
		$arySql[] = "strproductcode='" .$aryTempInfo["strproductcode"]. "'";	// 下whileで,文字結合を気にしないために必須

		// $aryTempInfo 配列分の処理
		while( list($strKey, $strValue) = each($aryTempInfo) )
		{
			// strproductcode, lngestimateno , curconversionrate, curstandardrate, lngplancartonproduction は条件に追加しない
			if( $strKey == "strproductcode" || $strKey == "lngestimateno" ||
				$strKey == "curconversionrate" || $strKey == "curstandardrate" ||
				$strKey == "lngplancartonproduction" )
			{
				continue;
			}

//fncDebug( 'temp_sql.txt', $arySql, __FILE__, __LINE__);


			// 値が存在しない場合、条件に追加しない
			if( $strValue == "" ) continue;

			// dtmdeliverylimitdate の場合、YYYY/mm/dd 形式に変換
			if( $strKey == "dtmdeliverylimitdate" ) $strValue	= $strValue . "/01";

			// strGroupDisplayCode -> lnginchargegroupcode
			if( $strKey == "strgroupdisplaycode" )
			{
				$strKey		= "lnginchargegroupcode";
				$strValue	= fncGetMasterValue( "m_group", "strgroupdisplaycode", "lnggroupcode", $strValue.":str", '', $objDB );
			}

			// strUserDiplayCode -> lnginchargeusercode
			if( $strKey == "struserdiplaycode" )
			{
				$strKey		= "lnginchargegroupcode";
				$strValue	= fncGetMasterValue( "m_user", "struserdisplaycode", "lngusercode", $strValue.":str", '', $objDB );
			}

			// 型取得
			$strType	= substr( $strKey, 0, 3 );

			switch( $strType )
			{
				case "str":
					$arySql[] = "," .$strKey. "='" .$strValue. "'";
					break;
				case "dtm":
					$arySql[] = "," .$strKey. "='" .$strValue. "'";
					break;
				default:
					$arySql[] = "," .$strKey. "=" .$strValue;
					break;
			}
		}



		// 生産予定数単位を「PCS」に変更（強制）
		$arySql[]	= ",lngproductionunitcode=1";

		$arySql[]	= "where";
		$arySql[]	= "strproductcode='" .$aryTempInfo["strproductcode"]. "'";
		$strSql	= implode($arySql,"\n");

		// 更新実行
		list ($lngResultID, $lngResultNum) = fncQuery($strSql, $objDB);

		return true;
	}



	// ------------------------------------------------------------------------
	/**
	*   fncGetTempData() 関数
	*
	*   処理概要
	*     ・テンポラリテーブルの内容取得する
	*
	*   @param   $objDB				[Object]	データベースオブジェクト
	*   @param   $lngTempNo			[integer]	用いるテンポラリテーブルのNo（lngTempNo）
	*   @return  Array/Boolean  	[Object]	成功:Array ／ 失敗:Flase
	*
	*/
	// ------------------------------------------------------------------------
	function fncGetTempData($objDB, $lngTempNo)
	{
		// テンポラリテーブルオブジェクト生成
		$objTT = new clsTableTemp;
		$objTT->objDB = $objDB;


		// テンポラリテーブルから取得
		$aryTempInfo = $objTT->fncSelect($lngTempNo);

		// データが取得出来ない場合
		if( !isset($aryTempInfo) ) return false;

		return $aryTempInfo;
	}



	// ------------------------------------------------------------------------
	/**
	*   fncDeleteEstimateTempNo() 関数
	*
	*   処理概要
	*     ・見積原価番号をキーとして、その対象テーブルのlngTempNoを消す
	*
	*   @param   $objDB			[Object]	データベースオブジェクト
	*   @param   $lngKeyNo		[integer]	キーとなる見積原価番号（lngEstimateNo）
	*   @return  true/false  	[boolean]	成功／失敗
	*
	*/
	// ------------------------------------------------------------------------
	function fncDeleteEstimateTempNo( $objDB, $lngKeyNo )
	{
		require_once ( LIB_DEBUGFILE );


		$arySql	=	array();
		$arySql[]	= "update";
		$arySql[]	= "	m_estimate";
		$arySql[]	= "set";
		$arySql[]	= "	lngtempno = null";
		$arySql[]	= "where";
		$arySql[]	= "	m_estimate.lngrevisionno = (select max(me1.lngrevisionno) from m_estimate me1 where me1.lngestimateno = m_estimate.lngestimateno)";
		$arySql[]	= "and m_estimate.lngestimateno = ".$lngKeyNo;

		$strSql	= implode( "\n", $arySql );

		// 更新実行
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSql, $objDB );

//fncDebug( 'temp_no.txt', $lngResultNum, __FILE__, __LINE__);

		return true;
	}

?>
