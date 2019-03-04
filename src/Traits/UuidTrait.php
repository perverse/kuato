<?php

namespace Kuato\Traits;

trait UuidTrait 
{
    /**
    *
    * @return string
    */
    public static function generateUuid() 
    {
        do
        {
            $uuid = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', 
                mt_rand(0, 0xffff), 
                mt_rand(0, 0xffff), 
                mt_rand(0, 0xffff), 
                mt_rand(0, 0x0fff) | 0x4000, 
                mt_rand(0, 0x3fff) | 0x8000, 
                mt_rand(0, 0xffff), 
                mt_rand(0, 0xffff), 
                mt_rand(0, 0xffff) 
            );
            $entity = self::where('uuid', $uuid)->get();
        } while( ! $entity->isEmpty() );

        return $uuid;
    }
}
