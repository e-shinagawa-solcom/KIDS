<!--
//�Х��ȿ��η׻�
function fncGetByte( str )
{
	if (str=="" || !str || str==null) return 0;
	str = fncTrashGomi( str );
	var strS=str.replace(/[^0-9a-z��-��\!\"\#\$\%\&\'\(\)\-\=\^\~\\\|\@\`\[\{\;\+\:\*\]\}\,\<\.\>\/\?\_]/ig,"##");
	return strS.length;
}

//���ߤ���
function fncTrashGomi( str )
{
	str = unescape( escape(str).split("%00")[0] );
	return str;
}



// html -> plain text
function fncEditConvertToText( htmldata )
{
	var text = htmldata.replace( /<div>&nbsp;<\/div>/ig, "" );
	text = text.replace( /<([a-z]+)([^>]*)>/ig, "" );
	text = text.replace( /<\/([a-z]+)>/ig, "" );
	text = text.replace( /^&nbsp;\r\n/ig, "\r\n" );
	text = text.replace( /&nbsp;/ig, " " );
	text = text.replace( /&lt;/ig, "<");
	text = text.replace( /&gt;/ig, ">");
	text = text.replace( /&quot;/ig, "\"");
	text = text.replace( /&amp;/ig, "&");

	if( text == " " )
	{
		text = "";
	}
	else if( text.length > 0 && text.charAt(text.length-1) == ' ' )
	{
		if( htmldata.length > 17 && htmldata.substr(htmldata.length-17) == "<div>&nbsp;</div>" )
			text = text.substring( 0, text.length-1 );
	}

	return text;
}

// plain text -> html
function fncEditConvertToHtml( textdata )
{
	if( ! textdata || textdata.length < 1 )
		return "";

	var text = textdata.replace( /&/g, "&amp;" );
	text = text.replace( /</g, "&lt;" );
	text = text.replace( />/g, "&gt;" );
	text = text.replace( /"/g, "&quot;" );
	text = text.replace( / /g, "&nbsp;" );
	text = text.replace( /\r\n/g, "</div>\r\n<div>" );
	text = text.replace( /<div><\/div>/ig, "<div>&nbsp;</div>" );

	text = "<div>"+text+"</div>";

	text = text.replace( /<div><\/div>/ig, "<div>&nbsp;</div>" );

	return text;
}


function fncEditGetEditArea()
{
	return document.getElementById( "htmltext" );
}

//�ɥ�åץ�˥塼�����
function fncEditGetMenuElement( menuID )
{
	return document.getElementById("drop" + menuID);
}

//���ǥåȥ�����������
function fncEditGetMenuButton( id )
{
	return document.getElementById("btn" + menuID);
}


//��˥塼���Ĥ���
function fncEditHideMenu( menuID )
{
	var elm = fncEditGetMenuElement( menuID );

	elm.style.display = "none";

	return true;
}


//��˥塼�򳫤�
function fncEditShowMenu( menuID )
{
	//��˥塼���Ĥ���
	fncEditHideMenu( menuID );

	var elm = fncEditGetMenuElement( menuID );

	elm.style.display = "block";

	elm.focus();

	return true;
}


//���ǥåȥ�������ξ�˥ޥ����������Ȥ��ν���
function fncEditBtnOver(btn)
{
	btn.style.border="1px outset";
	btn.style.borderLeftColor="#ffffff";
	btn.style.borderTopColor="#ffffff";
}


//���ǥåȥ������󤫤�ޥ��������Ȥ����Ȥ��ν���
function fncEditBtnOut(btn,id)
{
	//��˥塼��������Ƥ��ʤ��Ȥ����ѹ�
	btn.style.border="1px solid #f1f1f1";
}


//��˥塼�ι��ܤ˥ޥ��������С������Ȥ�
function fncEditMenuItemOver(style)
{
	style.background = "#000099";
	style.color = "#FFFFFF";
}


//��˥塼�ι��ܤ���ޥ��������Ȥ����Ȥ�
function fncEditMenuItemOut(style)
{
	style.background = "#FFFFFF";
	style.color = "#000000";
}


//�����ΰ褬���뤫�ɤ���������å�
function fncEditIsEditableSelection()
{
	//�����ƥ��֤����򤬤���н���
	if( document.selection )
	{
		//selection���֥������ȤˤĤ��Ʋ���������Ԥ����ˤϡ�
		//selection���֥������Ȥ�createRange�᥽�åɤ�Ȥä�TextRange���֥������Ȥκ�����ɬ��
		var range = document.selection.createRange();

		//���֥������Ȥ�����н���
		if( range )
		{
			//���ꤷ��TextRange���֥������Ȥοƥ�����Ȥ��ɤ߽Ф���
			//�ƥ�����ȤϤ���TextRange���֥������Ȥ�������ޤ��롣
			var rp = range.parentElement();

			return rp && fncEditGetEditArea().contains(rp);
		}
	}

	return false;
}


//�ե���ȥ��������ѹ�
function fncEditSetFontSize( fn )
{
	if( fncEditIsEditableSelection() )
	{
		document.execCommand( "FontSize", false, fn );
	}
}


//���顼���ѹ�
function fncEditSetColor( id, c )
{
	if( c == null || c.length < 1 )
		return ;

	if( fncEditIsEditableSelection() )
	{
		if( id == 1 )
		{
			if( c == -1 )
				document.execCommand( "ForeColor", false, null );
			else
				document.execCommand( "ForeColor", false, c );
		}
		else if( id == 2 )
		{
			if( c == -1 )
				document.execCommand( "BackColor", false, null );
			else
				document.execCommand( "BackColor", false, c );
		}
	}

	fncEditHideMenu( id );
}


//�Խ����ޥ�ɤ�¹�
function fncEditDoCommand( cmd )
{
	//������������ϡ����顼�ˤʤ뤿����������ǡ�
	if( document.selection.type == "Control" ) return false;

	if( fncEditIsEditableSelection() )
	{
		if( cmd == "CreateLink" ) {
			var r = document.selection.createRange();

			if( ! r.text ) 
			{
				window.alert("�ϥ��ѡ���󥯤��������ʸ��������򤷤Ƥ���������");
				return ;
			}
		}
		document.execCommand( cmd );
	}
}


//���ǥåȲ��̤�ɽ����Ʋ��̤�ȿ��
function fncEditHtmltextToParent()
{
	hyoujiHTML = document.all.htmltext.innerHTML;


	var strBuffHidden	= "";

	//�ʤˤ�ɽ�����Ƥʤ��ä��顢ɽ������ˤ���
	if( hyoujiHTML == "<DIV>&nbsp;</DIV>" )
	{
		hyoujiHTML = "";
	}

	hyoujiHTML = hyoujiHTML.replace( /<div>&nbsp;<\/div>/ig, "<br>" );

//alert(hyoujiHTML);


	//	IMG�����Υե�����̾��������
	var urls = getImageUrls(document.all.htmltext);
	
	//	hidden���ǤˤĤ���PHP����������̾
	var PHP_ARRAY_NAME = "uploadimages[]";
	
	for( var i=0; i<urls.length; i++ ){
		//	hidden�Ρ��ɤ��ɲä���
		//alert(urls[i]);
		strBuffHidden += '<input id="'+PHP_ARRAY_NAME+'" name="'+PHP_ARRAY_NAME+'" type="hidden" value="'+urls[i]+'" />';
	}

	// hidden�Ρ��ɤ�����
	parent.document.all.EditorRecord.innerHTML = strBuffHidden;


//alert(parent.document.all.EditorRecord.innerHTML);



	//���Ϥ��줿�Х��ȿ�������å�
	byteSuu = fncGetByte( hyoujiHTML );

	if( byteSuu < 10000 )
	{
		parent.PPP2.strSpecificationDetails.value = hyoujiHTML;

		parent.fncEditer( parent.EditFrame );
	}
	else
	{
		alert("10000�Х��Ȥ���¤Ǥ���\n" +
			"���ߤΥХ��ȿ� : " + byteSuu );
	}
	
}


//�Ʋ��̤Υ������򥨥ǥåȲ��̤�ɽ��
function fncEditParentToHtmltext()
{
	hyoujiHTML = parent.PPP2.strSpecificationDetails.value;

	document.all.htmltext.innerHTML = hyoujiHTML;
}


//���ǥåȲ��̤������˲���������
function fncEditGazou( imgsrc )
{
	oldHtmltext = document.all.htmltext.innerHTML;
	gazouSrc = '<a href="' + imgsrc + '" target="_blank"><img src="' + imgsrc + '" border="0" /></a>';
	document.all.htmltext.innerHTML = oldHtmltext + gazouSrc;
	/*
	oldHtmltext = document.all.htmltext.innerHTML;

	gazouSrc = "<img src=\"" + document.all.gazou.value + "\">";

	document.all.htmltext.innerHTML = oldHtmltext + gazouSrc;
	*/
}

/* �������åץ����ѥ�����ץ� */

//	getElementById�λ��ȥ᥽�å�
function $(id){
	return document.getElementById(id);
}

//	�������åץ����Ѥ�iFrame����
//	�ե�����˥������åȤȥ�����������ꤹ��
function createIframeForImageLoad(){

	var iframename		= "imageloadFrame";

	$("imgLoadIframeContainer").innerHTML = '<iframe id="'+iframename+'" name="'+iframename+'" style="width:0;height:0;border:0;" src="dummy.html"></iframe>';

	$("form-uploadimage").target = iframename;
}

function checkAndSubmitImgUp(){
	
	if( $('userfile').value != "" )
	{
		var strTempImageDir	= ( parent.document.all.strTempImageDir != undefined ) ? parent.document.all.strTempImageDir.value : "";
		var uploadaction	= "upload.php?strTempImageDir=" + strTempImageDir;

		$("form-uploadimage").action = uploadaction;
		$('form-uploadimage').submit();
	}
	else
	{
		//	alert execute..
	}
}

//	������ʸ���󤫤�img��������Υե�����̾��������ˤ����֤�
function getImageUrls(txt) {

	var filenames = new Array();

	var imgnodes = txt.getElementsByTagName("img");
	for( var i=0; i<imgnodes.length; i++ ){
		var imgnode = imgnodes[i];
		var filename = imgnode.src.split("/").pop();
		if( !isContains(filenames,filename) ){
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

//	��������Ʊ�����Ǥ����뤫Ĵ�٤�
function isContains(arr,element){
	for( var i=0; i<arr.length; i++ ){
		if( arr[i] == element ) return true;
	}
	return false;
}


//-->