// Url stuffs
let url = document.URL.split('/').pop().split('?').pop(), query = {};
url.split('&').forEach(item => {
    let e = item.split('=');
    if (e[1]) query[e[0]] = e[1];
});
//
var currentRoute;

function initRouters() {
    try {
        document.querySelectorAll('[data-route]').forEach(i => i.classList.remove('active'));
        document.querySelector(`[data-route="${currentRoute}"]`).classList.add('active');
    } catch (err) { }
    document.querySelectorAll('[data-route]').forEach(e => {
        e.onclick = function () {
            let route = this.getAttribute('data-route');
            // if (currentRoute == route) return;

            route = route.split('-');
            loadModule(route[0], route[1]);
        };
    });
}

function initPrism() {
    document.querySelectorAll('[data-prism-lang]').forEach(item => {
        let lang = item.getAttribute('data-prism-lang');
        item.innerHTML = Prism.highlight(item.textContent, Prism.languages[lang], lang);
    });
}

function loadModule(page = 'home', section = 'index') {
    var oReq = new XMLHttpRequest();

    oReq.onload = function () {
        currentRoute = `${page}-${section}`;
        window.history.pushState({}, '', `?page=${page}&section=${section}`);
        document.querySelector('#root').innerHTML = this.responseText;
        initPrism();
        initRouters();
    };

    oReq.open("GET", `/pages/${page}/${(getLang())}/${section}.html`);
    oReq.send();
}

loadModule(query.page, query.section);