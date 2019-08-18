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
*	@param	String	$strSalesCode			売上コード	空白指定時:検索結果出力	売上コード指定時:管理用、同じ売上コードの一覧取得
*	@param	Integer	$lngSalesNo				売上Ｎｏ	0:検索結果出力	売上Ｎｏ指定時:管理用、同じ売上コードとする時の対象外売上NO
*	@param	Boolean	$bytAdminMode			有効な削除データの取得用フラグ	FALSE:検索結果出力	TRUE:管理用、削除データ取得
*	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
*	@access public
*/
function fncGetSearchSlipSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strSalesCode, $lngSalesNo, $bytAdminMode )
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

		// 納品伝票コード（納税書NO）
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

	// 売上Ｎｏ
	$arySelectQuery[] = ", s.strSalesCode as strSalesCode";

	// 通貨単位
	$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
	$flgMonetaryUnit = TRUE;

	// 条件の追加
	$detailFlag = FALSE;

	// 管理モードの検索時、同じ売上コードのデータを取得する場合
	if ( $strSalesCode or $bytAdminMode )
	{
		// 同じ売上コードに対して指定の売上番号のデータは除外する
		if ( $lngSalesNo )
		{
			$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.strSalesCode = '" . $strSalesCode . "'";
		}
		else
		{
			fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../sc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}

		// 削除データ取得時は条件追加
		if ( $bytAdminMode )
		{
			$aryQuery[] = " AND s.lngRevisionNo < 0\n";
		}
	}
	// 管理モードでの同じ売上コードに対する検索モード以外の場合は検索条件を追加する
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

			// 納品書NO.（納品伝票コード）
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
	$aryDetailFrom[] = ", (SELECT distinct on ( sd1.lngSalesNo ) sd1.lngSalesNo ";
	$aryDetailFrom[] = "	,sd1.lngSalesDetailNo";
	$aryDetailFrom[] = "	,p.strProductCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayName";
	$aryDetailFrom[] = "	,mu.struserdisplaycode";
	$aryDetailFrom[] = "	,mu.struserdisplayname";
	$aryDetailFrom[] = "	,p.strProductName";
	$aryDetailFrom[] = "	,p.strProductEnglishName";
	$aryDetailFrom[] = "	,sd1.lngSalesClassCode";	// 売上区分
	$aryDetailFrom[] = "	,p.strGoodsCode";
	$aryDetailFrom[] = "	,sd1.dtmDeliveryDate";		// 納期
	$aryDetailFrom[] = "	,sd1.curProductPrice";		// 単価
	$aryDetailFrom[] = "	,sd1.lngProductUnitCode";	// 単位
	$aryDetailFrom[] = "	,sd1.lngProductQuantity";	// 製品数量
	$aryDetailFrom[] = "	,sd1.curSubTotalPrice";		// 税抜金額
	$aryDetailFrom[] = "	,sd1.lngTaxClassCode";		// 税区分
	$aryDetailFrom[] = "	,mt.curtax";				// 税率
	$aryDetailFrom[] = "	,sd1.curtaxprice";			// 税額
	$aryDetailFrom[] = "	,sd1.strNote";				// 明細備考
	$aryDetailFrom[] = "	FROM t_SalesDetail sd1 ";
	$aryDetailFrom[] = "		LEFT JOIN m_Product p ON sd1.strProductCode = p.strProductCode";
	$aryDetailFrom[] = "		left join m_group mg on p.lnginchargegroupcode = mg.lnggroupcode";
	$aryDetailFrom[] = "		left join m_user  mu on p.lnginchargeusercode = mu.lngusercode";
	$aryDetailFrom[] = "		left join m_tax  mt on mt.lngtaxcode = sd1.lngtaxcode";

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
	$aryOutQuery[] = "SELECT distinct s.lngSalesNo as lngSalesNo";
	$aryOutQuery[] = "	,s.lngRevisionNo as lngRevisionNo";
	$aryOutQuery[] = "	,s.lngSalesStatusCode as lngSalesStatusCode";

	// 明細行の 'order by' 用に追加
	$aryOutQuery[] = "	,sd.lngSalesDetailNo";
	$aryOutQuery[] = "	,sd.strProductCode";
	$aryOutQuery[] = "	,sd.struserdisplaycode";
	$aryOutQuery[] = "	,sd.strGroupDisplayCode";
	$aryOutQuery[] = "	,sd.strProductName";
	$aryOutQuery[] = "	,sd.strProductEnglishName";
	$aryOutQuery[] = "	,sd.lngSalesClassCode";
	$aryOutQuery[] = "	,sd.strGoodsCode";
	$aryOutQuery[] = "	,sd.dtmDeliveryDate";
	$aryOutQuery[] = "	,sd.curProductPrice";
	$aryOutQuery[] = "	,sd.lngProductUnitCode";
	$aryOutQuery[] = "	,sd.lngProductQuantity";
	$aryOutQuery[] = "	,sd.curSubTotalPrice";
	$aryOutQuery[] = "	,sd.lngTaxClassCode";
	$aryOutQuery[] = "	,sd.curTax";
	$aryOutQuery[] = "	,sd.curTaxPrice";
	$aryOutQuery[] = "	,sd.strNote";

	// select句 クエリー連結
	$aryOutQuery[] = implode("\n", $arySelectQuery);

	// From句 の生成
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Sales s";

	// 追加表示用の参照マスタ対応
	if ( $flgCustomerCompany )
	{
		$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode";
	}
	if ( $flgMonetaryUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	}
	if ( $flgInsertUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User insert_u ON s.lngInsertUserCode = insert_u.lngUserCode";
	}

	/*
	if ( $flgReceive )
	{
		$aryFromQuery[] = "	left join t_salesdetail tsd";
		$aryFromQuery[] = "	on tsd.lngsalesno = s.lngsalesno";
		$aryFromQuery[] = "		left join m_Receive r on r.lngreceiveno = tsd.lngreceiveno";
	}
	if ( $flgInputUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User input_u ON s.lngInputUserCode = input_u.lngUserCode";
	}
	if ( $flgSalesStatus )
	{
		$aryFromQuery[] = " LEFT JOIN m_SalesStatus ss USING (lngSalesStatusCode)";
	}
	*/

	/*
	if ( $flgWorkFlowStatus )
	{
		$aryFromQuery[] = " left join
		( m_workflow mw
			left join t_workflow tw
			on mw.lngworkflowcode = tw.lngworkflowcode
			and tw.lngworkflowsubcode = (select max(lngworkflowsubcode) from t_workflow where lngworkflowcode = tw.lngworkflowcode)
		) on  mw.strworkflowkeycode = trim(to_char(s.lngSalesNo, '9999999'))
			and mw.lngfunctioncode = " . DEF_FUNCTION_SC1; // 売上登録時のWFデータを対象にする為に条件指定
	}
	*/

	// From句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryFromQuery);
	
	// Where句 クエリー連結
	$aryOutQuery[] = $strDetailQuery;

	// Where句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryQuery);

	// 明細行用の条件連結
	$aryOutQuery[] = " AND sd.lngSalesNo = s.lngSalesNo";


	/////////////////////////////////////////////////////////////
	//// 最新売上（リビジョン番号が最大、リバイズ番号が最大、////
	//// かつリビジョン番号負の値で無効フラグがFALSEの       ////
	//// 同じ売上コードを持つデータが無い売上データ          ////
	/////////////////////////////////////////////////////////////
	// 売上コードが指定されていない場合は検索条件を設定する
	if ( !$strSalesCode )
	{
		$aryOutQuery[] = " AND s.lngRevisionNo = ( "
			. "SELECT MAX( s1.lngRevisionNo ) FROM m_Sales s1 WHERE s1.strSalesCode = s.strSalesCode AND s1.bytInvalidFlag = false )";

		// 管理モードの場合は削除データも検索対象とするため以下の条件は対象外
		if ( !$arySearchDataColumn["Admin"] )
		{
			$aryOutQuery[] = " AND 0 <= ( "
				. "SELECT MIN( s2.lngRevisionNo ) FROM m_Sales s2 WHERE s2.bytInvalidFlag = false AND s2.strSalesCode = s.strSalesCode )";
		}
	}

	// 管理モードの検索時、同じ売上コードのデータを取得する場合
	if ( $strSalesCode or $bytAdminMode )
	{
		$aryOutQuery[] = " ORDER BY dtmInsertDate DESC";
	}
	else
	{
		// ソート条件設定
		$aryOutQuery[] = " ORDER BY lngSalesNo DESC";


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
* 対応する注文書NOのデータに対する明細行を取得するSQL文の作成関数
*
*	注文書NOから明細を取得する SQL文を作成する
*
*	@param  Array 	$aryDetailViewColumn 	表示対象明細カラム名の配列
*	@param  String 	$lngSalesNo 			対象注文書NO
*	@param  Array 	$aryData 				POSTデータの配列
*	@param  Object	$objDB       			DBオブジェクト
*	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
*	@access public
*/
function fncGetSalesToProductSQL ( $aryDetailViewColumn, $lngSalesNo, $aryData, $objDB )
{
	reset( $aryDetailViewColumn );

	// 表示用カラムに設定されている内容を検索用に文字列設定
	for ( $i = 0; $i < count($aryDetailViewColumn); $i++ )
	{
		$strViewColumnName = $aryDetailViewColumn[$i];

		// 表示項目
		// 製品コード
		if ( $strViewColumnName == "strProductCode" )
		{
			$arySelectQuery[] = ", sd.strProductCode as strProductCode";
		}

		// 部門
		if ( $strViewColumnName == "lngInChargeGroupCode" )
		{
			$arySelectQuery[] = ", '['||mg.strgroupdisplaycode||'] '|| mg.strgroupdisplayname as lngInChargeGroupCode";
		}
		// 担当者
		if ( $strViewColumnName == "lngInChargeUserCode" )
		{
			$arySelectQuery[] = ", '['||mu.struserdisplaycode ||'] '|| mu.struserdisplayname  as lngInChargeUserCode";
		}

		// 製品名称（日本語）
		if ( $strViewColumnName == "strProductName" )
		{
			$arySelectQuery[] = ", p.strProductName as strProductName";
			$flgProductCode = TRUE;
		}

		// 製品名称（英語）
		if ( $strViewColumnName == "strProductEnglishName" )
		{
			$arySelectQuery[] = ", p.strProductEnglishName as strProductEnglishName";
			$flgProductCode = TRUE;
		}
		// 売上区分
		if ( $strViewColumnName == "lngSalesClassCode" )
		{
			$arySelectQuery[] = ", sd.lngSalesClassCode as lngSalesClassCode";
			$arySelectQuery[] = ", ss.strSalesClassName as strSalesClassName";
			$flgSalesClass = TRUE;
		}

		// 顧客品番
		if ( $strViewColumnName == "strGoodsCode" )
		{
			$arySelectQuery[] = ", p.strGoodsCode as strGoodsCode";
			$flgProductCode = TRUE;
		}

		// 納期
		if ( $strViewColumnName == "dtmDeliveryDate" )
		{
			$arySelectQuery[] = ", to_char( sd.dtmDeliveryDate, 'YYYY/MM/DD' ) as dtmDeliveryDate";
		}

		// 単価
		if ( $strViewColumnName == "curProductPrice" )
		{
			$arySelectQuery[] = ", To_char( sd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
		}

		// 単位
		if ( $strViewColumnName == "lngProductUnitCode" )
		{
			$arySelectQuery[] = ", sd.lngProductUnitCode as lngProductUnitCode";
			$arySelectQuery[] = ", pu.strProductUnitName as strProductUnitName";
			$flgProductUnit = TRUE;
		}

		// 数量
		if ( $strViewColumnName == "lngProductQuantity" )
		{
			$arySelectQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
		}

		// 税抜金額
		if ( $strViewColumnName == "curSubTotalPrice" )
		{
			$arySelectQuery[] = ", To_char( sd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
		}

		// 税区分
		if ( $strViewColumnName == "lngTaxClassCode" )
		{
			$arySelectQuery[] = ", sd.lngTaxClassCode as lngTaxClassCode";
			$arySelectQuery[] = ", tc.strTaxClassName as strTaxClassName";
			$flgTaxClass = TRUE;
		}

		// 税率
		if ( $strViewColumnName == "curTax" )
		{
			$arySelectQuery[] = ", sd.lngTaxCode as lngTaxCode";
			$arySelectQuery[] = ", To_char( t.curTax, '9,999,999,990.999' ) as curTax";
			$flgTax = TRUE;
		}

		// 税額
		if ( $strViewColumnName == "curTaxPrice" )
		{
			$arySelectQuery[] = ", To_char( sd.curTaxPrice, '9,999,999,990.99' )  as curTaxPrice";
		}

		// 明細備考
		if ( $strViewColumnName == "strDetailNote" )
		{
			$arySelectQuery[] = ", sd.strNote as strDetailNote";
		}
	}

	// 絶対条件 対象売上NOの指定
	$aryQuery[] = " WHERE sd.lngSalesNo = " . $lngSalesNo . "";

	// 条件の追加

	// ////売上マスタ内の検索条件////
	// SQL文の作成
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT sd.lngSortKey as lngRecordNo";
	$aryOutQuery[] = "	,sd.lngSalesNo as lngSalesNo";
	$aryOutQuery[] = "	,sd.lngRevisionNo as lngRevisionNo";
	
	// select句 クエリー連結
	if( !empty($arySelectQuery) )
	{
		$aryOutQuery[] = implode("\n", $arySelectQuery);
	}

	// From句 の生成
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM t_SalesDetail sd";

	// 追加表示用の参照マスタ対応
//	if ( $flgProductCode )
//	{
		$aryFromQuery[] = " LEFT JOIN m_Product p USING (strProductCode)";
		$aryFromQuery[] = " left join m_group mg on mg.lnggroupcode = p.lnginchargegroupcode";
 		$aryFromQuery[] = " left join m_user  mu on mu.lngusercode = p.lnginchargeusercode";
//	}
	if ( $flgSalesClass )
	{
		$aryFromQuery[] = " LEFT JOIN m_SalesClass ss USING (lngSalesClassCode)";
	}
	if ( $flgProductUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_ProductUnit pu ON sd.lngProductUnitCode = pu.lngProductUnitCode";
	}
	if ( $flgTaxClass )
	{
		$aryFromQuery[] = " LEFT JOIN m_TaxClass tc USING (lngTaxClassCode)";
	}
	if ( $flgTax )
	{
		$aryFromQuery[] = " LEFT JOIN m_Tax t USING (lngTaxCode)";
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
			//$aryOutQuery[] = " ORDER BY sd." . $aryData["strSort"] . " " . $strAsDs . ", sd.lngSortKey ASC";
			$aryOutQuery[] = " ORDER BY sd.lngSortKey ASC";
	}

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
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngsalesno=\"" . $aryDetailResult[$i]["lngsalesno"] . "\" class=\"detail button\"></td>\n";
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
	// 2004.03.01 Suzukaze update start
	//					or ( $aryHeadResult["lngsalesstatuscode"] == DEF_SALES_END and !$aryData["Admin"] ) 
	// 2004.03.01 Suzukaze update end
						or $aryHeadResult["lngrevisionno"] < 0 
						or $bytDeleteFlag )
					{
						$aryHtml[] = "\t<td></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/renew_off_bt.gif\" lngsalesno=\"" . $aryDetailResult[$i]["lngsalesno"] . "\" class=\"detail button\"></td>\n";
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
	// 2004.03.01 Suzukaze update start
	//						and $aryHeadResult["lngsalesstatuscode"] != DEF_SALES_END 
	// 2004.03.01 Suzukaze update end
							and !$bytDeleteFlag )

						{
							$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngsalesno=\"" . $aryDetailResult[$i]["lngsalesno"] . "\" class=\"detail button\"></td>\n";
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
								$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngsalesno=\"" . $aryDetailResult[$i]["lngsalesno"] . "\" class=\"detail button\"></td>\n";
							}
							else
							{
								$aryHtml[] = "\t<td></td>\n";
							}
						}
					}
				}
			}
			else if ($strColumnName != "") {
				$TdData = "\t<td>";
				$TdDataUse = true;
				$strText = "";
				// 登録日
				if ( $strColumnName == "dtmInsertDate" )
				{
					$TdData .= str_replace( "-", "/", substr( $aryHeadResult["dtminsertdate"], 0, 19 ) );
				}

				// 計上日
				else if ( $strColumnName == "dtmSalesAppDate" )
				{
					$TdData .= str_replace( "-", "/", $aryHeadResult["dtmsalesappdate"] );
				}

				// 売上NO
				else if ( $strColumnName == "strSalesCode" )
				{
					$TdData .= $aryHeadResult["strsalescode"];
					// 管理モードの場合　リビジョン番号を表示する
					if ( $aryData["Admin"] )
					{
						$TdData .= "</td>\n\t<td>" . $aryHeadResult["lngrevisionno"];
					}
				}

				// 顧客受注番号
				else if ( $strColumnName == "strCustomerReceiveCode" )
				{
					$TdData .= $aryHeadResult["strcustomerreceivecode"];
				}

				// 伝票コード
				else if ( $strColumnName == "strSlipCode" )
				{
					$TdData .= $aryHeadResult["strslipcode"];
				}

				// 入力者
				else if ( $strColumnName == "lngInputUserCode" )
				{
					if ( $aryHeadResult["strinputuserdisplaycode"] )
					{
						$strText .= "[" . $aryHeadResult["strinputuserdisplaycode"] ."]";
					}
					else
					{
						$strText .= "     ";
					}
					$strText .= " " . $aryHeadResult["strinputuserdisplayname"];
					$TdData .= $strText;
				}

				// 顧客
				else if ( $strColumnName == "lngCustomerCode" )
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

				// 状態
				else if ( $strColumnName == "lngSalesStatusCode" )
				{
					$TdData .= $aryHeadResult["strsalesstatusname"];
				}

				// その他の項目はそのまま出力
				else
				{
					$strLowColumnName = strtolower($strColumnName);
					if ( $strLowColumnName == "strnote" )
					{
						$strText .= nl2br($aryHeadResult[$strLowColumnName]);
					}
					else if ( array_key_exists( $strLowColumnName , $aryDetailResult[$i] ) )
					{
						$strText .= $aryDetailResult[$i][$strLowColumnName];
					}
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
function fncSetSlipTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName )
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
// 2004.03.31 suzukaze update start
		// 詳細部
		else if ( $strColumnName == "strProductCode" 
			or $strColumnName == "lngInChargeGroupCode" or $strColumnName == "lngInChargeUserCode" // <-- added by siato
			or $strColumnName == "lngRecordNo"
			or $strColumnName == "lngSalesClassCode" or $strColumnName == "strGoodsCode" or $strColumnName == "dtmDeliveryDate"
			or $strColumnName == "curProductPrice" or $strColumnName == "lngProductUnitCode" or $strColumnName == "lngProductQuantity"
			or $strColumnName == "curSubTotalPrice" or $strColumnName == "lngTaxClassCode" or $strColumnName == "curTax"
			or $strColumnName == "curTaxPrice" or $strColumnName == "strDetailNote" 
			or $strColumnName == "strProductName" or $strColumnName == "strProductEnglishName" )
// 2004.03.31 suzukaze update end
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
		// 管理モードの場合　同じ売上コードの一覧を取得し表示する

		$strSalesCodeBase = $aryResult[$i]["strsalescode"];

		$strSameSalesCodeQuery = fncGetSearchSalesSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $strSalesCodeBase, $aryResult[$i]["lngsalesno"], FALSE );

		// 値をとる =====================================
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameSalesCodeQuery, $objDB );

		// 配列のクリア
		unset( $arySameSalesCodeResult );

		if ( $lngResultNum )
		{
			for ( $j = 0; $j < $lngResultNum; $j++ )
			{
				$arySameSalesCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
			}
			$lngSameSalesCount = $lngResultNum;
		}
		$objDB->freeResult( $lngResultID );

		// 同じ売上コードでの過去リバイズデータが存在すれば
		if ( $lngResultNum )
		{
			for ( $j = 0; $j < $lngSameSalesCount; $j++ )
			{
				// 検索結果部分の設定

				reset( $arySameSalesCodeResult[$j] );

				// 明細出力用の調査
				$lngDetailViewCount = count( $aryDetailViewColumn );

				if ( $lngDetailViewCount )
				{
					// 明細行数の調査
					$strDetailQuery = fncGetSalesToProductSQL ( $aryDetailViewColumn, $arySameSalesCodeResult[$j]["lngsalesno"], $aryData, $objDB );

					// クエリー実行
					if ( !$lngDetailResultID = $objDB->execute( $strDetailQuery ) )
					{
						$strMessage = fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../sc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
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
				if ( $arySameSalesCodeResult[0]["lngrevisionno"] < 0 )
				{
					$bytDeleteFlag = TRUE;
				}
				else
				{
					$bytDeleteFlag = FALSE;
				}

				// １レコード分の出力
				$aryHtml_add = fncSetSlipHeadTable ( $lngColumnCount, $arySameSalesCodeResult[$j], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameSalesCount, $j, $bytDeleteFlag );
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