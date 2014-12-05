<?php

namespace ExactTarget;

class ET_AssocArrayUtils
{
    public static function isAssoc($array)
    {
        return ($array !== array_values($array));
    }
}
