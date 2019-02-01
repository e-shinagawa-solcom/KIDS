<?php
require_once("htmltemplate.inc"); 

//�ǥե���ȤΥ����������򥻥å�
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

//����ǥå����ֹ�

function fncJSfile($jsdir)
{
	global $jsparam;
	global $index;

	//windows�δĶ��ǻ��Ѥ�����Τ߻���
//	Mb_Convert_Variables("SJIS","EUC-JP", $jsdir);
	
	//��ȥǥ��쥯�ȥ�ΰ�ư
	@ChDir($jsdir);
	
	//��ȥǥ��쥯�ȥ��javascript�ե����������Ǽ���
	$aryJSfiles = Glob("*.js");
	
	//��ȥǥ��쥯�ȥ��javascript�ե�����ο�
	$aryJSfilesLength = Count($aryJSfiles);

	//javascript�Υե����뤬���ä���ե�����Υѥ�������˳�Ǽ
	if($aryJSfilesLength > 0)
	{
		$jsparam[jsfiles][] = "<div class=\"sub\">";
		$jsparam[jsfiles][] = "����".GetCwd()."��<br>";
	}

	//����ǥå���
	for( $i=0 ; $i<$aryJSfilesLength ; $i++ )
	{
		//�ե�����̾��ɽ��
		$filename = $aryJSfiles[$i];
		$jsparam[jsfiles][] = "<a href=# onClick='treeMenu(\"".$index."_".$i."\"); return false;'>��<b>".$filename."</b></a><br>";
	
		$jsparam[jsfiles][] = "<div id=\"menuId_".$index."_".$i."\" style=\"display:none\">";
	
		//�ե�����ΰ�ԤŤŤ�����������
		$file = File( $aryJSfiles[$i] );
	
		//�����Ĺ��(�ե�����ιԤ�Ĺ��)
		$fileLength = Count($file);
		
		//main��ʬ���������
		fncJSMain($file, $fileLength, $filename, $i);

		//�ƴؿ��֥�å�����Ƭ���ֹ�
		$sentouNo = 0;

		//index��ʬ���������
		//�����0���饫����Ȥ���ȡ�<!--�פ����Ϥ��줽��ʹߤ��٤Ƥ������ȥ����Ȥ���뤿��
		//���󣱤��饫�����
		for( $j=1 ; $j<$fileLength - 1 ; $j++ )
		{
			//�ƴؿ��֥�å�����Ƭ���ֹ���Ǽ
			if( Mb_EReg("^//@", $file[$j]) ||
				Mb_EReg("^'@",  $file[$j]) )
			{
				$sentouNo = $j;
			}

			//��function�פε��Ҥ����ä������
			if( Mb_EReg("^function", $file[$j]) )
			{
				//�ؿ�̾��ȴ���Ф�
				//����ΰ���
				$intStart = StrCSpn($file[$j], " ") +1;
				//��(�פΰ���
				$intEnd   = StrCSpn($file[$j], "(") - $intStart;
				//�ؿ�̾
				$functionName = Mb_StrCut($file[$j], $intStart, $intEnd);

				//�����ι��ֹ�
				$linkNo = $index."_".$i."_".$sentouNo;

				//�ؿ�̾�����
				$jsparam[jsfiles][] = "����<a href=\"#fileId".$linkNo."\" onClick=\"fncSentaku('linkId".$linkNo."')\">".$functionName."</a><br>";
			}
			//��Public Function�פε��Ҥ����ä������
			else if( Mb_EReg("^Public Function", $file[$j]) )
			{
				//�ؿ�̾��ȴ���Ф�
				//���ϰ���
				$intStart = 15;
				//��(�פΰ���
				$intEnd   = StrCSpn($file[$j], "(") - $intStart;
				//�ؿ�̾
				$functionName = Mb_StrCut($file[$j], $intStart, $intEnd);

				//�����ι��ֹ�
				$linkNo = $index."_".$i."_".$sentouNo;

				//�ؿ�̾�����
				$jsparam[jsfiles][] = "����<a href=\"#fileId".$linkNo."\" onClick=\"fncSentaku('linkId".$linkNo."')\">".$functionName."</a><br>";
			}

		}

		$jsparam[jsfiles][] = "</div>";
	}

	//javascript�Υե����뤬���ä���֥�å����Ĥ���
	if($aryJSfilesLength > 0)
	{
		$jsparam[jsfiles][] = "</div>";
	}


	//�ǥ��쥯�ȥ�˥ե���������ä��顢�Ƶ�Ū�˴ؿ����ɤ߽Ф�
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


//main����ʬ�����
function fncJSMain($file, $fileLength, $filename ,$i)
{
	global $jsparam;
	global $index;

	//�֥�å��γ��Ϥ�ɽ��
	$jsparam[main][] = "<div id=\"mainId_".$index."_".$i."\" style=\"display:none\">";

	//�ե�����̾��ɽ��
	$jsparam[main][] = "��<b>".$filename."</b><br><br>";

	//�ؿ�����Ƭ�ե饰
	$sentouFlg = 0;

	//������ʸ�Υե饰
	$helpFlg = 0;

	//�ؿ�����ʬ���ä���ե饰����(function fncName(����)�פ�ɽ�����뤿��)
	$kansuuFlg = 0;

	//�ؿ��θĿ��ΤĤ��Ĥޤ��碌���Ǹ�δؿ��ˡ�</div>�פ��������뤿��˻���
	$tsujitsumaFlg = 0;

	for( $j=1 ; $j<$fileLength ; $j++ )
	{
		//�ե�����γ��פ�ɽ������
		if( Mb_EReg("^//:", $file[$j]) || 
			Mb_EReg("^':", $file[$j]) )
		{
			$file[$j] = fncConvertForHtml($file[$j]);
			$jsparam[main][] = $file[$j]."<br>";
			continue;
		}

		//�ƴؿ�����Ƭ��//@�פ�õ��
		if( Mb_EReg("^//@", $file[$j]) || 
			Mb_EReg("^'@", $file[$j]) )
		{
			//�ǽ�δؿ�
			if($sentouFlg == 0)
			{
				$jsparam[main][] = "<br><br>";
				$sentouFlg = 1;
			}
			else
			{
				//�ؿ��ν�λ�֥�å����ѿ��˵���
				$jsparam[main][] = "</div>";
				$tsujitsumaFlg = 0;
			}
			//�ؿ��γ��ϥ֥�å����ѿ��˳�Ǽ
			$jsparam[main][] = "<div id=\"linKId".$index."_".$i."_".$j."\">";
			$jsparam[main][] = "<a name=\"fileId".$index."_".$i."_".$j."\"></a>";

			$helpFlg = 1;
			$tsujitsumaFlg = 1;

			//�Ԥ�HTML�����Ѥ��Ѵ������ѿ��˳�Ǽ
			$file[$j] = fncConvertForHtml($file[$j]);
			$jsparam[main][] = $file[$j]."<br>";
			continue;
		}

		//function�ε��Ҥ��顢���٤Ƥΰ�����ɽ������ޤ�³��
		if( $kansuuFlg == 1 )
		{
			if( Mb_EReg("\)", $file[$j]) )
			{
				//��{)�פ���Ƭ���ä�����ԤΤ�
				if( Mb_EReg("^\)", $file[$j]) )
				{
					//����
					$jsparam[main][] = "<br>";
				}
				//��)�פ���Ƭ�ʳ����ä���ƹԤν��Ϥ���Ӳ���
				else
				{
					//�Ԥ�HTML�����Ѥ��Ѵ������ѿ��˳�Ǽ
					$file[$j] = fncConvertForHtml($file[$j]);
					$jsparam[main][] = $file[$j]."<br>";
				}
				$kansuuFlg = 0;
			}
			else
			{
				//�Ԥ�HTML�����Ѥ��Ѵ������ѿ��˳�Ǽ
				$file[$j] = fncConvertForHtml($file[$j]);
				$jsparam[main][] = $file[$j]."<br>";
			}
			continue;
		}

		//��function�ס�Public Function�פε��Ҥ����ä������
		if( Mb_EReg("^function", $file[$j]) || 
			Mb_EReg("^Public Function", $file[$j]) )
		{
			//����ʸ�ε��Ҥ�ߤ��
			$helpFlg = 0;

			//�Ԥ�HTML�����Ѥ��Ѵ������ѿ��˳�Ǽ
			$file[$j] = fncConvertForHtml($file[$j]);
			$jsparam[main][] = $file[$j]."<br>";

			//��)�״ؿ��γ��ϰ��֤򸡺��ʰ����򤹤٤�ɽ�������ơ����Ԥ��뤿���
			if( Mb_EReg("\)", $file[$j]) )
			{
				//����
				$jsparam[main][] = "<br>";
			}
			else
			{
				$kansuuFlg = 1;
			}
			continue;
		}

		//�ؿ�������ʸ�ν��ϡʡ�function�ס�Public Function�פ������ޤ�³�ԡ�
		if( $helpFlg == 1 )
		{
			//�Ԥ�HTML�����Ѥ��Ѵ������ѿ��˳�Ǽ
			$file[$j] = fncConvertForHtml($file[$j]);
			$jsparam[main][] = $file[$j]."<br>";
			continue;
		}

		//�ե����������
		if( $j == ($fileLength - 1) )
		{
			//</div>�����ο������äƤʤ��ä���
			if($tsujitsumaFlg == 1)
			{
				//�ؿ��ν�λ�֥�å����ѿ��˵���
				$jsparam[main][] = "</div>";
				$tsujitsumaFlg = 0;
				continue;
			}
		}

	}
	//�֥�å��ν�����ɽ��
	$jsparam[main][] = "</div>";
}


//html�ؽ��Ϥ��뤿����Ѵ�
function fncConvertForHtml($file)
{
	//����ʸ�������ɤ�ʤ�
	$file = str_replace("\n","",$file);
	//���֤�&nbsp;���Ѵ�
	$file = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$file);
	//Ⱦ�ѥ��ڡ�����&nbsp;���Ѵ�
	$file = str_replace(" ","&nbsp;",$file);

	//��<�ץ������Ѵ�
	$file = str_replace("<","&lt;",$file);
	//��>�ץ������Ѵ�
	$file = str_replace(">","&gt;",$file);

	return $file;
}

HtmlTemplate::t_include("/home/kuwagata/src/jsdoc/jsdoc.html",$jsparam); 
//HtmlTemplate::t_include("C:/home/src/jsdoc.html",$jsparam); 

?>
