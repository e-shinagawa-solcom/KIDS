function promptGoodsCode(){
    var elmProductCode = document.getElementById("ProductCode");
    var elmCustomerReceiveCode = document.getElementById("CustomerReceiveCode");
    var elmGoodsCode = document.getElementById("GoodsCode");
    var elmSessionID = document.getElementById("SessionID");

    // �ܵ����֤�̤����ξ��
    if (elmProductCode.value && !elmGoodsCode.value) {
        // ���ϥ���������ɽ��
        var newgoodscode = window.prompt('�ܵ����֤����Ϥ��Ƥ���������(Ⱦ�ѱѿ��Τ�)', '');

        // ����󥻥벡�������å�
        if (!newgoodscode)
        {
            // ��å���������
            window.alert('���ʥ����ɤ�ɳ�դ��ܵ����֤�ɬ�ܹ��ܤǤ���(������ξ������)');
            return;
        }

        // ���ϥ����å�
        if (!newgoodscode.match(/^[A-Za-z0-9]{1,10}$/)) {
            window.alert('�ܵ����֤�Ⱦ�ѱѿ�����10ʸ����������Ϥ��Ƥ���������');
            elmProductCode.fireEvent('onchange');
            return;
        }

        // �ܵ����֤ι���
        execUpdateGoodsCode(newgoodscode);
    }
}
