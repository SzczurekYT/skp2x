<?php
    $this->set('title_for_layout', 'Pulpit');    
    $theOrderDetail = $this->App->trimAll($this->element('webixes/theOrderDetail'));
    $theCustomerDetail1 = $this->App->trimAll(
        $this->element('webixes/labelName', [
            'extraClass' => null,
            'label' => 'Klient',
            'name' => '#WebixCustomer_name#'
        ]));    
?>

<script type="text/javascript" charset="utf-8">

/*
Globalny obiekt, przechowywyjący różne użyteczne dane */
var globalAppData = {
    loggedInUser: <?php echo json_encode($loggedInUser); ?>, // Info o zalogowanym użytkowniku
    customerOwners: [
        {id: 0, value: "Wszyscy"},    
        {id: 3, value: "Agnieszka"},
        {id: 17, value: "Ania"},
        {id: 2, value: "Beata"},
        //{id: 1, value: "Darek"},
        {id: 4, value: "Jola"},
        {id: 11, value: "Marzena"},
        {id: 31, value: "Piotr"},
        {id: 10, value: "Renata"}            
    ],    
    config: { // różnorakie przydatne dane
        logoutUrl: "/users/logout",
        // url do zasysania klientów dla celów dodania nowego zamówienia
        customersAddOrder: "/webixCustomers/getForAddingAnOrder.json",
        justOneCustomerData: "/webixCustomers/getOneForQuickAddOrder/",
        wyglad: {
            mainPad: 35, // definujący główny padding
            buttonOnMainToolbarWidth: 50
        },
        dataForManuInToolbar: {
            items: [ // elementy menu
                { id: "klienci", value:"Klienci", icon: "address-book-o" },
                { id: "prywatne", value:"Prywatne", icon: "handshake-o" }
            ],
            associations: { // powiązania menu z róznymi obiektami w aplikacj
                listOfCustomers: "klienci", // id elementu menu powiązanego z view  "listOfCustomers"
                listOfPrivateOrders: "prywatne"
            }
        }
    },
    template: { // tu bardziej złożone tamplates        
        theOrderDetail: "<?php echo $theOrderDetail; ?>",        
        theCustomerDetail1: '<?php echo $theCustomerDetail1; ?>'
    } 
};

webix.ready(function(){ //to ensure that your code is executed after the page is fully loaded
  
    let layout1 = {
        id: "theLayout", 
        css: 'app-main',
        rows:[
            mainToolbar,
            { 
                cols:[
                    leftSidebar,
                    content//allTheRest // zawartość
                ]
            }             
        ]   
    }    

    webix.ui(
        layout1 // 1 - pierwsza wersja, 2 - druga wersja
    );

});

</script>