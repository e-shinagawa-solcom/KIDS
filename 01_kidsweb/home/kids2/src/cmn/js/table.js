function setCheckBoxClickEvent(chkboxObj, tableA, tableA_chkbox, allCheckObj) {
    chkboxObj.on('click', function (e) {
        e.stopPropagation();
        var rowindex = $(this).parent().parent().index();
        if (this.checked) {
            tableA.find("tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#bbbbbb');
            tableA_chkbox.find("tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#bbbbbb');
            tableA_chkbox.find("tbody tr:nth-child(" + (rowindex + 1) + ")").find('td').find('input[name="edit"]').prop('checked', true);
        } else {
            $("#tableB tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#ffffff');
            tableA_chkbox.find("tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#ffffff');
            tableA_chkbox.find("tbody tr:nth-child(" + (rowindex + 1) + ")").find('td').find('input[name="edit"]').prop('checked', false);
        }
        // 対象チェックボックスチェック状態の設定
        scanAllCheckbox(tableA_chkbox, allCheckObj);
    });


}

function setAllCheckClickEvent(allCheckObj, tableA, tableA_chkbox) {
    // チェックボックスの切り替え処理のバインド
    allCheckObj.on({
        'click': function () {
            var status = this.checked;
            tableA_chkbox.find("tbody tr td ").find('input[type="checkbox"]')
                .each(function () {
                    this.checked = status;
                    if (status) {
                        tableA_chkbox.find("tbody tr").css("background-color", "#bbbbbb");
                        tableA.find("tbody tr").css("background-color", "#bbbbbb");
                    } else {
                        tableA_chkbox.find("tbody tr").css("background-color", "#ffffff");
                        tableA.find("tbody tr").css("background-color", "#ffffff");
                    }
                });
        }
    });
}

function scanAllCheckbox(tableA_chkbox, allCheckObj) {
    var $all_chkbox_rows = tableA_chkbox.find('tbody tr');
    var $all_checkbox = $all_chkbox_rows.find('input[type="checkbox"]');

    // 有効 <tr> ＊選択可能行
    var count_checked = 0;
    var count_disabled = 0;

    // data がない場合、全選択／解除チェックボックスを寝かせて無効化
    if (!$all_chkbox_rows.length) {
        allCheckObj.prop({ 'checked': false, 'disabled': true });
    } else {
        allCheckObj.prop('disabled', false);
    }

    $.each($all_checkbox, function (i) {
        // チェックボックスがひとつでも外れている場合、全選択／解除チェックボックスを寝かす
        if (!($(this).closest('tr').css("background-color") != 'rgb(255, 255, 255)')) {
            allCheckObj.prop('checked', false);
        }

        // チェックボックスがすべてチェックされた場合、全選択／解除チェックボックスを立てる
        if ($(this).closest('tr').css("background-color") != 'rgb(255, 255, 255)') {
            ++count_checked;
        }
        if ($all_chkbox_rows.length === count_checked) {
            allCheckObj.prop('checked', true);
        }

        // すべてのチェックボックスが無効化された場合、全選択／解除チェックボックスを寝かせて無効化
        if ($(this).prop('disabled')) {
            ++count_disabled;
        }
        console.log(count_disabled);
        console.log(count_checked);
        console.log($all_chkbox_rows.length);
        if ($all_chkbox_rows.length === count_disabled) {
            allCheckObj.prop({ 'checked': false, 'disabled': true });
        }
    });
}
// テーブルの行をクリックする時のイベント
function selectRow(type, tableA_fix, tableA, allCheckObj) {
    var rows = tableA_fix.find('tbody tr');
    var rows = tableA.find('tbody tr');
    var lastSelectedRow;
    /* Create 'click' event handler for rows */
    tableA_fix.find('tbody tr').on('click', function (e) {
        lastSelectedRow = trClickEvent($(this), lastSelectedRow, e, tableA_fix, tableA);
        if (type == 'hasChkbox') {
            scanAllCheckbox(tableA_fix, allCheckObj);
        }
    });

    /* Create 'click' event handler for rows */
    tableA.find('tbody tr').on('click', function (e) {
        lastSelectedRow = trClickEvent($(this), lastSelectedRow, e, tableA_fix, tableA);
        if (type == 'hasChkbox') {
            scanAllCheckbox(tableA_fix, allCheckObj);
        }
    });

    /* This 'event' is used just to avoid that the table text 
     * gets selected (just for styling). 
     * For example, when pressing 'Shift' keyboard key and clicking 
     * (without this 'event') the text of the 'table' will be selected.
     * You can remove it if you want, I just tested this in 
     * Chrome v30.0.1599.69 */
    $(document).bind('selectstart dragstart', function (e) {
        e.preventDefault(); return false;
    });
}

function trClickEvent(row, lastSelectedRow, e, tableA_fix, tableA) {
    /* Check if 'Ctrl', 'cmd' or 'Shift' keyboard key was pressed
     * 'Ctrl' => is represented by 'e.ctrlKey' or 'e.metaKey'
     * 'Shift' => is represented by 'e.shiftKey' */
    if (e.ctrlKey || e.metaKey) {
        /* If pressed highlight the other row that was clicked */
        if (tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color") != 'rgb(255, 255, 255)') {
            tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#ffffff");
            tableA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#ffffff");
            tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").find('input[type="checkbox"]').prop('checked', false);
        } else {
            tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
            tableA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
            tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").find('input[type="checkbox"]').prop('checked', true);
        }
    } else if (e.shiftKey) {
        /* If pressed highlight the other row that was clicked */
        var indexes = [lastSelectedRow.index(), row.index()];
        indexes.sort(function (a, b) {
            return a - b;
        });
        for (var i = indexes[0]; i <= indexes[1]; i++) {
            tableA_fix.find("tbody tr:nth-child(" + (i + 1) + ")").css("background-color", "#bbbbbb");
            tableA.find("tbody tr:nth-child(" + (i + 1) + ")").css("background-color", "#bbbbbb");
            tableA_fix.find("tbody tr:nth-child(" + (i + 1) + ")").find('input[type="checkbox"]').prop('checked', true);
        }
    } else {
        /* Otherwise just highlight one row and clean others */
        tableA_fix.find("tbody tr").css("background-color", "#ffffff");
        tableA_fix.find("tbody tr").find('input[type="checkbox"]').prop('checked', false);
        tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
        tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").find('input[type="checkbox"]').prop('checked', true);
        tableA.find("tbody tr").css("background-color", "#ffffff");
        tableA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
        lastSelectedRow = row;
    }

    return lastSelectedRow;
}



// テーブルの幅をリセットする
function resetTableWidth(table_fix_head, table_fix, table, table_head) {
    table_fix.find("tbody tr td").width(table_fix_head.find("thead tr th").width() + 1);
    table.find("tbody tr td").width('');
    table_head.find("thead tr th").width('');
    var thwidthArry = [];
    var tdwidthArry = [];
    var columnNum = table_head.find('thead tr th').length;
    console.log(columnNum);
    var width = 0;
    for (var i = 1; i <= columnNum; i++) {
        var thwidth = table_head.find('thead tr th:nth-child(' + i + ')').width();
        var tdwidth = table.find('tbody tr td:nth-child(' + i + ')').width();
        thwidthArry.push(thwidth + 20);
        tdwidthArry.push(tdwidth + 20);
    }

    for (var i = 1; i <= columnNum; i++) {
        if (table.find("tr td:nth-child(" + i + ")").css("display") != "none") {
            if (thwidthArry[i - 1] > tdwidthArry[i - 1]) {
                table_head.find("thead tr th:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                table.find("tbody tr td:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                width += thwidthArry[i - 1];
            } else {
                table_head.find("thead tr th:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                table.find("tbody tr td:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                width += tdwidthArry[i - 1];
            }
        }
    }

    table_head.width(width + 100);
    table.width(width + 100);
}

