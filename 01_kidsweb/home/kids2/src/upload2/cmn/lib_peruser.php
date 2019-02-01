<?php

	// Peruserの問題点
	//
	// ■Excel2003
	// addslashes() を無効にすると
	// ￥マークがエスケープ文字となり、数値前にある円マークが作用してしまう。
	//　htmlentities() を有効にしても効果無し
	// ■Excel2010
	// addslashes() を有効にしても効果無し。
	// htmlentities() を有効にすると、その後の数値が全て消える。
	//
	function outStr($str)
	{
		$charset='utf-8';
		$str = addslashes($str);
//		$str = htmlentities($str, ENT_QUOTES, $charset);
		$str = str_replace('&conint;',mb_convert_encoding("∮", $charset, "utf-8"),$str);
		$str = str_replace('&ang90;',mb_convert_encoding("∟", $charset, "utf-8"),$str);
		$str = str_replace('&becaus;',mb_convert_encoding("∵", $charset, "utf-8"),$str);
		return $str;
	}


	function fncExcelCss($obj)
	{
		$css=$obj->makecss();
		
		$strCss = '
		<style type="text/css">
		<!--
		body,td,th {
			font-size: normal;
			color: black;
			text-align:left;
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
		}'."\n".$css.'
		-->
		</style>'."\n";
		
		return $strCss;
	}

?>