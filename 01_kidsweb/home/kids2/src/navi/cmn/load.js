
function getNaviMenu(strSessionID, strNaviCode) {
    $.ajax({
        url: '/navi/index2.php',
        type: 'POST',
        data: {
            'strNaviCode': strNaviCode,
            'strSessionID': strSessionID
        }
    })
        .done(function (data) {
            $('.navigation-loadpoint').html(data);
        });
}