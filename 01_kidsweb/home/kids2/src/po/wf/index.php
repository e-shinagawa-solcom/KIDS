<?php

// ----------------------------------------------------------------------------
/**
*       ����  ��ǧ�롼�Ȱ�������
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
*       ��������
*         ������Ͽ����ӽ������̾�ξ�ǧ�롼�Ȱ�����ɽ��
*
*       ��������
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
<meta http-equiv="content-type" content="text/html; charset=euc-jp">

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