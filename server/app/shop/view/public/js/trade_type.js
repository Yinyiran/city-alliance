/**
 * 商品交易类型配置交互（新增/编辑商品共用）
 */
layui.use(['form'], function () {
    var form = layui.form;

    // 解析初始规则与门店列表（支持多重降级方案）
    var RULES = window.__APPOINTMENT_RULES__;
    var STORE_LIST = window.__STORE_LIST__;

    if (!Array.isArray(STORE_LIST) || !STORE_LIST.length) {
        try {
            var sData = $('#js_store_list_data').val();
            if (sData) STORE_LIST = JSON.parse(sData);
        } catch (e) {}
    }
    if (!Array.isArray(RULES) || !RULES.length) {
        try {
            var rData = $('#js_appointment_rules_data').val();
            if (rData) RULES = JSON.parse(rData);
        } catch (e) {}
    }

    if (!Array.isArray(STORE_LIST)) STORE_LIST = [];
    if (!Array.isArray(RULES)) RULES = [];

    // 若页面内仍未获取到门店列表，自动通过异步接口拉取
    if (!STORE_LIST.length) {
        $.ajax({
            url: ns.url("shop/goods/getStoreList"),
            type: "GET",
            dataType: "JSON",
            success: function (res) {
                if (res.code == 0 && Array.isArray(res.data) && res.data.length > 0) {
                    STORE_LIST = res.data;
                    window.__STORE_LIST__ = STORE_LIST;
                    renderRules();
                }
            }
        });
    }

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

    /**
     * 规范化时间为 HH:mm
     */
    function parseTimeStr(t) {
        t = $.trim(String(t || ''));
        var p = t.split(':');
        if (p.length >= 2) {
            var h = ('0' + parseInt(p[0], 10)).slice(-2);
            var m = ('0' + parseInt(p[1], 10)).slice(-2);
            return h + ':' + m;
        }
        return '';
    }

    /**
     * 获取指定门店的营业时间区间
     */
    function getStoreOpenHours(storeId) {
        if (!storeId) return null;
        for (var i = 0; i < STORE_LIST.length; i++) {
            if (String(STORE_LIST[i].store_id) === String(storeId)) {
                var openDate = $.trim(STORE_LIST[i].open_date || '');
                if (!openDate) return null;
                var parts = openDate.split(/[-~至到]/);
                if (parts.length === 2) {
                    var s = parseTimeStr(parts[0]);
                    var e = parseTimeStr(parts[1]);
                    if (s && e) {
                        return { start: s, end: e, raw: openDate };
                    }
                }
                return null;
            }
        }
        return null;
    }

    /**
     * 门店营业时间展示提示
     */
    function getStoreHoursTip(storeId) {
        var hours = getStoreOpenHours(storeId);
        if (hours) {
            return '<div class="ns-word-aux" style="margin:2px 0 0;font-size:12px;color:#009688;">营业：' + hours.start + ' ~ ' + hours.end + '</div>';
        }
        return '<div class="ns-word-aux" style="margin:2px 0 0;font-size:12px;color:#999;">营业：未设置</div>';
    }

    function storeOptions(selectedId) {
        var html = '<option value="">请选择门店</option>';
        $.each(STORE_LIST, function (i, s) {
            var sel = String(s.store_id) == String(selectedId == null ? '' : selectedId) ? ' selected' : '';
            var openTip = s.open_date ? ' (' + s.open_date + ')' : '';
            html += '<option value="' + s.store_id + '"' + sel + '>' + (s.store_name || ('门店' + s.store_id)) + openTip + '</option>';
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
            var duration = parseInt(item.service_duration || 0, 10);
            if (!duration) duration = parseInt($('#goods_service_duration').val() || 0, 10) || 60;
            html += '<tr>'
                + '<td><select class="layui-input layui-unselect js-rule-store" data-index="' + index + '" lay-filter="rule_store">' + storeOptions(item.store_id) + '</select></td>'
                + '<td>' + weekChecks(index, item.week_days) + '</td>'
                + '<td><input type="time" class="layui-input layui-unselect js-rule-start" data-index="' + index + '" value="' + timeVal(item.start_time, '09:00') + '"> ~ <input type="time" class="layui-input layui-unselect js-rule-end" data-index="' + index + '" value="' + timeVal(item.end_time, '18:00') + '">' + getStoreHoursTip(item.store_id) + '</td>'
                + '<td><input type="number" min="5" class="layui-input layui-unselect js-rule-interval" data-index="' + index + '" value="' + (item.interval_minutes || 30) + '"></td>'
                //服务时长：该时段占用多少分钟，决定时段结束时间与可约人数周转
                + '<td><input type="number" min="0" class="layui-input layui-unselect js-rule-duration" data-index="' + index + '" value="' + duration + '"></td>'
                //提前截止：距开始多少分钟停止接单，用于留出备餐/准备时间
                + '<td><input type="number" min="0" class="layui-input layui-unselect js-rule-stop" data-index="' + index + '" value="' + (item.stop_before_minutes || 0) + '"></td>'
                + '<td><input type="number" min="1" class="layui-input layui-unselect js-rule-max" data-index="' + index + '" value="' + (item.max_num || 1) + '"></td>'
                //启用开关：可临时关闭某条规则而不删除（用原生 checkbox，避免依赖 layui 渲染时序）
                + '<td><label class="ns-rule-switch"><input type="checkbox" class="js-rule-status" data-index="' + index + '" ' + (Number(item.status) === 0 ? '' : 'checked') + '><span>启用</span></label></td>'
                + '<td><a href="javascript:;" class="ns-text-color js-rule-del" data-index="' + index + '">删除</a></td>'
                + '</tr>';
        });
        $('.ns-appointment-rule-table tbody').html(html);
        form.render('select');
        form.render('checkbox');
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
            RULES[index].store_name = (text == '请选择门店') ? '' : text.replace(/\s*\(.*?\)$/, '');
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
        $('.js-rule-duration').each(function () {
            var index = $(this).data('index');
            if (RULES[index]) RULES[index].service_duration = parseInt($(this).val() || 0, 10);
        });
        $('.js-rule-stop').each(function () {
            var index = $(this).data('index');
            if (RULES[index]) RULES[index].stop_before_minutes = parseInt($(this).val() || 0, 10);
        });
        $('.js-rule-status').each(function () {
            var index = $(this).data('index');
            if (RULES[index]) RULES[index].status = this.checked ? 1 : 0;
        });
        //补排序字段，保证多时段规则的先后顺序可控
        for (var i = 0; i < RULES.length; i++) RULES[i].sort = i;
        $('.js-appointment-rules').val(JSON.stringify(RULES));
    }

    //规则合法性提示：避免商户保存出「无法被预约」的规则
    function checkRules() {
        var enabledCount = 0;
        for (var i = 0; i < RULES.length; i++) {
            var r = RULES[i];
            if (Number(r.status) !== 0) enabledCount++;
            if (!r.store_id) return '第 ' + (i + 1) + ' 条规则未选择门店';
            if (!r.week_days || !r.week_days.length) return '第 ' + (i + 1) + ' 条规则未选择可预约星期';
            if (!r.start_time || !r.end_time) return '第 ' + (i + 1) + ' 条规则未填写时间段';

            var rStart = parseTimeStr(r.start_time);
            var rEnd = parseTimeStr(r.end_time);
            if (!rStart || !rEnd) return '第 ' + (i + 1) + ' 条规则的时间段格式不正确';
            if (rStart >= rEnd) return '第 ' + (i + 1) + ' 条规则的开始时间必须早于结束时间（暂不支持跨天时段）';

            // 营业时间区间校验：不能超出门店营业时间区间
            var storeHours = getStoreOpenHours(r.store_id);
            if (storeHours) {
                if (rStart < storeHours.start || rEnd > storeHours.end) {
                    var sName = r.store_name || ('门店' + r.store_id);
                    return '第 ' + (i + 1) + ' 条规则的时间段（' + rStart + ' ~ ' + rEnd + '）超出门店【' + sName + '】的营业时间区间（' + storeHours.start + ' ~ ' + storeHours.end + '）';
                }
            }

            if (!(parseInt(r.interval_minutes, 10) > 0)) return '第 ' + (i + 1) + ' 条规则的预约间隔必须大于 0';
            if (!(parseInt(r.max_num, 10) > 0)) return '第 ' + (i + 1) + ' 条规则的「同时接待」人数必须大于 0';
            var duration = parseInt(r.service_duration, 10) || 0;
            var interval = parseInt(r.interval_minutes, 10);
            if (duration > 0 && duration < interval) {
                return '第 ' + (i + 1) + ' 条规则的服务时长(' + duration + '分) 小于预约间隔(' + interval + '分)，会造成时段重叠';
            }
        }
        //至少要有一条启用中的规则，否则顾客端永远看不到可约时段
        if (RULES.length > 0 && enabledCount === 0) {
            return '预约规则已被全部禁用，顾客将无法预约，请至少启用一条';
        }
        return '';
    }
    window.nsCheckAppointmentRules = checkRules;

    $(document).on('click', '.ns-app-add-rule', function () {
        var defaultStoreId = STORE_LIST.length > 0 ? STORE_LIST[0].store_id : '';
        var defaultStoreName = STORE_LIST.length > 0 ? STORE_LIST[0].store_name : '';
        var defaultStart = '09:00:00';
        var defaultEnd = '18:00:00';

        if (defaultStoreId) {
            var hours = getStoreOpenHours(defaultStoreId);
            if (hours) {
                defaultStart = hours.start + ':00';
                defaultEnd = hours.end + ':00';
            }
        }

        RULES.push({
            store_id: defaultStoreId,
            store_name: defaultStoreName,
            week_days: ['1', '2', '3', '4', '5', '6', '7'],
            start_time: defaultStart,
            end_time: defaultEnd,
            interval_minutes: 30,
            max_num: 1,
            //服务时长默认跟随商品「服务时长」，提前截止默认 30 分钟
            service_duration: parseInt($('#goods_service_duration').val() || 0, 10) || 60,
            stop_before_minutes: 30,
            status: 1,
            sort: RULES.length
        });
        renderRules();
    });

    $(document).on('click', '.js-rule-del', function () {
        RULES.splice($(this).data('index'), 1);
        renderRules();
    });

    $(document).on('change', '.js-week', saveRules);
    $(document).on('change keyup', '.js-rule-start,.js-rule-end,.js-rule-interval,.js-rule-max,.js-rule-duration,.js-rule-stop', saveRules);
    $(document).on('change', '.js-rule-status', saveRules);
    $(document).on('change', '.js-rule-store', function () {
        var idx = $(this).data('index');
        var storeId = $(this).val();
        if (RULES[idx]) {
            RULES[idx].store_id = storeId;
            var hours = getStoreOpenHours(storeId);
            if (hours) {
                var curS = parseTimeStr(RULES[idx].start_time);
                var curE = parseTimeStr(RULES[idx].end_time);
                if (!curS || curS < hours.start || curS >= hours.end) {
                    RULES[idx].start_time = hours.start + ':00';
                }
                if (!curE || curE > hours.end || curE <= hours.start) {
                    RULES[idx].end_time = hours.end + ':00';
                }
            }
        }
        renderRules();
    });

    //初始化
    $(function () {
        var current = $('input[name=trade_type]:checked').val() || 0;
        toggleTradeBox(current);
        renderRules();
    });

});
