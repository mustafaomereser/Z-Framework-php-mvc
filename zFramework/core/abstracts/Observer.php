<?php

namespace zFramework\Core\Abstracts;

abstract class Observer
{
    /**
     * Observer Router to methods
     * @param string $function
     * @param array $args
     * @return self|void
     */
    public function observer_router(string $function, array $args)
    {
        $call = null;
        switch ($function) {
            case 'delete':
                $call = 'ondelete';
                break;
        }

        if ($call) return $this->$call($args);
    }
}
