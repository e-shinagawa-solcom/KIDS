// ����åץܡ��ɽ������饹
var excludeClassName = ".exclude-in-clip-board-target";
// �оݥơ��֥�
var table = $('table#result');
// �إå�����
var headers = table.find('th:not(' + excludeClassName + ')');
// �ơ��֥�쥳����
var records = table.find('tbody > tr');
// ���ڤ�ʸ��
var delimiter = '\t';
var brReplacement = ',';

// br�ִ�����ڤ�ʸ������������ѥѥ�����
eval('var patternLastBrReplacement = /' + brReplacement +'+$/');

// ���ԡ��ܥ���
$('img.copy').on({
    'click': function(){
        // ����åץܡ��ɤ�ȿ�Ǥ�����ʸ����
        var contents = "";

        // �إå��Ԥμ���
        headers.each(function(){
            contents += $(this).children('div').get(0).innerHTML + delimiter;
        });
        // ����
        contents += '\r\n';

        // �ǡ����Ԥμ���
        records.each(function(){
            var cells = $(this).find('td:not(' + excludeClassName + ')');

            cells.each(function(){
                contents += this.innerHTML;
                contents += delimiter;
            });
            // ����
            contents += '\r\n'
        });

        // ����åץܡ��ɤ��ͤ�ȿ��
        if (window.clipboardData.setData('Text', contents)){
            alert("����åץܡ��ɤ˸�����̤򥳥ԡ����ޤ�����");
        }
        else {
            alert("����åץܡ��ɤؤΥ��ԡ��˼��Ԥ��ޤ�����");
        }
    }
});
