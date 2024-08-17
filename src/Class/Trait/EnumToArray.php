<?php

namespace App\Class\Trait;
    
    
trait EnumToArray
{
  
  /**
   * for basic enums: names
   *
   * @return array
   */
  public static function names(): array
  {
    return array_column(self::cases(), 'name');
  }
  
  /**
   * For backed enums where you want the: values
   *
   * @return array
   */
  public static function values(): array
  {
    return array_column(self::cases(), 'value');
  }

  public static function array(): array
  {
    return array_combine(self::values(), self::names());
  }

}