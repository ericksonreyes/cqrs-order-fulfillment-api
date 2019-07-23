<?php

namespace App\Models\Command;

use Illuminate\Database\Eloquent\Model;

/**
 * @property false|string event_meta_data
 * @property false|string event_data
 * @property string entity_id
 * @property string entity_type
 * @property string context_name
 * @property int happened_on
 * @property string event_name
 * @property string event_id
 * @property string event_hash
 */
class EventModel extends Model
{

    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var string
     */
    protected $table = 'events';

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * Prevent delete
         */
        self::deleting(function () {
            return false;
        });

        /**
         * Prevent update
         */
        self::updating(function () {
            return false;
        });
    }
}
