
class TableBuilder{

    constructor(){
        this.model = new Model();

        this.startFrom = 0;
        this.limit = parseInt($("#rowsCount").val());
        this.status_filter = ''
        this.plan_filter = ''
        this.sort_by = 'id';
        this.sort_dir = 'asc';
        this.search_by = '';
        this.search_term = '';

        this.target = 'customer';

        this.tbody = $("tbody");
        this.__initSorters();
        this.setFilters();        
    }

    setFilters(){
        $("#statusSelect").change(()=>{
            this.startFrom = 0;
            this.status_filter = $("#statusSelect").val() == 'all' ? '':$("#statusSelect").val();
            this.__build();
        });
        $("#planSelect").change(()=>{
            this.startFrom = 0;
            this.plan_filter = $("#planSelect").val() == 'all' ? '':$("#planSelect").val();
            this.__build();
        });
    }

    buildTable(opts){
        if(opts != null){
            if(opts['startFrom'] != null){
                this.startFrom = Math.max(0, opts['startFrom']);
            }
            if(opts['limit']){
                this.limit = opts['limit'];
            }
            if(opts['target']){
                this.target = opts['target'];
            }
            if(opts['search_term'] && opts['search_term'] != ''){
                this.search_by = opts['search_by'];
                this.search_term = opts['search_term'];
            }else{
                this.search_by = '';
                this.search_term = '';
            }   
        }
        this.__build();
    }

    async __build(){
        if(this.target == 'admin'){
            $("#planCol").hide();
        }else{
            $("#planCol").show();
        }
        if(this.search_term == ''){
            this.search_by = '';
        }

        let records = await this.model.getRecords(
            this.target, 
            this.startFrom, this.limit, 
            this.status_filter, this.plan_filter, 
            this.sort_by, this.sort_dir, 
            this.search_by, this.search_term
        );

        let data = JSON.parse(records);
        this.__fillTable(data);
    }

    __fillTable(data){
        this.tbody.empty();
        this.recordsArray = data;
        data.forEach((record)=>{
            let row = new RowBuilder(record, this);
            this.tbody.append(row.buildRow());
        });
    }

    __initSorters(){
        $("#idHead").click(()=>{
            this.setSortData('id', '#idHead');
        });
        $("#nameHead").click(()=>{
            this.setSortData('name', '#nameHead');
        });
        $("#birthHead").click(()=>{
            this.setSortData('birthdate', '#birthHead');
        });
    }

    setSortData(sortBy, sortElement){
        this.startFrom = 0;
        this.__toggleMark(sortElement)
        this.sort_by = sortBy;
        this.__toggleSortDir();
        this.__build();
    }

    __toggleSortDir(){
        this.sort_dir = (this.sort_dir == 'desc') ? 'asc':'desc';
    }

    __toggleMark(element){
        $(element).find('span').toggleClass('fa-sort-amount-asc');
        $(element).find('span').toggleClass('fa-sort-amount-desc');
    }
}