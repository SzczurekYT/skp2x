$(function() {
    //console.log( "ready!" );
    $('.ikony > #loopka').click(function(){       
        fireSearch();
    });

    $('.inputddown').on('hidden.bs.dropdown', function () {        
        fireSearch();
    })    
});

// wyślij zapytanie na serwer
function fireSearch() {
    loadingON();    
    getData(loadingOFF);
}

//WŁącz kręciołe
function loadingON() {
    $('.ikony').addClass("kreci");
}

//WYącz kręciołe
function loadingOFF() {
    $('.ikony').removeClass("kreci");
    //console.log( "Kreciola OFF!" );
}

function getData( doItWhenYouHaveData ) {

    var theUrl = "/disposals/search";

    var posting = $.post( theUrl, request );

    posting.done(function( answer ) { // sukces, dostaliśmy dane                
        updateDOM(answer); //wpisz otrzymane dane
        doItWhenYouHaveData();
    });

    posting.fail(function( ) { // sukces, dostaliśmy dane        
        console.log(posting.status);
        updateDOM(posting.responseText); //wpisz otrzymane dane
        doItWhenYouHaveData();
    });

}

//
function updateDOM(dane) {

    var domElId = 'rezultat';

    $('#' + domElId + '>#czas').append( Date() + '<br>');
    //$('#' + domElId + '>#tmp').html(dane);
    $('#wyniki').html(dane);
    
}