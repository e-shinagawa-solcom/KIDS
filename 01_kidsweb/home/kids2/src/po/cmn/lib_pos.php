<?
// ----------------------------------------------------------------------------
/**
 *       発注管理  検索関連関数群
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
 *         ・検索結果関連の関数
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------


/**
 * 検索項目から一致する最新の発注データを取得するSQL文の作成関数
 *
 *	検索項目から SQL文を作成する
 *
 *	@param  Array 	$aryViewColumn 			表示対象カラム名の配列
 *	@param  Array 	$arySearchColumn 		検索対象カラム名の配列
 *	@param  Array 	$arySearchDataColumn 	検索内容の配列
 *	@param  Object	$objDB       			DBオブジェクト
 *	@param	String	$strOrderCode			発注コード	空白指定時:検索結果出力	発注コード指定時:管理用、同じ発注コードの一覧取得
 *	@param	Integer	$lngOrderNo				発注Ｎｏ	0:検索結果出力	発注Ｎｏ指定時:管理用、同じ発注コードとする時の対象外発注NO
 *	@param	Boolean	$bytAdminMode			有効な削除データの取得用フラグ	FALSE:検索結果出力	TRUE:管理用、削除データ取得
 *	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
 *	@access public
 */
 
 
function fncGetSearchPurchaseSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strOrderCode, $lngOrderNo, $bytAdminMode)
{

	// 表示用カラムに設定されている内容を検索用に文字列設定
	for ( $i = 0; $i < count($aryViewColumn); $i++ )
	{
		$strViewColumnName = $aryViewColumn[$i];

		// 表示項目　管理モードの過去リビジョンデータ、および、明細情報は検索結果より取得

		// 登録日
		if ( $strViewColumnName == "dtmInsertDate" )
		{
			$arySelectQuery[] = ", to_char( o.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmInsertDate";
		}

		// 計上日
		if ( $strViewColumnName == "dtmOrderAppDate" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", to_char( o.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmOrderAppDate";
		}

		// 発注Ｎｏ
		if ( $strViewColumnName == "strOrderCode" )
		{
			$arySelectQuery[] = ", o.strOrderCode || '_' || to_char(o.lngRevisionNo, 'FM00') as strOrderCode";
		}

		// 入力者
		if ( $strViewColumnName == "lngInputUserCode" )
		{
			$arySelectQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode";
			$arySelectQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName";
			$flgInputUser = TRUE;
		}

		// 仕入先
		if ( $strViewColumnName == "lngCustomerCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
			$arySelectQuery[] = ", cust_c.strCompanyDisplayName as strCustomerDisplayName";
			$flgCustomerCompany = TRUE;
		}
		// 状態
		if ( $strViewColumnName == "lngOrderStatusCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", o.lngOrderStatusCode as lngOrderStatusCode";
			$arySelectQuery[] = ", os.strOrderStatusName as strOrderStatusName";
			$flgOrderStatus = TRUE;
		}
		
		// ワークフロー状態
		if ( $strViewColumnName == "lngWorkFlowStatusCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", (select strWorkflowStatusName from m_WorkflowStatus where lngWorkflowStatusCode = tw.lngWorkflowStatusCode) as lngWorkFlowStatusCode";
			$flgWorkFlowStatus = TRUE;
		}
		

		// 支払条件
		if ( $strViewColumnName == "lngPayConditionCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", o.lngPayConditionCode as lngPayConditionCode";
			$arySelectQuery[] = ", pc.strPayConditionName as strPayConditionName";
			$flgPayCondition = TRUE;
		}

		// 発注有効期限日
		if ( $strViewColumnName == "dtmExpirationDate" and !$bytAdminMode )
		{
			// $arySelectQuery[] = ", to_char( o.dtmExpirationDate, 'YYYY/MM/DD' ) as dtmExpirationDate";
			$arySelectQuery[] = ", to_char( od.dtmExpirationDate, 'YYYY/MM/DD') as dtmExpirationDate";
		}

		// 備考
		if ( $strViewColumnName == "strNote" and !$bytAdminMode )
		{
			//$arySelectQuery[] = ", o.strNote as strNote";
		}

		// 合計金額
		if ( $strViewColumnName == "curTotalPrice" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", To_char( o.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
		}
	}

	//
	$arySelectQuery[] = ", mm.strMonetaryUnitSign as strMonetaryUnitSign";
	$flgMonetaryUnit = TRUE;


	// 条件の追加
	$detailFlag = FALSE;

	// 管理モードの検索時、同じ発注コードのデータを取得する場合
	if ( $strOrderCode or $bytAdminMode )
	{
		// 同じ発注コードに対して指定の発注番号のデータは除外する
		if ( $lngOrderNo )
		{
			$aryQuery[] = " WHERE o.bytInvalidFlag = FALSE AND o.strOrderCode = '" . $strOrderCode . "'";
		}
		else
		{
			fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}

		// 削除データ取得時は条件追加
		if ( $bytAdminMode )
		{
			$aryQuery[] = " AND o.lngRevisionNo < 0";
		}
	}

	// 管理モードでの同じ発注コードに対する検索モード以外の場合は検索条件を追加する
	else
	{
		// 絶対条件 無効フラグが設定されておらず、最新発注のみ
		$aryQuery[] = " WHERE o.bytInvalidFlag = FALSE AND o.lngRevisionNo >= 0";

		// 表示用カラムに設定されている内容を検索用に文字列設定
		for ( $i = 0; $i < count($arySearchColumn); $i++ )
		{
			$strSearchColumnName = $arySearchColumn[$i];

			// ////発注マスタ内の検索条件////
			// 登録日
			if ( $strSearchColumnName == "dtmInsertDate" )
			{
				if ( $arySearchDataColumn["dtmInsertDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmInsertDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND o.dtmInsertDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmInsertDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmInsertDateTo"] . " 23:59:59";
					$aryQuery[] = " AND o.dtmInsertDate <= '" . $dtmSearchDate . "'";
				}
			}
			// 計上日
			if ( $strSearchColumnName == "dtmOrderAppDate" )
			{
				if ( $arySearchDataColumn["dtmOrderAppDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmOrderAppDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND o.dtmAppropriationDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmOrderAppDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmOrderAppDateTo"] . " 23:59:59";
					$aryQuery[] = " AND o.dtmAppropriationDate <= '" . $dtmSearchDate . "'";
				}
			}
			// 発注Ｎｏ
			if ( $strSearchColumnName == "strOrderCode" )
			{
				if ( $arySearchDataColumn["strOrderCodeFrom"] )
				{
					if ( strpos($arySearchDataColumn["strOrderCodeFrom"], "-") )
					{
						// リバイズコード付の発注Ｎｏのリバイズコードは検索結果では最新版を表示するため、無視する
						$strNewOrderCode_from = preg_replace( strstr( $arySearchDataColumn["strOrderCodeFrom"], "_" ), "", $arySearchDataColumn["strOrderCodeFrom"] );
					}
					else
					{
						$strNewOrderCode_from = $arySearchDataColumn["strOrderCodeFrom"];
					}
//					$aryQuery[] = " AND o.strOrderCode >= '" . $strNewOrderCode_from . "'";

				}
				if ( $arySearchDataColumn["strOrderCodeTo"] )
				{
					if ( strpos($arySearchDataColumn["strOrderCodeTo"], "-") )
					{
						// リバイズコード付の発注Ｎｏのリバイズコードは検索結果では最新版を表示するため、無視する
						$strNewOrderCode_to = preg_replace( strstr( $arySearchDataColumn["strOrderCodeTo"], "_" ), "", $arySearchDataColumn["strOrderCodeTo"] );
					}
					else
					{
						$strNewOrderCode_to = $arySearchDataColumn["strOrderCodeTo"];
					}
//					$aryQuery[] = " AND o.strOrderCode <= '" . $strNewOrderCode_to . "'";
				}
				if( ( $strNewOrderCode_from && $strNewOrderCode_to ) && ( $strNewOrderCode_from == $strNewOrderCode_to ) )
				{
					// fromとtoが同じ値の場合は、範囲指定ではなく"="で指定"
					$aryQuery[] = " AND o.strOrderCode = '" . $strNewOrderCode_to . "'";
				}
				else
				{
					if( $strNewOrderCode_from )
					{
						$aryQuery[] = " AND o.strOrderCode >= '" . $strNewOrderCode_from . "'";
					}
					if( $strNewOrderCode_to )
					{
						$aryQuery[] = " AND o.strOrderCode <= '" . $strNewOrderCode_to . "'";
					}
				}
			}
			// 入力者
			if ( $strSearchColumnName == "lngInputUserCode" )
			{
				if ( $arySearchDataColumn["lngInputUserCode"] )
				{
					$aryQuery[] = " AND input_u.strUserDisplayCode ~* '" . $arySearchDataColumn["lngInputUserCode"] . "'";
					$flgInputUser = TRUE;
				}
				if ( $arySearchDataColumn["strInputUserName"] )
				{
					$aryQuery[] = " AND UPPER( input_u.strUserDisplayName ) LIKE UPPER( '%" . $arySearchDataColumn["strInputUserName"] . "%' )";
					$flgInputUser = TRUE;
				}
			}
			// 仕入先
			if ( $strSearchColumnName == "lngCustomerCode" )
			{
				if ( $arySearchDataColumn["lngCustomerCode"] )
				{
					$aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngCustomerCode"] . "'";
					$flgCustomerCompany = TRUE;
				}
				if ( $arySearchDataColumn["strCustomerName"] )
				{
					$aryQuery[] = " AND UPPER( cust_c.strCompanyDisplayName ) LIKE UPPER( '%" . $arySearchDataColumn["strCustomerName"] . "%' )";
					$flgCustomerCompany = TRUE;
				}
			}
			
			
			// 部門
			if ( $strSearchColumnName == "lngInChargeGroupCode" )
			{
				if ( $arySearchDataColumn["lngInChargeGroupCode"] )
				{
					$aryQuery[] = " AND mg.strGroupDisplayCode = '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'";
					$flgGroup = TRUE;
				}
				if ( $arySearchDataColumn["strInChargeGroupName"] )
				{
					$aryQuery[] = " AND UPPER(mg.strGroupDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeGroupName"] . "%')";
					$flgGroup = TRUE;
				}
			}
			// 担当者
			if ( $strSearchColumnName == "lngInChargeUserCode" )
			{
				if ( $arySearchDataColumn["lngInChargeUserCode"] )
				{
					$aryQuery[] = " AND mu.strUserDisplayCode = '" . $arySearchDataColumn["lngInChargeUserCode"] . "'";
					$flgUser = TRUE;
				}
				if ( $arySearchDataColumn["strInChargeUserName"] )
				{
					$aryQuery[] = " AND UPPER(mu.strUserDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeUserName"] . "%')";
					$flgUser = TRUE;
				}
			}
			// 状態
			if ( $strSearchColumnName == "lngOrderStatusCode" )
			{
				if ( $arySearchDataColumn["lngOrderStatusCode"] )
				{
					// 発注状態は ","区切りの文字列として渡される
					//$arySearchStatus = explode( ",", $arySearchDataColumn["lngOrderStatusCode"] );
					// チェックボックス化により、配列をそのまま代入
					$arySearchStatus = $arySearchDataColumn["lngOrderStatusCode"];

					if ( is_array( $arySearchStatus ) )
					{
						$aryQuery[] = " AND ( ";
						// 発注状態は複数設定されている可能性があるので、設定個数分ループ
						for ( $j = 0; $j < count($arySearchStatus); $j++ )
						{
							// 初回処理
							if ( $j <> 0 )
							{
								$aryQuery[] = " OR ";
							}
							$aryQuery[] = "o.lngOrderStatusCode = " . $arySearchStatus[$j] . "";
						}
						$aryQuery[] = " ) ";
					}
				}
			}
/*
			// ワークフロー状態
			if ( $strSearchColumnName == "lngWorkFlowStatusCode" )
			{
				if ( $arySearchDataColumn["lngWorkFlowStatusCode"] )
				{
					// チェックボックス値より、配列をそのまま代入
					$arySearchStatus = $arySearchDataColumn["lngWorkFlowStatusCode"];
					
					if ( is_array( $arySearchStatus ) )
					{
						$aryQuery[] = " AND tw.lngworkflowstatuscode in ( ";

						// WF状態は複数設定されている可能性があるので、設定個数分ループ
						$strBuff = "";
						for ( $j = 0; $j < count($arySearchStatus); $j++ )
						{
							// 初回処理
							if ( $j <> 0 )
							{
								$strBuff .= " ,";
							}
							$strBuff .= "" . $arySearchStatus[$j] . "";
						}
						$aryQuery[] = "\t".$strBuff . " )";
					}
					
					$flgWorkFlowStatus = true;
				}
			}
*/
			// 支払条件
			if ( $strSearchColumnName == "lngPayConditionCode" )
			{
				if ( $arySearchDataColumn["lngPayConditionCode"] )
				{
					$aryQuery[] = " AND o.lngPayConditionCode = " . $arySearchDataColumn["lngPayConditionCode"] . "";
				}
			}
			// 発注有効期限日
			if ( $strSearchColumnName == "dtmExpirationDate" )
			{
				if ( $arySearchDataColumn["dtmExpirationDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmExpirationDateFrom"] . " 00:00:00";
					// $aryQuery[] = " AND o.dtmExpirationDate >= '" . $dtmSearchDate . "'";
					$aryQuery[] = " AND od.dtmExpirationDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmExpirationDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmExpirationDateTo"] . " 23:59:59";
					// $aryQuery[] = " AND o.dtmExpirationDate <= '" . $dtmSearchDate . "'";
					$aryQuery[] = " AND od.dtmExpirationDate <= '" . $dtmSearchDate . "'";
				}
			}


			//
			// 明細テーブルの条件
			//
			
			// 製品コード
			if ( $strSearchColumnName == "strProductCode" )
			{
			    if ( ( $arySearchDataColumn["strProductCodeFrom"] && $arySearchDataColumn["strProductCodeTo"] )
			     && ( $arySearchDataColumn["strProductCodeFrom"] == $arySearchDataColumn["strProductCodeTo"] ) )
			    {
					$aryDetailWhereQuery[] = "AND ";
					$aryDetailWhereQuery[] = "od1.strProductCode = '" . $arySearchDataColumn["strProductCodeFrom"] . "' ";
					$detailFlag = TRUE;
			    }
			    else
			    {
					if ( $arySearchDataColumn["strProductCodeFrom"] )
					{
						$aryDetailWhereQuery[] = "AND ";
						$aryDetailWhereQuery[] = "od1.strProductCode >= '" . $arySearchDataColumn["strProductCodeFrom"] . "' ";
						$detailFlag = TRUE;
					}
					if ( $arySearchDataColumn["strProductCodeTo"] )
					{
						$aryDetailWhereQuery[] = "AND ";
						$aryDetailWhereQuery[] = "od1.strProductCode <= '" . $arySearchDataColumn["strProductCodeTo"] . "' ";
						$detailFlag = TRUE;
					}
				}
			}
			// 製品名称（日本語）
			if ( $strSearchColumnName == "strProductName" )
			{
				if ( $arySearchDataColumn["strProductName"] )
				{
					$aryDetailWhereQuery[] = "AND ";
					$aryDetailWhereQuery[] = "UPPER( p.strProductName ) LIKE UPPER( '%" . $arySearchDataColumn["strProductName"] . "%' ) ";
					$detailFlag = TRUE;
				}
			}
			// 製品名称（英語）
			if ( $strSearchColumnName == "strProductEnglishName" )
			{
				if ( $arySearchDataColumn["strProductEnglishName"] )
				{
					$aryDetailWhereQuery[] = "AND ";
					$aryDetailWhereQuery[] = "UPPER( p.strProductEnglishName ) LIKE UPPER( '%" . $arySearchDataColumn["strProductEnglishName"] . "%' ) ";
					$detailFlag = TRUE;
				}
			}

			// 仕入科目
			if ( $strSearchColumnName == "lngStockSubjectCode" )
			{
				if ( $arySearchDataColumn["lngStockSubjectCode"] )
				{
					$aryDetailWhereQuery[] = "AND ";
					$aryDetailWhereQuery[] = "od1.lngStockSubjectCode = " . $arySearchDataColumn["lngStockSubjectCode"] . " ";
					$detailFlag = TRUE;
					$StockSubjectFlag = TRUE;
				}
			}
			// 仕入部品
			if ( $strSearchColumnName == "lngStockItemCode" )
			{
				if ( $arySearchDataColumn["lngStockItemCode"] )
				{
					$aryDetailWhereQuery[] = "AND ";
					$aryDetailWhereQuery[] = "od1.lngStockItemCode = " . $arySearchDataColumn["lngStockItemCode"] . " ";
					if ( $StockSubjectFlag != TRUE )
					{
						$aryDetailWhereQuery[] = "AND od1.lngStockSubjectCode = " . $arySearchDataColumn["lngStockSubjectCode"] . " ";
					}
					$detailFlag = TRUE;
				}
			}

			// 納期
			if ( $strSearchColumnName == "dtmDeliveryDate" )
			{
				if ( $arySearchDataColumn["dtmDeliveryDateFrom"] )
				{
					$aryDetailWhereQuery[] = "AND ";
					$aryDetailWhereQuery[] = "od1.dtmDeliveryDate >= '" . $arySearchDataColumn["dtmDeliveryDateFrom"] . "' ";
					$detailFlag = TRUE;
				}
				if ( $arySearchDataColumn["dtmDeliveryDateTo"] )
				{
					$aryDetailWhereQuery[] = "AND ";
					$aryDetailWhereQuery[] = "od1.dtmDeliveryDate <= '" . $arySearchDataColumn["dtmDeliveryDateTo"] . "' ";
					$detailFlag = TRUE;
				}
			}
		}
	}

	// 明細行の検索対応
