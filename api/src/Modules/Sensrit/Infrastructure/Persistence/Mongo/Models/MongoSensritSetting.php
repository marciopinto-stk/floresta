<?php

namespace App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Models;

use MongoDB\Laravel\Eloquent\Model;

class MongoSensritSetting extends Model
{
    protected $connection   = 'mongodb';
    protected $table        = 'sensrit_settings';
    protected $primaryKey   = '_id';
    public $incrementing    = false;
    protected $keyType      = 'string';
    protected $guarded      = [];
}
