<?
// ----------------------------------------------------------------------------
/**
*       仕入管理  検索関連関数群
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
* 検索項目から一致する最新の仕入データを取得するSQL文の作成関数
*
*	検索項目から SQL文を作成する
*
*	@param  Array 	$aryViewColumn 			表示対象カラム名の配列
*	@param  Array 	$arySearchColumn 		検索対象カラム名の配列
*	@param  Array 	$arySearchDataColumn 	検索内容の配列
*	@param  Object	$objDB       			DBオブジェクト
*	@param	String	$strStockCode			仕入コード	空白指定時:検索結果出力	仕入コード指定時:管理用、同じ仕入コードの一覧取得
*	@param	Integer	$lngStockNo				仕入Ｎｏ	0:検索結果出力	仕入Ｎｏ指定時:管理用、同じ仕入コードとする時の対象外仕入NO
*	@param	Boolean	$bytAdminMode			有効な削除データの取得用フラグ	FALSE:検索結果出力	TRUE:管理用、削除データ取得
*	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
*	@access public
*/
function fncGetSearchStockSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strStockCode, $lngStockNo, $bytAdminMode )
{

	// 表示用カラムに設定されている内容を検索用に文字列設定
	for ( $i = 0; $i < count($aryViewColumn); $i++ )
	{
		$strViewColumnName = $aryViewColumn[$i];

		// 表示項目　管理モードの過去リビジョンデータ、および、明細情報は検索結果より取得

		// 登録日
		if ( $strViewColumnName == "dtmInsertDate" )
		{
			$arySelectQuery[] = ", to_char( s.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmInsertDate";
		}

		// 計上日
		if ( $strViewColumnName == "dtmStockAppDate" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", to_char( s.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmStockAppDate";
		}

		// 仕入Ｎｏ
		if ( $strViewColumnName == "strStockCode" )
		{
			$arySelectQuery[] = ", s.strStockCode as strStockCode";
		}

		// 発注Ｎｏ
		if ( $strViewColumnName == "strOrderCode" )
		{
			$arySelectQuery[] = ", o.strOrderCode || '-' || o.strReviseCode as strOrderCode";
			$flgOrder = TRUE;
		}

		// 伝票コード
		if ( $strViewColumnName == "strSlipCode" )
		{
			$arySelectQuery[] = ", s.strSlipCode as strSlipCode";
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
		if ( $strViewColumnName == "lngStockStatusCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", s.lngStockStatusCode as lngStockStatusCode";
			$arySelectQuery[] = ", ss.strStockStatusName as strStockStatusName";
			$flgStockStatus = TRUE;
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
			$arySelectQuery[] = ", s.lngPayConditionCode as lngPayConditionCode";
			$arySelectQuery[] = ", pc.strPayConditionName as strPayConditionName";
			$flgPayCondition = TRUE;
		}

		// 仕入有効期限日
		if ( $strViewColumnName == "dtmExpirationDate" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", to_char( s.dtmExpirationDate, 'YYYY/MM/DD' ) as dtmExpirationDate";
		}

		// 備考
		if ( $strViewColumnName == "strNote" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", s.strNote as strNote";
		}

		// 合計金額
		if ( $strViewColumnName == "curTotalPrice" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
			$arySelectQuery[] = ", To_char( s.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
		}
	}

	//
	$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
	$flgMonetaryUnit = TRUE;


	// 条件の追加
	$detailFlag = FALSE;

	// 管理モードの検索時、同じ仕入コードのデータを取得する場合
	if ( $strStockCode or $bytAdminMode )
	{
		// 同じ仕入コードに対して指定の仕入番号のデータは除外する
		if ( $lngStockNo )
		{
			$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.strStockCode = '" . $strStockCode . "'";
		}
		else
		{
			fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}

		// 削除データ取得時は条件追加
		if ( $bytAdminMode )
		{
			$aryQuery[] = " AND s.lngRevisionNo < 0";
		}
	}

	// 管理モードでの同じ仕入コードに対する検索モード以外の場合は検索条件を追加する
	else
	{
		// 絶対条件 無効フラグが設定されておらず、最新仕入のみ
		$aryQuery[] = " WHERE s.bytInvalidFlag = FALSE AND s.lngRevisionNo >= 0";

		// 表示用カラムに設定されている内容を検索用に文字列設定
		for ( $i = 0; $i < count($arySearchColumn); $i++ )
		{
			$strSearchColumnName = $arySearchColumn[$i];

			// ////仕入マスタ内の検索条件////
			// 登録日
			if ( $strSearchColumnName == "dtmInsertDate" )
			{
				if ( $arySearchDataColumn["dtmInsertDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmInsertDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND s.dtmInsertDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmInsertDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmInsertDateTo"] . " 23:59:59";
					$aryQuery[] = " AND s.dtmInsertDate <= '" . $dtmSearchDate . "'";
				}
			}
			// 計上日
			if ( $strSearchColumnName == "dtmStockAppDate" )
			{
				if ( $arySearchDataColumn["dtmStockAppDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmStockAppDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND s.dtmAppropriationDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmStockAppDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmStockAppDateTo"] . " 23:59:59";
					$aryQuery[] = " AND s.dtmAppropriationDate <= '" . $dtmSearchDate . "'";
				}
			}
			// 仕入Ｎｏ
			if ( $strSearchColumnName == "strStockCode" )
			{
				if ( $arySearchDataColumn["strStockCodeFrom"] )
				{
					$aryQuery[] = " AND s.strStockCode >= '" . $arySearchDataColumn["strStockCodeFrom"] . "'";
				}
				if ( $arySearchDataColumn["strStockCodeTo"] )
				{
					$aryQuery[] = " AND s.strStockCode <= '" . $arySearchDataColumn["strStockCodeTo"] . "'";
				}
			}
			// 発注Ｎｏ
			if ( $strSearchColumnName == "strOrderCode" )
			{
				if ( $arySearchDataColumn["strOrderCodeFrom"] )
				{
					if ( strpos($arySearchDataColumn["strOrderCodeFrom"], "-") )
					{
						// リバイズコード付の仕入Ｎｏのリバイズコードは検索結果では最新版を表示するため、無視する
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
						// リバイズコード付の仕入Ｎｏのリバイズコードは検索結果では最新版を表示するため、無視する
						$strNewOrderCode = ereg_replace( strstr( $arySearchDataColumn["strOrderCodeTo"], "-" ), "", $arySearchDataColumn["strOrderCodeTo"] );
					}
					else
					{
						$strNewStockCode = $arySearchDataColumn["strOrderCodeTo"];
					}
					$aryQuery[] = " AND o.strOrderCode <= '" . $strNewOrderCode . "'";
				}
				$flgOrder = TRUE;
			}
			// 伝票コード
			if ( $strSearchColumnName == "strSlipCode" )
			{
				if ( $arySearchDataColumn["strSlipCode"] )
				{
					$aryQuery[] = " AND UPPER(s.strSlipCode) LIKE UPPER('%" . $arySearchDataColumn["strSlipCode"] . "%')";
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
					$aryQuery[] = " AND UPPER(input_u.strUserDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInputUserName"] . "%')";
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
					$aryQuery[] = " AND UPPER(cust_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strCustomerName"] . "%')";
					$flgCustomerCompany = TRUE;
				}
			}
			// 状態
			if ( $strSearchColumnName == "lngStockStatusCode" )
			{
				if ( $arySearchDataColumn["lngStockStatusCode"] )
				{
					// 仕入状態は ","区切りの文字列として渡される
					//$arySearchStatus = explode( ",", $arySearchDataColumn["lngStockStatusCode"] );
					// チェックボックス化により、配列をそのまま代入
					$arySearchStatus = $arySearchDataColumn["lngStockStatusCode"];
					if ( is_array( $arySearchStatus ) )
					{
						$aryQuery[] = " AND ( ";
						// 仕入状態は複数設定されている可能性があるので、設定個数分ループ
						for ( $j = 0; $j < count($arySearchStatus); $j++ )
						{
							// 初回処理
							if ( $j <> 0 )
							{
								$aryQuery[] = " OR ";
							}
							$aryQuery[] = "s.lngStockStatusCode = " . $arySearchStatus[$j] . "";
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
					$aryQuery[] = " AND s.lngPayConditionCode = " . $arySearchDataColumn["lngPayConditionCode"] . "";
				}
			}
			// 製品到着日
			if ( $strSearchColumnName == "dtmExpirationDate" )
			{
				if ( $arySearchDataColumn["dtmExpirationDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmExpirationDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND s.dtmExpirationDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmExpirationDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmExpirationDateTo"] . " 23:59:59";
					$aryQuery[] = " AND s.dtmExpirationDate <= '" . $dtmSearchDate . "'";
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
					$aryDetailWhereQuery[] = "sd1.strProductCode >= '" . $arySearchDataColumn["strProductCodeFrom"] . "'";
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
					$aryDetailWhereQuery[] = "sd1.strProductCode <= '" . $arySearchDataColumn["strProductCodeTo"] . "' ";
					$detailFlag = TRUE;
				}
			}
			// 部門
			if ( $strSearchColumnName == "lngInChargeGroupCode" )
			{
				if( $arySearchDataColumn["lngInChargeGroupCode"] || $arySearchDataColumn["strInChargeGroupName"])
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
				if( $arySearchDataColumn["lngInChargeUserCode"] || $arySearchDataColumn["strInChargeUserName"])
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
					$aryDetailWhereQuery[] = "UPPER(p.strProductName) LIKE UPPER('%" . $arySearchDataColumn["strProductName"] . "%') ";
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
					$aryDetailWhereQuery[] = "UPPER(p.strProductEnglishName) LIKE UPPER('%" . $arySearchDataColumn["strProductEnglishName"] . "%') ";
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
					$aryDetailWhereQuery[] = "sd1.lngStockSubjectCode = " . $arySearchDataColumn["lngStockSubjectCode"] . " ";
					$StockSubjectFlag = TRUE;
					$detailFlag = TRUE;
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
					$aryDetailWhereQuery[] = "sd1.lngStockItemCode = " . $arySearchDataColumn["lngStockItemCode"] . " ";
					if ( $StockSubjectFlag != TRUE )
					{
						$aryDetailWhereQuery[] = "AND sd1.lngStockSubjectCode = " . $arySearchDataColumn["lngStockSubjectCode"] . " ";
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
					$aryDetailWhereQuery[] = "sd1.dtmDeliveryDate >= '" . $arySearchDataColumn["dtmDeliveryDateFrom"] . "' ";
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
					$aryDetailWhereQuery[] = "sd1.dtmDeliveryDate <= '" . $arySearchDataColumn["dtmDeliveryDateTo"] . "' ";
					$detailFlag = TRUE;
				}
			}
		}
	}



	// 明細行の検索対応

	// 明細検索用テーブル結合条件
	$aryDetailFrom = array();
	$aryDetailFrom[] = ", (SELECT distinct on ( sd1.lngStockNo ) sd1.lngStockNo";
	$aryDetailFrom[] = "	,sd1.lngStockDetailNo";
	$aryDetailFrom[] = "	,p.strProductCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayName";
	$aryDetailFrom[] = "	,mu.struserdisplaycode";
	$aryDetailFrom[] = "	,mu.struserdisplayname";
	$aryDetailFrom[] = "	,p.strProductName";
	$aryDetailFrom[] = "	,p.strProductEnglishName";
	$aryDetailFrom[] = "	,sd1.lngStockSubjectCode";	// 仕入科目
	$aryDetailFrom[] = "	,sd1.lngStockItemCode";		// 仕入部品
	$aryDetailFrom[] = "	,sd1.strMoldNo";			// 金型No.
	$aryDetailFrom[] = "	,p.strGoodsCode";			// 顧客品番
	$aryDetailFrom[] = "	,sd1.lngDeliveryMethodCode";// 運搬方法
	$aryDetailFrom[] = "	,sd1.dtmDeliveryDate";		// 納期
	$aryDetailFrom[] = "	,sd1.curProductPrice";		// 単価
	$aryDetailFrom[] = "	,sd1.lngProductUnitCode";	// 単位
	$aryDetailFrom[] = "	,sd1.lngProductQuantity";	// 数量
	$aryDetailFrom[] = "	,sd1.curSubTotalPrice";		// 税抜金額
	$aryDetailFrom[] = "	,sd1.lngTaxClassCode";		// 税区分
	$aryDetailFrom[] = "	,mt.curtax";				// 税率
	$aryDetailFrom[] = "	,sd1.curtaxprice";			// 税額
	$aryDetailFrom[] = "	,sd1.strNote";				// 明細備考
	$aryDetailFrom[] = "	FROM t_StockDetail sd1";
	$aryDetailFrom[] = "		LEFT JOIN m_Product p ON sd1.strProductCode = p.strProductCode";
	$aryDetailFrom[] = "		left join m_group mg on p.lnginchargegroupcode = mg.lnggroupcode";
	$aryDetailFrom[] = "		left join m_user  mu on p.lnginchargeusercode = mu.lngusercode";
	$aryDetailFrom[] = "		left join m_tax  mt on mt.lngtaxcode = sd1.lngtaxcode";


	$aryDetailWhereQuery[] = ") as sd";
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
	$aryOutQuery[] = "SELECT s.lngStockNo as lngStockNo";
	$aryOutQuery[] = "	,s.lngRevisionNo as lngRevisionNo";
	$aryOutQuery[] = "	,s.lngStockStatusCode as lngStockStatusCode";

	// 明細行の 'order by' 用に追加
	$aryOutQuery[] = "	,sd.lngStockDetailNo";


	// select句 クエリー連結
	$aryOutQuery[] = implode("\n", $arySelectQuery);

	// From句 の生成
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Stock s";

	// 追加表示用の参照マスタ対応
	if ( $flgOrder )
	{
		$aryFromQuery[] = " LEFT JOIN m_Order o USING (lngOrderNo)";
	}
	if ( $flgInputUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User input_u ON s.lngInputUserCode = input_u.lngUserCode";
	}
	if ( $flgCustomerCompany )
	{
		$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode";
	}
	if ( $flgStockStatus )
	{
		$aryFromQuery[] = " LEFT JOIN m_StockStatus ss USING (lngStockStatusCode)";
	}
	if ( $flgPayCondition )
	{
		$aryFromQuery[] = " LEFT JOIN m_PayCondition pc ON s.lngPayConditionCode = pc.lngPayConditionCode";
	}
	if ( $flgMonetaryUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	}
	if ( $flgWorkFlowStatus )
	{
		$aryFromQuery[] = " left join
		( m_workflow mw
			left join t_workflow tw
			on mw.lngworkflowcode = tw.lngworkflowcode
			and tw.lngworkflowsubcode = (select max(lngworkflowsubcode) from t_workflow where lngworkflowcode = tw.lngworkflowcode)
		) on  mw.strworkflowkeycode = trim(to_char(s.lngStockNo, '9999999'))
			and mw.lngfunctioncode = " . DEF_FUNCTION_PC1; // 仕入登録時のWFデータを対象にする為に条件指定
	}
	
	// From句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryFromQuery);
	
	// Where句 クエリー連結
	$aryOutQuery[] = $strDetailQuery;
	
	// Where句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryQuery);

	// 明細行条件があった場合の 条件連結
	$aryOutQuery[] = " AND sd.lngStockNo = s.lngStockNo";


	/////////////////////////////////////////////////////////////
	//// 最新仕入（リビジョン番号が最大、リバイズ番号が最大、////
	//// かつリビジョン番号負の値で無効フラグがFALSEの       ////
	//// 同じ仕入コードを持つデータが無い仕入データ          ////
	/////////////////////////////////////////////////////////////
	// 仕入コードが指定されていない場合は検索条件を設定する
	if ( !$strStockCode )
	{
		$aryOutQuery[] = " AND s.lngRevisionNo = ( "
			. "SELECT MAX( s1.lngRevisionNo ) FROM m_Stock s1 WHERE s1.strStockCode = s.strStockCode AND s1.bytInvalidFlag = false )";

		// 管理モードの場合は削除データも検索対象とするため以下の条件は対象外
		if ( !$arySearchDataColumn["Admin"] )
		{
			$aryOutQuery[] = " AND 0 <= ( "
				. "SELECT MIN( s2.lngRevisionNo ) FROM m_Stock s2 WHERE s2.bytInvalidFlag = false AND s2.strStockCode = s.strStockCode )";
		}
	}

	// 管理モードの検索時、同じ仕入コードのデータを取得する場合
	if ( $strStockCode or $bytAdminMode )
	{
		$aryOutQuery[] = " ORDER BY s.dtmInsertDate DESC";
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
			case "strStockCode":
			case "strSlipCode":
			case "lngStockStatusCode":
			case "lngPayConditionCode":
			case "dtmExpirationDate":
			case "strNote":
			case "curTotalPrice":
				$aryOutQuery[] = " ORDER BY s." . $arySearchDataColumn["strSort"] . " " . $strAsDs . ", s.lngStockNo DESC";
				break;
			case "dtmAppropriationDate":
				$aryOutQuery[] = " ORDER BY dtmStockAppDate" . $strAsDs . ", lngStockNo DESC";
				break;
			case "strOrderCode":
				$aryOutQuery[] = " ORDER BY strOrderCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngInputUserCode":
				$aryOutQuery[] = " ORDER BY strInputUserDisplayCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngCustomerCompanyCode":
				$aryOutQuery[] = " ORDER BY strCustomerDisplayCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngWorkFlowStatusCode":
				$aryOutQuery[] = " ORDER BY lngWorkFlowStatusCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngStockDetailNo":	// 明細行番号
				$aryOutQuery[] = " ORDER BY sd.lngStockDetailNo" . $strAsDs . ", lngStockNo DESC";
				break;
			case "strProductCode":		// 製品コード
				$aryOutQuery[] = " ORDER BY sd.strProductCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngGroupCode":		// 部門
				$aryOutQuery[] = " ORDER BY sd.strGroupDisplayCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngUserCode":			// 担当者
				$aryOutQuery[] = " ORDER BY sd.strUserDisplayCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "strProductName":		// 製品名称
				$aryOutQuery[] = " ORDER BY sd.strProductName" . $strAsDs . ", lngStockNo DESC";
				break;
			case "strProductEnglishName":	// 製品英語名称
				$aryOutQuery[] = " ORDER BY sd.strProductEnglishName" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngStockSubjectCode":	// 仕入科目
				$aryOutQuery[] = " ORDER BY sd.lngStockSubjectCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngStockItemCode":	// 仕入部品
				$aryOutQuery[] = " ORDER BY sd.lngStockItemCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "strMoldNo":			// 金型No.
				$aryOutQuery[] = " ORDER BY sd.strMoldNo" . $strAsDs . ", lngStockNo DESC";
				break;
			case "strGoodsCode":		// 顧客品番
				$aryOutQuery[] = " ORDER BY sd.strGoodsCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngDeliveryMethodCode":// 運搬方法
				$aryOutQuery[] = " ORDER BY sd.lngDeliveryMethodCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "dtmDeliveryDate":		// 納期
				$aryOutQuery[] = " ORDER BY sd.dtmDeliveryDate" . $strAsDs . ", lngStockNo DESC";
				break;
			case "curProductPrice":		// 単価
				$aryOutQuery[] = " ORDER BY sd.curProductPrice" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngProductUnitCode":	// 単位
				$aryOutQuery[] = " ORDER BY sd.lngProductUnitCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngProductQuantity":	// 数量
				$aryOutQuery[] = " ORDER BY sd.lngProductQuantity" . $strAsDs . ", lngStockNo DESC";
				break;
			case "curSubTotalPrice":	// 税抜金額
				$aryOutQuery[] = " ORDER BY sd.curSubTotalPrice" . $strAsDs . ", lngStockNo DESC";
				break;
			case "lngTaxClassCode":		// 税区分
				$aryOutQuery[] = " ORDER BY sd.lngTaxClassCode" . $strAsDs . ", lngStockNo DESC";
				break;
			case "curTax":				// 税率
				$aryOutQuery[] = " ORDER BY sd.curTax" . $strAsDs . ", lngStockNo DESC";
				break;
			case "curTaxPrice":			// 税額
				$aryOutQuery[] = " ORDER BY sd.curTaxPrice" . $strAsDs . ", lngStockNo DESC";
				break;
			case "strDetailNote":		// 明細備考
				$aryOutQuery[] = " ORDER BY sd.strNote" . $strAsDs . ", lngStockNo DESC";
				break;
			default:
				$aryOutQuery[] = " ORDER BY s.lngStockNo DESC";
		}
	}

//fncDebug( 'lib_pcs.txt', implode("\n", $aryOutQuery), __FILE__, __LINE__);
//fncDebug( 'lib_pcs.txt', fncGetSearchStockSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strStockCode, $lngStockNo, $bytAdminMode ), __FILE__, __LINE__);

	return implode("\n", $aryOutQuery);
}