/*
	$aryDetailWhereQuery[] = ") as od on od.lngOrderNo = o.lngOrderNo and od.lngrevisionno = o.lngrevisionno";
	// where句（明細行） クエリー連結
	$strDetailQuery = "\n";
//	$strDetailQuery = implode("\n", $aryDetailFrom) . "\n";
	// 明細行の検索対応
	if ( $detailFlag )
	{
		$strDetailQuery .= implode("\n", $aryDetailTargetQuery) . "\n";
	}
	$strDetailQuery .= implode("\n", $aryDetailWhereQuery) . "\n";
*/

	// SQL文の作成
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT o.lngOrderNo as lngOrderNo";
	$aryOutQuery[] = "	,o.lngRevisionNo as lngRevisionNo";
	$aryOutQuery[] = "	,o.strOrderCode as strOrderCode";
	$aryOutQuery[] = "	,o.lngRevisionNo as lngRevisionNo";
	$aryOutQuery[] = "	,o.lngOrderStatusCode as lngOrderStatusCode";

	// 明細行の 'order by' 用に追加
	$aryOutQuery[] = "	,od.lngOrderDetailNo";

	// select句 クエリー連結
	$aryOutQuery[] = implode("\n", $arySelectQuery);

	// From句 の生成
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Order o";
	if ( !$strOrderCode )
	{
	    $aryFromQuery[] = " INNER JOIN (";
	    $aryFromQuery[] = " select strordercode, MAX(lngrevisionno) as lngrevisionno from m_order group by strordercode ) rev";
	    $aryFromQuery[] = "on rev.strordercode = o.strordercode and rev.lngrevisionno = o.lngrevisionno ";
	}
	// 明細検索用テーブル結合条件
//	$aryDetailFrom = array();
	$aryFromQuery[] = "INNER JOIN  (SELECT od1.lngOrderNo";
	$aryFromQuery[] = "	,od1.lngOrderDetailNo";
	$aryFromQuery[] = "	,od1.lngRevisionNo";
	$aryFromQuery[] = "    ,mp.dtmexpirationdate";
	$aryFromQuery[] = "	FROM t_OrderDetail od1";
	$aryFromQuery[] = "	INNER JOIN (";
	$aryFromQuery[] = "	    select m_product.*";
	$aryFromQuery[] = "		from m_product";
	$aryFromQuery[] = "		inner join (";
	$aryFromQuery[] = "			select";
	$aryFromQuery[] = "				strProductCode";
	$aryFromQuery[] = "				,strrevisecode";
	$aryFromQuery[] = "				,MAX(lngrevisionno) as lngrevisionno";
	$aryFromQuery[] = "			from m_product";
	$aryFromQuery[] = "			group by strProductCode, strrevisecode";
	$aryFromQuery[] = "		) a";
	$aryFromQuery[] = "			on a.strProductCode = m_product.strproductcode";
	$aryFromQuery[] = "			and a.strrevisecode = m_product.strrevisecode";
	$aryFromQuery[] = "			and a.lngrevisionno = m_product.lngrevisionno";
	$aryFromQuery[] = "	) p ON od1.strProductCode = p.strProductCode and p.strrevisecode = od1.strrevisecode";

