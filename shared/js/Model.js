
class Model{

    constructor(){
        this.URI = 'http://localhost/private/controller.php';
    }

    async getRecords(
                target, 
                offset, limit, 
                status_filter, plan_filter,
                sort_by, sort_dir, 
                search_by, search_term) {
        const URI = `${this.URI}?
                    task=getUsers&target=${target}&
                    offset=${offset}&limit=${limit}&
                    status_filter=${status_filter}&
                    plan_filter=${plan_filter}&
                    order_by=${sort_by}&order_dir=${sort_dir}&
                    search_by=${search_by}&search_term=${search_term}`;
        
        let data = null;
        await $.get(URI, (response, status) => {
            data = response;
        }).fail(function(e) {
            console.log( e );
        });
        return data;
    }

    async changeUserStatus(target, id, newStatusId){
        await $.ajax({
            url: this.URI,
            method: 'POST',
            data:{
                'task':'updateStatus',
                'id':id,
                'target':target,
                'newStatusId':newStatusId
            },
            success: (result) => {

            },
            error: (result) => {
                alert('fail');
            }
        });
    }

    async updateUser(target, id, email, name, phone, birthDate, active, plan){
        await $.ajax({
            url: this.URI,
            method: 'POST',
            data:{
                'task':'updateUser',
                'target':target,
                'id':id,
                'email':email,
                'name':name,
                'phone':phone,
                'birthDate':birthDate,
                'status':(active == true ? '1':'2'),
                'plan':plan
            },
            success: (result) => {
                
            },
            error: (result) => {
                console.log(result);
            }
        });
    }

    async deleteUser(target, id){
        await $.ajax({
            url: this.URI,
            method: 'POST',
            data:{
                'task':'deleteUser',
                'id':id,
                'target':target
            },
            success: (result) => {
            },
            error: (result) => {
                alert('fail');
            }
        });
    }

    async getUsersCount(target) {
        const URI = `${this.URI}?task=getUsersCount&target=${target}`;
        let data = null;
        await $.get(URI, (response, status) => {
            data = response;
        }).fail(function(e) {
            console.log( e );
        });
        return parseInt(JSON.parse(data).count);
    }
}