<?php

require_once ('conf.inc');

class makeHTML {
    public static function setSelectData($data) {
        $selectHTML = '';
        $selectHTML .= "<select>\n";
        $selectHTML .= self::setOptionsList($data);
        $selectHTML .= "</select>\n";
        return $selectHTML;
    }
    

    public static function getOptionsList($sheetNameList) {
        $optionHTML = '';
		foreach($sheetNameList as $key => $sheetName) {
			// シート名があればシート名を取得する
			if (strlen($sheetName)) {
				$sheetName = mb_convert_encoding($sheetName, "EUC-JP", "UTF-8");
				$optionHTML .= "<option value = " .$key. ">" .$sheetName. "</option>\n";
			} else {
				return false;
			}            
		}
        return $optionHTML;
	}

	// Excelの適用レートがマスターと異なるデータのテーブルを作成する
	public static function makeDifferenceRateTable($difference, $message) {
		$strTemporary = '';
		if ($difference) {
			if ($message) {
				$strTemporary .= "<div style = \"padding: 5px 0px 5px 0px\">&nbsp;&nbsp;". $message ."</div>\n";
			}
			$strTemporary .= "<div class = 'temporary'>\n";
			$strTemporary .= "<table class = \"temporaryTable\" align=\"center\">\n";
			$strTemporary .= "<tr>\n";
			$strTemporary .= "<th>項目</th>\n";
			$strTemporary .= "<th>KIDS設定値</th>\n";
			$strTemporary .= "<th>ブック設定値</th>\n";
			// $strTemporary .= "<th>納品日</th>\n";
			$strTemporary .= "</tr>\n";
	
			foreach($difference as $rateInfo) {
				$strTemporary .= "<tr>\n";
				$strTemporary .= "<td class='rateDiff'>".$rateInfo['monetary']."</td>\n";
				$strTemporary .= "<td class='rateDiff'>".$rateInfo['temporaryRate']."</td>\n";
				$strTemporary .= "<td class='rateDiff'>".$rateInfo['sheetRate']."</td>\n";
				// $strTemporary .= "<td class='rateDiff'>".$rateInfo['delivery']."</td>\n";		
				$strTemporary .= "</tr>\n";
			}
			$strTemporary .= "</table>\n";
			$strTemporary .= "</div>\n";
		}
		return $strTemporary;
	}

	// Excelの適用レート取得できなかったデータのテーブルを作成する
	public static function makeNotFoundRateTable($notFound, $message) {
		$strTemporary = '';
		if ($notFound) {
			if ($message) {
				$strTemporary .= "<div style = \"padding: 5px 0px 5px 0px\">&nbsp;&nbsp;". $message ."</div>\n";
			}
			$strTemporary .= "<div class = 'temporary'>\n";
			$strTemporary .= "<table class = \"temporaryTable\" align=\"center\">\n";
			$strTemporary .= "<tr>\n";
			$strTemporary .= "<th>通貨</th>\n";
			$strTemporary .= "<th>Excelレート</th>\n";
			$strTemporary .= "<th>納品日</th>\n";
			$strTemporary .= "</tr>\n";
	
			foreach($notFound as $rateInfo) {
				$strTemporary .= "<tr>\n";
				$strTemporary .= "<td class='rateDiff'>".$rateInfo['monetary']."</td>\n";
				$strTemporary .= "<td class='rateDiff'>".$rateInfo['sheetRate']."</td>\n";
				$strTemporary .= "<td class='rateDiff'>".$rateInfo['delivery']."</td>\n";		
				$strTemporary .= "</tr>\n";
			}
			$strTemporary .= "</table>\n";
			$strTemporary .= "</div>\n";
		}
		return $strTemporary;
	}


	public static function makeWarningHTML($warning = false) {
		$strWarning = '';
		if ($warning) {
			foreach($warning as $warningMessage) {
			$strWarning .= "<div>".$warningMessage."</div><br>";
			}
		}
		return $strWarning;
	}

	

	//@------------------------------------------------------------------------
	/**
	*	概要	: divタグ返却
	*
	*
	*	解説	: Handsontable表示用にid = grid＋シートNo.の<div>タグをセットする
	*
	*	@param	[$sheetNum]	: [Array]	表示するtable(sheet)数
	*
	*	@return	[$strHTML]	: [String]
	*/
	//-------------------------------------------------------------------------
    public static function getGridTable($sheetNum) {
        $gridHTML = "<div id=\"grid".$sheetNum."\" class=\"grid\"></div>\n";
        return $gridHTML;
	}
	
	//@------------------------------------------------------------------------
	/**
	*	概要	: ファイルHIDDEN要素返却
	*
	*
	*	解説	: 各ワークシート毎にHIDDENオブジェクトを生成、返却
	*
	*	対象	: 結果用テンプレート
	*
	*	@param	[$file]	: [Array]	. $_FILE より取得した値
	*
	*	@return	[$strHTML]	: [String]
	*/
	//-------------------------------------------------------------------------
	public static function getHiddenFileData( $file )
	{
		$aryHTML	= array();
		$strHTML	= "";

		$aryHTML[]	= "\t<input type=\"hidden\" name=\"exc_name\"			value=\"" .$file["exc_name"]. "\" >\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"exc_type\"			value=\"" .$file["exc_type"]. "\" >\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"exc_tmp_name\"		value=\"" .$file["exc_tmp_name"]. "\" >\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"exc_error\"			value=\"" .$file["exc_error"]. "\" >\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"exc_size\"			value=\"" .$file["exc_size"]. "\" >\n";

		$strHTML	= implode( "", $aryHTML );

		return $strHTML;
	}

