<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Collections\CustomerCollection;

class Customer extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];
    protected $casts = [
        'name' => 'string',
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new CustomerCollection($models);
    }
}
