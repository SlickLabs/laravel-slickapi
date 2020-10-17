<?php
/**
 * Created by SlickLabs - Wefabric.
 * User: nathanjansen <nathan@wefabric.nl>
 * Date: 26-03-18
 * Time: 12:00
 */

namespace SlickLabs\Laravel\SlickAPI;

use SlickLabs\Laravel\SlickAPI\Exceptions\InvalidConfiguration;

class SlickAPIFactory
{
    /**
     * @param array $config
     * @return SlickAPI
     * @throws InvalidConfiguration
     * @throws \Exception
     */
    public static function create(array $config): SlickAPI
    {
        return new SlickAPI(new SlickAPIConfig($config));
    }
}