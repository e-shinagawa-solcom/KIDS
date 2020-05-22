<?
    // 期取得
    $basePeriod = 42;
    $baseDate   = '2019-04-01';

    $dtminvoicedate = '2021/04/31';
    $invoicemonth = '04';
    $dateTimeBase = new DateTime($baseDate);
    $dateTimeNow  = new DateTime(substr($dtminvoicedate, 0, 4) ."-".sprintf('%02d', $invoicemonth) ."-02");
    $diff   = $dateTimeBase->diff($dateTimeNow);
	$diffY = (int)$diff->format('%Y');
	$diffM = (int)$diff->format('%M');
    $diffD = (int)$diff->format('%D');
	if ($diffM != 0 || $diffD != 0) {
		$diffY = $diffY + 1;
	}
	if ($dateTimeBase > $dateTimeNow)
	{
		$period = $basePeriod - $diffY;
	} else {
		$period = $basePeriod + $diffY - 1;
    }
    $thisMonth = $dateTimeNow->format('m');
    
    var_dump($period);
    var_dump($thisMonth);