/**
* 対応する仕入NOのデータに対する明細行を取得するSQL文の作成関数
*
*	仕入NOから明細を取得する SQL文を作成する
*
*	@param  Array 	$aryDetailViewColumn 	表示対象明細カラム名の配列
*	@param  String 	$lngStockNo 			対象仕入NO
*	@param  Array 	$aryData 				POSTデータの配列
*	@param  Object	$objDB       			DBオブジェクト
*	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
*	@access public
*/
function fncGetStockToProductSQL ( $aryDetailViewColumn, $lngStockNo, $aryData, $objDB )
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
		// 仕入科目
		if ( $strViewColumnName == "lngStockSubjectCode" )
		{
			$arySelectQuery[] = ", sd.lngStockSubjectCode as lngStockSubjectCode";
			$arySelectQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
			$flgStockSubject = TRUE;
		}
		// 仕入部品
		if ( $strViewColumnName == "lngStockItemCode" )
		{
			$arySelectQuery[] = ", sd.lngStockItemCode as lngStockItemCode";
			$flgStockItem = TRUE;
		}
		// 金型番号
		if ( $strViewColumnName == "strMoldNo" )
		{
			$arySelectQuery[] = ", sd.strMoldNo as strMoldNo";
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
			$arySelectQuery[] = ", sd.lngDeliveryMethodCode as lngDeliveryMethodCode";
			$arySelectQuery[] = ", dm.strDeliveryMethodName as strDeliveryMethodName";
			$flgDeliveryMethod = TRUE;
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

	// 仕入部品のみ表示対象だった時は仕入科目についてもデータを取得する
	if ( $flgStockItem == TRUE and $flgStockSubject == FALSE )
	{
		$arySelectQuery[] = ", sd.lngStockSubjectCode as lngStockSubjectCode";
		$arySelectQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
		$flgStockSubject = TRUE;
	}

	// 絶対条件 対象仕入NOの指定
	$aryQuery[] = " WHERE sd.lngStockNo = " . $lngStockNo . "";

	// 条件の追加

	// ////仕入マスタ内の検索条件////
	// SQL文の作成
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT sd.lngSortKey as lngRecordNo";
	$aryOutQuery[] = "	,sd.lngStockNo as lngStockNo";
	$aryOutQuery[] = "	,sd.lngRevisionNo as lngRevisionNo";

	// select句 クエリー連結
	if( !empty($arySelectQuery) )
	{
		$aryOutQuery[] = implode("\n", $arySelectQuery);
	}

	// From句 の生成
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM t_StockDetail sd";

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
		$strAsDs = " DESC";	// ヘッダ項目とは逆順にする
	}
	else
	{
		$strAsDs = " ASC";	//降順
	}
	switch($aryData["strSort"])
	{
		case "strDetailNote":
			$aryOutQuery[] = " ORDER BY sd.strNote" . $strAsDs . ", sd.lngSortKey ASC";
			break;
		case "lngStockDetailNo":
			$aryOutQuery[] = " ORDER BY sd.lngSortKey" . $strAsDs;
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
*	@param  Array 	$aryHeadViewColumn 		ヘッダ表示対象カラム名の配列
*	@param  Array 	$aryDetailViewColumn 	明細表示対象カラム名の配列
*	@param  Array 	$aryData 				ＰＯＳＴデータ群
*	@param	Array	$aryUserAuthority		ユーザーの操作に対する権限が入った配列
*	@access public
*/
function fncSetStockHeadTable ( $lngColumnCount, $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngReviseTotalCount, $lngReviseCount, $bytDeleteFlag )
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
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngstockno=\"" . $aryDetailResult[$i]["lngstockno"] . "\" class=\"detail button\"></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td></td>\n";
					}
				}

				// 修正
				if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
				{
					// 仕入データの状態により分岐  //// 状態が「締め済」、また削除対象の場合修正ボタンは選択不可
					// 最新仕入が削除データの場合も選択不可
					if ( $aryHeadResult["lngstockstatuscode"] == DEF_STOCK_CLOSED 
	// 2004.03.01 Suzukaze update start
	//					or ( $aryHeadResult["lngstockstatuscode"] == DEF_STOCK_END and !$aryData["Admin"] ) 
	// 2004.03.01 Suzukaze update end
						or $aryHeadResult["lngrevisionno"] < 0 
						or $bytDeleteFlag )
					{
						$aryHtml[] = "\t<td></td>\n";
					}
					else
					{
						$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/renew_off_bt.gif\" lngstockno=\"" . $aryDetailResult[$i]["lngstockno"] . "\" class=\"detail button\"></td>\n";
					}
				}

				// 削除
				if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
				{
					// 管理モードで無い場合もしくはリバイズが存在しない場合
					if ( !$aryData["Admin"] or $lngReviseTotalCount == 1 )
					{
						// 仕入データの状態により分岐  //// 状態が「締め済」の場合削除ボタンを選択不可
						// 最新発注が削除データの場合も選択不可
						if ( $aryHeadResult["lngstockstatuscode"] != DEF_STOCK_CLOSED 
	// 2004.03.01 Suzukaze update start
	//						and $aryHeadResult["lngstockstatuscode"] != DEF_STOCK_END 
	// 2004.03.01 Suzukaze update end
							and !$bytDeleteFlag )

						{
							$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngstockno=\"" . $aryDetailResult[$i]["lngstockno"] . "\" class=\"detail button\"></td>\n";
						}
						else
						{
							$aryHtml[] = "\t<td></td>\n";
						}
					}
					// 管理モードで複数リバイズが存在する場合
					else
					{
						// 最新仕入の場合
						if ( $lngReviseCount == 0 )
						{
							// 仕入データの状態により分岐  //// 状態が「締め済」の場合削除ボタンを選択不可
							// 最新発注が削除データの場合も選択不可
							if ( $aryHeadResult["lngstockstatuscode"] != DEF_STOCK_CLOSED 
								and !$bytDeleteFlag )
							{
								$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngstockno=\"" . $aryDetailResult[$i]["lngstockno"] . "\" class=\"detail button\"></td>\n";
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
				// 無効化
				if ( $strColumnName == "btnInvalid" and $aryData["Admin"] and $aryUserAuthority["Admin"] and $aryUserAuthority["Invalid"] )
				{
					// 仕入データの状態により分岐  //// 状態が「締め済」、また削除対象の場合無効化ボタンは選択不可
					// 最新仕入が削除データの場合も選択不可
	// 2004.03.01 Suzukaze update start
					if ( $aryHeadResult["lngstockstatuscode"] == DEF_STOCK_CLOSED )
	//					or ( $aryHeadResult["lngstockstatuscode"] == DEF_STOCK_END and !$aryData["Admin"] ) )
	// 2004.03.01 Suzukaze update end
					{
						$aryHtml[] = "\t<td></td>\n";
					}
					else
					{
						//$aryHtml[] = "\t<td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/remove_off_bt.gif\" lngstockno=\"" . $aryDetailResult[$i]["lngstockno"] . "\" class=\"detail button\"></td>\n";
						$aryHtml[] = "\t<td>ボタン画像不明</td>\n";
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
				else if ( $strColumnName == "dtmStockAppDate" )
				{
					$TdData .= str_replace( "-", "/", $aryHeadResult["dtmsalesappdate"] );
				}

				// 仕入NO
				else if ( $strColumnName == "strStockCode" )
				{
					$TdData .= $aryHeadResult["strstockcode"];
					// 管理モードの場合　リビジョン番号を表示する
					if ( $aryData["Admin"] )
					{
						$TdData .= "</td>\n\t<td>" . $aryHeadResult["lngrevisionno"];
					}
				}

				// 発注NO
				else if ( $strColumnName == "strOrderCode" )
				{
					$TdData .= $aryHeadResult["strordercode"];
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

				// 仕入先
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
				else if ( $strColumnName == "lngStockStatusCode" )
				{
					$TdData .= $aryHeadResult["strstockstatusname"];
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
*	@access public
*/
function fncSetStockTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName )
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
		// 明細部
		else if ( $strColumnName == "strProductCode" 
			or $strColumnName == "lngInChargeGroupCode" or $strColumnName == "lngInChargeUserCode" // <-- added by siato
			or $strColumnName == "lngRecordNo"
			or $strColumnName == "lngStockSubjectCode" or $strColumnName == "lngStockItemCode" or $strColumnName == "strGoodsCode"
			or $strColumnName == "lngDeliveryMethodCode" or $strColumnName == "curProductPrice" or $strColumnName == "lngProductUnitCode"
			or $strColumnName == "lngProductQuantity" or $strColumnName == "curSubTotalPrice" or $strColumnName == "lngTaxClassCode"
			or $strColumnName == "curTax" or $strColumnName == "curTaxPrice" or $strColumnName == "strDetailNote" 
			or $strColumnName == "dtmDeliveryDate" 
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
		if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" or $strColumnName == "btnInvalid" )
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
		// 管理モードの場合　同じ仕入コードの一覧を取得し表示する

		$strStockCodeBase = $aryResult[$i]["strstockcode"];

		$strSameStockCodeQuery = fncGetSearchStockSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $strStockCodeBase, $aryResult[$i]["lngstockno"], FALSE );

		// 値をとる =====================================
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameStockCodeQuery, $objDB );

		// 配列のクリア
		unset( $arySameStockCodeResult );

		if ( $lngResultNum )
		{
			for ( $j = 0; $j < $lngResultNum; $j++ )
			{
				$arySameStockCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
			}
			$lngSameStockCount = $lngResultNum;
		}
		$objDB->freeResult( $lngResultID );

		// 同じ仕入コードでの過去リバイズデータが存在すれば
		if ( $lngResultNum )
		{
			for ( $j = 0; $j < $lngSameStockCount; $j++ )
			{
				// 検索結果部分の設定

				reset( $arySameStockCodeResult[$j] );

				// 明細出力用の調査
				$lngDetailViewCount = count( $aryDetailViewColumn );

				if ( $lngDetailViewCount )
				{
					// 明細行数の調査
					$strDetailQuery = fncGetStockToProductSQL ( $aryDetailViewColumn, $arySameStockCodeResult[$j]["lngstockno"], $aryData, $objDB );

					// クエリー実行
					if ( !$lngDetailResultID = $objDB->execute( $strDetailQuery ) )
					{
						$strMessage = fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
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

				// 同じコードの仕入データで一番上に表示されている発注データが削除データの場合
				if ( $arySameStockCodeResult[0]["lngrevisionno"] < 0 )
				{
					$bytDeleteFlag = TRUE;
				}
				else
				{
					$bytDeleteFlag = FALSE;
				}

				// １レコード分の出力
				$aryHtml_add = fncSetStockHeadTable ( $lngColumnCount, $arySameStockCodeResult[$j], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameStockCount, $j, $bytDeleteFlag );
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