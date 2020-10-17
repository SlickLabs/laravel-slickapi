<?php
/**
 * Created by SlickLabs - Wefabric.
 * User: nathanjansen <nathan@wefabric.nl>
 * Date: 26-03-18
 * Time: 12:07
 */

namespace SlickLabs\Laravel\SlickAPI\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function apisNotSpecified()
    {
        return new static('There was no api specified. You must provide a valid api to connect start communicating with your API\'s.');
    }

    public static function apiConfigMissingRequired(string $missing)
    {
        return new static('The api Config is not complete, missing attributes: `' . $missing . '`');
    }
}