

//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 締め処理サブミット関数
*
* 対象 : 締め処理
*
* @param [objFrm]  : [オブジェクト型] . フォーム名
* @param [objFrom] : [オブジェクト型] . 入力フィールド名Ａ
* @param [objTo]   : [オブジェクト型] . 入力フィールド名Ｂ
*
* @event [onclick] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncClosedSubmit( objFrm , objFrom , objTo )
{
	if( !( objFrom.value == '' || objTo.value == '' ) )
	{
		objFrm.submit();
	}
	return false;
}


$(document).ready(function () {

    // 開始日時フォーカスを失ったときの処理
    $("input[name='dtmUpdateFrom'], input[name='dtmUpdateTo']").on('blur', function () {
        var value = $(this).val();
        if (/^[0-9]{6}$/.test(value)) {
            var str = value.trim();
            var y = str.substr(0, 4);
            var m = str.substr(4, 2);
            value = y + "/" + m;
        }
        if (/^[0-9]{5}$/.test(value)) {
            var str = value.trim();
            var y = str.substr(0, 4);
            var m = str.substr(4, 1);
            value = y + "/0" + m;
		}
		
		$(this).val(value);
		
		if ($(this).attr("name") == 'dtmUpdateFrom')
		{
			$("input[name='dtmUpdateTo']").val(value);
		}
		

		fncCheckDate(this,1);
    });

    // 開始日時フォーカスを取ったときの処理
    $("input[name='dtmUpdateFrom'], input[name='dtmUpdateTo']").on('focus', function () {
        var chgVal = $(this).val().replace(/\//g, "");
        $(this).val(chgVal);
        $(this).select();
    });

});