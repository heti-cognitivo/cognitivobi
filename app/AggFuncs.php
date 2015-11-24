<?php
namespace App;
class AggFuncs implements Enum
{
    const SUM            = 1;
    const SUMWORUNNINGTOTAL         = 2;

    public static function getCode($label)
    {
        $class = new \ReflectionClass( get_class() );
        $constants = $class->getConstants();
        return $constants[$label];
    }

    public static function getLabel($code)
    {
        $class = new \ReflectionClass( get_class() );
        $constants = $class->getConstants();
        $constants = array_flip($constants);
        return $constants[$code];
    }
}

?>
