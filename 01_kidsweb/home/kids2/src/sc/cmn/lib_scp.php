<?
// ----------------------------------------------------------------------------
/**
*       売上管理  分納用チェック関数群
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       処理概要
*         ・分納チェック
*
*       更新履歴
*       2013.05.31　　　　税率取得処理修正（期間内の税率取得できない場合、最新期間の税率を取得する ）
*
*/
// ----------------------------------------------------------------------------



/**
* 指定の受注データに関して、その受注データの売上状態より残数取得関数
*
*	受注数、受注金額よりその受注Noを指定している売上すべてから
*	受注残を取得する
*
*	@param  Integer 	$lngReceiveNo 	受注番号
*	@param  Integer 	$lngSalesNo 	対象外としない売上No　売上修正時使用
*	@param	Integer		$lngCalcCode	端数処理コード
*	@param  Object		$objDB			DBオブジェクト
*	@return Boolean 	0				実行成功
*						1				実行失敗 情報取得失敗
*	@access public
*
*	更新履歴
*	2004.04.16	注残数を求める際に端数処理、内税対策を行うように変更
*/
function fncGetSalesRemains ( $lngReceiveNo, $lngSalesNo, $lngCalcCode, $objDB )
{

	// 受注番号が存在しない場合そのまま終了
	if ( $lngReceiveNo == "" or $lngReceiveNo == 0 )
	{
		return 0;
	}

	// 最新の受注のデータを取得する
	$arySql = array();
	$arySql[] = "SELECT";
	$arySql[] = "	r.lngReceiveNo				as lngReceiveNo";
	$arySql[] = "	,r.strReceiveCode			as strReceiveCode";
	$arySql[] = "	,r.strCustomerReceiveCode	as strCustomerReceiveCode";
	$arySql[] = "	,r.lngReceiveStatusCode		as lngReceiveStatusCode";
	$arySql[] = "	,r.lngMonetaryUnitCode		as lngMonetaryUnitCode";
	$arySql[] = "FROM";
	$arySql[] = "	m_Receive r";
	$arySql[] = "WHERE";
	$arySql[] = "	r.strReceiveCode = (";
	$arySql[] = "		SELECT r1.strReceiveCode FROM m_Receive r1 WHERE r1.lngReceiveNo = " . $lngReceiveNo . " ) ";
	$arySql[] = "	AND r.bytInvalidFlag = FALSE";
	$arySql[] = "	AND r.lngRevisionNo >= 0 ";
	$arySql[] = "	AND r.lngRevisionNo = ( ";
	$arySql[] = "		SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode";
	$arySql[] = "		AND r2.strReviseCode = ( ";
	$arySql[] = "		SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode ) )";
	$arySql[] = "	AND 0 <= ( ";
	$arySql[] = "		SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode ) ";
	$strQuery = implode("\n", $arySql);

//fncDebug('lib_scp.txt', $strQuery, __FILE__, __LINE__);

	// 検索クエリーの実行
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult					= $objDB->fetchObject( $lngResultID, 0 );
		$lngNewReceiveNo 			= $objResult->lngreceiveno;
//		$strNewReceiveCode			= $objResult->strreceivecode;
		$strNewCutomerReceiveCode 	= $objResult->strcustomerreceivecode;
		$lngNewReceiveStatusCode 	= $objResult->lngreceivestatuscode;
		$ReceivelngMonetaryUnitCode = $objResult->lngmonetaryunitcode;
	}
	else
	{
		// 受注Noは指定しているが現在有効な最新受注が存在しない場合はそのまま終了
		return 0;
	}
	$objDB->freeResult( $lngResultID );

	// 受注時の通貨単位コードより処理対象桁数を設定
	if ( $ReceivelngMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$ReceivelngDigitNumber = 0;		// 日本円の場合は０桁
	}
	else
	{
		$ReceivelngDigitNumber = 2;		// 日本円以外の場合は小数点以下２桁
	}

	// 最新受注の明細情報を取得する
	$arySql = array();
	$arySql[] = "SELECT";
	$arySql[] = "	 rd.lngReceiveDetailNo	as lngOrderDetailNo";
	$arySql[] = "	,rd.lngReceiveDetailNo	as lngReceiveDetailNo";
	$arySql[] = "	,rd.strProductCode		as strProductCode";
	$arySql[] = "	,rd.lngSalesClassCode	as lngSalesClassCode";
	$arySql[] = "	,rd.dtmDeliveryDate		as dtmDeliveryDate";
	$arySql[] = "	,rd.lngConversionClassCode	as lngConversionClassCode";
	$arySql[] = "	,rd.curProductPrice		as curProductPrice";
	$arySql[] = "	,rd.lngProductQuantity	as lngProductQuantity";
	$arySql[] = "	,rd.lngProductUnitCode	as lngProductUnitCode";
	$arySql[] = "	,rd.lngTaxClassCode		as lngTaxClassCode";
	$arySql[] = "	,rd.lngTaxCode			as lngTaxCode";
	$arySql[] = "	,rd.curTaxPrice			as curTaxPrice";
	$arySql[] = "	,rd.curSubTotalPrice	as curSubTotalPrice";
	$arySql[] = "	,rd.strNote				as strDetailNote";
	$arySql[] = "	,p.lngCartonQuantity	as lngCartonQuantity";
	$arySql[] = "FROM";
	$arySql[] = "	t_ReceiveDetail rd";
	$arySql[] = "	,m_Product p";
	$arySql[] = "WHERE";
	$arySql[] = "	rd.lngReceiveNo = " . $lngNewReceiveNo;
	$arySql[] = "	AND rd.strProductCode = p.strProductCode";
	$arySql[] = "ORDER BY lngSortKey ASC";
	$strQuery = implode("\n", $arySql);

//fncDebug('lib_scp.txt', $strQuery, __FILE__, __LINE__);

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryReceiveDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		// 明細行が存在しない場合異常データ
		return 1;
	}
	$objDB->freeResult( $lngResultID );



	// 同じ「顧客受注番号」を指定している最新売上を検索
	$arySql = array();
	$arySql[] = "SELECT distinct";
	$arySql[] = "	s.lngSalesNo as lngSalesNo";
	$arySql[] = "	,s.lngSalesStatusCode as lngSalesStatusCode";
	$arySql[] = "	,s.lngMonetaryUnitCode as lngMonetaryUnitCode";

	$arySql[] = "	,case when (1<(select count(lngreceiveno) from m_receive where strreceivecode = r.strreceivecode)) then";
	$arySql[] = "			(";
	$arySql[] = "				select";
	$arySql[] = "					max(mr.lngreceiveno)";
	$arySql[] = "				from";
	$arySql[] = "					m_receive mr";
	$arySql[] = "				where";
	$arySql[] = "					mr.strreceivecode = r.strreceivecode";
	$arySql[] = "					and mr.bytInvalidFlag = false";
	$arySql[] = "					and mr.lngRevisionNo >= 0 ";
	$arySql[] = "					and mr.lngRevisionNo = (";
	$arySql[] = "						select max(mr2.lngRevisionNo) from m_receive mr2 where mr2.strreceivecode = mr.strreceivecode )";
	$arySql[] = "						and 0 <= (";
	$arySql[] = "							select min(mr3.lngRevisionNo) from m_receive mr3 where mr3.bytinvalidflag = false and mr3.strreceivecode = mr.strreceivecode";
	$arySql[] = "						)";

	$arySql[] = "			)";
	$arySql[] = "		else";
	$arySql[] = "		tsd.lngreceiveno";
	$arySql[] = "	end";

	$arySql[] = "FROM";
	$arySql[] = "	m_Sales s";
	$arySql[] = "	left join t_salesdetail tsd";
	$arySql[] = "		on s.lngsalesno = tsd.lngsalesno";
	$arySql[] = "	,m_Receive r";
	$arySql[] = "WHERE";
	$arySql[] = "	r.strCustomerReceiveCode = '" . $strNewCutomerReceiveCode . "'";
	$arySql[] = "	AND r.lngReceiveNo = tsd.lngReceiveNo";
	$arySql[] = "	AND s.bytInvalidFlag = FALSE";
	$arySql[] = "	AND s.lngRevisionNo >= 0";
	$arySql[] = "	AND s.lngRevisionNo = (";
	$arySql[] = "		SELECT MAX( s2.lngRevisionNo ) FROM m_Sales s2 WHERE s2.strSalesCode = s.strSalesCode )";
	$arySql[] = "		AND 0 <= (";
	$arySql[] = "			SELECT MIN( s3.lngRevisionNo ) FROM m_Sales s3 WHERE s3.bytInvalidFlag = false AND s3.strSalesCode = s.strSalesCode";
	$arySql[] = "		)";
	// 引数に $lngSalesNo が指定されている場合その売上番号のデータは対象外とする
	if ( $lngSalesNo != "" )
	{
		$arySql[] = "AND s.lngSalesNo <> " . $lngSalesNo;
	}
	$strQuery = implode("\n", $arySql);
