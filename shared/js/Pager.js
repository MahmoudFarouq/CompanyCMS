class Pager{

    constructor(tableBuilder){
        this.tableBuilder = tableBuilder;
        this.rowsCountInput = $("#rowsCount");
        this.pagerList = $('.pagination');
        this.prevBtn = $(`<li id="prevButton" class="page-item isDisabled"><a class="page-link" href="#">Previous</a></li>`);
        this.nextBtn = $(`<li id="nextButton" class="page-item"><a class="page-link" href="#">Next</a></li>`);
        
        this.__initPages();
        
        this.rowsCountInput.change(()=>{
            this.__initPages();
            let startFrom = 0;
            let limit = parseInt(this.rowsCountInput.val());
            this.__buildTable(startFrom, limit);
        })

        this.nextBtn.click(()=>{
            let startFrom = this.tableBuilder.startFrom + this.tableBuilder.limit;
            let limit = parseInt(this.rowsCountInput.val());
            this.__buildTable(startFrom, limit);
        });

        this.prevBtn.click(()=>{
            let startFrom = this.tableBuilder.startFrom - this.tableBuilder.limit;
            let limit = parseInt(this.rowsCountInput.val());
            this.__buildTable(startFrom, limit);
        });
    }


    async __initPages(){
        this.pagerList.empty();
        this.pagerList.append(this.prevBtn);
        this.pagerList.append(this.nextBtn);

        this.entriesCount = await this.tableBuilder.model.getUsersCount(tableBuilder.target);
        this.firstPage = 1;

        let rowsPerPage = parseInt(this.rowsCountInput.val());
        if(this.entriesCount%rowsPerPage == 0){
            this.lastPage = this.entriesCount/rowsPerPage;
        }else{
            this.lastPage = this.entriesCount/rowsPerPage + 1;
        }

        for(var i = this.firstPage; i <= this.lastPage; i++){
            let page = $(
                `<li id="${"btn"+i}" class="page-item" value="${i}"><a class="page-link" href="#">${i}</a></li>`
            );
            page.click(()=>{
                let startFrom = (parseInt(page.val())-1)*rowsPerPage;
                let limit = parseInt(this.rowsCountInput.val());
                this.__buildTable(startFrom, limit);
            });
            this.nextBtn.before(page);
        }
    }

    __buildTable(startFrom, limit){
        if(startFrom <= 1){
            this.prevBtn.addClass('isDisabled');
            startFrom = 0;
        }else{
            this.prevBtn.removeClass('isDisabled');
        }
        if(startFrom >= this.entriesCount){
            this.nextBtn.addClass('isDisabled');
            startFrom -= limit;
        }else{
            this.nextBtn.removeClass('isDisabled');
        }

        tableBuilder.buildTable({
            'startFrom':startFrom, 
            'limit':limit
        });
    }
}