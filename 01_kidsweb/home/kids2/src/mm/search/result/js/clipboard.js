// ���ԡ��ܥ���
$('img.copy').on({
    'click': function () {
        // ����åץܡ��ɤ��ͤ�ȿ��
        if (window.getSelection) {
            var selection = getSelection();
            selection.removeAllRanges();
            var range = document.createRange();
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