//fncDebug('lib_scp.txt', $strQuery, __FILE__, __LINE__);


	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		// 売上データが存在する場合
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$arySalesResult[] = $objDB->fetchArray( $lngResultID, $i );

			// 明細情報を取得する
			$arySql = array();
			$arySql[] = "SELECT";
			$arySql[] = "	sd.lngSalesDetailNo as lngOrderDetailNo";
			$arySql[] = "	,sd.strProductCode as strProductCode";
			$arySql[] = "	,sd.lngSalesClassCode as lngSalesClassCode";
			$arySql[] = "	,sd.dtmDeliveryDate as dtmDeliveryDate";
			$arySql[] = "	,sd.lngConversionClassCode as lngConversionClassCode";
			$arySql[] = "	,sd.curProductPrice as curProductPrice";
			$arySql[] = "	,sd.lngProductQuantity as lngProductQuantity";
			$arySql[] = "	,sd.lngProductUnitCode as lngProductUnitCode";
			$arySql[] = "	,sd.lngTaxClassCode as lngTaxClassCode";
			$arySql[] = "	,sd.lngTaxCode as lngTaxCode";
			$arySql[] = "	,sd.curTaxPrice as curTaxPrice";
			$arySql[] = "	,sd.curSubTotalPrice as curSubTotalPrice";
			$arySql[] = "	,sd.strNote as strDetailNote";
//			$arySql[] = "	,sd.lngreceiveno";
			$arySql[] = "	,".$arySalesResult[$i]["lngreceiveno"]." as lngreceiveno";	// 現 lngreceiveno から、最新の lngreceiveno へ
			$arySql[] = "	,sd.lngreceivedetailno";
			$arySql[] = "	,p.lngCartonQuantity as lngCartonQuantity";
			$arySql[] = "FROM";
			$arySql[] = "	t_SalesDetail sd";
			$arySql[] = "	,m_Product p";
			$arySql[] = "WHERE";
			$arySql[] = "	sd.lngSalesNo   = " . $arySalesResult[$i]["lngsalesno"];
//			$arySql[] = "	and sd.lngreceiveno = " . $arySalesResult[$i]["lngreceiveno"];
// ↓ 売上分納後、受注修正に対応（仮）
			
			$arySql[] = "	and sd.lngreceiveno in";
			$arySql[] = "		(";
			$arySql[] = "			select";
			$arySql[] = "				mr.lngreceiveno";
			$arySql[] = "			from";
			$arySql[] = "				m_receive mr";
			$arySql[] = "			where";
			$arySql[] = "				mr.strreceivecode = (select strreceivecode from m_receive where lngreceiveno = ".$arySalesResult[$i]["lngreceiveno"].")";
			$arySql[] = "				and mr.bytInvalidFlag = false";
			$arySql[] = "				AND mr.lngRevisionNo >= 0 ";
			$arySql[] = "		)";
			
// ↑
			$arySql[] = "	AND sd.strProductCode = p.strProductCode";

			$strSalesDetailQuery = implode("\n",$arySql);
//fncDebug('lib_scp.txt', $strSalesDetailQuery, __FILE__, __LINE__);

			list ( $lngSalesDetailResultID, $lngSalesDetailResultNum ) = fncQuery( $strSalesDetailQuery, $objDB );

			if ( $lngSalesDetailResultNum )
			{
				for ( $j = 0; $j < $lngSalesDetailResultNum; $j++ )
				{
					$arySalesDetailResult[$i][] = $objDB->fetchArray( $lngSalesDetailResultID, $j );

/*
					// 過去のリバイズされた lngreceiveno を求める
					if( !empty($arySalesDetailResult[$i][$j]["lngreceiveno"] ) )
					{
						$arySql = array();
						$arySql[] = "select";
						$arySql[] = "	mr.lngreceiveno";
						$arySql[] = "from";
						$arySql[] = "	m_receive mr";
						$arySql[] = "where";
						$arySql[] = "	mr.strreceivecode = (select strreceivecode from m_receive where lngreceiveno = ".$arySalesDetailResult[$i][$j]["lngreceiveno"].")";
						$arySql[] = "	and mr.bytInvalidFlag = false";
						$arySql[] = "	and mr.lngRevisionNo >= 0 ";

						$strReceiveQuery = implode("\n",$arySql);
						list( $lngReceiveResultId, $lngReceiveResultNum ) = fncQuery( $strReceiveQuery, $objDB );
						if ( $lngReceiveResultNum )
						{
							$aryBuff = array();
							for ( $k = 0; $k < $lngReceiveResultNum; $k++ )
							{
								 $aryBuff[$k] = $objDB->fetchArray( $lngReceiveResultId, $k );
								$arySalesDetailResult[$i][$j]["aryReceiveNo"][] = $aryBuff[$k]["lngreceiveno"];
							}
//fncDebug('lib_scp.txt', $arySalesDetailResult[$i][$j]["aryReceiveNo"], __FILE__, __LINE__);
						}
					}
*/
				}
			}
			$objDB->freeResult( $lngSalesDetailResultID );
		}
	}
	else
	{
		// 売上が存在しない受注についてはそのまま受注残として設定
		for ( $i = 0; $i < count($aryReceiveDetailResult); $i++ )
		{
			$aryRemainsDetail[$i]["lngorderdetailno"] 		= $aryReceiveDetailResult[$i]["lngorderdetailno"];			// 明細行番号
			$aryRemainsDetail[$i]["strproductcode"] 		= $aryReceiveDetailResult[$i]["strproductcode"];			// 製品コード
			$aryRemainsDetail[$i]["lngsalesclasscode"] 		= $aryReceiveDetailResult[$i]["lngsalesclasscode"];			// 売上区分コード
			$aryRemainsDetail[$i]["dtmdeliverydate"] 		
				= str_replace( "-", "/", $aryReceiveDetailResult[$i]["dtmdeliverydate"]);								// 納品日（取得文字列置換）
			$aryRemainsDetail[$i]["lngconversionclasscode"] = $aryReceiveDetailResult[$i]["lngconversionclasscode"];	// 換算区分コード
			$aryRemainsDetail[$i]["curproductprice"]		= $aryReceiveDetailResult[$i]["curproductprice"];			// 製品単価（荷姿数量）
			$aryRemainsDetail[$i]["lngproductquantity"]		= $aryReceiveDetailResult[$i]["lngproductquantity"];		// 製品数量（荷姿数量）
			$aryRemainsDetail[$i]["lngproductunitcode"]		= $aryReceiveDetailResult[$i]["lngproductunitcode"];		// 製品単位（荷姿単位）
//日本円以外の通貨は税金は非課税にする
			if ( $ReceivelngMonetaryUnitCode == DEF_MONETARY_YEN )
			{
				// 通貨が円の場合は税区分を外税にデフォルト設定する
				$aryRemainsDetail[$i]["lngtaxclasscode"]	= DEF_TAXCLASS_SOTOZEI;										// 消費税区分コード（外税）
			}
			else
			{
				// 通貨が円以外の場合は税区分を非課税にデフォルト設定する
				$aryRemainsDetail[$i]["lngtaxclasscode"]	= DEF_TAXCLASS_HIKAZEI;										// 消費税区分コード（非課税）
			}
//			$aryRemainsDetail[$i]["lngtaxclasscode"]		= $aryReceiveDetailResult[$i]["lngtaxclasscode"];			// 消費税区分コード
			$aryRemainsDetail[$i]["lngtaxcode"]				= $aryReceiveDetailResult[$i]["lngtaxcode"];				// 消費税コード
			$aryRemainsDetail[$i]["curtaxprice"]			= $aryReceiveDetailResult[$i]["curtaxprice"];				// 消費税金額
			$aryRemainsDetail[$i]["cursubtotalprice"]		= $aryReceiveDetailResult[$i]["cursubtotalprice"];			// 税抜金額
			$aryRemainsDetail[$i]["strdetailnote"]			= $aryReceiveDetailResult[$i]["strdetailnote"];				// 備考
			$aryRemainsDetail[$i]["lngcartonquantity"]		= $aryReceiveDetailResult[$i]["lngcartonquantity"];			// カートン入数
			$aryRemainsDetail[$i]["lngreceiveno"] 			= $lngNewReceiveNo;											// 受注番号
			$aryRemainsDetail[$i]["lngreceivedetailno"] 	= $aryReceiveDetailResult[$i]["lngreceivedetailno"];;		// 受注明細番号
		}
		$objDB->freeResult( $lngResultID );

//fncDebug('lib_scp.txt', $strSalesDetailQuery, __FILE__, __LINE__);
//fncDebug('lib_scp.txt', $aryRemainsDetail, __FILE__, __LINE__);

		return $aryRemainsDetail;
	}
