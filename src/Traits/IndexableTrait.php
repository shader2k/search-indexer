<?php


namespace Shader2k\SearchIndexer\Traits;


trait IndexableTrait
{
    public static function getSearchDriverName(): ?string
    {
        return null;
    }

    public static function getProviderName(): ?string
    {
        return null;
    }

    public static function getIndexParameters(): ?array
    {
        return null;
    }


}