//	$aryFromQuery[] = "	LEFT JOIN m_Product p ON od1.strProductCode = p.strProductCode";
	$aryFromQuery[] = "	left join t_purchaseorderdetail tp on  od1.lngorderno = tp.lngorderno and od1.lngorderdetailno = tp.lngorderdetailno and od1.lngrevisionno = tp.lngrevisionno";
	$aryFromQuery[] = "	left join m_purchaseorder mp on  tp.lngpurchaseorderno = mp.lngpurchaseorderno and tp.lngrevisionno = mp.lngrevisionno";
	$aryFromQuery[] = "	where mp.lngpurchaseorderno not in (select lngpurchaseorderno from m_purchaseorder where lngrevisionno < 0) ";
	$aryDetailWhereQuery[] = ") as od on od.lngOrderNo = o.lngOrderNo and od.lngrevisionno = o.lngrevisionno";
	// where句（明細行） クエリー連結
	$aryFromQuery[]  = "\n";
//	$strDetailQuery = implode("\n", $aryDetailFrom) . "\n";
	// 明細行の検索対応
	// if ( $detailFlag )
	// {
	// 	$aryFromQuery[] = implode("\n", $aryDetailTargetQuery) . "\n";
	// }
	$aryFromQuery[] = implode("\n", $aryDetailWhereQuery) . "\n";


	
	// 追加表示用の参照マスタ対応
	if ( $flgInputUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User input_u ON o.lngInputUserCode = input_u.lngUserCode";
	}
	if ( $flgCustomerCompany )
	{
		$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON o.lngCustomerCompanyCode = cust_c.lngCompanyCode";
	}
	
	if ( $flgGroup )
	{
		$aryFromQuery[] = " LEFT JOIN m_group mg ON o.lnggroupcode = mg.lnggroupcode";
	}
	
	if ( $flgUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User mu ON o.lngUserCode = mu.lngUserCode";
	}
	if ( $flgOrderStatus )
	{
		$aryFromQuery[] = " LEFT JOIN m_OrderStatus os USING (lngOrderStatusCode)";
	}
	if ( $flgPayCondition )
	{
		$aryFromQuery[] = " LEFT JOIN m_PayCondition pc ON o.lngPayConditionCode = pc.lngPayConditionCode";
	}
	if ( $flgMonetaryUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mm ON o.lngMonetaryUnitCode = mm.lngMonetaryUnitCode";
	}
	// if ( $flgWorkFlowStatus )
	// {
	// 	$aryFromQuery[] = " left join
	// 	( m_workflow mw
	// 		left join t_workflow tw
	// 		on mw.lngworkflowcode = tw.lngworkflowcode
	// 		and tw.lngworkflowsubcode = (select max(lngworkflowsubcode) from t_workflow where lngworkflowcode = tw.lngworkflowcode)
	// 	) on  mw.strworkflowkeycode = trim(to_char(o.lngOrderNo, '9999999'))
	// 		and mw.lngfunctioncode = " . DEF_FUNCTION_PO1; // 発注登録時のWFデータを対象にする為に条件指定
		
	// 	$aryFromQuery[] = "
	// 	 AND o.bytInvalidFlag = FALSE AND o.lngRevisionNo >= 0
	// 	 AND o.lngRevisionNo = ( SELECT MAX( o1.lngRevisionNo ) FROM m_Order o1 WHERE o1.strOrderCode = o.strOrderCode AND o1.bytInvalidFlag = false )
	// 	 AND o.strReviseCode = ( SELECT MAX( o2.strReviseCode ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode AND o2.bytInvalidFlag = false )
	// 	 AND 0 <= ( SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode )";
		
	// }
	
	// From句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryFromQuery);

	// Where句 クエリー連結
	$aryOutQuery[] = $strDetailQuery;
	
	// Where句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryQuery);

	// 明細行条件があった場合の 条件連結
//	$aryOutQuery[] = " AND od.lngOrderNo = o.lngOrderNo";


	/////////////////////////////////////////////////////////////
	//// 最新発注（リビジョン番号が最大、リバイズ番号が最大、////
	//// かつリビジョン番号負の値で無効フラグがFALSEの       ////
	//// 同じ発注コードを持つデータが無い発注データ          ////
	/////////////////////////////////////////////////////////////
	// 発注コードが指定されていない場合は検索条件を設定する
	if ( !$strOrderCode )
	{
//		$aryOutQuery[] = " AND o.lngRevisionNo = ( "
//			. "SELECT MAX( o1.lngRevisionNo ) FROM m_Order o1 WHERE o1.strOrderCode = o.strOrderCode AND o1.bytInvalidFlag = false )";
		// $aryOutQuery[] = " AND o.strReviseCode = ( "
		// 	. "SELECT MAX( o2.strReviseCode ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode AND o2.bytInvalidFlag = false )";

		// 管理モードの場合は削除データも検索対象とするため以下の条件は対象外
		if ( !$arySearchDataColumn["Admin"] )
		{
//			$aryOutQuery[] = " AND 0 <= ( "
//				. "SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode )";
            $aryOutQuery[] = " AND o.lngorderno not in (select lngorderno from m_Order where bytInvalidFlag and lngrevisionno < 0 )"; 

		}
	}
	// 管理モードの検索時、同じ発注コードのデータを取得する場合
	if ( $strOrderCode or $bytAdminMode )
	{
		$aryOutQuery[] = " ORDER BY o.dtmInsertDate DESC";
	}
	else
	{
		// ソート条件設定
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
			case "strOrderCode":
			case "lngOrderStatusCode":
			case "lngPayConditionCode":
			case "dtmExpirationDate":
			case "strNote":
			case "curTotalPrice":
				$aryOutQuery[] = " ORDER BY o." . $arySearchDataColumn["strSort"] . " " . $strAsDs . ", o.lngOrderNo DESC";
				break;
			case "dtmAppropriationDate":
				$aryOutQuery[] = " ORDER BY dtmOrderAppDate" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngInputUserCode":
				$aryOutQuery[] = " ORDER BY strInputUserDisplayCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngCustomerCompanyCode":
				$aryOutQuery[] = " ORDER BY strCustomerDisplayCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngWorkFlowStatusCode":
				$aryOutQuery[] = " ORDER BY lngWorkFlowStatusCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngOrderDetailNo":	// 明細行番号
				$aryOutQuery[] = " ORDER BY od.lngOrderDetailNo" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "strProductCode":		// 製品コード
				$aryOutQuery[] = " ORDER BY od.strProductCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngGroupCode":		// 部門
				$aryOutQuery[] = " ORDER BY od.strGroupDisplayCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngUserCode":			// 担当者
				$aryOutQuery[] = " ORDER BY od.strUserDisplayCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "strProductName":		// 製品名称
				$aryOutQuery[] = " ORDER BY od.strProductName" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "strProductEnglishName":	// 製品英語名称
				$aryOutQuery[] = " ORDER BY od.strProductEnglishName" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngStockSubjectCode":	// 仕入科目
				$aryOutQuery[] = " ORDER BY od.lngStockSubjectCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngStockItemCode":	// 仕入部品
				$aryOutQuery[] = " ORDER BY od.lngStockItemCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "strMoldNo":			// 金型No.
				$aryOutQuery[] = " ORDER BY od.strMoldNo" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "strGoodsCode":		// 顧客品番
				$aryOutQuery[] = " ORDER BY od.strGoodsCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngDeliveryMethodCode":// 運搬方法
				$aryOutQuery[] = " ORDER BY od.lngDeliveryMethodCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "dtmDeliveryDate":		// 納期
				$aryOutQuery[] = " ORDER BY od.dtmDeliveryDate" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "curProductPrice":		// 単価
				$aryOutQuery[] = " ORDER BY od.curProductPrice" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngProductUnitCode":	// 単位
				$aryOutQuery[] = " ORDER BY od.lngProductUnitCode" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "lngProductQuantity":	// 数量
				$aryOutQuery[] = " ORDER BY od.lngProductQuantity" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "curSubTotalPrice":	// 税抜金額
				$aryOutQuery[] = " ORDER BY od.curSubTotalPrice" . $strAsDs . ", lngOrderNo DESC";
				break;
			case "strDetailNote":		// 明細備考
				$aryOutQuery[] = " ORDER BY od.strNote" . $strAsDs . ", lngOrderNo DESC";
				break;
			default:
				$aryOutQuery[] = " ORDER BY o.lngOrderNo DESC";
		}
	}
	return implode("\n", $aryOutQuery);
}

/**
 * 検索項目から一致する最新の発注書データを取得するSQL文の作成関数
 *
 *	検索項目から SQL文を作成する
 *
 *	@param  Array 	$aryViewColumn 			表示対象カラム名の配列
 *	@param  Array 	$arySearchColumn 		検索対象カラム名の配列
 *	@param  Array 	$arySearchDataColumn 	検索内容の配列
 *	@param  Object	$objDB       			DBオブジェクト
 *	@param	String	$strOrderCode			発注コード	空白指定時:検索結果出力	発注コード指定時:管理用、同じ発注コードの一覧取得
 *	@param	Integer	$lngOrderNo				発注Ｎｏ	0:検索結果出力	発注Ｎｏ指定時:管理用、同じ発注コードとする時の対象外発注NO
 *	@param	Boolean	$bytAdminMode			有効な削除データの取得用フラグ	FALSE:検索結果出力	TRUE:管理用、削除データ取得
 *	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
 *	@access public
 */
