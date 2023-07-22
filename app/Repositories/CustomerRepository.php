<?php declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Collections\CustomerCollection;

class CustomerRepository
{
    public function getCustomer($filter)
    {

        $query = DB::table('tbl_customer')
            ->select('*')
            ->where(function($sub_query) use ($filter) {
                if (isset($filter['name'])) {
                    $sub_query->where('name', 'like', '%' . $filter['name'] . '%');
                }
            })
            ->get();

        return $query;
    }

    public function createCustomer($request) {
        $query = DB::table('tbl_customer')
                ->insert([
                    'name' => $request['name'],
                    'address' => $request['address']

                ]);
        return $query;
    }

    public function updateCustomer($request) {
        $query = DB::table('tbl_customer')
                ->where('id', $request['id'])
                ->update([
                    'name' => $request['name'],
                    'address' => $request['address']
                ]);

        return $query;
    }

    public function deleteCustomer($request) {
        $query = DB::table('tbl_customer')
                 ->where("id", $request)
                 ->delete();

         return $query;
    }

}
