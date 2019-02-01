<?
/** 
*	仕入　分納用チェック関数群
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.01
*
*	処理概要
*	分納チェック
*
*	修正履歴
*	2004.03.29	チェックにて取得する明細の順序を表示用ソートキー順に変更
*	2004.03.30	仕入科目、仕入部品が変更された場合でも、明細行番号のみを判断基準として相殺するように変更する
*	2004.03.30	発注残を求める際に税区分などの値にデフォルト値を挿入するように変更
*	2004.04.12	発注残を求める際に端数処理をどうするかという引数の追加
*	2004.04.12	発注残数の計上単位等の計算関数にて仕入が発生していない場合でも指定の端数処理を行うように変更
*	2004.04.16	発注残を求めるロジックにて税抜金額が内税の際に本来の税抜金額値になっていなかったバグの修正
*	2004.04.19	aryStockDetail 配列のKey項目名を小文字⇒大文字に修正 かつ lngProductQuantity となっていた一部を lngGoodsQuantity に変更
*	2004.04.20	fncGetStatusStockRemains 関数にて引数中の発注残が荷姿単位計上で渡ってきた際の比較対照バグを修正
*/

/**
* 指定の発注データに関して、その発注データの仕入状態より残数取得関数
*
*	発注数、発注金額よりその発注Noを指定している仕入すべてから
*	発注残を取得する
*
*	@param  Integer 	$lngOrderNo 	発注番号
*	@param  Integer 	$lngStockNo 	対象外としない仕入No　仕入修正時使用
*	@param	Integer		$lngCalcCode	端数処理コード
*	@param  Object		$objDB			DBオブジェクト
*	@return Boolean 	0				実行成功
*						1				実行失敗 情報取得失敗
*	@access public
*
*	更新履歴
*	2004.04.16	注残数を求める際に端数処理、内税対策を行うように変更
*       2013.05.31　　　　税率取得処理修正（期間内の税率取得できない場合、最新期間の税率を取得する ）
*/
function fncGetStockRemains ( $lngOrderNo, $lngStockNo, $lngCalcCode, $objDB )
{
	// 発注番号が存在しない場合そのまま終了
	if ( $lngOrderNo == "" or $lngOrderNo == 0 )
	{
		return 0;
	}

	// 最新の発注のデータを取得する
	$strQuery = "SELECT o.lngOrderNo as lngOrderNo, o.strOrderCode as strOrderCode, "
		. "o.lngOrderStatusCode as lngOrderStatusCode, o.lngMonetaryUnitCode as lngMonetaryUnitCode FROM m_Order o "
		. "WHERE o.strOrderCode = ( "
		. "SELECT o1.strOrderCode FROM m_Order o1 WHERE o1.lngOrderNo = " . $lngOrderNo . " ) "
		. "AND o.bytInvalidFlag = FALSE "
		. "AND o.lngRevisionNo >= 0 "
		. "AND o.lngRevisionNo = ( "
		. "SELECT MAX( o2.lngRevisionNo ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode ) "
		. "AND 0 <= ( "
		. "SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode ) ";

	// 検索クエリーの実行
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngNewOrderNo = $objResult->lngorderno;
		$strNewOrderCode = $objResult->strordercode;
		$lngNewOrderStatusCode = $objResult->lngorderstatuscode;
		$OrderlngMonetaryUnitCode = $objResult->lngmonetaryunitcode;
	}
	else
	{
		// 発注Noは指定しているが現在有効な最新発注が存在しない場合はそのまま終了
		return 0;
	}
	$objDB->freeResult( $lngResultID );

// 2004.04.16 suzukaze update start
	// 発注時の通貨単位コードより処理対象桁数を設定
	if ( $OrderlngMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$OrderlngDigitNumber = 0;		// 日本円の場合は０桁
	}
	else
	{
		$OrderlngDigitNumber = 2;		// 日本円以外の場合は小数点以下２桁
	}
