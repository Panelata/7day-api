<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    // note groups is a keyword in sql so table is called proj_groups
    protected $table = 'pokemon';
    protected $primaryKey = 'pokemonID';
    protected $connection = '7Day';
    public $timestamps = false;

    protected $filalble = [
        
    ];


}
