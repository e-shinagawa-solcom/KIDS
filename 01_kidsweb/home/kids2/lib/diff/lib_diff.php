<?php

/**
*
*	@charset	: utf-8
*/



	/**
		マスターデータ取得処理

		@param $objDB	[Object]	: DB Object
		@param $ary		[Array]		: SQLカラム
		@param $keys	[String]	: WHERE句 キー
		@param $strHash	[String]	: 対象配列ハッシュ名

		@return $objFetch	[Object]
	*/
	function getMasterData( $objDB, $ary, $keys, $strHash )
	{
		$objFetch;
		$arySQL			= array();
		$strSQL			= "";
		$lngBuffCnt		= 1;
		$lngTotalCnt	= 0;


		// クエリ行数のカウント
		$lngTotalCnt	= count( $ary[$strHash] );


		/* Query start */
		$arySQL[]	= "select";

		// SELECT句生成
		while( list($index, $value) = each($ary[$strHash]) )
		{
			if( $lngBuffCnt == $lngTotalCnt )
			{
				$arySQL[]	= $index;
				break;
			}
			else
			{
				$arySQL[]	= $index . ",";
				$lngBuffCnt++;
			}
		}

		$arySQL[]	= "from " .$ary["table"];

		if( $keys ) $arySQL[] = "where " .$ary["key"]. " = '" .$keys. "'";
		/* Query end // */


		$strSQL	= implode( "\n", $arySQL );


		// 実行
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSQL, $objDB );

		// 存在しない場合、処理終了
		if( !$lngResultNum ) return false;

		// 取得
		for( $i = 0; $i < $lngResultNum; $i++ )
		{
			$objFetch	= $objDB->fetchObject( $lngResultID, $i );
		}

		unset( $arySQL );
		return $objFetch;
	}



	/**
		マスターデータ差異チェック処理

		@param $objDB		[Object]	: DB Object
		@param $aryDiff		[Array]		: 差分チェック配列
		@param $aryMaster	[Array]		: マスターデータ配列
		@param $aryData		[Array]		: チェックデータ配列

		@return $blnVal	[Boolean]
	*/
	function fncCheckDiff( $objDB, $aryDiff, $aryMaster, $aryData )
	{
		$blnCheck	= false;
		$aryRetVal	= array();

		// 差異チェック
		while( list($index, $value) = each($aryDiff) )
		{
			// チェック対象外の場合
			if( !$value )
			{
				continue;
			}

			// 納期の場合
			if( $index == "dtmdeliverylimitdate" )
			{
				$aryBuffDate	= explode( "/", $aryData[$index] );

				// 月が10月未満の場合、0付加
				if( mb_strlen($aryBuffDate[1]) == 1 ) $aryBuffDate[1]	= "0" . $aryBuffDate[1];

				// 値を再設定
				$aryData[$index]	= implode( "/", $aryBuffDate );
				unset( $aryBuffDate );
			}

			// チェック対象で、値が異なる場合
			if( $aryMaster[$index] != $aryData[$index] )
			{
				$blnCheck			= true;
				$aryRetVal[$index]	= $aryData[$index];
			}
		}

		// 差異が認められない場合
		if( !$blnCheck ) return false;

		return $aryRetVal;
	}



	/**
		所属グループチェック処理

		@param $objDB			[Object]	: DB Object
		@param $aryProduct		[Array]		: 製品マスタ情報配列
		@param $strDispUserCode	[Array]		: 表示ユーザーコード

		@return $blnVal	[Boolean]
	*/
	function fncCheckGroup( $objDB, $aryProduct, $strDispUserCode )
	{
		require_once ( LIB_DEBUGFILE );	// Debugモジュール


		$blnCheck	= false;
		$arySQL		= array();

		$arySQL[]	= "select";
		$arySQL[]	= "	mg.strgroupdisplaycode";
		$arySQL[]	= "	,mg.strgroupdisplayname";
		$arySQL[]	= "	,mgr.bytdefaultflag";
		$arySQL[]	= "from";
		$arySQL[]	= "	m_group mg";
		$arySQL[]	= "		inner join m_grouprelation mgr";
		$arySQL[]	= "			on mgr.lnggroupcode = mg.lnggroupcode";
		$arySQL[]	= "			inner join m_user mu";
		$arySQL[]	= "				on mu.lngusercode =mgr.lngusercode";
		$arySQL[]	= "				and mu.struserdisplaycode = '" .$strDispUserCode. "'";
		$arySQL[]	= "order by";
		$arySQL[]	= "	mgr.bytdefaultflag desc";

		$strSQL	= implode( "\n", $arySQL );


		list ( $lngResultID, $lngResultNum )	= fncQuery( $strSQL, $objDB );

		for($i = 0; $i < $lngResultNum; $i++)
		{
			$objFetch	= $objDB->fetchObject( $lngResultID, $i );
			$aryFetch[$i]["strgroupdisplaycode"]	= $objFetch->strgroupdisplaycode;
			$aryFetch[$i]["strgroupdisplayname"]	= $objFetch->strgroupdisplayname;
			$aryFetch[$i]["bytdefaultflag"]			= $objFetch->bytdefaultflag;
		}

//fncDebug( 'group.txt', $aryProduct, __FILE__, __LINE__);


		for( $i = 0; $i < count($aryFetch); $i++ )
		{
			// グループが一つでも合致している場合、ＯＫ
			if( $aryFetch[$i]["strgroupdisplaycode"] == $aryProduct[DISP_INCHARGE_GROUP_CODE] )
			{
				$blnCheck	= true;
				break;
			}
		}

		return $blnCheck;
	}

?>
