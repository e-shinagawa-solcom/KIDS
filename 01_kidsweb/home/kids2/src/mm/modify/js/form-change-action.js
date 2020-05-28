// 金型ステータスが廃棄の場合、保管工場、移動工場欄を無効にする
$('select[init-value]').on({
    'change': function(){
        if ( $(this).prop('selectedIndex') == 0 | $(this).prop('selectedIndex') == 1 | $(this).prop('selectedIndex') == 2 ) {
            $('input[name="SourceFactory"]').prop('disabled', false).css('opacity', 1);
            $('input[name="SourceFactoryName"]').css('opacity', 1);
            $('input[name="SourceFactoryName"]').next('span').css('display',  '');
            $('input[name="DestinationFactory"]').prop('disabled', false).css('opacity', 1);
            $('input[name="DestinationFactoryName"]').css('opacity', 1);
            $('input[name="DestinationFactoryName"]').next('span').css('display',  '');

        } else if ( $(this).prop('selectedIndex') == 3 ) {
            $('input[name="SourceFactory"]').prop('disabled', true).css('opacity', 0.3);
            $('input[name="SourceFactoryName"]').css('opacity', 0.3);
            $('input[name="SourceFactoryName"] + .msw-button').prop('disabled', true).css('opacity', 0.3);
            $('input[name="DestinationFactory"]').prop('disabled', true).css('opacity', 0.3);
            $('input[name="DestinationFactoryName"]').css('opacity', 0.3);
            $('input[name="SourceFactoryName"]').next('span').css('display',  'none');
            $('input[name="DestinationFactoryName"]').next('span').css('display',  'none');
        }
    }
});
