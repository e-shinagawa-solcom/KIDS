<?php

	// �ɤ߹���
	include('conf.inc');
	require (LIB_FILE);
	require(SRC_ROOT."po/cmn/lib_po.php");
	
	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	
	$aryData["strSessionID"]	= $_GET["strSessionID"];
	$aryData["lngLanguageCode"]	= $_COOKIE["lngLanguageCode"];
	
	// ���å�����ǧ
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

<!-- �׾��� -->
	<input type="hidden" name="dtmOrderAppDate" value="">
<!-- ȯ��Σϡ�  -->
	<input type="hidden" name="strOrderCode" value="">
<!-- ������  -->
	<input type="hidden" name="lngCustomerCode" value="1101">
<!-- ���祳���� -->
	<input type="hidden" name="lngInChargeGroupCode" value="01">
<!-- ô���� -->
	<input type="hidden" name="lngInChargeUserCode" value="105">
<!-- Ǽ�ʾ�� -->
	<input type="hidden" name="lngLocationCode" value="0002">
<!-- ȯ��ͭ�������� -->
	<input type="hidden" name="dtmExpirationDate" value="2002/12/31">
	
<!-- ���� -->
	<input type="hidden" name="lngOrderStatusCode" value="">
<!-- �̲� -->
	<input type="hidden" name="lngMonetaryUnitCode" value="1">
<!-- �졼�ȥ����� -->
	<input type="hidden" name="lngMonetaryRateCode" value="0">
<!-- �����졼�� -->
	<input type="hidden" name="curConversionRate" value="">
<!-- ��ʧ��� -->
	<input type="hidden" name="lngPayConditionCode" value="0">


<!-- ��ǧ�롼�� -->
	<input type="hidden" name="lngWorkflowOrderCode" value="3">
<!-- ���� -->
	<input type="hidden" name="strNote" value="���ͥإå�">
<!-- ��׶�� -->
	<input type="hidden" name="curAllTotalPrice" value="150000">
	
	

<!-- ���� -->
	<input type="hidden" name="aryPoDitail[0][strProductCode]" value="0725">
<!-- �������� -->
	<input type="hidden" name="aryPoDitail[0][strStockSubjectCode]" value="402">
<!-- �������� -->
	<input type="hidden" name="aryPoDitail[0][strStockItemCode]" value="">
<!-- ����ñ�̷׾� -->
	<input type="hidden" name="aryPoDitail[0][lngConversionClassCode]" value="1">
<!-- ñ�� -->
	<input type="hidden" name="aryPoDitail[0][curProductPrice]" value="1000">
<!-- ñ�� -->
	<input type="hidden" name="aryPoDitail[0][lngProductUnitCode]" value="1">
<!-- ���� -->
	<input type="hidden" name="aryPoDitail[0][lngGoodsQuantity]" value="12">
<!-- ��ȴ��� -->
	<input type="hidden" name="aryPoDitail[0][curTotalPrice]" value="12000">
<!-- ������ˡ -->
	<input type="hidden" name="aryPoDitail[0][lngCarrierCode]" value="1">
<!-- ���� -->
	<input type="hidden" name="aryPoDitail[0][strDetailNote]" value="�������ٹ�:���������ʤʤ�">





	


	
	
<input type="hidden" value="test" name="test">
<input type="hidden" name="strSessionID" value="<?php echo $aryData["strSessionID"]; ?>">


	
<input type="submit">
</form>
</body>
</html>



