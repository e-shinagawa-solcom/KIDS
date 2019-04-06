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
function fncGetSearchPurchaseSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strOrderCode, $lngOrderNo, $bytAdminMode )
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
			$arySelectQuery[] = ", o.strOrderCode || '-' || o.strReviseCode as strOrderCode";
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
			$arySelectQuery[] = ", to_char( o.dtmExpirationDate, 'YYYY/MM/DD' ) as dtmExpirationDate";
		}

		// 備考
		if ( $strViewColumnName == "strNote" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", o.strNote as strNote";
		}

		// 合計金額
		if ( $strViewColumnName == "curTotalPrice" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", To_char( o.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
		}
	}

	//
	$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
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
						$strNewOrderCode = ereg_replace( strstr( $arySearchDataColumn["strOrderCodeFrom"], "-" ), "", $arySearchDataColumn["strOrderCodeFrom"] );
					}
					else
					{
						$strNewOrderCode = $arySearchDataColumn["strOrderCodeFrom"];
					}
					$aryQuery[] = " AND o.strOrderCode >= '" . $strNewOrderCode . "'";

				}
				if ( $arySearchDataColumn["strOrderCodeTo"] )
				{
					if ( strpos($arySearchDataColumn["strOrderCodeTo"], "-") )
					{
						// リバイズコード付の発注Ｎｏのリバイズコードは検索結果では最新版を表示するため、無視する
						$strNewOrderCode = ereg_replace( strstr( $arySearchDataColumn["strOrderCodeTo"], "-" ), "", $arySearchDataColumn["strOrderCodeTo"] );
					}
					else
					{
						$strNewOrderCode = $arySearchDataColumn["strOrderCodeTo"];
					}
					$aryQuery[] = " AND o.strOrderCode <= '" . $strNewOrderCode . "'";
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
					$aryQuery[] = " AND o.dtmExpirationDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmExpirationDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmExpirationDateTo"] . " 23:59:59";
					$aryQuery[] = " AND o.dtmExpirationDate <= '" . $dtmSearchDate . "'";
				}
			}


			//
			// 明細テーブルの条件
			//
			
			// 製品コード
			if ( $strSearchColumnName == "strProductCode" )
			{
				if ( $arySearchDataColumn["strProductCodeFrom"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = "od1.strProductCode >= '" . $arySearchDataColumn["strProductCodeFrom"] . "' ";
					$detailFlag = TRUE;
				}
				if ( $arySearchDataColumn["strProductCodeTo"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = "od1.strProductCode <= '" . $arySearchDataColumn["strProductCodeTo"] . "' ";
					$detailFlag = TRUE;
				}
			}
			
			// 部門
			if ( $strSearchColumnName == "lngInChargeGroupCode" )
			{
				if( $arySearchDataColumn["lngInChargeGroupCode"] || $arySearchDataColumn["strInChargeGroupName"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
				}

				if ( $arySearchDataColumn["lngInChargeGroupCode"] )
				{
					$aryDetailWhereQuery[] = " mg.strGroupDisplayCode = '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'";
					$detailFlag = TRUE;
				}
				if ( $arySearchDataColumn["strInChargeGroupName"] )
				{
					if( $arySearchDataColumn["lngInChargeGroupCode"] )
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = " UPPER(mg.strGroupDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeGroupName"] . "%')";
					$detailFlag = TRUE;
				}
			}
			// 担当者
			if ( $strSearchColumnName == "lngInChargeUserCode" )
			{
				if( $arySearchDataColumn["lngInChargeUserCode"] || $arySearchDataColumn["strInChargeUserName"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
				}

				if ( $arySearchDataColumn["lngInChargeUserCode"] )
				{
					$aryDetailWhereQuery[] = " mu.strUserDisplayCode = '" . $arySearchDataColumn["lngInChargeUserCode"] . "'";
					$detailFlag = TRUE;
				}
				if ( $arySearchDataColumn["strInChargeUserName"] )
				{
					if( $arySearchDataColumn["lngInChargeUserCode"] )
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = " UPPER(mu.strUserDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeUserName"] . "%')";
					$detailFlag = TRUE;
				}
			}
			// 製品名称（日本語）
			if ( $strSearchColumnName == "strProductName" )
			{
				if ( $arySearchDataColumn["strProductName"] )
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
					$aryDetailWhereQuery[] = "UPPER( p.strProductName ) LIKE UPPER( '%" . $arySearchDataColumn["strProductName"] . "%' ) ";
					$detailFlag = TRUE;
				}
			}
			// 製品名称（英語）
			if ( $strSearchColumnName == "strProductEnglishName" )
			{
				if ( $arySearchDataColumn["strProductEnglishName"] )
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
					$aryDetailWhereQuery[] = "UPPER( p.strProductEnglishName ) LIKE UPPER( '%" . $arySearchDataColumn["strProductEnglishName"] . "%' ) ";
					$detailFlag = TRUE;
				}
			}

			// 仕入科目
			if ( $strSearchColumnName == "lngStockSubjectCode" )
			{
				if ( $arySearchDataColumn["lngStockSubjectCode"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
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
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
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
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = "od1.dtmDeliveryDate >= '" . $arySearchDataColumn["dtmDeliveryDateFrom"] . "' ";
					$detailFlag = TRUE;
				}
				if ( $arySearchDataColumn["dtmDeliveryDateTo"] )
				{
					if ( !$detailFlag )
					{
						$aryDetailTargetQuery[] = " where";
					}
					else
					{
						$aryDetailWhereQuery[] = "AND ";
					}
					$aryDetailWhereQuery[] = "od1.dtmDeliveryDate <= '" . $arySearchDataColumn["dtmDeliveryDateTo"] . "' ";
					$detailFlag = TRUE;
				}
			}
		}
	}

	// 明細行の検索対応

	// 明細検索用テーブル結合条件
	$aryDetailFrom = array();
	$aryDetailFrom[] = ", (SELECT distinct on ( od1.lngOrderNo ) od1.lngOrderNo";
	$aryDetailFrom[] = "	,od1.lngOrderDetailNo";
	$aryDetailFrom[] = "	,p.strProductCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayName";
	$aryDetailFrom[] = "	,mu.struserdisplaycode";
	$aryDetailFrom[] = "	,mu.struserdisplayname";
	$aryDetailFrom[] = "	,p.strProductName";
	$aryDetailFrom[] = "	,p.strProductEnglishName";
	$aryDetailFrom[] = "	,od1.lngStockSubjectCode";	// 仕入科目
	$aryDetailFrom[] = "	,od1.lngStockItemCode";		// 仕入部品
	$aryDetailFrom[] = "	,od1.strMoldNo";			// 金型No.
	$aryDetailFrom[] = "	,p.strGoodsCode";			// 顧客品番
	$aryDetailFrom[] = "	,od1.lngDeliveryMethodCode";// 運搬方法
	$aryDetailFrom[] = "	,od1.dtmDeliveryDate";		// 納期
	$aryDetailFrom[] = "	,od1.curProductPrice";		// 単価
	$aryDetailFrom[] = "	,od1.lngProductUnitCode";	// 単位
	$aryDetailFrom[] = "	,od1.lngProductQuantity";	// 数量
	$aryDetailFrom[] = "	,od1.curSubTotalPrice";		// 税抜金額
	$aryDetailFrom[] = "	,od1.strNote";				// 明細備考
	$aryDetailFrom[] = "	FROM t_OrderDetail od1";
	$aryDetailFrom[] = "		LEFT JOIN m_Product p ON od1.strProductCode = p.strProductCode";
	$aryDetailFrom[] = "		left join m_group mg on p.lnginchargegroupcode = mg.lnggroupcode";
	$aryDetailFrom[] = "		left join m_user  mu on p.lnginchargeusercode = mu.lngusercode";
	$aryDetailFrom[] = "		left join m_tax  mt on mt.lngtaxcode = od1.lngtaxcode";

	$aryDetailWhereQuery[] = ") as od";
	// where句（明細行） クエリー連結
	$strDetailQuery = implode("\n", $aryDetailFrom) . "\n";
	// 明細行の検索対応
	if ( $detailFlag )
	{
		$strDetailQuery .= implode("\n", $aryDetailTargetQuery) . "\n";
	}
	$strDetailQuery .= implode("\n", $aryDetailWhereQuery) . "\n";


	// SQL文の作成
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT o.lngOrderNo as lngOrderNo";
	$aryOutQuery[] = "	,o.lngRevisionNo as lngRevisionNo";
	$aryOutQuery[] = "	,o.strOrderCode as strOrderCode";
	$aryOutQuery[] = "	,o.lngOrderStatusCode as lngOrderStatusCode";

	// 明細行の 'order by' 用に追加
	$aryOutQuery[] = "	,od.lngOrderDetailNo";


	// select句 クエリー連結
	$aryOutQuery[] = implode("\n", $arySelectQuery);

	// From句 の生成
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Order o";
	
	// 追加表示用の参照マスタ対応
	if ( $flgInputUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User input_u ON o.lngInputUserCode = input_u.lngUserCode";
	}
	if ( $flgCustomerCompany )
	{
		$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON o.lngCustomerCompanyCode = cust_c.lngCompanyCode";
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
		$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON o.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	}
	if ( $flgWorkFlowStatus )
	{
		$aryFromQuery[] = " left join
		( m_workflow mw
			left join t_workflow tw
			on mw.lngworkflowcode = tw.lngworkflowcode
			and tw.lngworkflowsubcode = (select max(lngworkflowsubcode) from t_workflow where lngworkflowcode = tw.lngworkflowcode)
		) on  mw.strworkflowkeycode = trim(to_char(o.lngOrderNo, '9999999'))
			and mw.lngfunctioncode = " . DEF_FUNCTION_PO1; // 発注登録時のWFデータを対象にする為に条件指定
		
		$aryFromQuery[] = "
		 AND o.bytInvalidFlag = FALSE AND o.lngRevisionNo >= 0
		 AND o.lngRevisionNo = ( SELECT MAX( o1.lngRevisionNo ) FROM m_Order o1 WHERE o1.strOrderCode = o.strOrderCode AND o1.bytInvalidFlag = false )
		 AND o.strReviseCode = ( SELECT MAX( o2.strReviseCode ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode AND o2.bytInvalidFlag = false )
		 AND 0 <= ( SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode )";
		
	}
	
	// From句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryFromQuery);

	// Where句 クエリー連結
	$aryOutQuery[] = $strDetailQuery;
	
	// Where句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryQuery);

	// 明細行条件があった場合の 条件連結
	$aryOutQuery[] = " AND od.lngOrderNo = o.lngOrderNo";


	/////////////////////////////////////////////////////////////
	//// 最新発注（リビジョン番号が最大、リバイズ番号が最大、////
	//// かつリビジョン番号負の値で無効フラグがFALSEの       ////
	//// 同じ発注コードを持つデータが無い発注データ          ////
	/////////////////////////////////////////////////////////////
	// 発注コードが指定されていない場合は検索条件を設定する
	if ( !$strOrderCode )
	{
		$aryOutQuery[] = " AND o.lngRevisionNo = ( "
			. "SELECT MAX( o1.lngRevisionNo ) FROM m_Order o1 WHERE o1.strOrderCode = o.strOrderCode AND o1.bytInvalidFlag = false )";
		$aryOutQuery[] = " AND o.strReviseCode = ( "
			. "SELECT MAX( o2.strReviseCode ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode AND o2.bytInvalidFlag = false )";

		// 管理モードの場合は削除データも検索対象とするため以下の条件は対象外
		if ( !$arySearchDataColumn["Admin"] )
		{
			$aryOutQuery[] = " AND 0 <= ( "
				. "SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode )";
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
function fncGetOrderToProductSQL ( $aryDetailViewColumn, $lngOrderNo, $aryData, $objDB )
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
			$arySelectQuery[] = ", od.strProductCode as strProductCode";
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
	$aryQuery[] = " WHERE od.lngOrderNo = " . $lngOrderNo . "";

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
	$aryFromQuery[] = " LEFT JOIN m_Product p USING (strProductCode)";
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
					// 発注データが削除対象の場合、詳細表示ボタンは選択不可
					if ( $aryHeadResult["lngrevisionno"] >= 0 )
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngorderno=\"" . $aryDetailResult[$i]["lngorderno"] . "\" class=\"detail button\"></td>\n";
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
					if ( $aryHeadResult["lngorderstatuscode"] == DEF_ORDER_TEMPORARY )
					{
						$aryHtml[] = "\t<td></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td>確定ボタン置く</td>\n";
					}
				}

				// 確定取消
				if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
				{
					//リバイズが存在しない場合
					if ( $lngReviseTotalCount == 1 )
					{
						// 発注データの状態により分岐  //// 状態が「申請中」「納品中」「納品済」「締め済」の場合削除ボタンを選択不可
						// 最新発注が削除データの場合も選択不可
						if ( $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_CLOSED 
							and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_DELIVER and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_END 
							and ( !$aryNewResult or $aryNewResult["lngrevisionno"] >= 0 ) )
						{
							$aryHtml[] = "\t<td>確定取消ボタン置く</td>\n";
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
							if ( $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and $aryHeadResult["lngorderstatuscode"] != DEF_ORDER_CLOSED 
								and ( !$aryNewResult or $aryNewResult["lngrevisionno"] >= 0 ) )
							{
								$aryHtml[] = "\t<td>確定取消ボタン置く</td>\n";
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
				else if ( $strColumnName == "dtmOrderAppDate" )
				{
					$TdData .= str_replace( "-", "/", $aryHeadResult["dtmorderappdate"] );
				}
				// 発注NO
				else if ( $strColumnName == "strOrderCode" )
				{
					$TdData .= $aryHeadResult["strordercode"];
					// 管理モードの場合　リビジョン番号を表示する
					if ( $aryData["Admin"] )
					{
						$TdData .= "</td>\n\t<td>" . $aryHeadResult["lngrevisionno"];
					}
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
				else if ( $strColumnName == "lngOrderStatusCode" )
				{
					$TdData .= $aryHeadResult["strorderstatusname"];
				}
				// 支払条件
				else if ( $strColumnName == "lngPayConditionCode" )
				{
					$TdData .= $aryHeadResult["strpayconditionname"];
				}
				// 発注有効期限日
				else if ( $strColumnName == "dtmExpirationDate" )
				{
					$TdData .= str_replace( "-", "/", $aryHeadResult["dtmexpirationdate"] );
				}
				// 明細行番号
				else if ( $strColumnName == "lngRecordNo" )
				{
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
								$aryStockItem[0] = $strStockItemName;
								$objCache->SetValue("lngstocksubjectcode:lngstockitemcode", $strSubjectItem, $aryStockItem);
							}
							$strText .= " " . $aryStockItem[0];
						}
					}
					else
					{
						$strText .= "      ";
						$strText .= " " . $aryDetailResult[$i]["strstockitemname"];
					}
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
					$TdData .= $strText;
				}
	// 2004.04.21 suzukaze update start
				// 納期
				else if ( $strColumnName == "dtmDeliveryDate" )
				{
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
function fncSetPurchaseTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName )
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
		// 管理モードの場合　同じ発注コードの一覧を取得し表示する

		// リバイズコード無しの発注コードを取得する
		if ( strlen($aryResult[$i]["strordercode"]) >= 9)
		{
			$strOrderCodeBase = preg_replace( "/" . strstr( $aryResult[$i]["strordercode"] . "/", "-" ), "", $aryResult[$i]["strordercode"] );
		}
		else
		{
			$strOrderCodeBase = $aryResult[$i]["strordercode"];
		}

		$strSameOrderCodeQuery = fncGetSearchPurchaseSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $strOrderCodeBase, $aryResult[$i]["lngorderno"], FALSE );

		// 値をとる =====================================
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameOrderCodeQuery, $objDB );

		// 配列のクリア
		unset( $arySameOrderCodeResult );

		if ( $lngResultNum )
		{
			for ( $j = 0; $j < $lngResultNum; $j++ )
			{
				$arySameOrderCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
			}
			$lngSameOrderCount = $lngResultNum;
		}
		$objDB->freeResult( $lngResultID );

		// 同じ発注コードでの過去リバイズデータが存在すれば
		if ( $lngResultNum )
		{
			for ( $j = 0; $j < $lngSameOrderCount; $j++ )
			{
				// 検索結果部分の設定

				reset( $arySameOrderCodeResult[$j] );

				// 明細出力用の調査
				$lngDetailViewCount = count( $aryDetailViewColumn );

				if ( $lngDetailViewCount )
				{
					// 明細行数の調査
					$strDetailQuery = fncGetOrderToProductSQL ( $aryDetailViewColumn, $arySameOrderCodeResult[$j]["lngorderno"], $aryData, $objDB );

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
				$aryHtml_add = fncSetPurchaseHeadTable ( $lngColumnCount, $arySameOrderCodeResult[$j], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameOrderCount, $j, $arySameOrderCodeResult[0] );
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