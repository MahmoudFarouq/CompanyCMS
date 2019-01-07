class NavController{

    constructor(tableBuilder){
        this.tableBuilder = tableBuilder;
        this.customers = $("#customers");
        this.admins = $("#admins");


        this.customers.click(()=>{
            this.tableBuilder.buildTable({'target':'customer'});
        });
        this.admins.click(()=>{
            this.tableBuilder.buildTable({'target':'admin'});
        });

    }
}