function fncGetSearchPurcheseOrderSQL( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strOrderCode, $lngOrderNo, $bytAdminMode ){
	// 表示用カラムに設定されている内容を検索用に文字列設定
	for($i = 0; $i < count($aryViewColumn); $i++){
		$strViewColumnName = $aryViewColumn[$i];

		// 登録日
		if($strViewColumnName == "dtmInsertDate"){
			$arySelectQuery[] = "  ,to_char(mp.dtminsertdate, 'YYYY/MM/DD') as dtmInsertDate";
		}

		// 入力者
		if($strViewColumnName == "lngInputUserCode"){
			$arySelectQuery[] = "  ,input_user.struserdisplaycode AS lngInsertUserCode";
			$arySelectQuery[] = "  ,mp.strinsertusername AS strInsertUserName";
		}

		// 発注有効期限日
		if($strViewColumnName == "dtmExpirationDate"){
			$arySelectQuery[] = "  ,to_char(mp.dtmexpirationdate, 'YYYY/MM/DD') as dtmExpirationDate";
		}
		
		// 発注NO.
		if($strViewColumnName == "strOrderCode"){
			$arySelectQuery[] = "  ,mp.strordercode as strOrderCode";
		}

		// 製品
		if($strViewColumnName == "strProductCode"){
			$arySelectQuery[] = "  ,mp.strproductcode as strProductCode";
			$arySelectQuery[] = "  ,mp.strproductname as strProductName";
			$arySelectQuery[] = "  ,mp.strproductenglishname as strProductEnglishName";
		}

		// 営業部署
		if($strViewColumnName == "lngInChargeGroupCode"){
			$arySelectQuery[] = "  ,mg.strgroupdisplaycode AS lngGroupCode";
			$arySelectQuery[] = "  ,mp.strgroupname as strGroupName";
		}

		// 開発担当者
		if($strViewColumnName == "lngInChargeUserCode"){
			$arySelectQuery[] = "  ,mu.struserdisplaycode as lngUserCode";
			$arySelectQuery[] = "  ,mp.strusername as strUserName";
		}

		// 仕入先
		if($strViewColumnName == "lngCustomerCode"){
			$arySelectQuery[] = "  ,mc_stock.strcompanydisplaycode as lngCustomerCode";
			$arySelectQuery[] = "  ,mp.strcustomername as strCustomerName";
		}

		// 納品場所
		if($strViewColumnName == "lngDeliveryPlaceCode"){
			$arySelectQuery[] = "  ,mp.strdeliveryplacename as strDeliveryPlaceName";
		}

		// 通貨
		if($strViewColumnName == "lngMonetaryunitCode" or $strViewColumnName == "curTotalPrice"){
			$arySelectQuery[] = "  ,mp.lngmonetaryunitcode as lngMonetaryUnitCode";
			$arySelectQuery[] = "  ,mp.strmonetaryunitsign as strMonetaryUnitSign";
		}

		// 通貨レート
		if($strViewColumnName == "lngMonetaryRateCode"){
			$arySelectQuery[] = "  ,mp.lngmonetaryratecode as lngMonetaryRateCode";
			$arySelectQuery[] = "  ,mp.strmonetaryratename as strMonetaryRateName";
		}

		// 支払条件
		if($strViewColumnName == "lngPayConditionCode"){
			$arySelectQuery[] = "  ,mp.lngpayconditioncode as lngPayConditionCode";
			$arySelectQuery[] = "  ,mp.strpayconditionname as strPayConditionName";
		}

		// 合計金額
		if($strViewColumnName == "curTotalPrice"){
			$arySelectQuery[] = "  ,mp.curtotalprice as curTotalPrice";
		}

		// 備考
		if($strViewColumnName == "strNote"){
			$arySelectQuery[] = "  ,mp.strnote as strNote";
		}

		// 印刷回数
		if($strViewColumnName == "lngPrintCount"){
			$arySelectQuery[] = "  ,mp.lngprintcount as lngPrintCount";
		}
	}

	$aryQuery[] = "WHERE mp.lngpurchaseorderno >= 0";
	// 検索用カラムに設定されている内容を検索条件に文字列設定
	for($i = 0; $i < count($arySearchColumn); $i++){
		$strSearchColumnName = $arySearchColumn[$i];

		// 発注書マスタの検索条件
		// 発注日
		if($strSearchColumnName == "dtmInsertDate"){
			if($arySearchDataColumn["dtmInsertDateFrom"]){
				$dtmSearchDate = $arySearchDataColumn["dtmInsertDateFrom"] . " 00:00:00";
				$aryQuery[] = "AND   mp.dtminsertdate >= '" . $dtmSearchDate . "'";
			}
			if($arySearchDataColumn["dtmInsertDataTo"]){
				$dtmSearchDate = $arySearchDataColumn["dtmInsertDateTo"] . " 23:59:59";
				$aryQuery[] = "AND   mp.dtminsertdate <= '" . $dtmSearchDate . "'";
			}
		}

		// 入力者
		if($strSearchColumnName == "lngInputUserCode"){
			if($arySearchDataColumn["lngInputUserCode"]){
//				$aryQuery[] = "AND   mp.lnginsertusercode ~* '" . $arySearchDataColumn["lngInputUserCode"] . "'";
				$aryQuery[] = "AND   input_user.struserdisplaycode = '" . $arySearchDataColumn["lngInputUserCode"] . "'";
			}
//			if($arySearchDataColumn["strInputUserName"]){
//				$aryQuery[] = "AND   UPPER(mp.strinsertusername) LIKE UPPER('%" . $arySearchDataColumn["strInputUserName"] . "%')";
//			}
		}

		// 発注有効期限
		if($arySearchColumnName == "dtmExpirationDate"){
			if($arySearchDataColumn["dtmExpirationDateFrom"]){
				$dtmSearchDate = $arySearchDataColumn["dtmExpirationDateFrom"] . " 00:00:00";
				$aryQuery[] = "AND   mp.dtmexpirationdate >= '" . $dtmSearchDate . "'";
			}
			if($arySearchDataColumn["dtmExpirationDateTo"]){
				$dtmSearchDate = $arySearchDataColumn["dtmExpirationDateTo"] . " 23:59:59";
				$aryQuery[] = "AND   mp.dtmexpirationdate <= '" . $dtmSearchDate . "'";
			}
		}

		// 発注NO.
		if($strSearchColumnName == "strOrderCode"){
			$aryQuery[] = "AND   mp.strordercode = '" . $arySearchDataColumn["strOrderCode"] . "'";
		}

		// 製品
		if($strSearchColumnName == "strProductCode"){
			if($arySearchDataColumn["strProductCode"]){
				$aryQuery[] = "AND   mp.strProductCode = '" . $arySearchDataColumn["strProductCode"] . "'";
			}
//			if($arySearchDataColumn["strProductName"]){
//				$aryQuery[] = "AND   UPPER(mp.strproductname) LIKE UPPER('%" . $arySearchDataColumn["strProductName"] . "%')";
//			}
		}

		// 営業部署
		if($strSearchColumnName == "lngInChargeGroupCode"){
			if($arySearchDataColumn["lngInChargeGroupCode"]){
//				$aryQuery[] = "AND   mp.lnggroupcode = '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'";
				$aryQuery[] = "AND   mg.strgroupdisplaycode = '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'";
			}
//			if($arySearchDataColumn["strInChargeGroupName"]){
//				$aryQuery[] = "AND   UPPER(mp.strgroupname) LIKE UPPER('%" . $arySearchDataColumn["strInChargeGroupName"] . "%')";
//			}
		}

		// 開発担当者
		if($strSearchColumnName == "lngInChargeUserCode"){
			if($arySearchDataColumn["lngInChargeUserCode"]){
//				$aryQuery[] = "AND   mp.lngusercode = '" . $arySearchDataColumn["lngInChargeUserCode"] . "'";
				$aryQuery[] = "AND   mu.struserdisplaycode = '" . $arySearchDataColumn["lngInChargeUserCode"] . "'";
			}
//			if($arySearchDataColumn["strInChargeUserName"]){
//				$aryQuery[] = "AND   UPPER(mp.strusername) LIKE UPPER('%" . $arySearchDataColumn["strInChargeUserName"] . "%')";
//			}
		}

		// 仕入先
		if($strSearchColumnName == "lngCustomerCode"){
			if($arySearchDataColumn["lngCustomerCode"]){
//				$aryQuery[] = "AND   mp.lngcustomercode = '" . $arySearchDataColumn["lngCustomerCode"] . "'";
				$aryQuery[] = "AND   mc_stock.strcompanydisplaycode = '" . $arySearchDataColumn["lngCustomerCode"] . "'";
			}
//			if($arySearchDataColumn["strCustomerName"]){
//				$aryQuery[] = "AND   UPPER(mp.strcustomername) LIKE UPPER('%" . $arySearchDataColumn["strCustomerName"] . "%')";
//			}
		}

		// 納品場所
		if($strSearchColumnName == "lngDeliveryPlaceCode"){
			if($arySearchDataColumn["lngDeliveryPlaceCode"]){
//				$aryQuery[] = "AND   mp.lngdeliveryplacecode = '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
				$aryQuery[] = "AND   mc_delivary.strcompanydisplaycode = '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
			}
//			if($arySearchDataColumn["strDeliveryPlaceName"]){
//				$aryQuery[] = "AND   UPPER(mp.strdeliveryplacename) LIKE UPPER('%" . $arySearchDataColumn["strDeliveryPlaceName"] . "%')";
//			}
		}

		// 通貨
		if($strSearchColumnName == "lngMonetaryunitCode"){
			$aryQuery[] = "AND   mp.lngmonetaryunitcode = " . $arySearchDataColumn["lngMonetaryunitCode"];
		}

		// 通貨レート
		if($strSearchColumnName == "lngMonetaryRateCode"){
			$aryQuery[] = "AND   mp.lngmonetaryratecode = " . $arySearchDataColumn["lngMonetaryRateCode"];
		}

		// 支払条件
		if($strSearchColumnName == "lngPayConditionCode"){
			$aryQuery[] = "AND   mp.lngpayconditioncode = " . $arySearchDataColumn["lngPayConditionCode"];
		}
	}

	// SQL作成
	$aryOutQuery[] = "SELECT";
	$aryOutQuery[] = "   mp.lngpurchaseorderno as lngPurchaseOrderNo";
	$aryOutQuery[] = "  ,mp.lngrevisionno as lngRevisionNo";
	$aryOutQuery[] = "  ,mp.strrevisecode as strReviseCode";
	$aryOutQuery[] = implode("\n", $arySelectQuery);
	$aryOutQuery[] = "FROM m_purchaseorder mp";
	$aryOutQuery[] = "inner join m_user input_user on input_user.lngusercode = mp.lnginsertusercode";
	$aryOutQuery[] = "inner join m_group mg on mg.lnggroupcode = mp.lnggroupcode";
	$aryOutQuery[] = "inner join m_user mu on mu.lngusercode = mp.lngusercode";
	$aryOutQuery[] = "inner join m_company mc_stock on mc_stock.lngcompanycode = mp.lngcustomercode";
	$aryOutQuery[] = "inner join m_company mc_delivary on mc_delivary.lngcompanycode = mp.lngdeliveryplacecode";
	$aryOutQuery[] = implode("\n", $aryQuery);
	$aryOutQuery[] = "ORDER BY";
	$aryOutQuery[] = "   mp.lngpurchaseorderno";
	$aryOutQuery[] = "  ,mp.lngrevisionno DESC";
	$aryOutQuery[] = "";

	switch($arySearchDataColumn["strSort"]){

	}

	return implode("\n", $aryOutQuery);
}

