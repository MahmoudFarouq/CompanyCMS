/*
    NOTE:
        for every record coming from the DB, i'm using TWO table rows,
        one for viewing the record data, buttons for delete and update,
        and the second for {
            on clicking the (update|view) button, it shows up with a form 
            having all it's fields filled with the User's data,
            and it's editable and submitable,

            on submitting :
                the form data will be saved for that user
            on re-clicking the (update|view):
                the row will just get emptied
        }
*/

class RowBuilder{

    constructor(rowData, tableBuilder){

        this.showingEditRow = false;
        this.tableBuilder = tableBuilder;

        this.id = rowData.id;
        this.name = rowData.name;
        this.email = rowData.email;
        this.phone = rowData.phone;
        this.birth_date = rowData.birthdate;

        if(tableBuilder.target == 'customer')
            this.plan = rowData.plan;

        this.status = rowData.status;
    }

    buildRow(){
        let viewRow = $("<tr></tr>");
        let editRow = $("<tr>dasda</tr>");
        let updateButton = $(`<td><button type="button" class="btn btn-info">View|Update</button></td>`);
        let deleteButton = $(`<td><button type="button" class="btn btn-danger">Delete</button></td>`);
        let statusButton = $(`<td><button type="button" class="btn">${this.status.status_name}</button></td>`);
        
        let planButton = null;
        if(this.plan){
            planButton = $(`<td><button type="button" class="btn">${this.plan.plan_name}</button></td>`);
            planButton.find('button').css({'width':'100%', 'text-align':'center'});
        }
        
        this.setViewRow(viewRow, statusButton, updateButton, deleteButton, planButton);
        this.setStatusButton(statusButton);
        this.setUpdateButton(updateButton, editRow);
        this.setDeleteButton(deleteButton);
        
        return [viewRow,editRow];
    }

    setDeleteButton(deleteButton){
        deleteButton.click(async ()=>{
            await this.tableBuilder.model.deleteUser(tableBuilder.target, this.id);
            this.tableBuilder.buildTable();
        });
    }

    setUpdateButton(updateButton, editRow){
        updateButton.click(()=>{
            if( this.showingEditRow ){
                editRow.empty();
            }else{
                editRow.load('../../shared/html/updateUserForm.html', ()=>{
                    this.fillForm(editRow)
                });
            }
            this.showingEditRow = !this.showingEditRow;
        });
    }

    setStatusButton(statusButton){
        statusButton.find('button').addClass( this.status.id == 1 ? 'btn-success':'btn-secondary' );
        statusButton.find('button').css({'width':'100%', 'text-align':'center'});
        statusButton.click(()=>{
            this.sendUpdateAndUpdateView(statusButton);
        });
    }

    setViewRow(viewRow, statusButton, updateButton, deleteButton, planButton){
        viewRow.append(
            $(`<td scope='row'>${this.id}</td>`),
            $(`<td>${this.name}</td>`),
            $(`<td>${this.email}</td>`),
            $(`<td>${this.phone}</td>`),
            $(`<td>${this.birth_date}</td>`),
            statusButton
        );
        if(planButton)
            viewRow.append(planButton);
        viewRow.append(
            updateButton,
            deleteButton
        );
    }

    sendUpdateAndUpdateView(statusButton) {
        this.tableBuilder.model.changeUserStatus(
            tableBuilder.target, this.id, this.status.id == 1 ? 2:1
        );
        this.tableBuilder.buildTable();
    }

    fillForm(row){
        
        row.find("#email").val(this.email);
        row.find("#name").val(this.name);
        row.find("#phone").val(this.phone);
        row.find("#birthDate").val(this.birth_date);
        row.find("#active")[0].checked = (this.status.id == 1 ? true:false);

        if(tableBuilder.target == 'customer'){
            row.find("#planSelector").show();
        }else{
            row.find("#planSelector").hide();
        }

        row.find("[type='button']").click(async ()=>{
            await this.tableBuilder.model.updateUser(
                tableBuilder.target,
                this.id,
                row.find("#email").val(),
                row.find("#name").val(),
                row.find("#phone").val(),
                row.find("#birthDate").val(),
                row.find("#active")[0].checked,
                $("[name='plan']:checked").val()
            );
            this.tableBuilder.buildTable();
            this.showingEditRow = !this.showingEditRow;
        });
    }
}