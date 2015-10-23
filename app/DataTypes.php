<?php
namespace App;
class DataTypes implements Enum
{
    const TEXT            = 0;
    const NUMERIC         = 1;
    const DATE            = 2;

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

interface Enum {
	public static function getCode($label);
	public static function getLabel($code);
}
?>
