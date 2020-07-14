$(document).ready(function () {
	trClickSelectRow();
});


jQuery(function () {
	// テーブルソートのリセット
	var sortList = "";
	var childSortList = "";
	console.log(location);
	$.each(location.href.split('&'), function(index, value) {
		if (value.split('=')[0] == 'sortList') {
			sortList = value.split('=')[1].split(',');
		}
		
		if (value.split('=')[0] == 'childSortList') {
			childSortList = value.split('=')[1].split(',');
		}
	});
	if (sortList != "") {
		var r = $('#result').tablesorter();
		r.trigger('sorton', [[[sortList[0], sortList[1]]]]);
	}
	
	if (childSortList != "") {
		var r = $('.tablesorter-child').tablesorter();
		r.trigger('sorton', [[[childSortList[0], childSortList[1]]]]);
	}
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
	$('#result tbody tr').on('click', function (e) {
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
		$('#result tbody tr').each(function (i, tr) {
			$(this).find('td').css("background-color", $(this).attr('before-click-bgcolor'));
		});

		var beforeClickBgcolor = $(this).parent().parent().parent().parent().attr('before-click-bgcolor');
		if (bgcolor != 'rgb(187, 187, 187)') {
			$(this).parent().parent().parent().parent().find('td').css("background-color", "#bbbbbb");
		} else {
			$(this).parent().parent().parent().parent().find('td').css("background-color", beforeClickBgcolor);
		}

	});
}