<?
// ----------------------------------------------------------------------------
/**
*       受注管理  検索関連関数群
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
* 検索項目から一致する最新の受注データを取得するSQL文の作成関数
*
*	検索項目から SQL文を作成する
*
*	@param  Array 	$aryViewColumn 			表示対象カラム名の配列
*	@param  Array 	$arySearchColumn 		検索対象カラム名の配列
*	@param  Array 	$arySearchDataColumn 	検索内容の配列
*	@param  Object	$objDB       			DBオブジェクト
*	@param	String	$strReceiveCode			受注コード	空白指定時:検索結果出力	受注コード指定時:管理用、同じ受注コードの一覧取得
*	@param	Integer	$lngReceiveNo				受注Ｎｏ	0:検索結果出力	受注Ｎｏ指定時:管理用、同じ受注コードとする時の対象外受注NO
*	@param	Boolean	$bytAdminMode			有効な削除データの取得用フラグ	FALSE:検索結果出力	TRUE:管理用、削除データ取得
*	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
*	@access public
*/
function fncGetSearchReceiveSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strReceiveCode, $lngReceiveNo, $bytAdminMode )
{

	// 表示用カラムに設定されている内容を検索用に文字列設定
	for ( $i = 0; $i < count($aryViewColumn); $i++ )
	{
		$strViewColumnName = $aryViewColumn[$i];

		// 表示項目　管理モードの過去リビジョンデータ、および、明細情報は検索結果より取得

		// 登録日
		if ( $strViewColumnName == "dtmInsertDate" )
		{
			$arySelectQuery[] = ", to_char( r.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS' ) as dtmInsertDate";
		}

		// 計上日
		if ( $strViewColumnName == "dtmReceiveAppDate" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", to_char( r.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmReceiveAppDate";
		}

		// 顧客受注番号
		if ( $strViewColumnName == "strCustomerReceiveCode" )
		{
			$arySelectQuery[] = ", r.strCustomerReceiveCode as strCustomerReceiveCode";
		}

		// 受注Ｎｏ
		if ( $strViewColumnName == "strReceiveCode" )
		{
			$arySelectQuery[] = ", r.strReceiveCode || '-' || r.strReviseCode as strReceiveCode";
		}

		// 入力者
		if ( $strViewColumnName == "lngInputUserCode" )
		{
			$arySelectQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode";
			$arySelectQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName";
			$flgInputUser = TRUE;
		}

		// 顧客
		if ( $strViewColumnName == "lngCustomerCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
			$arySelectQuery[] = ", cust_c.strCompanyDisplayName as strCustomerDisplayName";
			$flgCustomerCompany = TRUE;
		}
		// 状態
		if ( $strViewColumnName == "lngReceiveStatusCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", r.lngReceiveStatusCode as lngReceiveStatusCode";
			$arySelectQuery[] = ", rs.strReceiveStatusName as strReceiveStatusName";
			$flgReceiveStatus = TRUE;
		}
		
		
		// ワークフロー状態
		if ( $strViewColumnName == "lngWorkFlowStatusCode" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", (select strWorkflowStatusName from m_WorkflowStatus where lngWorkflowStatusCode = tw.lngWorkflowStatusCode) as lngWorkFlowStatusCode";
			$flgWorkFlowStatus = TRUE;
		}
		

		// 備考
		if ( $strViewColumnName == "strNote" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", r.strNote as strNote";
		}

		// 合計金額
		if ( $strViewColumnName == "curTotalPrice" and !$bytAdminMode )
		{
			$arySelectQuery[] = ", To_char( r.curTotalPrice, '9,999,999,990.99' ) as curTotalPrice";
		}
	}

	// 2005.11.02 条件分岐も無く、なぜか上記ループ内に組み込まれて複数回設定されてしまっていた為、外部へ抜き出した。
	$arySelectQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
	$flgMonetaryUnit = TRUE;

	// 条件の追加
	$detailFlag = FALSE;

	// 管理モードの検索時、同じ受注コードのデータを取得する場合
	if ( $strReceiveCode or $bytAdminMode )
	{
		// 同じ受注コードに対して指定の受注番号のデータは除外する
		if ( $lngReceiveNo )
		{
			$aryQuery[] = " WHERE r.bytInvalidFlag = FALSE AND r.strReceiveCode = '" . $strReceiveCode . "'";
		}
		else
		{
			fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}

		// 削除データ取得時は条件追加
		if ( $bytAdminMode )
		{
			$aryQuery[] = " AND r.lngRevisionNo < 0";
		}
	}

	// 管理モードでの同じ受注コードに対する検索モード以外の場合は検索条件を追加する
	else
	{
		// 絶対条件 無効フラグが設定されておらず、最新受注のみ
		$aryQuery[] = " WHERE r.bytInvalidFlag = FALSE AND r.lngRevisionNo >= 0";

		// 表示用カラムに設定されている内容を検索用に文字列設定
		for ( $i = 0; $i < count($arySearchColumn); $i++ )
		{
			$strSearchColumnName = $arySearchColumn[$i];

			// ////受注マスタ内の検索条件////
			// 登録日
			if ( $strSearchColumnName == "dtmInsertDate" )
			{
				if ( $arySearchDataColumn["dtmInsertDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmInsertDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND r.dtmInsertDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmInsertDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmInsertDateTo"] . " 23:59:59";
					$aryQuery[] = " AND r.dtmInsertDate <= '" . $dtmSearchDate . "'";
				}
			}
			// 計上日
			if ( $strSearchColumnName == "dtmReceiveAppDate" )
			{
				if ( $arySearchDataColumn["dtmReceiveAppDateFrom"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmReceiveAppDateFrom"] . " 00:00:00";
					$aryQuery[] = " AND r.dtmAppropriationDate >= '" . $dtmSearchDate . "'";
				}
				if ( $arySearchDataColumn["dtmReceiveAppDateTo"] )
				{
					$dtmSearchDate = $arySearchDataColumn["dtmReceiveAppDateTo"] . " 23:59:59";
					$aryQuery[] = " AND r.dtmAppropriationDate <= '" . $dtmSearchDate . "'";
				}
			}
			// 顧客受注番号
			if ( $strSearchColumnName == "strCustomerReceiveCode" )
			{
				if ( $arySearchDataColumn["strCustomerReceiveCodeFrom"] )
				{
					$strNewCustomerReceiveCode = $arySearchDataColumn["strCustomerReceiveCodeFrom"];
					$aryQuery[] = " AND r.strCustomerReceiveCode >= '" . $strNewCustomerReceiveCode . "'";

				}
				if ( $arySearchDataColumn["strCustomerReceiveCodeTo"] )
				{
					$strNewCustomerReceiveCode = $arySearchDataColumn["strCustomerReceiveCodeTo"];
					$aryQuery[] = " AND r.strCustomerReceiveCode <= '" . $strNewCustomerReceiveCode . "'";
				}
			}
			// 受注Ｎｏ
			if ( $strSearchColumnName == "strReceiveCode" )
			{
				if ( $arySearchDataColumn["strReceiveCodeFrom"] )
				{
					if ( strpos($arySearchDataColumn["strReceiveCodeFrom"], "-") )
					{
						// リバイズコード付の受注Ｎｏのリバイズコードは検索結果では最新版を表示するため、無視する
						$strNewReceiveCode = ereg_replace( strrchr( $arySearchDataColumn["strReceiveCodeFrom"], "-" ), "", $arySearchDataColumn["strReceiveCodeFrom"] );
					}
					else
					{
						$strNewReceiveCode = $arySearchDataColumn["strReceiveCodeFrom"];
					}
					$aryQuery[] = " AND r.strReceiveCode >= '" . $strNewReceiveCode . "'";

				}
				if ( $arySearchDataColumn["strReceiveCodeTo"] )
				{
					if ( strpos($arySearchDataColumn["strReceiveCodeTo"], "-") )
					{
						// リバイズコード付の受注Ｎｏのリバイズコードは検索結果では最新版を表示するため、無視する
						$strNewReceiveCode = ereg_replace( strrchr( $arySearchDataColumn["strReceiveCodeTo"], "-" ), "", $arySearchDataColumn["strReceiveCodeTo"] );
					}
					else
					{
						$strNewReceiveCode = $arySearchDataColumn["strReceiveCodeTo"];
					}
					$aryQuery[] = " AND r.strReceiveCode <= '" . $strNewReceiveCode . "'";
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
			// 顧客
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
			// 部門
			/*
			if ( $strSearchColumnName == "lngInChargeGroupCode" )
			{
				if ( $arySearchDataColumn["lngInChargeGroupCode"] )
				{
					$aryQuery[] = " AND inchg_g.strGroupDisplayCode ~* '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'";
					$flgInChargeGroup = TRUE;
				}
				if ( $arySearchDataColumn["strInChargeGroupName"] )
				{
					$aryQuery[] = " AND UPPER(inchg_g.strGroupDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeGroupName"] . "%')";
					$flgInChargeGroup = TRUE;
				}
			}
			*/
			// 担当者
			/*
			if ( $strSearchColumnName == "lngInChargeUserCode" )
			{
				if ( $arySearchDataColumn["lngInChargeUserCode"] )
				{
					$aryQuery[] = " AND inchg_u.strUserDisplayCode ~* '" . $arySearchDataColumn["lngInChargeUserCode"] . "'";
					$flgInChargeUser = TRUE;
				}
				if ( $arySearchDataColumn["strInChargeUserName"] )
				{
					$aryQuery[] = " AND UPPER(inchg_u.strUserDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeUserName"] . "%')";
					$flgInChargeUser = TRUE;
				}
			}
			*/

			// 状態
			if ( $strSearchColumnName == "lngReceiveStatusCode" )
			{
				if ( $arySearchDataColumn["lngReceiveStatusCode"] )
				{
					// 受注状態は ","区切りの文字列として渡される
					//$arySearchStatus = explode( ",", $arySearchDataColumn["lngReceiveStatusCode"] );
					// チェックボックス化により、配列をそのまま代入
					$arySearchStatus = $arySearchDataColumn["lngReceiveStatusCode"];
					
					if ( is_array( $arySearchStatus ) )
					{
						$aryQuery[] = " AND ( ";
						// 受注状態は複数設定されている可能性があるので、設定個数分ループ
						for ( $j = 0; $j < count($arySearchStatus); $j++ )
						{
							// 初回処理
							if ( $j <> 0 )
							{
								$aryQuery[] = " OR ";
							}
							$aryQuery[] = "r.lngReceiveStatusCode = " . $arySearchStatus[$j] . "";
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

			//
			// 明細テーブルの条件
			//
//			$strDetailFrom1 = ", (SELECT distinct on ( rd1.lngReceiveNo ) rd1.lngReceiveNo FROM t_ReceiveDetail rd1 WHERE";
			$strDetailFrom2 = ", (SELECT distinct on ( rd1.lngReceiveNo ) rd1.lngReceiveNo, mg.strGroupDisplayCode, mg.strGroupDisplayName, mu.struserdisplaycode, mu.struserdisplayname FROM t_ReceiveDetail rd1 "
							."LEFT JOIN m_Product p ON rd1.strProductCode = p.strProductCode "
							."left join m_group mg on p.lnginchargegroupcode = mg.lnggroupcode "
							."left join m_user  mu on p.lnginchargeusercode = mu.lngusercode WHERE ";
			
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
					$aryDetailWhereQuery[] = "rd1.strProductCode >= '" . $arySearchDataColumn["strProductCodeFrom"] . "' ";
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
					$aryDetailWhereQuery[] = "rd1.strProductCode <= '" . $arySearchDataColumn["strProductCodeTo"] . "' ";
					$detailFlag = TRUE;
				}
			}
			// 部門
			if ( $strSearchColumnName == "lngInChargeGroupCode" )
			{

				if( $arySearchDataColumn["lngInChargeGroupCode"] || $strSearchColumnName == "lngInChargeUserCode")
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
				if( $arySearchDataColumn["lngInChargeUserCode"] ||  $arySearchDataColumn["strInChargeUserName"])
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
					$aryDetailWhereQuery[] = "rd1.lngSalesClassCode = " . $arySearchDataColumn["lngSalesClassCode"] . " ";
					$detailFlag = TRUE;
				}
			}
			
//20170719kou追加
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
//20170719kou追加　END
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
					$aryDetailWhereQuery[] = "rd1.dtmDeliveryDate >= '" . $arySearchDataColumn["dtmDeliveryDateFrom"] . "' ";
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
					$aryDetailWhereQuery[] = "rd1.dtmDeliveryDate <= '" . $arySearchDataColumn["dtmDeliveryDateTo"] . "' ";
					$detailFlag = TRUE;
				}
			}
		}
	}

	// 明細検索用テーブル結合条件
	$aryDetailFrom = array();
	$aryDetailFrom[] = ", (SELECT distinct on ( rd1.lngReceiveNo ) rd1.lngReceiveNo";
	$aryDetailFrom[] = "	,rd1.lngReceiveDetailNo";
	$aryDetailFrom[] = "	,p.strProductCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayCode";
	$aryDetailFrom[] = "	,mg.strGroupDisplayName";
	$aryDetailFrom[] = "	,mu.struserdisplaycode";
	$aryDetailFrom[] = "	,mu.struserdisplayname";
	$aryDetailFrom[] = "	,p.strProductName";
	$aryDetailFrom[] = "	,p.strProductEnglishName";
	$aryDetailFrom[] = "	,rd1.lngSalesClassCode";
	$aryDetailFrom[] = "	,p.strGoodsCode";
	$aryDetailFrom[] = "	,rd1.dtmDeliveryDate";		// 納期
	$aryDetailFrom[] = "	,rd1.curProductPrice";		// 単価
	$aryDetailFrom[] = "	,rd1.lngProductUnitCode";	// 単位
	$aryDetailFrom[] = "	,rd1.lngProductQuantity";	// 製品数量
	$aryDetailFrom[] = "	,rd1.curSubTotalPrice";		// 税抜金額
	$aryDetailFrom[] = "	,rd1.lngTaxClassCode";		// 税区分
	$aryDetailFrom[] = "	,mt.curTax";				// 税率
	$aryDetailFrom[] = "	,rd1.curTaxPrice";			// 税額
	$aryDetailFrom[] = "	,rd1.strNote";				// 明細備考
	$aryDetailFrom[] = "	FROM t_ReceiveDetail rd1";
	$aryDetailFrom[] = "		LEFT JOIN m_Product p ON rd1.strProductCode = p.strProductCode";
	$aryDetailFrom[] = "		left join m_group mg on p.lnginchargegroupcode = mg.lnggroupcode";
	$aryDetailFrom[] = "		left join m_user  mu on p.lnginchargeusercode = mu.lngusercode";
	$aryDetailFrom[] = "		left join m_tax  mt on mt.lngtaxcode = rd1.lngtaxcode ";

	$aryDetailWhereQuery[] = ") as rd";
	// where句（明細行） クエリー連結
	$strDetailQuery = implode("\n", $aryDetailFrom) . "\n";
	// 明細行の条件が存在する場合
	if ( $detailFlag )
	{
		// where句（明細行） クエリー連結
		$strDetailQuery .= implode("\n", $aryDetailTargetQuery) . "\n";
	}
	$strDetailQuery .= implode("\n", $aryDetailWhereQuery) . "\n";


	// SQL文の作成
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT r.lngReceiveNo as lngReceiveNo";
	$aryOutQuery[] = "	,r.lngRevisionNo as lngRevisionNo";
	$aryOutQuery[] = "	,r.strReceiveCode as strReceiveCode";
	$aryOutQuery[] = "	,r.lngReceiveStatusCode as lngReceiveStatusCode";

	// 明細行の 'order by' 用に追加
	$aryOutQuery[] = "	,rd.lngReceiveDetailNo";
	$aryOutQuery[] = "	,rd.strProductCode";
	$aryOutQuery[] = "	,rd.strGroupDisplayCode";
	$aryOutQuery[] = "	,rd.strUserDisplayCode";
	$aryOutQuery[] = "	,rd.strProductName";
	$aryOutQuery[] = "	,rd.strProductEnglishName";
	$aryOutQuery[] = "	,rd.lngSalesClassCode";
	$aryOutQuery[] = "	,rd.strGoodsCode";
	$aryOutQuery[] = "	,rd.dtmDeliveryDate";
	$aryOutQuery[] = "	,rd.curProductPrice";
	$aryOutQuery[] = "	,rd.lngProductUnitCode";
	$aryOutQuery[] = "	,rd.lngProductQuantity";
	$aryOutQuery[] = "	,rd.curSubTotalPrice";
	$aryOutQuery[] = "	,rd.lngTaxClassCode";
	$aryOutQuery[] = "	,rd.curTax";
	$aryOutQuery[] = "	,rd.curTaxPrice";
	$aryOutQuery[] = "	,rd.strNote";

	// select句 クエリー連結
	$aryOutQuery[] = implode("\n", $arySelectQuery) . "\n";

	// From句 の生成
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM m_Receive r";
	
	// 追加表示用の参照マスタ対応
	if ( $flgInputUser )
	{
		$aryFromQuery[] = " LEFT JOIN m_User input_u ON r.lngInputUserCode = input_u.lngUserCode";
	}
	if ( $flgCustomerCompany )
	{
		$aryFromQuery[] = " LEFT JOIN m_Company cust_c ON r.lngCustomerCompanyCode = cust_c.lngCompanyCode";
	}
	if ( $flgReceiveStatus )
	{
		$aryFromQuery[] = " LEFT JOIN m_ReceiveStatus rs USING (lngReceiveStatusCode)";
	}
	if ( $flgPayCondition )
	{
		$aryFromQuery[] = " LEFT JOIN m_PayCondition pc ON r.lngPayConditionCode = pc.lngPayConditionCode";
	}
	if ( $flgMonetaryUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_MonetaryUnit mu ON r.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
	}
	if ( $flgWorkFlowStatus )
	{
		$aryFromQuery[] = " left join
		( m_workflow mw
			left join t_workflow tw
			on mw.lngworkflowcode = tw.lngworkflowcode
			and tw.lngworkflowsubcode = (select max(lngworkflowsubcode) from t_workflow where lngworkflowcode = tw.lngworkflowcode)
		) on  mw.strworkflowkeycode = trim(to_char(r.lngReceiveNo, '9999999'))
			and mw.lngfunctioncode = " . DEF_FUNCTION_SO1; // 受注登録時のWFデータを対象にする為に条件指定
	}
	
	// From句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryFromQuery);
	
	// Where句 クエリー連結
	$aryOutQuery[] = $strDetailQuery;
	
	// Where句 クエリー連結
	$aryOutQuery[] = implode("\n", $aryQuery);
	
	// 明細行用の条件連結
	$aryOutQuery[] = " AND rd.lngReceiveNo = r.lngReceiveNo";


	/////////////////////////////////////////////////////////////
	//// 最新受注（リビジョン番号が最大、リバイズ番号が最大、////
	//// かつリビジョン番号負の値で無効フラグがFALSEの       ////
	//// 同じ受注コードを持つデータが無い受注データ          ////
	/////////////////////////////////////////////////////////////
	// 受注コードが指定されていない場合は検索条件を設定する
	if ( !$strReceiveCode )
	{
		$aryOutQuery[] = " AND r.lngRevisionNo = ( "
			. "SELECT MAX( r1.lngRevisionNo ) FROM m_Receive r1 WHERE r1.strReceiveCode = r.strReceiveCode AND r1.bytInvalidFlag = false";
		$aryOutQuery[] = " AND r1.strReviseCode = ( "
			. "SELECT MAX( r2.strReviseCode ) FROM m_Receive r2 WHERE r2.strReceiveCode = r1.strReceiveCode AND r2.bytInvalidFlag = false ) )";

		// 管理モードの場合は削除データも検索対象とするため以下の条件は対象外
		if ( !$arySearchDataColumn["Admin"] )
		{
			$aryOutQuery[] = " AND 0 <= ( "
				. "SELECT MIN( r3.lngRevisionNo ) FROM m_Receive r3 WHERE r3.bytInvalidFlag = false AND r3.strReceiveCode = r.strReceiveCode )";
		}
	}

	// 管理モードの検索時、同じ受注コードのデータを取得する場合
	if ( $strReceiveCode or $bytAdminMode )
	{
		$aryOutQuery[] = " ORDER BY r.lngRevisionNo < 0 DESC, r.strReviseCode DESC, r.lngRevisionNo DESC";
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
			case "strReceiveCode":
			case "strCustomerReceiveCode":
			case "lngReceiveStatusCode":
			case "strNote":
			case "curTotalPrice":
				$aryOutQuery[] = " ORDER BY r." . $arySearchDataColumn["strSort"] . " " . $strAsDs . " , lngReceiveNo DESC";
				break;
			case "lngWorkFlowStatusCode":
				$aryOutQuery[] = " ORDER BY lngWorkFlowStatusCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "dtmAppropriationDate":
				$aryOutQuery[] = " ORDER BY dtmReceiveAppDate" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngInputUserCode":
				$aryOutQuery[] = " ORDER BY strInputUserDisplayCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngCustomerCompanyCode":
				$aryOutQuery[] = " ORDER BY strCustomerDisplayCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngReceiveDetailNo":	// 明細行番号
				$aryOutQuery[] = " ORDER BY rd.lngReceiveDetailNo" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "strProductCode":		// 製品コード
				$aryOutQuery[] = " ORDER BY rd.strProductCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngGroupCode":		// 部門
				$aryOutQuery[] = " ORDER BY rd.strGroupDisplayCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngUserCode":			// 担当者
				$aryOutQuery[] = " ORDER BY rd.strUserDisplayCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "strProductName":		// 製品名称
				$aryOutQuery[] = " ORDER BY rd.strProductName" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "strProductEnglishName":	// 製品英語名称
				$aryOutQuery[] = " ORDER BY rd.strProductEnglishName" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngSalesClassCode":	// 売上区分
				$aryOutQuery[] = " ORDER BY rd.lngSalesClassCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "strGoodsCode":		// 顧客品番
				$aryOutQuery[] = " ORDER BY rd.strGoodsCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "dtmDeliveryDate":		// 納期
				$aryOutQuery[] = " ORDER BY rd.dtmDeliveryDate" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "curProductPrice":		// 単価
				$aryOutQuery[] = " ORDER BY rd.curProductPrice" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngProductUnitCode":	// 単位
				$aryOutQuery[] = " ORDER BY rd.lngProductUnitCode" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "lngProductQuantity":	// 数量
				$aryOutQuery[] = " ORDER BY rd.lngProductQuantity" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "curSubTotalPrice":	// 税抜金額
				$aryOutQuery[] = " ORDER BY rd.curSubTotalPrice" . $strAsDs . ", lngReceiveNo DESC";
				break;
			case "strDetailNote":		// 明細備考
				$aryOutQuery[] = " ORDER BY rd.strNote" . $strAsDs . ", lngReceiveNo DESC";
				break;
			default:
				$aryOutQuery[] = " ORDER BY lngReceiveNo DESC";
		}

	}

//fncDebug( 'lib_sos.txt', implode("\n", $aryOutQuery), __FILE__, __LINE__);
//fncDebug( 'lib_sos.txt', $arySearchDataColumn["strSort"], __FILE__, __LINE__);

	return implode("\n", $aryOutQuery);
}






/**
* 対応する受注NOのデータに対する明細行を取得するSQL文の作成関数
*
*	受注NOから明細を取得する SQL文を作成する
*
*	@param  Array 	$aryDetailViewColumn 	表示対象明細カラム名の配列
*	@param  String 	$lngReceiveNo 			対象受注NO
*	@param  Array 	$aryData 				POSTデータの配列
*	@param  Object	$objDB       			DBオブジェクト
*	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
*	@access public
*/
function fncGetReceiveToProductSQL ( $aryDetailViewColumn, $lngReceiveNo, $aryData, $objDB )
{
	reset( $aryDetailViewColumn );

	// 表示用カラムに設定されている内容を検索用に文字列設定
	for ( $i = 0; $i < count($aryDetailViewColumn); $i++ )
	{
		$strViewColumnName = $aryDetailViewColumn[$i];

		// 表示項目　
// 2004.03.31 suzukaze update start
		// 製品コード
		if ( $strViewColumnName == "strProductCode" )
		{
			$arySelectQuery[] = ", rd.strProductCode as strProductCode";
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
// 2004.03.31 suzukaze update end
		// 売上区分
		if ( $strViewColumnName == "lngSalesClassCode" )
		{
			$arySelectQuery[] = ", rd.lngSalesClassCode as lngSalesClassCode";
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
			$arySelectQuery[] = ", to_char( rd.dtmDeliveryDate, 'YYYY/MM/DD' ) as dtmDeliveryDate";
		}
		// 単価
		if ( $strViewColumnName == "curProductPrice" )
		{
// 2004.03.17 suzukaze update start
			$arySelectQuery[] = ", To_char( rd.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
//			$arySelectQuery[] = ", To_char( rd.curProductPrice, '9,999,999,990.99' )  as curProductPrice\n";
// 2004.03.17 suzukaze update end
		}
		// 単位
		if ( $strViewColumnName == "lngProductUnitCode" )
		{
			$arySelectQuery[] = ", rd.lngProductUnitCode as lngProductUnitCode";
			$arySelectQuery[] = ", pu.strProductUnitName as strProductUnitName";
			$flgProductUnit = TRUE;
		}
		// 数量
		if ( $strViewColumnName == "lngProductQuantity" )
		{
			$arySelectQuery[] = ", To_char( rd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
		}
		// 税抜金額
		if ( $strViewColumnName == "curSubTotalPrice" )
		{
			$arySelectQuery[] = ", To_char( rd.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
		}
		// 明細備考
		if ( $strViewColumnName == "strDetailNote" )
		{
			$arySelectQuery[] = ", rd.strNote as strDetailNote";
		}
	}

	// 絶対条件 対象受注NOの指定
	$aryQuery[] = " WHERE rd.lngReceiveNo = " . $lngReceiveNo . "";

	// 条件の追加

	// ////受注マスタ内の検索条件////
	// SQL文の作成
	$aryOutQuery = array();
	$aryOutQuery[] = "SELECT rd.lngSortKey as lngRecordNo";
	$aryOutQuery[] = "	,rd.lngReceiveNo as lngReceiveNo";
	$aryOutQuery[] = "	,rd.lngRevisionNo as lngRevisionNo";


	// select句 クエリー連結
	if( !empty($arySelectQuery) )
	{
		$aryOutQuery[] = implode("\n", $arySelectQuery);
	}

	// From句 の生成
	$aryFromQuery = array();
	$aryFromQuery[] = " FROM t_ReceiveDetail rd";

	// 追加表示用の参照マスタ対応
	$aryFromQuery[] = " LEFT JOIN m_Product p USING (strProductCode)";
	$aryFromQuery[] = " left join m_group mg on mg.lnggroupcode = p.lnginchargegroupcode";
	$aryFromQuery[] = " left join m_user  mu on mu.lngusercode = p.lnginchargeusercode";

	if ( $flgSalesClass )
	{
		$aryFromQuery[] = " LEFT JOIN m_SalesClass ss USING (lngSalesClassCode)";
	}
	if ( $flgProductUnit )
	{
		$aryFromQuery[] = " LEFT JOIN m_ProductUnit pu ON rd.lngProductUnitCode = pu.lngProductUnitCode";
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
			$aryOutQuery[] = " ORDER BY rd.strNote " . $strAsDs . ", rd.lngSortKey ASC";
			break;
		case "lngReceiveDetailNo":
			$aryOutQuery[] = " ORDER BY rd.lngSortKey " . $strAsDs;
			break;
		case "strProductName":
		case "strProductEnglishName":
		case "strGoodsCode":
			$aryOutQuery[] = " ORDER BY " . $aryData["strSort"] . " " . $strAsDs . ", rd.lngSortKey ASC";
			break;
		default:
			$aryOutQuery[] = " ORDER BY rd.lngSortKey ASC";
			break;
	}
	
	return implode("\n", $aryOutQuery);
}






/**
* 検索結果表示関数（明細行用）
*
*	検索結果からテーブル構成で結果を出力する関数
*	明細行を表示する
*
*	@param  Array 	$aryHeadResult 			ヘッダ行の検索結果が格納された配列
*	@param  Array 	$aryDetailResult 		明細行の検索結果が格納された配列
*	@param  Array 	$aryDetailViewColumn 	表示対象カラム名の配列
*	@param  Array 	$aryData 				ＰＯＳＴデータ群
*	@param	Integer	$lngMode				出力モード　0: １行目の表示		それ以外: ２行目以降の表示
*	@param	Integer	$lngColumnCount			表示行数
*	@access public
*/
function fncSetReceiveDetailTable ( $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryData, $lngMode, $lngColumnCount, $objDB, $objCache )
{
	// 明細行数
	$lngDetailCount = count($aryDetailResult);

	// モード設定
	if ( $lngMode == 0 )
	{
		$lngStart = 0;
		$lngEnd = 1;
	}
	else
	{
		$lngStart = 1;
		$lngEnd = $lngDetailCount;
	}

	// $aryDetailResult[] 中の明細情報の出力（２行目以降）
	for ( $x = $lngStart; $x < $lngEnd; $x++ )
	{
//		reset( $aryDetailResult[$x] );

		if ( $lngMode )
		{
			$lngColumnCountMinus = $lngColumnCount - 1;
			if ( $lngDetailCount == "" )
			{
				$lngDetailCount = 0;
			}
			$aryHtml[] = "<tr id=\"TD" . $lngColumnCountMinus . "_" . $x . "\" class=\"Segs\" name=\"strTrName" . $lngColumnCountMinus . "\" onclick=\"fncSelectSomeTrColor( this,  'TD" . $lngColumnCountMinus . "_', " . $lngDetailCount . " );\" style=\"background:#FFB2B2;\">";
		}

		// 表示対象カラムの配列より結果の出力
		for ( $y = 0; $y < count($aryDetailViewColumn); $y++ )
		{
			$strDetailColumnName = $aryDetailViewColumn[$y];

			// 明細行番号
			if ( $strDetailColumnName == "lngRecordNo" )
			{
				$aryHtml[] = "<td align=\"center\" nowrap>";
				$aryHtml[] = $aryDetailResult[$x]["lngrecordno"] . "</td>";
			}

// 2004.03.31 suzukaze update start
			// 製品コード
			else if ( $strDetailColumnName == "strProductCode" )
			{
				$strText = "<td align=\"center\" nowrap>";
				if ( $aryDetailResult[$x]["strproductcode"] )
				{
					$strText .= "[" . $aryDetailResult[$x]["strproductcode"] ."]";
				}
				else
				{
					$strText .= "      ";
				}
				$strText .= "</td>";
				$aryHtml[] = $strText;
			}
// 2004.03.31 suzukaze update end

			// 売上区分
			else if ( $strDetailColumnName == "lngSalesClassCode" )
			{
				$aryHtml[] = "<td align=\"left\" nowrap>";
				if ( $aryDetailResult[$x]["lngsalesclasscode"] )
				{
					$aryHtml[] = "[" . $aryDetailResult[$x]["lngsalesclasscode"] ."]";
				}
				else
				{
					$aryHtml[] = "      ";
				}
				$aryHtml[] = " " . $aryDetailResult[$x]["strsalesclassname"] . "</td>";
			}

			// 納期
			else if ( $strDetailColumnName == "dtmDeliveryDate" )
			{
				$aryHtml[] = "<td align=\"left\" nowrap>";
				$aryHtml[] = str_replace( "-", "/", $aryDetailResult[$x]["dtmdeliverydate"] ) . "</td>";
			}

			// 単価
			else if ( $strDetailColumnName == "curProductPrice" )
			{
				$aryHtml[] = "<td align=\"right\" nowrap>";
				$aryHtml[] = $aryHeadResult["strmonetaryunitsign"] . " ";
				if ( !$aryDetailResult[$x]["curproductprice"] )
				{
					$aryHtml[] = "0.00</td>";
				}
				else
				{
					$aryHtml[] = $aryDetailResult[$x]["curproductprice"] . "</td>";
				}
			}

			// 単位
			else if ( $strDetailColumnName == "lngProductUnitCode" )
			{
				$aryHtml[] = "<td align=\"left\" nowrap>";
				$aryHtml[] = $aryDetailResult[$x]["strproductunitname"] . "</td>";
			}

			// 数量
			else if ( $strDetailColumnName == "lngProductQuantity" )
			{
				$aryHtml[] = "<td align=\"right\" nowrap>";
				$aryHtml[] = $aryDetailResult[$x]["lngproductquantity"] . "</td>";
			}

			// 税抜金額
			else if ( $strDetailColumnName == "curSubTotalPrice" )
			{
				$aryHtml[] = "<td align=\"right\" nowrap>";
				$aryHtml[] = $aryHeadResult["strmonetaryunitsign"] . " ";
				if ( !$aryDetailResult[$x]["cursubtotalprice"] )
				{
					$aryHtml[] = "0.00</td>";
				}
				else
				{
					$aryHtml[] = $aryDetailResult[$x]["cursubtotalprice"] . "</td>";
				}
			}

			// その他の項目はそのまま出力
			else
			{
				$strLowDetailColumnName = strtolower($strDetailColumnName);
				$aryHtml[] = "<td align=\"left\" nowrap>";
				if ( $strLowDetailColumnName == "strdetailnote" )
				{
					$aryHtml[] = nl2br($aryDetailResult[$x][$strLowDetailColumnName]) . "</td>";
				}
				else
				{
					$aryHtml[] = $aryDetailResult[$x][$strLowDetailColumnName] . "</td>";
				}
			}
		}
		if ( $lngMode )
		{
			$aryHtml[] = "</tr>";
		}
	}

	return $aryHtml;
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
function fncSetReceiveHeadTable ( $lngColumnCount, $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngReviseTotalCount, $lngReviseCount, $bytDeleteFlag )
{
	
	// 明細行の行数
	$lngDetailCount = count($aryDetailResult);
	if ( !$lngDetailCount )
	{
		$lngDetailCount = 1;
	}
	// 管理モードの場合も１
	if ( $aryData["Admin"] )
	{
		$lngDetailCount = 1;
	}

	$aryHtml[] =  "<td nowrap align=\"center\" rowspan=\"" . $lngDetailCount . "\">" . $lngColumnCount . "</td>";

	// 管理モードでない場合の明細行出力対応カウンター
	$count = 0;

	// 表示対象カラムの配列より結果の出力
	for ( $j = 0; $j < count($aryHeadViewColumn); $j++ )
	{
		$strColumnName = $aryHeadViewColumn[$j];

		// 表示対象がボタンの場合
		if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" or $strColumnName == "btnInvalid" )
		{
			// ボタン種により変更

			// 詳細表示
			if ( $strColumnName == "btnDetail" and $aryUserAuthority["Detail"] )
			{
				// 受注データが削除対象の場合、詳細表示ボタンは選択不可
				if ( $aryHeadResult["lngrevisionno"] >= 0 )
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/so/result/index2.php?lngReceiveNo=" . $aryHeadResult["lngreceiveno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . ", 'detail' )\"><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
				}
			}

			// 修正
			if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
			{
				// 受注データの状態により分岐  //// 状態が「締め済」、また削除対象の場合修正ボタンは選択不可
				// 最新受注が削除データの場合も選択不可
				// 納品済で管理モードで無い場合も選択不可
				if ( $aryHeadResult["lngreceivestatuscode"] == DEF_RECEIVE_CLOSED 
					or ( $aryHeadResult["lngreceivestatuscode"] == DEF_RECEIVE_END and !$aryData["Admin"] ) 
					or $aryHeadResult["lngrevisionno"] < 0 
					or $bytDeleteFlag )
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogRenew('/so/regist/renew.php?lngReceiveNo=" . $aryHeadResult["lngreceiveno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeRenew' , 'NO' , " . $aryData["lngLanguageCode"] . " )\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>";
				}
			}

			// 削除
			if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
			{
				// 管理モードで無い場合もしくはリバイズが存在しない場合
				if ( !$aryData["Admin"] or $lngReviseTotalCount == 1 )
				{
					// 受注データの状態により分岐  //// 状態が「申請中」「納品中」「納品済」「締め済」の場合削除ボタンを選択不可
					// 最新受注が削除データの場合も選択不可
					if (    $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_APPLICATE
						and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_DELIVER
						and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_END
						and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_CLOSED
						and !$bytDeleteFlag )
					{
						$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/so/result/index3.php?lngReceiveNo=" . $aryHeadResult["lngreceiveno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'delete' )\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"REMOVE\"></a></td>";
					}
					else
					{
						$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
					}
				}
				// 管理モードで複数リバイズが存在する場合
				else
				{
					// 最新受注の場合
					if ( $lngReviseCount == 0 )
					{
						// 受注データの状態により分岐  //// 状態が「締め済」以外
						// 最新受注が削除データの場合も選択不可
						if ( $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_CLOSED 
							and !$bytDeleteFlag )
						{
							$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngReviseTotalCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/so/result/index3.php?lngReceiveNo=" . $aryHeadResult["lngreceiveno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'delete' )\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"REMOVE\"></a></td>";
						}
						else
						{
							$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngReviseTotalCount . "\"></td>";
						}
					}
				}
			}

			// 無効化
			if ( $strColumnName == "btnInvalid" and $aryData["Admin"] and $aryUserAuthority["Admin"] and $aryUserAuthority["Invalid"] )
			{
				// 受注データの状態により分岐  //// 状態が「申請中」「納品中」「納品済」「締め済」の場合無効化ボタンを選択不可
				// 上記条件に加え、対象受注が削除データの場合も選択不可
				if (    $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_APPLICATE
					and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_DELIVER
					and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_END
					and $aryHeadResult["lngreceivestatuscode"] != DEF_RECEIVE_CLOSED
					and $aryHeadResult["lngrevisionno"] >= 0 )
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/so/result/index4.php?lngReceiveNo=" .$aryHeadResult["lngreceiveno"]. "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'Invalid01' )\"><img onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" onmouseover=\"fncInvalidSmallButton( 'on' , this );\" onmouseout=\"fncInvalidSmallButton( 'off' , this );fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/invalid_small_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"INVALID\"></a></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" rowspan=\"" . $lngDetailCount . "\"></td>";
				}
			}
		}

		// 登録日
		else if ( $strColumnName == "dtmInsertDate" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = str_replace( "-", "/", substr( $aryHeadResult["dtminsertdate"], 0, 19 ) ) . "</td>";
		}

		// 計上日
		else if ( $strColumnName == "dtmReceiveAppDate" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = str_replace( "-", "/", $aryHeadResult["dtmreceiveappdate"] ) . "</td>";
		}

		// 受注NO
		else if ( $strColumnName == "strReceiveCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = $aryHeadResult["strreceivecode"] . "</td>";
			// 管理モードの場合　リビジョン番号を表示する
			if ( $aryData["Admin"] )
			{
				$aryHtml[] = "<td align=\"center\" nowrap rowspan=\"" . $lngDetailCount . "\">" . $aryHeadResult["lngrevisionno"] . "</td>";
			}
		}

		// 入力者
		else if ( $strColumnName == "lngInputUserCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			if ( $aryHeadResult["strinputuserdisplaycode"] )
			{
				$aryHtml[] = "[" . $aryHeadResult["strinputuserdisplaycode"] ."]";
			}
			else
			{
				$aryHtml[] = "     ";
			}
			$aryHtml[] = " " . $aryHeadResult["strinputuserdisplayname"] . "</td>";
		}

		// 顧客
		else if ( $strColumnName == "lngCustomerCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			if ( $aryHeadResult["strcustomerdisplaycode"] )
			{
				$aryHtml[] = "[" . $aryHeadResult["strcustomerdisplaycode"] ."]";
			}
			else
			{
				$aryHtml[] = "      ";
			}
			$aryHtml[] = " " . $aryHeadResult["strcustomerdisplayname"] . "</td>";
		}
		
