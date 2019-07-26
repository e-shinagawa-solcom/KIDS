// データの取得
var script = $('#script').attr('data-param');
var result = JSON.parse(script);

// シート数の取得
var sheetNumber = Object.keys(result).length;
console.log(result);

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

  // セルのクラスを取得
  var cellClass = sheetData['cellClass'];

  // セルに格納するパラメータを取得
  var data = cellValue;

  var gridId = 'grid' + sheetNum;

  grid[sheetNum] = document.getElementById(gridId);

  // Handsontableでタグに表を埋め込む
  table[sheetNum] = new Handsontable(grid[sheetNum], {
    data: data,
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
  });
}

// HandsontableのtdタグCSS
function firstRenderer(instance, td, row, col, prop, value, cellProperties) {
  Handsontable.renderers.TextRenderer.apply(this, arguments);
  var cellInfoData = cellData[row][col];
  var rowHeightCell = rowHeight[row]  + 'px';
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
  td.style.height = rowHeightCell;
  td.style.padding = '2px';
}