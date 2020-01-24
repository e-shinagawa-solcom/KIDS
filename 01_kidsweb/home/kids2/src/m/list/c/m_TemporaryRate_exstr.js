/************************* [ 想定レートマスタ ] *************************/
$(function(){
    var searchMaster = {
                    url: '/cmn/querydata.php?strSessionID=' + $.cookie('strSessionID'),
                    type: 'post',
                    dataType: 'json'
                };
    $('select[name="lngmonetaryunitcode"]').change(function(){
		var val = $('select[name="lngmonetaryunitcode"] option:selected').val();
		var text = $('select[name="lngmonetaryunitcode"] option:selected').text();
		var condition = {
			data: {
				QueryName: 'selectMonetaryRate',
				Conditions: {
					lngmonetaryunitcode: val
				}
			}
		};
		$.ajax($.extend({}, searchMaster, condition))
		.done(function(response){			
			var tblheard = $('th[id="monetaryrateTbl_heard"]');
			tblheard.text(text);
			var tblTbody = document.getElementById('monetaryrateTbl_tbody');
			$.each(response, function(i, elm) {
				tblTbody.rows[i].cells[0].innerText = elm.dtmapplystartdate;
				tblTbody.rows[i].cells[1].innerText = elm.dtmapplyenddate;
				tblTbody.rows[i].cells[2].innerText = elm.curconversionrate;
			});
		})
		.fail(function(response){			
			alert(response.responseText);		
			console.log(response.responseText);
		});
	});
});


function applyMonetaryRate(objID) {
	var elements = document.getElementsByTagName("input") ;
	elements.namedItem( "dtmapplystartdate" ).value = objID.cells[0].innerHTML;	
	elements.namedItem( "dtmapplyenddate" ).value = objID.cells[1].innerHTML;	
	elements.namedItem( "curconversionrate" ).value = objID.cells[2].innerHTML;	
}


//////////////////////////////////////////////////////////////////
////////// [修正][追加]時・オブジェクトのオンロード処理関数 //////////
function fncEditObjectOnload()
{
	// オブジェクトの自動レイアウト
	fncInitLayoutObjectModule( objColumn , objInput  , 60 , 216 );

	// [追加]
	if( g_strMode == 'add' )
	{
		// オブジェクトのID変換
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2', 'Input3' ) ,
								 Array( 'Txt15L' , 'Txt10L' , 'Txt10L', 'Txt10L' ) );
	}
	// [修正]
	else if( g_strMode == 'fix' )
	{
		// オブジェクトのID変換
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2', 'Input3' ) ,
								 Array( 'Txt15L' , 'Txt10L' , 'TxtDis10L', 'Txt10L' ) );
	}

	// オブジェクトの名の設定
	setObjectName();

	return true;
}




//////////////////////////////////////////////////////////////////
////////// [CONFIRM]時・オブジェクトのオンロード処理関数 //////////
function fncConfirmObjectOnload( strMode )
{

	// 処理モードの初期化
	g_strMode = '';

	// 処理モードの取得
	g_strMode = strMode;


	// オブジェクトの名の設定
	setObjectName();


	return true;
}






//////////////////////////////////////////////////////////////////
////////// 追加ボタンの表示 //////////
if( typeof(window.top.MasterAddBt) != 'undefined' )
{
	window.top.MasterAddBt.style.visibility = 'visible';
}







//////////////////////////////////////////////////////////////////
////////// ヘッダーイメージの生成 //////////
var headerAJ = '<img src="' + h_trateJ + '" width="949" height="30" border="0" alt="想定レートマスタ">';







//////////////////////////////////////////////////////////////////
////////// オブジェクト名設定 //////////
function setObjectName()
{
	// クエリーテーブルＴＯＰ座標
	if( typeof(QueryTable) != 'undefined' )
	{
		QueryTable.style.top = 300;
	}

    // ヘッダーイメージ書き出し
	if( typeof(window.top.SegAHeader) != 'undefined' )
	{
        window.top.SegAHeader.innerHTML = headerAJ;
    }


    // クエリーボタンテーブル書き出し
    if( typeof(fncTitleOutput) != 'undefined' )
    {
        fncTitleOutput( 1 );
    }

    // 追加ボタンイメージ書き出し
    if( typeof(window.top.MasterAddBt) != 'undefined' )
    {
        window.top.MasterAddBt.innerHTML = maddbtJ1;
    }

	return false;

}
