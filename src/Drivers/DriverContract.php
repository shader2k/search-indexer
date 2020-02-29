<?php

namespace Shader2k\SearchIndexer\Drivers;

use Shader2k\SearchIndexer\Exceptions\DriverException;

interface DriverContract
{
    /**
     * Индексирование данных
     * @param array $rawData
     * @param object $model
     * @return bool
     * @throws \ReflectionException
     */
    public function indexingData(array $rawData): bool;

    /**
     * Подготовка индекса
     * @param object $model
     * @return bool
     * @throws DriverException
     * @throws \ReflectionException
     */
    public function prepareIndex(object $model): bool;

    /**
     * Завершающий шаг индексирования.
     * смена алиаса на чтение и удаление старого индекса
     * @return bool
     * @throws DriverException
     */
    public function deploymentIndex(): bool;

    /**
     * Установить модель
     * @param object $model
     * @throws \ReflectionException
     */
    public function setModel(object $model): void;

    /**
     * Получить модель
     * @return object
     */
    public function getModel(): object;
}
