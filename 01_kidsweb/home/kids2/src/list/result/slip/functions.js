function fncDownload(url) {
    // �ư��������å��ܥå����ξ��֤����
    var reprintFlag = parent.button.document.getElementById('rePrintChk').checked;
    // URL����
    url = url + '&reprintFlag=' + reprintFlag;
    // �ڡ�������
    location.href = url;
    // �����÷в�塢�Ʋ��̥�����
    setTimeout("alert('30�÷в�');parent.window.close();", 30000);

    return false;

}