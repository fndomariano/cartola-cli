<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $table = 'league';

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
}