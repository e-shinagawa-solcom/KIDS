<?php

	// 読み込み
	include('conf.inc');
	require (LIB_FILE);
	require(SRC_ROOT."po/cmn/lib_po.php");
	
	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	
	$aryData["strSessionID"]	= $_GET["strSessionID"];
	$aryData["lngLanguageCode"]	= 1;
	
	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode = $objAuth->UserCode;
	
	
	$nextyear  = mktime (0,0,0,date("m"),  date("d"),  date("Y")+1);
	echo "nextyear : $nextyear<br>";
	
	$nextDate = date("Y/m/d", $nextyear);
	echo "date : $nextDate<br>";
	
?>
<html>
<body>
<form name="form1" method="POST" action="index.php">

<!-- 計上日 -->
	<input type="hidden" name="dtmOrderAppDate" value="">
<!-- 発注ＮＯ．  -->
	<input type="hidden" name="strOrderCode" value="">
<!-- 仕入先  -->
	<input type="hidden" name="lngCustomerCode" value="1101">
<!-- 部門コード -->
	<input type="hidden" name="lngInChargeGroupCode" value="01">
<!-- 担当者 -->
	<input type="hidden" name="lngInChargeUserCode" value="105">
<!-- 納品場所 -->
	<input type="hidden" name="lngLocationCode" value="0002">
<!-- 発注有効期限日 -->
	<input type="hidden" name="dtmExpirationDate" value="2002/12/31">
	
<!-- 状態 -->
	<input type="hidden" name="lngOrderStatusCode" value="">
<!-- 通貨 -->
	<input type="hidden" name="lngMonetaryUnitCode" value="1">
<!-- レートタイプ -->
	<input type="hidden" name="lngMonetaryRateCode" value="0">
<!-- 換算レート -->
	<input type="hidden" name="curConversionRate" value="">
<!-- 支払条件 -->
	<input type="hidden" name="lngPayConditionCode" value="0">


<!-- 承認ルート -->
	<input type="hidden" name="lngWorkflowOrderCode" value="3">
<!-- 備考 -->
	<input type="hidden" name="strNote" value="備考ヘッダ">
<!-- 合計金額 -->
	<input type="hidden" name="curAllTotalPrice" value="150000">
	
	

<!-- 製品 -->
	<input type="hidden" name="aryPoDitail[0][strProductCode]" value="0725">
<!-- 仕入科目 -->
	<input type="hidden" name="aryPoDitail[0][strStockSubjectCode]" value="402">
<!-- 仕入部品 -->
	<input type="hidden" name="aryPoDitail[0][strStockItemCode]" value="">
<!-- 製品単位計上 -->
	<input type="hidden" name="aryPoDitail[0][lngConversionClassCode]" value="1">
<!-- 単価 -->
	<input type="hidden" name="aryPoDitail[0][curProductPrice]" value="1000">
<!-- 単位 -->
	<input type="hidden" name="aryPoDitail[0][lngProductUnitCode]" value="1">
<!-- 数量 -->
	<input type="hidden" name="aryPoDitail[0][lngGoodsQuantity]" value="12">
<!-- 税抜金額 -->
	<input type="hidden" name="aryPoDitail[0][curTotalPrice]" value="12000">
<!-- 運搬方法 -->
	<input type="hidden" name="aryPoDitail[0][lngCarrierCode]" value="1">
<!-- 備考 -->
	<input type="hidden" name="aryPoDitail[0][strDetailNote]" value="備考明細行:仕入れ部品なし">





	


	
	
<input type="hidden" value="test" name="test">
<input type="hidden" name="strSessionID" value="<?php echo $aryData["strSessionID"]; ?>">


	
<input type="submit">
</form>
</body>
</html>