//fncDebug('lib_scp.txt', $arySalesDetailResult, __FILE__, __LINE__);

	$objDB->freeResult( $lngResultID );

	$lngCnt = 0;		// 残が見つかった行数カウンタ

	// 参照元受注の明細毎に取得した売上にてどのような状態になっているのか調査
	for ( $i = 0; $i < count($aryReceiveDetailResult); $i++ )
	{
		
		$lngOrderDetailNo 		= $aryReceiveDetailResult[$i]["lngorderdetailno"];			// 明細行番号
		$strProductCode 		= $aryReceiveDetailResult[$i]["strproductcode"];			// 製品コード
		$lngSalesClassCode 		= $aryReceiveDetailResult[$i]["lngsalesclasscode"];			// 売上区分コード
		$lngConversionClassCode = $aryReceiveDetailResult[$i]["lngconversionclasscode"];	// 換算区分コード
		$curProductPrice		= $aryReceiveDetailResult[$i]["curproductprice"];			// 製品単価（荷姿単価）
		$lngProductQuantity		= $aryReceiveDetailResult[$i]["lngproductquantity"];		// 製品数量（荷姿数量）
		$lngProductUnitCode		= $aryReceiveDetailResult[$i]["lngproductunitcode"];		// 製品単位（荷姿単位）
		$curSubTotalPrice		= $aryReceiveDetailResult[$i]["cursubtotalprice"];			// 税抜金額
		$lngCartonQuantity		= $aryReceiveDetailResult[$i]["lngcartonquantity"];			// カートン入数
		$lngReceiveDetailNo 	= $aryReceiveDetailResult[$i]["lngreceivedetailno"];		// 明細行番号


		// 換算区分が荷姿単位計上の場合、製品単価へ計算
		if ( $lngConversionClassCode != DEF_CONVERSION_SEIHIN )
		{
			// 0 割り対策
			if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
			{
				// カートン入り数が ０ だった場合は その製品に対する納品済みかどうかの判断ができないため 強制的に １ にて換算
				$lngCartonQuantity = 1;
			}

			// 製品数量は荷姿数量 * カートン入数
			$lngProductQuantity = $lngProductQuantity * $lngCartonQuantity;

			// 製品価格は荷姿単価 / カートン入数
			$curProductPrice = $curProductPrice / $lngCartonQuantity;

			// 税抜き金額を計算する際に設定された端数処理を行う
			// 税抜金額を計算する
			// 税抜金額は製品数量 * 製品価格
			$curSubTotalPrice = $lngProductQuantity * $curProductPrice;
			// 端数処理を行う
			$curSubTotalPrice = fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $ReceivelngDigitNumber );

			// 単位は製品単位
			$lngProductUnitCode = DEF_PRODUCTUNIT_PCS;

			// 換算区分コードは製品単位に修正
			$lngConversionClassCode = DEF_CONVERSION_SEIHIN;
		}

		$bytEndFlag = 0;
		$lngSalesProductQuantity = 0;
		$curSalesSubTotalPrice = 0;


		for ( $j = 0; $j < count($arySalesResult); $j++ )
		{
			$SaleslngMonetaryUnitCode = $arySalesResult[$j]["lngmonetaryunitcode"];

			// 仕入時の通貨単位コードより処理対象桁数を設定
			if ( $SaleslngMonetaryUnitCode == DEF_MONETARY_YEN )
			{
				$SaleslngDigitNumber = 0;		// 日本円の場合は０桁
			}
			else
			{
				$SaleslngDigitNumber = 2;		// 日本円以外の場合は小数点以下２桁
			}

			for ( $k = 0; $k < count($arySalesDetailResult[$j]); $k++ )
			{
//fncDebug('lib_scp.txt', $lngOrderDetailNo 	.'='.$arySalesDetailResult[$j][$k]["lngreceivedetailno"], __FILE__, __LINE__);
//fncDebug('lib_scp.txt', $strProductCode 	.'='.$arySalesDetailResult[$j][$k]["strproductcode"], __FILE__, __LINE__);
//fncDebug('lib_scp.txt', $lngNewReceiveNo 	.'='.$arySalesDetailResult[$j][$k]["lngreceiveno"], __FILE__, __LINE__);
//fncDebug('lib_scp.txt', $ReceivelngMonetaryUnitCode 	.'='.$SaleslngMonetaryUnitCode, __FILE__, __LINE__);

				// 受注明細行番号に対して売上明細行番号が同じ、かつ製品コードが同じ明細が見つかった場合
				if ( $lngOrderDetailNo		== $arySalesDetailResult[$j][$k]["lngreceivedetailno"]
					and $strProductCode		== $arySalesDetailResult[$j][$k]["strproductcode"]
					and $lngNewReceiveNo	== $arySalesDetailResult[$j][$k]["lngreceiveno"]
//					and in_array($arySalesDetailResult[$j][$k]["lngreceiveno"], $arySalesDetailResult[$j][$k]["aryReceiveNo"])
					and $ReceivelngMonetaryUnitCode == $SaleslngMonetaryUnitCode )
				{

					
					$strDetailNote		= $arySalesDetailResult[$j][$k]["strdetailnote"];
					
					
					
					// 換算区分が荷姿計上であった場合は、製品単位計上に変更する
					if ( $arySalesDetailResult[$j][$k]["lngconversionclasscode"] != DEF_CONVERSION_SEIHIN )
					{
						// 0 割り対策
						if ( $arySalesDetailResult[$j][$k]["lngcartonquantity"] == 0 or $arySalesDetailResult[$j][$k]["lngcartonquantity"] == "" )
						{
							// カートン入り数が ０ だった場合は その製品に対する納品済みかどうかの判断ができないため 強制的に １ にて換算
							$arySalesDetailResult[$j][$k]["lngcartonquantity"] = 1;
						}

						// 製品数量は荷姿数量 * カートン入数
						$arySalesDetailResult[$j][$k]["lngproductquantity"] 
							= $arySalesDetailResult[$j][$k]["lngproductquantity"] * $arySalesDetailResult[$j][$k]["lngcartonquantity"];

						// 製品価格は荷姿単価 / カートン入数
						$arySalesDetailResult[$j][$k]["curproductprice"] 
							= $arySalesDetailResult[$j][$k]["curproductprice"] / $arySalesDetailResult[$j][$k]["lngcartonquantity"];

						// 税抜金額は製品数量 * 製品価格
						$arySalesDetailResult[$j][$k]["cursubtotalprice"] 
							= $arySalesDetailResult[$j][$k]["lngproductquantity"] * $arySalesDetailResult[$j][$k]["curproductprice"];

						// 税抜き金額を計算する際に設定された端数処理を行う
						// 端数処理を行う
						$arySalesDetailResult[$j][$k]["cursubtotalprice"] 
							= fncCalcDigit( $arySalesDetailResult[$j][$k]["cursubtotalprice"], $lngCalcCode, $SaleslngDigitNumber );

						// 単位は製品単位
						$arySalesDetailResult[$j][$k]["lngproductunitcode"] = DEF_PRODUCTUNIT_PCS;

						// 換算区分コードは製品単位に修正
						$arySalesDetailResult[$j][$k]["lngconversionclasscode"] = DEF_CONVERSION_SEIHIN;
					}

//fncDebug('lib_scp.txt', $lngNewReceiveNo 	.'='.$arySalesDetailResult[$j][$k]["lngreceiveno"], __FILE__, __LINE__);
//fncDebug('lib_scp.txt', $lngOrderDetailNo 	.'='.$arySalesDetailResult[$j][$k]["lngreceivedetailno"], __FILE__, __LINE__);
//fncDebug('lib_scp.txt',$lngProductQuantity .'='. $arySalesDetailResult[$j][$k]["lngproductquantity"] , __FILE__, __LINE__);
//fncDebug('lib_scp.txt',$curSubTotalPrice .'='. $arySalesDetailResult[$j]["cursubtotalprice"] , __FILE__, __LINE__);

					// 数量比較
					if ( $lngProductQuantity >= $arySalesDetailResult[$j][$k]["lngproductquantity"] )
					{
						$lngSalesProductQuantity += $arySalesDetailResult[$j][$k]["lngproductquantity"];
						// 複数売上からの合算での数量比較
						if ( $lngProductQuantity < $lngSalesProductQuantity )
						{
							$bytEndFlag = 99;
							break;
						}
					}
					else
					{
						$bytEndFlag = 99;
						break;
					}
					
					// 税抜金額比較
					if ( $curSubTotalPrice >= $arySalesDetailResult[$j]["cursubtotalprice"] )
					{
						$curSalesSubTotalPrice += $arySalesDetailResult[$j]["cursubtotalprice"];
						// 複数売上からの合算での税抜金額比較
						if ( $curSubTotalPrice < $curSalesSubTotalPrice )
						{
							$bytEndFlag = 99;
							break;
						}
					}
					else
					{
						$bytEndFlag = 99;
						break;
					}

					// 同じ明細行の情報が受注と売上で見つかった際には「納品中」となるため以下設定
					$bytEndFlag = 1;
				}
			}
			// 売上明細に受注明細と同内容が見つかった場合は　for 文抜け
			if ( $bytEndFlag == 99 )
			{
				break;
			}
		}


		// 受注明細行毎の売上明細行が見つかった状態を記憶
		$aryStatus[] = $bytEndFlag;
		// 受注に対する売上が見つからなかった場合
		if ( $bytEndFlag == 0 )
		{
			$aryRemainsDetail[$lngCnt]["lngorderdetailno"] 		= $lngOrderDetailNo;										// 明細行番号
			$aryRemainsDetail[$lngCnt]["strproductcode"] 		= $strProductCode;											// 製品コード
			$aryRemainsDetail[$lngCnt]["lngsalesclasscode"] 	= $lngSalesClassCode;										// 売上区分コード
			$aryRemainsDetail[$lngCnt]["dtmdeliverydate"] 		
				= str_replace( "-", "/", $aryReceiveDetailResult[$i]["dtmdeliverydate"]);									// 納品日（取得文字列置換）
			$aryRemainsDetail[$lngCnt]["lngconversionclasscode"] = DEF_CONVERSION_SEIHIN;									// 換算区分コード
			$aryRemainsDetail[$lngCnt]["curproductprice"] 		= $curProductPrice;											// 製品単価
			$aryRemainsDetail[$lngCnt]["lngproductquantity"] 	= $lngProductQuantity;										// 製品数量
			$aryRemainsDetail[$lngCnt]["lngproductunitcode"] 	= $lngProductUnitCode;										// 製品単位
												// （ここでは荷姿であってもそのままの値が設定されているものとする）
//			$aryRemainsDetail[$lngCnt]["lngtaxclasscode"]		= $aryReceiveDetailResult[$i]["lngtaxclasscode"];			// 消費税区分コード
//日本円以外の通貨は税金は非課税にする
			if ( $ReceivelngMonetaryUnitCode == DEF_MONETARY_YEN )
			{
				// 通貨が円の場合は税区分を外税にデフォルト設定する
				$aryRemainsDetail[$lngCnt]["lngtaxclasscode"]	= DEF_TAXCLASS_SOTOZEI;										// 消費税区分コード（外税）
			}
			else
			{
				// 通貨が円以外の場合は税区分を非課税にデフォルト設定する
				$aryRemainsDetail[$lngCnt]["lngtaxclasscode"]	= DEF_TAXCLASS_HIKAZEI;	
			}
			$aryRemainsDetail[$lngCnt]["lngtaxcode"]			= $aryReceiveDetailResult[$i]["lngtaxcode"];				// 消費税コード
			$aryRemainsDetail[$lngCnt]["curtaxprice"]			= $aryReceiveDetailResult[$i]["curtaxprice"];				// 消費税金額
			$aryRemainsDetail[$lngCnt]["cursubtotalprice"] 		= $curSubTotalPrice;										// 税抜金額
			$aryRemainsDetail[$lngCnt]["strdetailnote"]			= $aryReceiveDetailResult[$i]["strdetailnote"];				// 備考
			$aryRemainsDetail[$lngCnt]["lngcartonquantity"] 	= $lngCartonQuantity;										// カートン入数
			$aryRemainsDetail[$lngCnt]["lngreceiveno"] 			= $lngNewReceiveNo;											// 受注番号
			$aryRemainsDetail[$lngCnt]["lngreceivedetailno"] 	= $lngReceiveDetailNo;										// 受注明細番号
			
			$lngCnt++;	// 残が見つかった行数カウンタをカウントアップ
		}
		// 受注に対する売上が存在し、まだ完納状態にない場合
		else if ( $bytEndFlag == 1 )
		{
			$aryRemainsDetail[$lngCnt]["lngorderdetailno"] 		= $lngOrderDetailNo;										// 明細行番号
			$aryRemainsDetail[$lngCnt]["strproductcode"] 		= $strProductCode;											// 製品コード
			$aryRemainsDetail[$lngCnt]["lngsalesclasscode"] 	= $lngSalesClassCode;										// 売上区分コード
			$aryRemainsDetail[$lngCnt]["dtmdeliverydate"] 		
				= str_replace( "-", "/", $aryReceiveDetailResult[$i]["dtmdeliverydate"]);									// 納品日（取得文字列置換）
			$aryRemainsDetail[$lngCnt]["lngconversionclasscode"] = DEF_CONVERSION_SEIHIN;									// 換算区分コード
			$aryRemainsDetail[$lngCnt]["curproductprice"] 		= $curProductPrice;											// 製品単価
			// 製品数量は受注数量　−　売上総数量
			$aryRemainsDetail[$lngCnt]["lngproductquantity"] 	= $lngProductQuantity - $lngSalesProductQuantity;			// 製品数量
			$aryRemainsDetail[$lngCnt]["lngproductunitcode"] 	= $lngProductUnitCode;										// 製品単位
												// （ここでは荷姿であってもそのままの値が設定されているものとする）
			$aryRemainsDetail[$lngCnt]["lngtaxclasscode"]		= $aryReceiveDetailResult[$i]["lngtaxclasscode"];			// 消費税区分コード
//日本円以外の通貨は税金は非課税にする
			if ( $SaleslngMonetaryUnitCode == DEF_MONETARY_YEN )
			{
				// 通貨が円の場合は税区分を外税にデフォルト設定する
				$aryRemainsDetail[$lngCnt]["lngtaxclasscode"]	= DEF_TAXCLASS_SOTOZEI;										// 消費税区分コード（外税）
			}
			else
			{
				// 通貨が円以外の場合は税区分を非課税にデフォルト設定する
				$aryRemainsDetail[$lngCnt]["lngtaxclasscode"]	= DEF_TAXCLASS_HIKAZEI;	
			}
			$aryRemainsDetail[$lngCnt]["lngtaxcode"]				= $aryReceiveDetailResult[$i]["lngtaxcode"];			// 消費税コード
			$aryRemainsDetail[$lngCnt]["curtaxprice"]			= $aryReceiveDetailResult[$i]["curtaxprice"];				// 消費税金額
				// 税抜き金額は受注金額−売上総金額
			$aryRemainsDetail[$lngCnt]["cursubtotalprice"] 		= $curSubTotalPrice - $lngSalesSubTotalPrice;				// 税抜金額
//			$aryRemainsDetail[$lngCnt]["strdetailnote"]			= $aryReceiveDetailResult[$i]["strdetailnote"];				// 備考
//			$aryRemainsDetail[$lngCnt]["strdetailnote"]			= $strDetailNote;											// 備考
			$aryRemainsDetail[$lngCnt]["lngcartonquantity"] 	= $lngCartonQuantity;										// カートン入数
			$aryRemainsDetail[$lngCnt]["lngreceiveno"] 			= $lngNewReceiveNo;											// 受注番号
			$aryRemainsDetail[$lngCnt]["lngreceivedetailno"] 	= $lngReceiveDetailNo;										// 受注明細番号

			$lngCnt++;	// 残が見つかった行数カウンタをカウントアップ
		}
	}

