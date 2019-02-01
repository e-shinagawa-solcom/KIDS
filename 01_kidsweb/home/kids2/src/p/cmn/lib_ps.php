<?

// ----------------------------------------------------------------------------
/**
*       製品　検索関連関数群
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
*		検索結果関連の関数
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



/**
* 検索項目から一致する最新の商品データを取得するSQL文の作成関数
*
*	検索項目から SQL文を作成する
*
*	@param  Array 	$aryViewColumn 			表示対象カラム名の配列
*	@param  Array 	$arySearchColumn 		検索対象カラム名の配列
*	@param  Array 	$arySearchDataColumn 	検索内容の配列
*	@param  Object	$objDB       			DBオブジェクト
*	@param	Array	$aryUserAuthority		ユーザーの権限情報の配列（商品管理に対する権限情報）
*	@return Array 	$strSQL 検索用SQL文 OR Boolean FALSE
*	@access public
*/
function fncGetSearchProductSQL ( $aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $aryUserAuthority )
{

	// 表示用カラムに設定されている内容を検索用に文字列設定
	for ( $i = 0; $i < count($aryViewColumn); $i++ )
	{
		$strViewColumnName = $aryViewColumn[$i];

		// 表示項目

		// 作成日時
		if ( $strViewColumnName == "dtmInsertDate" )
		{
			$arySelectQuery[] = ", to_char( p.dtmInsertDate, 'YYYY/MM/DD' ) as dtmInsertDate\n";
		}
		// 企画進行状況
		if ( $strViewColumnName == "lngGoodsPlanProgressCode" )
		{
			$arySelectQuery[] = ", t_gp.lngGoodsPlanProgressCode as lngGoodsPlanProgressCode\n";
			$flgT_GoodsPlan = TRUE;
		}
		// 改訂日時
		if ( $strViewColumnName == "dtmRevisionDate" )
		{
			$arySelectQuery[] = ", to_char( p.dtmUpdateDate, 'YYYY/MM/DD' ) as dtmRevisionDate\n";
		}
		// 製品コード
		if ( $strViewColumnName == "strProductCode" )
		{
			$arySelectQuery[] = ", p.strProductCode as strProductCode\n";
		}
		// 製品名称
		if ( $strViewColumnName == "strProductName" )
		{
			$arySelectQuery[] = ", p.strProductName as strProductName\n";
		}
		// 製品名称（英語）
		if ( $strViewColumnName == "strProductEnglishName" )
		{
			$arySelectQuery[] = ", p.strProductEnglishName as strProductEnglishName\n";
		}
		// 入力者
		if ( $strViewColumnName == "lngInputUserCode" )
		{
			$arySelectQuery[] = ", p.lngInputUserCode as lngInputUserCode\n";
			$arySelectQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode\n";
			$arySelectQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName\n";
			$flgInputUser = TRUE;
		}
		// 部門
		if ( $strViewColumnName == "lngInChargeGroupCode" )
		{
			$arySelectQuery[] = ", p.lngInChargeGroupCode as lngInChargeGroupCode\n";
			$arySelectQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode\n";
			$arySelectQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName\n";
			$flgInChargeGroup = TRUE;
		}
		// 担当者
		if ( $strViewColumnName == "lngInChargeUserCode" )
		{
			$arySelectQuery[] = ", p.lngInChargeUserCode as lngInChargeUserCode\n";
			$arySelectQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode\n";
			$arySelectQuery[] = ", inchg_u.strUserDisplayName as strInChargeUserDisplayName\n";
			$flgInChargeUser = TRUE;
		}
		// カテゴリー
		if ( $strViewColumnName == "lngCategoryCode" )
		{
			$arySelectQuery[] = ", mc.strCategoryName as lngCategoryCode\n";
			$flgCategory = TRUE;
		}
		// 顧客品番
		if ( $strViewColumnName == "strGoodsCode" )
		{
			$arySelectQuery[] = ", p.strGoodsCode as strGoodsCode\n";
		}
		// 商品名称
		if ( $strViewColumnName == "strGoodsName" )
		{
			$arySelectQuery[] = ", p.strGoodsName as strGoodsName\n";
		}
		// 顧客
		if ( $strViewColumnName == "lngCustomerCompanyCode" )
		{
			$arySelectQuery[] = ", p.lngCustomerCompanyCode as lngCustomerCompanyCode\n";
			$arySelectQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerCompanyDisplayCode\n";
			$arySelectQuery[] = ", cust_c.strCompanyDisplayName as strCustomerCompanyDisplayName\n";
			$flgCustomerCompany = TRUE;
		}
		// 顧客担当者
		if ( $strViewColumnName == "lngCustomerUserCode" )
		{
			$arySelectQuery[] = ", p.lngCustomerUserCode as lngCustomerUserCode\n";
			$arySelectQuery[] = ", cust_u.strUserDisplayCode as strCustomerUserDisplayCode\n";
			$arySelectQuery[] = ", cust_u.strUserDisplayName as strCustomerUserDisplayName\n";
			$arySelectQuery[] = ", p.strCustomerUserName as strCustomerUserName\n";
			$flgCustomerUser = TRUE;
		}
		// 荷姿単位
		if ( $strViewColumnName == "lngPackingUnitCode" )
		{
			$arySelectQuery[] = ", p.lngPackingUnitCode as lngPackingUnitCode\n";
			$arySelectQuery[] = ", packingunit.strProductUnitName as strPackingUnitName\n";
			$flgPackingUnit = TRUE;
		}
		// 製品単位
		if ( $strViewColumnName == "lngProductUnitCode" )
		{
			$arySelectQuery[] = ", p.lngProductUnitCode as lngProductUnitCode\n";
			$arySelectQuery[] = ", productunit.strProductUnitName as strProductUnitName\n";
			$flgProductUnit = TRUE;
		}
		// 商品形態
		if ( $strViewColumnName == "lngProductFormCode" )
		{
			$arySelectQuery[] = ", p.lngProductFormCode as lngProductFormCode\n";
			$arySelectQuery[] = ", productform.strProductFormName as strProductFormName\n";
			$flgProductForm = TRUE;
		}
		// 内箱（袋）入数
		if ( $strViewColumnName == "lngBoxQuantity" )
		{
			$arySelectQuery[] = ", To_char( p.lngBoxQuantity, '9,999,999,990' ) as lngBoxQuantity\n";
		}
		// カートン入数
		if ( $strViewColumnName == "lngCartonQuantity" )
		{
			$arySelectQuery[] = ", To_char( p.lngCartonQuantity, '9,999,999,990' ) as lngCartonQuantity\n";
		}
		// 生産予定数
		if ( $strViewColumnName == "lngProductionQuantity" )
		{
			$arySelectQuery[] = ", To_char( p.lngProductionQuantity, '9,999,999,990' ) as lngProductionQuantity\n";
			$arySelectQuery[] = ", p.lngProductionUnitCode as lngProductionUnitCode\n";
		}
		// 初回納品数
		if ( $strViewColumnName == "lngFirstDeliveryQuantity" )
		{
			$arySelectQuery[] = ", To_char( p.lngFirstDeliveryQuantity, '9,999,999,990' ) as lngFirstDeliveryQuantity\n";
			$arySelectQuery[] = ", p.lngFirstDeliveryUnitCode as lngFirstDeliveryUnitCode\n";
		}
		// 生産工場
		if ( $strViewColumnName == "lngFactoryCode" )
		{
			$arySelectQuery[] = ", p.lngFactoryCode as lngFactoryCode\n";
			$arySelectQuery[] = ", fact_c.strCompanyDisplayCode as strFactoryDisplayCode\n";
			$arySelectQuery[] = ", fact_c.strCompanyDisplayName as strFactoryDisplayName\n";
			$flgFactory = TRUE;
		}
		// アッセンブリ工場
		if ( $strViewColumnName == "lngAssemblyFactoryCode" )
		{
			$arySelectQuery[] = ", p.lngAssemblyFactoryCode as lngAssemblyFactoryCode\n";
			$arySelectQuery[] = ", assemfact_c.strCompanyDisplayCode as strAssemblyFactoryDisplayCode\n";
			$arySelectQuery[] = ", assemfact_c.strCompanyDisplayName as strAssemblyFactoryDisplayName\n";
			$flgAssemblyFactory = TRUE;
		}
		// 納品場所
		if ( $strViewColumnName == "lngDeliveryPlaceCode" )
		{
			$arySelectQuery[] = ", p.lngDeliveryPlaceCode as lngDeliveryPlaceCode\n";
			$arySelectQuery[] = ", delv_c.strCompanyDisplayCode as strDeliveryPlaceDisplayCode\n";
			$arySelectQuery[] = ", delv_c.strCompanyDisplayName as strDeliveryPlaceDisplayName\n";
			$flgDeliveryPlace = TRUE;
		}
		// 納期
		if ( $strViewColumnName == "dtmDeliveryLimitDate" )
		{
			$arySelectQuery[] = ", to_char( p.dtmDeliveryLimitDate, 'YYYY/MM' ) as dtmDeliveryLimitDate";
		}
		// 納価
		if ( $strViewColumnName == "curProductPrice" )
		{
			$arySelectQuery[] = ", To_char( p.curProductPrice, '9,999,999,990.99' )  as curProductPrice\n";
		}
		// 上代
		if ( $strViewColumnName == "curRetailPrice" )
		{
			$arySelectQuery[] = ", To_char( p.curRetailPrice, '9,999,999,990.99' )  as curRetailPrice\n";
		}
		// 対象年齢
		if ( $strViewColumnName == "lngTargetAgeCode" )
		{
			$arySelectQuery[] = ", p.lngTargetAgeCode as lngTargetAgeCode\n";
			$arySelectQuery[] = ", targetage.strTargetAgeName as strTargetAgeName\n";
			$flgTargetAge = TRUE;
		}
		// ロイヤリティ
		if ( $strViewColumnName == "lngRoyalty" )
		{
			$arySelectQuery[] = ", To_char( p.lngRoyalty, '9,999,999,990.99' )  as lngRoyalty\n";
		}
		// 証紙
		if ( $strViewColumnName == "lngCertificateClassCode" )
		{
			$arySelectQuery[] = ", p.lngCertificateClassCode as lngCertificateClassCode\n";
			$arySelectQuery[] = ", certificate.strCertificateClassName as strCertificateClassName\n";
			$flgCertificateClass = TRUE;
		}
		// 版権元
		if ( $strViewColumnName == "lngCopyrightCode" )
		{
			$arySelectQuery[] = ", p.lngCopyrightCode as lngCopyrightCode\n";
			$arySelectQuery[] = ", copyright.strCopyrightName as strCopyrightName\n";
			$flgCopyright = TRUE;
		}
		// 版権元備考
		if ( $strViewColumnName == "strCopyrightNote" )
		{
			$arySelectQuery[] = ", p.strCopyrightNote as strCopyrightNote\n";
		}
		// 版権表示（刻印）
		if ( $strViewColumnName == "strCopyrightDisplayStamp" )
		{
			$arySelectQuery[] = ", p.strCopyrightDisplayStamp as strCopyrightDisplayStamp\n";
		}
		// 版権表示（印刷物）
		if ( $strViewColumnName == "strCopyrightDisplayPrint" )
		{
			$arySelectQuery[] = ", p.strCopyrightDisplayPrint as strCopyrightDisplayPrint\n";
		}
		// 製品構成
		if ( $strViewColumnName == "strProductComposition" )
		{
			$arySelectQuery[] = ", p.strProductComposition as strProductComposition\n";
		}
		// アッセンブリ内容
		if ( $strViewColumnName == "strAssemblyContents" )
		{
			$arySelectQuery[] = ", p.strAssemblyContents as strAssemblyContents\n";
		}
		// 仕様詳細
		if ( $strViewColumnName == "strSpecificationDetails" )
		{
			$arySelectQuery[] = ", p.strSpecificationDetails as strSpecificationDetails\n";
		}
		
		// ワークフロー状態
		if ( $strViewColumnName == "lngWorkFlowStatusCode" )
		{
			$arySelectQuery[] = ", (select strWorkflowStatusName from m_WorkflowStatus where lngWorkflowStatusCode = tw.lngWorkflowStatusCode) as lngWorkFlowStatusCode";
			$arySelectQuery[] = ",lngproductstatuscode";
			$flgWorkFlowStatus = TRUE;
		}
	}

	// 条件の追加
	$detailFlag = FALSE;

	// ログインユーザーの権限により削除された商品の表示非表示を切り替える
	if ( !$aryUserAuthority["SearchDelete"] )
	{
		$aryQuery[] = " WHERE p.bytInvalidFlag = FALSE\n";
	}
	else
	{
		$aryQuery[] = " WHERE p.lngProductNo >= 0\n";
	}

	// 表示用カラムに設定されている内容を検索用に文字列設定
	for ( $i = 0; $i < count($arySearchColumn); $i++ )
	{
		$strSearchColumnName = $arySearchColumn[$i];

		// ////商品マスタ内の検索条件////
		// 製品コード
		if ( $strSearchColumnName == "strProductCode" )
		{
			if ( $arySearchDataColumn["strProductCodeFrom"] )
			{
				$strNewProductCode = $arySearchDataColumn["strProductCodeFrom"];
				$aryQuery[] = " AND p.strProductCode >= '" . $strNewProductCode . "'\n";
			}
			if ( $arySearchDataColumn["strProductCodeTo"] )
			{
				$strNewProductCode = $arySearchDataColumn["strProductCodeTo"];
				$aryQuery[] = " AND p.strProductCode <= '" . $strNewProductCode . "'\n";
			}
		}
// 2004.04.12 suzukaze update start
		// 製品名称
		if ( $strSearchColumnName == "strProductName" )
		{
			if ( $arySearchDataColumn["strProductName"] )
			{
				$aryQuery[] = " AND UPPER(p.strProductName) LIKE UPPER('%" . $arySearchDataColumn["strProductName"] . "%')\n";
			}
		}
		// 製品名称（英語）
		if ( $strSearchColumnName == "strProductEnglishName" )
		{
			if ( $arySearchDataColumn["strProductEnglishName"] )
			{
				$aryQuery[] = " AND UPPER(p.strProductEnglishName) LIKE UPPER('%" . $arySearchDataColumn["strProductEnglishName"] . "%')\n";
			}
		}
		// 入力者
		if ( $strSearchColumnName == "lngInputUserCode" )
		{
			if ( $arySearchDataColumn["lngInputUserCode"] )
			{
				$aryQuery[] = " AND input_u.strUserDisplayCode ~* '" . $arySearchDataColumn["lngInputUserCode"] . "'\n";
				$flgInputUser = TRUE;
			}
			if ( $arySearchDataColumn["strInputUserName"] )
			{
				$aryQuery[] = " AND UPPER(input_u.strUserDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInputUserName"] . "%')\n";
				$flgInputUser = TRUE;
			}
		}
		// 部門
		if ( $strSearchColumnName == "lngInChargeGroupCode" )
		{
			if ( $arySearchDataColumn["lngInChargeGroupCode"] )
			{
				$aryQuery[] = " AND inchg_g.strGroupDisplayCode ~* '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'\n";
				$flgInChargeGroup = TRUE;
			}
			if ( $arySearchDataColumn["strInChargeGroupName"] )
			{
				$aryQuery[] = " AND UPPER(inchg_g.strGroupDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeGroupName"] . "%')\n";
				$flgInChargeGroup = TRUE;
			}
		}
		// 担当者
		if ( $strSearchColumnName == "lngInChargeUserCode" )
		{
			if ( $arySearchDataColumn["lngInChargeUserCode"] )
			{
				$aryQuery[] = " AND inchg_u.strUserDisplayCode ~* '" . $arySearchDataColumn["lngInChargeUserCode"] . "'\n";
				$flgInChargeUser = TRUE;
			}
			if ( $arySearchDataColumn["strInChargeUserName"] )
			{
				$aryQuery[] = " AND UPPER(inchg_u.strUserDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strInChargeUserName"] . "%')\n";
				$flgInChargeUser = TRUE;
			}
		}
		// カテゴリー
		if ( $strSearchColumnName == "lngCategoryCode" )
		{
			if ( $arySearchDataColumn["lngCategoryCode"] )
			{
				$aryQuery[] = " AND p.lngCategoryCode = " . $arySearchDataColumn["lngCategoryCode"]. "\n";
				$flgCategory = TRUE;
			}
		}
		// 顧客品番
		if ( $strSearchColumnName == "strGoodsCode" )
		{
			if ( $arySearchDataColumn["strGoodsCode"] )
			{
				$aryQuery[] = " AND UPPER(p.strGoodsCode) LIKE UPPER('%" . $arySearchDataColumn["strGoodsCode"] . "%')\n";
			}
		}
		// 商品名称
		if ( $strSearchColumnName == "strGoodsName" )
		{
			if ( $arySearchDataColumn["strGoodsName"] )
			{
				$aryQuery[] = " AND UPPER(p.strGoodsName) LIKE UPPER('%" . $arySearchDataColumn["strGoodsName"] . "%')\n";
			}
		}
		// 顧客
		if ( $strSearchColumnName == "lngCustomerCompanyCode" )
		{
			if ( $arySearchDataColumn["lngCustomerCompanyCode"] )
			{
				$aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngCustomerCompanyCode"] . "'\n";
				$flgCustomerCompany = TRUE;
			}
			if ( $arySearchDataColumn["strCustomerCompanyName"] )
			{
				$aryQuery[] = " AND UPPER(cust_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strCustomerCompanyName"] . "%')\n";
				$flgCustomerCompany = TRUE;
			}
		}
		// 顧客担当者
		if ( $strSearchColumnName == "lngCustomerUserCode" )
		{
			if ( $arySearchDataColumn["lngCustomerUserCode"] )
			{
				$aryQuery[] = " AND cust_u.strUserDisplayCode ~* '" . $arySearchDataColumn["lngCustomerUserCode"] . "'\n";
				$flgCustomerUser = TRUE;
			}
			else if ( $arySearchDataColumn["strCustomerUserName"] )
			{
				$aryQuery[] = " AND (UPPER(cust_u.strUserDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strCustomerUserName"] . "%')\n";
				$aryQuery[] = " OR UPPER(p.strCustomerUserName) LIKE UPPER('%" . $arySearchDataColumn["strCustomerUserName"] . "%'))\n";
				$flgCustomerUser = TRUE;
			}
		}
		// 生産工場
		if ( $strSearchColumnName == "lngFactoryCode" )
		{
			if ( $arySearchDataColumn["lngFactoryCode"] )
			{
				$aryQuery[] = " AND fact_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngFactoryCode"] . "'\n";
				$flgFactory = TRUE;
			}
			if ( $arySearchDataColumn["strFactoryName"] )
			{
				$aryQuery[] = " AND UPPER(fact_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strFactoryName"] . "%')\n";
				$flgFactory = TRUE;
			}
		}
		// アッセンブリ工場
		if ( $strSearchColumnName == "lngAssemblyFactoryCode" )
		{
			if ( $arySearchDataColumn["lngAssemblyFactoryCode"] )
			{
				$aryQuery[] = " AND assemfact_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngAssemblyFactoryCode"] . "'\n";
				$flgAssemblyFactory = TRUE;
			}
			if ( $arySearchDataColumn["strAssemblyFactoryName"] )
			{
				$aryQuery[] = " AND UPPER(assemfact_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strAssemblyFactoryName"] . "%')\n";
				$flgAssemblyFactory = TRUE;
			}
		}
		// 納品場所
		if ( $strSearchColumnName == "lngDeliveryPlaceCode" )
		{
			if ( $arySearchDataColumn["lngDeliveryPlaceCode"] )
			{
				$aryQuery[] = " AND delv_c.strCompanyDisplayCode ~* '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'\n";
				$flgDeliveryPlace = TRUE;
			}
			if ( $arySearchDataColumn["strDeliveryPlaceName"] )
			{
				$aryQuery[] = " AND UPPER(delv_c.strCompanyDisplayName) LIKE UPPER('%" . $arySearchDataColumn["strDeliveryPlaceName"] . "%')\n";
				$flgDeliveryPlace = TRUE;
			}
		}
