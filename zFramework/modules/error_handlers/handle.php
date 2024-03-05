<?php

use zFramework\Core\Facades\Auth;
use zFramework\Core\Facades\Config;

function errorHandler($data)
{
    ob_end_clean();
    $data = array_values((array) $data);
    $message = $data[0];
    if (!Config::get('app.debug')) abort(500, $message);

    $errors = [$data[3] => [['file' => $data[3], 'line' => $data[4]]]];
    foreach ($data[5] as $error) if (isset($error['file'])) $errors[$error['file']][] = $error;

    function getButtons($errors)
    {
        foreach ($errors as $key => $val) {
            if (!is_numeric($key)) {
                $id = uniqid("codeblock-");
?>
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button text-break" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $id ?>" aria-expanded="true" aria-expanded="true" aria-controls="<?= $id ?>">
                            <b><?= $key ?></b>
                        </button>
                    </h2>
                    <div id="<?= $id ?>" class="accordion-collapse collapse show">
                        <div class="list-group">
                            <?= getButtons($val) ?>
                        </div>
                    </div>
                </div>
            <?php
                continue;
            }
            ?>
            <a href="javascript:;" data-button class="list-group-item list-group-item-action">
                Line: <b><?= $val['line'] ?></b>
            </a>
        <?php
        }
    }

    function getErrors($errors)
    {
        foreach ($errors as $key => $val) :
            if (!is_numeric($key)) {
                getErrors($val);
                continue;
            }
            // $file = htmlspecialchars(file_get_contents($val['file']));
            $file = fopen($val['file'], "r");

            $show_line = 40;
            $line_count = 0;
            $line_start = $val['line'] - $show_line;
            $line_end = $val['line'] + $show_line;

            $code = '';

            if ($file) {
                while (($line = fgets($file)) !== false) {
                    $line_count++;
                    if ($line_start > $line_count) continue;

                    $str_line_count = $line_count;

                    // $caret = strlen($line);
                    $line = str_pad($str_line_count, 2, "0", STR_PAD_LEFT) . ". " . htmlspecialchars($line);

                    // if ($line_count == $val['line']) $code .= "<a href='javascript:goIDE(`" . str_replace("\\", "/", $val['file']) . "`, " . $val['line'] . ", $caret);' style='color: red; text-decoration: none;'>$line</a>";
                    // else $code .= $line;

                    $is_line = $line_count == $val['line'];

                    $code .= ($is_line ? "!*" : null) . $line;

                    if ($line_count >= $line_end) break;
                }
                fclose($file);
            }

            $id = uniqid();
        ?>

            <div class="code-block d-none h-100">
                <div class="tab-content h-100">
                    <div class="p-2">
                        <?php foreach (['Line' => $val['line'], 'Method' => @$val['class'] . @$val['type'] . @$val['function'], 'Arguments' => (@$val['args'] ? "<pre>" . var_export($val['args'], true) . "</pre>" : null)] as $title => $error) : if (!$error) continue; ?>
                            <div>
                                <b><?= $title ?>:</b> <code><?= $error ?></code>
                                <?php if ($title == 'Line') : ?>
                                    <a href="javascript:goIDE(`<?= str_replace("\\", "/", $val['file']) ?>`, <?= $error ?>, 9999);" class="fw-bold">(Go to line)</a>
                                <?php endif ?>
                            </div>
                        <?php endforeach ?>
                    </div>

                    <pre class="language-js h-100"><code class="language-js h-100"><?= $code ?></code></pre>
                </div>
            </div>
        <?php endforeach ?>
    <?php } ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.4/css/all.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.15.0/themes/prism.min.css" />

        <style>
            body {
                background: #e5e7eb;
            }

            .btn {
                text-decoration: none;
            }

            [onclick] {
                cursor: pointer;
            }
        </style>
    </head>

    <body>
        <div class="container mt-2">
            <div class="clearfix mb-3">
                <div class="float-start">
                    <button class="btn btn-sm btn-link fw-bold"><i class="fa fa-share"></i> SHARE</button>
                </div>
                <div class="float-end">
                    <div class="d-flex align-items-center">
                        <a href="https://github.com/mustafaomereser/Z-Framework-php-mvc" class="btn btn-sm btn-link fw-bold" target="_blank"><i class="fa fa-file-word"></i> DOCS</a>

                        <div class="dropdown">
                            <button class="btn btn-sm btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <section class="mb-3">
                <div class="row">
                    <div class="col-7 pe-1" id="error-message">
                        <div class="h-100 bg-body-tertiary border rounded-3 position-relative">
                            <div class="d-flex align-items-center gap-2 text-muted position-absolute" style="top: 5px; right: 10px;">
                                <div><i class="fab fa-lg fa-php"></i> <?= phpversion() ?></div>
                                <div><i class="fab fa-lg fa-sketch"></i> <?= FRAMEWORK_VERSION ?></div>
                            </div>

                            <div class="p-5">
                                <a href='javascript:goIDE(`<?= str_replace("\\", "/", $data[3]) ?>`, <?= $data[4] ?>);' style="color: black;">
                                    <h5><?= $data[3] . ":" . $data[4] ?></h5>
                                </a>
                                <div class="text-muted"><?= $message ?></div>
                            </div>

                            <select name="IDE" onchange="document.cookie = 'IDE=' + this.value + '; expires=Sun, 1 Jan <?= date('Y') + 1 ?> 00:00:00 UTC; path=/'">
                                <?php foreach (['vscode' => 'Visual Studio Code', 'phpstorm' => 'PHPStorm'] as $val => $title) : ?>
                                    <option value="<?= $val ?>" <?= @$_COOKIE['IDE'] == $val ? ' selected' : null ?>><?= $title ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-5 ps-1" id="did-you-mean">
                        <div class="border rounded-3 h-100 position-relative" style="background: #6ee7b7;">
                            <div class="text-end position-absolute" style="top: 5px; right: 10px;" onclick="$('#did-you-mean').remove(); $('#error-message').removeClass('col-7').addClass('col-12');">
                                <i class="fa fa-times text-muted"></i>
                            </div>
                            <div class="p-5">
                                <div>
                                    <h4>Bad Method Call</h4>
                                </div>
                                <div>
                                    did you mean App\Models\Post::query() ?
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-2">
                <div class="bg-body-tertiary rounded-3">
                    <div class="row" style="min-height: 700px">
                        <div class="col-3 border-end pe-0">
                            <div class="accordion">
                                <?= getButtons($errors) ?>
                            </div>
                        </div>
                        <div class="col-9 ps-0">
                            <?= getErrors($errors) ?>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="fw-bold">User</div>
                            <div>
                                <pre><code><?php print_r(Auth::check() ? Auth::user() : []) ?></code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="fw-bold">Requests</div>
                            <div>
                                <pre><code><?php print_r($_REQUEST) ?></code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="fw-bold">Server</div>
                            <div>
                                <pre><code><?php print_r($_SERVER) ?></code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="fw-bold">Globals</div>
                            <div>
                                <pre><code><?php print_r($GLOBALS) ?></code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="fw-bold">Sessions</div>
                            <div>
                                <pre><code><?php print_r($_SESSION) ?></code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="fw-bold">Cookies</div>
                            <div>
                                <pre><code><?php print_r($_COOKIE) ?></code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.15.0/prism.min.js"></script>

        <script>
            error_buttons = document.querySelectorAll('[data-button]');
            codes = document.querySelectorAll('.code-block');

            function selectError(id) {
                error_buttons.forEach(item => item.classList.remove('active'));
                codes.forEach(item => item.classList.add('d-none'));
                error_buttons[id].classList.add('active');
                codes[id].classList.remove('d-none');
            }

            error_buttons.forEach((item, index) => {
                item.onclick = () => selectError(index);
            });

            selectError(0);

            function goIDE(file, line, caret = 0) {
                let val = document.querySelector('[name="IDE"]').value,
                    link = '#';

                switch (val) {
                    case 'vscode':
                        link = `vscode://file/${file}:${line}:${caret}`
                        break;
                    case 'phpstorm':
                        link = `phpstorm://open?url=${file}&line=${line}`;
                        break;
                }
                location.href = link;
            }
        </script>
    </body>

    </html>
<?php
}

set_exception_handler('errorHandler');
