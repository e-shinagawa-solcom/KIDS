$(function() {
  // response ディープコピー用配列
  var data = [];

  // dom 初期化用空配列
  var dataEmpty = new Array(20).fill('');

  // テーブルA 複製dom格納用配列
  var domA = [];

  // テーブルB 複製dom格納用配列
  var domB = [];

  // データ一時保管 ＊送信前のデータ格納用配列
  var temp = [];

  // サブクエリキャッシュ
  $tableA = $('#tableA');
  $tableB = $('#tableB');

  // テーブルA <tbody>, <tr>
  $tableA_tbody = $('tbody', $tableA);
  $tableA_row = $('tr', $tableA_tbody);

  // テーブルB <tbody>, <tr>
  $tableB_tbody = $('tbody', $tableB);
  $tableB_row = $('tr', $tableB_tbody);

  // 複製元の $tableA_row を削除
  $tableA_row.remove();

  // 複製元の $tableB_row を削除
  $tableB_row.remove();

  /**
   * ----------------------------------------------------------------------------------------------------
   * 関数群
   * ----------------------------------------------------------------------------------------------------
   */

   /**
   * @method createSkeletonTable テーブル生成
   * @param data {Array} 基となるデータ配列
   * @param clone {Object} 複製元セレクター
   * @param dom {Array} 複製dom格納用配列
   * @param target {Object} 出力先ターゲットセレクター
   */
  $.createSkeletonTable = function(data, clone, dom, target) {
    dom = [];
    target.empty();
    $.each(data, function(i) {
      var $clone = clone.clone();
      dom.push({ html: $clone });
      var $html = dom[i].html;
      target.append($html);
    });
  };

  /**
   * @method addDataTableA テーブルAにデータを追加
   */
  $.addDataTableA = function() {
    if (data[0] === '') return;
    $.each(data, function(i, v) {
      var $target_row = $('tbody tr', $tableA).eq(i);
      var slipcode = v.strslipcode;
      var customercode = v.strcompanydisplaycode;
      var customername = v.strcustomername;
      var deliverydate = v.dtmdeliverydate.replace(/-/g, '/');
      var curtotalprice = Number(v.curtotalprice);
      var taxclasscode = v.lngtaxclasscode;
      var taxclassname = v.strtaxclassname;
      var tax = Number(v.curtax);
      var taxamount = (curtotalprice * (tax*100))/100;
      if(taxclasscode == 1) taxamount = 0;
      var id = v.strslipcode;
      var strnote = v.strnote;

      // html 出力
      $target_row.attr('data-id', id);
      $('.slipcode', $target_row).html(slipcode);
      $('.customer .customercode', $target_row).html('[' + customercode + '] ');
      $('.customer .customername', $target_row).html(customername);
      $('.deliverydate', $target_row).html(deliverydate);
      $('.price', $target_row).html(curtotalprice);
      $('.taxclass .taxclasscode', $target_row).html('[' + taxclasscode + '] ');
      $('.taxclass .taxclassname', $target_row).html(taxclassname);
      $('.tax', $target_row).html(tax*100 + '％');
      $('.taxamount', $target_row).html(Math.round(taxamount));
      $('.remarks', $target_row).html(strnote);
    });
  };

  /**
   * @method addDataTableB テーブルBにデータを追加
   */
  $.addDataTableB = function() {
    $.each(temp, function(i, v) {
      var $target_row = $('tbody tr', $tableB).eq(i);
      var slipcode = v.strslipcode;
      var customercode = v.strcompanydisplaycode;
      var customername = v.strcustomername;
      var deliverydate = v.dtmdeliverydate.replace(/-/g, '/');
      var curtotalprice = Number(v.curtotalprice);
      var taxclasscode = v.lngtaxclasscode;
      var taxclassname = v.strtaxclassname;
      var tax = Number(v.curtax);
      var taxamount = (curtotalprice * (tax*100))/100;
      if(taxclasscode == 1) taxamount = 0;
      var id = v.strslipcode;
      var strnote = v.strnote;

      // html 出力
      $target_row.attr('data-id', id);
      $('.slipcode', $target_row).html(slipcode);
      $('.customer .customercode', $target_row).html('[' + customercode + '] ');
      $('.customer .customername', $target_row).html(customername);
      $('.deliverydate', $target_row).html(deliverydate);
      $('.price', $target_row).html(curtotalprice);
      $('.taxclass .taxclasscode', $target_row).html('[' + taxclasscode + '] ');
      $('.taxclass .taxclassname', $target_row).html(taxclassname);
      $('.tax', $target_row).html(tax*100 + '％');
      $('.taxamount', $target_row).html(Math.round(taxamount));
      $('.remarks', $target_row).html(strnote);
    });
  };

  /**
   * @method scanAllCheckbox スキャンチェックボックス
   */
  $.scanAllCheckbox = function() {
    var $all_rows = $('tbody tr', $tableA);
    var $all_checkbox = $all_rows.find('.checkbox input[type="checkbox"].check');

    // 有効 <tr> ＊選択可能行
    var $enabled_rows = $all_rows.find('.checkbox input[type="checkbox"].check').not(':disabled');
    var count_checked = 0;
    var count_disabled = 0;

    // data がない場合、全選択／解除チェックボックスを寝かせて無効化
    if (!data.length) {
      $('#allChecked').prop({ 'checked': false, 'disabled': true });
    } else {
      $('#allChecked').prop('disabled', false);
    }

    // <tr> に data-id 属性が存在しない場合、該当チェックボックスを無効化
    $.each($all_rows, function() {
      if (!$(this).data('id')) {
        $all_checkbox.prop({ 'checked': false, 'disabled': true });
      }
    });

    $.each($all_checkbox, function(i) {
      // チェックボックスがひとつでも外れている場合、全選択／解除チェックボックスを寝かす
      if (!$(this).prop('checked')) {
        $('#allChecked').prop('checked', false);
      }

      // チェックボックスがすべてチェックされた場合、全選択／解除チェックボックスを立てる
      if ($(this).prop('checked')) {
        ++count_checked;
      }
      if ($enabled_rows.length === count_checked) {
        $('#allChecked').prop('checked', true);
      }

      // すべてのチェックボックスが無効化された場合、全選択／解除チェックボックスを寝かせて無効化
      if ($(this).prop('disabled')) {
        ++count_disabled;
      }
      if (data.length === count_disabled) {
        $('#allChecked').prop({ 'checked': false, 'disabled': true });
      }
    });
  };

  /**
   * @method initTableA テーブルA 初期化
   */
  $.initTableA = function() {
    // スキャンチェックボックス
    $.scanAllCheckbox();
    data = [];
    $.createSkeletonTable(dataEmpty, $tableA_row, domA, $tableA_tbody);
  };

  /**
   * @method setTableSorter テーブルソート機能設定
   */
  $.setTableSorter = function() {
    $('#tableA, #tableB').trigger('destroy');
    $('#tableA').tablesorter({
      headers: {
        0: { sorter: false },
        1: { sorter: false }
      }
    });
    $('#tableB').tablesorter({
      headers: {
        0: { sorter: false }
      }
    });
  };

  /**
   * ----------------------------------------------------------------------------------------------------
   * 初期表示
   * ----------------------------------------------------------------------------------------------------
   */

  // スキャンチェックボックス
  $.scanAllCheckbox();

  $.createTable = function(response) {
    data = (response === undefined || response && !response.length) ? dataEmpty : Array.from(new Set(response));

    // テーブルA生成
    $.createSkeletonTable(data, $tableA_row, domA, $tableA_tbody);
    $.addDataTableA();

    // テーブルソート機能設定
    $.setTableSorter();

    // スキャンチェックボックス
    $.scanAllCheckbox();
  };

  $.createTableRenew = function(response) {
	data = (response === undefined || response && !response.length) ? dataEmpty : Array.from(new Set(response));

	// テーブルB生成
	$.createSkeletonTable(data, $tableB_row, domB, $tableB_tbody);
	$.each(data, function(i) {
	  temp.push(data[i]);
	});
	$.addDataTableB();
	// テーブルソート機能設定
	$.setTableSorter();
	// スキャンチェックボックス
	$.scanAllCheckbox();
  };

  /**
   * ----------------------------------------------------------------------------------------------------
   * イベント設定
   * ----------------------------------------------------------------------------------------------------
   */

  // テーブルA 全選択／解除チェックボックス
  $(document).on('change', '#allChecked', function(e) {
    e.preventDefault();

    var $all_rows_checkbox = $('.checkbox input[type="checkbox"].check', $tableA);

    if (e.target.checked) {
      $all_rows_checkbox.not(':disabled').prop('checked', true).closest('tr').children('td').addClass('current');
    } else {
      $all_rows_checkbox.prop('checked', false).closest('tr').children('td').removeClass('current');
    }
  });

  // テーブルA 行選択／解除
  $(document).on('change', '#tableA .checkbox input[type="checkbox"].check', function(e) {
    e.preventDefault();
    var $self_row = $(this).closest('tr');

    if (e.target.checked) {
      $self_row.children('td').addClass('current');
    } else {
      $self_row.children('td').removeClass('current');
    }

    // スキャンチェックボックス
    $.scanAllCheckbox();
  });

  // テーブルA 追加ボタン
  $('#btnAdd').on('click', function(e) {
    e.preventDefault();

    var $all_rows = $('tbody tr', $tableA);
    var $all_checkbox = $all_rows.find('.checkbox input[type="checkbox"].check');
    var checked = [];

    $.each($all_rows, function() {
      var $isChecked = $(this).find('.checkbox input[type="checkbox"].check').prop('checked');
      checked.push($isChecked);
    });

    // チェックボックスがひとつも選択されていない場合、return
    if ($.inArray(true, checked) === -1) return;

    // チェックボックスの該当データをすべて temp に格納
    $.each($all_checkbox, function() {
      if ($(this).prop('checked')) {
        var $data_id = $(this).closest('tr').data('id');
        var data_index = data.findIndex(function(value) { return value.strslipcode == $data_id });
        if (data_index !== -1) {
            // id重複チェック
        	let sameId = false;
            $.each(temp, function(i,v) {
            	if(v.strslipcode == data[data_index].strslipcode) { sameId = true;}
            });
            if(sameId == true) { return; }
        	temp.push(data[data_index]);
        }
      }
    });

    // テーブルBにデータを追加
    $.createSkeletonTable(temp, $tableB_row, domB, $tableB_tbody);
    $.addDataTableB();

    // テーブルAの処理
    $.each(temp, function(i, v) {
      var target_index = data.findIndex(function(value) { return value.strslipcode === v.strslipcode });
      var $target_row = $('tbody tr', $tableA).eq(target_index);
      var $target_checkbox = $target_row.find('.checkbox input[type="checkbox"].check');

      if (target_index !== -1) {
        // 該当 <tr> から data-id を削除
        $target_row.removeAttr('data-id');

        // 該当 <tr> の セル背景色を変更 ＊選択不可 UI として
        $target_row.children('td').removeClass('current').addClass('disabled');

        // 該当データ選択のチェックボックスを無効化
        $target_checkbox.prop({ 'checked': false, 'disabled': true });
      }
    });

    // テーブルソート機能設定
    $.setTableSorter();

    // スキャンチェックボックス
    $.scanAllCheckbox();
  });

  // テーブルB 行選択／解除
  $(document).on('click', '#tableB tbody tr', function(e) {
    e.preventDefault();

    if (!temp.length) return;

    var $self_row = $(this).closest('tr');
    var $index = $self_row.index();
    var $selected_row = $('tbody tr.selected', $tableB);

    if (temp[$index] !== undefined && !$selected_row.length) {
      $self_row.addClass('selected').children('td').addClass('current');
    } else {
      $self_row.removeClass('selected').children('td').removeClass('current');
    }
  });

  // テーブルB 削除ボタン
  $('#btnDelete').on('click', function(e) {
    e.preventDefault();

    var $selected_row = $('tbody tr.selected', $tableB);

    if (!$selected_row.length) return;

    var $data_id = $selected_row.data('id');
    var temp_index = temp.findIndex(function(value) { return value.strslipcode == $data_id });

    // 該当データを temp から削除
    if (temp_index !== -1) {
      temp.splice(temp_index, 1);
    }

    // テーブルB 再生成
    $.createSkeletonTable(temp, $tableB_row, domB, $tableB_tbody);
    $.addDataTableB();

    // テーブルソート機能設定
    $.setTableSorter();

    // スキャンチェックボックス
    $.scanAllCheckbox();
  });

  // テーブルB 全削除ボタン
  $('#btnAllDelete').on('click', function(e) {
    e.preventDefault();

    var $tableB_row = $('tbody tr', $tableB);
    var count = 0;

    $.each($tableB_row, function(i) {
      if ($(this).data('id')) {
        ++count;
      }
    });

    if (!$tableB_row.data('id') && count === 0) return;

    temp = [];
    domB = [];

    // テーブルA 再生成
    $.createSkeletonTable(data, $tableA_row, domA, $tableA_tbody);
    $.addDataTableA();

    // テーブルB 初期化
    $tableB_tbody.empty();

    // テーブルソート機能設定
    $.setTableSorter();

    // スキャンチェックボックス
    $.scanAllCheckbox();
  });

  // 検索条件入力ボタン
  $('#btnSearchCondition').on('click', function(e) {
    e.preventDefault();

    var $all_rows = $('tbody tr', $tableA);
    var $all_checkbox = $all_rows.find('.checkbox input[type="checkbox"].check');

    // チェックボックスのチェックをすべて解除
    $.each($all_checkbox, function() {
      if ($(this).prop('checked')) {
        $(this).prop('checked', false);
      }
    });

    // すべての current を削除
    $.each($all_rows, function() {
      $(this).children('td').removeClass('current');
    });

    // 全選択／解除チェックボックスのチェックを解除
    if ($('#allChecked').prop('checked')) {
      $('#allChecked').prop('checked', false);
    }

    url = $('input[name="invConditionUrl"]').val();
    // 納品書検索ウィンドウをポップアップ表示
    sub_win = window.open(url, 'winSearch', "width=800,height=500,scrollbars=yes");
  });

});
