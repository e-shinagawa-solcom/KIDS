<!--


//------------------------------------------------------------
// ���� : �إå������᡼�������
//------------------------------------------------------------
var headerAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="L/C ͽ��ɽ">';
var headerAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="L/C SCHEDULE FILE">';


//------------------------------------------------------------
// ���� : �ץ�ӥ塼�ܥ��󥤥᡼�������
//------------------------------------------------------------
var blownpreviewBt = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncBlownPreviewButton( \'on\' , this );" onmouseout="fncBlownPreviewButton( \'off\' , this );fncAlphaOff( this );" src="' + blownpreviewbt1 + '" width="72" height="20" border="0" alt="PREVIEW"></a>';


//------------------------------------------------------------
// ���� : �������ݡ��ȥܥ��󥤥᡼�������
//------------------------------------------------------------
var blownexportBt = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncBlownExportButton( \'on\' , this );" onmouseout="fncBlownExportButton( \'off\' , this );fncAlphaOff( this );" src="' + blownexportbt1 + '" width="72" height="20" border="0" alt="PREVIEW"></a>';





//------------------------------------------------------------
// ���� : ���ܸ졦�Ѹ����شؿ�
//------------------------------------------------------------
function ChgEtoJ( lngCount )
{

	// �ץ�ӥ塼�ܥ���ν�Ф�
	PreviewBt.innerHTML = blownpreviewBt;
	// �������ݡ��ȥܥ���ν�Ф�
	ExportBt.innerHTML  = blownexportBt;


	// �Ѹ�
	if ( lngCount == 0 )
	{

		window.top.SegAHeader.innerHTML = headerAE;

		Column0.innerText = 'Setting date';
		Column1.innerText = 'P.O. No.';
		Column2.innerText = 'L/C Schedule file';
		Column3.innerText = 'Revise';

	}


	// ���ܸ�
	else if ( lngCount == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		Column0.innerText = '������';
		Column1.innerText = 'ȯ��Σ�.';
		Column2.innerText = 'L/C ͽ��ɽ';
		Column3.innerText = '��Х���';

	}

	return false;

}


//-->