// 2004.04.12 suzukaze update end
		// 納期
		if ( $strSearchColumnName == "dtmDeliveryLimitDate" )
		{
			if ( $arySearchDataColumn["dtmDeliveryLimitDateFrom"] )
			{
				$aryQuery[] = " AND p.dtmDeliveryLimitDate >= To_Date( '" . $arySearchDataColumn["dtmDeliveryLimitDateFrom"] . "', 'YYYY/MM' )\n";
			}
			if ( $arySearchDataColumn["dtmDeliveryLimitDateTo"] )
			{
				$dtmSearchDate = $arySearchDataColumn["dtmDeliveryLimitDateTo"] . " 23:59:59";
				$aryQuery[] = " AND p.dtmDeliveryLimitDate <= To_Date( '" . $arySearchDataColumn["dtmDeliveryLimitDateTo"] . "', 'YYYY/MM' )\n";
			}
		}
		// 証紙
		if ( $strSearchColumnName == "lngCertificateClassCode" )
		{
			if ( $arySearchDataColumn["lngCertificateClassCode"] )
			{
				$aryQuery[] = " AND p.lngCertificateClassCode = " . $arySearchDataColumn["lngCertificateClassCode"] . "\n";
			}
		}
		// 版権元
		if ( $strSearchColumnName == "lngCopyrightCode" )
		{
			if ( $arySearchDataColumn["lngCopyrightCode"] )
			{
				$aryQuery[] = " AND p.lngCopyrightCode = " . $arySearchDataColumn["lngCopyrightCode"] . "\n";
			}
		}
		// 作成日時
		if ( $strSearchColumnName == "dtmInsertDate" )
		{
			if ( $arySearchDataColumn["dtmInsertDateFrom"] )
			{
				$dtmSearchDate = $arySearchDataColumn["dtmInsertDateFrom"] . " 00:00:00";
				$aryQuery[] = " AND p.dtmInsertDate >= '" . $dtmSearchDate . "'\n";
			}
			if ( $arySearchDataColumn["dtmInsertDateTo"] )
			{
				$dtmSearchDate = $arySearchDataColumn["dtmInsertDateTo"] . " 23:59:59";
				$aryQuery[] = " AND p.dtmInsertDate <= '" . $dtmSearchDate . "'\n";
			}
		}
		// 改訂日時
		if ( $strSearchColumnName == "dtmRevisionDate" )
		{
			if ( $arySearchDataColumn["dtmRevisionDateFrom"] )
			{
				$dtmSearchDate = $arySearchDataColumn["dtmRevisionDateFrom"] . " 00:00:00";
				$aryQuery[] = " AND p.dtmUpdateDate >= '" . $dtmSearchDate . "'\n";
			}
			if ( $arySearchDataColumn["dtmRevisionDateTo"] )
			{
				$dtmSearchDate = $arySearchDataColumn["dtmRevisionDateTo"] . " 23:59:59";
				$aryQuery[] = " AND p.dtmUpdateDate <= '" . $dtmSearchDate . "'\n";
			}
		}

		//////  以下商品企画マスタ内検索  //////////
		// 企画進捗状況
		if ( $strSearchColumnName == "lngGoodsPlanProgressCode" )
		{
			if ( $arySearchDataColumn["lngGoodsPlanProgressCode"] )
			{
				$aryQuery[] = " AND t_gp.lngGoodsPlanProgressCode = " . $arySearchDataColumn["lngGoodsPlanProgressCode"] . " ";
				$flgT_GoodsPlan = TRUE;
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
				}
			}

	}

	// SQL文の作成
	$strQuery = "SELECT distinct p.lngProductNo as lngProductNo, p.lngInChargeGroupCode as lngGroupCode, p.bytInvalidFlag as bytInvalidFlag\n";

	// 表示用カラムの設定
	for ( $i = 0; $i < count( $arySelectQuery ); $i++ )
	{
		$strQuery .= $arySelectQuery[$i];
	}

	$strQuery .= " FROM m_Product p\n";
	
	// 追加表示用の参照マスタ対応
	if ( $flgInputUser )
	{
		$strQuery .= " LEFT JOIN m_User input_u ON p.lngInputUserCode = input_u.lngUserCode\n";
	}
	if ( $flgInChargeGroup )
	{
		$strQuery .= " LEFT JOIN m_Group inchg_g ON p.lngInChargeGroupCode = inchg_g.lngGroupCode\n";
	}
	if ( $flgInChargeUser )
	{
		$strQuery .= " LEFT JOIN m_User inchg_u ON p.lngInChargeUserCode = inchg_u.lngUserCode\n";
	}
	if ( $flgCategory )
	{
		$strQuery .= " LEFT JOIN m_Category mc ON p.lngCategoryCode = mc.lngCategoryCode\n";
	}
	if ( $flgCustomerCompany )
	{
		$strQuery .= " LEFT JOIN m_Company cust_c ON p.lngCustomerCompanyCode = cust_c.lngCompanyCode\n";
	}
	if ( $flgCustomerUser )
	{
		$strQuery .= " LEFT JOIN m_User cust_u ON p.lngCustomerUserCode = cust_u.lngUserCode\n";
	}
	if ( $flgPackingUnit )
	{
		$strQuery .= " LEFT JOIN m_ProductUnit packingunit ON p.lngPackingUnitCode = packingunit.lngProductUnitCode\n";
	}
	if ( $flgProductUnit )
	{
		$strQuery .= " LEFT JOIN m_ProductUnit productunit ON p.lngProductUnitCode = productunit.lngProductUnitCode\n";
	}
	if ( $flgProductForm )
	{
		$strQuery .= " LEFT JOIN m_ProductForm productform ON p.lngProductFormCode = productform.lngProductFormCode\n";
	}
	if ( $flgFactory )
	{
		$strQuery .= " LEFT JOIN m_Company fact_c ON p.lngFactoryCode = fact_c.lngCompanyCode\n";
	}
	if ( $flgAssemblyFactory )
	{
		$strQuery .= " LEFT JOIN m_Company assemfact_c ON p.lngAssemblyFactoryCode = assemfact_c.lngCompanyCode\n";
	}
	if ( $flgDeliveryPlace )
	{
		$strQuery .= " LEFT JOIN m_Company delv_c ON p.lngDeliveryPlaceCode = delv_c.lngCompanyCode\n";
	}
	if ( $flgTargetAge )
	{
		$strQuery .= " LEFT JOIN m_TargetAge targetage ON p.lngTargetAgeCode = targetage.lngTargetAgeCode\n";
	}
	if ( $flgCertificateClass )
	{
		$strQuery .= " LEFT JOIN m_CertificateClass certificate ON p.lngCertificateClassCode = certificate.lngCertificateClassCode\n";
	}
	if ( $flgCopyright )
	{
		$strQuery .= " LEFT JOIN m_Copyright copyright ON p.lngCopyrightCode = copyright.lngCopyrightCode\n";
	}

	if ( $flgWorkFlowStatus )
	{
//		$aryFromQuery[] = " left join
		$strQuery .= " left join
		( m_workflow mw
			left join t_workflow tw
			on mw.lngworkflowcode = tw.lngworkflowcode
			and tw.lngworkflowsubcode = (select max(lngworkflowsubcode) from t_workflow where lngworkflowcode = tw.lngworkflowcode)
		) on  mw.strworkflowkeycode = p.strProductCode
		and mw.dtmstartdate = (select max(dtmstartdate) from m_workflow where strworkflowkeycode = mw.strworkflowkeycode)
			and mw.lngfunctioncode = " . DEF_FUNCTION_P1; // 商品登録時のWFデータを対象にする為に条件指定
	}



	// 商品企画マスタ対応
	if ( $flgT_GoodsPlan )
	{
		$strQuery .= ", t_GoodsPlan t_gp\n";
	}

	for ( $i = 0; $i < count( $aryQuery ); $i++ )
	{
		$strQuery .= $aryQuery[$i];
	}

	if ( $flgT_GoodsPlan )
	{
		$strQuery .= " AND t_gp.lngProductNo = p.lngProductNo\n";
		$strQuery .= " AND t_gp.lngRevisionNo = ( "
			. "SELECT MAX( t_gp1.lngRevisionNo ) FROM t_GoodsPlan t_gp1 WHERE t_gp1.lngProductNo = p.lngProductNo )\n";
	}

	if( $arySearchDataColumn["strSort"] )
	{
		if ( $arySearchDataColumn["strSortOrder"] == "ASC" )
		{
			$strAsDs = "ASC";	//昇降
		}
		else
		{
			$strAsDs = "DESC";	//降順
		}
		$strColumnName = $arySearchDataColumn["strSort"];
		if ( $arySearchDataColumn["strSort"] == "lnggoodsplanprogresscode" )
		{
			$strQuery .= " ORDER BY " . $arySearchDataColumn["strSort"] . " " . $strAsDs . " , p.lngProductNo ASC" ;
		}
		else
		{
			$strQuery .= " ORDER BY " . $arySearchDataColumn["strSort"] . " " . $strAsDs . " , p.lngProductNo ASC" ;
		}
	}
	else
	{
		$strQuery .= " ORDER BY p.lngProductNo ASC\n";
	}

