<?php
/*
 Excel_Viewer Ver1.1beta4
  Author:kishiyan


　このサンプルの使い方
動作条件
	動作には、Excel_Peruser Ver0.11以上が必要です
	サーバー上でPHPが使用できること
	ファイルのuploadを許可する設定であること
	アップロード可能なファイルサイズは、max_upload_size
	に設定した値以下です。始めは128kbysにしてあります。

動作方法
　１．同梱の peruser.php と一緒にこのファイルを
　　　WEBアクセス可能なフォルダーに置きます。
　２．ブラウザーでこのファイルをアクセスします
　３．ファイルアップロードのフォームがでますのでそこに
　　　EXCELファイル(同梱のperuser011-demo.xls等)を送信します
　４．後は、画面でエラーが出ていないことを確認してください
　５．アップロード時に、チェックボックス「プロパティーも取得」
　　　にチェックを入れればプロパティーも読み込んで表示します。

注意
　これはあくまでサンプルソフトです。このソフトが動作する状態で
　インターネット上に“絶対”公開しないでください。
　xss等のセキュリティー対策は未実施です。
　あくまでExcel_Peruserの動作確認用に限定して使用してください。

　このサンプルでは、MS-EXCELでの表示を真似てブラウザ上に表示しますが、
　セル結合した場合の右部または下部の罫線の表示は、正しく表示しない場合が
　あります。これはExcel_Peruserで正しく読めていないのではなく、作者の
　htmlに変換する際の手抜きです。（スタイルシートって面倒ですね）
*/

require_once 'peruser.php';

// makeptn関数(セルのパターン塗りつぶし画像生成)を使う場合は必ず先頭で処理
if (isset($_GET['ptn'])) makeptn($_GET['ptn'],$_GET['fc']);

// utf-8以外の文字エンコーディングを使用する場合は、
// 以下のutf-8を変更してください。文字コードによっては機種依存文字が化ける
// 場合があります
$charset='utf-8';

// このサンプルで利用可能な最大ファイルサイズです
$max_upload_size = 1024 * 128; 

$errmes='';
if (This_Class_Name != 'Excel_Peruser')
	$errmes='Excel_Peruserが正しく読み込まれていません';
if (Peruser_Ver != 0.110)
	$errmes='Excel_Peruserのバージョンが異なります';

mb_internal_encoding($charset);
putheader($charset);

$uperror=-1;
if (isset($_FILES['userfile']['tmp_name']) && $errmes===""){
	switch($_FILES['userfile']['error']){
	case 0: $errmes="";
		break;
	case 1:
		$errmes="アップロードされたファイルは、php.ini の upload_max_filesize の値を超えています。";
		break;
        case 2:
		$errmes="アップロードされたファイルは、HTML フォームで指定された MAX_FILE_SIZE を超えています。";
                break;
        case 3:
                $errmes="アップロードされたファイルは一部しかアップロードされていません。";
                break;
        case 4:
                $errmes="ファイルはアップロードされませんでした。";
                break;
        case 5:
                $errmes="不明なエラー";
                break;
        case 6:
                $errmes="テンポラリフォルダがありません。php.iniまたはテンポラリーフォルダー有無およびパーミッションの確認をしてください";
                break;
        case 7:
                $errmes="ディスクへの書き込みに失敗しました。パーミッションの確認をしてください";
                break;
	}
	if (($errmes=="") && (!mb_ereg("\.xls$",$_FILES['userfile']['name']))) $errmes="EXCELファイルでは有りません";
}
if (isset($_FILES['userfile']['tmp_name']) && $errmes==="") {
	$obj =NEW Excel_Peruser;
	$obj->setErrorHandling(1);
	$obj->setInternalCharset($charset);
	$result=$obj->fileread($_FILES['userfile']['tmp_name']);
	if ($obj->isError($result)) {
		$errmes=$result->getMessage();
		if (strpos($errmes,$_FILES['userfile']['tmp_name'])!==false)
			$errmes=str_replace($_FILES['userfile']['tmp_name'],$_FILES['userfile']['name'],$errmes);
			$errmes=str_replace('Template file','Uploaded file',$errmes);
	} else {
		putcss();
	}
}
putform();