// 2004.04.16 suzukaze update end

	// 最新発注の明細情報を取得する
	$strQuery = "SELECT od.lngOrderDetailNo as lngOrderDetailNo, "			// 明細行番号
		. "od.strProductCode as strProductCode, "							// 製品コード
		. "od.lngStockSubjectCode as lngStockSubjectCode, "					// 仕入科目コード
		. "od.lngStockItemCode as lngStockItemCode, "						// 仕入部品コード
		. "od.dtmDeliveryDate as dtmDeliveryDate, "							// 納品日
		. "od.lngDeliveryMethodCode as lngCarrierCode, "					// 運搬方法コード
		. "od.lngConversionClassCode as lngConversionClassCode, "			// 換算区分コード
		. "od.curProductPrice as curProductPrice, "							// 製品価格
		. "od.lngProductQuantity as lngProductQuantity, "					// 製品数量
		. "od.lngProductUnitCode as lngProductUnitCode, "					// 製品単位コード
		. "od.lngTaxClassCode as lngTaxClassCode, "							// 消費税区分コード
		. "od.lngTaxCode as lngTaxCode, "									// 消費税コード
		. "od.curTaxPrice as curTaxPrice, "									// 消費税金額
		. "od.curSubTotalPrice as curSubTotalPrice, "						// 税抜金額
		. "od.strNote as strDetailNote, "									// 備考
		. "od.strMoldNo as strSerialNo, "									// 金型番号
		. "p.lngCartonQuantity as lngCartonQuantity "						// 製品のカートン入数(参考値）
		. "FROM t_OrderDetail od, m_Product p "
		. "WHERE od.lngOrderNo = " . $lngNewOrderNo . " AND od.strProductCode = p.strProductCode "
		. "ORDER BY lngSortKey ASC";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryOrderDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		// 明細行が存在しない場合異常データ
		return 1;
	}
	$objDB->freeResult( $lngResultID );

	// 同じ発注Noを指定している最新仕入を検索
	$strQuery = "SELECT s.lngStockNo as lngStockNo, s.lngStockStatusCode as lngStockStatusCode, "
		. "s.lngMonetaryUnitCode as lngMonetaryUnitCode FROM m_Stock s, m_Order o "
		. "WHERE o.strOrderCode = '" . $strNewOrderCode . "' AND o.lngOrderNo = s.lngOrderNo "
		. "AND s.bytInvalidFlag = FALSE "
		. "AND s.lngRevisionNo >= 0 "
		. "AND s.lngRevisionNo = ( "
		. "SELECT MAX( s2.lngRevisionNo ) FROM m_Stock s2 WHERE s2.strStockCode = s.strStockCode ) "
		. "AND 0 <= ( "
		. "SELECT MIN( s3.lngRevisionNo ) FROM m_Stock s3 WHERE s3.bytInvalidFlag = false AND s3.strStockCode = s.strStockCode ) ";

	// 引数に $lngStockNo が指定されている場合その仕入番号のデータは対象外とする
	if ( $lngStockNo != "" )
	{
		$strQuery = $strQuery 
			. "AND lngStockNo <> " . $lngStockNo . " ";
	}

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		// 仕入データが存在する場合
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryStockResult[] = $objDB->fetchArray( $lngResultID, $i );
			// 明細情報を取得する
			$strStockDetailQuery = "SELECT sd.lngStockDetailNo as lngOrderDetailNo, "	// 明細行番号
				. "sd.strProductCode as strProductCode, "								// 製品コード
				. "sd.lngStockSubjectCode as lngStockSubjectCode, "						// 仕入科目コード
				. "sd.lngStockItemCode as lngStockItemCode, "							// 仕入部品コード
				. "sd.dtmDeliveryDate as dtmDeliveryDate, "								// 納品日
				. "sd.lngDeliveryMethodCode as lngCarrierCode, "						// 運搬方法コード
				. "sd.lngConversionClassCode as lngConversionClassCode, "				// 換算区分コード
				. "sd.curProductPrice as curProductPrice, "								// 製品価格
				. "sd.lngProductQuantity as lngProductQuantity, "						// 製品数量
				. "sd.lngProductUnitCode as lngProductUnitCode, "						// 製品単位コード
				. "sd.lngTaxClassCode as lngTaxClassCode, "								// 消費税区分コード
				. "sd.lngTaxCode as lngTaxCode, "										// 消費税コード
				. "sd.curTaxPrice as curTaxPrice, "										// 消費税金額
				. "sd.curSubTotalPrice as curSubTotalPrice, "							// 税抜金額
				. "sd.strNote as strDetailNote, "										// 備考
				. "sd.strMoldNo as strSerialNo, "										// 金型番号
				. "p.lngCartonQuantity as lngCartonQuantity "							// 製品のカートン入数(参考値）
				. "FROM t_StockDetail sd, m_Product p "
				. "WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"] . " AND sd.strProductCode = p.strProductCode "
				. "ORDER BY lngSortKey ASC";

			list ( $lngStockDetailResultID, $lngStockDetailResultNum ) = fncQuery( $strStockDetailQuery, $objDB );

			if ( $lngStockDetailResultNum )
			{
				for ( $j = 0; $j < $lngStockDetailResultNum; $j++ )
				{
					$aryStockDetailResult[$i][] = $objDB->fetchArray( $lngStockDetailResultID, $j );
				}
			}
			$objDB->freeResult( $lngStockDetailResultID );
		}
	}
	else
	{
		// 仕入が存在しない発注についてはそのまま発注残として設定
		for ( $i = 0; $i < count($aryOrderDetailResult); $i++ )
		{
			$aryRemainsDetail[$i]["lngorderdetailno"] 		= $aryOrderDetailResult[$i]["lngorderdetailno"];			// 明細行番号
			$aryRemainsDetail[$i]["strproductcode"] 		= $aryOrderDetailResult[$i]["strproductcode"];				// 製品コード
			$aryRemainsDetail[$i]["lngstocksubjectcode"] 	= $aryOrderDetailResult[$i]["lngstocksubjectcode"];			// 仕入科目コード
			$aryRemainsDetail[$i]["lngstockitemcode"] 		= $aryOrderDetailResult[$i]["lngstockitemcode"];			// 仕入部品コード
			$aryRemainsDetail[$i]["dtmdeliverydate"] 		
				= str_replace( "-", "/", $aryOrderDetailResult[$i]["dtmdeliverydate"]);									// 納品日（取得文字列置換）
			$aryRemainsDetail[$i]["lngcarriercode"] 		= $aryOrderDetailResult[$i]["lngcarriercode"];				// 運搬方法コード
			$aryRemainsDetail[$i]["lngconversionclasscode"] = $aryOrderDetailResult[$i]["lngconversionclasscode"];		// 換算区分コード
			$aryRemainsDetail[$i]["curproductprice"]		= $aryOrderDetailResult[$i]["curproductprice"];				// 製品単価（荷姿数量）
			$aryRemainsDetail[$i]["lngproductquantity"]		= $aryOrderDetailResult[$i]["lngproductquantity"];			// 製品数量（荷姿数量）
			$aryRemainsDetail[$i]["lngproductunitcode"]		= $aryOrderDetailResult[$i]["lngproductunitcode"];			// 製品単位（荷姿単位）
// 2004.03.30 suzukaze update start
			// 仕入が発生していない場合は、税関連の値を、発注の通貨を参考にデフォルト値を挿入する
			if ( $OrderlngMonetaryUnitCode == DEF_MONETARY_YEN )
			{
				// 通貨が円の場合は税区分を外税にデフォルト設定する
				$aryRemainsDetail[$i]["lngtaxclasscode"]	= DEF_TAXCLASS_SOTOZEI;										// 消費税区分コード（外税）
			}
			else
			{
				// 通貨が円以外の場合は税区分を非課税にデフォルト設定する
				$aryRemainsDetail[$i]["lngtaxclasscode"]	= DEF_TAXCLASS_HIKAZEI;										// 消費税区分コード（非課税）
			}
// 2004.03.30 suzukaze update end
			$aryRemainsDetail[$i]["lngtaxcode"]				= $aryOrderDetailResult[$i]["lngtaxcode"];					// 消費税コード
			$aryRemainsDetail[$i]["curtaxprice"]			= $aryOrderDetailResult[$i]["curtaxprice"];					// 消費税金額
			$aryRemainsDetail[$i]["cursubtotalprice"]		= $aryOrderDetailResult[$i]["cursubtotalprice"];			// 税抜金額
			$aryRemainsDetail[$i]["strdetailnote"]			= $aryOrderDetailResult[$i]["strdetailnote"];				// 備考
			$aryRemainsDetail[$i]["strserialno"]			= $aryOrderDetailResult[$i]["strserialno"];					// 金型番号
			$aryRemainsDetail[$i]["lngcartonquantity"]		= $aryOrderDetailResult[$i]["lngcartonquantity"];			// カートン入数
		}
		$objDB->freeResult( $lngResultID );
		return $aryRemainsDetail;
	}

	$objDB->freeResult( $lngResultID );

	$count = 0;		// 残が見つかった行数カウンタ

	// 参照元発注の明細毎に取得した仕入にてどのような状態になっているのか調査
	for ( $i = 0; $i < count($aryOrderDetailResult); $i++ )
	{
		$lngOrderDetailNo 		= $aryOrderDetailResult[$i]["lngorderdetailno"];			// 明細行番号

		$strProductCode 		= $aryOrderDetailResult[$i]["strproductcode"];				// 商品コード
		$lngStockSubjectCode 	= $aryOrderDetailResult[$i]["lngstocksubjectcode"];			// 仕入科目コード
		$lngStockItemCode 		= $aryOrderDetailResult[$i]["lngstockitemcode"];			// 仕入部品コード
		$lngConversionClassCode = $aryOrderDetailResult[$i]["lngconversionclasscode"];		// 換算区分コード
		$curProductPrice		= $aryOrderDetailResult[$i]["curproductprice"];				// 製品単価（荷姿単価）
		$lngProductQuantity		= $aryOrderDetailResult[$i]["lngproductquantity"];			// 製品数量（荷姿数量）
		$lngProductUnitCode		= $aryOrderDetailResult[$i]["lngproductunitcode"];			// 製品単位（荷姿単位）
		$curSubTotalPrice		= $aryOrderDetailResult[$i]["cursubtotalprice"];			// 税抜金額
		$lngCartonQuantity		= $aryOrderDetailResult[$i]["lngcartonquantity"];			// カートン入数

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

// 2004.04.16 suzukaze update start
// 税抜き金額を計算する際に設定された端数処理を行う
			// 税抜金額を計算する
			// 税抜金額は製品数量 * 製品価格
			$curSubTotalPrice = $lngProductQuantity * $curProductPrice;
			// 端数処理を行う
			$curSubTotalPrice = fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $OrderlngDigitNumber );
