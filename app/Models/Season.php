<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Season extends Model
{
    use HasUuids, HasFactory;

    public $timestamps = false;

    protected $table = 'season';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'subscription');
    }

    public function league(): HasOne
    {
        return $this->hasOne(League::class, 'id', 'league_id');
    }
}
