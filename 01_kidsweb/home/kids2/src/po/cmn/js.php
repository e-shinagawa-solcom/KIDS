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
			$aryJScript[] = $aryData[$i]["strproductcode"].",";				// ���ʥ�����
			$aryJScript[] = $aryData[$i]["lngstocksubjectcode"].", ";		// �������ܥ�����
			$aryJScript[] = $aryData[$i]["lngstockitemcode"].", ";			// �������ʥ�����
			$aryJScript[] = $aryData[$i]["lngconversionclasscode"].", ";	// ������ʬ������ 
			$aryJScript[] = $aryData[$i]["curproductprice"].", ";			// ���ʲ���
			$aryJScript[] = $aryData[$i]["lngproductunitcode"].", ";		// ����ñ�̥�����
//			$aryJScript[] = $aryData[$i]["curtaxprice"].", ";				// �����Ƕ��
			$aryJScript[] = $aryData[$i]["lngdeliverymethodcode"].", ";		// ������ˡ������
			$aryJScript[] = $aryData[$i]["strnote"].", ";					// ����
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