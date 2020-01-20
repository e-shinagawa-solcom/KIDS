$(function () {
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


  // ���������ե��������򼺤ä��Ȥ��ν���
  $('input[name="curlastmonthbalance"]').on('blur', function () {
    var val = $(this).val();
    var thisMonthAmount = Number($('input[name="curthismonthamount"]').val().replace(/,/g, ''));
    var taxPrice = Number($('input[name="curtaxprice"]').val().replace(/,/g, ''));
    var curLastMonthBalance = Number(val);
    // ������׳�
    // ��������ĳ� + ��������� + ������
    var noTaxMonthAmount = curLastMonthBalance + thisMonthAmount + taxPrice;
    $('input[name="notaxcurthismonthamount"]').val(convertNumber(Math.round(noTaxMonthAmount))).change();
    $(this).val(convertNumber(val));
  });
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
  $.createSkeletonTable = function (data, clone, dom, target) {
    dom = [];
    target.empty();
    $.each(data, function (i) {
      var $clone = clone.clone();
      dom.push({ html: $clone });
      var $html = dom[i].html;
      target.append($html);
    });
  };

  /**
   * @method addDataTableA �ơ��֥�A�˥ǡ������ɲ�
   */
  $.addDataTableA = function () {
    if (data[0] === '') return;
    $.each(data, function (i, v) {
      var $target_row = $('tbody tr', $tableA).eq(i);
      var slipno = v.lngslipno;
      var revisionno = v.lngrevisionno;
      var slipcode = v.strslipcode;
      var customercode = v.lngdeliveryplacecode;
      var customername = v.strdeliveryplacename;
      var deliverydate = v.dtmdeliverydate.replace(/-/g, '/');
      var curtotalprice = Number(v.curtotalprice);
      var taxclasscode = v.lngtaxclasscode;
      var taxclassname = v.strtaxclassname;
      var tax = Number(v.curtax);
      var taxamount = (curtotalprice * (tax * 100)) / 100;
      if (taxclasscode == 1) taxamount = 0;
      var id = v.strslipcode;
      var strnote = v.strnote;

      // html ����
      $target_row.attr('data-id', id);
      $target_row.attr('slipno', slipno);
      $target_row.attr('revisionno', revisionno);
      $('.slipcode', $target_row).html(slipcode);
      $('.customer .customercode', $target_row).html('[' + customercode + '] ');
      $('.customer .customername', $target_row).html(customername);
      $('.deliverydate', $target_row).html(deliverydate);
      $('.price', $target_row).html(convertNumber(curtotalprice));
      $('.taxclass .taxclasscode', $target_row).html('[' + taxclasscode + '] ');
      $('.taxclass .taxclassname', $target_row).html(taxclassname);
      $('.tax', $target_row).html(tax * 100 + '��');
      $('.taxamount', $target_row).html(convertNumber(Math.round(taxamount)));
      $('.remarks', $target_row).html(strnote);
    });
  };

  /**
   * @method addDataTableB �ơ��֥�B�˥ǡ������ɲ�
   */
  $.addDataTableB = function () {
    $.each(temp, function (i, v) {
      var $target_row = $('tbody tr', $tableB).eq(i);
      var slipcode = v.strslipcode;
      var customercode = v.lngdeliveryplacecode;
      var customername = v.strdeliveryplacename;
      var deliverydate = v.dtmdeliverydate.replace(/-/g, '/');
      var curtotalprice = Number(v.curtotalprice);
      var taxclasscode = v.lngtaxclasscode;
      var taxclassname = v.strtaxclassname;
      var tax = Number(v.curtax);
      var taxamount = (curtotalprice * (tax * 100)) / 100;
      if (taxclasscode == 1) taxamount = 0;
      var id = v.strslipcode;
      var slipno = v.lngslipno;
      var revisionno = v.lngrevisionno;
      var strnote = v.strnote;

      // html ����
      $target_row.attr('data-id', id);
      $target_row.attr('slipno', slipno);
      $target_row.attr('revisionno', revisionno);
      $('.slipcode', $target_row).html(slipcode);
      $('.customer .customercode', $target_row).html('[' + customercode + '] ');
      $('.customer .customername', $target_row).html(customername);
      $('.deliverydate', $target_row).html(deliverydate);
      $('.price', $target_row).html(convertNumber(curtotalprice));
      $('.taxclass .taxclasscode', $target_row).html('[' + taxclasscode + '] ');
      $('.taxclass .taxclassname', $target_row).html(taxclassname);
      $('.tax', $target_row).html(tax * 100 + '��');
      $('.taxamount', $target_row).html(convertNumber(Math.round(taxamount)));
      $('.remarks', $target_row).html(strnote);
    });
  };

  /**
   * @method scanAllCheckbox �����������å��ܥå���
   */
  $.scanAllCheckbox = function () {
    var $all_rows = $('tbody tr', $tableA);
    var $all_checkbox = $all_rows.find('input[type="checkbox"]');

    // ͭ�� <tr> �������ǽ��
    var count_checked = 0;
    var count_disabled = 0;

    // data ���ʤ���硢�����򡿲�������å��ܥå����򿲤�����̵����
    if (!data.length) {
      $('#allChecked').prop({ 'checked': false, 'disabled': true });
    } else {
      $('#allChecked').prop('disabled', false);
    }

    // <tr> �� data-id °����¸�ߤ��ʤ���硢���������å��ܥå�����̵����
    $.each($all_rows, function () {
      if (!$(this).data('id')) {
        $all_checkbox.prop({ 'checked': false, 'disabled': true });
      }
    });

    $.each($all_checkbox, function (i) {
      // �����å��ܥå������ҤȤĤǤ⳰��Ƥ����硢�����򡿲�������å��ܥå����򿲤���
      if (!$(this).closest('tr').hasClass('selected')) {
        $('#allChecked').prop('checked', false);
      }

      // �����å��ܥå��������٤ƥ����å����줿��硢�����򡿲�������å��ܥå�����Ω�Ƥ�
      if ($(this).closest('tr').hasClass('selected')) {
        ++count_checked;
      }
      if ($all_rows.length === count_checked) {
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
  $.initTableA = function () {
    // �����������å��ܥå���
    $.scanAllCheckbox();
    data = [];
    $.createSkeletonTable(dataEmpty, $tableA_row, domA, $tableA_tbody);
  };

  /**
   * @method setTableSorter �ơ��֥륽���ȵ�ǽ����
   */
  $.setTableSorter = function () {
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

  $.createTable = function (response) {
    data = (response === undefined || response && !response.length) ? dataEmpty : Array.from(new Set(response));

    // �ơ��֥�A����
    $.createSkeletonTable(data, $tableA_row, domA, $tableA_tbody);
    $.addDataTableA();

    // �ơ��֥륽���ȵ�ǽ����
    $.setTableSorter();

    // �����������å��ܥå���
    $.scanAllCheckbox();
  };

  $.createTableRenew = function (response) {
    data = (response === undefined || response && !response.length) ? dataEmpty : Array.from(new Set(response));

    // �ơ��֥�B����
    $.createSkeletonTable(data, $tableB_row, domB, $tableB_tbody);
    $.each(data, function (i) {
      temp.push(data[i]);
    });
    $.addDataTableB();
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
  $(document).on('change', '#allChecked', function (e) {
    e.preventDefault();

    var $all_rows = $('tbody tr', $tableA);
    var $all_rows_checkbox = $('input[type="checkbox"]', $tableA);

    if (e.target.checked) {
      $all_rows.addClass('selected');
      $all_rows_checkbox.not(':disabled').prop('checked', true).closest('tr').children('td').addClass('current');
    } else {
      $all_rows.removeClass('selected');
      $all_rows_checkbox.prop('checked', false).closest('tr').children('td').removeClass('current');
    }
  });

  // �ơ��֥�A �ɲåܥ���
  $('#btnAdd').on('click', function (e) {
    e.preventDefault();

    var $all_rows = $('tbody tr', $tableA);
    var $all_checkbox = $all_rows.find('input[type="checkbox"]');
    var checked = [];

    $.each($all_rows, function () {
      var $isChecked = $(this).find('input[type="checkbox"]').prop('checked');
      checked.push($isChecked);
    });

    // �����å��ܥå������ҤȤĤ����򤵤�Ƥ��ʤ���硢return
    if ($.inArray(true, checked) === -1) return;

    // �����å��ܥå����γ����ǡ����򤹤٤� temp �˳�Ǽ
    $.each($all_checkbox, function () {
      if ($(this).prop('checked')) {
        var $data_id = $(this).closest('tr').data('id');
        var data_index = data.findIndex(function (value) { return value.strslipcode == $data_id });
        if (data_index !== -1) {
          // id��ʣ�����å�
          let sameId = false;
          $.each(temp, function (i, v) {
            if (v.strslipcode == data[data_index].strslipcode) { sameId = true; }
          });
          if (sameId == true) { return; }
          temp.push(data[data_index]);
          // �����ǡ����� data ������
          //data.splice(data_index, 1);
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

    // ��۷׻�
    billingAmount();
  });

  // �ơ��֥�B ����ܥ���
  $('#btnDelete').on('click', function (e) {
    e.preventDefault();

    var $selected_rows = $('tbody tr.selected', $tableB);

    if (!$selected_rows.length) return;

    $.each($selected_rows, function () {
      var $data_id = $(this).data('id');
      var temp_index = temp.findIndex(function (value) { return value.strslipcode == $data_id });

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
    
    // ��۷׻�
    billingAmount();
  });

  // �ơ��֥�B ������ܥ���
  $('#btnAllDelete').on('click', function (e) {
    e.preventDefault();

    var $tableB_row = $('tbody tr', $tableB);
    var count = 0;

    $.each($tableB_row, function (i) {
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
    
    // ��۷׻�
    billingAmount();
  });

  // ����������ϥܥ���
  $('#btnSearchCondition').on('click', function (e) {
    e.preventDefault();

    // selectedRowIndexes �����
    selectedRowIndexes = [];

    var $all_rows = $('tbody tr', $tableA);
    var $all_checkbox = $all_rows.find('input[type="checkbox"]');

    // �����å��ܥå����Υ����å��򤹤٤Ʋ��
    $.each($all_checkbox, function () {
      if ($(this).prop('checked')) {
        $(this).prop('checked', false);
      }
    });

    // ���٤Ƥ� current ����
    $.each($all_rows, function () {
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
  $(document).on('contextmenu', function (e) {
    if (e.which === 1) return false;
  });

  // ctrl, shift �������٥�Ȥ� document ������
  var isCtrlKey = false;
  var isShiftKey = false;

  $(document).on({
    'keydown': function (e) {
      if (e.ctrlKey) isCtrlKey = true;
      if (e.shiftKey) isShiftKey = true;
    },
    'keyup': function (e) {
      isCtrlKey = false;
      isShiftKey = false;
    }
  });

  // �ơ��֥�A ���٥�Ƚ���
  $(document).on('mousedown', '#tableA tbody tr', function (e) {
    e.preventDefault();

    var $tableA_rows = $('#tableA tbody tr');
    var $tableA_rows_length = $tableA_rows.length;

    // �ơ��֥�A <tr> ctrl + click -> �ơ��֥�B�˥ǡ������ɲ�
    if (isCtrlKey && e.which === 1) {
      var $data_id = $(this).data('id');
      var data_index = data.findIndex(function (value) { return value.strslipcode == $data_id });
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

    // �ơ��֥�A <input type="checkbox">
    if (e.target.nodeName === 'INPUT') {
      if ($tableA_rows_length !== 1 && isShiftKey && e.which === 1) {
        var $row_index = $(this).index();

        // ���򤵤줿�ԥ���ǥå����� selectedRowIndexes �˳�Ǽ
        selectedRowIndexes.push($row_index);

        // ���祽����
        selectedRowIndexes.sort(function (a, b) { return a - b });

        var min = selectedRowIndexes[0] + 1;
        var max = selectedRowIndexes[1] - 1;
        var array_range = [];
        for (var i = min; i <= max; ++i) {
          array_range.push(i);
        }

        // ʣ������
        if (!e.target.checked) {
          $(this).addClass('selected').children('td').addClass('current');

          $.each(array_range, function (i, v) {
            $tableA_rows.eq(v).addClass('selected').children('td').addClass('current');
            $tableA_rows.eq(v).find('input[type="checkbox"]').prop('checked', true);
          });
        }
        // ʣ��������
        else {
          $(this).removeClass('selected').children('td').removeClass('current');

          $.each(array_range, function (i, v) {
            $tableA_rows.eq(v).removeClass('selected').children('td').removeClass('current');
            $tableA_rows.eq(v).find('input[type="checkbox"]').prop('checked', false);
          });
        }

        // selectedRowIndexes �����
        if (selectedRowIndexes.length >= 2) selectedRowIndexes = [];
      } else {
        if (!e.target.checked) {
          $(this).addClass('selected').children('td').addClass('current');
        } else {
          $(this).removeClass('selected').children('td').removeClass('current');
        }
      }
    }

    // �ơ��֥�A <tr> shift + click -> ʣ�����򡿲��
    else if ($tableA_rows_length !== 1 && isShiftKey && e.which === 1) {
      var $row_index = $(this).index();

      // ���򤵤줿�ԥ���ǥå����� selectedRowIndexes �˳�Ǽ
      selectedRowIndexes.push($row_index);

      // ���祽����
      selectedRowIndexes.sort(function (a, b) { return a - b });

      // ʣ������
      if (!$(this).hasClass('selected')) {
        $(this).addClass('selected').children('td').addClass('current');
        $(this).find('input[type="checkbox"]').prop('checked', true);

        var i = selectedRowIndexes[0];
        while (i <= selectedRowIndexes[1]) {
          $tableA_rows.eq(i).addClass('selected').children('td').addClass('current');
          $tableA_rows.eq(i).find('input[type="checkbox"]').prop('checked', true);
          ++i;
        }
      }
      // ʣ��������
      else {
        $(this).removeClass('selected').children('td').removeClass('current');
        $(this).find('input[type="checkbox"]').prop('checked', false);

        var i = selectedRowIndexes[0];
        while (i <= selectedRowIndexes[1]) {
          $tableA_rows.eq(i).removeClass('selected').children('td').removeClass('current');
          $tableA_rows.eq(i).find('input[type="checkbox"]').prop('checked', false);
          ++i;
        }
      }

      // selectedRowIndexes �����
      if (selectedRowIndexes.length >= 2) selectedRowIndexes = [];
    }

    // selectedRowIndexes �����
    if ($tableA_rows_length === 1) selectedRowIndexes = [];

    // �����������å��ܥå���
    $.scanAllCheckbox();
  });

  // �ơ��֥�B <tr> �ü쥭�� + click ���٥�Ƚ���
  $(document).on('mousedown', '#tableB tbody tr', function (e) {
    e.preventDefault();

    var $tableB_rows = $('#tableB tbody tr');
    var $tableB_rows_length = $tableB_rows.length;

    // �ơ��֥�B <tr> shift + click -> ʣ�����򡿲��
    if ($tableB_rows_length !== 1 && isShiftKey && e.which === 1) {
      var $row_index = $(this).index();

      // ���򤵤줿�ԥ���ǥå����� selectedRowIndexes �˳�Ǽ
      selectedRowIndexes.push($row_index);

      // ���祽����
      selectedRowIndexes.sort(function (a, b) { return a - b });

      // ʣ������
      if (!$(this).hasClass('selected')) {
        $(this).addClass('selected').children('td').addClass('current');

        var i = selectedRowIndexes[0];
        while (i <= selectedRowIndexes[1]) {
          $tableB_rows.eq(i).addClass('selected').children('td').addClass('current');
          ++i;
        }
      }
      // ʣ��������
      else {
        $(this).removeClass('selected').children('td').removeClass('current');

        var i = selectedRowIndexes[0];
        while (i <= selectedRowIndexes[1]) {
          $tableB_rows.eq(i).removeClass('selected').children('td').removeClass('current');
          ++i;
        }
      }

      // selectedRowIndexes �����
      if (i === selectedRowIndexes[1] + 1) selectedRowIndexes = [];
    }
    else {
      if ($(this).hasClass('selected')) {
        $(this).removeClass('selected').children('td').removeClass('current');
      } else {
        $(this).addClass('selected').children('td').addClass('current');
      }
    }
  });

  function convertNumber(str) {
    if (str != "" && str != undefined && str != "null") {
      return Number(str).toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      });
    } else {
      return "";
    }
  }


  // ��۷׻�
  function billingAmount() {

    // �������ٰ�������
    tableB = $('#tableB');
    tableB_tbody = $('tbody', $tableB);
    tableB_row = $('tbody tr', $tableB);

    // �������ٰ������ꥢ��1���ܤξ�����Ψ���������
    let tax = false;
    for (var i = 0, rowlen = tableB_row.length; i < rowlen; i++) {
      if (tax !== false) continue;
      for (var j = 0, collen = tableB_row[i].cells.length; j < collen; j++) {
        if (tax !== false || !tableB_row[i].cells[j]) continue;
        if (tableB_row[i].cells[j].className == 'tax right') {
          // ������Ψ
          console.log(tableB_row[i].cells[j].innerText);
          strtax = tableB_row[i].cells[j].innerText.replace(/[^0-9]/g, '');
          tax = Number(strtax) / 100;
        }
      }
    }

    // ��������ĳ�
    // Ǽ�������ּ��װ����Ǥ������٤���ȴ��ۤι��+���ι�פ��Ф��Ʋ��Ƕ�ʬ�˱����Ʒ׻����줿������
    let lastMonthBalance = 0;
    let curLastMonthBalance = 0;
    // ���������
    // Ǽ�������ּ��װʹߤǤ������٤���ȴ��ۤι��
    let thisMonthAmount = 0;
    // ������
    // ��������ۤ��Ф��Ʋ��Ƕ�ʬ�˱����Ʒ׻�
    let taxPrice = 0;
    // ������׳�
    // ��������ĳ� + ��������� + ������"
    let noTaxMonthAmount = 0;

    // �ּ��סֻ�פ�׻�����
    // selectClosedDay();

    var chargetern = function () {
      // �ּ��׼���
      let chargeternstart = $('input[name="dtmchargeternstart"]').val();
      let cs = isEmpty(chargeternstart);
      // �ֻ�׼���
      let chargeternend = $('input[name="dtmchargeternend"]').val();
      let ce = isEmpty(chargeternend);

      if (cs == 0 || ce == 0) return false;

      startStamp = new Date(chargeternstart);
      endStamp = new Date(chargeternend);

      for (var i = 0, rowlen = tableB_row.length; i < rowlen; i++) {
        let deliverydate = false;
        let price = false;
        let data = false;

        for (var j = 0, collen = tableB_row[i].cells.length; j < collen; j++) {
          if (!tableB_row[i].cells[j].innerText) continue;
          if (tableB_row[i].cells[j].className == 'deliverydate') {
            // Ǽ����
            deliverydate = tableB_row[i].cells[j].innerText;
          }
          if (tableB_row[i].cells[j].className == 'price right') {

            console.log(tableB_row[i].cells[j].innerText);
            // ��ȴ��
            price = tableB_row[i].cells[j].innerText.replace(/,/g, '');
          }
        }
        console.log(price);
        if (!deliverydate || !price) continue;
        date = splitDate(deliverydate);
        deliverydateStamp = new Date(deliverydate);

        if (deliverydateStamp <= startStamp) {
          // ��������ĳ�
          // lastMonthBalance += Number(price);
        } else {
          // ���������
//          thisMonthAmount += Number(price);
        }
        thisMonthAmount += Number(price);
      }

      // console.log(lastMonthBalance);
      console.log(tax);
      // ��������ĳ�(�����ǹ���)
      // curLastMonthBalance = lastMonthBalance + (lastMonthBalance * (tax * 100)) / 100;
      curLastMonthBalance = Number($('input[name="curlastmonthbalance"]').val().replace(/,/g, ''));
      // �����Ƿ׻�
      // ��������ۤ��Ф��Ʋ��Ƕ�ʬ�˱����Ʒ׻�
      taxPrice = (thisMonthAmount * (tax * 100)) / 100;
      // ������׳�
      // ��������ĳ� + ��������� + ������
      noTaxMonthAmount = curLastMonthBalance + thisMonthAmount + taxPrice;
      // ��̤��˱�
      // $('input[name="curlastmonthbalance"]').val(convertNumber(Math.round(curLastMonthBalance))).change();
      $('input[name="curthismonthamount"]').val(convertNumber(thisMonthAmount)).change();
      $('input[name="curtaxprice"]').val(convertNumber(Math.round(taxPrice))).change();
      $('input[name="notaxcurthismonthamount"]').val(convertNumber(Math.round(noTaxMonthAmount))).change();
    };
    var result = setTimeout(chargetern, 500);

  }

  // �����ͤ�ʸ����ɽ�������
  function isEmpty(val) {
    if (val) {
      return '1';
    } else {
      return '0';
    }
  }

  // �����������դ�����å�������������С�/�פ�ʬ��
  function splitDate(str) {

    // ���եե����ޥå� yyyy/mm(m)/dd(d)����
    var regDate = /(\d{4})\/(\d{1,2})\/(\d{1,2})/;

    // yyyy/mm/dd������
    if (!(regDate.test(str))) {
      return false;
    }

    // ����ʸ����λ���ʬ��
    var regResult = regDate.exec(str);
    var yyyy = regResult[1];
    var mm = regResult[2];
    var dd = regResult[3];
    var di = new Date(yyyy, mm - 1, dd);
    // ���դ�ͭ���������å�
    if (di.getFullYear() == yyyy && di.getMonth() == mm - 1 && di.getDate() == dd) {
      return regResult;
    }

    return false;
  };

});
