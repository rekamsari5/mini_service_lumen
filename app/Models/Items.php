<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Collections\ItemsCollection;

class Items extends Model
{
    public $timestamps = false;

    protected $fillable = ['item','type','qty','unit_price'];
    protected $casts = [
        'item' => 'string',
        'type' => 'string',
        'qty' => 'float',
        'unit_price' => 'float',
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {

        return new ItemsCollection($models);
    }
}
