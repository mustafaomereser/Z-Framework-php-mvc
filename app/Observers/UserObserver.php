<?php

namespace App\Observers;

use zFramework\Core\Abstracts\Observer;

class UserObserver extends Observer
{
    public function oninsert()
    {
        // Insert before run that
        echo "inserting";
    }

    public function oninserted(array $args)
    {
        // Insert after run that
        echo "inserted: " . $args['id'];
    }

    public function onupdate(array $args)
    {
        // Update before run that

        echo "updating:";
        var_dump($args['where']);
        var_dump($args['sets']);
    }

    public function onupdated(array $args)
    {
        // Update after run that
        echo "updated:";
        var_dump($args);
    }

    public function ondelete(array $args)
    {
        // Delete before run that
        echo "deleting:";
        var_dump($args);
    }

    public function ondeleted(array $args)
    {
        // Delete after run that
        echo "deleted:";
        var_dump($args);
    }
}
