// handsontableの操作関数

$(function(){
  // データの取得
  var script = $('#script').attr('data-param');
  var result = JSON.parse(script);

  // シート数の取得
  var sheetNumber = Object.keys(result).length;

  var grid = [];
  var table = [];

  // 表示用データの作成
  for (var sheetNum = 0; sheetNum < sheetNumber; sheetNum++) {
    var sheetData = result[sheetNum];
    // 開始行列、終了行列の取得
    var startRow = sheetData['startRow'];
    var endRow = sheetData['endRow'];
    var startColumn = sheetData['startColumn'];
    var endColumn = sheetData['endColumn'];

    // セルのデータ取得
    var cellData = sheetData['cellData'];

    var cellValue = [];

    // セルの情報を配列に格納する
    for (var i = startRow; i <= endRow; i++) {
      var rowValue = [];
      for (var j = startColumn; j <= endColumn; j++) {
        rowValue.push(cellData[i][j]['value']);
      }
      cellValue.push(rowValue);
    }

    // マージセルの取得
    var merge = sheetData['mergedCellsList'];
    // 行の高さ、列の幅を取得
    var rowHeight = sheetData['rowHeight'];
    var columnWidth = sheetData['columnWidth'];

    var height = window.outerHeight;

    var gridWidth = 0;
    for (var i = 0; i < columnWidth.length; i++) {
      gridWidth += columnWidth[i];
    }

    var width = gridWidth + 30;

    // ウィンドウのリサイズ
    window.resizeTo(width, height);

    // セルのクラスを取得
    var cellClass = sheetData['cellClass'];

    // 受注番号リストを取得
    var receiveNoList = sheetData['receiveNoList'];

    // 発注番号リストを取得
    var orderNoList = sheetData['orderNoList'];

    var gridId = 'grid' + sheetNum;

    grid[sheetNum] = document.getElementById(gridId);

    // Handsontableでタグに表を埋め込む
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

  // Handsontableのアクティブ化のため、tableの左上を選択状態にする
  table[0].selectCell(0, 0);
  table[0].deselectCell();


  // HandsontableのtdタグCSS
  function firstRenderer(instance, td, row, col, prop, value, cellProperties) {
    if (col <= 2) {
      Handsontable.renderers.HtmlRenderer.apply(this, arguments);  //　プレビューの確認、取消設置用
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