	//@------------------------------------------------------------------------
	/**
	*	概要	: HIDDEN要素返却
	*
	*
	*	解説	: HIDDENオブジェクトを生成、返却
	*
	*	対象	: 結果用テンプレート
	*
	*	@param	[$aryData]	: [Array]	.  $_REQUESTより取得した値
	*
	*	@return	[$strHTML]	: [String]
	*/
	//-------------------------------------------------------------------------
	public static function getHiddenData( $aryData )
	{
		$aryHTML	= array();
		$strHTML	= "";

		foreach ($aryData as $key => $value) {
			$aryHTML[]	= "\t<input type=\"hidden\" name=\"".$key."\"			value=\"" .$value. "\" />\n";
		}

		$strHTML	= implode( "", $aryHTML );

		return $strHTML;
	}

	//@------------------------------------------------------------------------
	/**
	*	概要	: ExcelワークシートデータHTML返却
	*
	*
	*	解説	: ワークシートデータ表示 <table> HTML生成、返却
	*
	*
	*	@param	[$strWSName]	: [String]	. ワークシート名
	*	@param	[$ws_num]		: [Integer]	. 選択したExcelワークシート番号
	*	@param	[$strMode]		: [String]	. ワークシート選択・確認画面判定文字列
	*
	*	@return	[$strHTML]		: [String]
	*/
	//-------------------------------------------------------------------------
	public static function getWorkSheet2HTML($strWSName, $sn, $strMode)	{ 

		// ワークシート名取得
		$strHTML	.= "<a name=\"" .$sn. "\"></a>";
		$strHTML	.= "<br />\n";
		$strHTML	.= "<div class=\"worksheetTitleHeader\">";
		$strHTML	.= "<span class =\"worksheetTitle\">";
		$strHTML	.= "&nbsp;&nbsp;<b>Worksheet: \"";
		$strHTML	.= $strWSName;
		$strHTML	.= "\"</b></span>\n\n";

		// ファイル選択HTMLスクリプト
		$strHTML	.= self::getFileConfirmScript2HTML( $strWSName, $sn, $strMode );
		
		$strHTML	.= "</div>";	

        return $strHTML;
    }

	// 選択画面のヘッダ部のHtmlを生成する
    public static function getFileConfirmScript2HTML( $strWSName, $sn, $strMode )
	{
		$aryHTML	=	array();
		$strHTML	= "";

		$aryHTML[]	= "<span class=\"buttons\">\n";

		switch( $strMode )
		{
			// 選択画面
			case "select":
				$aryHTML[]	= "\t<button type=\"submit\" name=\"sheetname\" value=\"". $strWSName. "\">選択</button>";
				$aryHTML[]	= "\t<button type=\"button\" onclick=\"viewInvalidData(". $sn. ");\" data-column=\"0\" class=\"toggle\"> 情報表示 </button>&nbsp;&nbsp;&nbsp;\n";
				$aryHTML[]	= "\t<button type=\"button\" onclick=\"fncWindowClose();\"> 閉じる </button>&nbsp;&nbsp;&nbsp;\n";
				$aryHTML[]	= "\t<a id=\"excHref\" href=\"#\" onclick=\"scrollTop();\"><b>↑Page Top</b></a>&nbsp;&nbsp;\n";
				break;

			// 確認画面
			case "confirm":
				break;

			default:
				break;
		}

		$aryHTML[]	= "</span>\n\n\n";


		$strHTML	= implode( "", $aryHTML );

		unset( $aryHTML );
		return $strHTML;
	}