if($errmes) print outStr($errmes);
if (isset($_FILES['userfile']['tmp_name']) && $errmes==="") {
	print "<p>ファイル名　". outStr($_FILES['userfile']['name']) . " (" . $_FILES['userfile']['size'] . "bytes)</p>\n";
	if (isset($_POST['selprop']))
	if ($_POST['selprop']=='on') {
//		$prp=$obj->getPropEN();
		$prp=$obj->getPropJP();
		if (count($prp)>1){
			print '<table border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC"><tr bgcolor="#F8FFFF"><td bgcolor="#E0E0E0">プロパティ</td><td bgcolor="#E0E0E0">値</td></tr>'."\n";
			foreach($prp as $propid => $val){
				$val=mb_eregi_replace ('&lt;br */?&gt;','<br />',outStr($val));
				print "	<tr bgcolor='#F8FFFF'><td bgcolor='#E0E0E0'><font size=2>".outStr($propid)."</font></td>";
				print "<td bgcolor='#F8FFFF'><font size=2>${val}</font></td></tr>\n";
			}
			print "</table><p></p>\n";
		} else{
			print "\n<small>有効なプロパティを取得できませんでした。</small><br><br>\n";
		}
	}

	for($sn=0;$sn<$obj->sheetnum;$sn++){
		$w=32;
		if (!isset($obj->maxcell[$sn])) $obj->maxcell[$sn]=0;
		for($i=0;$i<=$obj->maxcell[$sn];$i++){
			$w+=$obj->getColWidth($sn,$i);
		}
	print "シート".$sn."： ".outStr($obj->sheetname[$sn]) ."<br>\n";
	$hd=$obj->getHEADER($sn);
	$ft=$obj->getFOOTER($sn);
	if ($hd!==null){
		$hd['left']=(isset($hd['left']))? outStr($hd['left']):'';
		$hd['center']=(isset($hd['center']))? outStr($hd['center']):'';
		$hd['right']=(isset($hd['right']))? outStr($hd['right']):'';
print <<<STR1
<table width="${w}" border="0" cellpadding="0" cellspacing="1" bordercolor="#CCCCCC" bgcolor="#CCCCCC">
<tr>
    <td width="30" nowrap><font size="1">ヘッダ</font></td>
    <td bgcolor="#FFFFFF"><div align="left"> ${hd['left']} </div></td>
    <td bgcolor="#FFFFFF"><div align="center"> ${hd['center']} </div></td>
    <td bgcolor="#FFFFFF"><div align="right"> ${hd['right']} </div></td>
</tr></table>
STR1;
	}
print <<<STR2
<table border="0" cellpadding="0" cellspacing="0" width="${w}" bgcolor="#FFFFFF" style="border-collapse: collapse;">
  <tr bgcolor="#CCCCCC">
    <th class="XF" bgcolor="#CCCCCC" scope="col" width="32">&nbsp;</th>
STR2;
	for($i=0;$i<=$obj->maxcell[$sn];$i++){
		$tdwidth=$obj->getColWidth($sn,$i);
		print '    <th class="XF" bgcolor="#CCCCCC" scope="col" width="';
		print $tdwidth.'">'.$i.'</th>'."\n";
	}
	print "  </tr>\n";
	if (!isset($obj->maxrow[$sn])) $obj->maxrow[$sn]=0;
	for($r=0;$r<=$obj->maxrow[$sn];$r++){
		print '  <tr height="'.$obj->getRowHeight($sn,$r).'">'."\n";
		print '    <th class="XF" bgcolor="#CCCCCC" scope="row">'.$r."</th>\n";
		for($i=0;$i<=$obj->maxcell[$sn];$i++){
			$tdwidth=$obj->getColWidth($sn,$i);
			$dispval=$obj->dispcell($sn,$r,$i);
			$dispval=outStr($dispval);
			if (isset($obj->hlink[$sn][$r][$i])){
				$dispval='<a href="'.$obj->hlink[$sn][$r][$i].'">'.$dispval.'</a>';
			}
		if ($dispval=='') $dispval=' ';
		$xf=$obj->getAttribute($sn,$r,$i);
		if (isset($xf['wrap']))
		if ($xf['wrap']) $dispval=ereg_replace("\n", "<br />", $dispval);
		$xfno=($xf['xf']>0) ? $xf['xf']: 0;

		$align ='x';
		if (isset($xf['halign'])) if ($xf['halign'] != 0) $align= '';
		if ($align == 'x') {
//			if (is_numeric($dispval)) $align = ' Align="right" '.$xf['type'];
			if ($xf['type']==Type_RK) $align = ' Align="right"';
			else if ($xf['type']==Type_RK2) $align = ' Align="right"';
			else if ($xf['type']==Type_NUMBER) $align = ' Align="right"';
			else if ($xf['type']==Type_FORMULA && is_numeric($dispval)) $align = ' Align="right"';
			else if ($xf['type']==Type_FORMULA2 && is_numeric($dispval)) $align = ' Align="right"';
			else if ($xf['type']==Type_FORMULA && ($dispval=='TRUE' || $dispval=='FALSE')) $align = ' Align="center"';
			else if ($xf['type']==Type_FORMULA2 && ($dispval=='TRUE' || $dispval=='FALSE')) $align = ' Align="center"';
			else if ($xf['type']==Type_BOOLERR) $align = ' Align="center"';
			else $align= '';
			if ($xf['format']=='@') $align = '';
		} else $align= '';
		if (substr($dispval,0,1)=="'") $dispval=substr($dispval,1);
		if (substr($dispval,0,6)=="&#039;") $dispval=substr($dispval,6);

		if(isset($obj->celmergeinfo[$sn][$r][$i]['cond'])){
			if($obj->celmergeinfo[$sn][$r][$i]['cond']==1){
				$colspan=$obj->celmergeinfo[$sn][$r][$i]['cspan'];
				$rowspan=$obj->celmergeinfo[$sn][$r][$i]['rspan'];
				if($colspan>1) $rcspan =' colspan="'.$colspan.'"';else $rcspan=' width="'.$tdwidth.'"';
				if($rowspan>1) $rcspan.=' rowspan="'.$rowspan.'"';
				print '    <td class="XFs'. $sn . "r" . $r . "c" . $i .'" '.$rcspan.$align.'>'.$dispval."</td>\n";
			}
		} else {
			print '    <td class="XF'.$xfno.'" width="'.$tdwidth.'"'.$align.'>'.$dispval."</td>\n";
		}
	}
		print "</tr>\n";
}
print "</table>\n";
if ($ft!==null){
	$ft['left'] = (isset($ft['left']))? outStr($ft['left']):'';
	$ft['center']= (isset($ft['center']))? outStr($ft['center']):'';
	$ft['right']= (isset($ft['right']))? outStr($ft['right']):'';
	print <<<STR3
<table width="${w}" border="0" cellpadding="0" cellspacing="1" bordercolor="#CCCCCC" bgcolor="#CCCCCC"><tr>
    <td width="30" nowrap><font size="1">フッタ</font></td>
    <td bgcolor="#FFFFFF"><div align="left">${ft['left']} </div></td>
    <td bgcolor="#FFFFFF"><div align="center">${ft['center']}</div></td>
    <td bgcolor="#FFFFFF"><div align="right">${ft['right']}</div></td>
</tr></table>
STR3;
	}
	print "<p> </p>\n";
	}
}
print <<<EOD
<p> </p><hr>
<!--copyright--><div style="color: gray; font-size: 9px; text-align: center">
Powered by Excel_Peruser<br>
Copyright &copy; 2007-2008 kishiyan <a href="http://chazuke.com">茶漬けドットコム</a>
</div><!--copyright-->
</body>
</html>
EOD;

