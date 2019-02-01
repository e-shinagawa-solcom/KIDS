<!--


//------------------------------------------------------------
// 解説 : 日本語・英語切替関数
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// 英語
	if( lngSelfCode == 0 )
	{
		RestartBt.innerHTML = restartBtE1;
		StopBt.innerHTML    = stopBtE1;
	}

	// 日本語
	else if( lngSelfCode == 1 )
	{
		RestartBt.innerHTML = restartBtJ1;
		StopBt.innerHTML    = stopBtJ1;
	}

	return false;

}


//-->