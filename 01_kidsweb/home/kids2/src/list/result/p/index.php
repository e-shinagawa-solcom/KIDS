<?
	/** 
	*	帳票出力 商品企画書 検索結果画面
	*
	*	@package   KIDS
	*	@license   http://www.wiseknot.co.jp/ 
	*	@copyright Copyright &copy; 2003, Wiseknot 
	*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
	*	@access    public
	*	@version   1.00
	*
	*	更新履歴
	*	2004.05.21	商品化企画書検索結果一覧にて製品コードとして表示していた内容が製品番号であったバグの修正
	*
	*/
	// 検索結果画面( * は指定帳票のファイル名 )
	// *.php -> strSessionID       -> index.php

	// 印刷画面へ
	// index.php -> strSessionID       -> frameset.php
	// index.php -> lngReportClassCode -> frameset.php
	// index.php -> strReportKeyCode   -> frameset.php
	// index.php -> lngReportCode      -> frameset.php

	// 設定読み込み
	include_once('conf.inc');

	// ライブラリ読み込み
	require (LIB_FILE);
	require (SRC_ROOT . "list/cmn/lib_lo.php");
	require (LIB_DEBUGFILE);

	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	//////////////////////////////////////////////////////////////////////////
	// POST(一部GET)データ取得
	//////////////////////////////////////////////////////////////////////////
	if ( $_POST )
	{
		$aryData = $_POST;
	}
	elseif ( $_GET )
	{
		$aryData = $_GET;
	}

	// 検索条件項目取得
	if ( $lngArrayLength = count ( $aryData["SearchColumn"] ) )
	{
		$aryColumn = $aryData["SearchColumn"];
		for ( $i = 0; $i < $lngArrayLength; $i++ )
		{
			$aryData[$aryColumn[$i]] = 1;
		}
		unset ( $aryData["SearchColumn"] );
		unset ( $aryColumn );
	}

	//echo getArrayTable( $aryData, "TABLE" );
	//exit;

	// 文字列チェック
	$aryCheck["strSessionID"]       = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// 権限確認
	if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}


	// 商品企画書出力
	// コピーファイル取得クエリ生成
	$strCopyQuery = "SELECT gp.lngProductNo AS strReportKeyCode, r.lngReportCode FROM t_GoodsPlan gp, t_Report r WHERE r.lngReportClassCode = " . DEF_REPORT_PRODUCT . " AND lngRevisionNo = ( SELECT MAX ( gp2.lngRevisionNo ) FROM t_GoodsPlan gp2 WHERE gp.lngProductNo = gp2.lngProductNo ) AND to_number ( r.strReportKeyCode, '9999999') = gp.lngGoodsPlanCode";

	// 商品企画書取得クエリ生成
	// 2004.05.21 suzukaze update start
	$aryQuery[] = "SELECT p.strProductCode, p.strProductName, p.lngproductstatuscode,";
	// 2004.05.21 suzukaze update start

	$aryQuery[] = " u1.strUserDisplayCode AS strInputUserDisplayCode,";
	$aryQuery[] = " u1.strUserDisplayName AS strInputUserDisplayName,";
	$aryQuery[] = " g.strGroupDisplayCode AS strInChargeGroupDisplayCode,";
	$aryQuery[] = " g.strGroupDisplayName AS strInChargeGroupDisplayName,";
	$aryQuery[] = " u2.strUserDisplayCode AS strInChargeUserDisplayCode,";
	$aryQuery[] = " u2.strUserDisplayName AS strInChargeUserDisplayName,";
	$aryQuery[] = " gp.lngProductNo AS strReportKeyCode ";

	// 2004.05.21 suzukaze update start
	$aryQuery[] = "FROM m_Product p";
	$aryQuery[] = " LEFT JOIN m_User u1 ON p.lngInputUserCode = u1.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_User u2 ON p.lngInChargeUserCode = u2.lngUserCode";
	$aryQuery[] = " LEFT JOIN m_Group g ON p.lngInChargeGroupCode = g.lngGroupCode";
	$aryQuery[] = ", t_GoodsPlan gp ";
	// 2004.05.21 suzukaze update end

	$aryQuery[] = "WHERE gp.lngRevisionNo =";
	$aryQuery[] = "(";
	$aryQuery[] = "  SELECT MAX ( gp2.lngRevisionNo )";
	$aryQuery[] = "  FROM t_GoodsPlan gp2";
	$aryQuery[] = "  WHERE gp.lngProductNo = gp2.lngProductNo";
	$aryQuery[] = ")";

	/////////////////////////////////////////////////////////////////
	// 検索条件
	/////////////////////////////////////////////////////////////////
	// 作成日時
	if ( $aryData["dtmInsertDateConditions"] )
	{
		if ( $aryData["dtmInsertDateFrom"] )
		{
			$aryQuery[] = " AND date_trunc('day', p.dtmInsertDate ) >= '" . $aryData["dtmInsertDateFrom"] . "'";
		}
		if ( $aryData["dtmInsertDateTo"] )
		{
			$aryQuery[] = " AND date_trunc('day', p.dtmInsertDate ) <= '" . $aryData["dtmInsertDateTo"] . "'";
		}
	}
	// 企画進行状況
	if ( $aryData["lngGoodsPlanProgressCodeConditions"] && $aryData["lngGoodsPlanProgressCode"] )
	{
		$aryQuery[] = " AND gp.lngGoodsPlanProgressCode = " . $aryData["lngGoodsPlanProgressCode"];
	}
	// 改訂日時
	if ( $aryData["dtmRevisionDateConditions"] )
	{
		if ( $aryData["dtmRevisionDateFrom"] )
		{
			$aryQuery[] = " AND date_trunc('day', p.dtmUpdateDate ) >= '" . $aryData["dtmRevisionDateFrom"] . "'";
		}
		if ( $aryData["dtmRevisionDateTo"] )
		{
			$aryQuery[] = " AND date_trunc('day', p.dtmUpdateDate ) <= '" . $aryData["dtmRevisionDateTo"] . "'";
		}
	}
	// 製品コード
	if ( $aryData["strProductCodeConditions"] )
	{
		if ( $aryData["strProductCodeFrom"] )
		{
			$aryQuery[] = " AND p.strProductCode >= '" . $aryData["strProductCodeFrom"] . "'";
		}
		if ( $aryData["strProductCodeTo"] )
		{
			$aryQuery[] = " AND p.strProductCode <= '" . $aryData["strProductCodeTo"] . "'";
		}
	}
	// 製品名
	if ( $aryData["strProductNameConditions"] && $aryData["strProductName"] )
	{
		$aryQuery[] = " AND p.strProductName LIKE '%" . $aryData["strProductName"] . "%'";
	}
	// 製品名(英語)
	if ( $aryData["strProductEnglishNameConditions"] && $aryData["strProductEnglishName"] )
	{
		$aryQuery[] = " AND p.strProductEnglishName LIKE '%" . $aryData["strProductEnglishName"] . "%'";
	}
	// 入力者コード
	if ( $aryData["lngInputUserCodeConditions"] && $aryData["strInputUserDisplayCode"] )
	{
		$aryQuery[] = " AND u1.strUserDisplayCode = '" . $aryData["strInputUserDisplayCode"] . "'";
	}
	// 部門コード
	if ( $aryData["lngInChargeGroupCodeConditions"] && $aryData["strInChargeGroupDisplayCode"] )
	{
		$aryQuery[] = " AND g.strGroupDisplayCode = '" . $aryData["strInChargeGroupDisplayCode"] . "'";
	}
	// 担当者コード
	if ( $aryData["lngInChargeUserCodeConditions"] && $aryData["strInChargeUserDisplayCode"] )
	{
		$aryQuery[] = " AND u2.strUserDisplayCode = '" . $aryData["strInChargeUserDisplayCode"] . "'";
	}

	$aryQuery[] = " AND p.lngProductNo = gp.lngProductNo";