/*
		// 部門
		else if ( $strColumnName == "lngInChargeGroupCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			if ( $aryHeadResult["strinchargegroupdisplaycode"] )
			{
				$aryHtml[] = "[" . $aryHeadResult["strinchargegroupdisplaycode"] ."]";
			}
			else
			{
				$aryHtml[] = "    ";
			}
			$aryHtml[] = " " . $aryHeadResult["strinchargegroupdisplayname"] . "</td>";
		}

		// 担当者
		else if ( $strColumnName == "lngInChargeUserCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			if ( $aryHeadResult["strinchargeuserdisplaycode"] )
			{
				$aryHtml[] = "[" . $aryHeadResult["strinchargeuserdisplaycode"] ."]";
			}
			else
			{
				$aryHtml[] = "     ";
			}
			$aryHtml[] = " " . $aryHeadResult["strinchargeuserdisplayname"] . "</td>";
		}
*/

		// 合計金額
		else if ( $strColumnName == "curTotalPrice" )
		{
			$aryHtml[] = "<td align=\"right\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = $aryHeadResult["strmonetaryunitsign"] . " ";
			if ( !$aryHeadResult["curtotalprice"] )
			{
				$aryHtml[] = "0.00</td>";
			}
			else
			{
				$aryHtml[] = $aryHeadResult["curtotalprice"] . "</td>";
			}
		}

		// 状態
		else if ( $strColumnName == "lngReceiveStatusCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
			$aryHtml[] = $aryHeadResult["strreceivestatusname"] . "</td>";
		}
		// 明細行の出力
		else if ( $strColumnName == "strProductCode"
			or $strColumnName == "lngInChargeGroupCode" or $strColumnName == "lngInChargeUserCode" // <-- added by siato
			or $strColumnName == "lngRecordNo" 
			or $strColumnName == "lngSalesClassCode" or $strColumnName == "strGoodsCode"
			or $strColumnName == "dtmDeliveryDate" or $strColumnName == "curProductPrice" or $strColumnName == "lngProductUnitCode"
			or $strColumnName == "lngProductQuantity" or $strColumnName == "curSubTotalPrice" or $strColumnName == "strDetailNote" 
			or $strColumnName == "strProductName" or $strColumnName == "strProductEnglishName" )
		{
			if ( !$aryData["Admin"] and $count == 0 )
			{
				// 明細行の出力
				$aryDetailHtml = fncSetReceiveDetailTable ( $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryData, 0, $lngColumnCount, $objDB, $objCache );
				for ( $k = 0; $k < count($aryDetailHtml); $k++ )
				{
					$aryHtml[] = $aryDetailHtml[$k];
				}
				$count++;
			}
			// 管理モードの場合、製品名称は１カラムに表示する
			else if ( $aryData["Admin"] and $strColumnName == "strProductCode" )
			{
				$aryHtml[] = "<td align=\"left\" nowrap rowspan=\"" . $lngDetailCount . "\">";
				for ( $k = 0; $k < count($aryDetailResult); $k++ )
				{
					if ( $aryDetailResult[$k]["strproductcode"] )
					{
						$aryHtml[] = "[" . $aryDetailResult[$k]["strproductcode"] ."]";
					}
					else
					{
						$aryHtml[] = "      ";
					}
					$aryHtml[] = " " . $aryDetailResult[$k]["strproductname"] . "<br>";
				}
				$aryHtml[] = "</td>";
			}
		}

		// その他の項目はそのまま出力
		else
		{
			$strLowColumnName = strtolower($strColumnName);
			$aryHtml[] = "<td align=\"left\" nowrap";
			$aryHtml[] = " rowspan=\"" . $lngDetailCount . "\">";
			if ( $strLowColumnName == "strnote" )
			{
				$aryHtml[] = nl2br($aryHeadResult[$strLowColumnName]) . "</td>";
			}
			else
			{
				$aryHtml[] = $aryHeadResult[$strLowColumnName] . "</td>";
			}
		}
	}

	$aryHtml[] = "</tr>";

	// もし、明細行が複数行存在していれば
	if ( ( !$aryData["Admin"] ) and ( count($aryDetailResult) >= 2 ) )
	{
		// 明細行の出力
		$aryDetailHtml = fncSetReceiveDetailTable ( $aryHeadResult, $aryDetailResult, $aryDetailViewColumn, $aryData, 1, $lngColumnCount, $objDB, $objCache );
		for ( $k = 0; $k < count($aryDetailHtml); $k++ )
		{
			$aryHtml[] = $aryDetailHtml[$k];
		}
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
*	@param  Object	$objCache       		DBオブジェクト
*	@access public
*/
function fncSetReceiveTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName )
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
		else if ( $strColumnName == "strProductCode" 
			or $strColumnName == "lngInChargeGroupCode" or $strColumnName == "lngInChargeUserCode" // <-- added by siato
			or $strColumnName == "lngRecordNo" 
			or $strColumnName == "lngSalesClassCode" or $strColumnName == "strGoodsCode"
			or $strColumnName == "dtmDeliveryDate" or $strColumnName == "curProductPrice" or $strColumnName == "lngProductUnitCode"
			or $strColumnName == "lngProductQuantity" or $strColumnName == "curSubTotalPrice" or $strColumnName == "strDetailNote" 
			or $strColumnName == "strProductName" or $strColumnName == "strProductEnglishName" )
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

	$aryHtml[] = "<span id=\"COPYAREA1\">";
	$aryHtml[] = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\" border=\"0\" bgcolor=\"#6f8180\" align=\"center\">";

	$lngColumnCount = 0;

	for ( $i = 0; $i < $lngResultCount; $i++ )
	{

// 項目名列の生成 start=========================================

		if ($i == 0)
		{
			$aryHtml[] = "<tr id=\"SegTitle\">";
			$aryHtml[] = "<td valign=\"top\" valign=\"center\"><a href=\"#\" onclick=\"fncDoCopy( copyhidden , document.getElementById('COPYAREA1') , document.getElementById('COPYAREA2') );return false;\"><img onmouseover=\"CopyOn(this);\" onmouseout=\"CopyOff(this);\" src=\"/img/type01/cmn/seg/copy_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"COPY\"></a></td>";

			// 表示対象カラムの配列より項目設定
			for ( $j = 0; $j < count($aryViewColumn); $j++ )
			{
				$strColumnName = $aryViewColumn[$j];
				// 管理モードの場合、製品名称は１カラムに表示する
				if ( $aryData["Admin"] and $strColumnName == "strProductName" )
				{
					// 管理モードで製品名称が表示対象になっている場合はカラムタイトルは非表示
				}
				else
				{
					// ソート項目以外の場合
					if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" or $strColumnName == "btnInvalid" )
					{
						// 詳細ボタンの場合は、詳細表示可能なユーザーのみ表示する
						if ( $strColumnName == "btnDetail" and $aryUserAuthority["Detail"] )
						{
							$aryHtml[] = "<td id=\"Columns\" nowrap>".$aryTytle[$strColumnName]."</td>";
						}
						// 修正ボタンの場合は、修正処理可能なユーザーのみ表示する
						if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
						{
							$aryHtml[] = "<td id=\"Columns\" nowrap>".$aryTytle[$strColumnName]."</td>";
						}
						// 削除ボタンの場合は、削除処理可能なユーザーのみ表示する
						if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"] )
						{
							$aryHtml[] = "<td id=\"Columns\" nowrap>".$aryTytle[$strColumnName]."</td>";
						}
						// 無効ボタンの場合は、管理モード、および無効処理可能なユーザーの場合のみ表示する
						if ( $strColumnName == "btnInvalid" and $aryData["Admin"] and $aryUserAuthority["Admin"] and $aryUserAuthority["Invalid"] )
						{
							$aryHtml[] = "<td id=\"Columns\" nowrap>".$aryTytle[$strColumnName]."</td>";
						}
					}
					// ソート項目の場合
					else
					{
						$aryHtml[] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" ";
						if ( $aryData["strSort"] == $aryTableName[$strColumnName] )
						{
							if ( $aryData["strSortOrder"] == "DESC" )
							{
								$strSortOrder = "ASC";
							}
							else
							{
								$strSortOrder = "DESC";
							}
						}
						else
						{
							$strSortOrder = "DESC";
						}
						$aryHtml[] = "onclick=\"fncSort2('" . $aryTableName[$strColumnName] . "', '" . $strSortOrder . "');\">";
						$aryHtml[] = "<a href=\"#\">".$aryTytle[$strColumnName]."</a></td>";
						// 管理モードの場合　リビジョン番号を表示する
						if ( $aryData["Admin"] and $strColumnName == "strReceiveCode" )
						{
							$aryHtml[] = "<td id=\"Columns\" nowrap>".$aryTytle["lngRevisionNo"]."</td>";
						}
					}
				}
			}
			$aryHtml[] = "</tr>";
// 検索結果のコピー機能対応の為以下の行をコメントアウト
//			$aryHtml[] = "</span>";

			// ダミーTR
			$aryHtml[] = "<tr id=\"DummyTR\"><td colspan=\"" . count($aryViewColumn) . "\">&nbsp;</td></tr>";

// 検索結果のコピー機能対応の為以下の行をコメントアウト
//			$aryHtml[] = "<span id=\"COPYAREA2\">";
		}

// 項目名列の生成 end=========================================

// 検索結果出力　　共通start==================================
		// 管理モードでなければ
		if ( !$aryData["Admin"] )
		{
			reset( $aryResult[$i] );

			// 明細出力用の調査
			$lngDetailViewCount = count( $aryDetailViewColumn );

			if ( $lngDetailViewCount )
			{
				// 明細行数の調査
				$strDetailQuery = fncGetReceiveToProductSQL( $aryDetailViewColumn, $aryResult[$i]["lngreceiveno"], $aryData, $objDB );
//fncDebug('lib_sos.txt', $strDetailQuery, __FILE__, __LINE__);
				// クエリー実行
				if ( !$lngDetailResultID = $objDB->execute( $strDetailQuery ) )
				{
					$strMessage = fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
				}

				$lngDetailCount = pg_num_rows( $lngDetailResultID );

				// 配列のクリア
				unset( $aryDetailResult );

				// 結果の取得
				if ( $lngDetailCount )
				{
					for ( $j = 0; $j < $lngDetailCount; $j++ )
					{
						$aryDetailResult[] = pg_fetch_array( $lngDetailResultID, $j, PGSQL_ASSOC );
					}
				}

				$objDB->freeResult( $lngDetailResultID );
			}

			// 検索結果部分の設定
			if ( $lngDetailCount == "" )
			{
				$lngDetailCount = 0;
			}
			$aryHtml[] = "<tr id=\"TD" . $lngColumnCount . "_0\" class=\"Segs\" name=\"strTrName" . $lngColumnCount . "\" onclick=\"fncSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\" style=\"background:#FFB2B2\">";

			$lngColumnCount++;

			// １レコード分の出力
			$aryHtml_add = fncSetReceiveHeadTable ( $lngColumnCount, $aryResult[$i], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, 1, 0, FALSE );
			for ( $j = 0; $j < count($aryHtml_add); $j++ )
			{
				$aryHtml[] = $aryHtml_add[$j];
			}
		}
// 検索結果出力　　共通end==================================

// 管理モード用過去リバイズ、削除データ出力start==================================
		// 管理モードの場合
		else
		{
			// 管理モードの場合　同じ受注コードの一覧を取得し表示する

			// リバイズコード無しの受注コードを取得する
			$strSubText = strrchr( $aryResult[$i]["strreceivecode"], "-" );
			if ( $strSubText )
			{
				$strReceiveCodeBase = ereg_replace( $strSubText, "", $aryResult[$i]["strreceivecode"] );
			}
			else
			{
				$strReceiveCodeBase = $aryResult[$i]["strreceivecode"];
			}

			$strSameReceiveCodeQuery = fncGetSearchReceiveSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $strReceiveCodeBase, $aryResult[$i]["lngreceiveno"], FALSE );

			// 値をとる =====================================
			list ( $lngResultID, $lngResultNum ) = fncQuery( $strSameReceiveCodeQuery, $objDB );

			// 配列のクリア
			unset( $arySameReceiveCodeResult );

			if ( $lngResultNum )
			{
				for ( $j = 0; $j < $lngResultNum; $j++ )
				{
					$arySameReceiveCodeResult[] = $objDB->fetchArray( $lngResultID, $j );
				}
				$lngSameReceiveCount = $lngResultNum;
			}
			$objDB->freeResult( $lngResultID );

			// 同じ受注コードでの過去リバイズデータが存在すれば
			if ( $lngResultNum )
			{
				for ( $j = 0; $j < $lngSameReceiveCount; $j++ )
				{
					// 検索結果部分の設定

					reset( $arySameReceiveCodeResult[$j] );

					// 明細出力用の調査
					$lngDetailViewCount = count( $aryDetailViewColumn );

					if ( $lngDetailViewCount )
					{
						// 明細行数の調査
						$strDetailQuery = fncGetReceiveToProductSQL ( $aryDetailViewColumn, $arySameReceiveCodeResult[$j]["lngreceiveno"], $aryData, $objDB );

						// クエリー実行
						if ( !$lngDetailResultID = $objDB->execute( $strDetailQuery ) )
						{
							$strMessage = fncOutputError( 3, "DEF_FATAL", "クエリー実行エラー" ,TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
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
					// データの状態による背景色の変更
					if ( $lngDetailCount == "" )
					{
						$lngDetailCount = 0;
					}
					if ( $arySameReceiveCodeResult[$j]["lngrevisionno"] < 0 )
					{
						// 削除データの場合
						$aryHtml[] = "<tr id=\"TD" . $lngColumnCount . "_0\" class=\"Segs\" name=\"strTrName" . $lngColumnCount . "\" onclick=\"fncSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', 1 );\" style=\"background:#B3E0FF;\">";
					}
					else if ( $j == 0 )
					{
						// 最新のデータの場合
						$aryHtml[] = "<tr id=\"TD" . $lngColumnCount . "_0\" class=\"Segs\" name=\"strTrName" . $lngColumnCount . "\" onclick=\"fncSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', 1 );\" style=\"background:#FFB2B2;\">";
					}
					else
					{
						$aryHtml[] = "<tr id=\"TD" . $lngColumnCount . "_0\" class=\"Segs\" name=\"strTrName" . $lngColumnCount . "\" onclick=\"fncSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', 1 );\" style=\"background:#FEEF8B;\">";
					}

					$lngColumnCount++;

					// 同じコードの受注データで一番上に表示されている受注データが削除データの場合
					if ( $arySameReceiveCodeResult[0]["lngrevisionno"] < 0 )
					{
						$bytDeleteFlag = TRUE;
					}
					else
					{
						$bytDeleteFlag = FALSE;
					}

					// １レコード分の出力
					$aryHtml_add = fncSetReceiveHeadTable ( $lngColumnCount, $arySameReceiveCodeResult[$j], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameReceiveCount, $j, $bytDeleteFlag );
					for ( $k = 0; $k < count($aryHtml_add); $k++ )
					{
						$aryHtml[] = $aryHtml_add[$k];
					}
				}
			}
		}

// 管理モード用過去リバイズデータ出力end==================================

	}

	$aryHtml[] = "</table>";
	$aryHtml[] = "</span>";

	// コピー不具合対応 ダミー行を抜かす処理を現状省略することで対応
	$aryHtml[] = "<span id=\"COPYAREA2\">";
	$aryHtml[] = "</span>";

	$strhtml = implode( "\n", $aryHtml );

	return $strhtml;
}




?>