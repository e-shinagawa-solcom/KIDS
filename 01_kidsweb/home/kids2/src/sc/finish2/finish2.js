// ��������ɥܥ���β����ѥ�
var imagepath_download_off = "/img/type01/cmn/querybt/listout_off_ja_bt.gif";
var imagepath_download_on  = "/img/type01/cmn/querybt/listout_off_on_ja_bt.gif";

// HTML������ȹ��۸�
function OnloadBody(){
    // ��������ɥܥ���ν������
    var images = document.getElementsByClassName('btn-download');
    for (var i = 0; i < images.length; i++) 
    {
        var image = images[i];
        image.src = imagepath_download_off;
        image.alt ="���������"
    }
}

// ��������ɥܥ���˥ޥ����������뤬��ä��Ȥ�
function OnMouseOverDownload(obj){
    // �ޥ��������С��Ѳ����˺����ؤ���
    obj.src = imagepath_download_on
}
// ��������ɥܥ��󤫤�ޥ����������뤬���줿�Ȥ�
function OnMouseOutDownload(obj){
    // ����������᤹
    obj.src = imagepath_download_off
}

// ��������ɥܥ��󤬲����줿
function OnClickDownload(obj, lngSlipNo, strSlipCode, lngRevisionNo){
    // --------------------------------------------------------------------------
    // ��������ɤΤ������Ʊ��POST
    // 
    // ���͡�jQuery��$.ajax��POST�Ǥϥե������������ɤ����ޤ������ʤ��餷���Τ�
    //      �Ǥ�javascript��Ȥ�
    // --------------------------------------------------------------------------
    // POST�ѥ�᡼�������ꡣ���å����ID�ϱ����ե�����ɤ������
    var postParams = "strMode=download"
                    + "&lngSlipNo=" + lngSlipNo
                    + "&strSlipCode=" + strSlipCode
                    + "&lngRevisionNo=" + lngRevisionNo
                    + "&strSessionID=" + document.getElementById("strSessionID").value
                    ;

    // ��������ɥե�����̾
    var fileName = "KWG" + strSlipCode + ".xlsx";

����// ��Ʊ���ꥯ�����Ȥ�����
    var url = "preview.php"
    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
    xhr.responseType = 'blob'; //blob���Υ쥹�ݥ󥹤�����դ���

    // ������Хå����
    xhr.onload = function (e) {
        // �������ν���
        if (this.status == 200) {
            var blob = this.response;//�쥹�ݥ�
            //IE�Ȥ���¾�ǽ������ڤ�ʬ��
            if (navigator.appVersion.toString().indexOf('.NET') > 0) {
                //IE 10+
                window.navigator.msSaveBlob(blob, fileName);
            } else {
                //a����������
                var a = document.createElement("a");
                //�쥹�ݥ󥹤���Blob���֥������ȡ�URL������
                var blobUrl = window.URL.createObjectURL(new Blob([blob], {
                    type: blob.type
                }));
                //�����������a�����򥢥ڥ��
                document.body.appendChild(a);
                a.style = "display: none";
                //Blob���֥�������URL�򥻥å�
                a.href = blobUrl;
                //��������ɤ�����ե�����̾������
                a.download = fileName;
                //����å����٥��ȯ��
                a.click();
            }
        }
    };

    // ��Ʊ���ꥯ�����Ȥ�����
    xhr.send(postParams);
}
