<?php
App::uses('AppController', 'Controller');
/**
 * Cards Controller
 *
 * @property Card $Card
 * @property PaginatorComponent $Paginator
 */
class CardsController extends AppController {

        public $helpers = array('Proof');
/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
	
	public $paginate = array(
            //'limit' => 25,
            'order' => array(
                'Card.id' => 'desc'
            )
        );
        
        // dla ludziów z perso
        public $paginate_perso = array(
            'order' => array(
                'Card.stop_perso' => 'desc'
                , 'Order.stop_day' => 'desc'
            )
        );
	
	public function beforeFilter() {
    	parent::beforeFilter();
    	//$this->actionAllowed();
	}
        

/**
 * index method
 *
 * @return void
 */
    public function index($par = null) {

        $this->Card->recursive = 0;
        $user_perso = $this->userPersoVis();
        
//        if( $user_perso && in_array($par, array('persocheck', 'ponly', 'pover', 'ptodo')) ) { 
//        } else {
//            $this->Paginator->settings = $this->paginate;
//        }
        
        if( !$user_perso || !in_array($par, array('persocheck', 'ponly', 'pover', 'ptodo')) ) {
            $this->Paginator->settings = $this->paginate;
        }

        if( !$this->akcjaOK(null, 'index', $par) ) {
                //jeżeli ta akcja nie jest dozwolona przekieruj na inną dozwoloną
                switch($this->Auth->user('CAX')) {
                        case IDX_ALL:
                        case IDX_SAL:
                                return $this->redirect( array('action' => 'index') );
                        case IDX_NO_PRIV:
                                return $this->redirect( array('action' => 'index', 'all-but-priv') );
                        case IDX_NO_KOR:
                                return $this->redirect( array('action' => 'index', 'no-priv-no-kor') );
                        case IDX_OWN:
                                return $this->redirect( array('action' => 'index', 'my') );
                        default:
                                $this->Session->setFlash('NIE MOŻNA WYŚWIETLIĆ LUB NIE MASZ UPRAWNIEŃ.');
                                return $this->redirect($this->referer());
                }
        }



        switch($par) {
                case 'all-but-priv':
                    $opcje = array('OR' => array(
                                    'Card.user_id' => $this->Auth->user('id'),
                                    'Card.status !=' => PRIV,
                                    //array( 'NOT' => array( 'Card.status' => array(STARTED, STICKED)))
                                                    )
                    );
                break;
                case 'no-priv-no-kor': //bez prywatnych i dla koordynatora
                    $opcje = array('OR' => array(
                                    'Card.user_id' => $this->Auth->user('id'),
                                    array( 'NOT' => array( 'Card.status' => array(PRIV, NOWKA)))
                                            )
                    );
                break;

                case 'my':
                        $opcje = array('Card.user_id' => $this->Auth->user('id'));
                break;
                case 'dtpcheck':
                        $opcje = array(
                                'Card.status' => array(W4D, W4DP, W4DPNO, W4DPOK),
                                'Order.status !=' => array(O_REJ, W4UZUP, UZUPED) );				
                break;
                case 'hot':
                        $opcje = array(
                                'Card.ishotstamp' => 1,
                                'Order.status !=' => array(O_REJ, W4UZUP, UZUPED, KONEC) );				
                break;			
                case 'persocheck':
                        $opcje = array(
                                'Card.status' => array( W4DP, W4PDOK ),
                                'Order.status !=' => array(O_REJ, W4UZUP, UZUPED) );
                break;
                case 'ponly':
                    $opcje = array(
                        'Card.status !=' => array(PRIV, KONEC),
                        'Card.isperso' => 1
                    );
                break;
                case 'pover':
                    $opcje = array(
                        'Card.status !=' => array(PRIV, KONEC),
                        'Card.isperso' => 1,
                        'Card.pover' => 1
                    );
                break; 
                case 'ptodo':
                    $opcje = array( 
                        //'Card.status !=' => array(PRIV, JOBED, KONEC),
                        // chcemy jednak tylko te co w produkcji
                        'Card.status' => W_PROD,
                        'Card.isperso' => 1,
                        'Card.pover' => 0
                    );
                break; 
                case 'active':
                        $opcje = array('OR' => array(
                                        array('Card.user_id' => $this->Auth->user('id'), 'Card.status !=' => KONEC),
                                        'Card.status !=' => array(PRIV, KONEC)
                                                )
                        );
                break;
                case 'closed':
                        $opcje = array('Card.status' => KONEC);
                break;			
                default:
                        $opcje = array();
        }
        //$karty = array();
        if( !empty($opcje) ) {
            $cards = $this->Paginator->paginate( 'Card', $opcje );            
        } else {
                $cards = $this->Paginator->paginate();
        }
        //$links = $this->links;
        //$cards['upc'] = $this->userPersoChange();
        $cards['pvis'] = $user_perso;
        $cards = $this->Card->quasiPaginate(
                $par,
                array('conditions' => $opcje),
                $this->request->params,
                $cards
        );
        $paramki = $this->request->params;
        $this->set( compact('cards', /*'karty', 'links'*/ 'par', 'paramki' ) );

    }

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */

