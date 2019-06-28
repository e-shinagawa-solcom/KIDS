function queryMasterData(condition, procDone, procFail) {

    // マスタ検索共通
    var searchMaster = {
                    url: '/cmn/querydata.php?strSessionID=' + $.cookie('strSessionID'),
                    type: 'post',
                    dataType: 'json'
                };

    $.ajax($.extend({}, searchMaster, condition))
    .done(function(response){
        procDone(response);
    })
    .fail(function(response){
        procFail(response);
    });
}