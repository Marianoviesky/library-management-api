<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Borrowing extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'borrowing.service';
    }
}