    /*
    Zmienna regulująca sposób wyświetlania danych o karcie
    - 0 - brak limitów
    - 1 - wyśwetlaie z limitami, typ 1
    - 2 - wyśwetlaie z limitami, typ 2
    Zminne jest, ewentualnie, ustawiana przez metodę $this->akcjaOK */
    private $limitedView = 0;

	public function view($id = null) {
            if (!$this->Card->exists($id)) {
                    throw new NotFoundException(__('Invalid card'));
            }
            $options = array('conditions' => array('Card.' . $this->Card->primaryKey => $id));
            $card = $this->Card->find('first', $options);
            if( !$this->akcjaOK($card, 'view') ) {
                    $this->Session->setFlash('NIE MOŻNA WYŚWIETLIĆ LUB NIE MASZ UPRAWNIEŃ.');
                    return $this->redirect($this->referer());
            }
            $evcontrol = $this->prepareSubmits($card );
            $links = $this->links;
            $vju = $this->Card->get_view_options();
            //$card['Card']['upc'] = $this->userPersoChange();
            $card['Card']['pvis'] = $this->userPersoVis();
            $limited = $this->limitedView;
            $this->set(compact('card', 'evcontrol', 'links', 'vju', 'limited'));

            //test
            /*
             * 
             * 

            $this->layout='pdf/default';
            $this->render('pdf/view');
             * 
             * 
             */
                 
	}       
        
        private function userPersoVis() {
        // sprawdź, czy zalogowany użytkownik może widzieć datę perso  
            if( $this->Auth->user('dzial') == SUA ||
                $this->Auth->user('dzial') == PER ||
                $this->Auth->user('dzial') == DTP ) {                
                return true;
            }
            return false;
        }
	
	public function prepareSubmits($card ) {
		
            //$user_dzial = $this->Auth->user('dzial');
            $tworca = $card['Card']['user_id'] == $this->Auth->user('id');
            $tab = array(
                    'buttons' => array(d_no, d_ok, p_no, p_ok, p_ov, put_kom, h_ov),
                    'bcontr' => array(d_no=>0, d_ok=>0, p_no=>0, p_ok=>0, p_ov=>0, put_kom=>0, h_ov=>0),
                    'ile' => 0 //liczba submitow do wyświetlenia
            );


            switch( $card['Card']['status'] ) {
                    case W4D:
                    case W4DPNO:
                    case W4DPOK:
                            $tab = $this->plant_KOMENTUJ($tab, $tworca, $card['Card']['isperso']);
                            $tab = $this->plant_DTP($tab, $card['Order']['status']);
                    break;
                    case W4DP:
                            $tab = $this->plant_KOMENTUJ($tab, $tworca, $card['Card']['isperso']);
                            $tab = $this->plant_DTP($tab, $card['Order']['status']);
                            $tab = $this->plant_PERSO($tab, $card, $card['Order']['status']);
                    break;
                    case W4PDNO:
                    case W4PDOK:
                            $tab = $this->plant_KOMENTUJ($tab, $tworca, $card['Card']['isperso']);
                            $tab = $this->plant_PERSO($tab, $card, $card['Order']['status']);
                    break;
                    case DOK:
                    case DNO:
                    case DOKPNO:
                    case DOKPOK:
                    case DNOPNO:
                    case DNOPOK:
                            $tab = $this->plant_KOMENTUJ($tab, $tworca, $card['Card']['isperso']);
                    break;
                    case W_PROD:
                        $tab = $this->plant_POVER($tab, $card['Card'], $this->Auth->user('dzial'));
                        $tab = $this->plant_KOMENTUJ($tab, $tworca, $card['Card']['isperso']);
                        // możliwość zakończenia hotstampingu
                        $tab = $this->plant_HOT($tab, $card['Card'], $this->Auth->user('dzial'));
                    break;
                    default:		
                            $tab = $this->plant_KOMENTUJ($tab, $tworca, $card['Card']['isperso']);
            }

            return $tab;
		
	}
        private function plant_HOT( $button_tab = array(), $karta = array(), $dzial = 0 ) {
            
            $ret_tab = $button_tab;
            
            $warunek = 
                    // d) użytkownik jest superadminem, z perso lub dtp 
                    in_array($dzial, array(SUA, DTP, PER) ) &&
                    $karta['ishotstamp'] == 1 && // b) karta ma hotstamping niezakończony                    
                    $karta['status'] == W_PROD; // a) ma klarowny status
            if( $warunek ) {
                $ret_tab['bcontr'][h_ov] = 1;
		        $ret_tab['ile']++;
            }
            return $ret_tab;
        }