/**
 * 対応する発注NOのデータに対する明細行を取得するSQL文の作成関数
 *
 *	発注NOから明細を取得する SQL文を作成する
 *
 *	@param  Array 	$aryDetailViewColumn 	表示対象明細カラム名の配列
 *	@param  String 	$lngOrderNo 			対象発注NO
 *	@param  Array 	$aryData 				POSTデータの配列
 *	@param  Object	$objDB       			DBオブジェクト
 *	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
 *	@access public
 */
function fncGetOrderToProductSQL ( $aryDetailViewColumn, $lngOrderNo, $lngRevisionNo, $aryData, $objDB )
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
			$arySelectQuery[] = ", od.strProductCode || '_' || od.strReviseCode  as strProductCode";
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

		// 仕入科目
		if ( $strViewColumnName == "lngStockSubjectCode" )
		{
			$arySelectQuery[] = ", od.lngStockSubjectCode as lngStockSubjectCode";
			$arySelectQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
			$flgStockSubject = TRUE;
		}

		// 仕入部品
		if ( $strViewColumnName == "lngStockItemCode" )
		{
			$arySelectQuery[] = ", od.lngStockItemCode as lngStockItemCode";
			$flgStockItem = TRUE;
		}

		// 金型番号
		if ( $strViewColumnName == "strMoldNo" )
		{
			$arySelectQuery[] = ", od.strMoldNo as strMoldNo";
		}

		// 顧客品番
		if ( $strViewColumnName == "strGoodsCode" )
		{
			$arySelectQuery[] = ", p.strGoodsCode as strGoodsCode";
			$flgProductCode = TRUE;
		}

		// 運搬方法
		if ( $strViewColumnName == "lngDeliveryMethodCode" )
		{
			$arySelectQuery[] = ", od.lngDeliveryMethodCode as lngDeliveryMethodCode";
			$arySelectQuery[] = ", dm.strDeliveryMethodName as strDeliveryMethodName";
			$flgDeliveryMethod = TRUE;
		}

		// 納期
		if ( $strViewColumnName == "dtmDeliveryDate" )
		{
			$arySelectQuery[] = ", to_char( od.dtmDeliveryDate, 'YYYY/MM/DD' ) as dtmDeliveryDate";
		}

		// 単価
		if ( $strViewColumnName == "curProductPrice" )
		{
			$arySelectQuery[] = ", To_char( od.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
		}

		// 単位
		if ( $strViewColumnName == "lngProductUnitCode" )
		{
			$arySelectQuery[] = ", od.lngProductUnitCode as lngProductUnitCode";
			$arySelectQuery[] = ", pu.strProductUnitName as strProductUnitName";
			$flgProductUnit = TRUE;
		}

		// 数量
		if ( $strViewColumnName == "lngProductQuantity" )
		{
			$arySelectQuery[] = ", To_char( od.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
		}

		// 税抜金額
		if ( $strViewColumnName == "curSubTotalPrice" )
		{
			$arySelectQuery[] = ", To_char( od.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
		}
		// 明細備考
		if ( $strViewColumnName == "strDetailNote" )
		{
			$arySelectQuery[] = ", od.strNote as strDetailNote";
		}
	}

	// 仕入部品のみ表示対象だった時は仕入科目についてもデータを取得する
	if ( $flgStockItem == TRUE and $flgStockSubject == FALSE )
	{
		$arySelectQuery[] = ", od.lngStockSubjectCode as lngStockSubjectCode";
		$arySelectQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
		$flgStockSubject = TRUE;
	}

	// 絶対条件 対象発注NOの指定
	$aryQuery[] = " WHERE od.lngOrderNo = " . $lngOrderNo . " AND od.lngrevisionno = " . $lngRevisionNo ;

	// 条件の追加

	// ////発注マスタ内の検索条件////
	// SQL文の作成
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT od.lngSortKey as lngRecordNo";
	$aryOutQuery[] = "	,od.lngOrderNo as lngOrderNo";
	$aryOutQuery[] = "	,od.lngRevisionNo as lngRevisionNo";

	// select句 クエリー連結
	if( !empty($arySelectQuery) )
	{
		$aryOutQuery[] = implode("\n", $arySelectQuery);
	}

	// From句 の生成
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM t_OrderDetail od";

	// 追加表示用の参照マスタ対応
	$aryFromQuery[] = "   LEFT JOIN m_Product p on p.strProductCode = od.strProductCode and p.strReviseCode = od.strReviseCode and p.lngrevisionno = od.lngrevisionno";
	$aryFromQuery[] = " left join m_group mg on mg.lnggroupcode = p.lnginchargegroupcode";
	$aryFromQuery[] = " left join m_user  mu on mu.lngusercode = p.lnginchargeusercode";

	if ( $flgStockSubject )
	{
		$aryFromQuery[] = " LEFT JOIN m_StockSubject ss USING (lngStockSubjectCode)";
	}
	if ( $flgStockItem )
	{
	//		$aryOutQuery[] = " LEFT JOIN m_StockItem si USING (lngStockItemCode)\n";
	}
	if ( $flgDeliveryMethod )
	{
		$aryFromQuery[] = " LEFT JOIN m_DeliveryMethod dm USING (lngDeliveryMethodCode)";
	}
	if ( $flgProductUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_ProductUnit pu ON od.lngProductUnitCode = pu.lngProductUnitCode";
	}

	// From句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryFromQuery);
	// Where句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryQuery);

	// ソート条件指定
	if ( $aryData["strSortOrder"] == "ASC" )
	{
		$strAsDs = " DESC";	// ヘッダ項目とは逆順にする
	}
	else
	{
		$strAsDs = " ASC";	//降順
	}

	switch($aryData["strSort"])
	{
		case "strDetailNote":
			$aryOutQuery[] = " ORDER BY od.strNote" . $strAsDs . ", od.lngSortKey ASC";
			break;
		case "lngOrderDetailNo":
			$aryOutQuery[] = " ORDER BY od.lngSortKey" . $strAsDs;
			break;
		case "strProductName":
		case "strProductEnglishName":
		case "strGoodsCode":
			$aryOutQuery[] = " ORDER BY " . $aryData["strSort"] . " " . $strAsDs . ", od.lngSortKey ASC";
			break;
		default:
			$aryOutQuery[] = " ORDER BY od.lngSortKey ASC";
	}

	return implode("\n", $aryOutQuery);
}

/**
 * 検索結果表示関数（ヘッダ用）
 *
 *	検索結果からテーブル構成で結果を出力する関数
 *	ヘッダ行を表示する
 *
 *	@param  Integer $lngColumnCount 		行数
 *	@param  Array 	$aryHeadResult 			ヘッダ行の検索結果が格納された配列
 *	@param  Array 	$aryDetailResult 		明細行の検索結果が格納された配列
 *	@param  Array 	$aryDetailViewColumn 	明細表示対象カラム名の配列
 *	@param  Array 	$aryHeadViewColumn 		ヘッダ表示対象カラム名の配列
 *	@param  Array 	$aryData 				ＰＯＳＴデータ群
 *	@param	Array	$aryUserAuthority		ユーザーの操作に対する権限が入った配列
 *	@param  Object 	$objDB 					DBオブジェクト
 *	@param  Object 	$objCache 				キャッシュオブジェクト
 *	@param	Integer	$lngReviseTotalCount	表示対象の発注の過去リバイズの合計数
 *	@param	Integer	$lngReviseCount			表示対象の発注の表示順（最新発注なら０）
 *	@param	Array	$aryNewResult			表示対象の発注の最新の発注データ
 *	@access public
 */
