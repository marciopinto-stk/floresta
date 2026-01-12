<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ExternalTicketRaw extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tickets_raw';

    protected $guarded = [];
}
