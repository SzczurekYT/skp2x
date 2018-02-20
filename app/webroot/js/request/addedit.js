/*
* Starting point of webix addedit request app
*/
var tekst = "";

if( edycja ) {
    tekst = " - edycja";
    console.log(edycja);
} else {
    tekst = " - nowe";
    console.log("NOWE");
}

//var gornyNaglowek = { view:"toolbar", /*type:"header",*/ template:"My App!" + tekst};
var gornyNaglowek = {
    view:"toolbar", elements: [
        {template: "My App!" + tekst},
        {}]
};

var test = {
    //container:"app_here",
    //view:"layout", // optional
    rows:[
        gornyNaglowek,
        { cols:[
            {},
            {},
            {}
        ]}      
    ]   
};

var filmset = [
    { id:1, title:"The Shawshank Redemption", year:1994},
    { id:2, title:"The Godfather", year:1972},
    { id:3, title:"The Godfather: Part II", year:1974},
    { id:4, title:"The Good, the Bad and the Ugly", year:1966},
    { id:5, title:"My Fair Lady", year:1964},
    { id:6, title:"12 Angry Men", year:1957}
];

var buttony = [
    { view:"button", value:"Add", width:70, click:"add_row" },
    { view:"button", value:"Delete", width:70, click:"delete_row" },
    { view:"button", value:"Update", width:70, click:"update_row" },
    { view:"button", value:"Clear Form", width:85, click:"$$('myform').clear()"}
];

var empty = [{ view:"button", value:"Add", width:70, click:"add_row" },{},{},{}];

var test2 = {
    rows: [
        { view:"toolbar", id:"mybar", elements: empty//buttony
            /*
                { view:"button", value:"Add", width:70, click:"add_row" },
                { view:"button", value:"Delete", width:70, click:"delete_row" },
                { view:"button", value:"Update", width:70, click:"update_row" },
                { view:"button", value:"Clear Form", width:85, click:"$$('myform').clear()"}
            */
            
        },
        { cols:[
                {view:"form", id:"myform", width:200, elements:[
                    { view:"text", name:"title", placeholder:"Title", width:180, align:"center"}, 
                    { view:"text", name:"year", placeholder:"Year", width:180, align:"center"} ]
                },
                {
                    view:"list", 
                    id:"mylist",
                    template:"#title#. Shot in #year#.", 
                    select:true, //enables selection 
                    height:400,
                    data: filmset
                } 
            ]
        }
    ]
    };

webix.ready(function(){
    
    webix.ui(test2);
    
});

