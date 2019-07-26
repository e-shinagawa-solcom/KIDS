// �ǡ����μ���
var script = $('#script').attr('data-param');
var result = JSON.parse(script);

// �����ȿ��μ���
var sheetNumber = Object.keys(result).length;
console.log(result);

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

  // ����Υ��饹�����
  var cellClass = sheetData['cellClass'];

  // ����˳�Ǽ����ѥ�᡼�������
  var data = cellValue;

  var gridId = 'grid' + sheetNum;

  grid[sheetNum] = document.getElementById(gridId);

  // Handsontable�ǥ�����ɽ��������
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

// Handsontable��td����CSS
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