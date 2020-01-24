<?php

// ----------------------------------------------------------------------------
/**
*       商品管理  商品仕様詳細・画像アップロード処理用スクリプト（HTML生成）
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
*       処理概要
*         ・初期登録画面を表示
*         ・入力エラーチェック
*         ・登録ボタン押下後、登録確認画面へ
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

	// 環境設定読み込み
	include( 'conf.inc' );
	require_once(CLS_IMAGELO_FILE);
	
	// イメージ・ラージオブジェクト操作オブジェクト生成
	$objImageLo = new clsImageLo();

	// ファイル変数よりイメージ情報構造体へ格納
	$aryImageInfo = $objImageLo->getUploadFileInfo($_FILES, 'userfile');

	// 一度保持されたイメージディレクトリを参照する
	if(!empty($_GET["strTempImageDir"]))
	{
		$strTempImageDir = $_GET["strTempImageDir"];
	}

	$strDestPath = constant("USER_IMAGE_PEDIT_TMPDIR");
	// イメージ情報構造体を基に、ファイルをテンポラリに保存しその結果を得る
	if(!$objImageLo->setTempImage($aryImageInfo, $strDestPath, $strTempImageDir, $strTempImageFile))
	{
		// コピー失敗
		echo "";
		exit;
	}

	//	HTMLから参照するパス
	$refpath = constant("DEF_PEDIT_IMGTMP").$strTempImageDir."/".$strTempImageFile;


	// 画像サイズの確認
	list($lngWidth, $lngHeight, $lngType, $strAttr) = getimagesize($strDestPath.$strTempImageDir."/".$strTempImageFile);
	// 規定以上のサイズの場合リサイズ（<img>タグの要素指定のみ）
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
	
	//	エディターに画像をセットする
	setImage("<?=$refpath?>");
	
}

function setImage(imagepath){
	
	//	hidden要素につけるPHP参照用配列名
	//var PHP_ARRAY_NAME = "uploadimages[]";
	
	var HTMLTEXT = window.parent.document.getElementById("htmltext");
	HTMLTEXT.innerHTML += '<a href="' + imagepath + '" target="_blank"><img src="'+imagepath+'" <?=$strAttr?> border="0" />';

//alert(HTMLTEXT.innerHTML);
// HTMLTEXT.innerHTML.match(/(https:\/\/[\w|.|-]+)/i);
//alert(RegExp.$1);

//	HTMLTEXT.innerHTML = HTMLTEXT.innerHTML.replace(/(https:\/\/[\w|.|-]+[^\/]+)/i, '');
//alert(HTMLTEXT.innerHTML);


	// アップロードイメージディレクトリを保存
	window.parent.parent.document.all.EditorDir.innerHTML	= '<input type="hidden" name="strTempImageDir" value="<?=$strTempImageDir?>" />';


	// 「参照」ボックスをクリア
	window.parent.document.getElementById( "form-uploadimage" ).reset();


	//	hiddenノードを追加する
	//var PPP = parent.parent;
	//PPP.document.all.EditorRecord.innerHTML += '<span><?=$encfilename?></span>'
	//PPP.document.all.EditorRecord.innerHTML += '<input id="'+PHP_ARRAY_NAME+'" name="'+PHP_ARRAY_NAME+'" type="hidden" value="<?=$encfilename?>" />';
}


--></script>
</head>
<body></body>
</html>