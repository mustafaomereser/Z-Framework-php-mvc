<form method="POST" action="/1">
    <?= csrf() ?>
    <?= inputMethod('PATCH') ?>
    <button type="submit">Gönder</button>
</form>