<?php
/**
 * Created by SlickLabs - Wefabric.
 * User: nathanjansen <nathan@wefabric.nl>
 * Date: 26-03-18
 * Time: 12:07
 */

namespace SlickLabs\Laravel\SlickAPI\Exceptions;

use Exception;

class ClassCreationError extends Exception
{
    public static function APIDoesNotExist($name)
    {
        return new static('The requested API `' . $name . '` is not configured. Add it to your config/slickapi.php file');
    }
}