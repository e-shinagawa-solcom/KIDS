<!--


//------------------------------------------------------------
// ���� : ���ܸ졦�Ѹ����شؿ�
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// �Ѹ�
	if( lngSelfCode == 0 )
	{
		RestartBt.innerHTML = restartBtE1;
		StopBt.innerHTML    = stopBtE1;
	}

	// ���ܸ�
	else if( lngSelfCode == 1 )
	{
		RestartBt.innerHTML = restartBtJ1;
		StopBt.innerHTML    = stopBtJ1;
	}

	return false;

}


//-->