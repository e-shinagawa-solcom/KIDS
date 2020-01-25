<?php
require_once("htmltemplate.inc"); 

//デフォルトのサーチキーをセット
if( IsSet($_POST["jsdir"]) )
{
	$jsparam[searchkey] = $_POST["jsdir"];
}
else
{
	$jsparam[searchkey] = "/home/kuwagata/src/jsdoc/test/";
//	$jsparam[searchkey] = "C:/home/src/cmn";
}

$jsdir = $_POST["jsdir"];
$index = 0;
$jsparam;

fncJSfile($jsdir,$index);

//インデックス番号

function fncJSfile($jsdir)
{
	global $jsparam;
	global $index;

	//windowsの環境で使用する場合のみ使用
//	Mb_Convert_Variables("SJIS","UTF-8", $jsdir);
	
	//作業ディレクトリの移動
	@ChDir($jsdir);
	
	//作業ディレクトリのjavascriptファイルを配列で取得
	$aryJSfiles = Glob("*.js");
	
	//作業ディレクトリのjavascriptファイルの数
	$aryJSfilesLength = Count($aryJSfiles);

	//javascriptのファイルがあったらフォルダのパスを配列に格納
	if($aryJSfilesLength > 0)
	{
		$jsparam[jsfiles][] = "<div class=\"sub\">";
		$jsparam[jsfiles][] = "■「".GetCwd()."」<br>";
	}

	//インデックス
	for( $i=0 ; $i<$aryJSfilesLength ; $i++ )
	{
		//ファイル名を表示
		$filename = $aryJSfiles[$i];
		$jsparam[jsfiles][] = "<a href=# onClick='treeMenu(\"".$index."_".$i."\"); return false;'>○<b>".$filename."</b></a><br>";
	
		$jsparam[jsfiles][] = "<div id=\"menuId_".$index."_".$i."\" style=\"display:none\">";
	
		//ファイルの一行づづを配列に入れる
		$file = File( $aryJSfiles[$i] );
	
		//配列の長さ(ファイルの行の長さ)
		$fileLength = Count($file);
		
		//main部分を作成する
		fncJSMain($file, $fileLength, $filename, $i);

		//各関数ブロックの先頭行番号
		$sentouNo = 0;

		//index部分を作成する
		//配列を0からカウントすると「<!--」が出力されそれ以降すべてがコメントアウトされるため
		//配列１からカウント
		for( $j=1 ; $j<$fileLength - 1 ; $j++ )
		{
			//各関数ブロックの先頭行番号を格納
			if( Mb_EReg("^//@", $file[$j]) ||
				Mb_EReg("^'@",  $file[$j]) )
			{
				$sentouNo = $j;
			}

			//「function」の記述があったら出力
			if( Mb_EReg("^function", $file[$j]) )
			{
				//関数名を抜き出す
				//空白の位置
				$intStart = StrCSpn($file[$j], " ") +1;
				//「(」の位置
				$intEnd   = StrCSpn($file[$j], "(") - $intStart;
				//関数名
				$functionName = Mb_StrCut($file[$j], $intStart, $intEnd);

				//リンク先の行番号
				$linkNo = $index."_".$i."_".$sentouNo;

				//関数名を出力
				$jsparam[jsfiles][] = "　・<a href=\"#fileId".$linkNo."\" onClick=\"fncSentaku('linkId".$linkNo."')\">".$functionName."</a><br>";
			}
			//「Public Function」の記述があったら出力
			else if( Mb_EReg("^Public Function", $file[$j]) )
			{
				//関数名を抜き出す
				//開始位置
				$intStart = 15;
				//「(」の位置
				$intEnd   = StrCSpn($file[$j], "(") - $intStart;
				//関数名
				$functionName = Mb_StrCut($file[$j], $intStart, $intEnd);

				//リンク先の行番号
				$linkNo = $index."_".$i."_".$sentouNo;

				//関数名を出力
				$jsparam[jsfiles][] = "　・<a href=\"#fileId".$linkNo."\" onClick=\"fncSentaku('linkId".$linkNo."')\">".$functionName."</a><br>";
			}

		}

		$jsparam[jsfiles][] = "</div>";
	}

	//javascriptのファイルがあったらブロックを閉じる
	if($aryJSfilesLength > 0)
	{
		$jsparam[jsfiles][] = "</div>";
	}


	//ディレクトリにフォルダがあったら、再帰的に関数を読み出す
	if ($handle = @opendir($jsdir))
	{
		while( $dirName = ReadDir($handle) )
		{
			if( $dirName != "." && $dirName != ".." && Is_Dir($jsdir."/".$dirName) )
			{
				fncJSfile($jsdir."/".$dirName, ++$index);
			}
		}
		closedir($handle);
	}

}