function fncSetPurchaseHeadTable ( $lngColumnCount, $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, 
									$aryData, $aryUserAuthority, $objDB, $objCache, $lngReviseTotalCount, $lngReviseCount, $aryNewResult )
{
	include_once('conf.inc');
	require_once (LIB_DEBUGFILE);
	for ( $i = 0; $i < count($aryDetailResult); $i++ )
	{
		$aryHtml[] =  "<tr>";
		$aryHtml[] =  "\t<td class=\"rownum\">" . ($lngColumnCount + $i) . "</td>";
		// 表示対象カラムの配列より結果の出力
		for ( $j = 0; $j < count($aryHeadViewColumn); $j++ )
		{
			$strColumnName = $aryHeadViewColumn[$j];
			$TdData = "";

			// 表示対象がボタンの場合
			if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" or $strColumnName == "btnInvalid" or $strColumnName == "Record" or $strColumnName == "btnAdmin" )
			{
				// ボタン種により変更

				// 詳細表示
				if ( $strColumnName == "btnDetail" and $aryUserAuthority["Detail"] )
				{
					// 発注データが削除対象の場合、詳細表示ボタンは選択不可
					if ( $aryHeadResult["lngrevisionno"] >= 0 )
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" lngrevisionno=\"" . $aryDetailResult[$i]["lngrevisionno"] . "\" class=\"detail button\"></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td></td>\n";
					}
				}

				// 確定
				if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
				{
					// 発注データの状態により分岐  //// 状態が「仮発注」の場合確定ボタンは選択不可
					if ( $aryHeadResult["lngorderstatuscode"] == 1)
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"fix button\"></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td></td>\n";
					}
				}

				// 履歴
				if ( $strColumnName == "Record" ){
					if ( $aryHeadResult["lngrevisionno"] > 0 ) {
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" strordercode=\"" . $aryHeadResult["strordercode"] . "\" class=\"record button\"></td>\n";
					} else {
						$aryHtml[] = "\t<td></td>\n";
					}
				}

				// 確定取消
				if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
				{
					// 発注データの状態により分岐  //// 状態が「申請中」「納品中」「納品済」「締め済」の場合削除ボタンを選択不可
					// 最新発注が削除データの場合も選択不可
					if ( $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_CLOSED)
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"remove button\"></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td></td>\n";
					}
/*
					//リバイズが存在しない場合
					if ( $lngReviseTotalCount == 1 )
					{
						// 発注データの状態により分岐  //// 状態が「仮発注」「締め済」の場合削除ボタンを選択不可
						// 最新発注が削除データの場合も選択不可
						if ( $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_CLOSED)
						{
							$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"remove button\"></td>\n";
						}
						else
						{
							$aryHtml[] = "\t<td></td>\n";
						}
					}
					//複数リバイズが存在する場合
					else
					{
						// 最新発注の場合
						if ( $lngReviseCount == 0 )
						{
							// 発注データの状態により分岐  //// 状態が「申請中」「納品中」「納品済」「締め済」の場合削除ボタンを選択不可
							// 最新発注が削除データの場合も選択不可
							if ( $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_CLOSED)
							{
								$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"remove button\"></td>\n";
							}
							else
							{
								$aryHtml[] = "\t<td></td>\n";
							}
						}
						else
						{
							$aryHtml[] = "\t<td></td>\n";
						}
					}
*/
				}

				// 削除済
				if ( $strColumnName == "btnAdmin" and $aryUserAuthority["Admin"] ){
					if( $aryHeadResult["lngRevisionno"] == -1 ) {
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"admin button\"></td>\n";
					} else {
						$aryHtml[] = "\t<td></td>\n";
					}
				}

			}
			else if ($strColumnName != "") {
				// $TdData = "\t<td>";
				$TdData = "";
				$TdDataUse = true;
				$strText = "";
				// 登録日
				if ( $strColumnName == "dtmInsertDate" )
				{
					$TdData .= "\t<td class=\"td-dtminsertdate\">";
					$TdData .= str_replace( "-", "/", substr( $aryHeadResult["dtminsertdate"], 0, 19 ) );
				}
				// 計上日
				else if ( $strColumnName == "dtmOrderAppDate" )
				{
					$TdData .= "\t<td class=\"td-dtmorderappdate\">";
					$TdData .= str_replace( "-", "/", $aryHeadResult["dtmorderappdate"] );
				}
				// 発注NO
				else if ( $strColumnName == "strOrderCode" )
				{
					$baseOrderCode = explode("_", $aryHeadResult["strordercode"])[0];
					$TdData .= "\t<td class=\"td-strordercode\" baseordercode=\"". $baseOrderCode . "\">";
					$TdData .= $aryHeadResult["strordercode"];
					// // 管理モードの場合　リビジョン番号を表示する
					// if ( $aryData["Admin"] )
					// {
					// 	$TdData .= "</td>\n\t<td>" . $aryHeadResult["lngrevisionno"];
					// }
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
					$TdData .= "\t<td class=\"td-strinputuserdisplaycode\">";
					$TdData .= $strText;
				}
				// 仕入先
				else if ( $strColumnName == "lngCustomerCode" )
				{
					if ( $aryHeadResult["strcustomerdisplaycode"] )
					{
						$strText .= "[" . $aryHeadResult["strcustomerdisplaycode"] ."]";
					}
					else
					{
						$strText .= "      ";
					}
					$strText .= " " . $aryHeadResult["strcustomerdisplayname"];
					$TdData .= "\t<td class=\"td-strcustomerdisplaycode\">";
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
					$TdData .= "\t<td class=\"td-curtotalprice\">";
					$TdData .= $strText;
				}
				// 状態
				else if ( $strColumnName == "lngOrderStatusCode" )
				{
					$TdData .= "\t<td class=\"td-strorderstatusname\">";
					$TdData .= $aryHeadResult["strorderstatusname"];
				}
				// 支払条件
				else if ( $strColumnName == "lngPayConditionCode" )
				{
					$TdData .= "\t<td class=\"td-strpayconditionname\">";
					$TdData .= $aryHeadResult["strpayconditionname"];
				}
				// 発注有効期限日
				else if ( $strColumnName == "dtmExpirationDate" )
				{
					$TdData .= "\t<td class=\"td-dtmexpirationdate\">";
					$TdData .= str_replace( "-", "/", $aryHeadResult["dtmexpirationdate"] );
				}
				// 明細行番号
				else if ( $strColumnName == "lngRecordNo" )
				{
					$TdData .= "\t<td class=\"td-lngrecordno\">";
					$TdData .= $aryDetailResult[$i]["lngrecordno"];
				}
				// 2004.03.31 suzukaze update start
				// 製品コード
				else if ( $strColumnName == "strProductCode" )
				{
					if ( $aryDetailResult[$i]["strproductcode"] )
					{
						$strText .= "[" . $aryDetailResult[$i]["strproductcode"] ."]";
					}
					else
					{
						$strText .= "      ";
					}
					$TdData .= "\t<td class=\"td-strproductcode\">";
					$TdData .= $strText;
				}
				// 2004.03.31 suzukaze update start
				// 仕入科目
				else if ( $strColumnName == "lngStockSubjectCode" )
				{
					if ( $aryDetailResult[$i]["lngstocksubjectcode"] )
					{
						$strText .= "[" . $aryDetailResult[$i]["lngstocksubjectcode"] ."]";
					}
					else
					{
						$strText .= "      ";
					}
					$strText .= " " . $aryDetailResult[$i]["strstocksubjectname"];
//fncDebug("kids2.log", $strText , __FILE__, __LINE__, "a" );
					$TdData .= "\t<td class=\"td-lngstocksubjectcode\">";
					$TdData .= $strText;
				}
				// 仕入部品
				else if ( $strColumnName == "lngStockItemCode" )
				{
					if ( $aryDetailResult[$i]["lngstockitemcode"] )
					{
						$strText .= "[" . $aryDetailResult[$i]["lngstockitemcode"] ."]";
						// 仕入科目コードが存在するならば
						if ( $aryDetailResult[$i]["lngstocksubjectcode"] )
						{
							$strSubjectItem = $aryDetailResult[$i]["lngstocksubjectcode"] . ":" . $aryDetailResult[$i]["lngstockitemcode"];
							$aryStockItem = $objCache->GetValue("lngstocksubjectcode:lngstockitemcode", $strSubjectItem);
							if( !is_array($aryStockItem) )
							{
								// 仕入名称の取得
								$strStockItemName = fncGetMasterValue( "m_stockitem", "lngstockitemcode", "strstockitemname" , 
									$aryDetailResult[$i]["lngstockitemcode"], "lngstocksubjectcode = " . $aryDetailResult[$i]["lngstocksubjectcode"], $objDB );
								// 仕入名称の設定
								$aryStockItem = $strStockItemName;
								$objCache->SetValue("lngstocksubjectcode:lngstockitemcode", $strSubjectItem, $aryStockItem);
							}
							$strText .= " " . $aryStockItem;
						}
					}
					else
					{
						$strText .= "      ";
						$strText .= " " . $aryDetailResult[$i]["strstockitemname"];
					}
					$TdData .= "\t<td class=\"td-lngstockitemcode\">";
					$TdData .= $strText;
				}
				// 運搬方法
				else if ( $strColumnName == "lngDeliveryMethodCode" )
				{
					if ( $aryDetailResult[$i]["strdeliverymethodname"] == "" )
					{
						$aryDetailResult[$i]["strdeliverymethodname"] = "未定";
					}
					$strText .= $aryDetailResult[$i]["strdeliverymethodname"];
					$TdData .= "\t<td class=\"td-strdeliverymethodname\">";
					$TdData .= $strText;
				}
				// 2004.04.21 suzukaze update start
				// 納期
				else if ( $strColumnName == "dtmDeliveryDate" )
				{
					$TdData .= "\t<td class=\"td-dtmdeliverydate\">";
					$TdData .= str_replace( "-", "/", $aryDetailResult[$i]["dtmdeliverydate"] );
				}
				// 2004.04.21 suzukaze update end
				// 単価
				else if ( $strColumnName == "curProductPrice" )
				{
					$TdDataUse = false;
					$strText = "\t<td align=\"right\">";
					$strText .= $aryHeadResult["strmonetaryunitsign"] . " ";
					if ( !$aryDetailResult[$i]["curproductprice"] )
					{
						$strText .= "0.00";
					}
					else
					{
						$strText .= $aryDetailResult[$i]["curproductprice"];
					}
					$aryHtml[] = $strText . "</td>\n";
				}
				// 単位
				else if ( $strColumnName == "lngProductUnitCode" )
				{
					$TdData .= "\t<td class=\"td-strproductunitname\">";
					$TdData .= $aryDetailResult[$i]["strproductunitname"];
				}
				// 数量
				else if ( $strColumnName == "lngProductQuantity" )
				{
					$TdDataUse = false;
					$aryHtml[] = "\t<td align=\"right\">" . $aryDetailResult[$i]["lngproductquantity"] .  "</td>\n";
				}
				// 税抜金額
				else if ( $strColumnName == "curSubTotalPrice" )
				{
					$TdDataUse = false;
					$strText = "\t<td align=\"right\">";
					$strText .= $aryHeadResult["strmonetaryunitsign"] . " ";
					if ( !$aryDetailResult[$i]["cursubtotalprice"] )
					{
						$strText .= "0.00";
					}
					else
					{
						$strText .= $aryDetailResult[$i]["cursubtotalprice"];
					}
					$aryHtml[] = $strText . "</td>\n";
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
					$TdData .= "\t<td>";
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
 * 発注書データHTML変換
 * 
 * @param	Array	$aryViewColumn		カラム情報
 * @param	Array	$aryResult			発注書データ
 * @param	Array	$aryUserAuthority	権限
 * @access	public
 * 
 */
function fncSetPurchaseOrderHtml($aryViewColumn, $aryResult, $aryUserAuthority){
	for($i = 0; $i < count($aryResult); $i++){
		$aryHtml[] = "<tr>";
		$aryHtml[] = "  <td class=\"rownum\">" . ($i + 1) . "</td>";
		for($j = 0; $j < count($aryViewColumn); $j++){
			$strColumn = $aryViewColumn[$j];
			// 表示対象がボタンの場合
			if($strColumn == "btnEdit" or $strColumn == "btnRecord" or $strColumn == "btnDelete") {
				// 修正ボタン
				if($strColumn == "btnEdit" and $aryUserAuthority["Edit"]){
					// 発注書データが削除済みの場合、修正ボタンは非表示
					if($aryResult[$i]["lngrevisionno"] == -1){
						$aryHtml[] = "  <td></td>";
					} else {
						$aryHtml[] = "  <td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngpurchaseorderno=\"" . $aryResult[$i]["lngpurchaseorderno"] . "\" lngrevisionno=\"" . $aryResult[$i]["lngrevisionno"] . "\" class=\"edit button\"></td>";
					}
				}
				// 履歴ボタン
				if($strColumn == "btnRecord"){
					// リビジョンが0の場合、履歴ボタンは非表示
					if($aryResult[$i]["lngrevisionno"] == 1) {
						$aryHtml[] = "  <td></td>";
					} else {
						$strOrderCode = sprintf("%s_%02d", $aryResult[$i]["strordercode"], $aryResult[$i]["lngrevisionno"]);
						$aryHtml[] = "  <td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngpurchaseorderno=\"" . $aryResult[$i]["lngpurchaseorderno"] . "\" strOrderCode=\"" . $strOrderCode . "\" class=\"record button\"></td>";
					}
				}
				// 削除済ボタン
				if($strColumn == "btnDelete" and $aryUserAuthority["Admin"]) {
					// 削除済みのみ表示
					if($aryResult[$i]["lngrevisionno"] == -1){
						$aryHtml[] = "  <td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngpurchaseorderno=\"" . $aryResult[$i]["lngpurchaseorderno"] . "\" class=\"record button\"></td>";
					} else {
						$aryHtml[] = "  <td></td>";
					}
				}
			} else {
				// 発注NO.
				if($strColumn == "strOrderCode"){
					$aryHtml[] = "  <td class=\"td-strordercode\" baseordercode=\"" . $aryResult[$i]["strordercode"] . "\">" . sprintf("%s_%02d", $aryResult[$i]["strordercode"], $aryResult[$i]["lngrevisionno"]) . "</td>";
				}
				// 発注有効期限日
				if($strColumn == "dtmExpirationDate"){
					$aryHtml[] = "  <td class=\"td-dtmexpirationdate\">" . $aryResult[$i]["dtmexpirationdate"] . "</td>";
				}
				// 製品コード
				if($strColumn == "strProductCode"){
					$aryHtml[] = "  <td class=\"td-strproductcode\">" . sprintf("[%s]", $aryResult[$i]["strproductcode"]) . "</td>";
				}
				// 登録日
				if($strColumn == "dtmInsertDate"){
					$aryHtml[] = "  <td class=\"td-dtminsertdate\">" . $aryResult[$i]["dtminsertdate"] . "</td>";
				}
				// 入力者
				if($strColumn == "lngInputUserCode"){
					$aryHtml[] = "  <td class=\"td-lnginsertusercode\">" . sprintf("[%s] %s", $aryResult[$i]["lnginsertusercode"], $aryResult[$i]["strinsertusername"]) . "</td>";
				}
				// 製品名
				if($strColumn == "strProductName"){
					$aryHtml[] = "  <td class=\"td-strproductname\">" . $aryResult[$i]["strproductname"] . "</td>";
				}
				// 製品名(英語)
				if($strColumn == "strProductEnglishName"){
					$aryHtml[] = "  <td class=\"td-strproductenglishname\">" . $aryResult[$i]["strproductenglishname"] . "</td>";
				}
				// 営業部署
				if($strColumn == "lngInChargeGroupCode"){
					$aryHtml[] = "  <td class=\"td-lnggroupcode\">" . sprintf("[%s] %s", $aryResult[$i]["lnggroupcode"], $aryResult[$i]["strgroupname"]) . "</td>";
				}
				// 開発担当者
				if($strColumn == "lngInChargeUserCode"){
					$aryHtml[] = "  <td class=\"td-lngusercode\">" . sprintf("[%s] %s", $aryResult[$i]["lngusercode"], $aryResult[$i]["strusername"]) . "</td>";
				}
				// 仕入先
				if($strColumn == "lngCustomerCode"){
					$aryHtml[] = "  <td class=\"td-lngcustomercode\">" .sprintf("[%s] %s", $aryResult[$i]["lngcustomercode"], $aryResult[$i]["strcustomername"]) . "</td>";
				}
				// 支払条件
				if($strColumn == "lngPayConditionCode"){
					$aryHtml[] = "  <td class=\"td-strpaycnoditionname\">" . $aryResult[$i]["strpaycnoditionname"] . "</td>";
				}
				// 税抜金額
				if($strColumn == "curTotalPrice"){
					$aryHtml[] = "  <td class=\"td-curtotalprice\">" . sprintf("%s %.2f", $aryResult[$i]["strmonetaryunitsign"], $aryResult[$i]["curtotalprice"]) . "</td>";
				}
				// 納品場所
				if($strColumn == "lngDeliveryPlaceCode"){
					$aryHtml[] = "  <td class=\"td-strdeliveryplacename\">" . $aryResult[$i]["strdeliveryplacename"] . "</td>";
				}
				// 明細備考
				if($strColumn == "strNote"){
					$aryHtml[] = "  <td class=\"td-strnote\">" . $aryResult[$i]["strnote"] . "</td>";
				}
			}
		}
		$aryHtml[] = "</tr>";
	}

	return implode("\n", $aryHtml);
}

/**
 * 検索結果表示関数
 *
 *	検索結果からテーブル構成で結果を出力する関数
 *
 *	@param  Array 	$aryResult 			検索結果が格納された配列
 *	@param  Array 	$aryViewColumn 		表示対象カラム名の配列
 *	@param  Array 	$aryData 			ＰＯＳＴデータ群
 *	@param	Array	$aryUserAuthority	ユーザーの操作に対する権限が入った配列
 *	@param	Array	$aryTytle			項目名が格納された配列（呼び出し元で日本語用、英語用の切り替え）
 *	@param  Object	$objDB       		DBオブジェクト
 *	@param  Object	$objCache       	キャッシュオブジェクト
 *	@param	Array	$aryTableName		表示カラム名とマスタ内カラム名変更用
 *	@access public
 */
function fncSetPurchaseTable ( $aryResult, $arySearchColumn, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName )
{
	// 準備
	include_once('conf.inc');
	require_once (LIB_DEBUGFILE);

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
		else if( $strColumnName == "btnAdmin")
		{
			if ( $aryUserAuthority["Admin"]){
				$aryHeadViewColumn[] = $strColumnName;
			}
		}
		// 2004.03.31 suzukaze update start
		// 詳細部
		else if ( $strColumnName == "strProductCode" 
			or $strColumnName == "lngInChargeGroupCode" or $strColumnName == "lngInChargeUserCode" // <-- added by siato
			or $strColumnName == "lngRecordNo" or $strColumnName == "lngStockSubjectCode" or $strColumnName == "lngStockItemCode"
			or $strColumnName == "strGoodsCode" or $strColumnName == "lngDeliveryMethodCode" or $strColumnName == "curProductPrice"
			or $strColumnName == "lngProductUnitCode" or $strColumnName == "lngProductQuantity" or $strColumnName == "curSubTotalPrice"
			or $strColumnName == "strDetailNote" or $strColumnName == "dtmDeliveryDate" 
			or $strColumnName == "strProductName" or $strColumnName == "strProductEnglishName" or $strColumnName == "strMoldNo" )
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
			or ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
			or ( $strColumnName == "btnAdmin" and $aryUserAuthority["Admin"] ) ) 
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
		// 管理モードの場合　同じ発注コードの一覧を取得し表示する

		// リバイズコード無しの発注コードを取得する
		if ( strlen($aryResult[$i]["strordercode"]) >= 9)
		{
			$strOrderCodeBase = preg_replace( "/" . strstr( $aryResult[$i]["strordercode"] . "/", "_" ), "", $aryResult[$i]["strordercode"] );
		}
		else
		{
			$strOrderCodeBase = $aryResult[$i]["strordercode"];
		}

//		$strSameOrderCodeQuery = fncGetSearchPurchaseSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $strOrderCodeBase, $aryResult[$i]["lngorderno"], FALSE ,$aryResult[$i]["lngrevisionno"]);
//		// 値をとる =====================================
//		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameOrderCodeQuery, $objDB );

		// 配列のクリア
//		unset( $arySameOrderCodeResult );

//		if ( $lngResultNum )
//		{
//			for ( $j = 0; $j < $lngResultNum; $j++ )
//			{
//				$arySameOrderCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
//			}
//			$lngSameOrderCount = $lngResultNum;
//		}
//		$objDB->freeResult( $lngResultID );

		// 同じ発注コードでの過去リバイズデータが存在すれば
//		if ( $lngResultNum )
//		{
//			for ( $j = 0; $j < $lngSameOrderCount; $j++ )
//			{
				// 検索結果部分の設定

//				reset( $arySameOrderCodeResult[$j] );

				// 明細出力用の調査
				$lngDetailViewCount = count( $aryDetailViewColumn );

				if ( $lngDetailViewCount )
				{
					// 明細行数の調査
					$strDetailQuery = fncGetOrderToProductSQL ( $aryDetailViewColumn, $aryResult[$i]["lngorderno"], $aryResult[$i]["lngrevisionno"], $aryData, $objDB );
//("kids2.log", $strDetailQuery , __FILE__, __LINE__, "a" );
					// クエリー実行
					if ( !$lngDetailResultID = $objDB->execute( $strDetailQuery ) )
					{
						$strMessage = fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
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

				// １レコード分の出力
				$aryHtml_add = fncSetPurchaseHeadTable ( $lngColumnCount, $aryResult[$i], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameOrderCount, $aryResult[$i]["lngrevisionno"], $aryResult[$i] );
//				$aryHtml_add = fncSetPurchaseHeadTable ( $lngColumnCount, $arySameOrderCodeResult[$j], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameOrderCount, $j, $arySameOrderCodeResult[0] );
				$lngColumnCount = $lngColumnCount + count($aryDetailResult);
				
				$strColBuff = '';
				for ( $k = 0; $k < count($aryHtml_add); $k++ )
				{
					$strColBuff .= $aryHtml_add[$k];
				}
				$aryHtml[] =$strColBuff;
//			}
//		}

	// 管理モード用過去リバイズデータ出力end==================================

	}

	$aryHtml[] = "</tbody>";

	$strhtml = implode( "\n", $aryHtml );

	return $strhtml;
}

/**
 * 発注データHTML変換
 * 
 * @param	Array	$aryResult			発注データ
 * @param	Array	$aryViewColumn		表示列
 * @param	Array	$aryUserAuthority	権限
 * @param	Array	$aryTitle			列名
 * @param	Object	$objDB				DBオブジェクト
 * @param	Object	$objCache			キャッシュオブジェクト
 * @param	Array	$aryTableName		テーブル名
 */
function fncSetPurchaseOrderTable( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTitle, $objDB, $objCache, $aryTableName ){
	// 表示カラムのヘッダ部と明細部の分離処理
	for($i = 0; $i < count($aryViewColumn); $i++){
		$strColumnName = $aryViewColumn[$i];

		// ボタンの場合ここで表示・非表示切り替え
		if($strColumnName == "btnEdit"){
			if($aryUserAuthority["Edit"]){
				$aryHeadViewColumn[] = $strColumnName;
			}
		} else if($strColumnName == "btnRecord"){
			$aryHeadViewColumn[] = $strColumnName;
		} else if($strColumnName == "btnDelete"){
			if($aryUserAuthority["Admin"]){
				$aryHeadViewColumn[] = $strColumnName;
			}
		} else if($strColumnName == "dtmInsertDate"
				or $strColumnName == "lngInputUserCode"
				or $strColumnName == "dtmExpirationDate"
				or $strColumnName == "strOrderCode"
				or $strColumnName == "strProductCode"
				or $strColumnName == "strProductName"
				or $strColumnName == "strProductEnglishName"
				or $strColumnName == "lngInChargeGroupCode"
				or $strColumnName == "lngInChargeUserCode"
				or $strColumnName == "lngCustomerCode"
				or $strColumnName == "strDeliveryPlaceName"
				or $strColumnName == "lngMonetaryunitCode"
				or $strColumnName == "lngMonetaryRateCode"
				or $strColumnName == "lngPayConditionCode")
		{
			$aryDetailViewColumn[] = $strColumnName;
			$aryHeadViewColumn[] = $strColumnName;
		} else {
			$aryHeadViewColumn[] = $strColumnName;
		}
	}

	// テーブルの形成
	$lngColumnCount = 1;

	// 項目名列の生成
	$aryHtml[] = "<thead>";
	$aryHtml[] = "<tr>";
	$aryHtml[] = "\t<th class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/copy_off_bt.gif\" class=\"copy button\"></th>";

	// 表示対象カラムの配列より項目設定
	for($i = 0; $i < count($aryViewColumn); $i++){
		$addTh = "\t<th>";
		$strColumnName = $aryViewColumn[$i];

		if($strColumnName == "btnPreview" or $strColumnName == "btnEdit" or $strColumnName == "btnRecord" or $strColumnName == "btnDelete"){
			// ソート項目以外の場合
			if(($strColumnName == "btnPreview" and $aryUserAuthority["Preview"])
				or ($strColumnName == "btnEdit" and $aryUserAuthority["Edit"])
				or ($strColumnName == "btnRecord")
				or ($strColumnName == "btnDelete" and $aryUserAuthority["Admin"])
			){
				$addTh .= $aryTitle[$strColumnName];
			} else {
				// 表示対象外
				continue;
			}
		} else {
			// ソート項目の場合
			$addTh .= $aryTitle[$strColumnName];
		}

		$addTh .= "</th>";
		$aryHtml[] = $addTh;
	}
	$aryHtml[] = "</tr>";
	$aryHtml[] = "</thead>";

	// データ部
	$aryHtml[] = "<tbody>";
	$lngResultCount = count($aryResult);

	$aryHtml[] = fncSetPurchaseOrderHtml($aryViewColumn, $aryResult, $aryUserAuthority);
	$aryHtml[] = "</tbody>";
	$strHtml = implode("\n", $aryHtml);

	return $strHtml;
}

/**
 * 発注書データHTML変換
 * 
 * @param	Array	$aryResult			発注書データ
 * @param	Array	$aryViewColumn		表示列
 * @param	Array	$aryUserAuthority	権限
 * @param	Array	$aryTitle			列名
 * @param	Object	$objDB				DBオブジェクト
 * @param	Object	$objCache			キャッシュオブジェクト
 * @param	Array	$aryTableName		テーブル名
 */

function fncSetPurchaseOrderTable2( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTitle, $objDB, $objCache, $aryTableName ){
	for($i = 0; $i < count($aryDetailResult); $i++){
		$aryHtml[] = "<tr>";
		$aryHtml[] = "\t<td>" . ($lngColumnCount + 1) . "</td>";

		// 表示対象カラムの配列より結果の出力
		for($j = 0; $j < count($aryHeadViewColumn); $j++){
			$strColumnName = $aryHeadViewColumn[$j];
			$tdData = "";

			// 表示対象がボタンの場合
			if($strColumnName == "btnEdit" or $strColumnName == "btnRecord" or $strColumnName == "btnDelete"){
				// 修正ボタン
				if($strColumnName == "btnEdit" and $aryUserAuthority["Edit"]){
					$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngPurchaseOrderNo=\"" . $aryDetailResult[$i]["lngPurchaseOrderNo"] . "\" class=\"detail button\"></td>\n";
				} else {
					$aryHtml[] = "\t<td></td>\n";
				}

				// 履歴ボタン
				if($strColumnName == "btnRecord"){
					if($aryHeadResult["lngRevisionNo"] > 0){
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngPurchaseOrderNo=\"" . $aryDetailResult[$i]["lngPurchaseOrderNo"] . "\" strOrderCode =\"" . $aryResult["strordercode"] . "\" class=\"fix button\"></td>\n";
					} else {
						$aryHtml[] = "\t<td></td>\n";
					}
				}

				// 削除済ボタン
				if($strColumnName == "btnDelete" and $aryUserAuthority["Admin"]){
					if($aryHeadResult["lngRevisionNo"] == -1){
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngPurchaseOrderNo=\"" . $aryDetailResult[$i]["lngPurchaseOrderNo"] . "\" class=\"fix button\"></td>\n";
					} else {
						$aryHtml[] = "\t<td></td>\n";
					}
				}
			}
			$tdData .= "</td>\n";
			//if($tdDataUse){
				$aryHtml[] = $tdData;
			//}
		}
		$aryHtml[] = "</tr>";
	}
	return $aryHtml;
}

function fncResortSearchColumn($aryViewColumn){
	$aryResult = array();

	$aryResult[] = "btnDetail";
	$aryResult[] = "btnFix";
	$aryResult[] = "Record";
	if(in_array("btnAdmin",              $aryViewColumn)){ $aryResult[] = "btnAdmin"; }
	if(in_array("strOrderCode",          $aryViewColumn)){ $aryResult[] = "strOrderCode"; }
	if(in_array("dtmExpirationDate",     $aryViewColumn)){ $aryResult[] = "dtmExpirationDate"; }
	if(in_array("strProductCode",        $aryViewColumn)){ $aryResult[] = "strProductCode"; }
	if(in_array("dtmInsertDate",         $aryViewColumn)){ $aryResult[] = "dtmInsertDate"; }
	if(in_array("lngInputUserCode",      $aryViewColumn)){ $aryResult[] = "lngInputUserCode"; }
	if(in_array("strProductName",        $aryViewColumn)){
		$aryResult[] = "strProductName";
		$aryResult[] = "strProductEnglishName";
	}
	if(in_array("lngInChargeGroupCode",  $aryViewColumn)){ $aryResult[] = "lngInChargeGroupCode"; }
	if(in_array("lngInChargeUserCode",   $aryViewColumn)){ $aryResult[] = "lngInChargeUserCode"; }
	if(in_array("lngCustomerCode",       $aryViewColumn)){ $aryResult[] = "lngCustomerCode"; }
	if(in_array("lngStockSubjectCode",   $aryViewColumn)){ $aryResult[] = "lngStockSubjectCode"; }
	if(in_array("lngStockItemCode",      $aryViewColumn)){ $aryResult[] = "lngStockItemCode"; }
	if(in_array("dtmDeliveryDate",       $aryViewColumn)){ $aryResult[] = "dtmDeliveryDate"; }
	if(in_array("lngOrderStatusCode",    $aryViewColumn)){ $aryResult[] = "lngOrderStatusCode"; }
	if(in_array("lngRecordNo",           $aryViewColumn)){ $aryResult[] = "lngRecordNo"; }
	if(in_array("curProductPrice",       $aryViewColumn)){ $aryResult[] = "curProductPrice"; }
	if(in_array("lngProductQuantity",    $aryViewColumn)){ $aryResult[] = "lngProductQuantity"; }
	if(in_array("curSubTotalPrice",      $aryViewColumn)){ $aryResult[] = "curSubTotalPrice"; }
	if(in_array("strNote",               $aryViewColumn)){ $aryResult[] = "strNote"; }
	if(in_array("strDetailNote",         $aryViewColumn)){ $aryResult[] = "strDetailNote"; }
	if(in_array("btnDelete",             $aryViewColumn)){ $aryResult[] = "btnDelete"; }

	return $aryResult;
}

function fncResortSearchColumn2($aryViewColumn){
	$aryResult = array();

	if(in_array("btnPreview",            $aryViewColumn)){ $aryResult[] = "btnPreview"; }
	$aryResult[] = "btnEdit";
	$aryResult[] = "btnRecord";
	$aryResult[] = "btnDelete";
	if(in_array("strOrderCode",          $aryViewColumn)){ $aryResult[] = "strOrderCode"; }
	if(in_array("dtmExpirationDate",     $aryViewColumn)){ $aryResult[] = "dtmExpirationDate"; }
	if(in_array("strProductCode",        $aryViewColumn)){ $aryResult[] = "strProductCode";	}
	if(in_array("dtmInsertDate",         $aryViewColumn)){ $aryResult[] = "dtmInsertDate"; }
	if(in_array("lngInputUserCode",      $aryViewColumn)){ $aryResult[] = "lngInputUserCode"; }
	if(in_array("strProductCode",        $aryViewColumn)){
		$aryResult[] = "strProductName";
		$aryResult[] = "strProductEnglishName";
	}
	if(in_array("lngInChargeGroupCode",  $aryViewColumn)){ $aryResult[] = "lngInChargeGroupCode"; }
	if(in_array("lngInChargeUserCode",   $aryViewColumn)){ $aryResult[] = "lngInChargeUserCode"; }
	if(in_array("lngCustomerCode",       $aryViewColumn)){ $aryResult[] = "lngCustomerCode"; }
	if(in_array("lngPayConditionCode",   $aryViewColumn)){ $aryResult[] = "lngPayConditionCode"; }
	if(in_array("curTotalPrice",         $aryViewColumn)){ $aryResult[] = "curTotalPrice"; }
	if(in_array("lngDeliveryPlaceCode",  $aryViewColumn)){ $aryResult[] = "lngDeliveryPlaceCode"; }
	if(in_array("strNote",               $aryViewColumn)){ $aryResult[] = "strNote"; }

	return $aryResult;
}

?>