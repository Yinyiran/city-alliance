/**
 * 商品交易类型配置交互（新增/编辑商品共用）
 */
layui.use(['form'], function () {
    var form = layui.form;
    var RULES = window.__APPOINTMENT_RULES__ || [];
    var STORE_LIST = window.__STORE_LIST__ || [];
    //交易类型切换
    function toggleTradeBox(tradeType) {
        tradeType = String(tradeType == null ? 0 : tradeType);
        $('.js-trade-box').each(function () {
            var types = String($(this).data('trade')).split(',');
            $(this).css('display', types.indexOf(tradeType) > -1 ? '' : 'none');
        });
        //仅到店自提(0)与快递邮寄(3)需要物流配送信息，其余为到店/同城场景
        var needShipping = tradeType === '0' || tradeType === '3';
        $('.js-shipping-card').css('display', needShipping ? '' : 'none');
    }

    form.on('radio(trade_type)', function (data) { toggleTradeBox(data.value); });

    function storeOptions(selectedId) {
        var html = '<option value="">请选择门店</option>';
        $.each(STORE_LIST, function (i, s) {
            var sel = String(s.store_id) == String(selectedId == null ? '' : selectedId) ? ' selected' : '';
            html += '<option value="' + s.store_id + '"' + sel + '>' + (s.store_name || ('门店' + s.store_id)) + '</option>';
        });
        return html;
    }

    function weekChecks(index, weekDays) {
        var days = String(weekDays || '').split(',');
        var names = ['一', '二', '三', '四', '五', '六', '日'];
        var html = '';
        for (var d = 1; d <= 7; d++) {
            var ck = days.indexOf(String(d)) > -1 ? ' checked' : '';
            html += '<label class="layui-form-checkbox ns-week-label"><input type="checkbox" class="js-week" data-index="' + index + '" data-day="' + d + '"' + ck + '><span>周' + names[d - 1] + '</span></label>';
        }
        return html;
    }

    function timeVal(v, def) {
        v = String(v || '');
        return v.length >= 5 ? v.substring(0, 5) : def;
    }

    function renderRules() {
        var html = '';
        $.each(RULES, function (index, item) {
            html += '<tr>'
                + '<td><select class="layui-input layui-unselect js-rule-store" data-index="' + index + '" lay-filter="rule_store">' + storeOptions(item.store_id) + '</select></td>'
                + '<td>' + weekChecks(index, item.week_days) + '</td>'
                + '<td><input type="time" class="layui-input layui-unselect js-rule-start" data-index="' + index + '" value="' + timeVal(item.start_time, '09:00') + '"> ~ <input type="time" class="layui-input layui-unselect js-rule-end" data-index="' + index + '" value="' + timeVal(item.end_time, '18:00') + '"></td>'
                + '<td><input type="number" min="5" class="layui-input layui-unselect js-rule-interval" data-index="' + index + '" value="' + (item.interval_minutes || 30) + '"></td>'
                + '<td><input type="number" min="1" class="layui-input layui-unselect js-rule-max" data-index="' + index + '" value="' + (item.max_num || 1) + '"></td>'
                + '<td><a href="javascript:;" class="ns-text-color js-rule-del" data-index="' + index + '">删除</a></td>'
                + '</tr>';
        });
        $('.ns-appointment-rule-table tbody').html(html);
        form.render('select');
        saveRules();
    }

    //采集规则到隐藏域
    function saveRules() {
        $('.js-week').each(function () {
            var index = $(this).data('index');
            var day = String($(this).data('day'));
            if (!RULES[index]) return;
            RULES[index].week_days = RULES[index].week_days || [];
            var pos = RULES[index].week_days.indexOf(day);
            if (this.checked && pos < 0) RULES[index].week_days.push(day);
            if (!this.checked && pos > -1) RULES[index].week_days.splice(pos, 1);
        });
        $('.js-rule-store').each(function () {
            var index = $(this).data('index');
            if (!RULES[index]) return;
            RULES[index].store_id = $(this).val();
            var text = $(this).find('option:selected').text();
            RULES[index].store_name = (text == '请选择门店') ? '' : text;
        });
        $('.js-rule-start').each(function () {
            var index = $(this).data('index');
            if (RULES[index] && $(this).val()) RULES[index].start_time = $(this).val() + ':00';
        });
        $('.js-rule-end').each(function () {
            var index = $(this).data('index');
            if (RULES[index] && $(this).val()) RULES[index].end_time = $(this).val() + ':00';
        });
        $('.js-rule-interval').each(function () {
            var index = $(this).data('index');
            if (RULES[index]) RULES[index].interval_minutes = parseInt($(this).val() || 30, 10);
        });
        $('.js-rule-max').each(function () {
            var index = $(this).data('index');
            if (RULES[index]) RULES[index].max_num = parseInt($(this).val() || 1, 10);
        });
        $('.js-appointment-rules').val(JSON.stringify(RULES));
    }

    $(document).on('click', '.ns-app-add-rule', function () {
        RULES.push({
            store_id: '', store_name: '',
            week_days: ['1', '2', '3', '4', '5', '6', '7'],
            start_time: '09:00:00', end_time: '18:00:00',
            interval_minutes: 30, max_num: 1, stop_before_minutes: 60, status: 1
        });
        renderRules();
    });

    $(document).on('click', '.js-rule-del', function () {
        RULES.splice($(this).data('index'), 1);
        renderRules();
    });

    $(document).on('change', '.js-week', saveRules);
    $(document).on('change keyup', '.js-rule-start,.js-rule-end,.js-rule-interval,.js-rule-max', saveRules);
    $(document).on('change', '.js-rule-store', function () {
        saveRules();
        form.render('select');
    });

    //初始化
    $(function () {
        var current = $('input[name=trade_type]:checked').val() || 0;
        toggleTradeBox(current);
        renderRules();
    });

});
