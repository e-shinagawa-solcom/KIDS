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
*	@param  Array 	$aryViewColumn 			表示対象カラム名の配列
*	@param  Array 	$arySearchColumn 		検索対象カラム名の配列
*	@param  Array 	$arySearchDataColumn 	検索内容の配列
*	@param  Object	$objDB       			DBオブジェクト
*	@param	String	$strSlipCode			納品伝票コード	空白指定時:検索結果出力	納品伝票コード指定時:管理用、同じ納品書ＮＯの一覧取得
*	@param	Integer	$lngSlipNo				納品伝票番号	0:検索結果出力	納品伝票番号指定時:管理用、同じ納品伝票コードとする時の対象外納品伝票番号
*	@param	Boolean	$bytAdminMode			有効な削除データの取得用フラグ	FALSE:検索結果出力	TRUE:管理用、削除データ取得
*	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
*	@access public
*/
function fncGetSearchSlipSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strSlipCode, $lngSlipNo, $bytAdminMode, $strSessionID)
{

	// 表示用カラムに設定されている内容を検索用に文字列設定
	for ( $i = 0; $i < count($aryViewColumn); $i++ )
	{
		$strViewColumnName = $aryViewColumn[$i];

		// 表示項目　管理モードの過去リビジョンデータ、および、明細情報は検索結果より取得

		// 顧客
		if ( $strViewColumnName == "lngCustomerCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
			$arySelectQuery[] = ", cust_c.strCompanyDisplayName as strCustomerDisplayName";
			$flgCustomerCompany = TRUE;
		}

		// 納品伝票コード（納品書NO）
		if ( $strViewColumnName == "strSlipCode" )
		{
			$arySelectQuery[] = ", s.strSlipCode as strSlipCode";
		}
		
		// 備考
		if ( $strViewColumnName == "strNote" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", s.strNote as strNote";
		}

		// 合計金額
		if ( $strViewColumnName == "curTotalPrice" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
		}
	}

	//// 売上Ｎｏ
	//$arySelectQuery[] = ", s.strSalesCode as strSalesCode";

	// 売上状態コード
	$arySelectQuery[] = ", sa.lngSalesStatusCode as lngSalesStatusCode";
	$arySelectQuery[] = ", ss.strSalesStatusName as strSalesStatusName";

	// 通貨単位
	$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
	$flgMonetaryUnit = TRUE;

	// 条件の追加
	$detailFlag = FALSE;

	// 管理モードの検索時、同じ納品伝票コードのデータを取得する場合
	if ( $strSlipCode or $bytAdminMode )
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

		// 削除データ取得時は条件追加
		if ( $bytAdminMode )
		{
			$aryQuery[] = " AND s.lngRevisionNo < 0\n";
		}
	}
	// 管理モードでの同じ納品伝票コードに対する検索モード以外の場合は検索条件を追加する
	else
	{
		// 絶対条件 無効フラグが設定されておらず、最新売上のみ
		$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.lngRevisionNo >= 0";

		// 表示用カラムに設定されている内容を検索用に文字列設定
		for ( $i = 0; $i < count($arySearchColumn); $i++ )
		{
			$strSearchColumnName = $arySearchColumn[$i];

			// ////納品書マスタ内の検索条件////
			// 顧客（売上先）
			if ( $strSearchColumnName == "lngCustomerCode" )
			{
				if ( $arySearchDataColumn["lngCustomerCode"] )
				{
					$aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngCustomerCode"] . "'";
					$flgCustomerCompany = TRUE;
				}
				if ( $arySearchDataColumn["strCustomerName"] )
				{
					$aryQuery[] = " AND UPPER(cust_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strCustomerName"] . "%')";
					$flgCustomerCompany = TRUE;
				}
			}

			// 課税区分（消費税区分）
			if ( $strSearchColumnName == "lngTaxClassCode" )
			{
				if ( $arySearchDataColumn["lngTaxClassCode"] )
				{
					$aryQuery[] = " AND s.strTaxClassName ~* '" . $arySearchDataColumn["lngTaxClassCode"] . "'";
				}
			}

			// 納品伝票コード（納品書NO）
			if ( $strSearchColumnName == "strSlipCode" )
			{
				if ( $arySearchDataColumn["strSlipCode"] )
				{
					$aryQuery[] = " AND UPPER(s.strSlipCode) LIKE UPPER('%" . $arySearchDataColumn["strSlipCode"] . "%')";
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
					$aryQuery[] = " AND s.strDeliveryPlaceCode ~* '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
					$flgCustomerCompany = TRUE;
				}
				if ( $arySearchDataColumn["strDeliveryPlaceName"] )
				{
					$aryQuery[] = " AND UPPER(s.strDeliveryPlaceName) LIKE UPPER('%" . $arySearchDataColumn["strDeliveryPlaceName"] . "%')";
					$flgCustomerCompany = TRUE;
				}
			}

			// 起票者
			if ( $strSearchColumnName == "lngInsertUserCode" )
			{
				if ( $arySearchDataColumn["lngInsertUserCode"] )
				{
					$aryQuery[] = " AND insert_u.strUserDisplayCode ~* '" . $arySearchDataColumn["lngInsertUserCode"] . "'";
					$flgInsertUser = TRUE;
				}
				if ( $arySearchDataColumn["strInsertUserName"] )
				{
					$aryQuery[] = " AND UPPER(insert_u.strInsertUserName) LIKE UPPER('%" . $arySearchDataColumn["strInsertUserName"] . "%')";
					$flgInsertUser = TRUE;
				}
			}

			//
			// 明細テーブルの条件
			//

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
					$aryDetailWhereQuery[] = "UPPER(p.strCustomerSalesCode) LIKE UPPER('%" . $arySearchDataColumn["strCustomerSalesCode"] . "%') ";
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
					$aryDetailWhereQuery[] = "UPPER(p.strGoodsCode) LIKE UPPER('%" . $arySearchDataColumn["strGoodsCode"] . "%') ";
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

	// 明細行の検索対応

	// 明細検索用テーブル結合条件
	$aryDetailFrom = array();
	$aryDetailFrom[] = ", (SELECT distinct on ( sd1.lngSlipNo ) sd1.lngSlipNo ";
	$aryDetailFrom[] = "	,sd1.lngSlipDetailNo";		      // 納品伝票明細番号
	$aryDetailFrom[] = "	,sd1.lngSortKey as lngRecordNo";  // 明細行NO
	$aryDetailFrom[] = "	,sd1.strCustomerSalesCode";	      // 注文書NO
	$aryDetailFrom[] = "	,p.strGoodsCode";                 // 顧客品番
	$aryDetailFrom[] = "	,p.strProductName";			      // 品名
	$aryDetailFrom[] = "	,sd1.lngSalesClassCode";	// 売上区分
	$aryDetailFrom[] = "	,sd1.curProductPrice";		// 単価
	$aryDetailFrom[] = "	,sd1.lngQuantity";	        // 入数
	$aryDetailFrom[] = "	,sd1.lngProductQuantity";	// 数量
	$aryDetailFrom[] = "	,sd1.lngProductUnitCode";	// 単位
	$aryDetailFrom[] = "	,sd1.curSubTotalPrice";		// 税抜金額
	$aryDetailFrom[] = "	,sd1.strNote";				// 明細備考
	$aryDetailFrom[] = "	FROM t_SlipDetail sd1 ";
	$aryDetailFrom[] = "		LEFT JOIN m_Product p ON sd1.strProductCode = p.strProductCode";

	$aryDetailWhereQuery[] = ") as sd";
	// where句（明細行） クエリー連結
	$strDetailQuery = implode("\n", $aryDetailFrom) . "\n";
	// 明細行の条件が存在する場合
	if ( $detailFlag )
	{
		$strDetailQuery .= implode("\n", $aryDetailTargetQuery) . "\n";
	}
	$strDetailQuery .= implode("\n", $aryDetailWhereQuery) . "\n";
	

	// SQL文の作成
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT distinct s.lngSlipNo as lngSlipNo";
	$aryOutQuery[] = "	,s.lngRevisionNo as lngRevisionNo";
	$aryOutQuery[] = "	,s.dtmInsertDate as dtmInsertDate";

	// 明細行の 'order by' 用に追加
	$aryOutQuery[] = "	,sd.lngSlipDetailNo";		      // 納品伝票明細番号
	$aryOutQuery[] = "	,sd.lngRecordNo";                 // 明細行NO
	$aryOutQuery[] = "	,sd.strCustomerSalesCode";	      // 注文書NO
	$aryOutQuery[] = "	,sd.strGoodsCode";                // 顧客品番
	$aryOutQuery[] = "	,sd.strProductName";			  // 品名
	$aryOutQuery[] = "	,sd.lngSalesClassCode";	          // 売上区分
	$aryOutQuery[] = "	,sd.curProductPrice";		      // 単価
	$aryOutQuery[] = "	,sd.lngQuantity";	              // 入数
	$aryOutQuery[] = "	,sd.lngProductQuantity";	      // 数量
	$aryOutQuery[] = "	,sd.lngProductUnitCode";	      // 単位
	$aryOutQuery[] = "	,sd.curSubTotalPrice";		      // 税抜金額
	$aryOutQuery[] = "	,sd.strNote";				      // 明細備考

	// select句 クエリー連結
	$aryOutQuery[] = implode("\n", $arySelectQuery);

	// From句 の生成
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Slip s";
	$aryFromQuery[] = " LEFT JOIN m_Sales sa ON s.lngSalesNo = sa.lngSalesNo";
	$aryFromQuery[] = " LEFT JOIN m_SalesStatus ss ON sa.lngSalesStatusCode = ss.lngSalesStatusCode";

	// 追加表示用の参照マスタ対応
	if ( $flgCustomerCompany )
	{
		$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON s.strCustomerCode = cust_c.strCompanyDisplayCode";
	}
	if ( $flgMonetaryUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	}
	if ( $flgInsertUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User insert_u ON s.strInsertUserCode = insert_u.strUserDisplayCode";
	}

	// From句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryFromQuery);
	
	// Where句 クエリー連結
	$aryOutQuery[] = $strDetailQuery;

	// Where句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryQuery);

	// 明細行用の条件連結
	$aryOutQuery[] = " AND sd.lngSlipNo = s.lngSlipNo";


	/////////////////////////////////////////////////////////////
	//// 最新売上（リビジョン番号が最大、リバイズ番号が最大、////
	//// かつリビジョン番号負の値で無効フラグがFALSEの       ////
	//// 同じ納品伝票コードを持つデータが無い売上データ          ////
	/////////////////////////////////////////////////////////////
	// 納品伝票コードが指定されていない場合は検索条件を設定する
	if ( !$strSlipCode )
	{
		$aryOutQuery[] = " AND s.lngRevisionNo = ( "
			. "SELECT MAX( s1.lngRevisionNo ) FROM m_Slip s1 WHERE s1.strSlipCode = s.strSlipCode AND s1.bytInvalidFlag = false )";

		// 管理モードの場合は削除データも検索対象とするため以下の条件は対象外
		if ( !$arySearchDataColumn["Admin"] )
		{
			$aryOutQuery[] = " AND 0 <= ( "
				. "SELECT MIN( s2.lngRevisionNo ) FROM m_Slip s2 WHERE s2.bytInvalidFlag = false AND s2.strSlipCode = s.strSlipCode )";
		}
	}

	// 管理モードの検索時、同じ納品伝票コードのデータを取得する場合
	if ( $strSlipCode or $bytAdminMode )
	{
		$aryOutQuery[] = " ORDER BY dtmInsertDate DESC";
	}
	else
	{
		// ソート条件設定
		$aryOutQuery[] = " ORDER BY lngSlipNo DESC";

		// TODO:ソート機能必要か？（要確認）
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
*	@param  Array 	$aryDetailViewColumn 	表示対象明細カラム名の配列
*	@param  String 	$lngSlipNo 			    対象納品伝票番号
*	@param  Array 	$aryData 				POSTデータの配列
*	@param  Object	$objDB       			DBオブジェクト
*	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
*	@access public
*/
function fncGetSlipToProductSQL ( $aryDetailViewColumn, $lngSlipNo, $aryData, $objDB )
{
	reset( $aryDetailViewColumn );

	// 表示用カラムに設定されている内容を検索用に文字列設定
	for ( $i = 0; $i < count($aryDetailViewColumn); $i++ )
	{
		$strViewColumnName = $aryDetailViewColumn[$i];
		
		// 注文書NO.
		if ( $strViewColumnName == "strCustomerSalesCode" )
		{
			$arySelectQuery[] = ", sd.strCustomerSalesCode as strCustomerSalesCode";
		}

		// 顧客品番
		if ( $strViewColumnName == "strGoodsCode" )
		{
			$arySelectQuery[] = ", p.strGoodsCode as strGoodsCode";
			$flgProductCode = TRUE;
		}

		// 品名
		if ( $strViewColumnName == "strProductName" )
		{
			$arySelectQuery[] = ", p.strProductName as strProductName";
			$flgProductCode = TRUE;
		}

		// 売上区分
		if ( $strViewColumnName == "lngSalesClassCode" )
		{
			$arySelectQuery[] = ", sd.lngSalesClassCode as lngSalesClassCode";
			$arySelectQuery[] = ", sc.strSalesClassName as strSalesClassName";
			$flgSalesClass = TRUE;
		}
		
		// 単価
		if ( $strViewColumnName == "curProductPrice" )
		{
			$arySelectQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
		}

		// 入数
		if ( $strViewColumnName == "lngQuantity" )
		{
			$arySelectQuery[] = ", To_char( sd.lngQuantity, '9,999,999,990' )  as lngQuantity";
		}

		// 数量
		if ( $strViewColumnName == "lngProductQuantity" )
		{
			$arySelectQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
		}
		
		// 単位
		if ( $strViewColumnName == "lngProductUnitCode" )
		{
			$arySelectQuery[] = ", sd.lngProductUnitCode as lngProductUnitCode";
			$arySelectQuery[] = ", pu.strProductUnitName as strProductUnitName";
			$flgProductUnit = TRUE;
		}

		// 税抜金額
		if ( $strViewColumnName == "curSubTotalPrice" )
		{
			$arySelectQuery[] = ", To_char( sd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
		}

		// 明細備考
		if ( $strViewColumnName == "strDetailNote" )
		{
			$arySelectQuery[] = ", sd.strNote as strDetailNote";
		}

	}

	// 絶対条件 対象納品伝票番号の指定
	$aryQuery[] = " WHERE sd.lngSlipNo = " . $lngSlipNo . "";

	// 条件の追加

	// ////納品伝票マスタ内の検索条件////
	// SQL文の作成
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT sd.lngSortKey as lngRecordNo";		//明細行NO
	$aryOutQuery[] = "	,sd.lngSlipNo as lngSlipNo";			//納品伝票番号
	$aryOutQuery[] = "	,sd.lngRevisionNo as lngRevisionNo";	//リビジョン番号
	
	// select句 クエリー連結
	if( !empty($arySelectQuery) )
	{
		$aryOutQuery[] = implode("\n", $arySelectQuery);
	}

	// From句 の生成
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM t_SlipDetail sd";

	// 追加表示用の参照マスタ対応
	$aryFromQuery[] = " LEFT JOIN m_Product p USING (strProductCode)";
		 
	if ( $flgSalesClass )
	{
		$aryFromQuery[] = " LEFT JOIN m_SalesClass sc USING (lngSalesClassCode)";
	}
	if ( $flgProductUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_ProductUnit pu ON sd.lngProductUnitCode = pu.lngProductUnitCode";
	}

	// From句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryFromQuery);
	// Where句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryQuery);

	// ソート条件指定
	if ( $aryData["strSortOrder"] == "ASC" )
	{
		$strAsDs = "DESC";	// ヘッダ項目とは逆順にする
	}
	else
	{
		$strAsDs = "ASC";	//降順
	}

	$aryOutQuery[] = " ORDER BY sd.lngSortKey ASC";

	// TODO:ソート機能必要か？（要確認）
	/*
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
* 納品書検索結果表示関数（ヘッダ用）
*
*	納品書検索結果からテーブル構成で結果を出力する関数
*	ヘッダ行を表示する
*
*	@param  Integer $lngColumnCount 		行数
*	@param  Array 	$aryHeadResult 			ヘッダ行の検索結果が格納された配列
*	@param  Array 	$aryDetailResult 		明細行の検索結果が格納された配列
*	@param  Array 	$aryHeadViewColumn 		ヘッダ表示対象カラム名の配列
*	@param  Array 	$aryDetailViewColumn 	明細表示対象カラム名の配列
*	@param  Array 	$aryData 				ＰＯＳＴデータ群
*	@param	Array	$aryUserAuthority		ユーザーの操作に対する権限が入った配列
*	@access public
*/
function fncSetSlipHeadTable ( $lngColumnCount, $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngReviseTotalCount, $lngReviseCount, $bytDeleteFlag )
{
	for ( $i = 0; $i < count($aryDetailResult); $i++ )
	{
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

				// 詳細表示
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

				// 修正
				if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
				{
					// 売上データの状態により分岐  //// 状態が「締め済」、また削除対象の場合修正ボタンは選択不可
					// 最新売上が削除データの場合も選択不可
					if ( $aryHeadResult["lngsalesstatuscode"] == DEF_SALES_CLOSED 
						or $aryHeadResult["lngrevisionno"] < 0 
						or $bytDeleteFlag )
					{
						$aryHtml[] = "\t<td></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/renew_off_bt.gif\" lngslipno=\"" . $aryDetailResult[$i]["lngslipno"] . "\" class=\"detail button\"></td>\n";
					}
				}

				// 削除
				if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
				{
					// 管理モードで無い場合もしくはリバイズが存在しない場合
					if ( !$aryData["Admin"] or $lngReviseTotalCount == 1 )
					{
						// 売上データの状態により分岐  //// 状態が「締め済」の場合削除ボタンを選択不可
						// 最新発注が削除データの場合も選択不可
						if ( $aryHeadResult["lngsalesstatuscode"] != DEF_SALES_CLOSED 
							and !$bytDeleteFlag )

						{
							$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngslipno=\"" . $aryDetailResult[$i]["lngslipno"] . "\" class=\"detail button\"></td>\n";
						}
						else
						{
							$aryHtml[] = "\t<td></td>\n";
						}
					}
					// 管理モードで複数リバイズが存在する場合
					else
					{
						// 最新受注の場合
						if ( $lngReviseCount == 0 )
						{
							// 売上データの状態により分岐  //// 状態が「締め済」の場合削除ボタンを選択不可
							// 最新売上が削除データの場合も選択不可
							if ( $aryHeadResult["lngsalesstatuscode"] != DEF_SALES_CLOSED 
								and !$bytDeleteFlag )
							{
								$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngslipno=\"" . $aryDetailResult[$i]["lngslipno"] . "\" class=\"detail button\"></td>\n";
							}
							else
							{
								$aryHtml[] = "\t<td></td>\n";
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
				// 納品日
				else if ( $strColumnName == "dtmDeliveryDate" )
				{
					$TdData .= str_replace( "-", "/", substr( $aryHeadResult["dtmdeliverydate"], 0, 19 ) );
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
					if ( $aryHeadResult["strinsertuserdisplaycode"] )
					{
						$strText .= "[" . $aryHeadResult["strinsertuserdisplaycode"] ."]";
					}
					else
					{
						$strText .= "     ";
					}
					$strText .= " " . $aryHeadResult["strinsertuserdisplayname"];
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
function fncSetSlipTable ( $aryResult, $aryViewColumn, $arySearchColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName)
{
	// 準備

	// 表示カラムのヘッダ部と明細部の分離処理
	for ( $i = 0; $i < count($aryViewColumn); $i++ )
	{
		$strColumnName = $aryViewColumn[$i];

		// ボタンの場合ここで表示、非表示切り替え
		if ( $strColumnName == "btnDetail" )
		{
			if ( $aryUserAuthority["Detail"] )
			{
				$aryHeadViewColumn[] = $strColumnName;
			}
		}
		else if ( $strColumnName == "btnFix" )
		{
			if ( $aryUserAuthority["Fix"] )
			{
				$aryHeadViewColumn[] = $strColumnName;
			}
		}
		else if ( $strColumnName == "btnDelete" )
		{
			if ( $aryUserAuthority["Delete"] )
			{
				$aryHeadViewColumn[] = $strColumnName;
			}
		}
		else if ( $strColumnName == "btnInvalid" )
		{
			if ( $aryUserAuthority["Invalid"] )
			{
				$aryHeadViewColumn[] = $strColumnName;
			}
		}
		// 詳細部
		else if ( $strColumnName == "lngRecordNo"				//明細行NO
			or $strColumnName == "strCustomerSalesCode"			//注文書NO
			or $strColumnName == "strGoodsCode"					//顧客品番
			or $strColumnName == "strProductName"				//品名
			or $strColumnName == "lngSalesClassCode"			//売上区分
			or $strColumnName == "curProductPrice"				//単価
			or $strColumnName == "lngQuantity"					//入数
			or $strColumnName == "lngProductQuantity"			//数量
			or $strColumnName == "lngProductUnitCode"			//単位
			or $strColumnName == "curSubTotalPrice"				//税抜金額
			or $strColumnName == "strDetailNote"      )			//明細備考
		{
			$aryDetailViewColumn[] = $strColumnName;
			$aryHeadViewColumn[] = $strColumnName;
		}
		// ヘッダ部
		else
		{
			$aryHeadViewColumn[] = $strColumnName;
		}
	}

	// テーブルの形成
	$lngResultCount = count($aryResult);

	$lngColumnCount = 1;
	
	// 項目名列の生成 start=========================================
	$aryHtml[] = "<thead>";
	$aryHtml[] = "<tr>";
	$aryHtml[] = "\t<th class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/copy_off_bt.gif\" class=\"copy button\"></th>";

	// 表示対象カラムの配列より項目設定
	for ( $j = 0; $j < count($aryViewColumn); $j++ )
	{
		$Addth = "\t<th>";
		$strColumnName = $aryViewColumn[$j];
		
		// ソート項目以外の場合
		if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" )
		{
			// ソート項目以外の場合
			if ( ( $strColumnName == "btnDetail" and $aryUserAuthority["Detail"] ) 
			or ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] ) 
			or ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] ) )
			{
				$Addth .= $aryTytle[$strColumnName];
			}
		}
		// ソート項目の場合
		else
		{
			$Addth .= $aryTytle[$strColumnName];
		}

		$Addth .= "</th>";
		$aryHtml[] = $Addth;
	}
	$aryHtml[] = "</tr>";
	$aryHtml[] = "</thead>";

// 項目名列の生成 end=========================================

	$aryHtml[] = "<tbody>";

	for ( $i = 0; $i < $lngResultCount; $i++ )
	{
// 管理モード用過去リバイズ、削除データ出力start==================================
		// 管理モードの場合　同じ納品伝票コードの一覧を取得し表示する

		$strSlipCodeBase = $aryResult[$i]["strslipcode"];

		$strSameSlipCodeQuery = fncGetSearchSlipSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $strSlipCodeBase, $aryResult[$i]["lngslipno"], FALSE, $aryData["strSessionID"]);

		// 値をとる =====================================
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameSlipCodeQuery, $objDB );

		// 配列のクリア
		unset( $arySameSlipCodeResult );

		if ( $lngResultNum )
		{
			for ( $j = 0; $j < $lngResultNum; $j++ )
			{
				$arySameSlipCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
			}
			$lngSameSalesCount = $lngResultNum;
		}
		$objDB->freeResult( $lngResultID );

		// 同じ納品伝票コードでの過去リバイズデータが存在すれば
		if ( $lngResultNum )
		{
			for ( $j = 0; $j < $lngSameSalesCount; $j++ )
			{
				// 検索結果部分の設定

				reset( $arySameSlipCodeResult[$j] );

				// 明細出力用の調査
				$lngDetailViewCount = count( $aryDetailViewColumn );

				if ( $lngDetailViewCount )
				{
					// 明細行数の調査
					$strDetailQuery = fncGetSlipToProductSQL ( $aryDetailViewColumn, $arySameSlipCodeResult[$j]["lngslipno"], $aryData, $objDB );

					// クエリー実行
					if ( !$lngDetailResultID = $objDB->execute( $strDetailQuery ) )
					{
						$strMessage = fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
					}

					$lngDetailCount = pg_num_rows( $lngDetailResultID );

					// 配列のクリア
					unset( $aryDetailResult );

					// 結果の取得
					if ( $lngDetailCount )
					{
						for ( $k = 0; $k < $lngDetailCount; $k++ )
						{
							$aryDetailResult[] = pg_fetch_array( $lngDetailResultID, $k, PGSQL_ASSOC );
						}
					}

					$objDB->freeResult( $lngDetailResultID );
				}

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
				$aryHtml_add = fncSetSlipHeadTable ( $lngColumnCount, $arySameSlipCodeResult[$j], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameSalesCount, $j, $bytDeleteFlag );
				$lngColumnCount = $lngColumnCount + count($aryDetailResult);
				
				$strColBuff = '';
				for ( $k = 0; $k < count($aryHtml_add); $k++ )
				{
					$strColBuff .= $aryHtml_add[$k];
				}
				$aryHtml[] =$strColBuff;
			}
		}

// 管理モード用過去リバイズデータ出力end==================================

	}

	$aryHtml[] = "</tbody>";

	$strhtml = implode( "\n", $aryHtml );

	return $strhtml;
}

?>