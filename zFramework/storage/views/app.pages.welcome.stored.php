
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>zFramework</title>

    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/assets/libs/notify/style.css" />
    <link rel="stylesheet" href="/assets/css/style.css" />
    
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
                            Languages                        </button>
                        <ul class="dropdown-menu">
                                                        <li>
                                <a class="dropdown-item active" href="http://192.168.1.18/language/en">
                                    English                                </a>
                            </li>
                                                        <li>
                                <a class="dropdown-item " href="http://192.168.1.18/language/tr">
                                    Türkçe                                </a>
                            </li>
                                                    </ul>
                    </div>
                </div>
            </div>
        </div>

        
<div class="my-5">
    <div class="text-center mb-4">
        <h1>Welcome!</h1>
    </div>
    <div class="card rounded-0">
        <pre class="card-body" style="height: 400px; overflow-y: auto;" id="terminal-body">you can read for more information documention.</pre>
    </div>
    <div class="form-group">
        <form id="terminal-form">
            <input type='hidden' name='_token' value='onJrTstHpyyTFM6zieu9f07xH6FqJW' />            <input type="text" name="command" class="form-control rounded-0" placeholder="Command to Helper Terminal.">
        </form>
    </div>
</div>


        <div class="row text-center">
            <div class="col-lg-6 col-12 text-lg-start">
                <a href="/api/v1">API</a>
            </div>
            <div class="col-lg-6 col-12 text-lg-end">
                <small data-toggle="tooltip" title="zFramework Version"><b>zFramework</b> v2.6.0</small>
                <small data-toggle="tooltip" title="PHP Version">| <b>PHP</b> v7.1.9</small>
                <small data-toggle="tooltip" title="Current Project Version">| <b>APP</b> v1.0.0</small>
            </div>
        </div>
    </div>

    <div id="load-modals"></div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/main.js"></script>
    <script src="/assets/libs/notify/script.js"></script>

    
<script>
    $('#terminal-form').on('submit', function(e) {
        e.preventDefault();
        let data = {};
        $(this).find('[name]').each((index, item) => data[$(item).attr('name')] = item.value);
        $.ajax({
            method: 'POST',
            url: 'http://192.168.1.18',
            data: data,
            success: e => $('#terminal-body').html(e).scrollTop(99999999999),
            error: e => $('#terminal-body').html(JSON.parse(e.responseText).message)
        });
    });
</script>

</body>

</html>


