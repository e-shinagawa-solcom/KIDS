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
});
