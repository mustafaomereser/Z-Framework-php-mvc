<?php

namespace App\Observers;

use zFramework\Core\Abstracts\Observer;

class UserObserver extends Observer
{
    public function oninsert()
    {
        echo "inserting";
    }

    public function oninserted(array $args)
    {
        echo "inserted: " . $args['id'];
    }

    public function onupdate(array $args)
    {
        echo "updating: " . $args['id'];
    }

    public function onupdated(array $args)
    {
        echo "updated:";
        print_r($args);
    }

    public function ondelete(array $args)
    {
        echo "deleting: " . $args['id'];
    }

    public function ondeleted(array $args)
    {
        echo "deleted:";
        print_r($args);
    }
}