// 2004.04.16 suzukaze update end

			// 単位は製品単位
			$lngProductUnitCode = DEF_PRODUCTUNIT_PCS;

			// 換算区分コードは製品単位に修正
			$lngConversionClassCode = DEF_CONVERSION_SEIHIN;
		}

		$bytEndFlag = 0;
		$lngStockProductQuantity = 0;
		$curStockSubTotalPrice = 0;

		for ( $j = 0; $j < count($aryStockResult); $j++ )
		{
			$StocklngMonetaryUnitCode = $aryStockResult[$j]["lngmonetaryunitcode"];

// 2004.04.16 suzukaze update start
			// 仕入時の通貨単位コードより処理対象桁数を設定
			if ( $StocklngMonetaryUnitCode == DEF_MONETARY_YEN )
			{
				$StocklngDigitNumber = 0;		// 日本円の場合は０桁
			}
			else
			{
				$StocklngDigitNumber = 2;		// 日本円以外の場合は小数点以下２桁
			}
// 2004.04.16 suzukaze update end

			for ( $k = 0; $k < count($aryStockDetailResult[$j]); $k++ )
			{
// 2004.03.30 suzukaze update start
				// 発注明細行番号に対して仕入明細行番号が同じ、かつ製品コードが同じ明細が見つかった場合
				if ( $lngOrderDetailNo == $aryStockDetailResult[$j][$k]["lngorderdetailno"] 
					and $strProductCode == $aryStockDetailResult[$j][$k]["strproductcode"] 
//					and $lngStockSubjectCode == $aryStockDetailResult[$j][$k]["lngstocksubjectcode"] 
//					and $lngStockItemCode == $aryStockDetailResult[$j][$k]["lngstockitemcode"] 
					and $OrderlngMonetaryUnitCode == $StocklngMonetaryUnitCode )
// 2004.03.30 suzukaze update end
				{
// 2004.03.30 suzukaze update start
					// 仕入時の税区分を記憶する
					$lngTaxClassCode = $aryStockDetailResult[$j][$k]["lngtaxclasscode"];
// 2004.03.30 suzukaze update end

					// 換算区分が荷姿計上であった場合は、製品単位計上に変更する
					if ( $aryStockDetailResult[$j][$k]["lngconversionclasscode"] != DEF_CONVERSION_SEIHIN )
					{
						// 0 割り対策
						if ( $aryStockDetailResult[$j][$k]["lngcartonquantity"] == 0 or $aryStockDetailResult[$j][$k]["lngcartonquantity"] == "" )
						{
							// カートン入り数が ０ だった場合は その製品に対する納品済みかどうかの判断ができないため 強制的に １ にて換算
							$aryStockDetailResult[$j][$k]["lngcartonquantity"] = 1;
						}

						// 製品数量は荷姿数量 * カートン入数
						$aryStockDetailResult[$j][$k]["lngproductquantity"] 
							= $aryStockDetailResult[$j][$k]["lngproductquantity"] * $aryStockDetailResult[$j][$k]["lngcartonquantity"];

						// 製品価格は荷姿単価 / カートン入数
						$aryStockDetailResult[$j][$k]["curproductprice"] 
							= $aryStockDetailResult[$j][$k]["curproductprice"] / $aryStockDetailResult[$j][$k]["lngcartonquantity"];

						// 税抜金額は製品数量 * 製品価格
						$aryStockDetailResult[$j][$k]["cursubtotalprice"] 
							= $aryStockDetailResult[$j][$k]["lngproductquantity"] * $aryStockDetailResult[$j][$k]["curproductprice"];
// 2004.04.16 suzukaze update start
// 税抜き金額を計算する際に設定された端数処理を行う
						// 端数処理を行う
						$aryStockDetailResult[$j][$k]["cursubtotalprice"] 
							= fncCalcDigit( $aryStockDetailResult[$j][$k]["cursubtotalprice"], $lngCalcCode, $StocklngDigitNumber );
// 2004.04.16 suzukaze update end

						// 単位は製品単位
						$aryStockDetailResult[$j][$k]["lngproductunitcode"] = DEF_PRODUCTUNIT_PCS;

						// 換算区分コードは製品単位に修正
						$aryStockDetailResult[$j][$k]["lngconversionclasscode"] = DEF_CONVERSION_SEIHIN;
					}

					// 数量比較
					if ( $lngProductQuantity > $aryStockDetailResult[$j][$k]["lngproductquantity"] )
					{
						$lngStockProductQuantity += $aryStockDetailResult[$j][$k]["lngproductquantity"];
						// 複数仕入からの合算での数量比較
						if ( $lngProductQuantity <= $lngStockProductQuantity )
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
					if ( $curSubTotalPrice > $aryStockDetailResult[$j]["cursubtotalprice"] )
					{
						$curStockSubTotalPrice += $aryStockDetailResult[$j]["cursubtotalprice"];
						// 複数仕入からの合算での税抜金額比較
						if ( $curSubTotalPrice <= $curStockSubTotalPrice )
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

					// 同じ明細行の情報が発注と仕入で見つかった際には「納品中」となるため以下設定
					$bytEndFlag = 1;
				}
			}
			// 仕入明細に発注明細と同内容が見つかった場合は　for 文抜け
			if ( $bytEndFlag == 99 )
			{
				break;
			}
		}

		// 発注明細行毎の仕入明細行が見つかった状態を記憶
		$aryStatus[] = $bytEndFlag;
		// 発注に対する仕入が見つからなかった場合
		if ( $bytEndFlag == 0 )
		{
			$aryRemainsDetail[$count]["lngorderdetailno"] 		= $lngOrderDetailNo;										// 明細行番号
			$aryRemainsDetail[$count]["strproductcode"] 		= $strProductCode;											// 製品コード
			$aryRemainsDetail[$count]["lngstocksubjectcode"] 	= $lngStockSubjectCode;										// 仕入科目コード
			$aryRemainsDetail[$count]["lngstockitemcode"] 		= $lngStockItemCode;										// 仕入部品コード
			$aryRemainsDetail[$count]["dtmdeliverydate"] 		
				= str_replace( "-", "/", $aryOrderDetailResult[$i]["dtmdeliverydate"]);									// 納品日（取得文字列置換）
			$aryRemainsDetail[$count]["lngcarriercode"] 		= $aryOrderDetailResult[$i]["lngcarriercode"];				// 運搬方法コード
			$aryRemainsDetail[$count]["lngconversionclasscode"] = DEF_CONVERSION_SEIHIN;									// 換算区分コード
			$aryRemainsDetail[$count]["curproductprice"] 		= $curProductPrice;											// 製品単価
			$aryRemainsDetail[$count]["lngproductquantity"] 	= $lngProductQuantity;										// 製品数量
			$aryRemainsDetail[$count]["lngproductunitcode"] 	= $lngProductUnitCode;										// 製品単位
												// （ここでは荷姿であってもそのままの値が設定されているものとする）
// 2004.03.30 suzukaze update start
			// 仕入が発生していない場合は、税関連の値を、発注の通貨を参考にデフォルト値を挿入する
			if ( $OrderlngMonetaryUnitCode == DEF_MONETARY_YEN )
			{
				// 通貨が円の場合は税区分を外税にデフォルト設定する
				$aryRemainsDetail[$count]["lngtaxclasscode"]	= DEF_TAXCLASS_SOTOZEI;								// 消費税区分コード（外税）
			}
			else
			{
				// 通貨が円以外の場合は税区分を非課税にデフォルト設定する
				$aryRemainsDetail[$count]["lngtaxclasscode"]	= DEF_TAXCLASS_HIKAZEI;								// 消費税区分コード（非課税）
			}
// 2004.03.30 suzukaze update end
//			$aryRemainsDetail[$count]["lngtaxclasscode"]		= $aryOrderDetailResult[$i]["lngtaxclasscode"];				// 消費税区分コード
			$aryRemainsDetail[$count]["lngtaxcode"]				= $aryOrderDetailResult[$i]["lngtaxcode"];					// 消費税コード
			$aryRemainsDetail[$count]["curtaxprice"]			= $aryOrderDetailResult[$i]["curtaxprice"];					// 消費税金額
			$aryRemainsDetail[$count]["cursubtotalprice"] 		= $curSubTotalPrice;										// 税抜金額
			$aryRemainsDetail[$count]["strdetailnote"]			= $aryOrderDetailResult[$i]["strdetailnote"];				// 備考
			$aryRemainsDetail[$count]["strserialno"]			= $aryOrderDetailResult[$i]["strserialno"];					// 金型番号
			$aryRemainsDetail[$count]["lngcartonquantity"] 		= $lngCartonQuantity;										// カートン入数

			$count++;	// 残が見つかった行数カウンタをカウントアップ
		}
		// 発注に対する仕入が存在し、まだ完納状態にない場合
		else if ( $bytEndFlag == 1 )
		{
			$aryRemainsDetail[$count]["lngorderdetailno"] 		= $lngOrderDetailNo;										// 明細行番号
			$aryRemainsDetail[$count]["strproductcode"] 		= $strProductCode;											// 製品コード
			$aryRemainsDetail[$count]["lngstocksubjectcode"] 	= $lngStockSubjectCode;										// 仕入科目コード
			$aryRemainsDetail[$count]["lngstockitemcode"] 		= $lngStockItemCode;										// 仕入部品コード
			$aryRemainsDetail[$count]["dtmdeliverydate"] 		
				= str_replace( "-", "/", $aryOrderDetailResult[$i]["dtmdeliverydate"]);									// 納品日（取得文字列置換）
			$aryRemainsDetail[$count]["lngcarriercode"] 		= $aryOrderDetailResult[$i]["lngcarriercode"];				// 運搬方法コード
			$aryRemainsDetail[$count]["lngconversionclasscode"] = DEF_CONVERSION_SEIHIN;									// 換算区分コード
			
			$aryRemainsDetail[$count]["curproductprice"] 		= $curProductPrice;											// 製品単価
			// 製品数量は発注数量　−　仕入総数量
			$aryRemainsDetail[$count]["lngproductquantity"] 	= $lngProductQuantity - $lngStockProductQuantity;			// 製品数量
			$aryRemainsDetail[$count]["lngproductunitcode"] 	= $lngProductUnitCode;										// 製品単位
												// （ここでは荷姿であってもそのままの値が設定されているものとする）
// 2004.03.30 suzukaze update start
			$aryRemainsDetail[$count]["lngtaxclasscode"]		= $lngTaxClassCode;											// 消費税区分コード
// 2004.03.30 suzukaze update end
			$aryRemainsDetail[$count]["lngtaxcode"]				= $aryOrderDetailResult[$i]["lngtaxcode"];					// 消費税コード
			$aryRemainsDetail[$count]["curtaxprice"]			= $aryOrderDetailResult[$i]["curtaxprice"];					// 消費税金額
			// 税抜き金額は発注金額−仕入総金額
			$aryRemainsDetail[$count]["cursubtotalprice"] 		= $curSubTotalPrice - $lngStockSubTotalPrice;				// 税抜金額
			$aryRemainsDetail[$count]["strdetailnote"]			= $aryOrderDetailResult[$i]["strdetailnote"];				// 備考
			$aryRemainsDetail[$count]["strserialno"]			= $aryOrderDetailResult[$i]["strserialno"];					// 金型番号
			$aryRemainsDetail[$count]["lngcartonquantity"] 		= $lngCartonQuantity;										// カートン入数

			$count++;	// 残が見つかった行数カウンタをカウントアップ
		}
	}

	return $aryRemainsDetail;
}






