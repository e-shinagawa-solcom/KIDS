$(document).ready(function () {
	trClickSelectRow();
});

function historyTrClickSelectRow() {
	$('.history').on('click', function () {
		var bgcolor = $(this).find('td').css("background-color");
		$('#result tbody tr').each(function (i, tr) {
			$(this).find('td').css("background-color", $(this).attr('before-click-bgcolor'));
		});
		var beforeClickBgcolor = $(this).attr('before-click-bgcolor');
		if (bgcolor != 'rgb(187, 187, 187)') {
			$(this).find('td').css("background-color", "#bbbbbb");
		} else {
			$(this).find('td').css("background-color", beforeClickBgcolor);
		}
	});
}

function trClickSelectRow() {
	console.log($('#result tbody tr').length);
	$('#result tbody tr').on('click', function (e) {
		// e.preventDefault();
		console.log("bgcolor");
		var index = $(this).index();
		var bgcolor = $(this).find('td').css("background-color");

		$('#result tbody tr').each(function (i, tr) {
			$(this).find('td').css("background-color", $(this).attr('before-click-bgcolor'));
		});

		var beforeClickBgcolor = $(this).attr('before-click-bgcolor');
		if (bgcolor != 'rgb(187, 187, 187)') {
			$(this).find('td').css("background-color", "#bbbbbb");
		} else {
			$(this).find('td').css("background-color", beforeClickBgcolor);
		}
	});

	$('.tablesorter-child tbody tr').on('click', function (e) {
		var bgcolor = $(this).find('td').css("background-color");
		console.log(bgcolor);
		$('#result tbody tr').each(function (i, tr) {
			$(this).find('td').css("background-color", $(this).attr('before-click-bgcolor'));
		});

		var beforeClickBgcolor = $(this).parent().parent().parent().parent().attr('before-click-bgcolor');
		console.log(beforeClickBgcolor);
		if (bgcolor != 'rgb(187, 187, 187)') {
			$(this).parent().parent().parent().parent().find('td').css("background-color", "#bbbbbb");
		} else {
			$(this).parent().parent().parent().parent().find('td').css("background-color", beforeClickBgcolor);
		}

	});
}