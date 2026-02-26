<?php

namespace App\SharedKernel\Infrastructure\S2\Models;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;

abstract class BaseS2Model extends Model
{
    protected $connection       = 'S2';
    protected bool $readOnly    = true;
    public $timestamps          = false;

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if ($model->readOnly) {
                throw new RuntimeException('S2 model is read-only');
            }
        });

        static::deleting(function (self $model) {
            if ($model->readOnly) {
                throw new \RuntimeException('S2 model is read-only');
            }
        });
    }
}
