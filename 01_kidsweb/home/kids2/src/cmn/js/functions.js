
//---------------------------------------------------
//テーブル表示用日付成型関数
//---------------------------------------------------
function convertDate(str, type='yyyy/mm/dd') {
	if (type == 'yy/mm/dd') {
		if (str != "" && str != undefined && str != "null") {
			if (str.length == 8) {
				var year = str.substring(2, 4);
				var month = str.substring(4, 6);
				var day = str.substring(6, 8);
				return year + "/" + month + "/" + day;
			} else {
				var date = new Date(str);
				var year = String(date.getFullYear()).substring(2, 4); //年
				var month = ("0" + (date.getMonth() + 1)).slice(-2);    //月
				var day = ("0" + date.getDate()).slice(-2);     //日
				return year + "/" + month + "/" + day;
			}
		} else {
			return "";
		}
	} else {
		if (str != "" && str != undefined && str != "null") {
			if (str.length == 8) {
				var year = str.substring(0, 4);
				var month = str.substring(4, 6);
				var day = str.substring(6, 8);
				return year + "/" + month + "/" + day;
			} else {
				var date = new Date(str);
				var year = String(date.getFullYear()).substring(0, 4); //年
				var month = ("0" + (date.getMonth() + 1)).slice(-2);    //月
				var day = ("0" + date.getDate()).slice(-2);     //日
				return year + "/" + month + "/" + day;
			}
		} else {
			return "";
		}

	}
}

function money_format(lngmonetaryunitcode, strmonetaryunitsign, price, type) {
	if (lngmonetaryunitcode == 1) {
		if (type == 'unitprice') {
			return '\xA5' + " " + convertNumber(price, 4);
		} else if (type == 'price') {
			return '\xA5' + " " + convertNumber(price, 0);
		} else if (type == 'taxprice') {
			return '\xA5' + " " + convertNumber(price, 0);
		}
		return '\xA5' + " " + convertNumber(price, 0);
	} else {
		if (type == 'unitprice') {
			return strmonetaryunitsign + " " + convertNumber(price, 4);
		} else if (type == 'price') {
			return strmonetaryunitsign + " " + convertNumber(price, 2);
		} else if (type == 'taxprice') {
			return strmonetaryunitsign + " " + convertNumber(price, 0);
		}
		return strmonetaryunitsign + " " + convertNumber(price, 2);
	}
}

function convertNumber(str, fracctiondigits) {
	console.log(str);
	if ((str != "" && str != undefined && str != "null") || str == 0) {
		console.log("null以外の場合：" + str);
		return Number(str).toLocaleString(undefined, {
			minimumFractionDigits: fracctiondigits,
			maximumFractionDigits: fracctiondigits
		});
	} else {
		console.log("nullの場合：" + str);
		return "";
	}
}