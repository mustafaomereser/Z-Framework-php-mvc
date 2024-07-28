<?php

namespace App\Observers;

use zFramework\Core\Abstracts\Observer;

#[\AllowDynamicProperties]
class TestObserver extends Observer
{
    /**
     * Insert before run that
     * @param array $sets
     * @return array
     */
    public function oninsert(array $sets): array
    {
        echo "test";
        exit;
    }

    /**
     * Insert after run that
     * @param array $args
     */
    public function oninserted(array $args)
    {
    }

    /**
     * Update before run that
     * @param array $sets
     * @return array
     */
    public function onupdate(array $sets): array
    {
        return [];
    }

    /**
     * Update after run that
     * @param array $args
     */
    public function onupdated(array $args)
    {
    }

    /**
     * Delete before run that
     * @param array $args
     */
    public function ondelete(array $args)
    {
    }

    /**
     * Delete after run that
     * @param array $args
     */
    public function ondeleted(array $args)
    {
    }
}
