<?php

namespace App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Models;

use MongoDB\Laravel\Eloquent\Model;

class MongoSyncState extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'sensrit_sync_state';

    protected $guarded = [];

    public $incrementing    = false;
    protected $keyType      = 'string';
}
