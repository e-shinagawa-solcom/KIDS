// ---------------------------------------------------
// !!�����Х�!!
// ---------------------------------------------------
// �������̤ˤơ�ɽ�����ܤΥ����å����֤�COOKIE����¸����
function saveCookieDispayItems(form){
    // ��¸����(Ŭ��)
    var expires = 1095;
    // ��¸�оݤΥ����å��ܥå������Ǥμ���
    var items = $(form).find('input[type="checkbox"][name^="IsDisplay_"]');
    // COOKIE keyname
    var keyname = $(form).attr('name') + '_IsDisplay';
    // ɽ���ե饰����
    var flags = '';
    // ���ڤ�ʸ��
    var delimiter = '&';
    var sepalator = ':';

    // ���ǿ�ʬ����
    items.each(function(){
        flags += this.name + sepalator + this.checked + delimiter
    });

    // COOKIE����¸
    $.cookie(keyname , flags.substr(0, flags.length-1), {
        'expires':��expires
    });
}
// �������̤ˤơ�ɽ�����ܤΥ����å����֤���¸���줿COOKIE������������
function restoreCookieDispayItems(form){
    // ��¸�оݤΥ����å��ܥå������Ǥμ���
    var items = $(form).find();
    // COOKIE keyname
    var keyname = $(form).attr('name') + '_IsDisplay';
    // ɽ���ե饰����
    var flags = $.cookie(keyname);
    // ���ڤ�ʸ��
    var delimiter = '&';
    var sepalator = ':';

    if (flags)
    {
        var conditions = flags.split(delimiter);

        $.each(conditions, function(){
            var sep = this.split(sepalator);
            var name = sep[0];
            var value = sep[1];
            var target = $(form).find('input[type="checkbox"][name^="' + name + '"]');

            switch (value) {
                case "true":
                    target.attr('checked', true);
                    break;
                case "false":
                    target.attr('checked', false);
                    break;
                default:
                    break;
            }
        });
    }
}
