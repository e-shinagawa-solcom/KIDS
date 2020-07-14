(function(){
    $('#result').tablesorter({
        // widthFixed : true,
        dateFormat: 'yyyy/mm/dd',
        sortInitialOrder: 'asc',
        widgets:['zebra','stickyHeaders'],
        headers: { 0: { sorter: false } },
        debug: true,
        widgetOptions: {
            cssStickyHeaders_offset        : 0,
            cssStickyHeaders_addCaption    : true,
            // jQuery selector or object to attach sticky header to
            cssStickyHeaders_attachTo      : null,
            cssStickyHeaders_filteredToTop : true,
            cssStickyHeaders_zIndex        : 10
        }
    });
    
	// テーブルソートのリセット
	var sortList = "";
	var childSortList = "";
	$.each(location.href.split('&'), function(index, value) {
		if (value.split('=')[0] == 'sortList') {
			sortList = value.split('=')[1].split(',');
		}
	});
	if (sortList != "") {
		var r = $('#result').tablesorter();
		r.trigger('sorton', [[[sortList[0], sortList[1]]]]);
	}
})();
