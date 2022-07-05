<?php

namespace App\Observers;

use zFramework\Core\Abstracts\Observer;

class UserObserver extends Observer
{
    public function oninsert(array $args)
    {
        // when insert a row before run here
    }

    public function onupdate(array $args)
    {
        // when update a row before run here
    }

    public function ondelete(array $args)
    {
        // when delete a row before run here
    }
}