        private function plant_POVER( $button_tab = array(), $karta = array(), $dzial = 0 ) {
            
            $ret_tab = $button_tab;
            
            $warunek = 
                    // d) użytkownik jest superadminem, z perso lub dtp 
                    in_array($dzial, array(SUA, DTP, PER) ) &&
                    $karta['isperso'] && // b) karta ma personalizację
                    !$karta['pover'] && // c) nie została już zamarkowana
                    $karta['status'] == W_PROD; // a) ma klarowny status
            if( $warunek ) {
                $ret_tab['bcontr'][p_ov] = 1;
		$ret_tab['ile']++;
            }
            return $ret_tab;
        }

	private function plant_KOMENTUJ( $button_tab = array(), $tworca = false, $isperso = false ) {
		
		$ret_tab = $button_tab;
		
		switch( $this->Auth->user('CA_K') ) { // uprawnienia do komentowania kart
			case r_OWN:
				if( $tworca ) {
					$ret_tab['bcontr'][put_kom] = 1;
					$ret_tab['ile']++;
				}
			break;
			case r_NOP:
				if( $isperso ) {
					$ret_tab['bcontr'][put_kom] = 1;
					$ret_tab['ile']++;
				}
			break;
			case r_ALL:
			case r_SAL:
				$ret_tab['bcontr'][put_kom] = 1;
				$ret_tab['ile']++;
			break;
		}
		
		return $ret_tab;
	}
	
	private function plant_PERSO( $button_tab = array(), $card = array(), $ordstat = null ) {
		
		// sprawdzanie statusu zamówienia i czy ma perso "na wszelki wypadek"
		//if( $card['Order']['status'] == W4CARD && $this->kartaMaPerso($card['Card']) ) {
			
		// sprawdzanie czy karta ma perso "na wszelki wypadek"
		if( $this->kartaMaPerso($card['Card']) && !in_array($ordstat, array(O_REJ, W4UZUP, UZUPED))) {
			$ret_tab = $button_tab;
			switch( $this->Auth->user('CA_P') ) { // uprawnienia do akceptacji/odrzucania personalizacji
				case r_ALL:
				case r_SAL:
					$ret_tab['bcontr'][p_ok] = 1;
					$ret_tab['bcontr'][p_no] = 1;
					$ret_tab['ile'] = $ret_tab['ile'] + 2;
				break;
			}
			return $ret_tab;
		}
		return $button_tab;
	}
	
	private function plant_DTP( $button_tab = array(), $ordstat = null ) {
		
		$ret_tab = $button_tab;
		if( !in_array($ordstat, array(O_REJ, W4UZUP, UZUPED)) )
		//if( $ordstat != O_REJ && $ordstat != W4UZUP)
			switch( $this->Auth->user('CA_D') ) { // akceptacji/odrzucania plików/projektó DTP
				case r_ALL:
				case r_SAL:
					$ret_tab['bcontr'][d_no] = 1;
					$ret_tab['bcontr'][d_ok] = 1;
					$ret_tab['ile'] = $ret_tab['ile'] + 2;
				break;
			}
		return $ret_tab;
		
	}

