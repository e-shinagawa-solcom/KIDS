


	// MAIN MTITLE IMAGE
	var maintitleJ	= '<img src="' + titleJ + '" width="314" height="22" border="0" alt="Excel �ե����륢�åץ�����">';
	var maintitleE	= '<img src="' + titleE + '" width="314" height="22" border="0" alt="Excel FILE UPLOADER">';

	// HEADER IMAGE
	var headerAJ	= '<img src="' + upheadtitle1J + '" width="949" height="30" border="0" alt="���åץ���">';
	var headerAE	= '<img src="' + upheadtitle1E + '" width="949" height="30" border="0" alt="UPLOAD">';


	var uploadBT1	= '<a href="#"><img onfocus="fncChangeBtImg( this, upBT2 );" onblur="fncChangeBtImg( this, upBT1 );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncChangeBtImg( this, upBT2 );" onmouseout="fncChangeBtImg( this, upBT1 ); fncAlphaOff( this );" src="' + upBT1 + '" width="72" height="20" border="0"></a>';


	// CSS VALUE
	var fcolor		= '#666666';							//	���ܥե���ȥ��顼
	var segcolor 	= '#e8f0f1';							//	�����طʿ�
	var segbody		= '#d6d0b1';							//	INPUT A BODY �طʿ�
	var brcolor01	= '#798787 #e8f0f1 #798787 #798787';	//	���ܱ�����BORDER
	var brcolor02	= '#798787 #798787 #798787 #e8f0f1';	//	���ܺ�����BORDER
	var brcolor03	= '#798787 #e8f0f1 #798787 #e8f0f1';	//	����ξ����BORDER


	function initLayoutUpload()
	{
		// �ᥤ�󥿥��ȥ�
		MainTitle.innerHTML	= maintitleJ;

		// INPUT A BODYS COLOR
		SegABodys.style.background	= segbody;

		// INPUT A HEADER TITLE IMAGE
		SegAHeader.innerHTML	= headerAJ;

		// INPUT A BOTTOM IMAGE
		SegABottom.innerHTML	= bottom03;



		// NPUT A FONT COLOR
		colExcel.style.color	= fcolor;


		// INPUT A SEGMENT BG COLOR
		colExcel.style.background	= segcolor;


		// INPUT A VARS BG COLOR
		varExcel.style.background	= segcolor;


		// INPUT A SEGMENT BORDER COLOR
		colExcel.style.borderColor	= brcolor01;


		// INPUT A VARS BORDER COLOR
		varExcel.style.borderColor	= brcolor02;


		// UPLOAD BUTTON
		if( typeof(btnUpload) != "undefined" ) btnUpload.innerHTML	= uploadBT1;



		return false;
	}