//fncDebug("lib_ps.txt", $strQuery, __FILE__, __LINE__);

	return $strQuery;
}






/**
* 検索結果表示関数
*
*	検索結果からテーブル構成で結果を出力する関数
*
*	@param  Integer $lngColumnCount 		行数
*	@param  Array 	$aryResult 				検索結果が格納された配列
*	@param  Array 	$aryViewColumn 			表示対象カラム名の配列
*	@param  Array 	$aryData 				ＰＯＳＴデータ群
*	@param	Array	$aryUserAuthority		ユーザーの操作に対する権限が入った配列
*	@access public
*/
function fncSetProductViewTable ( $lngColumnCount, $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache )
{
	$aryHtml[] =  "<td nowrap align=\"center\">" . $lngColumnCount . "</td>";

	// 表示対象カラムの配列より結果の出力
	for ( $j = 0; $j < count($aryViewColumn); $j++ )
	{
		$strColumnName = $aryViewColumn[$j];

		if ( $aryResult["bytinvalidflag"] == "f" )
		{
			$aryResult["bytinvalidflag"] = 0;
		}
		else
		{
			$aryResult["bytinvalidflag"] = 1;
		}

		///////////////////////////////////
		////// 表示対象がボタンの場合 /////
		///////////////////////////////////
		if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" )
		{
			// ボタン種により変更
			// 詳細表示
			if ( $strColumnName == "btnDetail" )
			{
				if ( ( $aryResult["bytinvalidflag"] and $aryUserAuthority["DetailDelete"] ) 
					or ( !$aryResult["bytinvalidflag"] and $aryUserAuthority["Detail"] ) )
				{
					// 商品データが削除対象の場合、詳細表示ボタンは選択不可
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', 0 );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/p/result/index2.php?lngProductNo=" . $aryResult["lngproductno"] . "&strSessionID=" . $aryData["strSessionID"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'detail' )\"><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\"></td>";
				}
			}

			// 修正
			if ( $strColumnName == "btnFix" )
			{
				// 商品が削除データの場合は選択不可
				if ( (!$aryResult["bytinvalidflag"] and $aryUserAuthority["Fix"])
					and $aryResult["lngproductstatuscode"] != DEF_PRODUCT_APPLICATE
				)
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', 0 );\"><a class=\"cells\" href=\"javascript:fncShowDialogRenew('/p/regist/renew.php?strProductCode=" . $aryResult["strproductcode"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeRenew' , 'NO' , " . $aryData["lngLanguageCode"] . " )\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\"></td>";
				}
			}

			// 削除
			if ( $strColumnName == "btnDelete" )
			{
				// 商品が削除データの場合は選択不可（申請中の場合も）
				if ( (!$aryResult["bytinvalidflag"] and $aryUserAuthority["Delete"]) 
					and $aryResult["lngproductstatuscode"] != DEF_PRODUCT_APPLICATE
				)
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', 0 );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon( '/p/result/index3.php?lngProductNo=" . $aryResult["lngproductno"] . "&strSessionID=" . $aryData["strSessionID"] . "&lngLanguageCode=" . $aryData["lngLanguageCode"] . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'delete' )\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"REMOVE\"></a></td>";
				}
				else
				{
					$aryHtml[] = "<td bgcolor=\"#FFFFFF\" align=\"center\"></td>";
				}
			}
		}

		///////////////////////////////////
		////// 表示対象が日付の場合 ///////
		///////////////////////////////////
		// 作成日時、改訂日時
		else if ( $strColumnName == "dtmInsertDate" or $strColumnName == "dtmRevisionDate" )
		{
			$strLowerColumnName = strtolower($strColumnName);
			if ( $aryResult[$strLowerColumnName] )
			{
				$aryHtml[] = "<td align=\"left\" nowrap>" . str_replace( "-", "/", $aryResult[$strLowerColumnName] ) . "</td>";
			}
			else
			{
				$aryHtml[] = "<td align=\"left\" nowrap></td>";
			}
		}
		// 納期
		else if ( $strColumnName == "dtmDeliveryLimitDate" )
		{
			if ( $aryResult["dtmdeliverylimitdate"] )
			{
				$dtmNewDate = substr( $aryResult["dtmdeliverylimitdate"], 0, 7 );
				$aryHtml[] = "<td align=\"left\" nowrap>" . str_replace( "-", "/", $dtmNewDate ) . "</td>";
			}
			else
			{
				$aryHtml[] = "<td align=\"left\" nowrap></td>";
			}
		}

		/////////////////////////////////////////////////
		////// 表示対象がコードから名称参照の場合 ///////
		/////////////////////////////////////////////////
		// 企画進行状況
		else if ( $strColumnName == "lngGoodsPlanProgressCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["lnggoodsplanprogresscode"] )
			{
				$aryGoodsPlanProgressCode = $objCache->GetValue("lnggoodsplanprogresscode", $aryResult["lnggoodsplanprogresscode"]);
				if( !is_array($aryGoodsPlanProgressCode) )
				{
					// 企画進行状況名称の取得
					$strGoodsPlanProgressName = fncGetMasterValue( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname" , 
						$aryResult["lnggoodsplanprogresscode"], "", $objDB );
					// 企画進行状況名称の設定
					$aryGoodsPlanProgressCode[0] = $strGoodsPlanProgressName;
					$objCache->SetValue("lnggoodsplanprogresscode", $strGoodsPlanProgressName, $aryGoodsPlanProgressCode);
				}
				$strText .= $aryGoodsPlanProgressCode[0] . "</td>";
			}
			$aryHtml[] = $strText;
		}
		// 入力者
		else if ( $strColumnName == "lngInputUserCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strinputuserdisplaycode"] )
			{
				$strText .= "[" . $aryResult["strinputuserdisplaycode"] ."]";
			}
			else
			{
				$strText .= "     ";
			}
			$strText .= " " . $aryResult["strinputuserdisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
		// 部門
		else if ( $strColumnName == "lngInChargeGroupCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strinchargegroupdisplaycode"] )
			{
				$strText .= "[" . $aryResult["strinchargegroupdisplaycode"] ."]";
			}
			else
			{
				$strText .= "    ";
			}
			$strText .= " " . $aryResult["strinchargegroupdisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
		// 担当者
		else if ( $strColumnName == "lngInChargeUserCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strinchargeuserdisplaycode"] )
			{
				$strText .= "[" . $aryResult["strinchargeuserdisplaycode"] ."]";
			}
			else
			{
				$strText .= "     ";
			}
			$strText .= " " . $aryResult["strinchargeuserdisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
		// 顧客
		else if ( $strColumnName == "lngCustomerCompanyCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strcustomercompanydisplaycode"] )
			{
				$strText .= "[" . $aryResult["strcustomercompanydisplaycode"] ."]";
			}
			else
			{
				$strText .= "      ";
			}
			$strText .= " " . $aryResult["strcustomercompanydisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
		// 顧客担当者
		else if ( $strColumnName == "lngCustomerUserCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strcustomeruserdisplaycode"] )
			{
				$strText .= "[" . $aryResult["strcustomeruserdisplaycode"] ."]";
				$strText .= " " . $aryResult["strcustomeruserdisplayname"] . "</td>";
			}
			else
			{
				$strText .= "      ";
				$strText .= " " . $aryResult["strcustomerusername"] . "</td>";
			}
			$aryHtml[] = $strText;
		}
		// 荷姿単位
		else if ( $strColumnName == "lngPackingUnitCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap>" . $aryResult["strpackingunitname"] . "</td>";
		}
		// 製品単位
		else if ( $strColumnName == "lngProductUnitCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap>" . $aryResult["strproductunitname"] . "</td>";
		}
		// 商品形態
		else if ( $strColumnName == "lngProductFormCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap>" . $aryResult["strproductformname"] . "</td>";
		}
		// 生産工場
		else if ( $strColumnName == "lngFactoryCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strfactorydisplaycode"] )
			{
				$strText .= "[" . $aryResult["strfactorydisplaycode"] ."]";
			}
			else
			{
				$strText .= "      ";
			}
			$strText .= " " . $aryResult["strfactorydisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
		// アッセンブリ工場
		else if ( $strColumnName == "lngAssemblyFactoryCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strassemblyfactorydisplaycode"] )
			{
				$strText .= "[" . $aryResult["strassemblyfactorydisplaycode"] ."]";
			}
			else
			{
				$strText .= "      ";
			}
			$strText .= " " . $aryResult["strassemblyfactorydisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
		// 納品場所
		else if ( $strColumnName == "lngDeliveryPlaceCode" )
		{
			$strText = "<td align=\"left\" nowrap>";
			if ( $aryResult["strdeliveryplacedisplaycode"] )
			{
				$strText .= "[" . $aryResult["strdeliveryplacedisplaycode"] ."]";
			}
			else
			{
				$strText .= "      ";
			}
			$strText .= " " . $aryResult["strdeliveryplacedisplayname"] . "</td>";
			$aryHtml[] = $strText;
		}
		// 対象年齢
		else if ( $strColumnName == "lngTargetAgeCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap>" . $aryResult["strtargetagename"] . "</td>";
		}
		// 証紙
		else if ( $strColumnName == "lngCertificateClassCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap>" . $aryResult["strcertificateclassname"] . "</td>";
		}
		// 版権元
		else if ( $strColumnName == "lngCopyrightCode" )
		{
			$aryHtml[] = "<td align=\"left\" nowrap>" . $aryResult["strcopyrightname"] . "</td>";
		}

		///////////////////////////////////
		////// 表示対象が数量の場合 ///////
		///////////////////////////////////
		// 内箱（袋）入数、カートン入数
		else if ( $strColumnName == "lngBoxQuantity" or $strColumnName == "lngCartonQuantity" )
		{
			$strLowerColumnName = strtolower($strColumnName);
			$strText = "<td align=\"right\" nowrap>";
			if ( !$aryResult[$strLowerColumnName] )
			{
				$strText .= "0</td>";
			}
			else
			{
				$strText .= $aryResult[$strLowerColumnName] . "</td>";
			}
			$aryHtml[] = $strText;
		}
		// 生産予定数
		else if ( $strColumnName == "lngProductionQuantity" )
		{
			$strText = "<td align=\"right\" nowrap>";
			if ( !$aryResult["lngproductionquantity"] )
			{
				$strText .= "0";
			}
			else
			{
				$strText .= $aryResult[lngproductionquantity];
			}
			// 単位の設定
			if ( $aryResult["lngproductionunitcode"] )
			{
				$aryProductUnit = $objCache->GetValue("lngproductunitcode", $aryResult["lngproductionunitcode"]);
				if( !is_array($aryProductUnit) )
				{
					// 単位名称の取得
					$strProductUnitName = fncGetMasterValue( "m_productunit", "lngproductunitcode", "strproductunitname" , 
						$aryResult["lngproductionunitcode"], "", $objDB );
					// 単位名称の設定
					$aryProductUnit[0] = $strProductUnitName;
					$objCache->SetValue("lngproductunitcode", $strProductUnitName, $aryProductUnit);
				}
				$strText .= " " . $aryProductUnit[0];
			}
			$strText .= "</td>";
			$aryHtml[] = $strText;
		}
		// 初回納品数
		else if ( $strColumnName == "lngFirstDeliveryQuantity" )
		{
			$strText = "<td align=\"right\" nowrap>";
			if ( !$aryResult["lngfirstdeliveryquantity"] )
			{
				$strText .= "0";
			}
			else
			{
				$strText .= $aryResult[lngfirstdeliveryquantity];
			}
			// 単位の設定
			if ( $aryResult["lngfirstdeliveryunitcode"] )
			{
				$aryProductUnit = $objCache->GetValue("lngproductunitcode", $aryResult["lngfirstdeliveryunitcode"]);
				if( !is_array($aryProductUnit) )
				{
					// 単位名称の取得
					$strProductUnitName = fncGetMasterValue( "m_productunit", "lngproductunitcode", "strproductunitname" , 
						$aryResult["lngfirstdeliveryunitcode"], "", $objDB );
					// 単位名称の設定
					$aryProductUnit[0] = $strProductUnitName;
					$objCache->SetValue("lngproductunitcode", $strProductUnitName, $aryProductUnit);
				}
				$strText .= " " . $aryProductUnit[0];
			}
			$strText .= "</td>";
			$aryHtml[] = $strText;
		}

		///////////////////////////////////
		////// 表示対象が価格の場合 ///////
		///////////////////////////////////
		// 納価、上代
		else if ( $strColumnName == "curProductPrice" or $strColumnName == "curRetailPrice" )
		{
			$strLowerColumnName = strtolower($strColumnName);
			$strText = "<td align=\"right\" nowrap>";
			$strText .= DEF_PRODUCT_MONETARYSIGN . " ";
			if ( !$aryResult[$strLowerColumnName] )
			{
				$strText .= "0.00</td>";
			}
			else
			{
				$strText .= $aryResult[$strLowerColumnName] . "</td>";
			}
			$aryHtml[] = $strText;
		}

		///////////////////////////////////
		////// 表示対象が数値の場合 ///////
		///////////////////////////////////
		// ロイヤリティ
		else if ( $strColumnName == "lngRoyalty" )
		{
			$aryHtml[] = "<td align=\"right\" nowrap>" . $aryResult["lngroyalty"] . "</td>";
		}

		/////////////////////////////////////////
		////// 表示対象が文字列項目の場合 ///////
		/////////////////////////////////////////
		// その他の項目はそのまま出力
		else
		{
			$strLowerColumnName = strtolower($strColumnName);
			$strText = "<td align=\"left\" nowrap>";
			// 仕様詳細は改行設定
			if ( $strColumnName == "strSpecificationDetails" )
			{
				$strText .= $aryResult[$strLowerColumnName] . "</td>";
			}
			// 製品構成は文字列追加
			else if ( $strColumnName == "strProductComposition" )
			{
				if ( $aryResult[$strLowerColumnName] )
				{
					$strText .= "全" . $aryResult[$strLowerColumnName] . "種アッセンブリ</td>";
				}
				else
				{
					$strText .= $aryResult[$strLowerColumnName] . "</td>";
				}
			}
			else
			{
				$strText .= $aryResult[$strLowerColumnName] . "</td>";
			}
			$aryHtml[] = $strText;
		}
	}

	$aryHtml[] = "</tr>";

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
function fncSetProductTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName )
{
	// テーブルの形成
	$lngResultCount = count($aryResult);

	$aryHtml[] = "<span id=\"COPYAREA1\">";
	$aryHtml[] = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\" bproduct=\"0\" bgcolor=\"#6f8180\" align=\"center\">";

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
				// ソート項目以外の場合
				if ( $strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete" )
				{
					// 詳細ボタンの場合は、詳細表示可能なユーザーのみ表示する
					if ( $strColumnName == "btnDetail" and $aryUserAuthority["Detail"] )
					{
						$aryHtml[] = "<td nowrap>".$aryTytle[$strColumnName]."</td>";
					}
					// 修正ボタンの場合は、修正処理可能なユーザーのみ表示する
					if ( $strColumnName == "btnFix" and $aryUserAuthority["Fix"] )
					{
						$aryHtml[] = "<td nowrap>".$aryTytle[$strColumnName]."</td>";
					}
					// 削除ボタンの場合は、削除処理可能なユーザーのみ表示する
					if ( $strColumnName == "btnDelete" and $aryUserAuthority["Delete"])
					{
						$aryHtml[] = "<td nowrap>".$aryTytle[$strColumnName]."</td>";
					}
				}
				// ソート項目の場合
				else
				{
					$strText = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" ";
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
						$strSortOrder = "ASC";
					}
					$strText .= "onclick=\"fncSort2('" . $aryTableName[$strColumnName] . "', '" . $strSortOrder . "');\">";
					$strText .= "<a href=\"#\">".$aryTytle[$strColumnName]."</a></td>";
					$aryHtml[] = $strText;
				}
			}
			$aryHtml[] = "</tr>";
