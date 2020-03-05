function convertNumberByClass(str, currencyclass, fracctiondigits) {
	if (str != "" && str != undefined && str != "null") {
		if (currencyclass != "") {
			if (currencyclass == '円') {
				return Number(str).toLocaleString(undefined, {
					minimumFractionDigits: 0,
					maximumFractionDigits: 0
				});
			} else {
				return Number(str).toLocaleString(undefined, {
					minimumFractionDigits: 2,
					maximumFractionDigits: 2
				});
			}
		} else {
			return Number(str).toLocaleString(undefined, {
				minimumFractionDigits: fracctiondigits,
				maximumFractionDigits: fracctiondigits
			});
		}
	} else {
		return "";
	}
}