<?php
/**
 * Created by PhpStorm.
 * User: Lutfor Rahman
 * Email: contact.lutforrahman@gmail.com
 * Web: www.lutforrahman.com
 * GitHub: https://github.com/contactlutforrahman
 * Packagist: https://packagist.org/users/lutforrahman/
 * Date: 7/16/2016
 * Time: 1:33 PM
 */

namespace Lutforrahman\iLaCart;

use Illuminate\Support\Collection;

class CartRowOptionsCollection extends Collection
{
    public function __construct($items)
    {
        parent::__construct($items);
    }

    public function __get($arg)
    {
        if($this->has($arg))
        {
            return $this->get($arg);
        }

        return NULL;
    }

    public function search($search, $strict = false)
    {
        foreach($search as $key => $value)
        {
            $found = ($this->{$key} === $value) ? true : false;

            if( ! $found) return false;
        }

        return $found;
    }

}