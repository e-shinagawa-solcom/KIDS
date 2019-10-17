//バイト数の計算
function fncGetByte(str) {
	if (str == "" || !str || str == null) return 0;
	str = fncTrashGomi(str);
	var strS = str.replace(/[^0-9a-zｱ-ﾝ\!\"\#\$\%\&\'\(\)\-\=\^\~\\\|\@\`\[\{\;\+\:\*\]\}\,\<\.\>\/\?\_]/ig, "##");
	return strS.length;
}

//ごみを取る
function fncTrashGomi(str) {
	str = unescape(escape(str).split("%00")[0]);
	return str;
}



// html -> plain text
function fncEditConvertToText(htmldata) {
	var text = htmldata.replace(/<div>&nbsp;<\/div>/ig, "");
	text = text.replace(/<([a-z]+)([^>]*)>/ig, "");
	text = text.replace(/<\/([a-z]+)>/ig, "");
	text = text.replace(/^&nbsp;\r\n/ig, "\r\n");
	text = text.replace(/&nbsp;/ig, " ");
	text = text.replace(/&lt;/ig, "<");
	text = text.replace(/&gt;/ig, ">");
	text = text.replace(/&quot;/ig, "\"");
	text = text.replace(/&amp;/ig, "&");

	if (text == " ") {
		text = "";
	}
	else if (text.length > 0 && text.charAt(text.length - 1) == ' ') {
		if (htmldata.length > 17 && htmldata.substr(htmldata.length - 17) == "<div>&nbsp;</div>")
			text = text.substring(0, text.length - 1);
	}

	return text;
}

// plain text -> html
function fncEditConvertToHtml(textdata) {
	if (!textdata || textdata.length < 1)
		return "";

	var text = textdata.replace(/&/g, "&amp;");
	text = text.replace(/</g, "&lt;");
	text = text.replace(/>/g, "&gt;");
	text = text.replace(/"/g, "&quot;");
	text = text.replace(/ /g, "&nbsp;");
	text = text.replace(/\r\n/g, "</div>\r\n<div>");
	text = text.replace(/<div><\/div>/ig, "<div>&nbsp;</div>");

	text = "<div>" + text + "</div>";

	text = text.replace(/<div><\/div>/ig, "<div>&nbsp;</div>");

	return text;
}


function fncEditGetEditArea() {
	return document.getElementById("htmltext");
}

//ドロップメニューを指定
function fncEditGetMenuElement(menuID) {
	return document.getElementById("drop" + menuID);
}

//エデットアイコンを指定
function fncEditGetMenuButton(id) {
	return document.getElementById("btn" + menuID);
}


//メニューを閉じる
function fncEditHideMenu(menuID) {
	var elm = fncEditGetMenuElement(menuID);

	elm.style.display = "none";

	return true;
}

//すべてのメニューを閉じる
function fncEditHideAllMenu() {
	document.getElementById("drop0").style.display = "none";
	document.getElementById("drop1").style.display = "none";
	document.getElementById("drop2").style.display = "none";

	return true;
}

//メニューを開く
function fncEditShowMenu(menuID) {
	//メニューを閉じる
	fncEditHideMenu(menuID);

	var elm = fncEditGetMenuElement(menuID);

	elm.style.display = "block";

	return true;
}

//エデットアイコンの上にマウスがきたときの処理
function fncEditBtnOver(btn) {
	btn.style.border = "1px outset";
	btn.style.borderLeftColor = "#ffffff";
	btn.style.borderTopColor = "#ffffff";
}


//エデットアイコンからマウスアウトしたときの処理
function fncEditBtnOut(btn, id) {
	//メニューが開かれていないときに変更
	btn.style.border = "1px solid #f1f1f1";
}


//メニューの項目にマウスオーバーしたとき
function fncEditMenuItemOver(style) {
	style.background = "#000099";
	style.color = "#FFFFFF";
}


//メニューの項目からマウスアウトしたとき
function fncEditMenuItemOut(style) {
	style.background = "#FFFFFF";
	style.color = "#000000";
}


//選択領域があるかどうかをチェック
function fncEditIsEditableSelection() {

	var parentEl = null, sel;
	if (window.getSelection) {
		sel = window.getSelection();
		if (sel.rangeCount) {
			parentEl = sel.getRangeAt(0).commonAncestorContainer;
			if (parentEl.nodeType != 1) {
				parentEl = parentEl.parentNode;
			}
		}
	} else if ((sel = document.selection) && sel.type != "Control") {
		parentEl = sel.createRange().parentElement();
	}
	return parentEl;
}


//フォントサイズを変更
function fncEditSetFontSize(fn) {
	if (fncEditIsEditableSelection()) {
		document.execCommand("FontSize", false, fn);
	}
}

//カラーの変更
function fncEditSetColor(id, c) {
	if (c == null || c.length < 1)
		return;

	if (fncEditIsEditableSelection()) {
		if (id == 1) {
			if (c == -1)
				document.execCommand("ForeColor", false, null);
			else
				document.execCommand("ForeColor", false, c);
		}
		else if (id == 2) {
			if (c == -1)
				document.execCommand("BackColor", false, null);
			else
				document.execCommand("BackColor", false, c);
		}
	}

	fncEditHideMenu(id);
}


//編集コマンドを実行
function fncEditDoCommand(cmd) {
	//画像を選択中は、エラーになるため処理を中断。
	if (document.getSelection().type == "Control") return false;

	if (fncEditIsEditableSelection()) {
		if (cmd == "CreateLink") {
			var r = window.getSelection().getRangeAt(0);

			if (!r.text) {
				window.alert("ハイパーリンクを作成する文字列を選択してください。");
				return;
			}
		}
		document.execCommand(cmd);
	}
}


//エデット画面の表示を親画面に反映
function fncEditHtmltextToParent() {
	hyoujiHTML = document.all.htmltext.innerHTML;


	var strBuffHidden = "";

	//なにも表示してなかったら、表示を空にする
	if (hyoujiHTML == "<DIV>&nbsp;</DIV>") {
		hyoujiHTML = "";
	}

	hyoujiHTML = hyoujiHTML.replace(/<div>&nbsp;<\/div>/ig, "<br>");

	//alert(hyoujiHTML);


	//	IMGタグのファイル名配列を取得
	var urls = getImageUrls(document.all.htmltext);

	//	hidden要素につけるPHP参照用配列名
	var PHP_ARRAY_NAME = "uploadimages[]";

	for (var i = 0; i < urls.length; i++) {
		//	hiddenノードを追加する
		//alert(urls[i]);
		strBuffHidden += '<input id="' + PHP_ARRAY_NAME + '" name="' + PHP_ARRAY_NAME + '" type="hidden" value="' + urls[i] + '" />';
	}

	// hiddenノードを設定
	parent.document.all.EditorRecord.innerHTML = strBuffHidden;


	//alert(parent.document.all.EditorRecord.innerHTML);



	//入力されたバイト数をチェック
	byteSuu = fncGetByte(hyoujiHTML);

	if (byteSuu < 10000) {
		parent.PPP.strSpecificationDetails.value = hyoujiHTML;

		parent.EditFrame.style.display = 'none';
	}
	else {
		alert("10000バイトが上限です。\n" +
			"現在のバイト数 : " + byteSuu);
	}

}


//親画面のソースをエデット画面に表示
function fncEditParentToHtmltext() {
	hyoujiHTML = parent.PPP.strSpecificationDetails.value;

	document.all.htmltext.innerHTML = hyoujiHTML;
}


//エデット画面の末尾に画像を挿入
function fncEditGazou(imgsrc) {
	oldHtmltext = document.all.htmltext.innerHTML;
	gazouSrc = '<a href="' + imgsrc + '" target="_blank"><img src="' + imgsrc + '" border="0" /></a>';
	document.all.htmltext.innerHTML = oldHtmltext + gazouSrc;
	/*
	oldHtmltext = document.all.htmltext.innerHTML;

	gazouSrc = "<img src=\"" + document.all.gazou.value + "\">";

	document.all.htmltext.innerHTML = oldHtmltext + gazouSrc;
	*/
}

/* 画像アップロード用スクリプト */

//	getElementByIdの参照メソッド
function $(id) {
	return document.getElementById(id);
}

//	画像アップロード用のiFrame生成
//	フォームにターゲットとアクションを指定する
function createIframeForImageLoad() {

	var iframename = "imageloadFrame";

	$("imgLoadIframeContainer").innerHTML = '<iframe id="' + iframename + '" name="' + iframename + '" style="width:0;height:0;border:0;" src="dummy.html"></iframe>';

	$("form-uploadimage").target = iframename;
}

function checkAndSubmitImgUp() {

	if ($('userfile').value != "") {
		var strTempImageDir = (parent.document.all.strTempImageDir != undefined) ? parent.document.all.strTempImageDir.value : "";
		var uploadaction = "upload.php?strTempImageDir=" + strTempImageDir;

		$("form-uploadimage").action = uploadaction;
		$('form-uploadimage').submit();
	}
	else {
		//	alert execute..
	}
}

//	引数の文字列からimgタグの中のファイル名だけ配列にして返す
function getImageUrls(txt) {

	var filenames = new Array();

	var imgnodes = txt.getElementsByTagName("img");
	for (var i = 0; i < imgnodes.length; i++) {
		var imgnode = imgnodes[i];
		var filename = imgnode.src.split("/").pop();
		if (!isContains(filenames, filename)) {
			filenames.push(filename);
		}
	}

	/*
	for( var i=0; i<txt.childNodes.length; i++ ){
		if(txt.childNodes[i].nodeName == 'IMG'){
			var imgnode = txt.childNodes[i];
			var filename = imgnode.src.split("/").pop();
			if( !isContains(filenames,filename) ){
				filenames.push(filename);
			}
			
		}
	}
	*/
	return filenames;

	/*
	var urls = new Array();
	var data = new Array();
	data = txt.match(/<img+.*?>+/ig);
	
	for( var i=0; i<data.length; i++ ){
		var v = /src=\"(.*)?\"/.exec(data[i]);
		var filename = v[1].split("/").pop();
		if( filename != undefined && !isContains(urls,filename) ){
			urls.push(filename);
		}
	}
	return urls;
	*/
}

//	配列の中に同じ要素があるか調べる
function isContains(arr, element) {
	for (var i = 0; i < arr.length; i++) {
		if (arr[i] == element) return true;
	}
	return false;
}


//-->