/**
* 指定の発注残数データより発注にて指定されていた計上単位に合わせる処理
*
*	発注残データより発注の際に指定された計上単位に修正する処理
*	発注残を取得する
*
*	@param  array 		$aryStockRemains 		発注残
*	@param  array 		$aryOrderDetail 		発注明細
*	@param	Integer		$lngMonetaryUnitCode	発注時の通貨単位コード
*	@param	Integer		$lngCalcCode			端数処理コード
*	@param	Date		$dtmAppropriationDate	仕入計上日
*	@param  Object		$objDB					DBオブジェクト
*	@return Boolean 	$aryStockRemains_New	実行成功
*						1						実行失敗 情報取得失敗
*	@access public
*/
function fncSetConversionStockRemains ( $aryStockRemains, $aryOrderDetail, $lngMonetaryUnitCode, $lngCalcCode, $dtmAppropriationDate, $objDB )
{
// 2004.04.12 suzukaze update start
	if ( !is_array($aryOrderDetail) )
	{
		return 1;
	}

	// 発注時の通貨単位コードより処理対象桁数を設定
	if ( $lngMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$lngDigitNumber = 0;		// 日本円の場合は０桁
	}
	else
	{
		$lngDigitNumber = 2;		// 日本円以外の場合は小数点以下２桁
	}
// 2004.04.12 suzukaze update end

	if ( is_array($aryStockRemains) )
	{
		// 参照元発注の明細毎に取得した発注残にてどのような状態になっているのか調査
		for ( $i = 0; $i < count($aryStockRemains); $i++ )
		{
			// 発注残の明細情報を変数に設定
			$lngOrderDetailNo 		= $aryStockRemains[$i]["lngorderdetailno"];			// 明細行番号
			$strProductCode 		= $aryStockRemains[$i]["strproductcode"];			// 商品コード
			$lngStockSubjectCode 	= $aryStockRemains[$i]["lngstocksubjectcode"];		// 仕入科目コード
			$lngStockItemCode 		= $aryStockRemains[$i]["lngstockitemcode"];			// 仕入部品コード
			$lngConversionClassCode = $aryStockRemains[$i]["lngconversionclasscode"];	// 換算区分コード
			$curProductPrice		= $aryStockRemains[$i]["curproductprice"];			// 製品単価（荷姿単価）
			$lngProductQuantity		= $aryStockRemains[$i]["lngproductquantity"];		// 製品数量（荷姿数量）
			$lngProductUnitCode		= $aryStockRemains[$i]["lngproductunitcode"];		// 製品単位（荷姿単位）
			$curSubTotalPrice		= $aryStockRemains[$i]["cursubtotalprice"];			// 税抜金額
			$lngCartonQuantity		= $aryStockRemains[$i]["lngcartonquantity"];		// カートン入数
// 2004.04.16 suzukaze update start
			$lngTaxClassCode		= $aryStockRemains[$i]["lngtaxclasscode"];			// 税区分コード
			if ( $lngTaxClassCode == "" )
			{
				$lngTaxClassCode = 0;
			}
			$curTaxPrice			= $aryStockRemains[$i]["curtaxprice"];				// 税額
			if ( $curTaxPrice == "" )
			{
				$curTaxPrice = 0;
			}
// 2004.04.16 suzukaze update end

			for ( $j = 0; $j < count($aryOrderDetail); $j++ )
			{
	// 2004.03.30 suzukaze update start
				// 明細行に対して同じ内容の明細が発注残のデータに見つかった場合
				if ( $aryOrderDetail[$j]["lngorderdetailno"] == $lngOrderDetailNo 
					and $aryOrderDetail[$j]["strproductcode"] == $strProductCode )
	//				and $aryOrderDetail[$j]["lngstocksubjectcode"] == $lngStockSubjectCode 
	//				and $aryOrderDetail[$j]["lngstockitemcode"] == $lngStockItemCode )
	// 2004.03.30 suzukaze update end
				{
					// 発注残の計上単位と発注の計上単位が違う、また、発注残の計上単位は製品単位計上である
					if ( $aryOrderDetail[$j]["lngconversionclasscode"] != $lngConversionClassCode 
						and $lngConversionClassCode == DEF_CONVERSION_SEIHIN )
					{
						// 0 割り対策
						if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
						{
							// カートン入り数が ０ だった場合は その製品に対する納品済みかどうかの判断ができないため 強制的に １ にて換算
							$lngCartonQuantity = 1;
						}

						// 発注の計上単位である荷姿単位計上に値を修正
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
					else if ( $lngConversionClassCode != DEF_CONVERSION_SEIHIN )
					// 発注残の計上単位と発注の計上単位が違う、また、発注残の計上単位は荷姿単位計上である
					{
						// 荷姿でわたってくる際にはその仕入計上されていないのと同じなので計算しない

						// その際に製品単位についてはデフォルトの c/t に設定する
						$lngProductUnitCode = DEF_PRODUCTUNIT_CTN;

						// 換算区分については荷姿単位計上とする
						$lngConversionClassCode = DEF_CONVERSION_NISUGATA;
					}
// 2004.04.09 suzukaze update start
// 税抜き金額を計算する際に設定された端数処理を行う
					// 税抜金額を計算する
					// 税抜金額は数量 * 単価
					$curSubTotalPrice = $lngProductQuantity * $curProductPrice;
					$curSubTotalPrice = fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $lngDigitNumber );
// 2004.04.09 suzukaze update end
// 2004.04.16 suzukaze update start
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
								// 最新の税率情報を取得する 20130531 add
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
// 2004.04.16 suzukaze update end

					// for 文抜け
					break;
				}
			}

			// 変換された値を設定する
			$aryStockRemains_New[$i]["lngorderdetailno"] 		= $lngOrderDetailNo;								// 明細行番号
			$aryStockRemains_New[$i]["strproductcode"]			= $strProductCode;									// 商品コード
			$aryStockRemains_New[$i]["lngstocksubjectcode"]		= $lngStockSubjectCode;								// 仕入科目コード
			$aryStockRemains_New[$i]["lngstockitemcode"]		= $lngStockItemCode;								// 仕入部品コード
			$aryStockRemains_New[$i]["dtmdeliverydate"] 		
					= str_replace( "-", "/", $aryStockRemains[$i]["dtmdeliverydate"]);								// 納品日（取得文字列置換）
			$aryStockRemains_New[$i]["lngcarriercode"] 			= $aryStockRemains[$i]["lngcarriercode"];			// 運搬方法コード
			$aryStockRemains_New[$i]["lngconversionclasscode"]	= $lngConversionClassCode;							// 換算区分コード
			$aryStockRemains_New[$i]["curproductprice"]			= $curProductPrice;									// 製品単価（荷姿単価）
			$aryStockRemains_New[$i]["lngproductquantity"]		= $lngProductQuantity;								// 製品数量（荷姿数量）
			$aryStockRemains_New[$i]["lngproductunitcode"]		= $lngProductUnitCode;								// 製品単位（荷姿単位）
			$aryStockRemains_New[$i]["lngtaxclasscode"]			= $aryStockRemains[$i]["lngtaxclasscode"];			// 消費税区分コード
			$aryStockRemains_New[$i]["lngtaxcode"]				= $aryStockRemains[$i]["lngtaxcode"];				// 消費税コード
			$aryStockRemains_New[$i]["curtaxprice"]				= $aryStockRemains[$i]["curtaxprice"];				// 消費税金額
			$aryStockRemains_New[$i]["cursubtotalprice"]		= $curSubTotalPrice;								// 税抜金額
			$aryStockRemains_New[$i]["strdetailnote"]			= $aryStockRemains[$i]["strdetailnote"];			// 備考
			$aryStockRemains_New[$i]["strserialno"]				= $aryStockRemains[$i]["strserialno"];				// 金型番号
			$aryStockRemains_New[$i]["lngcartonquantity"]		= $lngCartonQuantity;								// カートン入数

		}
	}

// 2004.04.12 suzukaze update start
	// 仕入が発生していない場合
	else
	{
		// 発注残の情報より指定の端数処理を行う
		for ( $i = 0; $i < count($aryOrderDetail); $i++ )
		{
			// 変換された値を設定する
			$aryStockRemains_New[$i]["lngorderdetailno"] 		= $aryOrderDetail[$i]["lngorderdetailno"];			// 明細行番号
			$aryStockRemains_New[$i]["strproductcode"]			= $aryOrderDetail[$i]["strproductcode"];			// 商品コード
			$aryStockRemains_New[$i]["lngstocksubjectcode"]		= $aryOrderDetail[$i]["lngstocksubjectcode"];		// 仕入科目コード
			$aryStockRemains_New[$i]["lngstockitemcode"]		= $aryOrderDetail[$i]["lngstockitemcode"];			// 仕入部品コード
			$aryStockRemains_New[$i]["dtmdeliverydate"] 		
					= str_replace( "-", "/", $aryStockRemains[$i]["dtmdeliverydate"]);								// 納品日（取得文字列置換）
			$aryStockRemains_New[$i]["lngcarriercode"] 			= $aryOrderDetail[$i]["lngcarriercode"];			// 運搬方法コード
			$aryStockRemains_New[$i]["lngconversionclasscode"]	= $aryOrderDetail[$i]["lngconversionclasscode"];	// 換算区分コード
			$aryStockRemains_New[$i]["curproductprice"]			= $aryOrderDetail[$i]["curproductprice"];			// 製品単価（荷姿単価）
			$aryStockRemains_New[$i]["lngproductquantity"]		= $aryOrderDetail[$i]["lngproductquantity"];		// 製品数量（荷姿数量）
			$aryStockRemains_New[$i]["lngproductunitcode"]		= $aryOrderDetail[$i]["lngproductunitcode"];		// 製品単位（荷姿単位）
			$aryStockRemains_New[$i]["lngtaxclasscode"]			= $aryOrderDetail[$i]["lngtaxclasscode"];			// 消費税区分コード
			$aryStockRemains_New[$i]["lngtaxcode"]				= $aryOrderDetail[$i]["lngtaxcode"];				// 消費税コード
			$aryStockRemains_New[$i]["curtaxprice"]				= $aryOrderDetail[$i]["curtaxprice"];				// 消費税金額
			// 税抜金額については端数処理の設定より端数処理を行う
			$curSubTotalPrice = $aryOrderDetail[$i]["lngproductquantity"] * $aryOrderDetail[$i]["curproductprice"];
			$aryStockRemains_New[$i]["cursubtotalprice"]		
					= fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $lngDigitNumber );								// 税抜金額
			$aryStockRemains_New[$i]["cursubtotalprice"]		= $aryOrderDetail[$i]["cursubtotalprice"];			// 税抜金額
			$aryStockRemains_New[$i]["strdetailnote"]			= $aryOrderDetail[$i]["strdetailnote"];				// 備考
			$aryStockRemains_New[$i]["strserialno"]				= $aryOrderDetail[$i]["strserialno"];				// 金型番号
			$aryStockRemains_New[$i]["lngcartonquantity"]		= $aryOrderDetail[$i]["lngcartonquantity"];			// カートン入数
		}
	}
