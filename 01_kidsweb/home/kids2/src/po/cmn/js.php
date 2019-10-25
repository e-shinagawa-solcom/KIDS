<?php
	
	function fncJScript( $aryData, $objDB )
	{
	
	//print_r($aryData );
	
		$aryJScript[] = "var arrayDtDefalt = [";
		for ($i = 0; $i < count( $aryData ); $i++ )
		{
			if( $i > 1 )
			{
				$aryJScript[] = ",";
			}
			$aryJScript[] = "[ ";
			$aryJScript[] = $aryData[$i]["strproductcode"].",";				// 製品コード
			$aryJScript[] = $aryData[$i]["lngstocksubjectcode"].", ";		// 仕入科目コード
			$aryJScript[] = $aryData[$i]["lngstockitemcode"].", ";			// 仕入部品コード
			$aryJScript[] = $aryData[$i]["lngconversionclasscode"].", ";	// 換算区分コード 
			$aryJScript[] = $aryData[$i]["curproductprice"].", ";			// 製品価格
			$aryJScript[] = $aryData[$i]["lngproductunitcode"].", ";		// 製品単位コード
//			$aryJScript[] = $aryData[$i]["curtaxprice"].", ";				// 消費税金額
			$aryJScript[] = $aryData[$i]["lngdeliverymethodcode"].", ";		// 運搬方法コード
			$aryJScript[] = $aryData[$i]["strnote"].", ";					// 備考
			$aryJScript[] = "] ";

		}
		$aryJScript[] = " ];";
		
		$strJS = implode("\n", $aryJScript);
		
		//header ("Content-Type: application/x-javascript");
		//echo "$strJS";
		
		
		//return $aryJScript
	}
?>

<script language="Javascript">
<?php echo "$strJS"; ?>
</script>