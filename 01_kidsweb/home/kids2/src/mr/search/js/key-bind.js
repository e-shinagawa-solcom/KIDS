(function(){
    $('body').on('keydown', function(e){
            console.log('enter');
            if(e.which == 13){
                $('img.search').click();
            }
        }
    );
})();