// 2004.04.12 suzukaze update end

	return $aryStockRemains_New;
}






/**
* 指定の発注データに関して、その発注データの仕入状態より残数取得関数
*
*	発注数、発注金額よりその発注Noを指定している仕入すべてから
*	発注残を取得する
*
*	@param  Integer 	$lngOrderNo 				発注番号
*	@param	Array		$aryStockDetail				仕入登録にて設定された明細情報
*	@param	Integer		$lngOrderMonetaryUnitCode	発注時の通貨単位コード
*	@param	Integer		$lngStockMonetaryUnitCode	仕入時の通貨単位コード
*	$param	Integer		$lngStockNo					対象外とする仕入No　（仕入修正時使用）
*	@param	Integer		$lngCalcCode				端数処理コード
*	@param  Object		$objDB						DBオブジェクト
*	@return Boolean 	0							実行成功
*						1							実行失敗 情報取得失敗
*						50							実行成功　明細の内容に発注残を超える情報はない
*						99							発注残以上に指定されている
*	@access public
*
*	更新履歴
*	2004.04.16	fncGetStockRemains 関数の引数変更に伴う修正
*	2004.04.19	aryStockDetail 配列のKey項目名を小文字⇒大文字に修正
*	2004.04.20	発注残を求めた後、発注残の単位計上が荷姿であった場合に比較がおかしくなるバグの修正
*/
function fncGetStatusStockRemains ( $lngOrderNo, $aryStockDetail, $lngOrderMonetaryUnitCode, $lngStockMonetaryUnitCode, 
									$lngStockNo, $lngCalcCode, $objDB )
{
// 2004.04.16 suzukaze update start
	// 発注残を求める関数の呼び出し
	$aryRemainsDetail = fncGetStockRemains ( $lngOrderNo, $lngStockNo, $lngCalcCode, $objDB );
// 2004.04.16 suzukaze update end

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

// 2004.04.16 suzukaze update start
	// 発注時の通貨単位コードより処理対象桁数を設定
	if ( $lngOrderMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$lngDigitNumber = 0;		// 日本円の場合は０桁
	}
	else
	{
		$lngDigitNumber = 2;		// 日本円以外の場合は小数点以下２桁
	}
// 2004.04.16 suzukaze update end

	// 発注残が存在すれば
	// 今回指定された仕入と調査した発注残をチェックし、発注残以上に注文していないかどうかを調査する
	for ( $i = 0; $i < count($aryRemainsDetail); $i++ )
	{
		$lngOrderDetailNo 		= $aryRemainsDetail[$i]["lngorderdetailno"];			// 明細行番号

		$strProductCode 		= $aryRemainsDetail[$i]["strproductcode"];				// 商品コード
		$lngStockSubjectCode 	= $aryRemainsDetail[$i]["lngstocksubjectcode"];			// 仕入科目コード
		$lngStockItemCode 		= $aryRemainsDetail[$i]["lngstockitemcode"];			// 仕入部品コード
		$lngConversionClassCode = $aryRemainsDetail[$i]["lngconversionclasscode"];		// 換算区分コード
		$curProductPrice		= $aryRemainsDetail[$i]["curproductprice"];				// 製品単価（荷姿単価）
		$lngProductQuantity		= $aryRemainsDetail[$i]["lngproductquantity"];			// 製品数量（荷姿数量）
		$lngProductUnitCode		= $aryRemainsDetail[$i]["lngproductunitcode"];			// 製品単位（荷姿単位）
		$curSubTotalPrice		= $aryRemainsDetail[$i]["cursubtotalprice"];			// 税抜金額
		$lngCartonQuantity		= $aryRemainsDetail[$i]["lngcartonquantity"];			// カートン入数

// 2004.04.20 suzukaze update start
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
// 2004.04.20 suzukaze update end

		for ( $j = 0; $j < count($aryStockDetail); $j++ )
		{
// 2004.03.30 suzukaze update start
			// 発注残明細行番号に対して仕入明細行番号が同じ、かつ製品コードが同じ明細が見つかった場合
			if ( $lngOrderDetailNo == $aryStockDetail[$j]["lngOrderDetailNo"] 
				and $strProductCode == $aryStockDetail[$j]["strProductCode"] 
//				and $lngStockSubjectCode == $aryStockDetail[$j]["strStockSubjectCode"] 
//				and $lngStockItemCode == $aryStockDetail[$j]["strStockItemCode"] 
				and $lngOrderMonetaryUnitCode == $lngStockMonetaryUnitCode )
// 2004.03.30 suzukaze update end
			{
				// 換算区分が荷姿計上であった場合は、製品単位計上に変更する
				if ( $aryStockDetail[$j]["lngConversionClassCode"] != "gs" )
				{
					// 今回の仕入情報にはカートン入り数の情報を持っていないので
					// ここでは製品コードが同じということから発注残のカートン入り数を使用する
					// 0 割り対策
					if ( $lngCartonQuantity == 0 or $lngCartonQuantity == "" )
					{
						// カートン入り数が ０ だった場合は その製品に対する納品済みかどうかの判断ができないため 強制的に １ にて換算
						$lngCartonQuantity = 1;
					}

// 2004.04.19 suzukaze update start
					// 製品数量は荷姿数量 * カートン入数
					$aryStockDetail[$j]["lngGoodsQuantity"] 
						= $aryStockDetail[$j]["lngGoodsQuantity"] * $lngCartonQuantity;

					// 製品価格は荷姿単価 / カートン入数
					$aryStockDetail[$j]["curProductPrice"] 
						= $aryStockDetail[$j]["curProductPrice"] / $lngCartonQuantity;

					// 税抜金額は製品数量 * 製品価格
					$aryStockDetail[$j]["curSubTotalPrice"] 
						= $aryStockDetail[$j]["lngGoodsQuantity"] * $aryStockDetail[$j]["curProductPrice"];

// 2004.04.16 suzukaze update start
// 税抜き金額を計算する際に設定された端数処理を行う
					$aryStockDetail[$j]["curSubTotalPrice"] 
						= fncCalcDigit( $aryStockDetail[$j]["curSubTotalPrice"], $lngCalcCode, $lngDigitNumber );
// 2004.04.16 suzukaze update end

					// 単位は製品単位
					$aryStockDetail[$j]["lngProductUnitCode"] = DEF_PRODUCTUNIT_PCS;

					// 換算区分コードは製品単位に修正
					$aryStockDetail[$j]["lngConversionClassCode"] = DEF_CONVERSION_SEIHIN;
				}

				// 数量比較
				if ( $lngProductQuantity < $aryStockDetail[$j]["lngGoodsQuantity"] )
				{
					// 数量が発注残数以上
					return 99;
				}
// 2004.04.19 suzukaze update end

				// 税抜金額比較
				if ( $curSubTotalPrice < $aryStockDetailResult[$j]["curSubTotalPrice"] )
				{
					// 税抜金額が発注残以上
					return 99;
				}

				// 発注残に同じ明細情報が見つかった場合は次の行を処理
				break;
			}
		}
	}

	return 50;	// 実行成功　今回の仕入に発注残を越える情報はない
}






