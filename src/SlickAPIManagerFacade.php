<?php
/**
 * Created by SlickLabs - Wefabric.
 * User: nathanjansen <nathan@wefabric.nl>
 * Date: 27-03-18
 * Time: 11:19
 */

namespace SlickLabs\Laravel\SlickAPI;

use Illuminate\Support\Facades\Facade;

class SlickAPIManagerFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'slickapi-manager';
    }
}