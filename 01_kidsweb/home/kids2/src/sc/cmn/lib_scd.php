<?
// ----------------------------------------------------------------------------
/**
*       売上管理  納品書検索関連関数群
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
*         ・納品書検索結果関連の関数
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



/**
* 納品書検索の検索項目から一致する最新の売上データを取得するSQL文の作成関数
*
*	納品書検索の検索項目から SQL文を作成する
*
*	@param  Array 	$arySearchColumn 		検索対象カラム名の配列
*	@param  Array 	$arySearchDataColumn 	検索内容の配列
*	@param  Object	$objDB       			DBオブジェクト
*	@param	String	$strSlipCode			納品伝票コード	空白指定時:検索結果出力	納品伝票コード指定時:管理用、同じ納品書ＮＯの一覧取得
*	@param	Integer	$lngSlipNo				納品伝票番号	0:検索結果出力	納品伝票番号指定時:管理用、同じ納品伝票コードとする時の対象外納品伝票番号
*	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
*	@access public
*/
function fncGetSearchSlipSQL ( $arySearchColumn, $arySearchDataColumn, $objDB, $strSlipCode, $lngSlipNo, $strSessionID)
{
	// -----------------------------
	//  検索条件の動的設定
	// -----------------------------
	// 明細条件追加済みフラグ
	$detailFlag = FALSE;

	// 同じ納品伝票コードのデータを取得する場合
	if ( $strSlipCode )
	{
		// 同じ納品伝票コードに対して指定の納品伝票番号のデータは除外する
		if ( $lngSlipNo )
		{
			$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.strSlipCode = '" . $strSlipCode . "'";
		}
		else
		{
			fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../sc/search2/index.php?strSessionID=".$strSessionID, $objDB );
		}
	}
	// 管理モードでの同じ納品伝票コードに対する検索モード以外の場合は検索条件を追加する
	else
	{
		// 絶対条件 無効フラグが設定されておらず、最新売上のみ
		$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.lngRevisionNo >= 0";

		// 検索チェックボックスがONの項目のみ検索条件に追加
		for ( $i = 0; $i < count($arySearchColumn); $i++ )
		{
			$strSearchColumnName = $arySearchColumn[$i];
			
			// ----------------------------------------------
			//   納品書マスタ（ヘッダ部）の検索条件
			// ----------------------------------------------
			// 顧客（売上先）
			if ( $strSearchColumnName == "lngCustomerCompanyCode" )
			{
				if ( $arySearchDataColumn["lngCustomerCompanyCode"] )
				{
					$aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngCustomerCompanyCode"] . "'";
				}
//				if ( $arySearchDataColumn["strCustomerName"] )
//				{
//					$aryQuery[] = " AND UPPER(cust_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strCustomerName"] . "%')";
//				}
			}

			// 課税区分（消費税区分）
			if ( $strSearchColumnName == "lngTaxClassCode" )
			{
				if ( $arySearchDataColumn["lngTaxClassCode"] )
				{
					$aryQuery[] = " AND s.lngTaxClassCode = '" . $arySearchDataColumn["lngTaxClassCode"] . "'";
				}
			}

			// 納品伝票コード（納品書NO）
			if ( $strSearchColumnName == "strSlipCode" )
			{
				if ( $arySearchDataColumn["strSlipCode"] )
				{
					// カンマ区切りの入力値をOR条件に展開
					$arySCValue = explode(",",$arySearchDataColumn["strSlipCode"]);
					foreach($arySCValue as $strSCValue){
						$arySCOr[] = "UPPER(s.strSlipCode) LIKE UPPER('%" . $strSCValue . "%')";
					}
					$aryQuery[] = " AND (";
					$aryQuery[] = implode(" OR ", $arySCOr);
					$aryQuery[] = ") ";
				}
			}

			// 納品日
			if ( $strSearchColumnName == "dtmDeliveryDate" )
			{
				if ( $arySearchDataColumn["dtmDeliveryDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmDeliveryDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND s.dtmDeliveryDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmDeliveryDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmDeliveryDateTo"] . " 23:59:59";
					$aryQuery[] = " AND s.dtmDeliveryDate <= '" . $dtmSearchDate . "'";
				}
			}

			// 納品先
			if ( $strSearchColumnName == "lngDeliveryPlaceCode" )
			{
				if ( $arySearchDataColumn["lngDeliveryPlaceCode"] )
				{
					//会社マスタと紐づけた値と比較
					$aryQuery[] = " AND delv_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
				}
//				if ( $arySearchDataColumn["strDeliveryPlaceName"] )
//				{
//					$aryQuery[] = " AND UPPER(s.strDeliveryPlaceName) LIKE UPPER('%" . $arySearchDataColumn["strDeliveryPlaceName"] . "%')";
//				}
			}

			// 起票者
			if ( $strSearchColumnName == "lngInsertUserCode" )
			{
				if ( $arySearchDataColumn["lngInsertUserCode"] )
				{
					$aryQuery[] = " AND s.strInsertUserCode ~* '" . $arySearchDataColumn["lngInsertUserCode"] . "'";
				}
//				if ( $arySearchDataColumn["strInsertUserName"] )
//				{
//					$aryQuery[] = " AND UPPER(s.strInsertUserName) LIKE UPPER('%" . $arySearchDataColumn["strInsertUserName"] . "%')";
//				}
			}

			// ----------------------------------------------
			//   納品伝票明細テーブル（明細部）の検索条件
			// ----------------------------------------------
			// 注文書NO.
			if ( $strSearchColumnName == "strCustomerSalesCode" )
			{
				if ( $arySearchDataColumn["strCustomerSalesCode"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						unset( $aryDetailTargetQuery );
						$aryDetailTargetQuery[] = " where";
						
						$aryDetailWhereQuery[] = "AND ";
					}

					// カンマ区切りの入力値をOR条件に展開
					$aryCSCValue = explode(",",$arySearchDataColumn["strCustomerSalesCode"]);
					foreach($aryCSCValue as $strCSCValue){
						$aryCSCOr[] = "UPPER(sd1.strCustomerSalesCode) LIKE UPPER('%" . $strCSCValue . "%')";
					}
					$aryDetailWhereQuery[] = " (";
					$aryDetailWhereQuery[] = implode(" OR ", $aryCSCOr);
					$aryDetailWhereQuery[] = ") ";

					$detailFlag = TRUE;
				}
			}
		
			// 顧客品番
			if ( $strSearchColumnName == "strGoodsCode" )
			{
				if ( $arySearchDataColumn["strGoodsCode"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						unset( $aryDetailTargetQuery );
						$aryDetailTargetQuery[] = " where";
						
						$aryDetailWhereQuery[] = "AND ";
					}

					// カンマ区切りの入力値をOR条件に展開
					$aryGCValue = explode(",",$arySearchDataColumn["strGoodsCode"]);
					foreach($aryGCValue as $strGCValue){
						$aryGCOr[] = "UPPER(sd1.strGoodsCode) LIKE UPPER('%" . $strGCValue . "%')";
					}
					$aryDetailWhereQuery[] = " (";
					$aryDetailWhereQuery[] = implode(" OR ", $aryGCOr);
					$aryDetailWhereQuery[] = ") ";

					$detailFlag = TRUE;
				}
			}

			// 売上区分
			if ( $strSearchColumnName == "lngSalesClassCode" )
			{
				if ( $arySearchDataColumn["lngSalesClassCode"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = "sd1.lngSalesClassCode = " . $arySearchDataColumn["lngSalesClassCode"] . " ";
					$detailFlag = TRUE;
				}
			}
		}
	}



	// ---------------------------------
	//   SQL文の作成
	// ---------------------------------
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT distinct s.lngSlipNo as lngSlipNo";	//納品伝票番号
	$aryOutQuery[] = "	,s.lngSalesNo as lngSalesNo";			    //売上番号
	$aryOutQuery[] = "	,s.lngRevisionNo as lngRevisionNo";			//リビジョン番号
	$aryOutQuery[] = "	,s.dtmInsertDate as dtmInsertDate";			//作成日

	// 明細行の 'order by' 用に追加
	$aryOutQuery[] = "	,sd.lngSlipDetailNo";		      // 納品伝票明細番号
	$aryOutQuery[] = "	,sd.lngRecordNo";                 // 明細行NO
	$aryOutQuery[] = "	,sd.strCustomerSalesCode";	      // 注文書NO
	$aryOutQuery[] = "	,sd.strGoodsCode";                // 顧客品番
	$aryOutQuery[] = "	,sd.strProductName";			  // 品名
	$aryOutQuery[] = "	,sd.strSalesClassName";	          // 売上区分
	$aryOutQuery[] = "	,sd.curProductPrice";		      // 単価
	$aryOutQuery[] = "	,sd.lngQuantity";	              // 入数
	$aryOutQuery[] = "	,sd.lngProductQuantity";	      // 数量
	$aryOutQuery[] = "	,sd.strProductUnitName";	      // 単位
	$aryOutQuery[] = "	,sd.curSubTotalPrice";		      // 税抜金額
	$aryOutQuery[] = "	,sd.strNote";				      // 明細備考

	// 顧客
	$arySelectQuery[] = ", s.strCustomerCode as strCustomerDisplayCode";
	$arySelectQuery[] = ", s.strCustomerName as strCustomerDisplayName";
	// 顧客の国
	$arySelectQuery[] = ", cust_c.lngCountryCode as lngcountrycode";
	// 請求書番号
	$arySelectQuery[] = ", sa.lngInvoiceNo as lnginvoiceno";
	// 課税区分
	$arySelectQuery[] = ", s.strTaxClassName as strTaxClassName";
	// 納品伝票コード（納品書NO）
	$arySelectQuery[] = ", s.strSlipCode as strSlipCode";
	// 納品日
	$arySelectQuery[] = ", to_char( s.dtmDeliveryDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmDeliveryDate";
	// 納品先
	$arySelectQuery[] = " , s.strDeliveryPlaceName as strDeliveryPlaceName";
	// 起票者
	$arySelectQuery[] = ", s.strInsertUserCode as strInsertUserCode";
	$arySelectQuery[] = ", s.strInsertUserName as strInsertUserName";
	// 備考
	$arySelectQuery[] = ", s.strNote as strNote";
	// 合計金額
	$arySelectQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
	//// 売上Ｎｏ
	$arySelectQuery[] = ", sa.strSalesCode as strSalesCode";
	// 売上状態コード
	$arySelectQuery[] = ", sa.lngSalesStatusCode as lngSalesStatusCode";
	$arySelectQuery[] = ", ss.strSalesStatusName as strSalesStatusName";
	// 通貨単位
	$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";

	// select句 クエリー連結
	$aryOutQuery[] = implode("\n", $arySelectQuery);

	// From句 の生成
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Slip s";
//	if ( !$strSlipCode )
//	{
		 $aryFromQuery[] = "INNER JOIN (SELECT lngSlipNo, MAX(lngRevisionNo) AS lngRevisionNo from m_slip group by lngSlipNo) max_rev "
		 . "on max_rev.lngSlipNo = s.lngslipno and max_rev.lngRevisionNo = s.lngrevisionno";

//    }
	$aryFromQuery[] = " INNER JOIN m_Sales sa ON s.lngSalesNo = sa.lngSalesNo AND s.lngRevisionNo = sa.lngRevisionNo";
	$aryFromQuery[] = " LEFT JOIN m_SalesStatus ss ON sa.lngSalesStatusCode = ss.lngSalesStatusCode";
	$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON CAST(s.strCustomerCode AS INTEGER) = cust_c.lngCompanyCode";
	$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	$aryFromQuery[] = " LEFT JOIN m_User insert_u ON s.strInsertUserCode = insert_u.strUserDisplayCode";
	$aryFromQuery[] = " LEFT JOIN m_Company delv_c ON s.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
	// From句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryFromQuery);

	// 明細検索用テーブル結合条件
	$aryDetailFrom = array();
	$aryDetailFrom[] = ", (SELECT distinct on ( sd1.lngSlipNo ) sd1.lngSlipNo ";
	$aryDetailFrom[] = "	,sd1.lngSlipDetailNo";				// 納品伝票明細番号
	$aryDetailFrom[] = "	,sd1.lngSortKey as lngRecordNo";	// 明細行NO
	$aryDetailFrom[] = "	,sd1.strCustomerSalesCode";			// 注文書NO
	$aryDetailFrom[] = "	,sd1.strGoodsCode";					// 顧客品番
	$aryDetailFrom[] = "	,sd1.strProductName";				// 品名
	$aryDetailFrom[] = "	,sd1.strSalesClassName";	// 売上区分
	$aryDetailFrom[] = "	,sd1.curProductPrice";		// 単価
	$aryDetailFrom[] = "	,sd1.lngQuantity";	        // 入数
	$aryDetailFrom[] = "	,sd1.lngProductQuantity";	// 数量
	$aryDetailFrom[] = "	,sd1.strProductUnitName";	// 単位
	$aryDetailFrom[] = "	,sd1.curSubTotalPrice";		// 税抜金額
	$aryDetailFrom[] = "	,sd1.strNote";				// 明細備考
	$aryDetailFrom[] = "	FROM t_SlipDetail sd1 ";
	// where句（明細行） クエリー連結
	$strDetailQuery = implode("\n", $aryDetailFrom) . "\n";
	// 明細行の条件が存在する場合
	if ( $detailFlag )
	{
		$strDetailQuery .= implode("\n", $aryDetailTargetQuery) . "\n";
	}
	$aryDetailWhereQuery[] = ") as sd";
	$strDetailQuery .= implode("\n", $aryDetailWhereQuery) . "\n";
	
	// Where句 クエリー連結
	$aryOutQuery[] = $strDetailQuery;
	$aryOutQuery[] = implode("\n", $aryQuery);

	// 明細行用の条件連結
	$aryOutQuery[] = " AND sd.lngSlipNo = s.lngSlipNo";


	/////////////////////////////////////////////////////////////
	//// 最新売上（リビジョン番号が最大、リバイズ番号が最大、     ////
	//// かつリビジョン番号負の値で無効フラグがFALSEの           ////
	//// 同じ納品伝票コードを持つデータが無い売上データ          ////
	/////////////////////////////////////////////////////////////
	// 納品伝票コードが指定されていない場合は検索条件を設定する
	if ( !$strSlipCode )
	{
//		$aryOutQuery[] = " AND s.lngRevisionNo = ( "
//			. "SELECT MAX( s1.lngRevisionNo ) FROM m_Slip s1 WHERE s1.strSlipCode = s.strSlipCode AND s1.bytInvalidFlag = false )";

		// 管理モードの場合は削除データも検索対象とするため以下の条件は対象外
		if ( !$arySearchDataColumn["Admin"] )
		{
//			$aryOutQuery[] = " AND 0 <= ( "
//				. "SELECT MIN( s2.lngRevisionNo ) FROM m_Slip s2 WHERE s2.bytInvalidFlag = false AND s2.strSlipCode = s.strSlipCode )";
			$aryOutQuery[] = " AND s.lngslipno not in (SELECT lngslipno from m_slip where lngRevisionNo < 0 and bytInvalidFlag = false)";
		}
	}

	// 同じ納品伝票コードのデータを取得する場合
	if ($strSlipCode)
	{
		$aryOutQuery[] = " ORDER BY dtmInsertDate DESC";
	}
	else
	{
		// ソート条件設定
		$aryOutQuery[] = " ORDER BY lngSlipNo DESC";

		// 特に定められたソート仕様は無いためコメントアウト
		/*
		if ( $arySearchDataColumn["strSortOrder"] == "ASC" )
		{
			$strAsDs = " ASC";	//昇降
		}
		else
		{
			$strAsDs = " DESC";	//降順
		}

		switch($arySearchDataColumn["strSort"])
		{
			case "dtmInsertDate":
			case "strSalesCode":
			case "strSlipCode":
			case "lngSalesStatusCode":
			case "lngWorkFlowStatusCode":
			case "strNote":
			case "curTotalPrice":
			case "strCustomerReceiveCode":
				$aryOutQuery[] = " ORDER BY " . $arySearchDataColumn["strSort"] . " " . $strAsDs . ", lngSalesNo DESC";
				break;
			case "dtmAppropriationDate":
				$aryOutQuery[] = " ORDER BY dtmSalesAppDate" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "dtmSalesAppDate":
				$aryOutQuery[] = " ORDER BY dtmAppropriationDate" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngInputUserCode":
				$aryOutQuery[] = " ORDER BY strInputUserDisplayCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngCustomerCompanyCode":
				$aryOutQuery[] = " ORDER BY strCustomerDisplayCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngSalesDetailNo":	// 明細行番号
				$aryOutQuery[] = " ORDER BY sd.lngSalesDetailNo" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "strProductCode":		// 製品コード
				$aryOutQuery[] = " ORDER BY sd.strProductCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngGroupCode":		// 部門
				$aryOutQuery[] = " ORDER BY sd.strGroupDisplayCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngUserCode":			// 担当者
				$aryOutQuery[] = " ORDER BY sd.strUserDisplayCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "strProductName":		// 製品名称
				$aryOutQuery[] = " ORDER BY sd.strProductName" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "strProductEnglishName":	// 製品英語名称
				$aryOutQuery[] = " ORDER BY sd.strProductEnglishName" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngSalesClassCode":	// 売上区分
				$aryOutQuery[] = " ORDER BY sd.lngSalesClassCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "strGoodsCode":		// 顧客品番
				$aryOutQuery[] = " ORDER BY sd.strGoodsCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "dtmDeliveryDate":		// 納期
				$aryOutQuery[] = " ORDER BY sd.dtmDeliveryDate" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "curProductPrice":		// 単価
				$aryOutQuery[] = " ORDER BY sd.curProductPrice" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngProductUnitCode":	// 単位
				$aryOutQuery[] = " ORDER BY sd.lngProductUnitCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngProductQuantity":	// 数量
				$aryOutQuery[] = " ORDER BY sd.lngProductQuantity" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "curSubTotalPrice":	// 税抜金額
				$aryOutQuery[] = " ORDER BY sd.curSubTotalPrice" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "lngTaxClassCode":		// 税区分
				$aryOutQuery[] = " ORDER BY sd.lngTaxClassCode" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "curTax":				// 税率
				$aryOutQuery[] = " ORDER BY sd.curTax" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "curTaxPrice":			// 税額
				$aryOutQuery[] = " ORDER BY sd.curTaxPrice" . $strAsDs . ", lngSalesNo DESC";
				break;
			case "strDetailNote":		// 明細備考
				$aryOutQuery[] = " ORDER BY sd.strNote" . $strAsDs . ", lngSalesNo DESC";
				break;
				
			default:
				$aryOutQuery[] = " ORDER BY lngSalesNo DESC";
		}
		*/
		
		
	}
	return implode("\n", $aryOutQuery);
}



/**
* 指定した納品伝票番号のデータに対応する「明細行」を取得するSQL文の作成関数
*
*	納品伝票番号から明細を取得するSQL文を作成する
*
*	@param  String 	$lngSlipNo 			    対象納品伝票番号
*	@param  Array 	$aryData 				POSTデータの配列
*	@param  Object	$objDB       			DBオブジェクト
*	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
*	@access public
*/
function fncGetSlipToProductSQL ( $lngSlipNo, $lngRevisionNo, $aryData, $objDB )
{
	// ----------------------
	//   SQL文の作成
	// ----------------------
	$aryOutQuery = array();
	//明細行NO
	$aryOutQuery[] = "SELECT sd.lngSortKey as lngRecordNo";
	//納品伝票番号
	$aryOutQuery[] = "	,sd.lngSlipNo as lngSlipNo";
	//リビジョン番号	
	$aryOutQuery[] = "	,sd.lngRevisionNo as lngRevisionNo";
	// 注文書NO.
	$aryOutQuery[] = ", sd.strCustomerSalesCode as strCustomerSalesCode";
	// 顧客品番
	$aryOutQuery[] = ", sd.strGoodsCode as strGoodsCode";
	// 品名
	$aryOutQuery[] = ", sd.strProductName as strProductName";
	// 売上区分
	$aryOutQuery[] = ", sd.lngSalesClassCode as lngSalesClassCode";
	$aryOutQuery[] = ", sd.strSalesClassName as strSalesClassName";
	// 単価
	$aryOutQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
	// 入数
	$aryOutQuery[] = ", To_char( sd.lngQuantity, '9,999,999,990' )  as lngQuantity";
	// 数量
	$aryOutQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
	// 単位
	$aryOutQuery[] = ", sd.strProductUnitName as strProductUnitName";
	// 税抜金額
	$aryOutQuery[] = ", To_char( sd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
	// 明細備考
	$aryOutQuery[] = ", sd.strNote as strDetailNote";
	// 受注ステータスコード
	$aryOutQuery[] = ", re.lngReceiveStatusCode as lngReceiveStatusCode";

	// From句
	$aryOutQuery[] = " FROM t_SlipDetail sd";
	$aryOutQuery[] = "    LEFT JOIN m_Receive re ON sd.lngReceiveNo = re.lngReceiveNo";

	// Where句
	$aryOutQuery[] = " WHERE sd.lngSlipNo = " . $lngSlipNo . " AND sd.lngRevisionNo = " . $lngRevisionNo . "";	// 対象納品伝票番号の指定

	// OrderBy句
	$aryOutQuery[] = " ORDER BY sd.lngSortKey ASC";

	// 特に定められたソート仕様は無いためコメントアウト
	/*
	if ( $aryData["strSortOrder"] == "ASC" )
	{
		$strAsDs = "DESC";	// ヘッダ項目とは逆順にする
	}
	else
	{
		$strAsDs = "ASC";	//降順
	}

	switch($aryData["strSort"])
	{
		case "strDetailNote":
			$aryOutQuery[] = " ORDER BY sd.strNote " . $strAsDs . ", sd.lngSortKey ASC";
			break;
		case "lngSalesDetailNo":
			$aryOutQuery[] = " ORDER BY sd.lngSortKey " . $strAsDs;
			break;
		case "strProductName":
		case "strProductEnglishName":
		case "strGoodsCode":
			$aryOutQuery[] = " ORDER BY " . $aryData["strSort"] . " " . $strAsDs . ", sd.lngSortKey ASC";
			break;
		case "lngUserCode":
			$aryOutQuery[] = " ORDER BY mu.struserdisplaycode " . $strAsDs . ", sd.lngSortKey ASC";
			break;
		case "lngGroupCode":
			$aryOutQuery[] = " ORDER BY mg.strgroupdisplaycode " . $strAsDs . ", sd.lngSortKey ASC";
			break;
		default:
			$aryOutQuery[] = " ORDER BY sd.lngSortKey ASC";
	}
	*/

	return implode("\n", $aryOutQuery);
}


/**
* 納品書検索結果表示関数
*
*	納品書検索結果からテーブル構成で結果を出力する関数
*	1レコード分のHTMLを取得
*
*	@param  Integer $lngColumnCount 		行数
*	@param  Array 	$aryHeadResult 			ヘッダ行の検索結果が格納された配列
*	@param  Array 	$aryDetailResult 		明細行の検索結果が格納された配列
*	@param  Array 	$aryHeadViewColumn 		ヘッダ表示対象カラム名の配列
*	@param  Array 	$aryData 				ＰＯＳＴデータ群
*	@param	Array	$aryUserAuthority		ユーザーの操作に対する権限が入った配列
*	@access public
*/
function fncSetSlipTableRow ( $lngColumnCount, $aryHeadResult, $aryDetailResult, $aryHeadViewColumn, $aryData, $aryUserAuthority, $lngReviseTotalCount, $lngReviseCount, $bytDeleteFlag )
{
	// 顧客の国が日本で、かつ納品書ヘッダに紐づく請求書明細が存在する
	$japaneseInvoiceExists = ($aryHeadResult["lngcountrycode"] == 81) && ($aryHeadResult["lnginvoiceno"] != null);

	for ( $i = 0; $i < count($aryDetailResult); $i++ )
	{
		// 納品伝票明細に紐づく受注ステータスが「締済み」である
		$receiveStatusIsClosed = $aryDetailResult[$i]["lngreceivestatuscode"] == DEF_RECEIVE_CLOSED;

		$aryHtml[] =  "<tr>";
		$aryHtml[] =  "\t<td>" . ($lngColumnCount + $i) . "</td>";
		
		// 表示対象カラムの配列より結果の出力
		for ( $j = 0; $j < count($aryHeadViewColumn); $j++ )
		{
			$strColumnName = $aryHeadViewColumn[$j];
			$TdData = "";

			// 表示対象がボタンの場合
			if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" or $strColumnName == "btnInvalid" )
			{
				// ボタン種により変更

				// 詳細ボタン
				if ( $strColumnName == "btnDetail" and $aryUserAuthority["Detail"] )
				{
					if ( $aryHeadResult["lngrevisionno"] >= 0 )
					{						
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngslipno=\"" . $aryDetailResult[$i]["lngslipno"] . "\" class=\"detail button\"></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td></td>\n";
					}
				}

				// 修正ボタン
				if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
				{
					// 納品書データの状態により分岐 
					// 最新納品書が削除データの場合も選択不可
					if ( $japaneseInvoiceExists
					    or $receiveStatusIsClosed
						or $aryHeadResult["lngrevisionno"] < 0 
						or $bytDeleteFlag )
					{
						$aryHtml[] = "\t<td></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\">"
									."<img src=\"/mold/img/renew_off_bt.gif\" "
									."lngslipno=\"" . $aryHeadResult["lngslipno"] . "\" "
									."lngrevisionno=\"" . $aryHeadResult["lngrevisionno"] . "\" "
									."strslipcode=\"" . $aryHeadResult["strslipcode"] . "\" "
									."lngsalesno=\"" . $aryHeadResult["lngsalesno"] . "\" "
									."strsalescode=\"" . $aryHeadResult["strsalescode"] . "\" "
									."strcustomercode=\"" . $aryHeadResult["strcustomerdisplaycode"] . "\" "
									."class=\"renew button\"></td>\n";
					}
				}

				// 削除ボタン
				if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
				{
					// 管理モードで無い場合もしくはリバイズが存在しない場合
					if ( !$aryData["Admin"] or $lngReviseTotalCount == 1 )
					{
						// 納品書データの状態により分岐 
						// 最新納品書が削除データの場合も選択不可
						if ( $japaneseInvoiceExists
						    or $receiveStatusIsClosed
					        or $bytDeleteFlag )
						{
							$aryHtml[] = "\t<td></td>\n";
						}
						else
						{
							$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngslipno=\"" . $aryDetailResult[$i]["lngslipno"] . "\" class=\"delete button\"></td>\n";
						}
					}
					// 管理モードで複数リバイズが存在する場合
					else
					{
						// 最新受注の場合
						if ( $lngReviseCount == 0 )
						{
							// 納品書データの状態により分岐 
							// 最新納品書が削除データの場合も選択不可
							if ( $japaneseInvoiceExists
								or $receiveStatusIsClosed
								or $bytDeleteFlag )
							{
								$aryHtml[] = "\t<td></td>\n";
							}
							else
							{
								$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngslipno=\"" . $aryDetailResult[$i]["lngslipno"] . "\" class=\"detail button\"></td>\n";
								
							}
						}
					}
				}
			}
			// 表示対象がボタン以外の場合
			else if ($strColumnName != "") {
				$TdData = "\t<td>";
				$TdDataUse = true;
				$strText = "";

				// 顧客
				if ( $strColumnName == "lngCustomerCode" )
				{
					if ( $aryHeadResult["strcustomerdisplaycode"] )
					{
						$strText .= "[" . $aryHeadResult["strcustomerdisplaycode"] ."]";
					}
					else
					{
						$strText .= "     ";
					}
					$strText .= " " . $aryHeadResult["strcustomerdisplayname"];
					$TdData .= $strText;
				}
				// 課税区分
				else if ($strColumnName == "lngTaxClassCode"){
					$TdData  .= $aryHeadResult["strtaxclassname"];
				}
				// 納品日
				else if ( $strColumnName == "dtmDeliveryDate" )
				{
					$TdData .= str_replace( "-", "/", substr( $aryHeadResult["dtmdeliverydate"], 0, 19 ) );
				}
				// 納品先
				else if ( $strColumnName == "lngDeliveryPlaceCode" )
				{
					$TdData  .= $aryHeadResult["strdeliveryplacename"];
				}
				// 納品伝票コード（納品書NO）
				else if ( $strColumnName == "strSlipCode" )
				{
					$TdData .= $aryHeadResult["strslipcode"];
					// 管理モードの場合　リビジョン番号を表示する
					if ( $aryData["Admin"] )
					{
						$TdData .= "</td>\n\t<td>" . $aryHeadResult["lngrevisionno"];
					}
				}
				// 起票者
				else if ( $strColumnName == "lngInsertUserCode" )
				{
					if ( $aryHeadResult["strinsertusercode"] )
					{
						$strText .= "[" . $aryHeadResult["strinsertusercode"] ."]";
					}
					else
					{
						$strText .= "     ";
					}
					$strText .= " " . $aryHeadResult["strinsertusername"];
					$TdData .= $strText;
				}
				// 合計金額
				else if ( $strColumnName == "curTotalPrice" )
				{
					$strText .= $aryHeadResult["strmonetaryunitsign"] . " ";
					if ( !$aryHeadResult["curtotalprice"] )
					{
						$strText .= "0.00";
					}
					else
					{
						$strText .= $aryHeadResult["curtotalprice"];
					}
					$TdData .= $strText;
				}
				else
				{
					//（カラム名を小文字変換）
					$strLowColumnName = strtolower($strColumnName);

					// 備考
					if ( $strLowColumnName == "strnote" )
					{
						$strText .= nl2br($aryHeadResult[$strLowColumnName]);
					}
					// 詳細項目
					else if ( array_key_exists( $strLowColumnName , $aryDetailResult[$i] ) )
					{
						$strText .= $aryDetailResult[$i][$strLowColumnName];
					}
					// その他の項目
					else
					{
						$strText .= $aryHeadResult[$strLowColumnName];
					}
					$TdData .= $strText;
				}
				$TdData .= "</td>\n";
				if ($TdDataUse) {
					$aryHtml[] = $TdData;
				}
			}
		}
		$aryHtml[] = "</tr>";
	}
	return $aryHtml;
}


/**
* 納品書検索結果表示関数
*
*	納品書検索結果からテーブル構成で結果を出力する関数
*
*	@param  Array 	$aryResult 			検索結果が格納された配列
*	@param  Array 	$aryViewColumn 		表示対象カラム名の配列
*	@param  Array 	$aryData 			ＰＯＳＴデータ群
*	@param	Array	$aryUserAuthority	ユーザーの操作に対する権限が入った配列
*	@param	Array	$aryTytle			項目名が格納された配列（呼び出し元で日本語用、英語用の切り替え）
*	@param  Object	$objDB       		DBオブジェクト
*	@param  Object	$objCache       	キャッシュオブジェクト
*	@access public
*/
function fncSetSlipTableBody ( $aryResult, $arySearchColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache)
{
	// 詳細ボタンの表示制御
	if ( $aryUserAuthority["Detail"] )
	{
		$aryHeadViewColumn[] = "btnDetail";
	}

	// 修正ボタンの表示制御
	if ( $aryUserAuthority["Fix"] )
	{
		$aryHeadViewColumn[] = "btnFix";
	}

	// ヘッダ部
	$aryHeadViewColumn[] = "lngCustomerCode";		//顧客
	$aryHeadViewColumn[] = "lngTaxClassCode";		//課税区分
	$aryHeadViewColumn[] = "strSlipCode";			//納品書NO
	$aryHeadViewColumn[] = "dtmDeliveryDate";		//納品日
	$aryHeadViewColumn[] = "lngDeliveryPlaceCode";	//納品先
	$aryHeadViewColumn[] = "lngInsertUserCode";		//起票者
	$aryHeadViewColumn[] = "strNote";				//備考
	$aryHeadViewColumn[] = "curTotalPrice";			//合計金額
	
	// 明細部
	$aryHeadViewColumn[] = "lngRecordNo";			//明細行NO
	$aryHeadViewColumn[] = "strCustomerSalesCode";	//注文書NO
	$aryHeadViewColumn[] = "strGoodsCode";			//顧客品番
	$aryHeadViewColumn[] = "strProductName";		//品名
	$aryHeadViewColumn[] = "strSalesClassName";		//売上区分
	$aryHeadViewColumn[] = "curProductPrice";		//単価
	$aryHeadViewColumn[] = "lngQuantity";			//入数
	$aryHeadViewColumn[] = "lngProductQuantity";	//数量
	$aryHeadViewColumn[] = "strProductUnitName";	//単位
	$aryHeadViewColumn[] = "curSubTotalPrice";		//税抜金額
	$aryHeadViewColumn[] = "strDetailNote";			//明細備考
	
	// 削除ボタン（権限による表示/非表示切り替え）
	if ( $aryUserAuthority["Delete"] )
	{
		$aryHeadViewColumn[] = "btnDelete";
	}

	// 仕様に無いため非表示にする（2019/8/22 T.Miyata）
	/*
	// 無効ボタン（権限による表示/非表示切り替え）
	if ( $aryUserAuthority["Invalid"] )
	{
		$aryHeadViewColumn[] = "btnInvalid";
	}
	*/

	// テーブルの形成
	$lngResultCount = count($aryResult);
	$lngColumnCount = 1;
	
	// 項目名列（先頭行）の生成 start=========================================
	$aryHtml[] = "<thead>";
	$aryHtml[] = "<tr>";
	$aryHtml[] = "\t<th class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/copy_off_bt.gif\" class=\"copy button\"></th>";

	// 表示対象カラムの配列より項目設定
	for ( $j = 0; $j < count($aryHeadViewColumn); $j++ )
	{
		$Addth = "\t<th>";
		
		$strColumnName = $aryHeadViewColumn[$j];
		$Addth .= $aryTytle[$strColumnName];
		
		$Addth .= "</th>";

		$aryHtml[] = $Addth;
	}
	$aryHtml[] = "</tr>";
	$aryHtml[] = "</thead>";
	// 項目名列（先頭行）の生成 end=========================================

	$aryHtml[] = "<tbody>";

	for ( $i = 0; $i < $lngResultCount; $i++ )
	{
		// 同じ納品伝票コードの一覧を取得し表示する
//		$strSlipCodeBase = $aryResult[$i]["strslipcode"];
//		$strSameSlipCodeQuery = fncGetSearchSlipSQL( $arySearchColumn, $aryData, $objDB, $strSlipCodeBase, $aryResult[$i]["lngslipno"], $aryData["strSessionID"]);
//		fncDebug("kids2.log", $strSameSlipCodeQuery, __FILE__, __LINE__, "a+");

		// 値をとる =====================================
//		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameSlipCodeQuery, $objDB );

		// 配列のクリア
//		unset( $arySameSlipCodeResult );

//		if ( $lngResultNum )
//		{
//			for ( $j = 0; $j < $lngResultNum; $j++ )
//			{
//				$arySameSlipCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
//			}
//			$lngSameSlipCount = $lngResultNum;
//		}
//		$objDB->freeResult( $lngResultID );

		// 同じ納品伝票コードでの過去リバイズデータが存在すれば
//		if ( $lngResultNum )
//		{
//			for ( $j = 0; $j < $lngSameSlipCount; $j++ )
//			{
//				// 検索結果部分の設定
//				reset( $arySameSlipCodeResult[$j] );

				// 明細選択クエリー実行
				$strDetailQuery = fncGetSlipToProductSQL ( $aryResult[$i]["lngslipno"], $aryResult[$i]["lngrevisionno"], $aryData, $objDB );
//				fncDebug("kids2.log", $strDetailQuery, __FILE__, __LINE__, "a+");
				if ( !$lngDetailResultID = $objDB->execute( $strDetailQuery ) )
				{
					$strMessage = fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
				}

				// 明細選択結果の取得
				unset( $aryDetailResult );
				$lngDetailCount = pg_num_rows( $lngDetailResultID );
				if ( $lngDetailCount )
				{
					for ( $k = 0; $k < $lngDetailCount; $k++ )
					{
						$aryDetailResult[] = pg_fetch_array( $lngDetailResultID, $k, PGSQL_ASSOC );
					}
				}

				$objDB->freeResult( $lngDetailResultID );

				// 同じコードの売上データで一番上に表示されている売上データが削除データの場合
				if ( $arySameSlipCodeResult[0]["lngrevisionno"] < 0 )
				{
					$bytDeleteFlag = TRUE;
				}
				else
				{
					$bytDeleteFlag = FALSE;
				}

				// １レコード分の出力
				$aryHtml_add = fncSetSlipTableRow ( $lngColumnCount, $aryResult[$i], $aryDetailResult, $aryHeadViewColumn, $aryData, $aryUserAuthority, $lngSameSlipCount, $j, $bytDeleteFlag );
				$lngColumnCount = $lngColumnCount + count($aryDetailResult);
				
				$strColBuff = '';
				for ( $k = 0; $k < count($aryHtml_add); $k++ )
				{
					$strColBuff .= $aryHtml_add[$k];
				}
				$aryHtml[] =$strColBuff;
//			}
//		}
	}

	$aryHtml[] = "</tbody>";

	$strhtml = implode( "\n", $aryHtml );

	return $strhtml;
}

?>