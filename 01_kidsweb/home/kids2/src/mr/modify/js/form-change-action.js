// �ֵ�ͽ������������֤Ǥ���������
$('input[name="ReturnSchedule"]')
.prop({ disabled: true })
.css({ opacity: 0.3 });
// ������ν������ݴɹ�����ֵѤξ�硢�ֵ�ͽ�������ͭ���ˤ���
$('select[name="FinalKeep"]').on({
    'change': function(){
        $('input[name="ReturnSchedule"]').trigger('blur');
        var chk = $(this).prop('selectedIndex'),
            rtnDay = $('input[name="ReturnSchedule"]');
        (chk == 2) ? rtnDay.prop('disabled', false).css('opacity', 1) : rtnDay.prop('disabled', true).css('opacity', 0.3);
    }
});

// Ģɼ��ʬ���Ѵ��Ǥξ�硢��ư��ˡ��������ν������ݴɹ��졢��ư�������̵���ˤ���
$('select[name="ReportCategory"]').on({
    'change': function(){
        if ( $(this).prop('selectedIndex') == 0 | $(this).prop('selectedIndex') == 1 | $(this).prop('selectedIndex') == 2 ) {
            $('select[name="TransferMethod"]').prop('disabled', false).css('opacity', 1);
            $('select[name="FinalKeep"]').prop({'disabled': false, 'selectedIndex': 0}).css('opacity', 1);
            $('input[name="SourceFactory"]').prop('disabled', false).css('opacity', 1);
            $('input[name="SourceFactoryName"]').prop('disabled', false).css('opacity', 1);
            $('input[name="DestinationFactory"]').prop('disabled', false).css('opacity', 1);
            $('input[name="DestinationFactoryName"]').prop('disabled', false).css('opacity', 1);
            $('input[name="DestinationFactoryName"] + .msw-button').prop('disabled', false).css('opacity', 1);
            // ������ν����˱������ֵ�ͽ������ͭ��̵��������
            $('select[name="FinalKeep"]').trigger('change');

        } else if ( $(this).prop('selectedIndex') == 3 ) {
            $('select[name="TransferMethod"]').prop('disabled', true).css('opacity', 0.3);
            $('select[name="FinalKeep"]').prop({'disabled': true, 'selectedIndex': 0}).css('opacity', 0.3);
            $('input[name="ReturnSchedule"]').prop('disabled', true).css('opacity', 0.3);
            $('input[name="SourceFactory"]').prop('disabled', true).css('opacity', 0.3);
            $('input[name="SourceFactoryName"]').prop('disabled', true).css('opacity', 0.3);
            $('input[name="DestinationFactory"]').prop('disabled', true).css('opacity', 0.3);
            $('input[name="DestinationFactoryName"]').prop('disabled', true).css('opacity', 0.3);
            $('input[name="DestinationFactoryName"] + .msw-button').prop('disabled', true).css('opacity', 0.3);
            // �ֵ�ͽ������validation���å�
            $('select[name="FinalKeep"]').trigger('change');
        }
    }
});

// ���ʥ������ѹ����ݴɹ���ȶⷿ�����ơ��֥��ꥻ�å�
$('input[name="ProductCode"]').on({
    'change': function(){
        // �ݴɹ���ꥻ�å�
        $('input[name="SourceFactory"]').val('');
        // �ⷿ�����ơ��֥��۲���TBODY���Ǥλ����Ǥ���
        $('form[name="RegistMoldReport"]').find('table.table-description').find('tbody').children().remove();
    }
});
