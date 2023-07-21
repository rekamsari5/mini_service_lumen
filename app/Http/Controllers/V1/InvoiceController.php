<?php

namespace App\Http\Controllers\V1;

use stdClass;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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

        $total_item = count($request->list_item);
        $dueDate = $request->due_date;
        $status = $this->calculateInvoiceStatus($dueDate);

        $calculateSummary = $this->calculateSubtotal($request->list_item);

        $list_invoice = [
            "subject" => $request->subject,
            "issue_date" => $request->issue_date,
            "due_date" =>  $dueDate,
            "customer_id" => $request->customer_id,
            "address" => $request->address,
            "total_item" => $total_item,
            "status" => $status,
            "subTotal" => $calculateSummary['subTotal'],
            "taxAmount" => $calculateSummary['taxAmount'],
            "grandTotal" => $calculateSummary['grandTotal'],
        ];

        $insertInvoice = $this->invoiceRepository->createInvoice($list_invoice);

        $insertUserItems = $this->invoiceRepository->insertUserItem($request->customer_id,$request->list_item);

        $this->output->responseCode = '00';
        $this->output->responseMessage = 'Success';
        $this->output->responseDesc = 'Success Insert Data';
        return response()->json($this->output);
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

        $result = [];
        foreach($list_invoice as $val){
            $list['invoice_id'] = $val->id;
            $list['subject'] =  $val->subject;
            $list['issue_date'] =  $val->issue_date;
            $list['due_date'] =  $val->due_date;
            $list['customer_id'] =  $val->customer_id;
            $list['customer_name'] =  $val->customer_name;
            $list['address'] =  $val->address;
            $list['total_item'] =  $val->total_item;
            $list['list_items'] = $this->invoiceRepository->getUserItems($val->customer_id);
            $list['subTotal'] =  $val->subTotal;
            $list['taxAmount'] =  $val->taxAmount;
            $list['grandTotal'] =  $val->grandTotal;
            $list['status'] =  $val->status;
            $result[] = $list;
        }

        $this->output->responseCode = '00';
        $this->output->responseDesc = 'Success Inquiry Customer';
        $this->output->responseData = $result;
        return response()->json($this->output);

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

    function calculateSubtotal($request)
    {
        $total = 0;
        foreach($request as $v){
            $amount = str_replace(',', '', $v['amount']);
            $total += (float) $amount ;
        }

        $taxAmount = $total * (10/100); // 10 persen
        $grandTotal =  $total + $taxAmount ;

        $list = [
            "subTotal" => $total,
            "taxAmount" =>  $taxAmount,
            "grandTotal" => $grandTotal
        ];

        return $list;

    }



}
