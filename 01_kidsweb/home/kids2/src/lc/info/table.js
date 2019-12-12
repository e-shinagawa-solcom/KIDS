$(document).ready(function () {
    var sortval = 0;
    $('#lc_head thead tr th').on('click', function () {
        var sortkey = $(this)[0].cellIndex;
        console.log(sortkey);
        if (sortval == 1) {
            sortval = 0;
        } else {
            sortval = 1;
        }
        var r = $('#lc_table').tablesorter()
        r.trigger('sorton', [[[(sortkey), sortval]]]);
    });
});
