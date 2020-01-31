<?
/**
* 指定された商品番号から商品ヘッダ情報を取得するＳＱＬ文を作成
*
*	指定商品情報の取得用ＳＱＬ文作成関数
*
*	@param  Integer 	$lngProductNo 			取得する商品番号
*	@param  Integer 	$lngRevisionNo 			取得する商品リビジョン番号
*	@return strQuery 	$strQuery 検索用SQL文
*	@access public
*/
function fncGetProductNoToInfoSQL ( $lngProductNo, $lngRevisionNo )
{
	// SQL文の作成
	$aryQuery[] = "SELECT distinct on (p.lngProductNo) p.lngProductNo as lngProductNo, \n";
	$aryQuery[] = " p.lngInChargeGroupCode as lngGroupCode, p.bytInvalidFlag as bytInvalidFlag\n";

	// 作成日時
	$aryQuery[] = ", to_char( p.dtmInsertDate, 'YYYY/MM/DD' ) as dtmInsertDate\n";
	// 企画進行状況
	$aryQuery[] = ", t_gp.lngGoodsPlanProgressCode as lngGoodsPlanProgressCode\n";
	$aryQuery[] = ", t_gp.strgoodsplanprogressname as strgoodsplanprogressname\n";
	// 改訂番号
	$aryQuery[] = ", t_gp.lngRevisionNo as lngRevisionNo\n";
	$aryQuery[] = ", t_gp.lnggoodsplancode as lnggoodsplancode\n";
	// 改訂日時
	$aryQuery[] = ", to_char( p.dtmUpdateDate, 'YYYY/MM/DD' ) as dtmRevisionDate\n";
	// 製品コード
	$aryQuery[] = ", p.strProductCode as strProductCode\n";
	// 製品名称
	$aryQuery[] = ", p.strProductName as strProductName\n";
	// 製品名称（英語）
	$aryQuery[] = ", p.strProductEnglishName as strProductEnglishName\n";
	// 入力者
	$aryQuery[] = ", p.lngInputUserCode as lngInputUserCode\n";
	$aryQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode\n";
	$aryQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName\n";
	// 部門
	$aryQuery[] = ", p.lngInChargeGroupCode as lngInChargeGroupCode\n";
	$aryQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode\n";
	$aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName\n";
	// 担当者
	$aryQuery[] = ", p.lngInChargeUserCode as lngInChargeUserCode\n";
	$aryQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode\n";
	$aryQuery[] = ", inchg_u.strUserDisplayName as strInChargeUserDisplayName\n";
	// 開発担当者
	$aryQuery[] = ", p.lngdevelopusercode as lngdevelopusercode\n";
	$aryQuery[] = ", devp_u.strUserDisplayCode as strDevelopUserDisplayCode\n";
	$aryQuery[] = ", devp_u.strUserDisplayName as strDevelopUserDisplayName\n";
	$aryQuery[] = ", mc.strCategoryName as lngCategoryCode\n";	// カテゴリー
		
	// 顧客品番
	$aryQuery[] = ", p.strGoodsCode as strGoodsCode\n";
	$aryQuery[] = ", cust_c.strDistinctCode as strDistinctCode\n";
	// 商品名称
	$aryQuery[] = ", p.strGoodsName as strGoodsName\n";
	// 顧客
	$aryQuery[] = ", p.lngCustomerCompanyCode as lngCustomerCompanyCode\n";
	$aryQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerCompanyDisplayCode\n";
	$aryQuery[] = ", cust_c.strCompanyDisplayName as strCustomerCompanyDisplayName\n";
	// 顧客担当者
	$aryQuery[] = ", p.lngCustomerUserCode as lngCustomerUserCode\n";
	$aryQuery[] = ", cust_u.strUserDisplayCode as strCustomerUserDisplayCode\n";
	$aryQuery[] = ", cust_u.strUserDisplayName as strCustomerUserDisplayName\n";
	$aryQuery[] = ", p.strCustomerUserName as strCustomerUserName\n";
	// 荷姿単位
	$aryQuery[] = ", p.lngPackingUnitCode as lngPackingUnitCode\n";
	$aryQuery[] = ", packingunit.strProductUnitName as strPackingUnitName\n";
	// 製品単位
	$aryQuery[] = ", p.lngProductUnitCode as lngProductUnitCode\n";
	$aryQuery[] = ", productunit.strProductUnitName as strProductUnitName\n";
	// 商品形態
	$aryQuery[] = ", p.lngProductFormCode as lngProductFormCode\n";
	$aryQuery[] = ", productform.strProductFormName as strProductFormName\n";
	// 内箱（袋）入数
	$aryQuery[] = ", To_char( p.lngBoxQuantity, '9,999,999,990' ) as lngBoxQuantity\n";
	// カートン入数
	$aryQuery[] = ", To_char( p.lngCartonQuantity, '9,999,999,990' ) as lngCartonQuantity\n";
	// 生産予定数
	$aryQuery[] = ", To_char( p.lngProductionQuantity, '9,999,999,990' ) as lngProductionQuantity\n";
	$aryQuery[] = ", p.lngProductionUnitCode as lngProductionUnitCode\n";
	
	// 初回納品数
	$aryQuery[] = ", To_char( p.lngFirstDeliveryQuantity, '9,999,999,990' ) as lngFirstDeliveryQuantity\n";
	$aryQuery[] = ", p.lngFirstDeliveryUnitCode as lngFirstDeliveryUnitCode\n";
	$aryQuery[] = ", fstdelyunit.strProductUnitName as strfirstdeliveryunitname\n";
	// 生産工場
	$aryQuery[] = ", p.lngFactoryCode as lngFactoryCode\n";
	$aryQuery[] = ", fact_c.strCompanyDisplayCode as strFactoryDisplayCode\n";
	$aryQuery[] = ", fact_c.strCompanyDisplayName as strFactoryDisplayName\n";
	// アッセンブリ工場
	$aryQuery[] = ", p.lngAssemblyFactoryCode as lngAssemblyFactoryCode\n";
	$aryQuery[] = ", assemfact_c.strCompanyDisplayCode as strAssemblyFactoryDisplayCode\n";
	$aryQuery[] = ", assemfact_c.strCompanyDisplayName as strAssemblyFactoryDisplayName\n";
	// 納品場所
	$aryQuery[] = ", p.lngDeliveryPlaceCode as lngDeliveryPlaceCode\n";
	$aryQuery[] = ", delv_c.strCompanyDisplayCode as strDeliveryPlaceDisplayCode\n";
	$aryQuery[] = ", delv_c.strCompanyDisplayName as strDeliveryPlaceDisplayName\n";
	// 納期
	$aryQuery[] = ", to_char( p.dtmDeliveryLimitDate, 'YYYY/MM' ) as dtmDeliveryLimitDate";
	// 納価
	$aryQuery[] = ", To_char( p.curProductPrice, '9,999,999,990.99' )  as curProductPrice\n";
	// 上代
	$aryQuery[] = ", To_char( p.curRetailPrice, '9,999,999,990.99' )  as curRetailPrice\n";
	// 対象年齢
	$aryQuery[] = ", p.lngTargetAgeCode as lngTargetAgeCode\n";
	$aryQuery[] = ", targetage.strTargetAgeName as strTargetAgeName\n";
	// ロイヤリティ
	$aryQuery[] = ", To_char( p.lngRoyalty, '9,999,999,990.99' )  as lngRoyalty\n";
	// 証紙
	$aryQuery[] = ", p.lngCertificateClassCode as lngCertificateClassCode\n";
	$aryQuery[] = ", certificate.strCertificateClassName as strCertificateClassName\n";
	// 版権元
	$aryQuery[] = ", p.lngCopyrightCode as lngCopyrightCode\n";
	$aryQuery[] = ", copyright.strCopyrightName as strCopyrightName\n";
	// 版権元備考
	$aryQuery[] = ", p.strCopyrightNote as strCopyrightNote\n";
	// 版権表示（刻印）
	$aryQuery[] = ", p.strCopyrightDisplayStamp as strCopyrightDisplayStamp\n";
	// 版権表示（印刷物）
	$aryQuery[] = ", p.strCopyrightDisplayPrint as strCopyrightDisplayPrint\n";
	// 製品構成
	$aryQuery[] = ", p.strProductComposition as strProductComposition\n";
	// アッセンブリ内容
	$aryQuery[] = ", p.strAssemblyContents as strAssemblyContents\n";
	// 仕様詳細
	$aryQuery[] = ", p.strSpecificationDetails as strSpecificationDetails\n";
	// リーバイスコード
	$aryQuery[] = ", p.strrevisecode as strrevisecode\n";


	$aryQuery[] = " FROM m_Product p\n";

	// 追加表示用の参照マスタ対応
	$aryQuery[] = " LEFT JOIN m_User input_u ON p.lngInputUserCode = input_u.lngUserCode\n";
	$aryQuery[] = " LEFT JOIN m_Group inchg_g ON p.lngInChargeGroupCode = inchg_g.lngGroupCode\n";
	$aryQuery[] = " LEFT JOIN m_User inchg_u ON p.lngInChargeUserCode = inchg_u.lngUserCode\n";
	$aryQuery[] = " LEFT JOIN m_User devp_u ON p.lngdevelopusercode = devp_u.lngUserCode\n";
	$aryQuery[] = " LEFT JOIN m_Category mc ON mc.lngCategoryCode = p.lngCategoryCode\n";
	$aryQuery[] = " LEFT JOIN m_Company cust_c ON p.lngCustomerCompanyCode = cust_c.lngCompanyCode\n";
	$aryQuery[] = " LEFT JOIN m_User cust_u ON p.lngCustomerUserCode = cust_u.lngUserCode\n";
	$aryQuery[] = " LEFT JOIN m_ProductUnit packingunit ON p.lngPackingUnitCode = packingunit.lngProductUnitCode\n";
	$aryQuery[] = " LEFT JOIN m_ProductUnit productunit ON p.lngProductUnitCode = productunit.lngProductUnitCode\n";
	$aryQuery[] = " LEFT JOIN m_ProductUnit fstdelyunit ON p.lngFirstDeliveryUnitCode = fstdelyunit.lngProductUnitCode\n";

	$aryQuery[] = " LEFT JOIN m_ProductForm productform ON p.lngProductFormCode = productform.lngProductFormCode\n";
	$aryQuery[] = " LEFT JOIN m_Company fact_c ON p.lngFactoryCode = fact_c.lngCompanyCode\n";
	$aryQuery[] = " LEFT JOIN m_Company assemfact_c ON p.lngAssemblyFactoryCode = assemfact_c.lngCompanyCode\n";
	$aryQuery[] = " LEFT JOIN m_Company delv_c ON p.lngDeliveryPlaceCode = delv_c.lngCompanyCode\n";
	$aryQuery[] = " LEFT JOIN m_TargetAge targetage ON p.lngTargetAgeCode = targetage.lngTargetAgeCode\n";
	$aryQuery[] = " LEFT JOIN m_CertificateClass certificate ON p.lngCertificateClassCode = certificate.lngCertificateClassCode\n";
	$aryQuery[] = " LEFT JOIN m_Copyright copyright ON p.lngCopyrightCode = copyright.lngCopyrightCode\n";

	$aryQuery[] = ", (select tt_gp.*, m_gp.strgoodsplanprogressname from t_GoodsPlan tt_gp \n";
	$aryQuery[] = " LEFT JOIN  m_goodsplanprogress m_gp on m_gp.lnggoodsplanprogresscode = tt_gp.lnggoodsplanprogresscode) t_gp\n";

	$aryQuery[] = " WHERE p.lngProductNo = " . $lngProductNo . "";
	$aryQuery[] = " AND p.lngRevisionNo = " . $lngRevisionNo . "";

	$aryQuery[] = " AND t_gp.lngProductNo = p.lngProductNo\n";
	$aryQuery[] = " AND t_gp.lngRevisionNo = ( SELECT MAX( t_gp1.lngRevisionNo ) FROM t_GoodsPlan t_gp1 WHERE t_gp1.lngProductNo = p.lngProductNo )\n";

	$strQuery = implode( "\n", $aryQuery );
	return $strQuery;
}






