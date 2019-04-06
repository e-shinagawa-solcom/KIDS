<!--

/************************************************************/
var g_SelectColor  = '#bbbbbb'; // 選択したときの色
var g_DefaultColor_odd = '#F0F0F6';
var g_DefaultColor_even = '#FFF';

/************************************************************/

////////// TR選択行の背景色変更関数 //////////
(function(){
	$('tr.odd').on('click', function(){
		var $cur_tr = $(this)[0];
		var $cur_tds = $cur_tr.children;
		
		if ($cur_tds[0].style.backgroundColor != "") {
			ToDefault($cur_tr);
			return true;
		}
		
		ToDefault($cur_tr);
		
		for (var i = 0; i < $cur_tds.length; i++){
			if ($cur_tds[i].style.backgroundColor  == "") {
				$cur_tds[i].style.backgroundColor  = g_SelectColor;
			}
			else {
				$cur_tds[i].style.backgroundColor  = "";
			}
		}
	});
	
	$('tr.even').on('click', function(){
		var $cur_tr = $(this)[0];
		var $cur_tds = $cur_tr.children;
		
		if ($cur_tds[0].style.backgroundColor != "") {
			ToDefault($cur_tr);
			return true;
		}
		
		ToDefault($cur_tr);
		
		for (var i = 0; i < $cur_tds.length; i++){
			if ($cur_tds[i].style.backgroundColor  == "") {
				$cur_tds[i].style.backgroundColor  = g_SelectColor;
			}
			else {
				$cur_tds[i].style.backgroundColor  = "";
			}
		}
	});
})();

function ToDefault($target_tr) {
	var $parent_table = $target_tr.parentNode;
	var $trs = $parent_table.children;
	
	for (var i = 0; i < $trs.length; i++){
		var $cur_tds = $trs[i].children;
		
		if ($cur_tds[0].style.backgroundColor != "") {
			for (var j = 0; j < $cur_tds.length; j++){
				$cur_tds[j].style.backgroundColor  = "";
			}
			break;
		}
	}
}

//-->