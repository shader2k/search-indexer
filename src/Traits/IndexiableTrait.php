<?php


namespace Shader2k\SearchIndexer\Traits;


trait IndexiableTrait
{
    public function getSearchDriver(): ?string
    {
        return null;
    }

    public function getProvider(): ?string
    {
        return null;
    }


}
