
$(document).ready(function () {
  var sortval = 0;
  var headersArry = {};
  headersArry[0] = { sorter: false };
  $("#result thead tr").eq(0).find("th").each(function (i, e) {
    var sortkey = $(this).attr("childsortkey");
    if (typeof sortkey !== 'undefined') {
      var cellIndex = $(this)[0].cellIndex;
      headersArry[cellIndex] = { sorter: false };
    }
  });

  $("#result").tablesorter({
    selectorHeaders: '> thead > tr > th',
    headers: headersArry,
  });

  window.resetTable = function () {    
    var widthArry = [];
    var colnum = $("#result thead tr").eq(0).find("th").length;
    // �إå������γ���������������¸����
    for (var i = 1; i <= colnum; i++) {
      var width = $("#result thead tr th:nth-child(" + (i) + ")").eq(0).width();
      widthArry.push(Math.round(width));
    }

    // �������γ���κ�������������������¸����
    var childwidthArray = [];
    var tablechildcount = $(".tablesorter-child thead").length;
    console.log(tablechildcount);
    var detailcolcount = $(".tablesorter-child thead").eq(0).find("tr:first th").length;
    for (var i = 0; i < detailcolcount; i++) {
      var width = 0;
      for (var j = 0; j < tablechildcount; j++) {
        var tmp = Math.round($(".tablesorter-child thead").eq(j).find("tr:first th").eq(i).width());
        if (tmp > width) {
          width = tmp;
        }
      }
      childwidthArray.push(width);
    }

    // �������γ��������ꥻ�åȤ���
    var width = 0;
    var endindex = 0;
    $(".tablesorter-child thead").eq(0).find("tr:first th").each(function (i, e) {
      var index1 = $(this)[0].cellIndex;
      var index2 = $(".tablesorter-child").parent()[0].cellIndex;
      var index3 = index1 + index2 + 1;
      console.log(index3);
      if (endindex == 0) {
        endindex = index3;
      }
      var child_width = childwidthArray[index1] + 20;
      var parent_width = widthArry[index3 - 1] + 20;
      if (child_width > widthArry[index3 - 1]) {
        $("#result thead tr th:nth-child(" + (index3) + ")").eq(0).width(child_width);
        $(".tablesorter-child tbody tr td:nth-child(" + (index1 + 1) + ")").width(child_width);
        widthArry[index3 - 1] = child_width;
      } else {
        $("#result thead tr th:nth-child(" + (index3) + ")").eq(0).width(parent_width);
        $(".tablesorter-child tbody tr td:nth-child(" + (index1 + 1) + ")").width(parent_width);
        widthArry[index3 - 1] = parent_width;
      }
    });

    // �ơ��֥������ꥻ�åȤ���
    var parent_width = 0;
    for (var i = 0; i < widthArry.length; i++) {
      $("#result thead tr th:nth-child(" + (i + 1) + ")").eq(0).width(widthArry[i]);
      parent_width += widthArry[i];
    }
    $("#result").width(parent_width);

    // �������ƥơ��֥�Υإå�������ɽ�������ꤹ��
    $(".tablesorter-child thead").css('display', 'none');
    // �ơ��֥�Υ쥤�����Ȥ���ꤹ��
    $(".tablesorter-child").css('table-layout', 'fixed');
    $("#result").css('table-layout', 'fixed');
  }

  // �ơ��֥�ꥻ�åȤθƤӽФ�
  resetTable();

  $("#result thead tr").eq(0).find("th").on('click', function () {
    var r = $('.tablesorter-child').tablesorter();
    var sortkey = $(this).attr("childsortkey");
    console.log(sortkey);
    if (typeof sortkey !== 'undefined') {
      if (sortval == 1) {
        sortval = 0;
      } else {
        sortval = 1;
      }
      r.trigger('sorton', [[[(sortkey - 1), sortval]]]);
    }

    // ���ԡ��ܥ���Υ��٥��
    if ($(this)[0].cellIndex == 0) {
        // ����åץܡ��ɤ��ͤ�ȿ��
        if (window.getSelection) {
            var selection = getSelection();
            console.log(selection);
            selection.removeAllRanges();
            var range = document.createRange();
            
            console.log(document.getElementById("result"));
            range.selectNodeContents(document.getElementById("result"));
            selection.addRange(range);
            document.execCommand('copy');
            selection.removeAllRanges();
            alert('����åץܡ��ɤ˥��ԡ����ޤ�����');
        } else {
            alert("����åץܡ��ɤؤΥ��ԡ��˼��Ԥ��ޤ�����");
        }
    }
  });
});
