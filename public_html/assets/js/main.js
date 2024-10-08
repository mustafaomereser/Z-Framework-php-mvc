$.fn.hasAttr = function (name) {
    return this.attr(name) !== undefined;
};

$.fn.sbmt = function (cb) {
    $(this).off('submit').on('submit', function (_) {
        _.preventDefault();
        cb(this, $(this).find('[type="submit"]'));
    });
};

$.showAlerts = alerts => {
    // alerts.forEach(alert => $.notify(alert[0]).show(alert[1]));
    for (let index of Object.keys(alerts)) {
        const alert = alerts[index];
        $.notify(alert[0]).show(alert[1]);
    }
}

$.selectData = (trigger = true, force = false) => $('[data-select]').each((index, item) => {
    is = $(item);
    if (is.hasAttr('data-selected') && force == false) return;

    is.attr('data-selected', true);
    is.attr('data-select').split(',').forEach(e => is.find(`option[value="${e}"]`).prop('selected', true));
    if (trigger) is.trigger('change');
});

$.checkData = (trigger = true) => $('[data-check]:not([data-checked])').each((index, item) => {
    is = $(item);
    is.attr('data-checked', true);
    if (parseInt(is.attr('data-check'))) is.prop('checked', true);
    if (trigger) is.trigger('checked');
});


$.ajaxSetup({
    // beforeSend: jqXHR => $.xhrPool.push(jqXHR),
    // complete: jqXHR => {
    //     let i = $.xhrPool.indexOf(jqXHR);
    //     if (i > -1) $.xhrPool.splice(i, 1);
    // },

    error: e => {
        let response;
        try {
            response = JSON.parse(e.responseText), status = e.status;
        } catch (err) {
            return;
        }

        let errors = {};
        if (status == 406) return location.reload();

        try {
            errors = JSON.parse(response.message) ?? {};
            try {
                alerts = errors.alerts;
                delete errors['alerts'];
                $.showAlerts(alerts);
            } catch (_) { }
            //
            if (errors.redirect) return location.href = errors.redirect;
            //

            for (let index of Object.keys(errors)) errors[index].forEach(text => $.notify('danger').show(text));
        } catch (err) {
            if (response.message != null) $.notify('danger').show(response.message);
        }

        $.core.btn.unset($.core.btn.lastBtn);
        modal_status = 0;

        if (typeof e.error_callback != 'undefined') e.error_callback(e);
    },
});

$.cookie = {
    list: () => {
        let cookies = document.cookie.split('; ');
        let list = {};
        cookies.forEach(item => {
            let explode = item.split('='), name = explode[0], value = "";
            delete explode[0];
            explode.forEach(add => value += add);
            list[name] = value;
        });
        return list;
    },
    get: name => $.cookie.list()[name] ?? null,
    set: (name, value) => document.cookie = `${name}=${value}; expires=Thu, 18 Dec 2040 12:00:00 UTC; path=/`,
    delete: name => document.cookie = `${name}=; expires=Thu, 18 Dec 1970 12:00:00 UTC; path=/`
};

