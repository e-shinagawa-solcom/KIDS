<?
// 
// ���ס��ǡ����١������ͤ��˳ƻ������Ǥ��������롣SELECT���֥������Ȥ�������
// ������
//		$objDB		�ǡ����١������֥�������
//		$strMark	_%...%_ ���֤���������
//		$strSelectedKey	���������֤Ȥ���Value��
// 
function fncGetElements($objDB, $strMark, $strSelectedKey="")
{
	// <select name="????"> ����ꤹ��
	$strSelectName = "";
	
	switch($strMark)
	{
		case "_%M_GROUP%_":
			$strSelectName = "lngGroupCode";
			break;
		case "_%M_USER%_":
			$strSelectName = "lngUserCode";
			break;
		case "_%DATE_FROM%_":
			$strSelectName = "date_from";
			break;
		case "_%DATE_TO%_":
			$strSelectName = "date_to";
			break;
		case "_%CAL_DATE_FROM%_":
			$strSelectName = "cal_date_from";
			break;
		case "_%CAL_DATE_TO%_":
			$strSelectName = "cal_date_to";
			break;
		case "_%M_ORDERSTATUS%_":
			$strSelectName = "lngOrderStatusCode";
			break;

	}



	// 
	switch($strSelectName)
	{
		case "lngGroupCode":
			unset($aryQuery);
			$aryQuery[] = "SELECT * FROM m_group";
			$aryQuery[] = "WHERE bytGroupDisplayFlag = true";
			$aryQuery[] = "AND lngCompanyCode = 1";
			$aryQuery[] = "ORDER BY strGroupDisplayCode";
			
			unset($aryQyeryData);
			if( fncGetQyeryData($objDB, implode("\n",$aryQuery), $aryQyeryData) != true )
			{
				echo "Qyery Error!";
			}

			unset($arySelect);
			$arySelect[] = "\n";
			$arySelect[] = '<select name="'.$strSelectName.'">';
			$arySelect[] = '<option value="0">����...</option>';
			for( $iCnt = 0; $iCnt < count($aryQyeryData); $iCnt++ )
			{
				$strBuff = '<option value="' . $aryQyeryData[$iCnt][strtolower("lngGroupCode")] . '"_%selected%_>' . $aryQyeryData[$iCnt][strtolower("strGroupDisplayCode")] . " " . $aryQyeryData[$iCnt][strtolower("strGroupDisplayName")] . '</option>';
				if( $strSelectedKey == $aryQyeryData[$iCnt][strtolower("lngGroupCode")] )
				{
					$strBuff = preg_replace( "/_%selected%_/", "selected", $strBuff);
				}
				$arySelect[] = $strBuff;
			}
			$arySelect[] = '</select>';

			//$strValue = preg_replace( "/_%M_GROUP%_/", implode("\n", $arySelect), $strValue );
			$strValue = implode("\n", $arySelect);
			break;
		
		case "lngUserCode":

			unset($aryQuery);
			$aryQuery[] = "SELECT * FROM m_user";
			$aryQuery[] = "WHERE bytUserDisplayFlag = true";
			$aryQuery[] = "AND lngCompanyCode = 1";
			$aryQuery[] = "ORDER BY strUserDisplayCode";

			unset($aryQyeryData);
			if( fncGetQyeryData($objDB, implode("\n",$aryQuery), $aryQyeryData) != true )
			{
				echo "Qyery Error!";
			}

			unset($arySelect);
			$arySelect[] = "\n";
			$arySelect[] = '<select name="'.$strSelectName.'">';
			$arySelect[] = '<option value="0">����...�����ơ�</option>';
			for( $iCnt = 0; $iCnt < count($aryQyeryData); $iCnt++ )
			{
				$strBuff = '<option value="' . $aryQyeryData[$iCnt][strtolower("lngUserCode")] . '"_%selected%_>' . $aryQyeryData[$iCnt][strtolower("strUserDisplayCode")] . " " . $aryQyeryData[$iCnt][strtolower("strUserDisplayName")] . '</option>';
				if( $strSelectedKey == $aryQyeryData[$iCnt][strtolower("lngUserCode")] )
				{
					$strBuff = preg_replace( "/_%selected%_/", "selected", $strBuff);
				}
				$arySelect[] = $strBuff;
			}
			$arySelect[] = '</select>';

			//$strValue = preg_replace( "/_%M_USER%_/", implode("\n", $arySelect), $strValue );
			$strValue = implode("\n", $arySelect);
			
			break;
		
		case "date_from":
		case "cal_date_from":
			
			unset($arySelect);
			$arySelect[] = "\n";
			$arySelect[] = '<select name="'.$strSelectName.'">';
			$arySelect[] = '<option value="">����...</option>';
			for( $iCntY = 2004; $iCntY < 2010; $iCntY++ )
			{
				for( $iCntM = 1; $iCntM <= 12; $iCntM++ )
				{
					$strDateValue = date("Y-m-d",mktime(0,0,0,$iCntM,1,$iCntY));
					$strBuff = '<option value="' . $strDateValue . '"_%selected%_>' . date("Yǯm��",mktime(0,0,0,$iCntM,1,$iCntY)) . '</option>';
					if( $strSelectedKey == "" )
					{
						// �����������֤ˤ���
						if( substr($strDateValue,0,7) == date("Y-m") )
						{
							$strBuff = preg_replace( "/_%selected%_/", "selected", $strBuff);
						}
					}
					else
					{
						// ����Υǡ�����������֤ˤ���
						if( $strSelectedKey == $strDateValue )
						{
							$strBuff = preg_replace( "/_%selected%_/", "selected", $strBuff);
						}
					}
					$arySelect[] = $strBuff;
				}
			}
			$arySelect[] = '</select>';
			
			//$strValue = preg_replace( "/_%DATE_FROM%_/",  implode("\n", $arySelect), $strValue );
			$strValue = implode("\n", $arySelect);
			
			break;
			
		case "date_to":
		case "cal_date_to":

			unset($arySelect);
			$arySelect[] = "\n";
			$arySelect[] = '<select name="'.$strSelectName.'">';
			$arySelect[] = '<option value="">����...</option>';
			for( $iCntY = 2004; $iCntY < 2010; $iCntY++ )
			{
				for( $iCntM = 1; $iCntM <= 12; $iCntM++ )
				{
					$strDateValue = date("Y-m-d",mktime(0,0,0,$iCntM+1,0,$iCntY)); // �����0day�򸫤뤳�Ȥǡ��ǽ��������
					$strBuff = '<option value="' . $strDateValue . '"_%selected%_>' . date("Yǯm��",mktime(0,0,0,$iCntM+1,0,$iCntY)) . '</option>';
					if( $strSelectedKey == "" )
					{
						// �����������֤ˤ���
						if( substr($strDateValue,0,7) == date("Y-m") )
						{
							$strBuff = preg_replace( "/_%selected%_/", "selected", $strBuff);
						}
					}
					else
					{
						// ����Υǡ�����������֤ˤ���
						if( $strSelectedKey == $strDateValue )
						{
							$strBuff = preg_replace( "/_%selected%_/", "selected", $strBuff);
						}
					}
					$arySelect[] = $strBuff;
				}
			}
			$arySelect[] = '</select>';
			
			//$strValue = preg_replace( "/_%DATE_TO%_/",  implode("\n", $arySelect), $strValue );
			$strValue = implode("\n", $arySelect);
			
			break;

		case "lngOrderStatusCode":

			unset($aryQuery);
			$aryQuery[] = "SELECT * FROM m_orderstatus";

			unset($aryQyeryData);
			if( fncGetQyeryData($objDB, implode("\n",$aryQuery), $aryQyeryData) != true )
			{
				echo "Qyery Error!";
			}

			unset($arySelect);
			$arySelect[] = "\n";
			$arySelect[] = '<select name="'.$strSelectName.'">';
			$arySelect[] = '<option value="0">����...�����ơ�</option>';
			for( $iCnt = 0; $iCnt < count($aryQyeryData); $iCnt++ )
			{
				$strBuff = '<option value="' . $aryQyeryData[$iCnt][strtolower("lngOrderStatusCode")] . '"_%selected%_>' . $aryQyeryData[$iCnt][strtolower("lngOrderStatusCode")] . " " . $aryQyeryData[$iCnt][strtolower("strOrderStatusName")] . '</option>';
				if( $strSelectedKey == $aryQyeryData[$iCnt][strtolower("lngOrderStatusCode")] )
				{
					$strBuff = preg_replace( "/_%selected%_/", "selected", $strBuff);
				}
				$arySelect[] = $strBuff;
			}
			$arySelect[] = '</select>';

			$strValue = implode("\n", $arySelect);
			
			break;
		

	}

	return $strValue;
	
}

// 
// ���ס�����ƥ�ץ졼�Ȥ�������ֵѤ���
// ������
//		$strMark	_%...%_ ���֤���������
// 		$strErrMsg	���顼��å�����
//
function fncGetPages($strMark, $strErrMsg="")
{
	
	switch($strMark)
	{
		case "_%PAGE_HEADER%_":
			$strValue = implode("", file(CHKLIST_TEMPLATE_DIR . "lo.header.html.template") );
			break;

		case "_%PAGE_FOOTER%_":
			$strValue = implode("", file(CHKLIST_TEMPLATE_DIR . "lo.footer.html.template") );
			break;

		case "_%PAGE_ERROR%_":
			$strValue = implode("", file(CHKLIST_TEMPLATE_DIR . "lo.error.html.template") );
			$strValue = preg_replace( "/_%errormessage%_/", $strErrMsg, $strValue);
			break;
	}
	
	return $strValue;
}

?>
