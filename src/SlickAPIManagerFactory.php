<?php
/**
 * Created by SlickLabs - Wefabric.
 * User: nathanjansen <nathan@wefabric.nl>
 * Date: 26-03-18
 * Time: 13:11
 */

namespace SlickLabs\Laravel\SlickAPI;

class SlickAPIManagerFactory
{
    /**
     * @param array $config
     * @return SlickAPIManager
     */
    public static function create(array $config): SlickAPIManager
    {
        return new SlickAPIManager($config);
    }
}