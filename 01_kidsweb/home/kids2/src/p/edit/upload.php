<?php

// ----------------------------------------------------------------------------
/**
*       ���ʴ���  ���ʻ��;ܺ١��������åץ��ɽ����ѥ�����ץȡ�HTML������
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       ��������
*         �������Ͽ���̤�ɽ��
*         �����ϥ��顼�����å�
*         ����Ͽ�ܥ��󲡲��塢��Ͽ��ǧ���̤�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------

	// �Ķ������ɤ߹���
	include( 'conf.inc' );
	require_once(CLS_IMAGELO_FILE);
	
	// ���᡼�����顼�����֥����������֥�����������
	$objImageLo = new clsImageLo();

	// �ե������ѿ���ꥤ�᡼������¤�Τس�Ǽ
	$aryImageInfo = $objImageLo->getUploadFileInfo($_FILES, 'userfile');

	// �����ݻ����줿���᡼���ǥ��쥯�ȥ�򻲾Ȥ���
	if(!empty($_GET["strTempImageDir"]))
	{
		$strTempImageDir = $_GET["strTempImageDir"];
	}

	$strDestPath = constant("USER_IMAGE_PEDIT_TMPDIR");
	// ���᡼������¤�Τ��ˡ��ե������ƥ�ݥ�����¸�����η�̤�����
	if(!$objImageLo->setTempImage($aryImageInfo, $strDestPath, $strTempImageDir, $strTempImageFile))
	{
		// ���ԡ�����
		echo "";
		exit;
	}

	//	HTML���黲�Ȥ���ѥ�
	$refpath = constant("DEF_PEDIT_IMGTMP").$strTempImageDir."/".$strTempImageFile;


	// �����������γ�ǧ
	list($lngWidth, $lngHeight, $lngType, $strAttr) = getimagesize($strDestPath.$strTempImageDir."/".$strTempImageFile);
	// ����ʾ�Υ������ξ��ꥵ������<img>���������ǻ���Τߡ�
	if($lngWidth > 630 || $lngHeight > 380)
	{
		$strAttr = 'width="630" height="380"';
	}
	//var_dump($strAttr);

?>
<html>
<head>
<script type="text/javascript" language="javascript">
<!--

window.onload = function(){
	
	//	���ǥ������˲����򥻥åȤ���
	setImage("<?=$refpath?>");
	
}

function setImage(imagepath){
	
	//	hidden���ǤˤĤ���PHP����������̾
	//var PHP_ARRAY_NAME = "uploadimages[]";
	
	var HTMLTEXT = window.parent.document.getElementById("htmltext");
	HTMLTEXT.innerHTML += '<a href="' + imagepath + '" target="_blank"><img src="'+imagepath+'" <?=$strAttr?> border="0" />';

//alert(HTMLTEXT.innerHTML);
// HTMLTEXT.innerHTML.match(/(https:\/\/[\w|.|-]+)/i);
//alert(RegExp.$1);

//	HTMLTEXT.innerHTML = HTMLTEXT.innerHTML.replace(/(https:\/\/[\w|.|-]+[^\/]+)/i, '');
//alert(HTMLTEXT.innerHTML);


	// ���åץ��ɥ��᡼���ǥ��쥯�ȥ����¸
	window.parent.parent.document.all.EditorDir.innerHTML	= '<input type="hidden" name="strTempImageDir" value="<?=$strTempImageDir?>" />';


	// �ֻ��ȡץܥå����򥯥ꥢ
	window.parent.document.getElementById( "form-uploadimage" ).reset();


	//	hidden�Ρ��ɤ��ɲä���
	//var PPP = parent.parent;
	//PPP.document.all.EditorRecord.innerHTML += '<span><?=$encfilename?></span>'
	//PPP.document.all.EditorRecord.innerHTML += '<input id="'+PHP_ARRAY_NAME+'" name="'+PHP_ARRAY_NAME+'" type="hidden" value="<?=$encfilename?>" />';
}


--></script>
</head>
<body></body>
</html>