(function(){
    $('#result').tablesorter({
        // widthFixed : true,
        dateFormat: 'yyyy/mm/dd',
        sortInitialOrder: 'asc',
        widgets:['zebra','stickyHeaders'],
        //headers :{0:{sorter: "text"}},
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
})();
