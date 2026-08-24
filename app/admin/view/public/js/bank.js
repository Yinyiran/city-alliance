/**
 * Created by Administrator on 2019/12/13.
 */
var bank_form;

//获取所有的省
function getBank(dom){
    var init_id = $(dom).attr('data-init');
    var filter = $(dom).attr('lay-filter');
    $.ajax({
        type : "post",
        url : ns.url('admin/bank/getBank'),
        dataType : "json",
        success : function(data) {
            if (data != null && data.length > 0) {
                var str = "<option value='-1'>请选择银行号</option>";
                for (var i = 0; i < data.length; i++) {
                    if(init_id == data[i].id){
                        str += '<option value="'+data[i].code+'" selected>'+data[i].name+'</option>';
                    }else{
                        str += '<option value="'+data[i].code+'" >'+data[i].name+'</option>';
                    }
                }
                $(dom).html(str);
                $(dom).attr('data-init', '-1');
                bank_form.render("select");
            }
        }
    });
}

//获取某个省所有的市
function getBranch(dom, bank_num) {
    var init_id = $(dom).attr('data-init');
    var filter = $(dom).attr('lay-filter');

    $.ajax({
        type : "post",
        url : ns.url('admin/bank/getBranchCode'),
        dataType : "json",
        data : {
            "bank_num" : bank_num
        },
        async:false,
        success : function(data) {
            if (data != null && data.length > 0) {
                var str = "<option value='-1'>请选择支行</option>";
                for (var i = 0; i < data.length; i++) {
                    if(init_id ==data[i].id){
                        str += '<option value="'+data[i].serial_number+'" selected="selected">'+data[i].serial_name+'</option>';
                    }else{
                        str += '<option value="'+data[i].serial_number+'">'+data[i].serial_name+'</option>';
                    }
                }
                $(dom).html(str);
                $(dom).attr('data-init', '-1');
                bank_form.render("select");
            }
        }
    });
}



//初始化操作
function initBank(form){
    bank_form = form;
    $("[data-flag='bank']").each(function(){
        var bank_dom = $(this).find("select[data-type='bank']");
        //判断是否要初始化加载市列表
        var init_bank_id = bank_dom.attr('data-init');
        getBank(bank_dom);
        if(init_bank_id){
            var branch_code_dom = $(this).find("select[data-type='branch_code']");
            //判断是否要初始化加载区列表
            var init_branch_code_id = branch_code_dom.attr('data-init');
            getBranch(branch_code_dom, init_bank_id);

        }
    });

    //省改变以后触发的事件
    $("[data-flag='bank'] select[data-type='bank']").each(function(){
        var that = this;
        var filter = $(that).attr('lay-filter');
        form.on('select('+ filter +')', function(data){
            var bank_dom = $(that).parents("[data-flag='bank']");
            var bank_code = data.value;
            console.error(bank_code)
            $(that).val(bank_code);
            var branch_code_dom = bank_dom.find("select[data-type='branch_code']");
            getBranch(branch_code_dom, bank_code);
            form.render("select");
        })
    });

    //市改变以后触发的事件
    $("[data-flag='bank'] select[data-type='branch_code']").each(function(){
        var that = this;
        var filter = $(that).attr('lay-filter');
        form.on('select('+ filter +')', function(data){
            var branch_code = data.value;
            $(that).val(branch_code);
        })
    });



}