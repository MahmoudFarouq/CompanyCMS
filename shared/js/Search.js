class Search{
    constructor(tableBuilder){
        this.tableBuilder = tableBuilder;
        this.searchBy = $("#searchBy");
        this.searchInput = $("#searchInput");

        this.searchInput.on('keyup', (e)=>{
            this.tableBuilder.buildTable({
                'search_by':this.searchBy.val(),
                'search_term':this.searchInput.val()+""
            });
        });
    }

}