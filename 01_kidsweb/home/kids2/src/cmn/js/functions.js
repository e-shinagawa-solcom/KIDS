
//---------------------------------------------------
//テーブル表示用日付成型関数
//---------------------------------------------------
function convertDate(str){
	if(str != "" && str != undefined && str !="null"){
		if (str.length == 8) {
			var year = str.substring(2, 4);
			var month = str.substring(4, 6);
			var day = str.substring(6, 8);
			return year + "/" + month + "/" + day;
		} else {
			var date = new Date( str );
			var year  = String(date.getFullYear()).substring(2, 4); //年
			var month = ("0" + (date.getMonth() + 1)).slice(-2);    //月
			var day   = ("0"+date.getDate()).slice(-2);     //日
			return year + "/" + month + "/" + day;
		}
	} else {
		return "";
	}
}