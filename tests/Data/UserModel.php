<?php


namespace Shader2k\SearchIndexer\Tests\Data;


use Shader2k\SearchIndexer\Indexable\IndexableContract;

class UserModel implements IndexableContract
{

    public $id;
    public $name;
    public $email;

    /**
     * @inheritDoc
     */
    public static function getIdentifierField(): string
    {
        return 'id';
    }

    /**
     * @inheritDoc
     */
    public static function getIndexableFields(): array
    {
        return ['name', 'email'];
    }

    /**
     * @inheritDoc
     */
    public static function getSearchDriverName(): ?string
    {
        return 'fakeDriver';
    }

    /**
     * @inheritDoc
     */
    public static function getProviderName(): ?string
    {
        return 'fakeProvider';
    }

    /**
     * @inheritDoc
     */
    public function getIndexName(): string
    {
        return 'fakeIndexName';
    }

    /**
     * @inheritDoc
     */
    public function getIdentifierValue(): string
    {
        return (string)$this->id;
    }
}