//fncDebug('lib_scp.txt', $aryRemainsDetail, __FILE__, __LINE__);
	return $aryRemainsDetail;
}



/**
* 指定の受注残数データより受注にて指定されていた計上単位に合わせる処理
*
*	受注残データより受注の際に指定された計上単位に修正する処理
*	受注残を取得する
*
*	@param	array 		$arySalesRemains 		受注残（売上が存在しない場合でも、全ての受注データ）
*	@param	array 		$aryReceiveDetail 		受注明細
*	@param	Integer		$lngMonetaryUnitCode	受注時の通貨単位コード
*	@param	Integer		$lngCalcCode			端数処理コード
*	@param	Date		$dtmAppropriationDate	仕入計上日
*	@param	Object		$objDB					DBオブジェクト
*	@return	Boolean 	$arySalesRemains_New	実行成功
*						1						実行失敗 情報取得失敗
*	@access public
*/
function fncSetConversionSalesRemains ( $arySalesRemains, $aryReceiveDetail, $lngMonetaryUnitCode, $lngCalcCode, $dtmAppropriationDate, $objDB )
{
//fncDebug('lib_scp.txt', ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>\n", __FILE__, __LINE__);
//fncDebug('lib_scp.txt', $arySalesRemains, __FILE__, __LINE__);
//fncDebug('lib_scp.txt', $aryReceiveDetail, __FILE__, __LINE__);



	if ( !is_array($aryReceiveDetail) )
	{
		return 1;
	}

	// 売上明細行データとして表示させるデータを持つ配列変数の初期化
	$arySalesRemains_New = array();


	// 受注時の通貨単位コードより処理対象桁数を設定
	if ( $lngMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$lngDigitNumber = 0;		// 日本円の場合は０桁
	}
	else
	{
		$lngDigitNumber = 2;		// 日本円以外の場合は小数点以下２桁
	}

	// 売上データが存在する場合、変数が配列となる為、処理の振り分け
	// 配列では無い場合、売上データが存在しない
	if ( !is_array($arySalesRemains) )
	{

		for( $i = 0; $i < count($aryReceiveDetail); $i++ )
		{
			// 受注残の情報より指定の端数処理を行う
			$arySalesRemains_New[$i]["lngorderdetailno"] 		= $aryReceiveDetail[$i]["lngorderdetailno"];		// 明細行番号
			$arySalesRemains_New[$i]["strproductcode"]			= $aryReceiveDetail[$i]["strproductcode"];			// 製品コード
			$arySalesRemains_New[$i]["lngsalesclasscode"]		= $aryReceiveDetail[$i]["lngsalesclasscode"];		// 売上区分コード
			$arySalesRemains_New[$i]["dtmdeliverydate"] 		= str_replace( "-", "/", $aryReceiveDetail[$i]["dtmdeliverydate"]);	// 納品日（取得文字列置換）
			$arySalesRemains_New[$i]["lngconversionclasscode"]	= $aryReceiveDetail[$i]["lngconversionclasscode"];	// 換算区分コード
			$arySalesRemains_New[$i]["curproductprice"]			= $aryReceiveDetail[$i]["curproductprice"];			// 製品単価（荷姿単価）
			$arySalesRemains_New[$i]["lngproductquantity"]		= $aryReceiveDetail[$i]["lngproductquantity"];		// 製品数量（荷姿数量）
			$arySalesRemains_New[$i]["lngproductunitcode"]		= $aryReceiveDetail[$i]["lngproductunitcode"];		// 製品単位（荷姿単位）
			$arySalesRemains_New[$i]["lngtaxclasscode"]			= $aryReceiveDetail[$i]["lngtaxclasscode"];			// 消費税区分コード
			$arySalesRemains_New[$i]["lngtaxcode"]				= $aryReceiveDetail[$i]["lngtaxcode"];				// 消費税コード
			$arySalesRemains_New[$i]["curtaxprice"]				= $aryReceiveDetail[$i]["curtaxprice"];				// 消費税金額
			
			// 税抜金額については端数処理の設定より端数処理を行う
			$curSubTotalPrice = $aryReceiveDetail[$i]["lngproductquantity"] * $aryReceiveDetail[$i]["curproductprice"];
			$arySalesRemains_New[$i]["cursubtotalprice"]		= fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $lngDigitNumber );	// 税抜金額
			$arySalesRemains_New[$i]["strdetailnote"]			= $aryReceiveDetail[$i]["strdetailnote"];			// 備考
			$arySalesRemains_New[$i]["lngcartonquantity"]		= $aryReceiveDetail[$i]["lngcartonquantity"];		// カートン入数
			$arySalesRemains_New[$i]["lngreceiveno"] 			= $lngReceiveNo;									// 受注番号
			$arySalesRemains_New[$i]["lngreceivedetailno"] 		= $aryReceiveDetail[$i]["lngreceivedetailno"];		// 受注明細番号
		}

	}
	// 売上データが存在する場合
	else
	{


//fncDebug('lib_scp.txt', $arySalesRemains, __FILE__, __LINE__);


		// 参照元受注の明細毎に取得した受注残にてどのような状態になっているのか調査
		for ( $i = 0; $i < count($arySalesRemains); $i++ )
		{
			// 受注残の明細情報を変数に設定
			$lngOrderDetailNo 		= $arySalesRemains[$i]["lngorderdetailno"];			// 明細行番号
			$strProductCode 		= $arySalesRemains[$i]["strproductcode"];			// 製品コード
			$lngSalesClassCode 		= $arySalesRemains[$i]["lngsalesclasscode"];		// 売上区分コード
			$lngConversionClassCode = $arySalesRemains[$i]["lngconversionclasscode"];	// 換算区分コード
			$curProductPrice		= $arySalesRemains[$i]["curproductprice"];			// 製品単価（荷姿単価）
			$lngProductQuantity		= $arySalesRemains[$i]["lngproductquantity"];		// 製品数量（荷姿数量）
			$lngProductUnitCode		= $arySalesRemains[$i]["lngproductunitcode"];		// 製品単位（荷姿単位）
			$curSubTotalPrice		= $arySalesRemains[$i]["cursubtotalprice"];			// 税抜金額
			$lngCartonQuantity		= $arySalesRemains[$i]["lngcartonquantity"];		// カートン入数
			$lngReceiveNo 			= $arySalesRemains[$i]["lngreceiveno"];				// 受注番号
			$lngTaxClassCode		= $arySalesRemains[$i]["lngtaxclasscode"];			// 税区分コード
			$lngReceiveDetailNo 	= $arySalesRemains[$i]["lngreceivedetailno"];		// 受注明細番号

			if ( $lngTaxClassCode == "" )
			{
				$lngTaxClassCode = 0;
			}
			$curTaxPrice			= $arySalesRemains[$i]["curtaxprice"];				// 税額
			if ( $curTaxPrice == "" )
			{
				$curTaxPrice = 0;
			}

//fncDebug('lib_scp.txt', "++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++", __FILE__, __LINE__);
			for( $j = 0; $j < count($aryReceiveDetail); $j++ )
			{

//fncDebug('lib_scp.txt', $aryReceiveDetail, __FILE__, __LINE__);


			// 明細行に対して同じ内容の明細が受注残のデータに見つかった場合
			if ( $aryReceiveDetail[$j]["lngorderdetailno"] == $lngReceiveDetailNo 
				and $aryReceiveDetail[$j]["strproductcode"] == $strProductCode 
				and $aryReceiveDetail[$j]["lngreceiveno"] == $lngReceiveNo 
			)
			{

//fncDebug('lib_scp.txt', "同一データ：\n".$lngOrderDetailNo."\n".$strProductCode."\n".$lngReceiveNo."\n", __FILE__, __LINE__);

				// 受注残の計上単位と受注の計上単位が違う、また、受注残の計上単位は製品単位計上である
				if ( $aryReceiveDetail[$j]["lngconversionclasscode"] != $lngConversionClassCode 
					and $lngConversionClassCode == DEF_CONVERSION_SEIHIN )
				{
						// 0 割り対策
					if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
					{
						// カートン入り数が ０ だった場合は その製品に対する納品済みかどうかの判断ができないため 強制的に １ にて換算
						$lngCartonQuantity = 1;
					}

					// 受注の計上単位である荷姿単位計上に値を修正
					// 荷姿数量は製品数量 / カートン入数
					$NisugatalngProductQuantity = $lngProductQuantity / $lngCartonQuantity;
					// もし取得した荷姿数量が小数点を含む場合は製品単位数量のままで処理する
					if ( $NisugatalngProductQuantity - floor($NisugatalngProductQuantity) > 0 )
					{
						// その際に製品単位についてはデフォルトの pcs に設定する
						$lngProductUnitCode = DEF_PRODUCTUNIT_PCS;

						// 換算区分については製品単位計上とする
						$lngConversionClassCode = DEF_CONVERSION_SEIHIN;
					}
					else
					// 取得した荷姿数量が小数点を含まない場合は荷姿に変換処理する
					{
						$lngProductQuantity = $NisugatalngProductQuantity;

						// 荷姿価格は製品単価 * カートン入数
						$curProductPrice = $curProductPrice * $lngCartonQuantity;

						// 税抜金額は荷姿単価 * 荷姿数量
						$curSubTotalPrice = $lngProductQuantity * $curProductPrice;

						// その際に製品単位についてはデフォルトの c/t に設定する
						$lngProductUnitCode = DEF_PRODUCTUNIT_CTN;

						// 換算区分については荷姿単位計上とする
						$lngConversionClassCode = DEF_CONVERSION_NISUGATA;
					}
				}
				// 受注残の計上単位と受注の計上単位が違う、また、受注残の計上単位は荷姿単位計上である
				else if ( $lngConversionClassCode != DEF_CONVERSION_SEIHIN )
				{
					// 荷姿でわたってくる際にはその売上計上されていないのと同じなので計算しない

					// その際に製品単位についてはデフォルトの c/t に設定する
					$lngProductUnitCode = DEF_PRODUCTUNIT_CTN;

					// 換算区分については荷姿単位計上とする
					$lngConversionClassCode = DEF_CONVERSION_NISUGATA;
				}
				
// 税抜き金額を計算する際に設定された端数処理を行う
				// 税抜金額を計算する
				// 税抜金額は数量 * 単価
				$curSubTotalPrice = $lngProductQuantity * $curProductPrice;
				$curSubTotalPrice = fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $lngDigitNumber );

// 税区分の設定が内税の場合税額を引いた値を税抜金額とする
				if ( $lngTaxClassCode == DEF_TAXCLASS_UCHIZEI )
				{
					// 税額に値が含まれていない場合
					if ( $curTaxPrice == 0 )
					{
						// 計上日よりその時の税率をもとめる
						$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
							. "FROM m_tax "
							. "WHERE dtmapplystartdate <= '" . $dtmAppropriationDate . "' "
							. "AND dtmapplyenddate >= '" . $dtmAppropriationDate . "' "
							. "GROUP BY lngtaxcode, curtax "
							. "ORDER BY 3 ";

						// 税率などの取得クエリーの実行
						list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

						if ( $lngResultNum == 1 )
						{
							$objResult = $objDB->fetchObject( $lngResultID, 0 );
							$curTax = $objResult->curtax;
						}
						else
						{
							$strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
								. "FROM m_tax "
								. "WHERE dtmapplystartdate=(SELECT MAX(dtmapplystartdate) FROM m_tax) "
								. "GROUP BY lngtaxcode, curtax ";
							// 税率などの取得クエリーの実行
							list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

							if ( $lngResultNum == 1 )
							{
								$objResult = $objDB->fetchObject( $lngResultID, 0 );
								$curTax = $objResult->curtax;
							}
							else
							{
								fncOutputError ( 9051, DEF_ERROR, "消費税情報の取得に失敗しました。", TRUE, "", $objDB );
							}
						}
						$objDB->freeResult( $lngResultID );

						$curTaxPrice = $curSubTotalPrice * $curTax;
						// 端数処理を行う
						$curTaxPrice = fncCalcDigit( $curTaxPrice, $lngCalcCode, $lngDigitNumber );
					}
					// 税抜金額は単価×数量−税額
					$curSubTotalPrice = $curSubTotalPrice - $curTotalPrice;
				}
				// for 文抜け
				//break;
			}
			}
			
			// 変換された値を設定する
			$arySalesRemains_New[$i]["lngorderdetailno"] 		= $lngOrderDetailNo;								// 明細行番号
			$arySalesRemains_New[$i]["strproductcode"]			= $strProductCode;									// 製品コード
			$arySalesRemains_New[$i]["lngsalesclasscode"]		= $lngSalesClassCode;								// 売上区分コード
			$arySalesRemains_New[$i]["dtmdeliverydate"] 		
					= str_replace( "-", "/", $arySalesRemains[$i]["dtmdeliverydate"]);								// 納品日（取得文字列置換）
			$arySalesRemains_New[$i]["lngconversionclasscode"]	= $lngConversionClassCode;							// 換算区分コード
			$arySalesRemains_New[$i]["curproductprice"]			= $curProductPrice;									// 製品単価（荷姿単価）
			$arySalesRemains_New[$i]["lngproductquantity"]		= $lngProductQuantity;								// 製品数量（荷姿数量）
			$arySalesRemains_New[$i]["lngproductunitcode"]		= $lngProductUnitCode;								// 製品単位（荷姿単位）
			$arySalesRemains_New[$i]["lngtaxclasscode"]			= $arySalesRemains[$i]["lngtaxclasscode"];			// 消費税区分コード
			$arySalesRemains_New[$i]["lngtaxcode"]				= $arySalesRemains[$i]["lngtaxcode"];				// 消費税コード
			$arySalesRemains_New[$i]["curtaxprice"]				= $arySalesRemains[$i]["curtaxprice"];				// 消費税金額
			$arySalesRemains_New[$i]["cursubtotalprice"]		= $curSubTotalPrice;								// 税抜金額
			$arySalesRemains_New[$i]["strdetailnote"]			= $arySalesRemains[$i]["strdetailnote"];			// 備考
			$arySalesRemains_New[$i]["lngcartonquantity"]		= $lngCartonQuantity;								// カートン入数
			$arySalesRemains_New[$i]["lngreceiveno"] 			= $lngReceiveNo;									// 受注番号
			$arySalesRemains_New[$i]["lngreceivedetailno"] 		= $lngReceiveDetailNo;								// 受注明細番号

//fncDebug('lib_scp.txt', $arySalesRemains_New[$i], __FILE__, __LINE__);
		}
	}


	return $arySalesRemains_New;
}