	//@------------------------------------------------------------------------
	/**
	*	概要	: 見積原価プレビューヘッダ部生成
	*
	*	解説	: ヘッダ部のHTML生成、返却
	*
	*
	*	@param	[$maxRevisionNo]		: [Integer]	. 最大のリビジョン番号
	*	@param	[$strMode]		        : [Integer]	. 現在のリビジョン番号
	*
	*	@return	[$strHTML]		: [String]
	*/
	//-------------------------------------------------------------------------
	public function getPreviewHeader($maxRevisionNo, $revisionNo = null, $minRevisionNo = 0) {
		if (!isset($revisionNo)) {
			$revisionNo = $maxRevisionNo;
		}
		$strHTML = "<div class = \"sheetPreviewHeader\" id = \"preview\">\n";
		$strHTML .= "<div class = \"data-buttons\">\n";
		for ($i = $maxRevisionNo; $i >= 0; --$i) {
			if ($i == $revisionNo) {
				$strHTML .= "<button type=\"button\" id=\"btnSelected\" class=\"btnSwitch\" value=\"". $i."\">\n";
				$strHTML .= "<img class= \"selected_button\" src=\"/img/type01/estimate/preview/data_selected_preview.gif\">\n";
				$strHTML .= "<p>データ".$i ."</p>";
				$strHTML .=	"</button>\n";
			} else {
				$strHTML .= "<button type=\"button\" class=\"btnSwitch\" value=\"". $i."\">\n";
				$strHTML .= "<img class= \"switch_button\" src=\"/img/type01/estimate/preview/data_others_preview.gif\">\n";
				$strHTML .= "<p>データ".$i ."</p>";
				$strHTML .=	"</button>\n";
			}
		}
		$strHTML .=	"</div>\n";
		
		$strHTML .= "<div class = \"action-buttons\">\n";

		// URLコピー
		$strHTML .= "<button type=\"button\" id=\"url_copy\" onclick=\"urlCopy();\">\n";
		$strHTML .= "<img class= \"url_copy_button\" src=\"/img/type01/estimate/preview/url_copy.gif\">\n";
		$strHTML .=	"</button>\n";

		// ダウンロード
		$strHTML .= "<button type=\"button\" id=\"download\" onclick=\"fileDownload();\">\n";
		$strHTML .= "<img class= \"download_button\" src=\"/img/type01/estimate/preview/download.gif\">\n";
		$strHTML .=	"</button>\n";

		// 印刷(共通の帳票印刷が最新版しか印刷できないので最新リビジョンの時のみ表示)
		if ($revisionNo === $maxRevisionNo) {
			$strHTML .= "<button type=\"button\" id=\"print\" onclick=\"sheetPrint();\">\n";
			$strHTML .= "<img class= \"print_button\" src=\"/img/type01/estimate/preview/print.gif\">\n";
			$strHTML .=	"</button>\n";
		}

		// 編集(最新リビジョンかつ削除済みでない場合のみ編集可能)
		if ($revisionNo === $maxRevisionNo && $minRevisionNo >= 0) {
			$strHTML .= "<button type=\"button\" id=\"edit\" onclick=\"editModeTransition();\">\n";
			$strHTML .= "<img class= \"edit_button\" src=\"/img/type01/estimate/preview/edit.gif\">\n";
			$strHTML .=	"</button>\n";
		}		
		
		$strHTML .=	"</div>\n";
		$strHTML .=	"</div>\n";
		return $strHTML;
	}

	// 編集モードのヘッダ生成Html
	public function getEditHeader($maxRevisionNo, $revisionNo = null) {
		if (!isset($revisionNo)) {
			$revisionNo = $maxRevisionNo;
		}
		$strHTML = "<div class = \"sheetEditHeader\" id = \"edit\">\n";
		$strHTML .= "<div class = \"data-buttons\">\n";
		for ($i = $maxRevisionNo; $i >= 0; --$i) {
			if ($i == $revisionNo) {
				$strHTML .= "<button type=\"button\" id=\"btnSelected\" class=\"btnSwitch\">\n";
				$strHTML .= "<img class= \"selected_button\" src=\"/img/type01/estimate/preview/data_selected_preview.gif\">\n";
				$strHTML .= "<p>データ".$i ."</p>";
				$strHTML .=	"</button>\n";
			} else {
				$strHTML .= "<button type=\"button\" class=\"btnSwitch\">\n";
				$strHTML .= "<img class= \"switch_button\" src=\"/img/type01/estimate/preview/data_others_preview.gif\">\n";
				$strHTML .= "<p>データ".$i ."</p>";
				$strHTML .=	"</button>\n";
			}
		}
		$strHTML .=	"</div>\n";
		
		$strHTML .= "<div class = \"action-buttons\">\n";

		$strHTML .= "<button type=\"button\" id=\"cancel_edit\" onclick=\"cancelEdit();\">\n";
		$strHTML .= "<img class= \" cancel_edit_button\" src=\"/img/type01/estimate/preview/cancel.gif\">\n";
		$strHTML .=	"</button>\n";

		$strHTML .= "<button type=\"button\" id=\"update_regist\">\n";
		$strHTML .= "<img class= \"update_regist_button\" src=\"/img/type01/estimate/preview/regist.gif\">\n";
		$strHTML .=	"</button>\n";
		
		$strHTML .=	"</div>\n";
		$strHTML .=	"</div>\n";
		return $strHTML;
	}

	public static function outputErrorWindow($strErrorMessage) {
		// 検索でエラーが発生したらエラーメッセージを出力する
		if (is_array($strErrorMessage) && count($strErrorMessage)) {
			$strMessage = '';
			foreach($strErrorMessage as $message) {
				$strMessage .= "<div style=\"margin:5px;\">". $message. "</div>";
			}
		} else {
			$strMessage = "<div style=\"margin:5px;\">". $strErrorMessage. "</div>";
		}		

		// [strErrorMessage]書き出し
		$aryHtml["strErrorMessage"] = $strMessage;

		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "/result/error/parts.tmpl" );
		
		// テンプレート生成
		$objTemplate->replace( $aryHtml );
		$objTemplate->complete();

		// HTML出力
		echo $objTemplate->strTemplate;

		exit;
	}
}