// 2004.03.09 suzukaze update start
/**
* 指定の仕入データの登録に関して、その仕入データを登録することでの状態変更関数
*
*	仕入の状態が「納品済」の場合、発注Noを指定していた場合、分納であった場合など
*	各状態ごとにその仕入に関するデータの状態を変更する
*
*	@param  Integer 	$lngOrderNo 	仕入が参照している発注No
*	@param	Integer		$lngCalcCode	端数処理コード
*	@param  Object		$objDB			DBオブジェクト
*	@return Boolean 	0				実行成功
*						1				実行失敗 情報取得失敗
*	@access public
*
*	更新履歴
*	2004.04.16	端数処理コードの追加
*/
function fncStockSetStatus ( $lngOrderNo, $lngCalcCode, $objDB )
{
	// 発注番号が存在しない場合そのまま終了
	if ( $lngOrderNo == "" or $lngOrderNo == 0 )
	{
		return 1;
	}

	// 最新の発注のデータを取得する
	$strQuery = "SELECT o.lngOrderNo as lngOrderNo, o.strOrderCode as strOrderCode, "
		. "o.lngOrderStatusCode as lngOrderStatusCode, o.lngMonetaryUnitCode as lngMonetaryUnitCode FROM m_Order o "
		. "WHERE o.strOrderCode = ( "
		. "SELECT o1.strOrderCode FROM m_Order o1 WHERE o1.lngOrderNo = " . $lngOrderNo . " ) "
		. "AND o.bytInvalidFlag = FALSE "
		. "AND o.lngRevisionNo >= 0 "
		. "AND o.lngRevisionNo = ( "
		. "SELECT MAX( o2.lngRevisionNo ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode ) "
		. "AND 0 <= ( "
		. "SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode ) ";

	// 検索クエリーの実行
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngNewOrderNo = $objResult->lngorderno;
		$strNewOrderCode = $objResult->strordercode;
		$lngNewOrderStatusCode = $objResult->lngorderstatuscode;
		$OrderlngMonetaryUnitCode = $objResult->lngmonetaryunitcode;
	}
	else
	{
		// 発注Noは指定しているが現在有効な最新発注が存在しない場合はそのまま終了
		return 1;
	}
	$objDB->freeResult( $lngResultID );