/**
* 指定の受注データに関して、その受注データの売上状態より残数取得関数
*
*	受注数、受注金額よりその受注Noを指定している売上すべてから
*	受注残を取得する
*
*	@param  Integer 	$lngReceiveNo 				受注番号
*	@param	Array		$arySalesDetail				売上登録にて設定された明細情報
*	@param	Integer		$lngReceiveMonetaryUnitCode	受注時の通貨単位コード
*	@param	Integer		$lngSalesMonetaryUnitCode	売上時の通貨単位コード
*	$param	Integer		$lngSalesNo					対象外とする売上No　（売上修正時使用）
*	@param	Integer		$lngCalcCode				端数処理コード
*	@param  Object		$objDB						DBオブジェクト
*	@return Boolean 	0							実行成功
*						1							実行失敗 情報取得失敗
*						50							実行成功　明細の内容に受注残を超える情報はない
*						99							受注残以上に指定されている
*	@access public
*
*	更新履歴
*	2004.04.16	fncGetSalesRemains 関数の引数変更に伴う修正
*	2004.04.19	arySalesDetail 配列のKey項目名を小文字⇒大文字に修正
*	2004.04.20	受注残を求めた後、受注残の単位計上が荷姿であった場合に比較がおかしくなるバグの修正
*/
function fncGetStatusSalesRemains ( $lngReceiveNo, $arySalesDetail, $lngReceiveMonetaryUnitCode, $lngSalesMonetaryUnitCode, 
									$lngSalesNo, $lngCalcCode, $objDB )
{
	
	// 受注残を求める関数の呼び出し
	$aryRemainsDetail = fncGetSalesRemains ( $lngReceiveNo, $lngSalesNo, $lngCalcCode, $objDB );
fncDebug('lib_scp.txt', $aryRemainsDetail, __FILE__, __LINE__);

	// 関数結果より
	if ( $aryRemainsDetail == 1 )
	{
		// 異常終了
		return 1;
	}
	else if ( $aryRemainsDetail == 0 )
	{
		return 0;
	}

	// 受注時の通貨単位コードより処理対象桁数を設定
	if ( $lngReceiveMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$lngDigitNumber = 0;		// 日本円の場合は０桁
	}
	else
	{
		$lngDigitNumber = 2;		// 日本円以外の場合は小数点以下２桁
	}

	// 受注残が存在すれば
	// 今回指定された売上と調査した受注残をチェックし、受注残以上に注文していないかどうかを調査する
	for ( $i = 0; $i < count($aryRemainsDetail); $i++ )
	{
		$lngOrderDetailNo 		= $aryRemainsDetail[$i]["lngorderdetailno"];			// 明細行番号

		$strProductCode 		= $aryRemainsDetail[$i]["strproductcode"];				// 製品コード
		$lngSalesClassCode 		= $aryRemainsDetail[$i]["lngsalesclasscode"];			// 売上区分コード
		$lngConversionClassCode = $aryRemainsDetail[$i]["lngconversionclasscode"];		// 換算区分コード
		$curProductPrice		= $aryRemainsDetail[$i]["curproductprice"];				// 製品単価（荷姿単価）
		$lngProductQuantity		= $aryRemainsDetail[$i]["lngproductquantity"];			// 製品数量（荷姿数量）
		$lngProductUnitCode		= $aryRemainsDetail[$i]["lngproductunitcode"];			// 製品単位（荷姿単位）
		$curSubTotalPrice		= $aryRemainsDetail[$i]["cursubtotalprice"];			// 税抜金額
		$lngCartonQuantity		= $aryRemainsDetail[$i]["lngcartonquantity"];			// カートン入数
		$lngReceiveNo			= $aryRemainsDetail[$i]["lngreceiveno"];				// 受注番号
		$lngReceiveDetailNo		= $aryRemainsDetail[$i]["lngreceivedetailno"];			// 受注明細番号
		

		// 換算区分が荷姿計上であった場合は、製品単位計上に変更する
		if ( $lngConversionClassCode != DEF_CONVERSION_SEIHIN )
		{
			// 0 割り対策
			if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
			{
				// カートン入り数が ０ だった場合は その製品に対する納品済みかどうかの判断ができないため 強制的に １ にて換算
				$lngCartonQuantity = 1;
			}

			// 製品数量は荷姿数量 * カートン入数
			$lngProductQuantity = $lngProductQuantity * $lngCartonQuantity;

			// 製品価格は荷姿単価 / カートン入数
			$curProductPrice = $curProductPrice / $lngCartonQuantity;

			// 税抜金額は製品数量 * 製品価格
			$curSubTotalPrice = $lngGoodsQuantity * $curProductPrice;

			// 税抜き金額を計算する際に設定された端数処理を行う
			$curSubTotalPrice = fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $lngDigitNumber );

			// 単位は製品単位
			$lngProductUnitCode = DEF_PRODUCTUNIT_PCS;

			// 換算区分コードは製品単位に修正
			$lngConversionClassCode = DEF_CONVERSION_SEIHIN;
		}

		for ( $j = 0; $j < count($arySalesDetail); $j++ )
		{
			// 受注残明細行番号に対して売上明細行番号が同じ、かつ製品コードが同じ明細が見つかった場合
			if ( $lngOrderDetailNo	== $arySalesDetail[$j]["lngReceiveDetailNo"]	// $lngOrderDetailNo	== $arySalesDetail[$j]["lngOrderDetailNo"]
				and $strProductCode	== $arySalesDetail[$j]["strProductCode"]
				and $lngReceiveNo	== $arySalesDetail[$j]["lngReceiveNo"]
				and $lngReceiveMonetaryUnitCode == $lngSalesMonetaryUnitCode )
			{
				// 換算区分が荷姿計上であった場合は、製品単位計上に変更する
				if ( $arySalesDetail[$j]["lngConversionClassCode"] != "gs" )
				{
					// 今回の売上情報にはカートン入り数の情報を持っていないので
					// ここでは製品コードが同じということから受注残のカートン入り数を使用する
					// 0 割り対策
					if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
					{
						// カートン入り数が ０ だった場合は その製品に対する納品済みかどうかの判断ができないため 強制的に １ にて換算
						$lngCartonQuantity = 1;
					}

// 2004.04.19 suzukaze update start
					// 製品数量は荷姿数量 * カートン入数
					$arySalesDetail[$j]["lngGoodsQuantity"] 
						= $arySalesDetail[$j]["lngGoodsQuantity"] * $lngCartonQuantity;

					// 製品価格は荷姿単価 / カートン入数
					$arySalesDetail[$j]["curProductPrice"] 
						= $arySalesDetail[$j]["curProductPrice"] / $lngCartonQuantity;

					// 税抜金額は製品数量 * 製品価格
					$arySalesDetail[$j]["curSubTotalPrice"] 
						= $arySalesDetail[$j]["lngGoodsQuantity"] * $arySalesDetail[$j]["curProductPrice"];

// 2004.04.16 suzukaze update start
// 税抜き金額を計算する際に設定された端数処理を行う
					$arySalesDetail[$j]["curSubTotalPrice"] 
						= fncCalcDigit( $arySalesDetail[$j]["curSubTotalPrice"], $lngCalcCode, $lngDigitNumber );
// 2004.04.16 suzukaze update end

					// 単位は製品単位
					$arySalesDetail[$j]["lngProductUnitCode"] = DEF_PRODUCTUNIT_PCS;

					// 換算区分コードは製品単位に修正
					$arySalesDetail[$j]["lngConversionClassCode"] = DEF_CONVERSION_SEIHIN;
				}

				// 数量比較
				if ( $lngProductQuantity < $arySalesDetail[$j]["lngGoodsQuantity"] )
				{
					// 数量が受注残数以上
					return 99;
				}
// 2004.04.19 suzukaze update end

				// 税抜金額比較
				if ( $curSubTotalPrice < $arySalesDetailResult[$j]["curSubTotalPrice"] )
				{
					// 税抜金額が受注残以上
					return 99;
				}

				// 受注残に同じ明細情報が見つかった場合は次の行を処理
				break;
			}
		}
	}
	return 50;	// 実行成功　今回の売上に受注残を越える情報はない
}



