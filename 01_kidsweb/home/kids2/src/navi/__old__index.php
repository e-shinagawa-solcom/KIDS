<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<!--

<?php
	// 設定読み込み
	include_once('conf.inc');

	// ライブラリ読み込み
	require (LIB_FILE);

	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	$aryData = $_GET;

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ナビゲーションディレクトリとボタン状態情報を取得
	// 例) p1311(「商品管理」にて2番目「商品検索」を閲覧中の場合)
	// 例) m31  (「マスタ管理」にて1番目「マスタ一覧」を閲覧中の場合)
	list ( $strDirName, $strNaviCode ) = split ( "-", $aryData["strNaviCode"] );

	$length = strlen ( $strNaviCode );
	for ( $i = 0; $i < $length; $i++ )
	{
		$aryNaviCode[$i] = substr ( $strNaviCode, $i, 1 );
		if ( $aryNaviCode[$i] == 3 )
		{
			$aryDisabled[$i] = "disabled";
		}
	}

?>

-->

<html lang="ja">
<head>
<title>K.I.D.S.</title>
<meta http-equiv="content-type" content="text/html; charset=euc-jp">



<!-- START COMMON FILES -->
<script type="text/javascript" language="javascript" src="/cmn/functions.js"></script>
<script type="text/javascript" language="javascript" src="/cmn/query.js"></script>
<!-- END COMMON FILES -->


<!-- START EXCLUSIVE USE LAYOUT FILES -->
<script type="text/javascript" language="javascript" src="/layout/<? echo LAYOUT_CODE; ?>/navi/<? echo $strDirName; ?>/images.js"></script>
<script type="text/javascript" language="javascript" src="/layout/<? echo LAYOUT_CODE; ?>/navi/cmn/images.js"></script>
<script type="text/javascript" language="javascript" src="/layout/<? echo LAYOUT_CODE; ?>/navi/<? echo $strDirName; ?>/initlayout.js"></script>
<script type="text/javascript" language="javascript" src="/layout/<? echo LAYOUT_CODE; ?>/navi/cmn/initlayoutnavi.js"></script>
<!-- END EXCLUSIVE USE LAYOUT FILES -->


<!-- START EXCLUSIVE USE FILES -->
<script type="text/javascript" language="javascript" src="/navi/cmn/exstr.js"></script>
<!-- END EXCLUSIVE USE FILES -->

<?php
	if ( $strDirName == "uc" )
	{
?>

		<link rel="stylesheet" type="text/css" media="screen" href="/navi/uc/layout.css">

<?php
	}
	else
	{
?>

		<link rel="stylesheet" type="text/css" media="screen" href="/navi/cmn/layout.css">

<?php
	}
?>


</head>
<body onContextmenu="return true;" onload="initLayoutNavigation();">


<span id="NaviBodys"></span>


