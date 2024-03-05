<ul class="pagination">
    <?php foreach ($pages as $key => $page) : ?>
        <?php if ($page['type'] == 'page') : ?>
            <li class="<?= $page['current'] ? 'active' : null ?>">
                <a href="<?= $page['url'] ?>">
                    <?= $page['page'] ?>
                </a>
            </li>
        <?php elseif ($page['type'] == 'dot') : ?>
            <li><span>...</span></li>
        <?php endif ?>
    <?php endforeach ?>
</ul>