/**
* 指定の売上データの登録に関して、その売上データを登録することでの状態変更関数
*
*	売上の状態が「納品済」の場合、受注Noを指定していた場合、分納であった場合など
*	各状態ごとにその売上に関するデータの状態を変更する
*
*	@param  Integer 	$lngReceiveNo 	売上が参照している受注No
*	@param	Integer		$lngCalcCode	端数処理コード
*	@param  Object		$objDB			DBオブジェクト
*	@return Boolean 	0				実行成功
*						1				実行失敗 情報取得失敗
*	@access public
*
*	更新履歴
*	2004.04.19	端数処理コードの追加
*/
function fncSalesSetStatus ( $lngReceiveNo, $lngCalcCode, $objDB )
{
	// 受注番号が存在しない場合そのまま終了
	if ( $lngReceiveNo == "" or $lngReceiveNo == 0 )
	{
		return 1;
	}

	// 最新の受注のデータを取得する
	$arySql = array();
	$arySql[] = "SELECT";
	$arySql[] = "	r.lngReceiveNo		as lngReceiveNo";
	$arySql[] = "	,r.strReceiveCode	as strReceiveCode";
	$arySql[] = "	,r.lngReceiveStatusCode	as lngReceiveStatusCode";
	$arySql[] = "	,r.lngMonetaryUnitCode	as lngMonetaryUnitCode";
	$arySql[] = "	,r.strcustomerreceivecode";
	$arySql[] = "FROM";
	$arySql[] = "	m_Receive r";
	$arySql[] = "WHERE";
	$arySql[] = "	r.strReceiveCode = (";
	$arySql[] = "	SELECT r1.strReceiveCode FROM m_Receive r1 WHERE r1.lngReceiveNo = " . $lngReceiveNo;
	$arySql[] = "	)";
	$arySql[] = "	AND r.bytInvalidFlag = FALSE";
	$arySql[] = "	AND r.lngRevisionNo >= 0";
	$arySql[] = "	AND r.lngRevisionNo = (";
	$arySql[] = "		SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode";
	$arySql[] = "		AND r2.strReviseCode = (";
	$arySql[] = "		SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode )";
	$arySql[] = "	)";
	$arySql[] = "	AND 0 <= (";
	$arySql[] = "		SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode";
	$arySql[] = "	)";
	$strQuery = implode("\n", $arySql);

	// 検索クエリーの実行
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );


