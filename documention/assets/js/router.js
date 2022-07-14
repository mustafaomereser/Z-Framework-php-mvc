// Url stuffs
let url = document.URL.split('/').pop().split('?').pop(), query = {};
url.split('&').forEach(item => {
    let e = item.split('=');
    if (e[1]) query[e[0]] = e[1];
});
//
var currentRoute, lastRouteLang;

function initRouters() {
    try {
        document.querySelectorAll('[data-route]').forEach(i => i.classList.remove('active'));
        document.querySelector(`[data-route="${currentRoute}"]`).classList.add('active');
    } catch (err) { }
    document.querySelectorAll('[data-route]').forEach(e => {
        e.onclick = function () {
            let route = this.getAttribute('data-route');
            if (currentRoute == route && lastRouteLang == getLang()) return;
            route = route.split('-');
            loadModule(route[0], route[1]);
        };
    });
}

function loadModule(page = 'home', section = 'index') {
    var oReq = new XMLHttpRequest();

    oReq.onload = function () {
        if (oReq.status != 200) return loadModule('a-documention-errors', oReq.status);

        currentRoute = `${page}-${section}`;
        lastRouteLang = getLang();
        window.history.pushState({}, '', `?page=${page}&section=${section}`);
        document.querySelector('#root').innerHTML = this.responseText;
        initHighlight();
        initRouters();
    };

    oReq.open("GET", `/pages/${page}/${getLang()}/${section}.html`);
    oReq.send();
}

loadModule(query.page, query.section);