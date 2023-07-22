<?php

namespace App\Http\Controllers\V1;

use stdClass;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Exceptions\ParameterException;
use App\Exceptions\FailedDataException;
use App\Exceptions\InvalidRuleException;
use App\Repositories\InvoiceRespository;
use App\Exceptions\DataNotFoundException;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function __construct(InvoiceRespository $invoiceRepository)
    {
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
        $this->invoiceRepository = $invoiceRepository;
        $this->output = new stdClass();
        $this->output->responseCode = '';
        $this->output->responseDesc = '';
    }

    public function createinvoice(Request $request)
    {
        if (empty($request->all())) {
            throw new ParameterException('Parameter tidak boleh kosong');
        }

        $validator = Validator::make($request->all(), [
            'subject' => 'required|string',
            'issue_date' => 'required|date_format:Y-m-d',
            'due_date' => 'required|date_format:Y-m-d',
            'customer_id' => 'required|numeric',
            'list_item' => 'required|array',
            'list_item.*.item_name' => 'required|string',
            'list_item.*.type' => 'required|string',
            'list_item.*.qty' => 'required|numeric',
            'list_item.*.unit_price' => 'required|numeric',
            'list_item.*.amount' => 'required|numeric',

        ]);

        if ($validator->fails()) {
            $error_massage = $validator->errors()->first();
            throw new ParameterException($error_massage);
        }

        $total_item = count($request->list_item);
        $dueDate = $request->due_date;
        $status = $this->calculateInvoiceStatus($dueDate);

        $subTotal = $this->calculateTotal($request->list_item);

        $calculateSummary = $this->calculateSubtotal($subTotal);

        $list_invoice = [
            "subject" => $request->subject,
            "issue_date" => $request->issue_date,
            "due_date" =>  $dueDate,
            "customer_id" => $request->customer_id,
            "total_item" => $total_item,
            "status" => $status,
            "subTotal" => $subTotal,
            "taxAmount" => $calculateSummary['taxAmount'],
            "grandTotal" => $calculateSummary['grandTotal'],
        ];

        DB::beginTransaction();
        try {
            $insertInvoice = $this->invoiceRepository->createInvoice($list_invoice);
            $insertUserItems = $this->invoiceRepository->insertUserItem($insertInvoice,$request->list_item);

            DB::commit();

            $this->output->responseCode = '00';
            $this->output->responseMessage = 'Success';
            $this->output->responseDesc = 'Success Insert Data';
            return response()->json($this->output);
        } catch (\Thowable $e) {
            DB::rollback();
            $error_msg = $e->getMessage();
            throw new DataNotFoundException($error_msg);
        }
    }


    public function inquiryinvoice(Request $request)
    {

        $filter = [
            'invoice_id'    => $request->invoice_id,
            'issue_date' => $request->issue_date,
            'subject'    => $request->subject,
            'total_item' =>$request->total_item,
            'name' => $request->customer,
            'due_date' => $request->due_date,
            'status' => $request->status,
        ];


        $list_invoice = $this->invoiceRepository->getInvoice($filter);

        if(count($list_invoice) < 1){
            throw new DataNotFoundException('Data Not Found');
        }

        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $list_invoice->forPage($currentPage, $perPage),
            $list_invoice->count(),
            $perPage,
            $currentPage
        );

        $result = [];
        $index = ($currentPage - 1) * $perPage + 1;
        foreach ($paginator as $val) {
            $list['no'] = $index++;
            $list['invoice_id'] = $val->id;
            $list['subject'] = $val->subject;
            $list['issue_date'] = $val->issue_date;
            $list['due_date'] = $val->due_date;
            $list['customer_name'] = $val->customer_name;
            $list['total_item'] = $val->total_item;
            $list['status'] = $val->status;
            $list['createdDate'] = $val->createdDate;
            $list['updateDate'] = $val->updated_at == null ? null : $val->updated_at;
            $result[] = $list;
        }

        $this->output->responseCode = '00';
        $this->output->responseDesc = 'Success Inquiry Customer';
        $this->output->responseData = $result;
        $this->output->pagination = [
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ];

        return response()->json($this->output);

    }

    public function inquirydetail(Request $request, $invoiceId = null)
    {
        if(empty($invoiceId)) {
            throw new ParameterException("Invoice Id Is Required");
        }

        $list_invoice = $this->invoiceRepository->getInvoiceDetail($invoiceId);
        if(count($list_invoice) < 1){
            throw new DataNotFoundException('Data Not Found');
        }



        $getItems = $this->invoiceRepository->getUserItems($invoiceId);

        $result = [];
        foreach($list_invoice as $val){
            $list['invoiceId'] = $val->invoiceId;
            $list['subject'] =  $val->subject;
            $list['issue_date'] =  $val->issue_date;
            $list['due_date'] =  $val->due_date;
            $list['customer_id'] =  $val->customer_id;
            $list['customer_name'] =  $val->customer_name;
            $list['address'] =  $val->address;
            $list['total_item'] =  $val->total_item;
            $list['list_items'] = $getItems;
            $list['subTotal'] =  $val->subTotal;
            $list['taxAmount'] =  $val->taxAmount;
            $list['grandTotal'] =  $val->grandTotal;
            $list['status'] =  $val->status;
            $list['createdDate'] = $val->createdDate;
            $list['updateDate'] = $val->updated_at == null ? null : $val->updated_at;
            $result[] = $list;
        }

        $this->output->responseCode = '00';
        $this->output->responseDesc = 'Success Inquiry Customer';
        $this->output->responseData = $result;
        return response()->json($this->output);

    }


    public function updateinvoice(Request $request)
    {
        if (empty($request->all())) {
            throw new ParameterException('Parameter tidak boleh kosong');
        }

        $validator = Validator::make($request->all(), [
            'invoiceId' => 'required|string',
            'subject' => 'required|string',
            'issue_date' => 'required|date_format:Y-m-d',
            'due_date' => 'required|date_format:Y-m-d',
            'customer_id' => 'required|numeric',
            'list_items' => 'required|array',
            'list_items.*.id' => 'required|numeric',
            'list_items.*.invoiceId' => 'required|string',
            'list_items.*.item_name' => 'required|string',
            'list_items.*.type' => 'required|string',
            'list_items.*.qty' => 'required|numeric',
            'list_items.*.unit_price' => 'required|numeric',
            'list_items.*.amount' => 'required|numeric',
            'new_list_items' => 'nullable|array',
            'new_list_items.*.item_name' => 'nullable|string',
            'new_list_items.*.type' => 'nullable|string',
            'new_list_items.*.qty' => 'nullable|numeric',
            'new_list_items.*.unit_price' => 'nullable|numeric',
            'new_list_items.*.amount' => 'nullable|numeric',

        ]);

        if ($validator->fails()) {
            $error_massage = $validator->errors()->first();
            throw new ParameterException($error_massage);
        }

        $total_item = count($request->list_items);

        $subTotal = $this->calculateTotal($request->list_items);


        $total_new_item = count($request->new_list_items);
        if($total_new_item != 0){
            $subTotal2 = $this->calculateTotal($request->new_list_items);
        }else{
            $subTotal2 = 0;
        }

        $calculateSubtotal =  $subTotal + $subTotal2;

        $dueDate = $request->due_date;

        $calculateSummary = $this->calculateSubtotal($calculateSubtotal);

        $list_invoice = [
            "id" => $request->invoiceId,
            "subject" => $request->subject,
            "issue_date" => $request->issue_date,
            "due_date" =>  $dueDate,
            "customer_id" => $request->customer_id,
            "total_item" => $total_new_item + $total_item,
            "subTotal" => $calculateSubtotal ,
            "taxAmount" => $calculateSummary['taxAmount'],
            "grandTotal" => $calculateSummary['grandTotal'],
        ];

        DB::beginTransaction();
        try {
            $updateInvoice = $this->invoiceRepository->updateInvoice($list_invoice); //update invoice

            $listRequestIds = array_column($request->list_items, 'id');

            $getLastItem = $this->invoiceRepository->getUserItems($request->invoiceId);
            $deleteId = [];
            foreach ($getLastItem as $data) {
                if (!in_array($data['id'], $listRequestIds)) {
                    $listId = [
                        'id' => $data['id']
                    ];
                    $deleteId[] = $listId;
                }
            }

            if(count($deleteId) != 0) {
                $deleteLastItem = $this->invoiceRepository->deleteLastItems($deleteId, $request->invoiceId);
            }

            if($total_new_item != 0) {
                $insertNewItem = $this->invoiceRepository->insertUserItem($request->invoiceId,$request->new_list_items);
            }

            DB::commit();

            $this->output->responseCode = '00';
            $this->output->responseMessage = 'Success';
            $this->output->responseDesc = 'Success Insert Data';
            return response()->json($this->output);
        } catch (\Thowable $e) {
            DB::rollback();
            $error_msg = $e->getMessage();
            throw new DataNotFoundException($error_msg);
        }
    }


    function calculateInvoiceStatus($dueDate)
    {
        $now = time();
        $dueTimestamp = strtotime($dueDate);

        if ($now > $dueTimestamp) {
            return 'unpaid';
        } else {
            return 'paid';
        }
    }

    function calculateTotal($request)
    {
        $subTotal= array_reduce($request, function ($carry, $item) {
            return $carry + $item['amount'];
        }, 0);

        return $subTotal;
    }

    function calculateSubtotal($subTotal)
    {
        $taxAmount = $subTotal * (10/100); // 10 persen
        $grandTotal =  $subTotal + $taxAmount ;

        $list = [
            "taxAmount" =>  round($taxAmount,2),
            "grandTotal" => round($grandTotal,2)
        ];

        return $list;

    }



}
