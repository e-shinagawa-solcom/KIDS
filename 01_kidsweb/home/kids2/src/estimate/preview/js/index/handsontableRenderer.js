// handsontable�����ؿ�

$(function(){
  // �ǡ����μ���
  var script = $('#script').attr('data-param');
  var result = JSON.parse(script);

  // �����ȿ��μ���
  var sheetNumber = Object.keys(result).length;

  var grid = [];
  var table = [];

  // ɽ���ѥǡ����κ���
  for (var sheetNum = 0; sheetNum < sheetNumber; sheetNum++) {
    var sheetData = result[sheetNum];
    // ���Ϲ��󡢽�λ����μ���
    var startRow = sheetData['startRow'];
    var endRow = sheetData['endRow'];
    var startColumn = sheetData['startColumn'];
    var endColumn = sheetData['endColumn'];

    // ����Υǡ�������
    var cellData = sheetData['cellData'];

    var cellValue = [];

    // ����ξ��������˳�Ǽ����
    for (var i = startRow; i <= endRow; i++) {
      var rowValue = [];
      for (var j = startColumn; j <= endColumn; j++) {
        rowValue.push(cellData[i][j]['value']);
      }
      cellValue.push(rowValue);
    }

    // �ޡ�������μ���
    var merge = sheetData['mergedCellsList'];
    // �Ԥι⤵������������
    var rowHeight = sheetData['rowHeight'];
    var columnWidth = sheetData['columnWidth'];

    var height = window.outerHeight;

    var gridWidth = 0;
    for (var i = 0; i < columnWidth.length; i++) {
      gridWidth += columnWidth[i];
    }

    var width = gridWidth + 30;

    // ������ɥ��Υꥵ����
    window.resizeTo(width, height);

    // ����Υ��饹�����
    var cellClass = sheetData['cellClass'];

    // �����ֹ�ꥹ�Ȥ����
    var receiveNoList = sheetData['receiveNoList'];

    // ȯ���ֹ�ꥹ�Ȥ����
    var orderNoList = sheetData['orderNoList'];

    var gridId = 'grid' + sheetNum;

    grid[sheetNum] = document.getElementById(gridId);

    // Handsontable�ǥ�����ɽ��������
    table[sheetNum] = new Handsontable(grid[sheetNum], {
      data: cellValue,
      fillHandle: false,
      undo: false,
      rowHeights: rowHeight,
      colWidths: columnWidth,
      cell: cellClass,
      cells: function (row, col, prop) {
        var cellProperties = {};
        cellProperties.renderer = firstRenderer;
        return cellProperties;
      },
      readOnly: true,
      mergeCells: merge,
      outsideClickDeselects: false,
    });
  }

  // Handsontable�Υ����ƥ��ֲ��Τ��ᡢtable�κ����������֤ˤ���
  table[0].selectCell(0, 0);
  table[0].deselectCell();

  
  // var cellString = JSON.stringify(cellClass);
  // console.log(cellString);
  

  // Handsontable��td����CSS
  function firstRenderer(instance, td, row, col, prop, value, cellProperties) {
    if (col <= 2) {
      Handsontable.renderers.HtmlRenderer.apply(this, arguments);  //���ץ�ӥ塼�γ�ǧ�����������
    } else {
      Handsontable.renderers.TextRenderer.apply(this, arguments);
    }  
    var cellInfoData = cellData[row][col];
    var emphasis = cellInfoData['emphasis'];
    var border = cellInfoData['border'];
    var background = '#' + cellInfoData['backgroundColor'];
    var fontSize = (parseInt(cellInfoData['fontSize']) + 1) + 'px';
    var fontFamily = cellInfoData['fontFamily'];
    var fontColor = '#' + cellInfoData['fontColor'];  
    var verticalAlign = cellInfoData['verticalPosition'];
    var textAlign =  cellInfoData['horizontalPosition'];
    var boldStyle = emphasis['bold'] == true ? 'bold' : 'normal';
    var italicStyle = emphasis['italic'] == true ? 'italic' : 'normal';
    var borderColor = '#' + border['top']['color'] + ' #' + border['right']['color'] + ' #' + border['bottom']['color'] + ' #' + border['left']['color'];
    var borderStyle = border['top']['style'] + ' ' + border['right']['style'] + ' ' + border['bottom']['style'] + ' ' + border['left']['style'];
    var borderWidth = border['top']['width'] + ' ' + border['right']['width'] + ' ' + border['bottom']['width'] + ' ' + border['left']['width'];
    var clsRow = 'row' + row;
    var clsCol = 'col' + col;
    
    td.style.background = background;
    td.style.fontSize = fontSize;
    td.style.fontFamily = fontFamily;
    td.style.color = fontColor;
    td.style.verticalAlign = verticalAlign;
    td.style.textAlign = textAlign;
    td.style.fontWeight = boldStyle;
    td.style.fontStyle = italicStyle;
    td.style.borderColor = borderColor;
    td.style.borderStyle = borderStyle;
    td.style.borderWidth = borderWidth;
    td.style.padding = '2px';
    td.classList.add(clsRow);
    td.classList.add(clsCol);
  }
  
  /**
   * ���ꤷ���Ԥ���������ĥ���Υ��饹������������
   * @param {integer} row �����
   * @param {integer} col ������
   * 
   * @return {Array.object} elements ���饹���󥪥֥������Ȥ��������
   */
  function getElementsForRowAndColumn(row, col, searchList) {
    var filter = {
      row: row,
      col: col
    }
    var elements = searchList.filter(function(item) {
      for (var key in filter) {
        if (item[key] === undefined || item[key] != filter[key])
          return false;
      }
      return true;
    });
    return elements;
  }

  // ����������˥����å��ܥå����ξ��֤��ݻ����뤿��δؿ��ʳ����
  $(document).on('change', '.checkbox_applicate', function() {
    var row = $(this).parents('td').attr('class').match(/\srow(\d+)\s/);
    var col = $(this).parents('td').attr('class').match(/\scol(\d+)\s/);
    var rowNo = row[1];
    var colNo = col[1];
    var status = $(this).prop('checked');
    if (status === true) {
      cellValue[rowNo][colNo] = cellValue[rowNo][colNo].replace('></div>', ' checked=\"checked\"></div>');
    } else {
      cellValue[rowNo][colNo] = cellValue[rowNo][colNo].replace(' checked=\"checked\"', '');
    }
  });

  // ����������˥����å��ܥå����ξ��֤��ݻ����뤿��δؿ�(���)
  $(document).on('change', '.checkbox_cancel', function() {
    var row = $(this).parents('td').attr('class').match(/\srow(\d+)\s/);
    var col = $(this).parents('td').attr('class').match(/\scol(\d+)\s/);
    var rowNo = row[1];
    var colNo = col[1];
    var status = $(this).prop('checked');
    if (status === true) {
      cellValue[rowNo][colNo] = cellValue[rowNo][colNo].replace('></div>', ' checked=\"checked\"></div>');
    } else {
      cellValue[rowNo][colNo] = cellValue[rowNo][colNo].replace(' checked=\"checked\"', '');
    }
  });

  // ����������
  $(document).on('click', '.btn_confirm_receive', function() {
    var searchName = $(this).val();

    var confirmCol = 0;
    var buttonMatch = "name=\"" + searchName + "\"";
    var checkedMatch = "checked=\"checked\"";

    var ret = [];

    var beforeValue = '';

    var className = 'customerCompany';

    var searchList = cellClass;

    var check = true;

    // ����˳�Ǽ���줿html��ʸ�Ϥ����оݤΥ����å��ܥå������������
    // handsontable�����̳���ɽ��������ʤ�(= ���̳���ɽ�ˤ�html��¸�ߤ��Ƥ��ʤ�)���ᡢhtml���Ǥ���μ����ϹԤäƤ��ʤ�)
    cellValue.forEach(function(value, row) {
      // �ܥ�����б���������å��ѤΥ����å��ܥå��������
      if (value[confirmCol].match(buttonMatch) && value[confirmCol].match(checkedMatch)) {    
        var col = getColumnForRowAndClassName(row, className, searchList);
        if (beforeValue === '') {
          beforeValue = value[col];
        } else if (beforeValue !== value[col]) {
          check = false;
        }
        ret.push(value);
      }
    });
    
    if (check === false) {
      alert('ʣ���θܵ��������Ǥ��ޤ���');
      return false;
    }
    
    var target = ret.map(function(value) { // �����å��ܥå������б������������ȯ���ֹ�������������޶��ڤ��ʸ��������
      var ret = value[confirmCol].match(/value=\"(\d+)\"/);
      return ret[1];
    }).join(",");

    if (target) {
      var text = '���򤵤줿�������ꤷ�ޤ���\n������Ǥ�����';
      var result = confirm(text);
      if (result) {
        var sessionID = $('input[name="strSessionID"]').val();

        var baseUrl = "/so/decide/index.php"
        var numberKey = 'lngReceiveNo';

        var actionUrl = baseUrl + "?" + numberKey + "=" + target + "&strSessionID=" + sessionID;
        var windowName = 'window_confirm';
        var win = window.open(actionUrl, windowName, 'scrollbars=yes, width=1000, height=700, resizable=0 location=0');

        win.onload = function() {
          $(win).on('unload', function(){
            location.reload();
          });          
        }

        return false;

      } else {
        return false;
      }
    } else {
      alert('�о����٤����򤵤�Ƥ��ޤ���');
    }
  });

  // ȯ��������
  $(document).on('click', '.btn_confirm_order', function() {
    var searchName = $(this).val();

    var confirmCol = 0;
    var buttonMatch = "name=\"" + searchName + "\"";
    var checkedMatch = "checked=\"checked\"";

    var ret = [];

    var beforeValue = '';

    var className = 'customerCompany';

    var searchList = cellClass;

    var check = true;

    // ����˳�Ǽ���줿html��ʸ�Ϥ����оݤΥ����å��ܥå������������
    // handsontable�����̳���ɽ��������ʤ�(= ���̳���ɽ�ˤ�html��¸�ߤ��Ƥ��ʤ�)���ᡢhtml���Ǥ���μ����ϹԤäƤ��ʤ�)
    cellValue.forEach(function(value, row) {
      // �ܥ�����б���������å��ѤΥ����å��ܥå��������
      if (value[confirmCol].match(buttonMatch) && value[confirmCol].match(checkedMatch)) {    
        var col = getColumnForRowAndClassName(row, className, searchList);
        if (beforeValue === '') {
          beforeValue = value[col];
        } else if (beforeValue !== value[col]) {
          check = false;
        }
        ret.push(value);
      }
    });
    
    if (check === false) {
      alert('ʣ���λ����������Ǥ��ޤ���');
      return false;
    }
    
    var target = ret.map(function(value) { // �����å��ܥå������б������������ȯ���ֹ�������������޶��ڤ��ʸ��������
      var ret = value[confirmCol].match(/value=\"(\d+)\"/);
      return ret[1];
    }).join(",");

    if (target) {
      var text = '���򤵤줿ȯ�����ꤷ�ޤ���\n������Ǥ�����';
      var result = confirm(text);
      if (result) {
        var sessionID = $('input[name="strSessionID"]').val();

        var baseUrl = "/po/regist/index.php"
        var numberKey = 'lngOrderNo';

        var actionUrl = baseUrl + "?" + numberKey + "=" + target + "&strSessionID=" + sessionID;
        var windowName = 'window_confirm';
        var win = window.open(actionUrl, windowName, 'scrollbars=yes, width=1000, height=700, resizable=0 location=0');

        win.onload = function() {
          $(win).on('unload', function(){
            location.reload();
          });          
        }
        
        return false;

      } else {
        return false;
      }
    } else {
      alert('�о����٤����򤵤�Ƥ��ޤ���');
    }
  });

  // ȯ���ý���
  $(document).on('click', '.btn_cancel_order', function() {
    var searchName = $(this).val();

    var cancelCol = 1;
    var buttonMatch = "name=\"" + searchName + "\"";
    var checkedMatch = "checked=\"checked\"";

    // ����˳�Ǽ���줿html��ʸ�Ϥ����оݤΥ����å��ܥå������������
    // handsontable�����̳���ɽ��������ʤ�(= ���̳���ɽ�ˤ�html��¸�ߤ��Ƥ��ʤ�)���ᡢhtml���Ǥ���μ����ϹԤäƤ��ʤ�)
    var target = cellValue.filter(function(value) {
      // �ܥ�����б���������å��ѤΥ����å��ܥå��������
      if (value[cancelCol].match(buttonMatch) && value[cancelCol].match(checkedMatch)) {
        return value;
      }
    }).map(function(value) { // �����å��ܥå������б������������ȯ���ֹ�������������޶��ڤ��ʸ��������
      var ret = value[cancelCol].match(/value=\"(\d+)\"/);
      return ret[1];
    });

    var count = target.length;

    if (count === 1) {
      var text = '���򤵤줿ȯ��γ������ä��ޤ���\n������Ǥ�����';
      var result = confirm(text);
      if (result) {
        var sessionID = $('input[name="strSessionID"]').val();

        var baseUrl = "/po/result/index3.php"
        var numberKey = 'lngOrderNo';

        var actionUrl = baseUrl + "?" + numberKey + "=" + target[0] + "&strSessionID=" + sessionID;
        var windowName = 'window_confirm';
        var win = window.open(actionUrl, windowName, 'scrollbars=yes, width=1000, height=700, resizable=0 location=0');

        win.onload = function() {
          $(win).on('unload', function(){
            location.reload();
          });          
        }
        
        return false;

      } else {
        return false;
      }
    } else if (count > 1) {
      alert('ʣ�����٤�����ϤǤ��ޤ���');
    } else {
      alert('�о����٤����򤵤�Ƥ��ޤ���');
    }
  });


  /**
   * ���ꤷ���Ԥ��������Υ��饹̾���������������
   * @param {integer} row �����
   * @param {integer} className ���饹̾
   * 
   * @return {Array.object} col ���ֹ�
   */
  function getColumnForRowAndClassName(row, className, searchList) {
    var filter = {
      row: row,
    }
    
    var elements = searchList.filter(function(item) {
      for (var key in filter) {
        if (item[key] === undefined || item[key] != filter[key])
          return false;
      }
      return true;
    });
    
    for (var i = 0; i < elements.length; i++) {
      if (elements[i].className.includes(className)) {
        var col = elements[i].col;
        break;
      }
    }

    return col;      
  }

});