//			$aryHtml[] = "</span>";

			// ダミーTR
			$aryHtml[] = "<tr id=\"DummyTR\"><td colspan=\"" . count($aryViewColumn) . "\">&nbsp;</td></tr>";

//			$aryHtml[] = "<span id=\"COPYAREA2\">";
		}

// 項目名列の生成 end=========================================

// 検索結果出力　　共通start==================================
		reset( $aryResult[$i] );

		// 行の背景色の取得
		if ( $aryResult[$i]["lnggroupcode"] != "" )
		{
			$aryGroupColor = $objCache->GetValue("lnggroupcode", $aryResult[$i]["lnggroupcode"]);
			if( !is_array($aryGroupColor) )
			{
				// グループ色の取得
				$strGroupDisplayColor = fncGetMasterValue( "m_group", "lnggroupcode", "strgroupdisplaycolor" , 
					$aryResult[$i]["lnggroupcode"], "", $objDB );
				// グループ色の設定
				$aryGroupColor[0] = $strGroupDisplayColor;
				$objCache->SetValue("lnggroupcode", $strGroupDisplayColor, $aryGroupColor);
			}
			$strGroupColor = $aryGroupColor[0];
		}
		else
		{
			$strGroupColor = "#FFFFFF";
		}

		// 検索結果部分の設定
		$aryHtml[] = "<tr class=\"Segs\" name=\"strTrName" . $i . "\" style=\"background:" . $strGroupColor . "\" onclick=\"fncSelectTrColor( this );\">";

//		$aryHtml[] = "<tr id=\"TD" . $lngColumnCount . "_0\" class=\"Segs\" name=\"strTrName" . $lngColumnCount . "\" onclick=\"fncSelectSomeTrColor( this, 'TD" . $lngColumnCount . "_', " . $lngDetailCount . " );\" style=\"background:#99FF99\">";

		$lngColumnCount++;

		// １レコード分の出力
		$aryHtml_add = fncSetProductViewTable ( $lngColumnCount, $aryResult[$i], $aryViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache );
		for ( $j = 0; $j < count($aryHtml_add); $j++ )
		{
			$aryHtml[] = $aryHtml_add[$j];
		}
// 検索結果出力　　共通end==================================
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