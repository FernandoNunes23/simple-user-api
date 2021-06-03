<?php


namespace App\Infrastructure\Persistence;


class CachePersistence
{
    public function save($key, $object)
    {
        apcu_store((string) $key, $object);
    }

    public function get($key)
    {
        return apcu_fetch((string) $key);
    }

    public function clearAll()
    {
        apcu_clear_cache();
    }
}