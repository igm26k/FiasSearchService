<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class FiasObject
 *
 * @package App
 */
class FiasObject extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'fias_object';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return HasMany
     */
    public function houses()
    {
        return $this->hasMany('App\FiasHouse', 'aoguid', 'aoguid');
    }
}
