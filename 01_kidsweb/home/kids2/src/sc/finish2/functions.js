
function fncListOutput(strURL) {
	window.open(strURL, 'listWin', 'status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no');
	return false;
}

$(document).ready(function () {
	$(window).on("beforeunload", function (e) {
		if (window.opener.location.href.indexOf('renew') >= 0) {
			window.opener.close();
		} else {
			window.opener.location.reload();
		}
	});
});