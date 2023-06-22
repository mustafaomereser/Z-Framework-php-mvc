Users From Provider: (<?= base_path(findFile('ViewProvider', 'php', 'App')) ?>)
<pre>
users[0] Posts:
    <?php print_r($users[0]['posts']()) ?>

all users.
    <?php print_r($users) ?>
</pre>

User From WS:
<pre>
<?php print_r($user) ?>

if you seen: Undefined variable: user, go to `<a href="<?= route('ws.test', ['id' => 1]) ?>"><?= route('ws.test', ['id' => 1]) ?></a>`.
if you still seen nothing add a user to database.
</pre>