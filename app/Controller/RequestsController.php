<?php
// nazwa obiektu w którym bedą przetrzymywane wybrane dane do szukania
define('NEW_OBJ_NAME','request');
//defaultowa wartość wyboru dla pasków magnetycznych
define('MAG_DEFAULT', null);
//defaultowa wartość wyboru dla chipów
define('CHIP_DEFAULT', null);
//defaultowa wartość wyboru dla plastiku
define('PVC_DEFAULT', 'any');
//defaultowa wartość wyboru dla kształtu karty
define('SHA_DEFAULT', 'any');

App::uses('AppController', 'Controller');

class RequestsController extends AppController {

    public $helpers = array('Ma', 'BootForm', 'Boot');

    /*
        Tu zapisana cała struktura, która zawiera dane konfiguracyjne
        dla formularza do wyszukiwania zamówień po specyficznych cechach kart
    */    
    
    private $configForSpecialSearching = [
            // dane dla daty od
            'od' => [
                'id' => 'picker-od',
                'label' => 'OD:',               
                'acc' => NEW_OBJ_NAME . '.od' /* Tekstowa wartość ibiektu albo klucza,
                do którego skrypt ma zapisywać vartośc wybranej daty */                
            ],
            // dane dla daty do
            'do' => [
                'id' => 'picker-do',
                'label' => 'DO:',                
                'acc' => NEW_OBJ_NAME . '.do'                
            ],
            'mag' => [ // konfiguracja selecta pod pasek mag
                'id' => 'magsel', // id elementu html
                'acc' => NEW_OBJ_NAME . '.mag',
                'options' => [ /*
                    lista, opcje, w postaci klucz => wartość
                    wartość zostaje wyświetlona, jako element tekstowy
                    klucz zostaje wpisany do atrybutu value elementu li
                    */
                    MAG_DEFAULT => 'Pasek magnetyczny?',
                    'hico' => 'HiCo',
                    'loco' => 'LoCo',
                    'any' => 'HiCo lub LoCo'
                ],
                // opcje elementu selct
                'opcje' => [
                    // opcja 0
                    [
                         // wartość wybranej opcji - ta wartość będzie przesyłana na serwer
                        'value' => MAG_DEFAULT,                        
                        'display' => 'Pasek magnetyczny?', // wartość wyświetlana w elemencie select
                        'title' => 'Karta z paskiem magnetycznym lub bez.' // podpowiedź dla użytkownika
                    ],
                    // opcja 1
                    [                         
                        'value' => 'hico',                        
                        'display' => 'HiCo', 
                        'title' => 'Karty z paskiem magnetycznym HiCo.' 
                    ],
                    // opcja 2
                    [                         
                        'value' => 'loco',                        
                        'display' => 'LoCo', 
                        'title' => 'Karty z paskiem magnetycznym LoCo.' 
                    ],
                    // opcja 3
                    [                         
                        'value' => 'any',                        
                        'display' => 'HiCo lub LoCo', 
                        'title' => 'Karty z dowolnym paskiem magnetycznym.' 
                    ],
                    'default' => 0 // która opcja jest domyślna
                ],                
                'default' => MAG_DEFAULT
            ], 
            'chip' => [
                'id' => 'ischip',
                'acc' => NEW_OBJ_NAME . '.chip',
                'options' => [
                    CHIP_DEFAULT => 'Chip?',
                    true => 'Z chipem',
                    false => 'Bez chipa'
                ],
                'default' => CHIP_DEFAULT
            ],
            'pvc' => [
                'id' => 'whatpvc',
                'acc' => NEW_OBJ_NAME . '.pvc',
                'options' => [
                    PVC_DEFAULT => 'Dowolne PVC',
                    'std' => 'PVC - standard',
                    'exo' => 'PVC - nietypowe'
                ],
                'default' => PVC_DEFAULT
            ],
            'sha' => [
                'id' => 'shape',
                'acc' => NEW_OBJ_NAME . '.sha',
                'options' => [
                    SHA_DEFAULT => 'Kształt dowolny',
                    'std' => 'Kształt - standard',
                    'exo' => 'Kształt - nietypowy'
                ],
                'default' => SHA_DEFAULT
            ],            
            'varname' => NEW_OBJ_NAME,
            /*  struktura tworzonego obiektu w którym będą przechowywane dane odnośnie
                parametrów poszukiwań */
            'theobj' => [ 
                'od' => null, // Data od
                'do' => null,  // data do
                'mag' => null, // Rdzaj paska magnetycznego                    
                'chip' => null, // czy jest chip
                'pvc' => null, // jakie pvc
                'sha' => null // kształt karty
            ]
    ];

    // Szukaj wg zadanych parametrów
    public function search() {

        $answer = 'Tu będzie w przyszłości odpowiedź';
        $data = $this->request->data;
        $this->set( compact('answer', 'data') ); 
        $this->layout='ajax'; // nie wysyłamy całej struktury strony tylko fragment html
        sleep(1); // w celach testowych
    }

    public function index() {

        // Date picker config
        $config = $this->configForSpecialSearching;  
        
        $this->set( compact('config') );        
        $this->layout='bootstrap';
	}

    private function dataForTheDatePicker() {

        return [
            'label' => 'Od:',
            'old' => [
                'label' => 'Od:',
                'years' => [2017, 2016, 2015, 2014],
                'anydate' => true
                //'anydate' => false
            ]
        ];        
    }

}