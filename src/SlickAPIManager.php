<?php
/**
 * Created by SlickLabs - Wefabric.
 * User: nathanjansen <nathan@wefabric.nl>
 * Date: 26-03-18
 * Time: 13:07
 */

namespace SlickLabs\Laravel\SlickAPI;


use SlickLabs\Laravel\SlickAPI\Exceptions\ClassCreationError;
use SlickLabs\Laravel\SlickAPI\Exceptions\InvalidConfiguration;

class SlickAPIManager
{
    /**
     * @var array[]
     */
    protected $items;

    public function __construct(array $config)
    {
        $this->items = $config;
    }

    /**
     * @param $slug
     * @return SlickAPI
     * @throws InvalidConfiguration
     * @throws ClassCreationError
     * @throws \Exception
     */
    public function get($slug): SlickAPI
    {
        $config = $this->items[$slug] ?? null;
        
        if ($config) {
            return SlickAPIFactory::create($config);
        } else {
            throw ClassCreationError::APIDoesNotExist($slug);
        }
    }
}