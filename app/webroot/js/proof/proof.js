var tenSelektor = '#proof-preview';
$(function() {
    console.log('model = ', model);
    //log('start');
    refreshProofData( setEditableOrNot );    
    setKlik4Klodka();
  });

/* zasysnij swieze dane i gdy sukces, to wywołaj callback function */
function refreshProofData( callBack ) {
    
    /* myBase to zmienna generowana w nagłówku przez $this->webroot 
     pieprzony get robi cache i jak strona się ładuje z cacha, to skurczybyk
     się z serwerem nie kontaktuje i dlatego ten parametr potrzebny */
    loadingON(); // włącz kręciołe
    var posting = $.post( myBase + "cards/editable.json", { 
        "_": $.now(),
        "id": model.Proof.card_id
    });
    
    posting.done(function( dane ) {   
        //console.log('done');        
        updateModel(dane); // uaktualnij model "proofa" - czyli element łączący kartę/proofa
        callBack( dane ); // w zależności od stanu ustawiamy możliwość edycji lub jej brak        
    });
    
    posting.fail(function( dane ) {
        //console.log('fail');
    });
    
    posting.always(function( dane ) {
        loadingOFF();
        //console.log('always');
        //console.log('dane = ', dane)
        if( posting.status === 403) { // traktujemy to, że użytkownik nie jest zalogowany
            // przekierowujemy do logowania
            location.assign(myBase + 'users/login');
        }
    });
    
}

// uaktualnij js-owy model proofa
function updateModel( server_data ) {
    
    var proof = server_data.dane.Proof;
    
    console.log('dane = ', server_data);
    console.log('proof = ', proof);
    if( proof.id ) { // znaczy są jakieś dane w bazie
        console.log('jes!');
    } else {
        console.log('ni ma!');
    }
}

// kręcioła
// włącz 
function loadingON() {  $('#proof-preview').addClass('loading'); }
// wyłącz
function loadingOFF() { $('#proof-preview').removeClass('loading'); }

function setKlik4Klodka() {
    
    $("#panel > i").click(function(){        
        //console.log(stanKlodki());
        refreshProofData( klikService );
    });
}

function setEditableOrNot( info ) {
    /* info to dane zasysnięte z serwera o stałej strukturze */
    //console.log(info.editable);
    
    if( info.editable ) {   
        //console.log('dozwolona');
        setEdycjaDozwolona();        
    } else {
        //console.log('zabroniona');
        setEdycjaZabroniona();
    } 
}

// po kliknięciu w kłudkę i odświerzeniu danych Proof'a robimy obsługę tego kliknięcia
function klikService( info ) {
    // info to dane zasysnięte z serwera o stałej strukturze 
    switch( stanKlodki() ) { // stan klodki na stronie, NIE na serwerze!
        case 'czerwona':  // zamknięta i czerwona
            if( info.editable ) {
                // zmień kłudkę na zieloną
                setEdycjaDozwolona(); 
            }
            break;
        case 'zamknieta': // zamknięta i zielona
            if( info.editable ) {
                openTheLock(); // otwiera kłódkę i czyni odpowiednie pola edytowalne
            } else { // zrób czerwoną
                setEdycjaZabroniona(); }
            break; 
        case 'otwarta': // otwarta i zielona
            if( info.editable ) { // zapisz dane i zamknij kłudkę
                closeTheLock();
            } else { // zrób czerwoną i komunikat o niemożności zapisania
                swapLock();
                setEdycjaZabroniona();
                console.log('Edycja nie jest dozwolona, nie zapisano danych!');
            }
            break;
        default: // inny przypadek, czyli błąd! - wyświetl komunikat
            console.log('pieprzony blad!');
    }
}

function openTheLock() {    
    swapLock();
    setContentEditable(true);
}

// zapisuje dane zamyka kłódkę, i ustawia pola z klasą .pedit na contenteditable=false
function closeTheLock() {
    swapLock();
    setContentEditable(false);
}

// pola z klasą .pedit ustawiamy na contenteditable=true
function setContentEditable(yes_or_no) {
    
    $('#proof-preview .pedit').each(function(){
        $(this).attr('contenteditable', yes_or_no);
    });
}

function swapLock() {
    $('#proof-preview').toggleClass('locked');
    $('#proof-preview').toggleClass('unlocked');
}

function setEdycjaDozwolona() {    
    zamienKlasy(tenSelektor, 'edit-no', 'edit-yes');
}

function setEdycjaZabroniona() {       
    zamienKlasy(tenSelektor, 'edit-yes', 'edit-no');
    zamienKlasy(tenSelektor, 'unlocked', 'locked');
}


function stanKlodki() {
    
    if( $(tenSelektor).hasClass('edit-yes') ) {
        if( $(tenSelektor).hasClass('locked') ) {
            return 'zamknieta';
        }
        if( $(tenSelektor).hasClass('unlocked') ) {
            return 'otwarta';
        }
        return 'error'; // coś nie tak...
    }
    if( $(tenSelektor).hasClass('edit-no') ) {
        if( $(tenSelektor).hasClass('locked') ) {
            return 'czerwona';
        }
        return 'error'; // coś nie tak...
    }
    return 'error'; // coś nie tak... 
}

function zamienKlasy(selektor, skasuj, dodaj) {
/* selektor - selektor elementu,  skasuj - klasa, którą należy wyrzucić
 * dodaj - klasa, którą należy dodać  */    
    $(selektor).removeClass(skasuj);
    $(selektor).addClass(dodaj);
}

function log( str ) {
    
    var d = new Date();
    console.log(d.toLocaleTimeString());
    console.log(str);
}

// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> DEPREC
function setUpThePadlock_old() {
    
    /* myBase to zmienna generowana w nagłówku przez $this->webroot 
     pieprzony get robi cache i jak strona się ładuje z cacha, to skurczybyk
     się z serwerem nie kontaktuje i dlatego ten parametr potrzebny */
    var geting = $.get( myBase + "cards/editable.json", { "_": $.now() } );
    
    geting.done(function( dane ) {        
        //log(dane.editable);
        //console.log(dane);
        setEditableOrNot(dane.editable); // w zależności od stanu ustawiamy możliwość edycji lub jej brak        
    });
    setKlik4Klodka();
}

function setUpThePadlock() {
    
    refreshProofData( setEditableOrNot );    
    setKlik4Klodka();
}

//################################################


// ustaw kłodkę do klikania
function setupKlodka() {
    
   
    $("[data-editable=true] i").click(function(){
        
        console.log('Zakwakal !!');
    });
}


