<?php

App::uses('AppModel', 'Model');

class WebixCustomer extends AppModel {

    public $useTable = 'customers'; // No bo to tylko wrap dla Webix'a

    public $belongsTo = [
        'WebixCustomerRealOwner' => [ // Stały opiekun klienta
            'foreignKey' => 'opiekun_id',            
        ]
    ];

    private $fieldsWeWant = [
        'list' => [ // Do listy klientów przy dodawaniu zamówienia
            // za wyjątkiem $hasMany
            'WebixCustomer.id', 'WebixCustomer.name', 'WebixCustomer.osoba_kontaktowa',
            'WebixCustomerRealOwner.id', 'WebixCustomerRealOwner.name', 'WebixCustomerRealOwner.inic'            
        ],
        'one' => [ // just one customer
            'WebixCustomer.id', 'WebixCustomer.name',
            'WebixCustomer.osoba_kontaktowa', 'WebixCustomer.email',
            'WebixCustomerRealOwner.id', 'WebixCustomerRealOwner.name', 'WebixCustomerRealOwner.inic'
        ]
    ];

    //public $defaultConditions = [ 'WebixPrivateOrder.status' => 0 ]; // myk w AppModel z beforeFind
    

    /*
        Chcemy ifo o jednym kliencie dla szybkiego dodania zamówienia  */
    public function getOne4QuickOrderAdd( $id = 0 ) {

        $parameters = [
            'fields' => $this->fieldsWeWant['one'],
            'conditions' => [
                "WebixCustomer.id" => $id
            ]
        ];
        $cakeResults = $this->find('first', $parameters);
        $merged = $this->mergeCakeData($cakeResults);        
        $merged["cake"] = $cakeResults;
        return $merged;
    }

    /**
     *  Znajdż klientów na potrzeby dodania zamówienia handlowego
     *  $constantOwner - stały opiekun klienta - opiekun_id w tabeli customers
     *  $constantOwner = 0, znajdź wszystkich klientów, użytkownik dowolny
     *  $realOwner > 0, to znajdź klientów tylko tego użytkownika
     *  $limit - ile max rekordów */
    public function getCustomersForAddingAnOrder( $coSzukamy = null, $realOwner = 0, $limit = 11 ) {

        $out = [];
        $parameters = [
            'fields' => $this->fieldsWeWant['list'],
            'limit' => $limit
        ];

        if( $realOwner ) {           
            $parameters['conditions']['WebixCustomerRealOwner.id'] = $realOwner;
        }

        if( $coSzukamy != '' AND $coSzukamy != null ) { //niepusta fraza
            $parameters['conditions']['WebixCustomer.name LIKE'] = '%'.$coSzukamy.'%';
        }

        $cakeResults = $this->find('all', $parameters);        
        $out['records'] = $this->transferResults($cakeResults, $coSzukamy);// $this->mergeCakeManyRows( $cakeResults );
        $out['cake'] = $cakeResults; // w celach diagnostycznych
        return $out;
    }

    /**
     * Przekonvertuj do Webixa i wstaw span'y do wyników, które otoczą frazę */
    private function transferResults( $dane = [], $fraza = "" ) {
        
        $out = [];
        $start = "<span class='gruby'>";
        $stop = "</span>";
        foreach( $dane as $oneRow ) {
            $newRow = $this->mergeCakeData($oneRow);            
            if( $fraza ) {
                $pos = stripos($newRow["WebixCustomer_name"], $fraza);
                $frag = substr($newRow["WebixCustomer_name"], $pos, strlen($fraza));                
                $newRow["WebixCustomer_name"] = str_ireplace($fraza, "$start$frag$stop", $newRow["WebixCustomer_name"]);
            }            
            $out[] = $newRow;
        }
        return $out;
    }

}