<?php
	// 見積原価管理
	if ( $strDirName == "estimate" && fncCheckAuthority( DEF_FUNCTION_E0, $objAuth ) )
	{
		if ( !fncCheckAuthority( DEF_FUNCTION_E1, $objAuth ) )
		{
			$strVisibility1 = " style=\"visibility:hidden\"";
		}
		if ( !fncCheckAuthority( DEF_FUNCTION_E2, $objAuth ) )
		{
			$strVisibility2 = " style=\"visibility:hidden\"";
		}
		if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
		{
			$strVisibility3 = " style=\"visibility:hidden\"";
		}
?>

		<span id="RegistNaviBt<? echo $aryNaviCode[0] . $strVisibility1; ?>" onClick="top.location='/estimate/regist/edit.php?strSessionID=<? echo $aryData["strSessionID"]; ?>&lngFunctionCode=1501';" <? echo $aryDisabled[0]; ?>></span>
		<span id="SearchNaviBt<? echo $aryNaviCode[1] . $strVisibility2; ?>" onClick="top.location='/estimate/search/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[1]; ?>></span>
		<span id="ListExNaviBt<? echo $aryNaviCode[2] . $strVisibility3; ?>" onClick="top.location='/list/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[2]; ?>></span>

<?php
	}

	// 商品管理
	if ( $strDirName == "p" && fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) )
	{
		if ( !fncCheckAuthority( DEF_FUNCTION_P1, $objAuth ) )
		{
			$strVisibility1 = " style=\"visibility:hidden\"";
		}
		if ( !fncCheckAuthority( DEF_FUNCTION_P2, $objAuth ) )
		{
			$strVisibility2 = " style=\"visibility:hidden\"";
		}
		if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
		{
			$strVisibility3 = " style=\"visibility:hidden\"";
		}
?>
		<span id="RegistNaviBt<? echo $aryNaviCode[0] . $strVisibility1; ?>" onClick="top.location='/p/regist/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[0]; ?>></span>
		<span id="SearchNaviBt<? echo $aryNaviCode[1] . $strVisibility2; ?>" onClick="top.location='/p/search/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[1]; ?>></span>
		<span id="ListExNaviBt<? echo $aryNaviCode[2] . $strVisibility3; ?>" onClick="top.location='/list/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[2]; ?>></span>

<?php
	}

	// 発注管理
	if ( $strDirName == "po" && fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
	{
		if ( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
		{
			$strVisibility1 = " style=\"visibility:hidden\"";
		}
		if ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
		{
			$strVisibility2 = " style=\"visibility:hidden\"";
		}
		if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
		{
			$strVisibility3 = " style=\"visibility:hidden\"";
		}
		if ( !fncCheckAuthority( DEF_FUNCTION_DE0, $objAuth ) )
		{
			$strVisibility4 = " style=\"visibility:hidden\"";
		}
?>

		<span id="RegistNaviBt<? echo $aryNaviCode[0] . $strVisibility1; ?>" onClick="top.location='/po/regist/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[0]; ?>></span>
		<span id="SearchNaviBt<? echo $aryNaviCode[1] . $strVisibility2; ?>" onClick="top.location='/po/search/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[1]; ?>></span>
		<span id="ListExNaviBt<? echo $aryNaviCode[2] . $strVisibility3; ?>" onClick="top.location='/list/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[2]; ?>></span>
		<span id="DataExNaviBt<? echo $aryNaviCode[3] . $strVisibility4; ?>" onClick="top.location='/dataex/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[3]; ?>></span>

<?php
	}

// 仕入管理
if ( $strDirName == "pc" && fncCheckAuthority( DEF_FUNCTION_PC0, $objAuth ) )
{
	if ( !fncCheckAuthority( DEF_FUNCTION_PC1, $objAuth ) )
	{
		$strVisibility1 = " style=\"visibility:hidden\"";
	}
	if ( !fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )
	{
		$strVisibility2 = " style=\"visibility:hidden\"";
	}
	if ( !fncCheckAuthority( DEF_FUNCTION_DE0, $objAuth ) )
	{
		$strVisibility4 = " style=\"visibility:hidden\"";
	}
?>
<span id="RegistNaviBt<? echo $aryNaviCode[0] . $strVisibility1; ?>" onClick="top.location='/pc/regist/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[0]; ?>></span>
<span id="SearchNaviBt<? echo $aryNaviCode[1] . $strVisibility2; ?>" onClick="top.location='/pc/search/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[1]; ?>></span>
<span id="DataExNaviBt<? echo $aryNaviCode[2] . $strVisibility4; ?>" onClick="top.location='/dataex/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[3]; ?>></span><? echo $aryDisabled[3]; ?>></span>
<?
}

// 受注管理
if ( $strDirName == "so" && fncCheckAuthority( DEF_FUNCTION_SO0, $objAuth ) )
{
	if ( !fncCheckAuthority( DEF_FUNCTION_SO1, $objAuth ) )
	{
		$strVisibility1 = " style=\"visibility:hidden\"";
	}
	if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
	{
		$strVisibility2 = " style=\"visibility:hidden\"";
	}
?>
<span id="RegistNaviBt<? echo $aryNaviCode[0] . $strVisibility1; ?>" onClick="top.location='/so/regist/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[0]; ?>></span>
<span id="SearchNaviBt<? echo $aryNaviCode[1] . $strVisibility2; ?>" onClick="top.location='/so/search/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[1]; ?>></span>
<?
}

// 売上管理
if ( $strDirName == "sc" && fncCheckAuthority( DEF_FUNCTION_SC0, $objAuth ) )
{
	if ( !fncCheckAuthority( DEF_FUNCTION_SC1, $objAuth ) )
	{
		$strVisibility1 = " style=\"visibility:hidden\"";
	}
	if ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
	{
		$strVisibility2 = " style=\"visibility:hidden\"";
	}
	if ( !fncCheckAuthority( DEF_FUNCTION_DE0, $objAuth ) )
	{
		$strVisibility4 = " style=\"visibility:hidden\"";
	}
?>
<span id="RegistNaviBt<? echo $aryNaviCode[0] . $strVisibility1; ?>" onClick="top.location='/sc/regist/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[0]; ?>></span>
<span id="SearchNaviBt<? echo $aryNaviCode[1] . $strVisibility2; ?>" onClick="top.location='/sc/search/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[1]; ?>></span>
<span id="DataExNaviBt<? echo $aryNaviCode[2] . $strVisibility4; ?>" onClick="top.location='/dataex/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[3]; ?>></span><? echo $aryDisabled[3]; ?>></span>
<?
}

// ワークフロー管理
elseif ( $strDirName == "wf" && fncCheckAuthority( DEF_FUNCTION_WF1, $objAuth ) )
{
	if ( !fncCheckAuthority( DEF_FUNCTION_WF2, $objAuth ) )
	{
		$strVisibility2 = " style=\"visibility:hidden\"";
	}
?>
<span id="RegistNaviBt<? echo $aryNaviCode[0]; ?>" onClick="top.location='/wf/list/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[0]; ?>></span>
<span id="SearchNaviBt<? echo $aryNaviCode[1] . $strVisibility2; ?>" onClick="top.location='/wf/search/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[1]; ?>></span>
<?
}

// ユーザー管理
elseif ( $strDirName == "uc" && fncCheckAuthority( DEF_FUNCTION_UC1, $objAuth ) )
{
	if ( !fncCheckAuthority( DEF_FUNCTION_UC2, $objAuth ) )
	{
		$strVisibility2 = " style=\"visibility:hidden\"";
	}
	if ( !fncCheckAuthority( DEF_FUNCTION_UC3, $objAuth ) )
	{
		$strVisibility3 = " style=\"visibility:hidden\"";
	}
?>
<span id="UserInfoNaviBt<? echo $aryNaviCode[0]; ?>" onClick="top.location='/uc/regist/edit.php?strSessionID=<? echo $aryData["strSessionID"]; ?>&lngFunctionCode=<? echo DEF_FUNCTION_UC1; ?>';" <? echo $aryDisabled[0]; ?>></span>
<span id="RegistNaviBt<? echo $aryNaviCode[1] . $strVisibility2; ?>" onClick="top.location='/uc/regist/edit.php?strSessionID=<? echo $aryData["strSessionID"]; ?>&lngFunctionCode=<? echo DEF_FUNCTION_UC2; ?>';" <? echo $aryDisabled[1]; ?>></span>
<span id="SearchNaviBt<? echo $aryNaviCode[2] . $strVisibility3; ?>" onClick="top.location='/uc/search/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[2]; ?>></span>
<?
}

// マスタ管理
elseif ( $strDirName == "m" && fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
?>
<span id="RegistNaviBt<? echo $aryNaviCode[0]; ?>" onClick="top.location='/m/list/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[0]; ?>></span>
<span id="SearchNaviBt<? echo $aryNaviCode[1]; ?>" onClick="top.location='/m/search/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[1]; ?>></span>
<?
}

// 帳票出力
elseif ( $strDirName == "list" && fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
{
?>
<span id="RegistNaviBt<? echo $aryNaviCode[0]; ?>" onClick="top.location='/list/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[0]; ?>></span>
<?
}

// データエクスポート
elseif ( $strDirName == "dataex" && fncCheckAuthority( DEF_FUNCTION_DE0, $objAuth ) )
{
?>
<span id="RegistNaviBt<? echo $aryNaviCode[0]; ?>" onClick="top.location='/dataex/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[0]; ?>></span>
<?
}

// システム管理
elseif ( $strDirName == "sysc" && fncCheckAuthority( DEF_FUNCTION_SYS0, $objAuth ) )
{
	if ( !fncCheckAuthority( DEF_FUNCTION_SYS1, $objAuth ) )
	{
		$strVisibility1 = " style=\"visibility:hidden\"";
	}
	if ( !fncCheckAuthority( DEF_FUNCTION_SYS2, $objAuth ) )
	{
		$strVisibility2 = " style=\"visibility:hidden\"";
	}
	if ( !fncCheckAuthority( DEF_FUNCTION_SYS3, $objAuth ) )
	{
		$strVisibility3 = " style=\"visibility:hidden\"";
	}
	if ( !fncCheckAuthority( DEF_FUNCTION_SYS4, $objAuth ) )
	{
		$strVisibility4 = " style=\"visibility:hidden\"";
	}
?>
<span id="MessageBt<? echo $aryNaviCode[0]; ?>" onClick="top.location='/sysc/inf/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[0]; ?> <? echo $strVisibility1; ?>></span>
<span id="EmailBt<? echo $aryNaviCode[1]; ?>" onClick="top.location='/sysc/mail/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[1]; ?> <? echo $strVisibility2; ?>></span>
<span id="SessionBt<? echo $aryNaviCode[2]; ?>" onClick="top.location='/sysc/session/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[2]; ?> <? echo $strVisibility3; ?>></span>
<span id="ServerBt<? echo $aryNaviCode[3]; ?>" onClick="top.location='/sysc/sev/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[3]; ?> <? echo $strVisibility4; ?>></span>
<?
}

// 締め処理
elseif ( $strDirName == "closed" && fncCheckAuthority( DEF_FUNCTION_CLD0, $objAuth ) )
{
?>
<span id="ClosedBt<? echo $aryNaviCode[0]; ?>" onClick="top.location='/closed/index.php?strSessionID=<? echo $aryData["strSessionID"]; ?>';" <? echo $aryDisabled[0]; ?>></span>
<?
}

?>


</body>
</html>