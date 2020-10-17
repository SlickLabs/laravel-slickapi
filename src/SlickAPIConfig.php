<?php
/**
 * Created by SlickLabs - Wefabric.
 * User: nathanjansen <nathan@wefabric.nl>
 * Date: 26-03-18
 * Time: 12:43
 */

namespace SlickLabs\Laravel\SlickAPI;

use SlickLabs\Laravel\SlickAPI\Exceptions\InvalidConfiguration;

/**
 * Class SlickAPIConfig
 * @package SlickLabs\Laravel\SlickAPI
 * @version 1.0.0
 */
class SlickAPIConfig
{
    /**
     * All required config fields
     *
     * @var array
     */
    protected $required = ['name', 'url', 'token', 'cache_key'];

    /**
     * A list of config items, the items are equal to fields
     *
     * @var array
     */
    protected $items = [
        'verify' => true
    ];

    /**
     * SlickAPIConfig constructor.
     * @param array $config
     * @throws InvalidConfiguration
     */
    public function __construct(array $config)
    {
        $this->setConfig($config);
    }

    /**
     * @param array $config
     * @throws InvalidConfiguration
     */
    public function setConfig(array $config)
    {
        $this->guardAgainstInvalidConfig($config);

        foreach ($config as $key => $value) {
            switch ($key) {
                default:
                    $this->items[$key] = $value;
                    break;
            }
        }
    }

    /**
     * @param array $config
     * @throws InvalidConfiguration
     */
    public function guardAgainstInvalidConfig(array $config)
    {
        $missing = false;
        $missingAttributes = [];

        foreach ($this->required as $required) {
            if (empty($config[$required])) {
                $missing = true;
                $missingAttributes[] = $required;
            }
        }

        if ($missing) {
            throw InvalidConfiguration::apiConfigMissingRequired(implode(',', $missingAttributes));
        }
    }

    /**
     * Returns the set config value if the given key exists
     *
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return $this->items[$key] ?? $default;
    }

    /**
     * Manually sets the given config item
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->items[$key] = $value;
    }
}