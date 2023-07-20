<?php declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Items;
use App\Collections\ItemsCollection;

class ItemsRepository
{
    public function getItems($filter)
    {

        $query = DB::table('tbl_items')
            ->where(function($sub_query) use ($filter) {
                    $sub_query->where('item_name', 'like', '%' . $filter['item_name'] . '%')
                              ->where('type', 'like', '%' . $filter['type'] . '%');
            });
        $list = Items::hydrate($query->get()->toArray());

        return $list;
    }

    public function createItems($request) {
        $query = DB::table('tbl_items')
                ->insert([
                    'item_name' => $request['item_name'],
                    'type' => $request['type'],
                    'qty' => $request['qty'],
                    'unit_price' => $request['unit_price'],
                ]);

        return $query;
    }

    public function updateItems($request) {
       $query = DB::table('tbl_items')
                ->where("id", $request['id'])
                ->update([
                    'item_name' => $request['item_name'],
                    'type' => $request['type'],
                    'qty' => $request['qty'],
                    'unit_price' => $request['unit_price'],
                ]);


       return $query;

    }

    public function deleteItems($request) {
        $query = DB::table('tbl_items')
                 ->where("id", $request)
                 ->delete();

        return $query;
    }

}
