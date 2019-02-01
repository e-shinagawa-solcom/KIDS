


	// MAIN MTITLE IMAGE
	var maintitleJ	= '<img src="' + titleJ + '" width="314" height="22" border="0" alt="Excel ファイルアップローダー">';
	var maintitleE	= '<img src="' + titleE + '" width="314" height="22" border="0" alt="Excel FILE UPLOADER">';

	// HEADER IMAGE
	var headerAJ	= '<img src="' + upheadtitle1J + '" width="949" height="30" border="0" alt="アップロード">';
	var headerAE	= '<img src="' + upheadtitle1E + '" width="949" height="30" border="0" alt="UPLOAD">';


	var uploadBT1	= '<a href="#"><img onfocus="fncChangeBtImg( this, upBT2 );" onblur="fncChangeBtImg( this, upBT1 );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncChangeBtImg( this, upBT2 );" onmouseout="fncChangeBtImg( this, upBT1 ); fncAlphaOff( this );" src="' + upBT1 + '" width="72" height="20" border="0"></a>';


	// CSS VALUE
	var fcolor		= '#666666';							//	項目フォントカラー
	var segcolor 	= '#e8f0f1';							//	項目背景色
	var segbody		= '#d6d0b1';							//	INPUT A BODY 背景色
	var brcolor01	= '#798787 #e8f0f1 #798787 #798787';	//	項目右空きBORDER
	var brcolor02	= '#798787 #798787 #798787 #e8f0f1';	//	項目左空きBORDER
	var brcolor03	= '#798787 #e8f0f1 #798787 #e8f0f1';	//	項目両空きBORDER


	function initLayoutUpload()
	{
		// メインタイトル
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
