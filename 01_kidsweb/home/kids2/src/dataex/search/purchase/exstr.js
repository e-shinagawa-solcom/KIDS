<!--


//------------------------------------------------------------
// ���� : �إå������᡼�������
//------------------------------------------------------------
var headerAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="PURCHASE RECIPE FILE ����">';
var headerAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="PURCHASE RECIPE FILE SEARCH">';


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

	// �ץ�ӥ塼�ܥ���ν�Ф�
	PreviewBt2.innerHTML = blownpreviewBt;
	// �������ݡ��ȥܥ���ν�Ф�
	ExportBt2.innerHTML  = blownexportBt;

	return false;

}


//-->