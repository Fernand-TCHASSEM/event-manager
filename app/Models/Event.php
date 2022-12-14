<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{

    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to return events between two dates
     *
     * @param Builder  $query
     * @param string $fromDate
     * @param string $toDate
     * @return Builder
     */
    public function scopeDateRange(Builder $query, string $fromDate, string $toDate): Builder
    {
        return $query->whereBetween('start_date', [$fromDate, $toDate])
            ->orWhereBetween('end_date', [$fromDate, $toDate])
            ->orWhere([
                ['start_date', '<=', $fromDate],
                ['end_date', '>=', $toDate]
            ]);
    }

    /**
     * Scope a query to return events that contain the searched keywords
     *
     * @param Builder  $query
     * @param string $keywords
     *
     * @return Builder
     */
    public function scopeKeywords(Builder $query, string $keywords = null): Builder
    {
        if ($keywords) {
            $keywords = strtolower($keywords);

            $query->where(DB::raw('lower(title)'), 'like', '%' . $keywords . '%')
                ->orWhere(DB::raw('lower(description)'), 'like', '%' . $keywords . '%');
        }

        return $query;
    }
}
