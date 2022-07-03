<style>
    fieldset {
        margin-bottom: 15px;
    }

    .pagination {
        display: flex;
        list-style: none;
    }

    .pagination a {
        color: black;
        text-decoration: none;
        margin-right: 5px;
        padding: 5px;
        border-radius: 5px;
        background-color: #ddd;
    }

    .pagination li.active a {
        color: white;
        background-color: orange;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<?php if ($alerts = Core\Facedas\Alerts::get()) : ?>
    <div style="margin-bottom: 20px;">
        Alerts:
        <?php foreach ($alerts as $alert) : ?>
            <div class="alert alert-<?= $alert[0] ?>">
                <?= $alert[0] ?>: <?= $alert[1] ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<fieldset>
    <legend>Paginate Examples</legend>
    <div style="margin-bottom: 15px;">
        <small>Users diffrent paginations With groupBy for username.</small>
    </div>

    <div>
        <?php
        echo "Users List (shown " . str_replace("/", "of", $users['shown']) . ")";
        foreach ($users['items'] as $user) : ?>
            <div>
                <b><?= $users['start']++ ?>.)</b> <?= $user['username'] ?> (<?= $user['usernameCount'] ?>)
            </div>
        <?php endforeach; ?>

        <?= $users['links']() ?>
    </div>

    <div>
        <?php
        echo "Users_2 List (shown " . str_replace("/", "of", $users2['shown']) . ")";

        foreach ($users2['items'] as $user) : ?>
            <div>
                <b><?= $users2['start']++ ?>.)</b> <?= $user['username'] . ' ' . $user['id'] ?>
            </div>
        <?php endforeach; ?>

        <?= $users2['links']() ?>
    </div>
</fieldset>

<fieldset>
    <legend>String Examples</legend>
    <?php $str = 'Occaecat sit nostrud cillum tempor ipsum do laborum laborum culpa. Excepteur consequat aliquip ut labore mollit sunt qui exercitation velit nisi amet. Reprehenderit labore proident veniam magna esse minim ea id. Exercitation minim ipsum fugiat aute consequat minim ipsum eu laborum aliquip. Incididunt veniam deserunt magna excepteur adipisicing aute adipisicing. Deserunt ad ex ea aliqua reprehenderit sit laboris Lorem id.'; ?>

    <h5>have a Lorem paragraph. it's:</h5>
    <div style="margin: 15px 20px;">`<?= $str ?>`</div>

    <h5>and i wanna put a limit for chracter: (You can see how it work on file)</h5>
    <div style="margin: 15px 20px;"><?= Core\Facedas\Str::limit($str, 50, '... <a href="#">Read Continue.</a>') ?></div>

    <h5>And so how i can create a slug? VOALA</h5>
    <div style="margin: 15px 20px;"><?= Core\Facedas\Str::slug('slug text here') ?></div>

    <h5>How i create a random string and what i want length?</h5>
    <div style="margin: 15px 20px;"><?= Core\Facedas\Str::rand(10) ?> <b>That's it!</b></div>

    <h5>But you need a unique random string? Just add a true parameter and TADA</h5>
    <div style="margin: 15px 20px;"><?= Core\Facedas\Str::rand(10, true) ?> <b>That's it!</b></div>
</fieldset>

<fieldset>
    <legend>Auth Examples</legend>

    If you read document you already know methods, I show you your not knowns or i can't show docs i show here:

    <?php if (isset($createdUser) && count($createdUser)) : ?>
        <pre>Created User: <?php print_r($createdUser); ?></pre>
    <?php endif; ?>

    <div>
        <h4>Create User Form</h4>
        <form method="POST">
            <?= csrf() ?>
            <div style="margin-bottom: 10px;">
                <div>
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" />
                </div>
                <div>
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" />
                </div>
                <div>
                    <label for="re-password">Again Password</label>
                    <input type="password" name="re-password" id="re-password" />
                </div>
                <div>
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" />
                </div>
            </div>
            <input type="submit" value="Create" />
        </form>
    </div>
</fieldset>