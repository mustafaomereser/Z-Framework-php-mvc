@php
use zFramework\Core\Facades\Lang;
$lang_list = Lang::list();
@endphp

<!DOCTYPE html>
<html lang="{{ Lang::$locale }}" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>zFramework</title>

    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= asset('/assets/libs/notify/style.css') ?>" />
    <link rel="stylesheet" href="<?= asset('/assets/css/style.css') ?>" />
    @yield('header')
</head>

<body>
    <div class="container my-lg-5 my-2">
        <div class="clearfix">
            <div class="float-start">
                <a href="https://github.com/mustafaomereser/Z-Framework-php-mvc" target="_blank">Github & Docs</a>
            </div>
            <div class="float-end">
                <div class="d-flex align-items-center gap-2">
                    <div id="auth-content"></div>

                    <div class="btn-group">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 100px">
                            {{ _l('lang.languages') }}
                        </button>
                        <ul class="dropdown-menu">
                            @foreach($lang_list as $lang)
                            <li>
                                <a class="dropdown-item {{ Lang::currentLocale() == $lang ? 'active' : null }}" href="{{ route('language', ['lang' => $lang]) }}">
                                    {{ config("languages.$lang") }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @yield('body')

        <div class="row text-center">
            <div class="col-lg-6 col-12 text-lg-start">
                <a href="/api/v1">API</a>
            </div>
            <div class="col-lg-6 col-12 text-lg-end">
                <small data-toggle="tooltip" title="zFramework Version"><b>zFramework</b> v{{ FRAMEWORK_VERSION }}</small>
                <small data-toggle="tooltip" title="PHP Version">| <b>PHP</b> v{{ PHP_VERSION }}</small>
                <small data-toggle="tooltip" title="Current Project Version">| <b>APP</b> v{{ config('app.version') }}</small>
            </div>
        </div>
    </div>

    <div id="load-modals"></div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/main.js"></script>
    <script src="/assets/libs/notify/script.js"></script>

    @yield('footer')
</body>

</html>