//fncDebug('lib_scp.txt', $strQuery, __FILE__, __LINE__);


	if ( $lngResultNum == 1 )
	{
		$objResult			= $objDB->fetchObject( $lngResultID, 0 );
		$lngNewReceiveNo	= $objResult->lngreceiveno;
		$strNewReceiveCode	= $objResult->strreceivecode;
//		$strNewReceiveCode	= $objResult->strcustomerreceivecode;
		$lngNewReceiveStatusCode	= $objResult->lngreceivestatuscode;
		$ReceivelngMonetaryUnitCode	= $objResult->lngmonetaryunitcode;
	}
	else
	{
		// 受注Noは指定しているが現在有効な最新受注が存在しない場合はそのまま終了
		return 1;
	}
	$objDB->freeResult( $lngResultID );

	// 受注時の通貨単位コードより処理対象桁数を設定
	if ( $ReceivelngMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$lngDigitNumber = 0;		// 日本円の場合は０桁
	}
	else
	{
		$lngDigitNumber = 2;		// 日本円以外の場合は小数点以下２桁
	}

	$arySql = array();
	$arySql[] = "SELECT";
	$arySql[] = "	rd.lngReceiveDetailNo";
	$arySql[] = "	,rd.strProductCode		as strProductCode";
	$arySql[] = "	,rd.lngSalesClassCode	as lngSalesClassCode";
	$arySql[] = "	,rd.lngConversionClassCode	as lngConversionClassCode";
	$arySql[] = "	,rd.curProductPrice		as curProductPrice";
	$arySql[] = "	,rd.lngProductQuantity	as lngProductQuantity";
	$arySql[] = "	,rd.lngProductUnitCode	as lngProductUnitCode";
	$arySql[] = "	,rd.curSubTotalPrice	as curSubTotalPrice";
	$arySql[] = "	,p.lngCartonQuantity	as lngCartonQuantity";
	$arySql[] = "FROM";
	$arySql[] = "	t_ReceiveDetail rd";
	$arySql[] = "	,m_Product p";
	$arySql[] = "WHERE";
	$arySql[] = "	rd.lngReceiveNo = " . $lngNewReceiveNo;
	$arySql[] = "	AND rd.strProductCode = p.strProductCode";
	$arySql[] = "ORDER BY lngSortKey ASC";
	
	// 最新受注の明細情報を取得する
	$strQuery = implode("\n", $arySql);

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryReceiveDetail[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		// 明細行が存在しない場合異常データ
		return 2;
	}
	$objDB->freeResult( $lngResultID );

	// 同じ「受注No」を指定している最新売上を検索
	$arySql = array();
	$arySql[] = "SELECT distinct";
	$arySql[] = "	s.lngSalesNo as lngSalesNo";
	$arySql[] = "	,s.lngSalesStatusCode as lngSalesStatusCode";
	$arySql[] = "	,s.lngMonetaryUnitCode as lngMonetaryUnitCode";
//	$arySql[] = "	,tsd.lngreceiveno";
	$arySql[] = "FROM";
	$arySql[] = "	m_Sales s";
	$arySql[] = "	left join t_salesdetail tsd";
	$arySql[] = "		on s.lngsalesno = tsd.lngsalesno";
	$arySql[] = "	,m_Receive r";
	$arySql[] = "WHERE";
	$arySql[] = "	r.strReceiveCode = '" . $strNewReceiveCode . "'";
//	$arySql[] = "	r.strcustomerReceiveCode = '" . $strNewReceiveCode . "'";
	$arySql[] = "	AND r.lngReceiveNo = tsd.lngReceiveNo";
	$arySql[] = "	AND s.bytInvalidFlag = FALSE";
	$arySql[] = "	AND s.lngRevisionNo >= 0";
	$arySql[] = "	AND s.lngRevisionNo = (";
	$arySql[] = "		SELECT MAX( s2.lngRevisionNo ) FROM m_Sales s2 WHERE s2.strSalesCode = s.strSalesCode )";
	$arySql[] = "		AND 0 <= (";
	$arySql[] = "		SELECT MIN( s3.lngRevisionNo ) FROM m_Sales s3 WHERE s3.bytInvalidFlag = false AND s3.strSalesCode = s.strSalesCode";
	$arySql[] = "		)";
	$strQuery = implode("\n", $arySql);
//fncDebug('lib_scp.txt', $strQuery, __FILE__, __LINE__);
//exit;

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		$arySales = array();
		$arySalesDetail = array();
		// 売上データが存在する場合
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$arySales[] = $objDB->fetchArray( $lngResultID, $i );

			$arySql = array();
			$arySql[] = "SELECT";
			$arySql[] = "	sd.lngreceiveno";
			$arySql[] = "	,sd.lngreceivedetailno";
			$arySql[] = "	,sd.strProductCode		as strProductCode";
			$arySql[] = "	,sd.lngSalesClassCode	as lngSalesClassCode";
			$arySql[] = "	,sd.lngConversionClassCode as lngConversionClassCode";
			$arySql[] = "	,sd.curProductPrice		as curProductPrice";
			$arySql[] = "	,sd.lngProductQuantity	as lngProductQuantity";
			$arySql[] = "	,sd.lngProductUnitCode	as lngProductUnitCode";
			$arySql[] = "	,sd.curSubTotalPrice	as curSubTotalPrice";
			$arySql[] = "	,p.lngCartonQuantity	as lngCartonQuantity";
			$arySql[] = "FROM";
			$arySql[] = "	t_SalesDetail sd";
			$arySql[] = "	,m_Product p";
			$arySql[] = "WHERE";
			$arySql[] = "	sd.lngSalesNo = " . $arySales[$i]["lngsalesno"];
			$arySql[] = "	AND sd.strProductCode = p.strProductCode";
			$arySql[] = "ORDER BY lngSortKey ASC";
			
			// 明細情報を取得する
			$strSalesDetailQuery = implode("\n", $arySql);
//fncDebug('lib_scp.txt', $strSalesDetailQuery, __FILE__, __LINE__);
			list ( $lngSalesDetailResultID, $lngSalesDetailResultNum ) = fncQuery( $strSalesDetailQuery, $objDB );

			if ( $lngSalesDetailResultNum )
			{
				for ( $j = 0; $j < $lngSalesDetailResultNum; $j++ )
				{
					$arySalesDetail[$i][] = $objDB->fetchArray( $lngSalesDetailResultID, $j );
				}
			}
			$objDB->freeResult( $lngSalesDetailResultID );
		}