	public function kartaMaPerso( $card = array() ) {
	//zakładamy, że $card ma Cakeowy format
		
		return $card['isperso'];
	}
    
    private function isMulti() {        
        return !( $this->request->data['Card']['multi'] < 2 );
    }

    /* zduplikuj kartę i zapisz wiele */
    private function saveMulti( &$err /*, &$zwrotka*/ ) {

        //Ile mamy kart zapisać
        $ile = (int)$this->request->data['Card']['multi'];

        // Jeżeli checbox zaznaczony, to tworzymy puste zamówienie
        if( $this->request->data['Card']['zrobZamo'] ) { //$zwrotka = $klient; //return true;
            
            // zwraca id zamówienia lub zero
            $this->request->data['Card']['order_id'] = $this->pusteZamowienie();             
            
            //$zwrotka = $this->request->data; //return true;
        }

        // Some default data
        $this->request->data['Card']['ilosc'] = $this->request->data['Card']['quantity'] = 1;
        $this->request->data['Card']['price'] = 0;

        // Zapiszmy standardowo pierwszą
        if ( !$this->Card->saveitAll( $this->request->data, $err) ) { return false; }

        // Powiel zapisaną jeszcze $ile-1 razy
        if( !$this->Card->duplikujKarte( $this->Card->id, $ile-1, $err /*, $zwrotka*/ ) ) { return false; }
        
        return true;
    }

