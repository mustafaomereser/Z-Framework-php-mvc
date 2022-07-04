<?php

function errorHandler($data)
{
    $message = $data[0];
    $errors = [];
    foreach ($data[5] as $error) $errors[$error['file']][] = $error;

    function getButtons($errors)
    {
        foreach ($errors as $key => $val) :
            if (!is_numeric($key)) {
?>
                <div class="button-list">
                    <div class="title">
                        <?= $key ?>
                    </div>
                    <ul>
                        <?= getButtons($val) ?>
                    </ul>
                </div>
            <?php
                continue;
            }
            ?>
            <li>
                <button data-button>
                    Line: <b><?= $val['line'] ?></b>
                </button>
            </li>
        <?php
        endforeach;
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

                    $str_line_count = $line_count;
                    if (strlen($line_count) < 2) $str_line_count = "0$str_line_count";

                    $line = "$str_line_count. " . htmlspecialchars($line);

                    if ($line_start > $line_count) continue;
                    if ($line_count == $val['line']) $code .= "<font color=red>$line</font>";
                    else $code .= $line;

                    if ($line_count >= $line_end) break;
                }
                fclose($file);
            }
        ?>
            <div class="code-block">
                <div style="border-bottom: 3px dotted #ddd; padding: 0 0 15px 0">
                    <?php foreach (['Line' => $val['line'], 'Method' => @$val['class'] . @$val['type'] . @$val['function'], 'Arguments' => var_export($val['args'], true)] as $title => $val) : ?>
                        <div>
                            <b><?= $title ?>: </b> <?= $val ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <pre><?= $code ?></pre>
            </div>
    <?php endforeach;
    }
    ?>

    <style>
        body {
            background-color: #444;
        }

        .container {
            margin: 5vw 17vw;
        }

        .box {
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #eee;
        }

        .button-list {
            background-color: #ddd;
            word-wrap: break-word;
        }

        .button-list .title {
            padding: 25px 10px;
            font-size: 10pt;
        }

        .error-list button {
            width: 100%;
            background-color: #eee;
            border: unset;
            padding: 25px 10px;
            word-wrap: break-word;
            font-size: 9pt;
        }

        .error-list button:hover,
        .error-list button.active {
            background-color: #ddd;
        }

        .code-block {
            display: none;
        }

        .code-block.show {
            display: block;
        }
    </style>

    <div class="container">
        <div class="box" style="width: 96.6%">
            <div><?= $data[3] ?></div>
            <small style="color: gray;"><?= $message ?></small>
        </div>
        <div style="display: flex; width: 100%">
            <div class="box error-list" style="width: 20%; padding: 0;">
                <?= getButtons($errors) ?>
            </div>
            <div class="box" style="width: 100%; overflow: auto;">
                <?= getErrors($errors) ?>
            </div>
        </div>
    </div>

    <script>
        let error_buttons = document.querySelectorAll('[data-button]');
        let codes = document.querySelectorAll('.code-block');

        function selectError(id) {
            error_buttons.forEach(item => item.classList.remove('active'));
            codes.forEach(item => item.classList.remove('show'));
            error_buttons[id].classList.add('active');
            codes[id].classList.add('show');
        }

        error_buttons.forEach((item, index) => {
            item.onclick = () => selectError(index);
        });

        selectError(0);
    </script>
<?php
}