// 2004.04.16 suzukaze update start
	// 発注時の通貨単位コードより処理対象桁数を設定
	if ( $OrderlngMonetaryUnitCode == DEF_MONETARY_YEN )
	{
		$lngDigitNumber = 0;		// 日本円の場合は０桁
	}
	else
	{
		$lngDigitNumber = 2;		// 日本円以外の場合は小数点以下２桁
	}
// 2004.04.16 suzukaze update end

	// 最新発注の明細情報を取得する
	$strQuery = "SELECT od.lngOrderDetailNo as lngOrderDetailNo, od.strProductCode as strProductCode, "
		. "od.lngStockSubjectCode as lngStockSubjectCode, od.lngStockItemCode as lngStockItemCode, "
		. "od.lngConversionClassCode as lngConversionClassCode, od.curProductPrice as curProductPrice, "
		. "od.lngProductQuantity as lngProductQuantity, od.lngProductUnitCode as lngProductUnitCode, "
		. "od.curSubTotalPrice as curSubTotalPrice, p.lngCartonQuantity as lngCartonQuantity "
		. "FROM t_OrderDetail od, m_Product p "
		. "WHERE od.lngOrderNo = " . $lngNewOrderNo . " AND od.strProductCode = p.strProductCode "
		. "ORDER BY lngSortKey ASC";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryOrderDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		// 明細行が存在しない場合異常データ
		return 2;
	}
	$objDB->freeResult( $lngResultID );

	// 同じ発注Noを指定している最新仕入を検索
	$strQuery = "SELECT s.lngStockNo as lngStockNo, s.lngStockStatusCode as lngStockStatusCode, "
		. "s.lngMonetaryUnitCode as lngMonetaryUnitCode FROM m_Stock s, m_Order o "
		. "WHERE o.strOrderCode = '" . $strNewOrderCode . "' AND o.lngOrderNo = s.lngOrderNo "
		. "AND s.bytInvalidFlag = FALSE "
		. "AND s.lngRevisionNo >= 0 "
		. "AND s.lngRevisionNo = ( "
		. "SELECT MAX( s2.lngRevisionNo ) FROM m_Stock s2 WHERE s2.strStockCode = s.strStockCode ) "
		. "AND 0 <= ( "
		. "SELECT MIN( s3.lngRevisionNo ) FROM m_Stock s3 WHERE s3.bytInvalidFlag = false AND s3.strStockCode = s.strStockCode ) ";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		// 仕入データが存在する場合
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryStockResult[] = $objDB->fetchArray( $lngResultID, $i );
			// 明細情報を取得する
			$strStockDetailQuery = "SELECT sd.lngStockDetailNo as lngOrderDetailNo, sd.strProductCode as strProductCode, "
				. "sd.lngStockSubjectCode as lngStockSubjectCode, sd.lngStockItemCode as lngStockItemCode, "
				. "sd.lngConversionClassCode as lngConversionClassCode, sd.curProductPrice as curProductPrice, "
				. "sd.lngProductQuantity as lngProductQuantity, sd.lngProductUnitCode as lngProductUnitCode, "
				. "sd.curSubTotalPrice as curSubTotalPrice, p.lngCartonQuantity as lngCartonQuantity "
				. "FROM t_StockDetail sd, m_Product p "
				. "WHERE sd.lngStockNo = " . $aryStockResult[$i]["lngstockno"] . " AND sd.strProductCode = p.strProductCode "
				. "ORDER BY lngSortKey ASC";

			list ( $lngStockDetailResultID, $lngStockDetailResultNum ) = fncQuery( $strStockDetailQuery, $objDB );

			if ( $lngStockDetailResultNum )
			{
				for ( $j = 0; $j < $lngStockDetailResultNum; $j++ )
				{
					$aryStockDetailResult[$i][] = $objDB->fetchArray( $lngStockDetailResultID, $j );
				}
			}
			$objDB->freeResult( $lngStockDetailResultID );
		}

		// 参照元発注の明細毎に取得した仕入にてどのような状態になっているのか調査
		for ( $i = 0; $i < count($aryOrderDetailResult); $i++ )
		{
			// 参照元発注の明細行番号を取得･････明細行番号にひもづいて仕入が消しこみされるため
			$lngOrderDetailNo 		= $aryOrderDetailResult[$i]["lngorderdetailno"];				// 明細行番号

			$strProductCode 		= $aryOrderDetailResult[$i]["strproductcode"];				// 製品コード
			$lngStockSubjectCode 	= $aryOrderDetailResult[$i]["lngstocksubjectcode"];			// 仕入科目コード
			$lngStockItemCode 		= $aryOrderDetailResult[$i]["lngstockitemcode"];			// 仕入部品コード
			$lngConversionClassCode = $aryOrderDetailResult[$i]["lngconversionclasscode"];		// 換算区分コード
			$curProductPrice		= $aryOrderDetailResult[$i]["curproductprice"];				// 製品単価（荷姿単価）
			$lngProductQuantity		= $aryOrderDetailResult[$i]["lngproductquantity"];			// 製品数量（荷姿数量）
			$lngProductUnitCode		= $aryOrderDetailResult[$i]["lngproductunitcode"];			// 製品単位（荷姿単位）
			$curSubTotalPrice		= $aryOrderDetailResult[$i]["cursubtotalprice"];			// 税抜金額
			$lngCartonQuantity		= $aryOrderDetailResult[$i]["lngcartonquantity"];			// カートン入数

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
// 2004.04.09 suzukaze update start
// 税抜き金額を計算する際に設定された端数処理を行う
				// 税抜金額を計算する
				// 税抜金額は数量 * 単価
				$curSubTotalPrice = $lngProductQuantity * $curProductPrice;
				$curSubTotalPrice = fncCalcDigit( $curSubTotalPrice, $lngCalcCode, $lngDigitNumber );
// 2004.04.09 suzukaze update end
			}

			$bytEndFlag = 0;
			$lngStockProductQuantity = 0;
			$curStockSubTotalPrice = 0;
			
			for ( $j = 0; $j < count($aryStockResult); $j++ )
			{
				$StocklngMonetaryUnitCode = $aryStockResult[$j]["lngmonetaryunitcode"];
// 2004.04.16 suzukaze update start
				// 仕入時の通貨単位コードより処理対象桁数を設定
				if ( $StocklngMonetaryUnitCode == DEF_MONETARY_YEN )
				{
					$StocklngDigitNumber = 0;		// 日本円の場合は０桁
				}
				else
				{
					$StocklngDigitNumber = 2;		// 日本円以外の場合は小数点以下２桁
				}
// 2004.04.16 suzukaze update end

				for ( $k = 0; $k < count($aryStockDetailResult[$j]); $k++ )
				{
// 2004.03.30 suzukaze update start
					// 発注明細行番号に対して仕入明細行番号が同じ、かつ製品コードが同じ明細が見つかった場合
					// それに加え　通貨が同じ場合
					if ( $lngOrderDetailNo == $aryStockDetailResult[$j][$k]["lngorderdetailno"] 
						and $strProductCode == $aryStockDetailResult[$j][$k]["strproductcode"] 
//						and $lngStockSubjectCode == $aryStockDetailResult[$j][$k]["lngstocksubjectcode"] 
//						and $lngStockItemCode == $aryStockDetailResult[$j][$k]["lngstockitemcode"] 
						and $OrderlngMonetaryUnitCode == $StocklngMonetaryUnitCode )
// 2004.03.30 suzukaze update end
					{
						// 換算区分が荷姿単位計上の場合、製品単価へ計算
						if ( $aryStockDetailResult[$j][$k]["lngconversionclasscode"] != DEF_CONVERSION_SEIHIN )
						{
							// 0 割り対策
							if ( $aryStockDetailResult[$j][$k]["lngcartonquantity"] == 0 or $aryStockDetailResult[$j][$k]["lngcartonquantity"] == "" )
							{
								// カートン入り数が ０ だった場合は その製品に対する納品済みかどうかの判断ができないため 強制的に １ にて換算
								$aryStockDetailResult[$j][$k]["lngcartonquantity"] = 1;
							}

							// 製品数量は荷姿数量 * カートン入数
							$aryStockDetailResult[$j][$k]["lngproductquantity"] 
								= $aryStockDetailResult[$j][$k]["lngproductquantity"] * $aryStockDetailResult[$j][$k]["lngcartonquantity"];

							// 製品価格は荷姿単価 / カートン入数
							$aryStockDetailResult[$j][$k]["curproductprice"] 
								= $aryStockDetailResult[$j][$k]["curproductprice"] / $aryStockDetailResult[$j][$k]["lngcartonquantity"];

							// 税抜金額は荷姿単価 * 荷姿数量
							$aryStockDetailResult[$j][$k]["cursubtotalprice"] 
								= $aryStockDetailResult[$j][$k]["lngproductquantity"] * $aryStockDetailResult[$j][$k]["curproductprice"];

// 2004.04.16 suzukaze update start
// 税抜き金額を計算する際に設定された端数処理を行う
							// 端数処理を行う
							$aryStockDetailResult[$j][$k]["cursubtotalprice"] 
								= fncCalcDigit( $aryStockDetailResult[$j][$k]["cursubtotalprice"], $lngCalcCode, $StocklngDigitNumber );
// 2004.04.16 suzukaze update end

						}

						// 数量比較
						if ( $lngProductQuantity > $aryStockDetailResult[$j][$k]["lngproductquantity"] )
						{
							$lngStockProductQuantity += $aryStockDetailResult[$j][$k]["lngproductquantity"];
							// 複数仕入からの合算での数量比較
							if ( $lngProductQuantity <= $lngStockProductQuantity )
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
						if ( $curSubTotalPrice > $aryStockDetailResult[$j]["cursubtotalprice"] )
						{
							$curStockSubTotalPrice += $aryStockDetailResult[$j]["cursubtotalprice"];
							// 複数仕入からの合算での税抜金額比較
							if ( $curSubTotalPrice <= $curStockSubTotalPrice )
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

						// 同じ明細行の情報が発注と仕入で見つかった際には「納品中」となるため以下設定
						$bytEndFlag = 1;
					}
				}
				// 仕入明細に発注明細と同内容が見つかった場合は　for 文抜け
				if ( $bytEndFlag == 99 )
				{
					break;
				}
			}
			// 発注明細行毎の仕入明細行が見つかった状態を記憶
			$aryStatus[] = $bytEndFlag;
		}
		
		// 再度チェック　$aryStatus（明細ごとの状態）により発注全体としての状態を判断
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

		// 発注明細に対して一件も仕入が発生していない場合、または完納ではない場合
		// （flagZEROが発注明細数に対してイコールの場合実際は初期状態であるが、仕入にて
		//   発注Noが指定されているのでここでの状態は「納品中」とする）
		if ( $flagALL != count($aryStatus) )
		{
			// 仕入参照発注の状態の状態を「納品中」とする
		
			// 更新対象発注データをロックする
			$strLockQuery = "SELECT lngOrderNo FROM m_Order WHERE lngOrderNo = " . $lngNewOrderNo . " AND bytInvalidFlag = FALSE FOR UPDATE";

			list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
			$objDB->freeResult( $lngLockResultID );

			// 「納品中」状態への更新処理
			$strUpdateQuery = "UPDATE m_Order set lngOrderStatusCode = " . DEF_ORDER_DELIVER . " WHERE lngOrderNo = " . $lngNewOrderNo;

			list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
			$objDB->freeResult( $lngUpdateResultID );

			// 同じ発注NOを指定している仕入の状態に対しても「納品中」とする
			for ( $i = 0; $i < count($aryStockResult); $i++ )
			{
				// 更新対象仕入データをロックする
				$strLockQuery = "SELECT lngStockNo FROM m_Stock " 
					. "WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";

				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				$objDB->freeResult( $lngLockResultID );

				// 「納品中」状態への更新処理
				$strUpdateQuery = "UPDATE m_Stock set lngStockStatusCode = " . DEF_STOCK_DELIVER 
					. " WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"];

				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
			}
			
			return 0;
		}
		else
		// 対象発注は完納状態であったら
		{
			// 仕入参照発注の状態の状態を「納品済」とする
		
			// 更新対象発注データをロックする
			$strLockQuery = "SELECT lngOrderNo FROM m_Order WHERE lngOrderNo = " . $lngNewOrderNo . " AND bytInvalidFlag = FALSE FOR UPDATE";
			list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
			$objDB->freeResult( $lngLockResultID );

			// 「納品済」状態への更新処理
			$strUpdateQuery = "UPDATE m_Order set lngOrderStatusCode = " . DEF_ORDER_END . " WHERE lngOrderNo = " . $lngNewOrderNo;

			list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
			$objDB->freeResult( $lngUpdateResultID );

			// 同じ発注NOを指定している仕入の状態に対しても「納品済」とする
			for ( $i = 0; $i < count($aryStockResult); $i++ )
			{
				// 更新対象仕入データをロックする
				$strLockQuery = "SELECT lngStockNo FROM m_Stock " 
					. "WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
				list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
				$objDB->freeResult( $lngLockResultID );

				// 「納品済」状態への更新処理
				$strUpdateQuery = "UPDATE m_Stock set lngStockStatusCode = " . DEF_STOCK_END 
					. " WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"];

				list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
				$objDB->freeResult( $lngUpdateResultID );
			}
			return 0;
		}
	}
	else
	{
		// 仕入データが存在しない場合
		// 仕入の参照元最新発注の状態を「発注」に戻す
		
		// 更新対象発注データをロックする
		$strLockQuery = "SELECT lngOrderNo FROM m_Order WHERE lngOrderNo = " . $lngNewOrderNo . " AND bytInvalidFlag = FALSE FOR UPDATE";
		list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );
		if ( !$lngLockResultNum )
		{
			fncOutputError ( 9051, DEF_ERROR, "無効化処理エラー", TRUE, "", $objDB );
		}
		$objDB->freeResult( $lngLockResultID );

		// 「発注」状態への更新処理
		$strUpdateQuery = "UPDATE m_Order set lngOrderStatusCode = " . DEF_ORDER_ORDER . " WHERE lngOrderNo = " . $lngNewOrderNo;

		list ( $lngUpdateResultID, $lngUpdateResultNum ) = fncQuery( $strUpdateQuery, $objDB );
		$objDB->freeResult( $lngUpdateResultID );

		return 0;
	}

	$objDB->freeResult( $lngResultID );

	return 0;
}







?>