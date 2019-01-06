
$(document).ready(function(){
    tableBuilder = new TableBuilder();
    tableBuilder.buildTable();
    pager = new Pager(tableBuilder);
    navController = new NavController(tableBuilder);
    search = new Search(tableBuilder);
});