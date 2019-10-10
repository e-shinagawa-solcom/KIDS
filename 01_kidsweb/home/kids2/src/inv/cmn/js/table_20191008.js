$(function() {
  // response �ǥ����ץ��ԡ�������
  var data = [];

  // dom ������Ѷ�����
  var dataEmpty = new Array(20).fill('');

  // �ơ��֥�A ʣ��dom��Ǽ������
  var domA = [];

  // �ơ��֥�B ʣ��dom��Ǽ������
  var domB = [];

  // �ǡ�������ݴ� ���������Υǡ�����Ǽ������
  var temp = [];

  // ����ԥ���ǥå�����Ǽ������
  var selectedRowIndexes = [];

  // ���֥����ꥭ��å���
  $tableA = $('#tableA');
  $tableB = $('#tableB');

  // �ơ��֥�A <tbody>, <tr>
  $tableA_tbody = $('tbody', $tableA);
  $tableA_row = $('tr', $tableA_tbody);

  // �ơ��֥�B <tbody>, <tr>
  $tableB_tbody = $('tbody', $tableB);
  $tableB_row = $('tr', $tableB_tbody);

  // ʣ������ $tableA_row ����
  $tableA_row.remove();

  // ʣ������ $tableB_row ����
  $tableB_row.remove();

  /**
   * ----------------------------------------------------------------------------------------------------
   * �ؿ���
   * ----------------------------------------------------------------------------------------------------
   */

   /**
   * @method createSkeletonTable �ơ��֥�����
   * @param data {Array} ��Ȥʤ�ǡ�������
   * @param clone {Object} ʣ�������쥯����
   * @param dom {Array} ʣ��dom��Ǽ������
   * @param target {Object} �����西�����åȥ��쥯����
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
   * @method addDataTableA �ơ��֥�A�˥ǡ������ɲ�
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

      // html ����
      $target_row.attr('data-id', id);
      $('.slipcode', $target_row).html(slipcode);
      $('.customer .customercode', $target_row).html('[' + customercode + '] ');
      $('.customer .customername', $target_row).html(customername);
      $('.deliverydate', $target_row).html(deliverydate);
      $('.price', $target_row).html(curtotalprice);
      $('.taxclass .taxclasscode', $target_row).html('[' + taxclasscode + '] ');
      $('.taxclass .taxclassname', $target_row).html(taxclassname);
      $('.tax', $target_row).html(tax*100 + '��');
      $('.taxamount', $target_row).html(Math.round(taxamount));
      $('.remarks', $target_row).html(strnote);
    });
  };

  /**
   * @method addDataTableB �ơ��֥�B�˥ǡ������ɲ�
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

      // html ����
      $target_row.attr('data-id', id);
      $('.slipcode', $target_row).html(slipcode);
      $('.customer .customercode', $target_row).html('[' + customercode + '] ');
      $('.customer .customername', $target_row).html(customername);
      $('.deliverydate', $target_row).html(deliverydate);
      $('.price', $target_row).html(curtotalprice);
      $('.taxclass .taxclasscode', $target_row).html('[' + taxclasscode + '] ');
      $('.taxclass .taxclassname', $target_row).html(taxclassname);
      $('.tax', $target_row).html(tax*100 + '��');
      $('.taxamount', $target_row).html(Math.round(taxamount));
      $('.remarks', $target_row).html(strnote);
    });
  };

  /**
   * @method scanAllCheckbox �����������å��ܥå���
   */
  $.scanAllCheckbox = function() {
    var $all_rows = $('tbody tr', $tableA);
    var $all_checkbox = $all_rows.find('.checkbox input[type="checkbox"].check');

    // ͭ�� <tr> �������ǽ��
    var $enabled_rows = $all_rows.find('.checkbox input[type="checkbox"].check').not(':disabled');
    var count_checked = 0;
    var count_disabled = 0;

    // data ���ʤ���硢�����򡿲�������å��ܥå����򿲤�����̵����
    if (!data.length) {
      $('#allChecked').prop({ 'checked': false, 'disabled': true });
    } else {
      $('#allChecked').prop('disabled', false);
    }

    // <tr> �� data-id °����¸�ߤ��ʤ���硢���������å��ܥå�����̵����
    $.each($all_rows, function() {
      if (!$(this).data('id')) {
        $all_checkbox.prop({ 'checked': false, 'disabled': true });
      }
    });

    $.each($all_checkbox, function(i) {
      // �����å��ܥå������ҤȤĤǤ⳰��Ƥ����硢�����򡿲�������å��ܥå����򿲤���
      if (!$(this).prop('checked')) {
        $('#allChecked').prop('checked', false);
      }

      // �����å��ܥå��������٤ƥ����å����줿��硢�����򡿲�������å��ܥå�����Ω�Ƥ�
      if ($(this).prop('checked')) {
        ++count_checked;
      }
      if ($enabled_rows.length === count_checked) {
        $('#allChecked').prop('checked', true);
      }

      // ���٤ƤΥ����å��ܥå�����̵�������줿��硢�����򡿲�������å��ܥå����򿲤�����̵����
      if ($(this).prop('disabled')) {
        ++count_disabled;
      }
      if (data.length === count_disabled) {
        $('#allChecked').prop({ 'checked': false, 'disabled': true });
      }
    });
  };

  /**
   * @method initTableA �ơ��֥�A �����
   */
  $.initTableA = function() {
    // �����������å��ܥå���
    $.scanAllCheckbox();
    data = [];
    $.createSkeletonTable(dataEmpty, $tableA_row, domA, $tableA_tbody);
  };

  /**
   * @method setTableSorter �ơ��֥륽���ȵ�ǽ����
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
   * ���ɽ��
   * ----------------------------------------------------------------------------------------------------
   */

  // �����������å��ܥå���
  $.scanAllCheckbox();

  $.createTable = function(response) {
    data = (response === undefined || response && !response.length) ? dataEmpty : Array.from(new Set(response));

    // �ơ��֥�A����
    $.createSkeletonTable(data, $tableA_row, domA, $tableA_tbody);
    $.addDataTableA();

    // �ơ��֥륽���ȵ�ǽ����
    $.setTableSorter();

    // �����������å��ܥå���
    $.scanAllCheckbox();
  };

  /**
   * ----------------------------------------------------------------------------------------------------
   * ���٥������
   * ----------------------------------------------------------------------------------------------------
   */

  // �ơ��֥�A �����򡿲�������å��ܥå���
  $(document).on('change', '#allChecked', function(e) {
    e.preventDefault();

    var $all_rows_checkbox = $('.checkbox input[type="checkbox"].check', $tableA);

    if (e.target.checked) {
      $all_rows_checkbox.not(':disabled').prop('checked', true).closest('tr').children('td').addClass('current');
    } else {
      $all_rows_checkbox.prop('checked', false).closest('tr').children('td').removeClass('current');
    }
  });

  // �ơ��֥�A �����򡿲��
  $(document).on('change', '#tableA .checkbox input[type="checkbox"].check', function(e) {
    e.preventDefault();
    var $self_row = $(this).closest('tr');

    if (e.target.checked) {
      $self_row.addClass('selected');
      $self_row.children('td').addClass('current');
    } else {
      $self_row.removeClass('selected');
      $self_row.children('td').removeClass('current');
    }

    // �����������å��ܥå���
    $.scanAllCheckbox();
  });

  // �ơ��֥�A �ɲåܥ���
  $('#btnAdd').on('click', function(e) {
    e.preventDefault();

    var $all_rows = $('tbody tr', $tableA);
    var $all_checkbox = $all_rows.find('.checkbox input[type="checkbox"].check');
    var checked = [];

    $.each($all_rows, function() {
      var $isChecked = $(this).find('.checkbox input[type="checkbox"].check').prop('checked');
      checked.push($isChecked);
    });

    // �����å��ܥå������ҤȤĤ����򤵤�Ƥ��ʤ���硢return
    if ($.inArray(true, checked) === -1) return;

    // �����å��ܥå����γ����ǡ����򤹤٤� temp �˳�Ǽ
    $.each($all_checkbox, function() {
      if ($(this).prop('checked')) {
        var $data_id = $(this).closest('tr').data('id');
        var data_index = data.findIndex(function(value) { return value.strslipcode == $data_id });
        if (data_index !== -1) {
          // id��ʣ�����å�
          let sameId = false;
          $.each(temp, function(i,v) {
            if(v.strslipcode == data[data_index].strslipcode) { sameId = true;}
          });
          if(sameId == true) { return; }
          temp.push(data[data_index]);
          // �����ǡ����� data ������
          data.splice(data_index, 1);
        }
      }
    });

    // �ơ��֥�B�˥ǡ������ɲ�
    $.createSkeletonTable(temp, $tableB_row, domB, $tableB_tbody);
    $.addDataTableB();

    // �ơ��֥�A ������
    $.createSkeletonTable(data, $tableA_row, domA, $tableA_tbody);
    $.addDataTableA();

    // �ơ��֥륽���ȵ�ǽ����
    $.setTableSorter();

    // �����������å��ܥå���
    $.scanAllCheckbox();
  });
  
  // �ơ��֥�B ����ܥ���
  $('#btnDelete').on('click', function(e) {
    e.preventDefault();

    var $selected_rows = $('tbody tr.selected', $tableB);

    if (!$selected_rows.length) return;

    $.each($selected_rows, function () {
      var $data_id = $(this).data('id');
      var temp_index = temp.findIndex(function(value) { return value.strslipcode == $data_id });

      // �����ǡ����� temp ������
      if (temp_index !== -1) {
        temp.splice(temp_index, 1);
      }
    });

    // �ơ��֥�B ������
    $.createSkeletonTable(temp, $tableB_row, domB, $tableB_tbody);
    $.addDataTableB();

    // �ơ��֥륽���ȵ�ǽ����
    $.setTableSorter();

    // �����������å��ܥå���
    $.scanAllCheckbox();
  });

  // �ơ��֥�B ������ܥ���
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

    // �ơ��֥�A ������
    $.createSkeletonTable(data, $tableA_row, domA, $tableA_tbody);
    $.addDataTableA();

    // �ơ��֥�B �����
    $tableB_tbody.empty();

    // �ơ��֥륽���ȵ�ǽ����
    $.setTableSorter();

    // �����������å��ܥå���
    $.scanAllCheckbox();
  });

  // ����������ϥܥ���
  $('#btnSearchCondition').on('click', function(e) {
    e.preventDefault();

    // selectedRowIndexes �����
    selectedRowIndexes = [];

    var $all_rows = $('tbody tr', $tableA);
    var $all_checkbox = $all_rows.find('.checkbox input[type="checkbox"].check');

    // �����å��ܥå����Υ����å��򤹤٤Ʋ��
    $.each($all_checkbox, function() {
      if ($(this).prop('checked')) {
        $(this).prop('checked', false);
      }
    });

    // ���٤Ƥ� current ����
    $.each($all_rows, function() {
      $(this).children('td').removeClass('current');
    });

    // �����򡿲�������å��ܥå����Υ����å�����
    if ($('#allChecked').prop('checked')) {
      $('#allChecked').prop('checked', false);
    }

    url = $('input[name="invConditionUrl"]').val();
    // Ǽ�ʽ񸡺�������ɥ���ݥåץ��å�ɽ��
    sub_win = window.open(url, 'winSearch', "width=800,height=500,scrollbars=yes");
    // ����񸡺�������ɥ���ݥåץ��å�ɽ��
  });

  /**
   * ----------------------------------------------------------------------------------------------------
   * �ü쥭�� + click ���٥�Ƚ���
   * ----------------------------------------------------------------------------------------------------
   */

  // ctrl + �� click ����ƥ����ȥ�˥塼��ɽ��
  $(document).on('contextmenu', function(e) {
    if (e.which === 1) return false;
  });

  // ctrl, shift �������٥�Ȥ� document ������
  var isCtrlKey = false;
  var isShiftKey = false;

  $(document).on({
    'keydown': function(e) {
      if (e.ctrlKey) isCtrlKey = true;
      if (e.shiftKey) isShiftKey = true;
    },
    'keyup': function(e) {
      isCtrlKey = false;
      isShiftKey = false;
    }
  });

  // �ơ��֥�A <tr> �ü쥭�� + click ���٥�Ƚ���
  $(document).on('mousedown', '#tableA tbody tr', function(e) {
    e.preventDefault();

    var $tableA_rows = $('#tableA tbody tr');
    var $tableA_rows_length = $tableA_rows.length;

    // �ơ��֥�A <tr> ctrl + click -> �ơ��֥�B�˥ǡ������ɲ�
    if (isCtrlKey && e.which === 1) {
      var $data_id = $(this).data('id');
      var data_index = data.findIndex(function(value) { return value.strslipcode == $data_id });
      if (data_index !== -1) {
        temp.push(data[data_index]);
        // �����ǡ����� data ������
        data.splice(data_index, 1);
      }

      // �ơ��֥�B�˥ǡ������ɲ�
      $.createSkeletonTable(temp, $tableB_row, domB, $tableB_tbody);
      $.addDataTableB();

      // �ơ��֥�A ������
      $.createSkeletonTable(data, $tableA_row, domA, $tableA_tbody);
      $.addDataTableA();

      // �ơ��֥륽���ȵ�ǽ����
      $.setTableSorter();
    }

    // �ơ��֥�A <tr> shift + click -> ʣ�����򡿲��
    if ($tableA_rows_length !== 1 && isShiftKey && e.which === 1) {
      var $row_index = $(this).index();

      // ���򤵤줿�ԥ���ǥå����� selectedRowIndexes �˳�Ǽ
      selectedRowIndexes.push($row_index);

      // ���祽����
      selectedRowIndexes.sort(function(a, b) { return a - b });

      // ʣ��������
      if ($(this).hasClass('selected')) {
        $(this).removeClass('selected').children('td').removeClass('current');
        $(this).find('.checkbox input[type="checkbox"].check').prop('checked', false);

        var i = selectedRowIndexes[0];
        while (i <= selectedRowIndexes[1]) {
          $tableA_rows.eq(i).removeClass('selected').children('td').removeClass('current');
          $tableA_rows.eq(i).find('.checkbox input[type="checkbox"].check').prop('checked', false);
          ++i;
        }
      }
      // ʣ������
      else {
        $(this).addClass('selected').children('td').addClass('current');
        $(this).find('.checkbox input[type="checkbox"].check').prop('checked', true);

        var i = selectedRowIndexes[0];
        while (i <= selectedRowIndexes[1]) {
          $tableA_rows.eq(i).addClass('selected').children('td').addClass('current');
          $tableA_rows.eq(i).find('.checkbox input[type="checkbox"].check').prop('checked', true);
          ++i;
        }
      }

      // selectedRowIndexes �����
      if (i === selectedRowIndexes[1] + 1) selectedRowIndexes = [];
    }

    // selectedRowIndexes �����
    if ($tableA_rows_length === 1) selectedRowIndexes = [];

    // �����������å��ܥå���
    $.scanAllCheckbox();
  });

  // �ơ��֥�B <tr> �ü쥭�� + click ���٥�Ƚ���
  $(document).on('mousedown', '#tableB tbody tr', function(e) {
    e.preventDefault();

    var $tableB_rows = $('#tableB tbody tr');
    var $tableB_rows_length = $tableB_rows.length;

    // �ơ��֥�B <tr> shift + click -> ʣ�����򡿲��
    if ($tableB_rows_length !== 1 && isShiftKey && e.which === 1) {
      var $row_index = $(this).index();

      // ���򤵤줿�ԥ���ǥå����� selectedRowIndexes �˳�Ǽ
      selectedRowIndexes.push($row_index);

      // ���祽����
      selectedRowIndexes.sort(function(a, b) { return a - b });

      // ʣ��������
      if ($(this).hasClass('selected')) {
        $(this).removeClass('selected').children('td').removeClass('current');

        var i = selectedRowIndexes[0];
        while (i <= selectedRowIndexes[1]) {
          $tableB_rows.eq(i).removeClass('selected').children('td').removeClass('current');
          ++i;
        }
      }
      // ʣ������
      else {
        $(this).addClass('selected').children('td').addClass('current');

        var i = selectedRowIndexes[0];
        while (i <= selectedRowIndexes[1]) {
          $tableB_rows.eq(i).addClass('selected').children('td').addClass('current');
          ++i;
        }
      }

      // selectedRowIndexes �����
      if (i === selectedRowIndexes[1] + 1) selectedRowIndexes = [];
    } else {
      if ($(this).hasClass('selected')) {
        $(this).removeClass('selected').children('td').removeClass('current');
      } else {
        $(this).addClass('selected').children('td').addClass('current');
      }
    }
  });

});