//fncDebug('lib_scp.txt', $arySalesDetail, __FILE__, __LINE__);

		// 参照元受注の明細毎に取得した売上にてどのような状態になっているのか調査
		for ( $i = 0; $i < count($aryReceiveDetail); $i++ )
		{
			// 参照元受注の明細行番号を取得･････明細行番号にひもづいて売上が消しこみされるため
//			$lngTSDReceiveNo 		= $aryReceiveDetail[$i]["lngreceiveno"];			// 受注番号
			$lngReceiveDetailNo 	= $aryReceiveDetail[$i]["lngreceivedetailno"];		// 明細行番号

			$strProductCode 		= $aryReceiveDetail[$i]["strproductcode"];			// 製品コード
			$lngSalesClassCode 		= $aryReceiveDetail[$i]["lngsalesclasscode"];			// 売上区分コード
			$lngConversionClassCode = $aryReceiveDetail[$i]["lngconversionclasscode"];	// 換算区分コード
			$curProductPrice		= $aryReceiveDetail[$i]["curproductprice"];			// 製品単価（荷姿単価）
			$lngProductQuantity		= $aryReceiveDetail[$i]["lngproductquantity"];		// 製品数量（荷姿数量）
			$lngProductUnitCode		= $aryReceiveDetail[$i]["lngproductunitcode"];		// 製品単位（荷姿単位）
			$curSubTotalPrice		= $aryReceiveDetail[$i]["cursubtotalprice"];		// 税抜金額
			$lngCartonQuantity		= $aryReceiveDetail[$i]["lngcartonquantity"];		// カートン入数

			// 換算区分が荷姿単位計上の場合、製品単価へ計算
			if ( $lngConversionClassCode != DEF_CONVERSION_SEIHIN )
			{
				// 0 割り対策
				if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
				{
					// カートン入り数が ０ だった場合は その製品に対する納品済みかどうかの判断ができないため 強制的に １ にて換算
					$lngCartonQuantity = 1;
				}

				// 製品数量は荷姿数量 * カートン入数
				$lngProductQuantity = $lngProductQuantity * $lngCartonQuantity;

				// 製品価格は荷姿単価 / カートン入数
				$curProductPrice = $curProductPrice / $lngCartonQuantity;

				// 税抜金額は製品単価 * 製品数量
				$curSubTotalPrice = $lngProductQuantity * $curProductPrice;

				// 税抜き金額を計算する際に設定された端数処理を行う
				// 税抜金額を計算する
				// 税抜金額は数量 * 単価
				$curSubTotalPrice = $lngProductQuantity * $curProductPrice;
				$curSubTotalPrice = fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $lngDigitNumber );
			}

			$bytEndFlag = 0;
			$lngSalesProductQuantity = 0;
			$curSalesSubTotalPrice = 0;
			
			for ( $j = 0; $j < count($arySales); $j++ )
			{
				$SaleslngMonetaryUnitCode = $arySales[$j]["lngmonetaryunitcode"];

				// 売上時の通貨単位コードより処理対象桁数を設定
				if ( $SaleslngMonetaryUnitCode == DEF_MONETARY_YEN )
				{
					$SaleslngDigitNumber = 0;		// 日本円の場合は０桁
				}
				else
				{
					$SaleslngDigitNumber = 2;		// 日本円以外の場合は小数点以下２桁
				}


				for ( $k = 0; $k < count($arySalesDetail[$j]); $k++ )
				{

					// 受注明細行番号に対して売上明細行番号が同じ、かつ製品コードが同じ明細が見つかった場合
					// それに加え　通貨が同じ場合
					if ( $lngReceiveDetailNo == $arySalesDetail[$j][$k]["lngreceivedetailno"]
						and $strProductCode == $arySalesDetail[$j][$k]["strproductcode"] 
						and $ReceivelngMonetaryUnitCode == $SaleslngMonetaryUnitCode )
					{
//fncDebug('lib_scp.txt', $strProductCode ."=".$lngReceiveDetailNo, __FILE__, __LINE__);

						// 換算区分が荷姿単位計上の場合、製品単価へ計算
						if ( $arySalesDetail[$j][$k]["lngconversionclasscode"] != DEF_CONVERSION_SEIHIN )
						{
							// 0 割り対策
							if ( $arySalesDetail[$j][$k]["lngcartonquantity"] == 0 or $arySalesDetail[$j][$k]["lngcartonquantity"] == "" )
							{
								// カートン入り数が ０ だった場合は その製品に対する納品済みかどうかの判断ができないため 強制的に １ にて換算
								$arySalesDetail[$j][$k]["lngcartonquantity"] = 1;
							}

							// 製品数量は荷姿数量 * カートン入数
							$arySalesDetail[$j][$k]["lngproductquantity"] 
								= $arySalesDetail[$j][$k]["lngproductquantity"] * $arySalesDetail[$j][$k]["lngcartonquantity"];

							// 製品価格は荷姿単価 / カートン入数
							$arySalesDetail[$j][$k]["curproductprice"] 
								= $arySalesDetail[$j][$k]["curproductprice"] / $arySalesDetail[$j][$k]["lngcartonquantity"];

							// 税抜金額は荷姿単価 * 荷姿数量
							$arySalesDetail[$j][$k]["cursubtotalprice"] 
								= $arySalesDetail[$j][$k]["lngproductquantity"] * $arySalesDetail[$j][$k]["curproductprice"];

							// 税抜き金額を計算する際に設定された端数処理を行う
							// 端数処理を行う
							$arySalesDetail[$j][$k]["cursubtotalprice"] 
								= fncCalcDigit( $arySalesDetail[$j][$k]["cursubtotalprice"], $lngCalcCode, $SaleslngDigitNumber );
						}

						// 数量比較
						if ( $lngProductQuantity > $arySalesDetail[$j][$k]["lngproductquantity"] )
						{
							$lngSalesProductQuantity += $arySalesDetail[$j][$k]["lngproductquantity"];
							// 複数売上からの合算での数量比較
							if ( $lngProductQuantity <= $lngSalesProductQuantity )
							{
								$bytEndFlag = 99;
								break;
							}
						}
						else
						{
							$bytEndFlag = 99;
							break;
						}
						
						// 税抜金額比較
						if ( $curSubTotalPrice > $arySalesDetail[$j]["cursubtotalprice"] )
						{
							$curSalesSubTotalPrice += $arySalesDetail[$j]["cursubtotalprice"];
							// 複数売上からの合算での税抜金額比較
							if ( $curSubTotalPrice <= $curSalesSubTotalPrice )
							{
								$bytEndFlag = 99;
								break;
							}
						}
						else
						{
							$bytEndFlag = 99;
							break;
						}

						// 同じ明細行の情報が受注と売上で見つかった際には「納品中」となるため以下設定
						$bytEndFlag = 1;
					}
				}
				// 売上明細に受注明細と同内容が見つかった場合は　for 文抜け
				if ( $bytEndFlag == 99 )
				{
					break;
				}
			}
			// 受注明細行毎の売上明細行が見つかった状態を記憶
			$aryStatus[] = $bytEndFlag;
		}
		
		// 再度チェック　$aryStatus（明細ごとの状態）により受注全体としての状態を判断
		$flagZERO = 0;
		$flagALL  = 0;
		for ( $i = 0; $i < count($aryStatus); $i++ )
		{
			if ( $aryStatus[$i] == 0 )
			{
				$flagZERO++;
			}
			if ( $aryStatus[$i] == 99 )
			{
				$flagALL++;
			}
		}
//exit;

		// 受注明細に対して一件も売上が発生していない場合、または完納ではない場合
		// （flagZEROが受注明細数に対してイコールの場合実際は初期状態であるが、売上にて
		//   受注Noが指定されているのでここでの状態は「納品中」とする）
		if ( $flagALL != count($aryStatus) )
		{
			// 売上参照受注の状態の状態を「納品中」とする
		
			// 更新対象受注データをロックする
			$strLockQuery = "SELECT lngReceiveNo FROM m_Receive WHERE lngReceiveNo = " . $lngNewReceiveNo . " AND bytInvalidFlag = FALSE FOR UPDATE";

			list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
			$objDB->freeResult( $lngLockResultID );

			// 「納品中」状態への更新処理
			$strUpdateQuery = "UPDATE m_Receive set lngReceiveStatusCode = " . DEF_RECEIVE_DELIVER . " WHERE lngReceiveNo = " . $lngNewReceiveNo;

			list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
			$objDB->freeResult( $lngUpdateResultID );

			// 同じ受注NOを指定している売上の状態に対しても「納品中」とする
			for ( $i = 0; $i < count($arySales); $i++ )
			{
				// 更新対象売上データをロックする
				$strLockQuery = "SELECT lngSalesNo FROM m_Sales " 
					. "WHERE lngSalesNo = " . $arySales[$i]["lngsalesno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";

				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				$objDB->freeResult( $lngLockResultID );

				// 「納品中」状態への更新処理
				$strUpdateQuery = "UPDATE m_Sales set lngSalesStatusCode = " . DEF_SALES_DELIVER 
					. " WHERE lngSalesNo = " . $arySales[$i]["lngsalesno"];

				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
			}
			
			return 0;
		}
		else
		// 対象受注は完納状態であったら
		{
			// 売上参照受注の状態の状態を「納品済」とする
		
			// 更新対象受注データをロックする
			$strLockQuery = "SELECT lngReceiveNo FROM m_Receive WHERE lngReceiveNo = " . $lngNewReceiveNo . " AND bytInvalidFlag = FALSE FOR UPDATE";
			list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
			$objDB->freeResult( $lngLockResultID );

			// 「納品済」状態への更新処理
			$strUpdateQuery = "UPDATE m_Receive set lngReceiveStatusCode = " . DEF_RECEIVE_END . " WHERE lngReceiveNo = " . $lngNewReceiveNo;

			list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
			$objDB->freeResult( $lngUpdateResultID );

			// 同じ受注NOを指定している売上の状態に対しても「納品済」とする
			for ( $i = 0; $i < count($arySales); $i++ )
			{
				// 更新対象売上データをロックする
				$strLockQuery = "SELECT lngSalesNo FROM m_Sales " 
					. "WHERE lngSalesNo = " . $arySales[$i]["lngsalesno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				$objDB->freeResult( $lngLockResultID );

				// 「納品済」状態への更新処理
				$strUpdateQuery = "UPDATE m_Sales set lngSalesStatusCode = " . DEF_SALES_END 
					. " WHERE lngSalesNo = " . $arySales[$i]["lngsalesno"];

				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
			}
			return 0;
		}
	}
	else
	{
		// 売上データが存在しない場合
		// 売上の参照元最新受注の状態を「受注」に戻す
		
		// 更新対象受注データをロックする
		$strLockQuery = "SELECT lngReceiveNo FROM m_Receive WHERE lngReceiveNo = " . $lngNewReceiveNo . " AND bytInvalidFlag = FALSE FOR UPDATE";
		list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
		if ( !$lngLockResultNum )
		{
			fncOutputError ( 9051, DEF_ERROR, "無効化処理エラー", TRUE, "", $objDB );
		}
		$objDB->freeResult( $lngLockResultID );

		// 「受注」状態への更新処理
		$strUpdateQuery = "UPDATE m_Receive set lngReceiveStatusCode = " . DEF_RECEIVE_ORDER . " WHERE lngReceiveNo = " . $lngNewReceiveNo;

		list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
		$objDB->freeResult( $lngUpdateResultID );

		return 0;
	}

	$objDB->freeResult( $lngResultID );

	return 0;
}

?>