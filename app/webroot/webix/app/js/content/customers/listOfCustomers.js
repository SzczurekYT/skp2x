let listOfCustomers = { 
    id: "listOfCustomers",
    view:"datatable",
    select: true,
    css: "list-of-customers",
    gravity: 1.3,    
    columns: [
        { id:"index", header:"", sort:"int", width:35, css:{'text-align':'right'} },
        { id:"WebixCustomer_id", header:"id", width:53, css:{'text-align':'right'} },
        { id:"WebixCustomer_name", header:[ {content:"serverFilter"}], fillspace:true }, 
        { id:"WebixCustomerRealOwner_name", header: [ {content:"serverSelectFilter", options: globalAppData.customerOwners }], width:108}
    ],
    scheme:{
        $init:function(obj){ obj.index = this.count(); }
    }, 
    //globalAppData.config.customersAddOrder
    postData: { // początkowe parametry do zapytania do serwera
        fraza: '',
        realOwnerId: globalAppData.loggedInUser.id
    },
    url: function(){
        let url = globalAppData.config.customersAddOrder;                
        if( globalAppData.loggedInUser.id == listOfCustomers.postData.realOwnerId && loggedUserInHasNoAnyCustomer() ) {
            listOfCustomers.postData.realOwnerId = 0;
        }
        return webix.ajax().post(url, listOfCustomers.postData).then(function(data) {
            let dane = data.json();                            
            return dane.records;  // w records mamy faktyczne dane                            
        });
    },
    on: { // Pobieramy zawartości filtrów, dzięki czemu Webix wykona zapytanie z odpowiednimi parametrami
        'onBeforeFilter': function() {            
            listOfCustomers.postData.realOwnerId = this.getFilter("WebixCustomerRealOwner_name").value;
            listOfCustomers.postData.fraza = this.getFilter("WebixCustomer_name").value;
        },
        // na wzór privateOrders        
        'onAfterLoad': function(){
            if( this.getFilter("WebixCustomerRealOwner_name").value != listOfCustomers.postData.realOwnerId ) {                
                this.getFilter("WebixCustomerRealOwner_name").value = listOfCustomers.postData.realOwnerId;
            }
        }
    }
}

/**
    Sprawdzamy, czy zalogowany użytkownik ma pod opieką klientów */
function loggedUserInHasNoAnyCustomer() {
    let i;
    for (i = 0; i < globalAppData.customerOwners.length; i++) { 
        if( globalAppData.customerOwners[i].id == globalAppData.loggedInUser.id ) {
            return false;
        }
    }
    return true;    
}

