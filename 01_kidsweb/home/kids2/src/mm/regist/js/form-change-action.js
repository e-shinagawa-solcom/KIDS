// Ģɼ��ʬ���Ѵ��Ǥξ�硢�ݴɹ��졢��ư�������̵���ˤ���
$('select[name="Status"]').on({
    'change': function(){
        if ( $(this).prop('selectedIndex') == 0 | $(this).prop('selectedIndex') == 1 | $(this).prop('selectedIndex') == 2 ) {
            $('input[name="SourceFactory"]').prop('disabled', false).css('opacity', 1);
            $('input[name="SourceFactoryName"]').prop('disabled', false).css('opacity', 1);
            $('input[name="DestinationFactory"]').prop('disabled', false).css('opacity', 1);
            $('input[name="DestinationFactoryName"]').prop('disabled', false).css('opacity', 1);
            $('input[name="DestinationFactoryName"] + .msw-button').prop('disabled', false).css('opacity', 1);
        } else if ( $(this).prop('selectedIndex') == 3 ) {
            $('input[name="SourceFactory"]').prop('disabled', true).css('opacity', 0.3);
            $('input[name="SourceFactoryName"]').prop('disabled', true).css('opacity', 0.3);
            $('input[name="DestinationFactory"]').prop('disabled', true).css('opacity', 0.3);
            $('input[name="DestinationFactoryName"]').prop('disabled', true).css('opacity', 0.3);
            $('input[name="DestinationFactoryName"] + .msw-button').prop('disabled', true).css('opacity', 0.3);
        }
    }
});

// ���ʥ������ѹ����ݴɹ����ꥻ�å�
$('input[name="ProductCode"]').on({
    'change': function(){
        // �ݴɹ���ꥻ�å�
        $('input[name="SourceFactory"]').val('').trigger('change');
        $('input[name="SourceFactoryName"] + img').css('visibility', 'hidden');
    }
});
