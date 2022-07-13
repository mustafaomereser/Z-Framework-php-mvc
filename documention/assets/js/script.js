var defaultLang = "en",
    languages = {
        tr: {
            title: 'Z-Framework Dökümantasyonu',
            name: "Türkçe",
            languages: 'Diller',
            copy: 'Kopyala',
            copied: 'Kopyalandı!',

            "1_route": 'Yönlendiriciler',
            "1_1_route": 'Form Örnekleri',
            "1_2_route": 'Yönlendirici Ayarları',
            "1_3_route": 'Yönlendirici URL Bul',

            "2_model": 'Modeller',
            "2_1_model": 'Kullanıcı',
            "2_2_model": 'Gözlemciler',
            "2_3_model": 'Şablonlar',
            "2_4_model": 'Besleyiciler',

            "3_view": "Arayüzler",
            "3_1_view": "Arayüz Destekcisi",

            "4_controller": 'Kontrolcü',

            "5_date": 'Tarih',

            "6_mail": 'E-posta',

            "7_zhelper": '(zhelper) Yardımcı',

            "8_csrf": '(CSRF) Siteler Arası İstek Sahteciliği',

            "9_language": 'Dil sistemi',

            "10_crypter": "Şifreleyici",

            "11_config": "Yapılandırma dosyaları",

            "12_alert": "Uyarılar",

            "13_validator": "Onaylayıcı",

            "14_middleware": "Ara katman",

            "15_cache": "Önbellek",

            // "16_api": "API",

            "17_development": "Proje Geliştirme",

            "18_helper_methods": "Yardımcı metodlar",

            "19_run_project": "Projeyi Çalıştırma"
        },
        en: {
            title: "Z-Framework Documention",
            name: "English",
            languages: 'Languages',
            copy: 'Copy',
            copied: 'Copied!',

            "1_route": 'Route',
            "1_1_route": 'Form Examples',
            "1_2_route": 'Route Options',
            "1_3_route": 'Find Route\'s Url',

            "2_model": 'Models',
            "2_1_model": 'User',
            "2_2_model": 'Observers',
            "2_3_model": 'Migrators',
            "2_4_model": 'Seeders',

            "3_view": "Views",
            "3_1_view": "View Providers",

            "4_controller": 'Controller',

            "5_date": 'Date',

            "6_mail": 'Email',

            "7_zhelper": 'zhelper',

            "8_csrf": 'CSRF',

            "9_language": 'Language system',

            "10_crypter": "Crypter",

            "11_config": "Configs",

            "12_alert": "Alerts",

            "13_validator": "Validator",

            "14_middleware": "Middlewares",

            "15_cache": "Caches",

            // "16_api": "API",

            "17_development": "Project Development",

            "18_helper_methods": "Helper Methods",

            "19_run_project": "Run Project"
        }
    };

function initDropdown() {
    document.querySelectorAll('.dropdown-title:not([initilazed])').forEach(dropdown => {
        dropdown.setAttribute('initilazed', true);
        dropdown.onclick = e => e.target.parentElement.classList.toggle('show');
    });
}

function getLang() {
    return (localStorage.getItem('lang') ?? defaultLang);
}

function parseLang() {
    let list = languages[getLang()];
    for (let key of Object.keys(list)) document.querySelectorAll(`[data-setLang="${key}"]`).forEach(item => item.innerHTML = list[key]);
    document.querySelectorAll('[data-lang]').forEach(lang => lang.classList.remove('active'));
    document.querySelector(`[data-lang="${getLang()}"]`).classList.add('active');
}

function setLangList() {
    for (let index of Object.keys(languages))
        document.querySelector('#language-list').innerHTML += `<div class="dropdown-item" data-lang="${index}">${languages[index].name}</div>`;
}

function organizeMenu(ul = null) {
    parseLang();
    document.querySelectorAll('.scroll-spy ul li[data-to]').forEach((item, index) => {
        index = (index + 1);
        let route = item.getAttribute('data-to'), span = item.children[0], ul = item.children[1];

        span.innerHTML = `${index}. ${span.innerHTML}`;
        span.setAttribute('data-route', `${route}-index`);

        try {
            [...ul.children].forEach(item => {
                [...item.children].forEach((ul_item, ul_index) => {
                    ul_index = (ul_index + 1);
                    ul_item.innerHTML = `${index}.${ul_index}. ${ul_item.innerHTML}`;
                    ul_item.setAttribute('data-route', `${route}-${ul_index}`);
                });
            });
        } catch (err) { }
    });

    initRouters();
}

function trimSpaces(text) {
    let split = text.split(''), space_count = 0, finish = 0;
    split.forEach(_ => {
        if (finish) return;
        if (_ != ' ') return finish = 1;
        space_count++;
    });

    return text.trim().replaceAll((' '.repeat(space_count)), '');
}

function initHighlight() {
    document.querySelectorAll('[data-highlight-lang]').forEach(item => {
        item.outerHTML = `
            <div style="position: relative;">
                <pre><code class="language-${item.getAttribute('data-highlight-lang')}">${trimSpaces(item.innerHTML)}</code></pre>
                <button class="btn" style="position: absolute; right: 0px; top: 0;" onclick="copy(this, this.previousElementSibling.children[0].textContent);">${languages[getLang()].copy}</button>
            </div>
        `;
    });
    hljs.highlightAll();
}

function copy(is, text) {
    var copyText = document.getElementById("copy-input");
    copyText.value = text;
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);

    is.innerHTML = languages[getLang()].copied;
    setTimeout(() => is.innerHTML = languages[getLang()].copy, 700);
}


window.onload = () => {
    setLangList();
    initDropdown();
    organizeMenu();

    document.querySelectorAll('[data-lang]').forEach(lang => {
        lang.style.cursor = "pointer";
        lang.onclick = e => {
            localStorage.setItem('lang', e.target.getAttribute('data-lang'))
            organizeMenu();
            let route = currentRoute.split('-');
            loadModule(route[0], route[1]);
        };
    });
};