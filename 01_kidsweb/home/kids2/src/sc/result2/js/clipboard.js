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

// Ǥ�դ�ʸ����򥯥�åץܡ��ɤ˥��ԡ�����
function execCopy(string){

    // ��div ����
    var tmp = document.createElement("div");
    // �����ѤΥ�������
    var pre = document.createElement('pre');
  
    // �����Ǥ�CSS�� user-select: none ���ȥ��ԡ��Ǥ��ʤ��Τǽ񤭴�����
    pre.style.webkitUserSelect = 'auto';
    pre.style.userSelect = 'auto';
  
    tmp.appendChild(pre).textContent = string;
  
    // ���Ǥ���̳���
    var s = tmp.style;
    s.position = 'fixed';
    s.right = '200%';
  
    // body ���ɲ�
    document.body.appendChild(tmp);
    // ���Ǥ�����
    document.getSelection().selectAllChildren(tmp);
  
    // ����åץܡ��ɤ˥��ԡ�
    var result = document.execCommand("copy");
  
    // ���Ǻ��
    document.body.removeChild(tmp);
  
    return result;
 };
 
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
                contents += this.innerHTML + delimiter;
            });
            // ����
            contents += '\r\n'
        });

        // ����åץܡ��ɤ��ͤ�ȿ��
        // ��¸�����ɤ�IE�Ǥ���ư���ʤ�����������ѹ���2019/8/31 T.Miyata��
        //if (window.clipboardData.setData('Text', contents)){
        //    alert("����åץܡ��ɤ˸�����̤򥳥ԡ����ޤ�����");
        //}
        //else {
        //   alert("����åץܡ��ɤؤΥ��ԡ��˼��Ԥ��ޤ�����");
        //}
        if (execCopy(contents)){
            alert("����åץܡ��ɤ˸�����̤򥳥ԡ����ޤ�����");
        }else{
            alert("����åץܡ��ɤؤΥ��ԡ��˼��Ԥ��ޤ�����");
        }

    }

    
});
