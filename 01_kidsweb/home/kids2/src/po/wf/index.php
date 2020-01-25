<?php

// ----------------------------------------------------------------------------
/**
*       共通  承認ルート一覧画面
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
*         ・各登録および修正画面上の承認ルート一覧を表示
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



if ( $_POST )
{
	$aryData["strSessionID"] = $_POST["strSessionID"];
}
else
{
	$aryData["strSessionID"] = $_GET["strSessionID"];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="ja">
<head>
<title>K.I.D.S.</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

<!-- START COMMON LAYOUT FILES -->

<!-- END COMMON LAYOUT FILES -->


<!-- START COMMON FILES -->
<script type="text/javascript" language="javascript" src="/cmn/functions.js"></script>
<script type="text/javascript" language="javascript" src="/cmn/query.js"></script>
<script type="text/vbscript" Language="vbscript" src="/cmn/vbsclass.js"></script>
<script type="text/vbscript" Language="vbscript" src="/cmn/vbscript.js"></script>
<script type="text/javascript" language="javascript" src="/cmn/lngcheck.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="/cmn/layout.css">
<link rel="stylesheet" type="text/css" media="screen" href="/cmn/styles.css">
<!-- END COMMON FILES -->


<!-- START EXCLUSIVE USE LAYOUT FILES -->
<script type="text/javascript" language="javascript" src="/cmn/functions.js"></script>
<script type="text/javascript" language="javascript" src="/layout/type01/po/cmn/images.js"></script>
<script type="text/javascript" language="javascript" src="/layout/type01/po/wf/initlayout.js"></script>
<!-- END EXCLUSIVE USE LAYOUT FILES -->


<!-- START EXCLUSIVE USE FILES -->
<link rel="stylesheet" type="text/css" media="screen" href="layout.css">
<!-- END EXCLUSIVE USE FILES -->

</head>
<body id="Backs" onload="InitLayout();">


<span id="RootHeader"></span>
<span id="RootFooter"></span>

<span id="RootDataFrame">
	<iframe id="RootData" name="RootWin" frameborder="0" scrolling="yes" src="/po/wf/root.php?strSessionID=<? echo $aryData["strSessionID"]; ?>"></iframe>
</span>

</body>
</html>