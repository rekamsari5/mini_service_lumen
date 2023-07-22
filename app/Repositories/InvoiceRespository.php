<?php declare(strict_types=1);

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
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
            'total_item' => $request['total_item'],
            'status' => $request['status'],
            'subTotal' => $request['subTotal'],
            'taxAmount' => $request['taxAmount'],
            'grandTotal' => $request['grandTotal'],
        ];

        $query = DB::table('invoices')->insert($insertData);

        $queryIdNew = DB::table('invoices')->orderByDesc('id')->first();

        return $queryIdNew->{'id'};
    }


    public function insertUserItem($invoiceId, $req_item)
    {
        $queryResults = [];
        foreach($req_item as $v){
            $insertData = [
                'invoiceId' => $invoiceId,
                'item_name' => $v['item_name'],
                'type' => $v['type'],
                'qty' => $v['qty'],
                'unit_price' => $v['unit_price'],
                'amount' =>  $v['amount']
            ];
            $queryResults[] =  $insertData;

        }

        $query = DB::table('tbl_user_items')->insert( $queryResults );
        return $query;
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

    public function getInvoiceDetail($invoiceId) {
        $query = DB::table('invoices AS i')
                    ->select('i.*','i.id as invoiceId', 'b.name AS customer_name', 'b.address')
                    ->leftJoin('tbl_customer AS b','b.id','=','i.customer_id')
                    ->where("i.id", $invoiceId)
                    ->get()->toArray();
        $result = Invoice::hydrate($query);

        return $result ;
    }

    public function getUserItems($invoiceId) {
        $query = DB::table('tbl_user_items')
                    ->select('*')
                    ->where("invoiceId", $invoiceId);

        $list = Invoice::hydrate($query->get()->toArray());

        return $list;
    }


    public function updateInvoice($request) {

        $dt = [
            'subject' => $request['subject'],
            'issue_date' => $request['issue_date'],
            'due_date' => $request['due_date'],
            'customer_id' => $request['customer_id'],
            'total_item' => $request['total_item'],
            'subTotal' => $request['subTotal'],
            'taxAmount' => $request['taxAmount'],
            'grandTotal' => $request['grandTotal'],
            'updated_at' => Carbon::now()->format("Y-m-d H:i:s")
        ];

        $query = DB::table('invoices')
                ->where('id', $request['id'])
                ->update($dt);
        return $query;

    }


    public function deleteLastItems($deleteId, $invoiceId)
    {
        $query = DB::table('tbl_user_items')
                ->whereIn('id', $deleteId)
                ->where('invoiceId', $invoiceId)
                ->delete();
        return $query;
    }






}
