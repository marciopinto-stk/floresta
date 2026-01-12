<?php

namespace App\Modules\Sensrit\Infrastructure\Persistence\Mongo\Models;

use MongoDB\Laravel\Eloquent\Model;

class MongoTicketRaw extends Model
{
    protected $connection = 'mongodb';
    protected $table      = 'sensrit_tickets_raw';

    protected $primaryKey = 'ticket_id';

    protected $guarded = [];

    // Importante: usamos o _id como o id_tickets (int)
    public $incrementing    = false;
    protected $keyType      = 'int';

    protected $casts = [
        'ticket_id' => 'integer',
    ];
}