    /*
        Tworzymy puste zamówienie */
    private function pusteZamowienie() {

        $karta = $this->request->data['Card'];
        $klient = $this->Card->Customer->find('first', [
             'conditions' => ['Customer.id' => $karta['customer_id']]
        ]);
        $dane = ['Order' => [
            'user_id' => $karta['owner_id'],
            'customer_id' => $karta['customer_id'],
            'siedziba_id' => $klient['AdresSiedziby']['id'],
            'wysylka_id' => $klient['AdresSiedziby']['id'],
            'osoba_kontaktowa' => $klient['Customer']['osoba_kontaktowa'],
            'tel' => $klient['Customer']['tel'],
            'forma_zaliczki' => $klient['Customer']['forma_zaliczki'],
            'procent_zaliczki' => $klient['Customer']['procent_zaliczki'],
            'forma_platnosci' => $klient['Customer']['forma_platnosci'],
            'termin_platnosci' => $klient['Customer']['termin_platnosci'],
            'newcustomer' => NULL
        ]];
        $this->Card->Order->create();
        $this->Card->Order->save($dane, ['validate' => false]);

        return $this->Card->Order->id;
    }
    

/**
 * add method
 *
 * @return void
 */
	public function add() {
            if ($this->request->is('post')) {                
                //$this->Card->print_r2($this->request->data); return;			                    

                if( $this->isMulti() ) { // Jezeli uzytkownik chce zapisać wiele kart
                    if( $this->saveMulti( $blad /*, $zwrotka*/ ) ) {
                        //$this->Card->print_r2($zwrotka); return;
                        $this->Session->setFlash('JUPI!', 'default', array('class' => GOOD_FLASH));
                        return $this->redirect(array('controller' => 'orders', 'action' => 'view', $this->Card->Order->id));
                    } else {
                        $this->Session->setFlash('Nie można zapisac kart. Proszę spróbuj ponownie. (blad = ' . $blad . ')');
                    }
                } else { // business as usual
                    if ( $this->Card->saveitAll( $this->request->data, $blad ) ) {
                        $this->Session->setFlash('KARTA ZOSTAŁA ZAPISANA!', 'default', array('class' => GOOD_FLASH));
                        return $this->redirect(array('action' => 'view', $this->Card->id));
                    } else {
                        $this->Session->setFlash('Nie można zapisac karty. Proszę spróbuj ponownie. (blad = ' . $blad . ')');
                    }
                }
                
            }

            //chcemy tylko klientów, którzy są "własnością" zalogowanego użytkownika
            // UWAGA, przy zmianie na klientów różnych użytkowników - to nie bedzie dzialać            
            $ownerid = $this->Auth->user('id');
            $customers = $this->Card->Customer->find('all', array(
                    'recursive' => 0,
                    'conditions' => array('owner_id' => $ownerid),
                    'fields' => array('Customer.id', 'Customer.name', 'Customer.owner_id', 'Customer.etylang')
            ));

            if( empty($customers) ) {
                $this->Session->setFlash( 'Musisz najpierw dodać jakiegoś klienta !' );
                return $this->redirect($this->referer());
            }

            // przygotowujemy tablicę z klientami dla autocomplete w $jscode
            $klienci = array();
            foreach( $customers as $row ) { 
                $klienci[] = array(
                    'label' => $row['Customer']['name'],
                    'id' => (int)$row['Customer']['id'],
                    'etylang' => $row['Customer']['etylang']
                );             
            }
            $jscode =   "var yeswyb = " . true . ";\nvar yesperso = " . JEDEN .                        
                        ";\nvar klienci =  "  . json_encode($klienci);

            // Chcemy pliki podpięte do kart "w buforze" zalogowanego użytkownika
            $wspolne = $this->Card->findPropperUploads();
            $vju = $this->Card->get_view_options();
            $links = $this->links;
            $referer = $this->referer();
            $this->set(compact( 'vju', 'ownerid', 'wspolne', 'links', 'jscode', 'referer', 'customers', 'klienci'/**/ ));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
            if (!$this->Card->exists($id)) {
                    throw new NotFoundException('Nie ma tekiej karty!');
            }
            if ($this->request->is(array('post', 'put'))) {
			//$this->Card->print_r2($this->request->data); return;
			
                    if ($this->Card->saveitAll($this->request->data, $blad)) {
                            $this->Session->setFlash('KARTA ZOSTAŁA ZAPISANA!', 'default', array('class' => GOOD_FLASH));
                            return $this->redirect(array('action' => 'view', $this->Card->id));
                            /**/
                    } else {
                        $this->Session->setFlash('Nie można zapisac karty. Proszę spróbuj ponownie.' . ' :'. $blad);
                        //$this->Card->print_r2($this->Card->tempor);
                    }
            } else {                    
                    $this->request->data = $this->Card->znajdzTaKarta($id);
                    if( !$this->akcjaOK($this->request->data, 'edit') ) {
                            $this->Session->setFlash('EDYCJA NIE JEST MOŻLIWA LUB NIE MASZ UPRAWNIEŃ.');
                            return $this->redirect($this->referer());
                    } 
            }
            $users = $this->Card->Owner->find('list');

            $orders = $this->Card->Order->find('list');
            $jobs = $this->Card->Job->find('list');
            //$this -> render('edit_');
            $vju = $this->Card->get_view_options();
            $links = $this->links;

            // id usera brane z karty - przyda się do edycji przez inne osoby niz handlowiec: twórca
            $customers = $this->Card->Customer->find('all', array(
                'recursive' => 0,
                'conditions' => array('owner_id' => $this->request->data['Card']['owner_id']),
                'fields' => array('Customer.id', 'Customer.name', 'Customer.owner_id', 'Customer.etylang')
            ));            
            $klienci = array();
            
            foreach( $customers as $row) {
                $klienci[] = array('label' => $row['Customer']['name'], 'id' => $row['Customer']['id'], 'etylang' => $row['Customer']['etylang']);    
            }    
            $klienci = "var klienci =  \n" . str_replace("},", "},\n", json_encode($klienci) );
            $jscode = "var yeswyb = " . true . ";\nvar yesperso = " . JEDEN . ";\n";
            $jscode .= "var orvalue = " . OTHER_ROLE . ";\n\n" . $klienci . ";";

            $wspolne = $this->Card->findPropperUploads();
            $this->set(compact('vju', 'users', 'klienci', 'orders', 'jobs', 'links', 'jscode', 'wspolne'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Card->id = $id;
		if (!$this->Card->exists()) {
			throw new NotFoundException(__('Invalid card'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Card->delete()) {
			$this->Session->setFlash(__('The card has been deleted.'));
		} else {
			$this->Session->setFlash(__('The card could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

        // via ajax zasysamy dane i sprawdzamy, czy kartę można edytować
        public function editable() {
            
            // $this->request->data['id'] - id karty
            $dane = $this->Card->findCard4proofCheck( $this->request->data['id'] );
            
            $answer = array(
                'editable' => $this->isEdycjaKartyOK($dane['Card']['user_id'], $dane['Card']['status'], $dane['Order']['status']),
                //'editable' => false,
                'uid' => $this->Auth->user('id'),
                'dane' => $dane
            );            
            $this->set(array(
                'answer' => $answer,
                //'_serialize' => 'answer' //to używamy, gdy nie chcemy view (tu json/editable.ctp)
            ));
            //sleep(1);
	} 
        
        public function tmp($id = null) {
            
            $dane = $this->Card->findCard4proofCheck( $id );
            
            $answer = array(
                'editable' => $this->isEdycjaKartyOK($dane['Card']['user_id'], $dane['Card']['status'], $dane['Order']['status']),
                //'editable' => false,
                'uid' => $this->Auth->user('id'),
                'dane' => $dane
            );            
            $this->set(array(
                'answer' => $answer,));
	}
        
        private function isEdycjaKartyOK( $c_user_id = null, $c_status = null, $o_status = null ) {
            
            $cae = $this->Auth->user('CAE');
            if( $cae == EDIT_SAL || $c_status == PRIV ||							
                in_array( $o_status, array(O_REJ, W4UZUP, UZU_REJ)) ) {                 
                //stan karty pozawla na edycję
                
                if( $cae ==  EDIT_OWN && $this->Auth->user('id') == $c_user_id ) {
                    return true;
                }
                if( $cae ==  EDIT_ALL || $cae ==  EDIT_SAL ) {
                    return true;
                }                							
            } 
            return false; 
        }
        

	private function akcjaOK( $dane = array(), $akcja = null, $par = null  ) {
	
            switch($akcja) {
                case 'edit':         
                    return $this->isEdycjaKartyOK( $dane['Card']['user_id'], $dane['Card']['status'], $dane['Order']['status']);                    
                case 'view':
                    $card = $dane['Card'];
                    if( $this->Auth->user('id') == $card['user_id'] )
                            $jego_karta = true;
                    else
                            $jego_karta = false;
                    if( 1 ) {//jeżeli nie ma przeszkód, nie związanych z uprawnieniami, do wyświetlenia
                            switch( $this->Auth->user('CAV') ) {
                                    case VIEW_SAL:
                                    case VIEW_ALL:
                                            return true;
                                            break;
                                    case VIEW_NO_PRIV: //nie może prywatnych kart innych ludzi oglądać
                                            //if( $jego_karta || !in_array($card['status'], array(STARTED, STICKED))  )
                                            if( $jego_karta || $card['status'] != PRIV  )
                                                    return true;
                                    break;
                                    case VIEW_NO_KOR:
                                            if( $card['status'] != PRIV && $card['status'] != NOWKA )
                                                    return true;
                                    break;	
                                    case VIEW_LIM_1:
                                        $this->limitedView=1; // użytkownik będzie miał ograniczony widok karty, typ 1
                                        return true;
                                    break;							
                                    case NO_RIGHT:
                                    case VIEW_SHR:
                                            return false;
                                            break;
                                    case VIEW_OWN:
                                            return $jego_karta;
                                            break;
                            }
                    }
                    break;
                case 'index':
                    $upraw = $this->Auth->user('CAX');
                    switch($par) {
                            case null:
                                    if( $upraw == IDX_ALL || $upraw == IDX_SAL ) 
                                            return true;
                                    break;
                            case 'all-but-priv':
                            case 'active':
                            case 'closed':
                                    //if( $upraw == IDX_NO_PRIV || $upraw == IDX_ALL || $upraw == IDX_SAL) return true;
                                    if( in_array($upraw, array( IDX_NO_PRIV, IDX_ALL, IDX_SAL ) ) ) return true;															
                            break;
                            case 'no-priv-no-kor':
                                    if( in_array($upraw, array(IDX_NO_KOR, IDX_NO_PRIV, IDX_ALL, IDX_SAL) ) ) return true;
                            break;	
                            case 'ponly':
                            case 'pover':
                            case 'ptodo':
                            case 'my':
                            case 'dtpcheck':
                            case 'hot':
                            case 'persocheck':
                            case 'szukaj':
                                    return true;
                            break;								

                            default:
                                    return false;
                    }
                break;
            }
            return false;
	}
        
        /* Z powyższego na wszelki wypadek
         case 'edit':
                    $card = $dane['Card'];
                    $order = $dane['Order'];
                    if( $this->Auth->user('CAE') == EDIT_SAL || $card['status'] == PRIV ||							
                            in_array($order['status'], array(O_REJ, W4UZUP, UZU_REJ)) ) { 
                            //stan karty pozawla na edycję
                            switch( $this->Auth->user('CAE') ) {
                                case NO_RIGHT:
                                case EDIT_SHR:
                                        return false;
                                case EDIT_OWN: // ($lang ? 'en' : 'pl')
                                    return $this->Auth->user('id') == $card['user_id'] ? true : false;
                                case EDIT_ALL:
                                case EDIT_SAL:
                                    return true;
                            }							
                    } else {
                        return false; }
                    break;
          
         */

    public function addCzasPerso() {

    //$this->autoRender = false; // We don't render a view in this example
    $this->request->onlyAllow('ajax'); // No direct access via browser URL
    //$this->layout = 'ajax';

    $result = array(
        'msg' => ':-(',
        'saved' => false,
        'stop_perso' => null
    );

    $data =  array( 'Card' => $this->request->data );            
    if ( $this->Card->save( $data ) ) {
    // handle the success.
        $result['saved'] = true;
        $result['msg'] = 'Hura!';
        $result['stop_perso'] = $this->request->data['stop_perso'];
        $result['dl'] = $this->request->data['dl']; 
    } 

    $this->set(compact('result')); // Pass $data to the view
    //$this->set('_serialize', 'result'); <- to robimy, gdy nie używamy view files
    //sleep(2);
}
                
	
/**
 * search method
 *
 * @return void
 */
    public function search($par = null) {

        function korekt_numer( $moze_nr = null) {
        // sprawdzamy czy szukana fraza to poprawny numer
            $dl = strlen($moze_nr);
            $i = 0;
            while ( $i < $dl && ctype_digit( substr($moze_nr, $i, 1) ) ) {
                $i++;
            }
            // co przerwało pętlę?
            if( $i < $dl ) { //znaczy nie przeszliśmy całego stringu
                if( substr($moze_nr, $i, 1) != "/" ) { return 0; } // nie cyfra i ne slash
                else { // slash, muszą być jeszcze dokładnie 2 cyfry
                    if( $i + 3 != $dl ) { return 0; } //ilość znaków się nie zgadza
                    if( !ctype_digit( substr($moze_nr, $i+1, 2) ) ) { //dwa ostatnie znaki to nie cyfry
                        return 0;
                    }
                }
            }
            if( $i == $dl && $dl > 5 ) { return 0; }
            if( $i < $dl && $i > 5 ) { return 0; }
            // powyzej za dlugie numery
            return $i; // tu mamy pozycje slasha lub $i = $dl
        }

        if ($this->request->is('post')) {

            $szukane = $this->request->data['Card']['sirczname'];
            $fraza = $szukane;
            $kr = korekt_numer( $szukane );
            if( $kr ) { //jeżeli ktoś wpisał poprawny numer
                if( $kr == strlen($szukane)) { //ktoś wpisał same cyfry
                    $numer = (int)(date('y') . BASE_ZERO) + (int)$szukane;
                    $fraza .= '/' . date('y');
                } else {
                    $numer = (int)(substr($szukane, $kr+1) . BASE_ZERO) + (int)substr($szukane, 0, $kr);                       
                }
                $wynik = array(
                    'zamowienie' => $this->Card->Order->findOrderByNr($numer),
                    'zlecenie' => $this->Card->Job->findJobByNr($numer)
                );
            } else {                   
               $wynik = array(
                   'klienci' => $this->Card->Customer->findCustomerByName($szukane),
                   'karty' => $this->Card->findCardByName2($szukane)       
               );
            }    

            $ile = 0;
            foreach ($wynik as $arr) {  $ile += count($arr); }
            if( array_key_exists('zamowienie', $wynik) && !empty($wynik['zamowienie'])) {
                --$ile;
            }
            $this->set( compact('wynik', 'fraza', 'ile') );                
            //$this->render('search2');
        } else {
            $this->redirect($this->referer());		
        }
    }		

        
}
