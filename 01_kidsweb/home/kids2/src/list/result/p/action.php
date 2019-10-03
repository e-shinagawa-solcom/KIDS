<?
/** 
*	帳票出力 商品企画書 印刷完了画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// 印刷プレビュー画面( * は指定帳票のファイル名 )
// listoutput.php -> strSessionID       -> action.php
// listoutput.php -> strReportKeyCode   -> action.php
// listoutput.php -> lngReportCode      -> action.php

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "/list/cmn/lib_lo.php");
require (SRC_ROOT . "m/cmn/lib_m.php");

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

// 文字列チェック
$aryCheck["strSessionID"]       = "null:numenglish(32,32)";
$aryCheck["strReportKeyCode"]   = "null:number(0,9999)";


$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


// 指定キーコードの帳票データを取得
$strQuery = fncGetCopyFilePathQuery( DEF_REPORT_PRODUCT, $aryData["strReportKeyCode"], $aryData["lngReportCode"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum === 1 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$strListOutputPath = $objResult->strreportpathname;
	unset ( $objResult );
	$objDB->freeResult( $lngResultID );
	//echo "コピーファイル有り。";
}

// 帳票が存在しない場合、コピー帳票ファイルを生成、保存
elseif ( $lngResultNum === 0 )
{
	// データ取得クエリ
	$strQuery = fncGetListOutputQuery( DEF_REPORT_PRODUCT, $aryData["strReportKeyCode"], $objDB );


	$objMaster = new clsMaster();
	$objMaster->setMasterTableData( $strQuery, $objDB );

	$aryParts =& $objMaster->aryData[0];

	/////////////////////////////////////////////////////////////////
	// 特殊処理
	/////////////////////////////////////////////////////////////////
	// 顧客担当者コードがなかった場合、顧客担当者名を表示する処理
	if ( !$aryParts["lngcustomerusercode"] )
	{
		$aryParts["strcustomeruserdisplayname"] =& $aryParts["strcustomerusername"];
	}
	// 内箱(袋)入数が存在する場合、末尾に"pcs"をつける
	if ( $aryParts["lngboxquantity"] > 0 )
{
		$aryParts["lngboxquantity"] .= "pcs";
	}
	else
	{
		unset ( $aryParts["lngboxquantity"] );
	}
	// カートン入数が存在する場合、末尾に"pcs"をつける
	if ( $aryParts["lngcartonquantity"] > 0 )
	{
		$aryParts["lngcartonquantity"] .= "pcs";
	}
	else
	{
		unset ( $aryParts["lngcartonquantity"] );
	}
	// 商品構成が存在する場合、「全○種アッセンブリ」と表示
	if ( $aryParts["strproductcomposition"] > 0 )
	{
		$aryParts["strproductcomposition"] = "全" . $aryParts["strproductcomposition"] . "種アッセンブリ";
	}
	else
	{
		unset ( $aryParts["strproductcomposition"] );
	}





	//-------------------------------------------------------------------------
	// ■ 署名イメージ設定
	//-------------------------------------------------------------------------
	$strFullPath        = SRC_ROOT . "img/signature";
	$bytCheck           = false;

	$strImagePath       = '/img/signature/'; // イメージディレクトリパス
	$strDefaultImage    = 'default.gif';    // デフォルトイメージ
	$strCreateUserImage = '';               // 作成者イメージ
	$strAssentUserImage = '';               // 承認者イメージ


	// 作成者のユーザーコードを取得
	$aryQuery   = array();
	$aryQuery[] = "SELECT";
	$aryQuery[] = " mp.lnginputusercode as lngusercode";
	$aryQuery[] = "FROM";
	$aryQuery[] = " m_product mp";
	$aryQuery[] = "WHERE";
	$aryQuery[] = " mp.strproductcode = '" . $aryData["strReportKeyCode"] . "'";

	$strQuery = "";
	$strQuery = implode( "\n", $aryQuery );

	list( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strQuery, $objDB );

	// ユーザーコードを取得
	if( $lngCheckResultNum == 1 )
	{
		$bytCheck    = false;
		$objResult   = $objDB->fetchObject( $lngCheckResultID, 0 );
		$lngusercode = $objResult->lngusercode;

		// 署名ファイルの存在有無確認
		$bytCheck = fncSignatureCheckFile( $strFullPath, $lngusercode );

		if( $bytCheck )
		{
			$strCreateUserImage = $strImagePath . $lngusercode . ".gif";
		}
		else
		{
			$strCreateUserImage = $strImagePath . $strDefaultImage;
		}
	}
	// ユーザーが存在しない場合
	else
	{
		$strCreateUserImage = $strImagePath . $strDefaultImage;
	}

	// 承認者のユーザーコードを取得
	$lngusercode = $aryParts["lnginchargeusercode"];

	// ユーザーコードを取得
	if(!$lngusercode)
	{
		$bytCheck    = false;

		// 署名ファイルの存在有無確認
		$bytCheck = fncSignatureCheckFile( $strFullPath, $lngusercode );

		if( $bytCheck )
		{
			$strAssentUserImage = $strImagePath . $lngusercode . ".gif";
		}
		else
		{
			$strAssentUserImage = $strImagePath . $strDefaultImage;
		}
	}
	// ユーザーが存在しない場合
	else
	{
		$strAssentUserImage = $strImagePath . $strDefaultImage;
	}


	// 作成者(入力者)署名イメージ設定
	$aryParts["sigCreateImage"] = $strCreateUserImage;

	// 承認者署名イメージ設定
	$aryParts["sigAssentImage"] = $strAssentUserImage;


	// フォーマットコード設定
	$aryParts["strProductFormatCode"] = DEF_P_FORMAT_CODE;
	//-------------------------------------------------------------------------





	// HTML出力
	//echo getArrayTable( $aryDetail[1], "TABLE" );exit;

	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "list/result/p.tmpl" );

	// 置き換え
	$objTemplate->replace( $aryParts );

	$objTemplate->complete();
	$strHtml .= $objTemplate->strTemplate;

	$objDB->transactionBegin();

	// シーケンス発行
	$lngSequence = fncGetSequence( "t_Report.lngReportCode", $objDB );

	// 帳票テーブルにINSERT
	$strQuery = "INSERT INTO t_Report VALUES ( $lngSequence, " . DEF_REPORT_PRODUCT . ", " . $aryParts["lnggoodsplancode"] . ", '', '$lngSequence' )";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// 帳票ファイルオープン
	if ( !$fp = fopen ( SRC_ROOT . "list/result/cash/" . $lngSequence . ".tmpl", "w" ) )
	{
		list ( $lngResultID, $lngResultNum ) = fncQuery( "ROLLBACK", $objDB );
		fncOutputError ( 9059, DEF_FATAL, "帳票ファイルのオープンに失敗しました。", TRUE, "", $objDB );
	}

	// 帳票ファイルへの書き込み
	if ( !fwrite ( $fp, $strHtml ) )
	{
		list ( $lngResultID, $lngResultNum ) = fncQuery( "ROLLBACK", $objDB );
		fncOutputError ( 9059, DEF_FATAL, "帳票ファイルの書き込みに失敗しました。", TRUE, "", $objDB );
	}


	$objDB->transactionCommit();
	//echo "コピーファイル作成";
}
//echo "<script language=javascript>window.form1.submit();window.returnValue=true;window.close();</script>";
echo "<script language=javascript>parent.window.close();</script>";


$objDB->close();



return TRUE;
?>