$.core = {
    url: {
        serialize: data => Object.keys(data).map(key => `${key}=${decodeURIComponent(data[key])}`).join('&'),
        deserialize: url => {
            url = decodeURIComponent(url.split('?')[1]).split('&');
            let data = {};

            url.forEach(item => {
                item = item.split('=');
                let key = item[0];
                delete item[0];

                if (item[1]) {
                    data[key] = '';
                    item.forEach((e, i) => data[key] += `${i > 1 ? '=' : ''}${e}`);
                }
            });

            return data;
        }
    },

    inputFilter: (w, inputFilter, errMsg) => {
        $(w).on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function (e) {
            is = $(this);

            if (inputFilter(this.value)) {
                // Accepted value.
                if (["keydown", "mousedown", "focusout"].indexOf(e.type) >= 0) {
                    this.classList.remove("error-validate");
                    this.setCustomValidity("");
                }

                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.classList.add("error-validate");
                this.setCustomValidity(errMsg);
                this.reportValidity();
                is.val(this.oldValue).trigger('input');
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                this.value = "";
            }
        });
    },

    isset: val => typeof val != "undefined" ? true : false,

    SToA: form => {
        let arr = {};

        $(form).find('[name]').each((index, item) => {
            item = $(item);

            const type = item.attr('type');

            let verify = 0;
            if (type == 'checkbox' || type == 'radio') {
                if (item.is(':checked')) verify++;
            } else {
                verify++;
            }

            if (verify == 1) arr[item.attr('name')] = item.val() ?? item.html();
        });

        return arr;
    },

    copy: value => {
        navigator.clipboard.writeText(value);
    },

    numberFormat: (number, decimals, dec_point, thousands_sep) => {
        // Strip all characters but numerical ones.
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');

        if (s[0].length > 3) s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);

        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    },

    cformat: (number, symbol) => {
        const symbols = { eur: '€', usd: '$', tl: '₺' };
        return $.core.numberFormat(number, 2, ',', '.') + (symbol ? symbols[symbol.toLowerCase()] : '');
    },

    cleanFormat: strg => {
        strg = strg || "";
        let decimal = '.';
        strg = strg.replace(/[^0-9$.,]/g, '');
        if (strg.indexOf(',') > strg.indexOf('.')) decimal = ',';
        if ((strg.match(new RegExp("\\" + decimal, "g")) || []).length > 1) decimal = "";
        if (decimal != "" && (strg.length - strg.indexOf(decimal) - 1 == 3) && strg.indexOf("0" + decimal) !== 0) decimal = "";
        strg = strg.replace(new RegExp("[^0-9$" + decimal + "]", "g"), "");
        strg = strg.replace(',', '.');
        return parseFloat(strg);
    },

    btn: {
        lastBtn: null,
        spin: (btn, timeSec = 0) => {
            btn = $(btn);
            let tagName;
            if (btn.length) tagName = btn[0].tagName;

            $.core.btn.freeze(btn, timeSec);
            const content = btn.html();

            btn.addClass('position-relative').html(`
				<text style="visibility: hidden; opacity: 0;">${content}</text>
				<div class="position-absolute absolute-center h-100" style="top: ${tagName == 'BUTTON' ? '20' : '0'}%;">
					<span class="fas fa-spinner fa-spin"></span>
				</div>
			`);

            return $.core.btn;
        },

        freeze: (btn, timeSec = 0) => {
            btn = $(btn);
            btn.addClass('disabled').attr('disabled', true);

            if (timeSec) $.core.btn.unset(btn, timeSec);

            $.core.btn.lastBtn = btn;

            return $.core.btn;
        },

        message: (btn, data) => {
            btn = $(btn);
            // $.core.btn.unset(btn);
            $.core.btn.freeze(btn);

            let color = data.color ?? 'secondary';
            btn.addClass(`bg-${color}`).addClass('text-white');

            if (data.text) btn.html(data.text);

            return $.core.btn;
        },

        unset: (btn, timeSec = 0) => {
            btn = $(btn);
            setTimeout(() => {
                btn.removeClass('disabled').removeAttr('disabled').removeClass('position-relative');

                let text = btn.find('text');
                while (text.length) {
                    btn.html(text.html());
                    text = text.find('text');
                }

            }, (timeSec * 1000));

            $.core.btn.lastBtn = null;

            return $.core.btn;
        },
    },

    top: () => $(window).scrollTop(0),

    loading: (i, position = "center") => i.html(`<div class="d-flex align-items-${position} justify-content-${position} h-100"><i class="fa fa-spinner fa-spin me-2"></i> Yükleniyor...</div>`),

    random: length => {
        let result = '', characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', charactersLength = characters.length;
        for (var i = 0; i < length; i++) result += characters.charAt(Math.floor(Math.random() * charactersLength));
        return result;
    },

    loadingDots: () => {
        let id = $.core.random(10), step = 0;
        let interval = setInterval(() => {
            if (!$(`#${id}`).length) clearInterval(interval);
            step++;
            $(`#${id}`).html(('...'.substring(0, step)));
            if (step == 3) step = 0;
        }, 200);

        return `<span id="${id}"></span>`;
    },

    threeDots: (text, length) => {
        r = text.substr(0, length);
        if (text.length > length) r += "...";
        return r;
    },

    location: {
        latitude: 0,
        longitude: 0,

        get: (cb, err) => {
            navigator.geolocation.getCurrentPosition(pos => {
                $.core.location.latitude = pos.coords.latitude;
                $.core.location.longitude = pos.coords.longitude;
                return cb(pos.coords);
            }, err);
        },
    },

    secondsToHours: seconds => {
        var date = new Date(1970, 0, 1);
        date.setSeconds(seconds);
        date = date.toTimeString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, "$1").split(':');

        return {
            'h': date[0],
            'm': date[1],
            's': date[2],
            'seconds': seconds
        };
    },

    stringEscape: s => {
        return s ? s.replace(/\\/g, '\\\\').replace(/\n/g, '\\n').replace(/\t/g, '\\t').replace(/\v/g, '\\v').replace(/'/g, "\\'").replace(/"/g, '\\"').replace(/[\x00-\x1F\x80-\x9F]/g, hex) : s;
        function hex(c) { var v = '0' + c.charCodeAt(0).toString(16); return '\\x' + v.substr(v.length - 2); }
    },

    stripTags: text => text.replace(/(<([^>]+)>)/gi, ""),

    dataPlacer: (item, val) => {
        item = [...$(item)][0];
        if (typeof item == 'undefined') return;

        let j_item = $(item);

        switch (item.tagName) {
            case 'INPUT':
                j_item.val(val);
                break;

            case 'SELECT':
                j_item.find(`option[value="${val}"]`).prop('selected', true);
                break;

            default:
                j_item.html(val);
        }

        return j_item;
    },

    addZero: text => text.toString().length == 1 ? `0${text}` : text
};

