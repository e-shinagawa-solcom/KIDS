
(function(){

    // �ޥ�����������
    var searchMaster = {
                    url: '/mold/lib/queryMasterData.php?strSessionID=' + $.cookie('strSessionID'),
                    type: 'post',
                    dataType: 'json'
                };

    // --------------------------------------------------------------------------
    // ���٥����Ͽ
    // --------------------------------------------------------------------------
    // �ݴɸ�����/��ư�蹩��-ɽ����ҥ����� ���٥����Ͽ
    $('input[name="SourceFactory"], input[name="DestinationFactory"]').on({
        'change': function(){
            // ɽ��̾�����
            selectFactoryName($(this));
            // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
            $(this).trigger('blur');
            // �ե���������ɽ�����̾�˹�碌��
            $(this).next('input').focus();
        }
    });

    // --------------------------------------------------------------------------
    // ����-ɽ����ҥ����ɤˤ��ǡ�������
    // --------------------------------------------------------------------------
    // ����-ɽ����ҥ����ɤ���ɽ��̾�����
    var selectFactoryName =  function(invoker){
        console.log("����-ɽ����ҥ�����->ɽ��̾ change");
        // ������̤Υ��å���CSS���쥯���κ���
        var targetCssSelector = 'input[name="' + $(invoker).attr('name') + 'Name"]';
        // �������0��λ��Υ��������CSS���쥯���κ���
        var targetCodeCssSelector = 'input[name="' + $(invoker).attr('name') +'"]';

        // �������
        var condition = {
            data: {
                QueryName: 'selectFactoryName',
                Conditions: {
                    CompanyDisplayName: $(invoker).val()
                }
            }
        };

        // �ꥯ����������
        $.ajax($.extend({}, searchMaster, condition))
        .done(function(response){
            console.log("����-ɽ����ҥ�����->ɽ��̾ done");
            // ����-ɽ��̾���ͤ򥻥å�
            $(targetCssSelector).val(response[0].companydisplayname);
            if ($(invoker).attr('name')=="SourceFactory") {
                $('input[name="SourceFactoryName"] + img').css('visibility', 'hidden');
            }
        })
        .fail(function(response){
            console.log("����-ɽ����ҥ�����->ɽ��̾ fail");
            console.log(response.responseText);            
            var listlength = $('.mold-selection__choosen-list').find('option').length;
            if ($(invoker).attr('name')=="SourceFactory") {
                if (listlength > 0) {
                    $('input[name="SourceFactoryName"] + img').css('visibility', 'visible');
                } else {
                    $('input[name="SourceFactoryName"] + img').css('visibility', 'hidden');
                }
            }
            // ����-�����ɡ�ɽ��̾���ͤ�ꥻ�åȤ�����������˥ե�������
            $(targetCssSelector).val('');
            $(targetCodeCssSelector).val('').focus();
        });
    };

})();
