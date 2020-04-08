<?php


namespace Shader2k\SearchIndexer\Helpers;


use Exception;
use RuntimeException;

class Config extends Singleton
{
    private $configs = [];

    public function load($values, $parser = null, $string = false): void
    {
        if (is_array($values)) {
            foreach ($values as $value) {
                $this->setConfig($value, $parser, $string);
            }
        } else {
            $this->setConfig($values, $parser, $string);
        }
    }

    /**
     * @param $values
     * @param $parser
     * @param $string
     */
    protected function setConfig($values, $parser, $string): void
    {
        $name = pathinfo($values, PATHINFO_FILENAME);
        $this->configs[$name] = \Noodlehaus\Config::load($values, $parser = null, $string = false);
    }

    /**
     * @param string $key
     * @return \Noodlehaus\Config
     * @throws Exception
     */
    public function getConfig(string $key): \Noodlehaus\Config
    {
        if ($this->configs[$key] !== null) {
            return $this->configs[$key];
        }

        throw new RuntimeException('Config not load');
    }

}