//
let modals = $('#load-modals'), modal_status = 0;
var currentModal;
var loaded_modals = [];
var modal_count = 0;
const modalTemplate = `<div class="modal" tabindex="{modal_count}" data-bs-backdrop="static" {modal_appends}>
    <div class="modal-dialog modal-{size}">
        <div class="modal-content">
        <div class="modal-header ps-1">
                <div>
                    ${''/*
    <button onclick="currentModal.modal('hide'); loaded_modals[{modal_count}-1].reopen();" class="btn btn-sm btn-outline-light border" data-toggle="tooltip" title="Önceki Pencere"><i class="fa fa-arrow-left"></i></button>
    <button onclick="currentModal.modal('hide'); loaded_modals[{modal_count}+1].reopen();" class="btn btn-sm btn-outline-light border" data-toggle="tooltip" title="Sonraki Pencere"><i class="fa fa-arrow-right"></i></button>
    <a on-click="loaded_modals[{modal_count}].reopen();" class="btn btn-sm btn-outline-light border me-2" re-open><i class="fa fa-redo text-success" data-toggle="tooltip" title="Pencereyi yenile"></i></a>
    */}
                </div>
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div style="overflow-y: auto;" class="scroll" load></div>
        </div>
    </div>
</div>`;
modalSize = "md";
const loadModal = (url, title, cb) => {
    if (modal_status || !modals.length) return;

    let template = modalTemplate.replace('{size}', modalSize ? modalSize : 'fullscreen').replaceAll('{modal_count}', modal_count);

    modals.append(template);
    let modal = $([...modals.find('.modal')].pop());
    currentModal = modal;

    currentModal.reopen = function () {
        $(this).modal('hide');
        let modal = loadModal(url, title, cb);
        if (this.size) modalResize(modal, this.size);
    };

    modal.find('[re-open]').on('click', () => modal.reopen());

    currentModal.modal_id = modal_count;
    loaded_modals[modal_count] = currentModal;

    if (title) modal.find('.modal-title').html(title);
    modal_status = 1;
    $.get(url, null, e => {
        modal.find('[load]').html(e);
        modal.modal('show');

        // z-index ayarları
        let backdrop = $([...$('.modal-backdrop')].pop());
        modal.css('--bs-modal-zindex', 1092 + modal_count);
        backdrop.css('--bs-backdrop-zindex', 1091 + modal_count)
        //

        init();
        if (cb) cb(modal);
        modal_count++;
        modal_status = 0;
    });

    modal.on('hidden.bs.modal', () => {
        modal.remove();
        currentModal = null;
    });

    return modal;
};
const modalTitle = (modal, newTitle) => modal.find('.modal-title').html(newTitle);
const modalResize = (modal, size) => {
    modal.find('.modal-dialog').attr('class', `modal-dialog modal-${size}`);
    loaded_modals[loaded_modals.indexOf(modal)].size = size;
}
const modalClear = () => {
    modals.find('.modal .btn-close').click();
    modals.html(null);
    $('.modal-backdrop').remove();
}


function initModals() {
    $('[data-modal]:not([installed])').each((index, item) => {
        $(item).css('cursor', 'pointer').on('click', function () {
            let is = $(this).attr('installed', true);
            if (is.hasAttr('data-modal-clear')) modalClear();
            if (!is.hasAttr('data-modal-noload')) $.core.btn.spin(is);

            switch (is.attr('data-modal-type')) {
                case 'replace':
                    $('#load-modals .btn-close').click();
                    break;
            }

            let modal = loadModal($(this).attr('data-modal'), $(this).attr('data-modal-title') ?? null, () => {
                $.core.btn.unset(is);
                if (is.hasAttr('data-modal-callback')) currentModal.customCallback = eval(is.attr("data-modal-callback"));
            });
            if ($(this).attr('data-modal-size')) modalResize(modal, $(this).attr('data-modal-size'));
        });
    });
}

$.system = {
    loadAuthContent: () => {
        $.get('/auth-content', e => {
            $('#auth-content').html(e);
            initModals();
        });
    },
    signout: is => {
        $.core.btn.spin(is);
        $.get('/sign-out', e => {
            $.showAlerts(e.alerts);
            $.system.loadAuthContent();
        });
    }
};

function initTooltips() {
    $('[data-toggle="tooltip"]').tooltip();
}

function init() {
    initModals();
    $.selectData(false);
    $.checkData(false);
    $(() => initTooltips());
}

$.system.loadAuthContent();
init();