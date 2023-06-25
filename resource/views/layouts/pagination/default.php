<ul class="pagination">
    <?php for ($x = 1; $x <= $max_page_count; $x++) : ?>
        <li class="<?= $x == $current_page ? 'active' : null ?>">
            <a href="<?= str_replace("%7Bchange_page_$uniqueID%7D", $x, $url) ?>">
                <?= $x ?>
            </a>
        </li>
    <?php endfor ?>
</ul>