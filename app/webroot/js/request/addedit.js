/*
* Starting poin of webix addedit request app
*/

webix.ready(function(){
    webix.ui({
        rows:[
            { view:"template", 
                type:"header", template:"My App!" },
            { view:"datatable", 
                autoConfig:true, 
                data:{
                title:"My Fair Lady", year:1964, votes:533848, rating:8.9, rank:5
                }
            }
        ]
    });
});

if( edycja ) {
    console.log(edycja);
} else {
    console.log("NOWE");
}