//Add by kou	
	$aryQuery[] = " AND p.bytinvalidflag = false";
	
//end	
	// 2004.05.21 suzukaze update start
	// $aryQuery[] = " AND p.lngInputUserCode = u1.lngUserCode";
	// $aryQuery[] = " AND p.lngInChargeUserCode = u2.lngUserCode";
	// $aryQuery[] = " AND p.lngInChargeGroupCode = g.lngGroupCode ";
	// 2004.05.21 suzukaze update start


	$aryQuery[] = "AND p.lngproductstatuscode != " . DEF_PRODUCT_APPLICATE;

	$aryQuery[] = " ORDER BY p.strProductCode ASC";

	// ナンバーをキーとする連想配列に帳票コードを取得
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strCopyQuery, $objDB );

	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );
		$aryReportCode[$objResult->strreportkeycode] = $objResult->lngreportcode;
	}

	if ( $lngResultNum > 0 )
	{
		$objDB->freeResult( $lngResultID );
	}


	// 帳票データ取得クエリ実行・テーブル生成
	$strQuery = join ( "\n", $aryQuery );
//fncDebug( 'lib_list_p.txt', $strQuery, __FILE__, __LINE__);

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );

		$aryParts["strResult"] .= "<tr class=\"Segs\">\n";

	// 2004.05.21 suzukaze update start
	//	$aryParts["strResult"] .= "<td>" . $objResult->strreportkeycode . "</td>\n";
		$aryParts["strResult"] .= "<td>" . $objResult->strproductcode . "</td>\n";
	// 2004.05.21 suzukaze update end

		$aryParts["strResult"] .= "<td>" . $objResult->strproductname . "</td>\n";
		$aryParts["strResult"] .= "<td>" . $objResult->strinputuserdisplaycode . ":" . $objResult->strinputuserdisplayname . "</td>\n";
		$aryParts["strResult"] .= "<td>" . $objResult->strinchargegroupdisplaycode . ":" . $objResult->strinchargegroupdisplayname . "</td>\n";
		$aryParts["strResult"] .= "<td>" . $objResult->strinchargeuserdisplaycode . ":" . $objResult->strinchargeuserdisplayname . "</td>\n";

		$aryParts["strResult"] .= "<td align=center>";

		// コピーファイルパスが存在している場合、コピー帳票出力ボタン表示
		if ( $aryReportCode[$objResult->strreportkeycode] != NULL )
		{
			// コピー帳票出力ボタン表示
			$aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $objResult->strreportkeycode . "&lngReportCode=" . $aryReportCode[$objResult->strreportkeycode] . "' );return false;\" onmouseover=\"fncCopyPreviewButton( 'on' , this );\" onmouseout=\"fncCopyPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/list/copybig_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"COPY PREVIEW\"></a>";
		}

		$aryParts["strResult"] .= "</td>\n<td align=center>";

		// コピーファイルパスが存在しない または コピー解除権限がある場合、
		// 帳票出力ボタン表示
		if ( $aryReportCode[$objResult->strreportkeycode] == NULL || fncCheckAuthority( DEF_FUNCTION_LO3, $objAuth ) )
		{
			// 帳票出力ボタン表示
			$aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $objResult->strreportkeycode . "&strActionList=p' );return false;\" onmouseover=\"fncPreviewButton( 'on' , this );\" onmouseout=\"fncPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/preview_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"PREVIEW\"></a>";
		}

		$aryParts["strResult"] .= "</td></tr>\n";

		unset ( $strCopyCheckboxObject );
	}


	// カラム表示
	$aryParts["strColumn"] = "
						<td id=\"Column0\" nowrap>製品コード</td>
						<td id=\"Column1\" nowrap>製品名称</td>
						<td id=\"Column2\" nowrap>入力者</td>
						<td id=\"Column3\" nowrap>部門</td>
						<td id=\"Column4\" nowrap>担当者</td>
						<td id=\"Column5\" nowrap>COPY プレビュー</td>
						<td id=\"Column6\" nowrap>プレビュー</td>
	";

	$aryParts["strListType"] = "p";
	$aryParts["HIDDEN"] = getArrayTable( $aryData, "HIDDEN" );


	$objDB->close();

	$aryParts["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// HTML出力
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "list/result/parts.tmpl" );
	$objTemplate->replace( $aryParts );
	$objTemplate->replace( $aryData );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;

?>
