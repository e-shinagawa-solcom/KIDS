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
					$dtmSearchDate = $arySearchDataColumn["dtmDeliveryDateTo"] . " 23:59:59.99999";
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
					$aryQuery[] = " AND insert_u.struserdisplaycode ~* '" . $arySearchDataColumn["lngInsertUserCode"] . "'";
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
	$aryOutQuery[] = "	,s.lngSlipNo as lngpkno";			    //売上番号
	$aryOutQuery[] = "	,s.lngSalesNo as lngSalesNo";			    //売上番号
	$aryOutQuery[] = "	,s.lngRevisionNo as lngRevisionNo";			//リビジョン番号
	$aryOutQuery[] = "	,s.dtmInsertDate as dtmInsertDate";			//作成日
	// 顧客
	$arySelectQuery[] = ", cust_c.strcompanydisplaycode as strCustomerDisplayCode";
	$arySelectQuery[] = ", s.strcustomername as strCustomerDisplayName";
	// 顧客の国
	$arySelectQuery[] = ", cust_c.lngCountryCode as lngcountrycode";
	// 請求書番号
	$arySelectQuery[] = ", sa.lngInvoiceNo as lnginvoiceno";
	// 課税区分
	$arySelectQuery[] = ", s.strTaxClassName as strTaxClassName";
	// 納品伝票コード（納品書NO）
	$arySelectQuery[] = ", s.strSlipCode as strSlipCode";
	// 納品日
	$arySelectQuery[] = ", to_char( s.dtmDeliveryDate, 'YYYY/MM/DD' ) as dtmDeliveryDate";
	// 納品先
	$arySelectQuery[] = " , delv_c.strcompanydisplaycode as strdeliveryplacecode";
	$arySelectQuery[] = " , s.strDeliveryPlaceName as strDeliveryPlaceName";
	// 起票者
	$arySelectQuery[] = ", u.struserdisplaycode as strusercode";
	$arySelectQuery[] = ", s.strUserName as strusername";
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
	$arySelectQuery[] = ", s.lngMonetaryUnitCode";
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
	$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON s.lngCustomerCode = cust_c.lngCompanyCode";
	$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	$aryFromQuery[] = " LEFT JOIN m_User u ON s.lngusercode = u.lngusercode";
	$aryFromQuery[] = " LEFT JOIN m_Company delv_c ON s.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
	// From句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryFromQuery);

	// 明細検索用テーブル結合条件
	$aryDetailFrom = array();
	$aryDetailFrom[] = ", (SELECT sd1.lngSlipNo ";
	$aryDetailFrom[] = "	,sd1.lngSlipDetailNo";				// 納品伝票明細番号
	$aryDetailFrom[] = "	,sd1.lngrevisionno";	// リビジョン番号
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
	$aryOutQuery[] = " AND sd.lngrevisionno = s.lngrevisionno";


	/////////////////////////////////////////////////////////////
	//// 最新売上（リビジョン番号が最大、リバイズ番号が最大、     ////
	//// かつリビジョン番号負の値で無効フラグがFALSEの           ////
	//// 同じ納品伝票コードを持つデータが無い売上データ          ////
	/////////////////////////////////////////////////////////////
	// 納品伝票コードが指定されていない場合は検索条件を設定する
	if ( !$strSlipCode )
	{
		// 管理モードの場合は削除データも検索対象とするため以下の条件は対象外
		if ( !$arySearchDataColumn["Admin"] )
		{
//			$aryOutQuery[] = " AND 0 <= ( "
//				. "SELECT MIN( s2.lngRevisionNo ) FROM m_Slip s2 WHERE s2.bytInvalidFlag = false AND s2.strSlipCode = s.strSlipCode )";
			$aryOutQuery[] = " AND not exists (SELECT lngslipno from m_slip s1 where s1.lngslipno=s.lngslipno and s1.lngRevisionNo < 0 and s1.bytInvalidFlag = false)";
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
	}
	return implode("\n", $aryOutQuery);
}

?>