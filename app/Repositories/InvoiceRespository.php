<?php declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Collections\InvoiceCollection;

class InvoiceRespository
{

    public function createInvoice($request) {
        $lastRecord = DB::table('invoices')->orderByDesc('id')->first();

        if ($lastRecord == null) {
            $formattedID = '00001';
        }else{
            $lastID =  $lastRecord->{'id'};
            $nextNumber = $lastID + 1;
            $formattedID = str_pad("$nextNumber", strlen($lastID), '0', STR_PAD_LEFT);
        }

        $insertData = [
            'id' => $formattedID,
            'subject' => $request['subject'],
            'issue_date' => $request['issue_date'],
            'due_date' => $request['due_date'],
            'customer_id' => $request['customer_id'],
            'address' => $request['address'],
            'total_item' => $request['total_item'],
            'status' => $request['status'],
            'subTotal' => $request['subTotal'],
            'taxAmount' => $request['taxAmount'],
            'grandTotal' => $request['grandTotal'],
        ];

        $query = DB::table('invoices')->insert($insertData);

        return $query;
    }


    public function insertUserItem($customer, $req_item)
    {
        $queryResults = [];
        foreach($req_item as $v){
            $qty = (float) str_replace(',', '', $v['qty']);
            $unit_price = (float) str_replace(',', '', $v['unit_price']);
            $amount = (float) str_replace(',', '', $v['amount']);

            $insertData = [
                'customer_id' => $customer,
                'item_name' => $v['item_name'],
                'type' => $v['type'],
                'qty' => $qty,
                'unit_price' => $unit_price,
                'amount' =>  $amount
            ];

            $queryResults[] = DB::table('tbl_user_items')->insert( $insertData );
        }
        return $queryResults;
    }


    public function getInvoice( $filter) {
        $query = DB::table('invoices AS i')
                    ->select('i.*', 'b.name AS customer_name')
                    ->leftJoin('tbl_customer AS b','b.id','=','i.customer_id')
                    ->where(function($sub_query) use ($filter) {
                        foreach($filter as $key => $value){
                            if(!empty($value)){
                                if($key == 'name'){
                                    $sub_query->Where('b.name', 'LIKE','%'. $value.'%');
                                }elseif ($key == 'subject') {
                                    $sub_query->Where('i.subject', 'LIKE','%'. $value.'%');
                                }elseif ($key == 'invoice_id') {
                                    $sub_query->Where('i.id', $value);
                                }
                                else{
                                    $sub_query->Where($key, $value);
                                }
                            };
                        };
                    });
        $result = $query->orderBy('id','DESC')->get();

        return $result;
    }

    public function getUserItems($request) {
        $query = DB::table('tbl_user_items')
                    ->select('*')
                    ->where("customer_id", $request)
                    ->get();

        return $query;
    }






}
