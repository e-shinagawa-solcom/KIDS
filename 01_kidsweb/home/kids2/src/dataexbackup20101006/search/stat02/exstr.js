<!--


//------------------------------------------------------------
// ���� : �إå������᡼�������
//------------------------------------------------------------
var headerAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="�������ץǡ���  02�ʳ������ˡ�����">';
var headerAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="Statistical 02(Rough estimate sales)��SEARCH">';


//------------------------------------------------------------
// ���� : �ץ�ӥ塼�ܥ��󥤥᡼�������
//------------------------------------------------------------
var blownpreviewBt = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncBlownPreviewButton( \'on\' , this );" onmouseout="fncBlownPreviewButton( \'off\' , this );fncAlphaOff( this );" src="' + blownpreviewbt1 + '" width="72" height="20" border="0" alt="PREVIEW"></a>';


//------------------------------------------------------------
// ���� : �������ݡ��ȥܥ��󥤥᡼�������
//------------------------------------------------------------
var blownexportBt = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncBlownExportButton( \'on\' , this );" onmouseout="fncBlownExportButton( \'off\' , this );fncAlphaOff( this );" src="' + blownexportbt1 + '" width="72" height="20" border="0" alt="EXPORT"></a>';





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

		Column0.innerText = 'Sales / Adding up day';
		Column1.innerText = 'Group';
		Column2.innerText = 'Sales Class';

	}


	// ���ܸ�
	else if ( lngCount == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		Column0.innerText = '��塦�׾���';
		Column1.innerText = '����';
		Column2.innerText = '����ʬ';

	}

	return false;

}


//-->