/**
* 詳細表示関数
*
*	テーブル構成で商品データ詳細を出力する関数
*
*	@param  Array 	$aryResult 	検索結果が格納された配列
* 	@param	Object	$objDB		ＤＢオブジェクト
*	@access public
*/
function fncSetProductTableData ( $aryResult, $objDB )
{
	$aryColumnNames = array_keys($aryResult);

	unset( $aryNewResult );

	// 表示対象カラムの配列より結果の出力
	for ( $i = 0; $i < count($aryColumnNames); $i++ )
	{
		$strColumnName = $aryColumnNames[$i];

		unset( $strText );

		///////////////////////////////////
		////// 表示対象が日付の場合 ///////
		///////////////////////////////////
		// 作成日時、改訂日時
		if ( $strColumnName == "dtminsertdate" or $strColumnName == "dtmrevisiondate" )
		{
			if ( $aryResult[$strColumnName] )
			{
				$aryNewResult[$strColumnName] = str_replace( "-", "/", $aryResult[$strColumnName] );
			}
		}
		// 納期
		else if ( $strColumnName == "dtmdeliverylimitdate" )
		{
			if ( $aryResult["dtmdeliverylimitdate"] )
			{
				$dtmNewDate = substr( $aryResult["dtmdeliverylimitdate"], 0, 7 );
				$aryNewResult[$strColumnName] = str_replace( "-", "/", $dtmNewDate );
			}
		}

		/////////////////////////////////////////////////
		////// 表示対象がコードから名称参照の場合 ///////
		/////////////////////////////////////////////////
		// 企画進行状況
		else if ( $strColumnName == "lnggoodsplanprogresscode" )
		{
			if ( $aryResult["lnggoodsplanprogresscode"] )
			{
				$aryNewResult[$strColumnName] = $aryResult["strgoodsplanprogressname"];
			}
		}
		// 入力者
		else if ( $strColumnName == "lnginputusercode" )
		{
			if ( $aryResult["strinputuserdisplaycode"] )
			{
				$strText = "[" . $aryResult["strinputuserdisplaycode"] ."]";
			}
			else
			{
				$strText = "     ";
			}
			$strText .= " " . $aryResult["strinputuserdisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// 部門
		else if ( $strColumnName == "lnginchargegroupcode" )
		{
			if ( $aryResult["strinchargegroupdisplaycode"] )
			{
				$strText = "[" . $aryResult["strinchargegroupdisplaycode"] ."]";
			}
			else
			{
				$strText = "    ";
			}
			$strText .= " " . $aryResult["strinchargegroupdisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// 担当者
		else if ( $strColumnName == "lnginchargeusercode" )
		{
			if ( $aryResult["strinchargeuserdisplaycode"] )
			{
				$strText = "[" . $aryResult["strinchargeuserdisplaycode"] ."]";
			}
			else
			{
				$strText = "     ";
			}
			$strText .= " " . $aryResult["strinchargeuserdisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// 開発担当者
		else if ( $strColumnName == "lngdevelopusercode" )
		{
			if ( $aryResult["strdevelopuserdisplaycode"] )
			{
				$strText = "[" . $aryResult["strdevelopuserdisplaycode"] ."]";
			}
			else
			{
				$strText = "     ";
			}
			$strText .= " " . $aryResult["strdevelopuserdisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// 顧客
		else if ( $strColumnName == "lngcustomercompanycode" )
		{
			if ( $aryResult["strcustomercompanydisplaycode"] )
			{
				$strText = "[" . $aryResult["strcustomercompanydisplaycode"] ."]";
			}
			else
			{
				$strText .= "      ";
			}
			$strText .= " " . $aryResult["strcustomercompanydisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// 顧客担当者
		else if ( $strColumnName == "lngcustomerusercode" )
		{
			if ( $aryResult["strcustomeruserdisplaycode"] )
			{
				$strText = "[" . $aryResult["strcustomeruserdisplaycode"] ."]";
				$strText .= " " . $aryResult["strcustomeruserdisplayname"];
			}
			else
			{
				$strText = "      ";
				$strText .= " " . $aryResult["strcustomerusername"];
			}
			$aryNewResult[$strColumnName] = $strText;
		}
		// 荷姿単位
		else if ( $strColumnName == "lngpackingunitcode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strpackingunitname"];
		}
		// 製品単位
		else if ( $strColumnName == "lngproductunitcode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strproductunitname"];
		}
		// 商品形態
		else if ( $strColumnName == "lngproductformcode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strproductformname"];
		}
		// 生産工場
		else if ( $strColumnName == "lngfactorycode" )
		{
			if ( $aryResult["strfactorydisplaycode"] )
			{
				$strText = "[" . $aryResult["strfactorydisplaycode"] ."]";
			}
			else
			{
				$strText = "      ";
			}
			$strText .= " " . $aryResult["strfactorydisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// アッセンブリ工場
		else if ( $strColumnName == "lngassemblyfactorycode" )
		{
			if ( $aryResult["strassemblyfactorydisplaycode"] )
			{
				$strText = "[" . $aryResult["strassemblyfactorydisplaycode"] ."]";
			}
			else
			{
				$strText = "      ";
			}
			$strText .= " " . $aryResult["strassemblyfactorydisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// 納品場所
		else if ( $strColumnName == "lngdeliveryplacecode" )
		{
			if ( $aryResult["strdeliveryplacedisplaycode"] )
			{
				$strText = "[" . $aryResult["strdeliveryplacedisplaycode"] ."]";
			}
			else
			{
				$strText = "      ";
			}
			$strText .= " " . $aryResult["strdeliveryplacedisplayname"];
			$aryNewResult[$strColumnName] = $strText;
		}
		// 対象年齢
		else if ( $strColumnName == "lngtargetagecode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strtargetagename"];
		}
		// 証紙
		else if ( $strColumnName == "lngcertificateclasscode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strcertificateclassname"];
		}
		// 版権元
		else if ( $strColumnName == "lngcopyrightcode" )
		{
			$aryNewResult[$strColumnName] = $aryResult["strcopyrightname"];
		}

		///////////////////////////////////
		////// 表示対象が数量の場合 ///////
		///////////////////////////////////
		// 内箱（袋）入数、カートン入数
		else if ( $strColumnName == "lngboxquantity" or $strColumnName == "lngcartonquantity" )
		{
			if ( !$aryResult[$strColumnName] )
			{
				$strText = "0";
			}
			else
			{
				$strText = $aryResult[$strColumnName];
			}
			$aryNewResult[$strColumnName] = $strText;
		}
		// 生産予定数
		else if ( $strColumnName == "lngproductionquantity" )
		{
			if ( !$aryResult["lngproductionquantity"] )
			{
				$strText = "0";
			}
			else
			{
				$strText = $aryResult["lngproductionquantity"];
			}
			// 単位の設定
			if ( $aryResult["strproductunitname"] )
			{
				$strText .= " " . $aryResult["strproductunitname"];
			}
			$aryNewResult[$strColumnName] = $strText;
		}
		// 初回納品数
		else if ( $strColumnName == "lngfirstdeliveryquantity" )
		{
			if ( !$aryResult["lngfirstdeliveryquantity"] )
			{
				$strText = "0";
			}
			else
			{
				$strText = $aryResult["lngfirstdeliveryquantity"];
			}
			// 単位の設定
			if ( $aryResult["strfirstdeliveryunitname"] )
			{
				$strText .= " " . $aryResult["strfirstdeliveryunitname"];
			}
			$aryNewResult[$strColumnName] = $strText;
		}

		///////////////////////////////////
		////// 表示対象が価格の場合 ///////
		///////////////////////////////////
		// 納価、上代
		else if ( $strColumnName == "curproductprice" or $strColumnName == "curretailprice" )
		{
			$strText = DEF_PRODUCT_MONETARYSIGN . " ";
			if ( !$aryResult[$strColumnName] )
			{
				$strText .= "0.00";
			}
			else
			{
				$strText .= $aryResult[$strColumnName];
			}
			$aryNewResult[$strColumnName] = $strText;
		}

		///////////////////////////////////
		////// 表示対象が数値の場合 ///////
		///////////////////////////////////
		// ロイヤリティ
		else if ( $strColumnName == "lngroyalty" )
		{
			$aryNewResult[$strColumnName] = $aryResult["lngroyalty"];
		}

		/////////////////////////////////////////
		////// 表示対象が文字列項目の場合 ///////
		/////////////////////////////////////////
		// その他の項目はそのまま出力
		else
		{
			// 仕様詳細は改行設定
			if ( $strColumnName == "strspecificationdetails" )
			{
				$strText = $aryResult[$strColumnName];
			}
			// 製品構成は文字列追加
			else if ( $strColumnName == "strproductcomposition" )
			{
				if ( $aryResult[$strColumnName] )
				{
					$strText = "全" . $aryResult[$strColumnName] . "種アッセンブリ";
				}
				else
				{
					$strText = $aryResult[$strColumnName];
				}
			}
			// 顧客品番は識別コードを追加
			else if ( $strColumnName == "strGoodsCode" )
			{
				$strText = $aryResult["strdistinctcode"] . " " . $aryResult[$strColumnName];
			}
			else
			{
				$strText = $aryResult[$strColumnName];
			}
			$aryNewResult[$strColumnName] = $strText;
		}
	}

	return $aryNewResult;
}






/**
* 詳細表示用カラム名セット関数
*
*	詳細表示時のカラム名（日本語、英語）での設定関数
*
*	@param  Array 	$aryResult 		検索結果が格納された配列
*	@param  Array 	$aryTytle 		カラム名が格納された配列
*	@access public
*/
function fncSetProductTabelName ( $aryResult, $aryTytle )
{
	$aryColumnNames = array_values($aryResult);

	// 表示対象カラムの配列より結果の出力
	for ( $i = 0; $i < count($aryColumnNames); $i++ )
	{
		$strColumnName = $aryColumnNames[$i];

		if ( $aryTytle[$strColumnName] )
		{
			$strNewColumnName = "CN" . $strColumnName;
			$aryNames[$strNewColumnName] = $aryTytle[$strColumnName];
		}
	}

	return $aryNames;
}






/**
* 指定のコードのデータを他のマスタで使用しているコード取得
*
*	指定コードに対して、指定されたマスタの検索関数
*
*	@param  String 		$strCode 		検索対象コード
*	@param	Integer		$lngMode		検索モード	1:製品コードから受注マスタ（受注詳細テーブル）
*													2:製品コードから発注マスタ（発注詳細テーブル）
*													3:製品コードから売上マスタ（売上詳細テーブル）
*													4:製品コードから仕入マスタ（仕入詳細テーブル）
*	@param  Object		$objDB			DBオブジェクト
*	@return Array 		$aryCode		検索対象コードが使用されているマスタ内のコードの配列
*	@access public
*/
function fncGetDeleteCodeToMaster ( $strCode, $lngMode, $objDB )
{
	// SQL文の作成
	$strQuery = "SELECT distinct on (";
	switch ( $lngMode )
	{
		case 1:		// 製品コードから受注マスタの検索時
			$strQuery .= "r.strReceiveCode) r.strReceiveCode as lngSearchNo ";
			$strQuery .= "FROM m_Receive r LEFT JOIN t_ReceiveDetail tr ON r.lngReceiveNo = tr.lngReceiveNo, m_Product p ";
			$strQuery .= "WHERE tr.strProductCode = p.strProductCode AND r.bytInvalidFlag = FALSE AND p.strProductCode = '";
			break;
		case 2:		// 製品コードから発注マスタの検索時
			$strQuery .= "o.strOrderCode) o.strOrderCode as lngSearchNo ";
			$strQuery .= "FROM m_Order o LEFT JOIN t_OrderDetail tod ON o.lngOrderNo = tod.lngOrderNo, m_Product p ";
			$strQuery .= "WHERE tod.strProductCode = p.strProductCode AND o.bytInvalidFlag = FALSE AND p.strProductCode = '";
			break;
		case 3:		// 製品コードから売上マスタの検索時
			$strQuery .= "s.strSalesCode) s.strSalesCode as lngSearchNo ";
			$strQuery .= "FROM m_Sales s LEFT JOIN t_SalesDetail ts ON s.lngSalesNo = ts.lngSalesNo, m_Product p ";
			$strQuery .= "WHERE ts.strProductCode = p.strProductCode AND s.bytInvalidFlag = FALSE AND p.strProductCode = '";
			break;
		case 4:		// 製品コードから仕入マスタの検索時
			$strQuery .= "s.strStockCode) s.strStockCode as lngSearchNo ";
			$strQuery .= "FROM m_Stock s LEFT JOIN t_StockDetail ts ON s.lngStockNo = ts.lngStockNo, m_Product p ";
			$strQuery .= "WHERE ts.strProductCode = p.strProductCode AND s.bytInvalidFlag = FALSE AND p.strProductCode = '";
			break;
	}
	$strQuery .= $strCode . "'";

	// 検索クエリーの実行
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryCode[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		$aryCode = FALSE;
	}
	$objDB->freeResult( $lngResultID );

	return $aryCode;
}






?>
