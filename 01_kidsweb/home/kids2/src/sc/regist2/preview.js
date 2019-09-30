jQuery(function($){
    
    $("#OkBt").on('click', function(){
        registFrm.submit();
    });

    $("#CancelBt").on('click', function(){
        window.close();
    });

});