function putheader($charset){
	$chr=strtolower ($charset);
	if (substr($chr,0,4)=='sjis') $charset="Shift-JIS";
	if (substr($chr,0,5)=='eucjp') $charset="EUC-JP";
	if (substr($chr,0,3)=='jis') $charset="ISO-2022-JP";

header("Content-Type: text/html; charset=${charset}");
print <<<_HEADER_
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html"; charset="${charset}">
<title>Excel_Reviser ユーティリティ[EXCEL Viewer]</title>
_HEADER_;
}

function putform(){
	global $max_upload_size;
print <<<STR4
</head><body bgcolor="#FFFFF0"><center>
<H2>EXCEL Viewer Ver1.1 (Excel_Peruserテスト用)</H2>
<form enctype="multipart/form-data" action=${_SERVER['SCRIPT_NAME']}  method="POST">
<table border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
  <tr bgcolor="#F8FFFF">
    <td colspan="2" align="right" nowrap>EXCELファイルを送信するとHTMLに変換して表示します<br>
	<small>アップロード可能な最大ファイルサイズは、<strong>${max_upload_size} バイト</strong>です</small></td>
  </tr>
  <tr>
    <td width="100" align="right" bgcolor="#F8FFFF">送信ファイル</td>
    <td bgcolor="#FFFFF8">
    <input type="hidden" name="MAX_FILE_SIZE" value="${max_upload_size}" />
    <input name="userfile" type="file" />
    <input type="submit" value="送信" /><br />
	<input type="checkbox" name="selprop">プロパティも取得
  </td>
  </tr>
</table>
</form></center>
<hr>
STR4;
}

function putcss(){
	global $obj;
	$css=$obj->makecss();
print <<<_CSS
<style type="text/css">
<!--
body,td,th {
	font-size: normal;
}

.XF {
border-top-width: 1px;
border-top-style: solid;
border-top-color: #000000;
border-left-width: 1px;
border-left-style: solid;
border-left-color: #000000;
border-bottom-width: 1px;
border-bottom-style: solid;
border-bottom-color: #000000;
border-right-width: 1px;
border-right-style: solid;
border-right-color: #000000;
}

${css}
-->
</style>
_CSS;
}

function outStr($str){
	global $charset;
	$str = htmlentities($str, ENT_QUOTES,$charset);
	$str = str_replace('&conint;',mb_convert_encoding("∮", $charset, "utf-8"),$str);
	$str = str_replace('&ang90;',mb_convert_encoding("∟", $charset, "utf-8"),$str);
	$str = str_replace('&becaus;',mb_convert_encoding("∵", $charset, "utf-8"),$str);
	return $str;
}
?>