//mainの部分を作成
function fncJSMain($file, $fileLength, $filename ,$i)
{
	global $jsparam;
	global $index;

	//ブロックの開始を表示
	$jsparam[main][] = "<div id=\"mainId_".$index."_".$i."\" style=\"display:none\">";

	//ファイル名を表示
	$jsparam[main][] = "○<b>".$filename."</b><br><br>";

	//関数の先頭フラグ
	$sentouFlg = 0;

	//各説明文のフラグ
	$helpFlg = 0;

	//関数の部分だったらフラグ。「(function fncName(引数)」を表示するため)
	$kansuuFlg = 0;

	//関数の個数のつじつまあわせ。最後の関数に「</div>」を挿入するために使用
	$tsujitsumaFlg = 0;

	for( $j=1 ; $j<$fileLength ; $j++ )
	{
		//ファイルの概要を表示する
		if( Mb_EReg("^//:", $file[$j]) || 
			Mb_EReg("^':", $file[$j]) )
		{
			$file[$j] = fncConvertForHtml($file[$j]);
			$jsparam[main][] = $file[$j]."<br>";
			continue;
		}

		//各関数の先頭「//@」を探す
		if( Mb_EReg("^//@", $file[$j]) || 
			Mb_EReg("^'@", $file[$j]) )
		{
			//最初の関数
			if($sentouFlg == 0)
			{
				$jsparam[main][] = "<br><br>";
				$sentouFlg = 1;
			}
			else
			{
				//関数の終了ブロックを、変数に記述
				$jsparam[main][] = "</div>";
				$tsujitsumaFlg = 0;
			}
			//関数の開始ブロックを、変数に格納
			$jsparam[main][] = "<div id=\"linKId".$index."_".$i."_".$j."\">";
			$jsparam[main][] = "<a name=\"fileId".$index."_".$i."_".$j."\"></a>";

			$helpFlg = 1;
			$tsujitsumaFlg = 1;

			//行をHTML出力用に変換し、変数に格納
			$file[$j] = fncConvertForHtml($file[$j]);
			$jsparam[main][] = $file[$j]."<br>";
			continue;
		}

		//functionの記述から、すべての引数を表示するまで続行
		if( $kansuuFlg == 1 )
		{
			if( Mb_EReg("\)", $file[$j]) )
			{
				//「{)」が先頭だったら改行のみ
				if( Mb_EReg("^\)", $file[$j]) )
				{
					//改行
					$jsparam[main][] = "<br>";
				}
				//「)」が先頭以外だったら各行の出力および改行
				else
				{
					//行をHTML出力用に変換し、変数に格納
					$file[$j] = fncConvertForHtml($file[$j]);
					$jsparam[main][] = $file[$j]."<br>";
				}
				$kansuuFlg = 0;
			}
			else
			{
				//行をHTML出力用に変換し、変数に格納
				$file[$j] = fncConvertForHtml($file[$j]);
				$jsparam[main][] = $file[$j]."<br>";
			}
			continue;
		}

		//「function」「Public Function」の記述があったら出力
		if( Mb_EReg("^function", $file[$j]) || 
			Mb_EReg("^Public Function", $file[$j]) )
		{
			//説明文の記述を止める
			$helpFlg = 0;

			//行をHTML出力用に変換し、変数に格納
			$file[$j] = fncConvertForHtml($file[$j]);
			$jsparam[main][] = $file[$j]."<br>";

			//「)」関数の開始位置を検索（引数をすべて表示させて、改行するため）
			if( Mb_EReg("\)", $file[$j]) )
			{
				//改行
				$jsparam[main][] = "<br>";
			}
			else
			{
				$kansuuFlg = 1;
			}
			continue;
		}

		//関数の説明文の出力（「function」「Public Function」が現れるまで続行）
		if( $helpFlg == 1 )
		{
			//行をHTML出力用に変換し、変数に格納
			$file[$j] = fncConvertForHtml($file[$j]);
			$jsparam[main][] = $file[$j]."<br>";
			continue;
		}

		//ファイルの末尾
		if( $j == ($fileLength - 1) )
		{
			//</div>タグの数があってなかったら
			if($tsujitsumaFlg == 1)
			{
				//関数の終了ブロックを、変数に記述
				$jsparam[main][] = "</div>";
				$tsujitsumaFlg = 0;
				continue;
			}
		}

	}
	//ブロックの終わりを表示
	$jsparam[main][] = "</div>";
}


//htmlへ出力するための変換
function fncConvertForHtml($file)
{
	//改行文字コードを省く
	$file = str_replace("\n","",$file);
	//タブを&nbsp;に変換
	$file = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$file);
	//半角スペースを&nbsp;に変換
	$file = str_replace(" ","&nbsp;",$file);

	//「<」タグの変換
	$file = str_replace("<","&lt;",$file);
	//「>」タグの変換
	$file = str_replace(">","&gt;",$file);

	return $file;
}

HtmlTemplate::t_include("/home/kuwagata/src/jsdoc/jsdoc.html",$jsparam); 
//HtmlTemplate::t_include("C:/home/src/jsdoc.html",$jsparam); 

?>
