<?php declare(strict_types=1);

namespace App\Http\Controllers\V1;

use stdClass;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ItemsRepository;
use App\Exceptions\ParameterException;
use App\Exceptions\FailedDataException;
use App\Exceptions\InvalidRuleException;
use App\Exceptions\DataNotFoundException;
use Illuminate\Support\Facades\Validator;

class ItemsController extends Controller
{
    public function __construct(ItemsRepository $itemsRepository)
    {
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
        $this->itemsRepository = $itemsRepository;
        $this->output = new stdClass();
        $this->output->responseCode = '';
        $this->output->responseMessage = '';
        $this->output->responseDesc = '';
    }

    public function inquiryItems(Request $request)
    {
        $filter = [];

        if (empty($request->all())) {
            throw new ParameterException('Parameter tidak boleh kosong');
        }

        $validator = Validator::make($request->all(), [
            'item_name' => 'string',
            'type' => 'string'
        ]);

        if ($validator->fails()) {
            $error_massage = $validator->errors()->first();
            throw new ParameterException($error_massage);
        }

        $filter['item_name'] = $request->item_name;
        $filter['type'] = $request->type;

        $result= $this->itemsRepository->getItems($filter);

        if(count($result) < 1){
            throw new DataNotFoundException('Data Not Found');
        }

        $list =[];
        foreach($result as $dt) {
            $amount =  round($dt->qty * $dt->unit_price, 2);
            $item['item_name'] = $dt->item_name;
            $item['type'] = $dt->type;
            $item['qty'] = $dt->qty;
            $item['unit_price'] = $dt->unit_price;
            $item['amount'] =  $amount;
            $list[] = $item;
        };



        $this->output->responseCode = '00';
        $this->output->responseDesc = 'Success Inquiry Items';
        $this->output->responseData = $list;
        return response()->json($this->output);
    }

    public function createItems(Request $request){

        if (empty($request->all())) {
            throw new ParameterException('Parameter tidak boleh kosong');
        }

        $validator = Validator::make($request->all(), [
            'item_name' => 'required|string',
            'type' => 'required|string',
            'qty' => 'required|numeric',
            'unit_price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $error_massage = $validator->errors()->first();
            throw new ParameterException($error_massage);
        }

        $request = [
            'item_name' => $request->item_name,
            'type' => $request->type,
            'qty' => $request->qty,
            'unit_price' => $request->unit_price,
        ];

        $result= $this->itemsRepository->createItems($request);
        if($result == false){
            throw new FailedDataException('Failed to insert data');
        }

        $this->output->responseCode = '00';
        $this->output->responseMessage = 'Success';
        $this->output->responseDesc = 'Success Insert Data';
        return response()->json($this->output);

    }

    public function updateItems(Request $request){

        if (empty($request->all())) {
            throw new ParameterException('Parameter tidak boleh kosong');
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'item_name' => 'required|string',
            'type' => 'required|string',
            'qty' => 'required|numeric',
            'unit_price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $error_massage = $validator->errors()->first();
            throw new ParameterException($error_massage);
        }

        $request = [
            "id" => $request->id,
            'item_name' => $request->item_name,
            'type' => $request->type,
            'qty' => $request->qty,
            'unit_price' => $request->unit_price,
        ];

        $result = $this->itemsRepository->updateItems($request);

        if($result != 1){
            throw new FailedDataException('Failed to update data');
        }

        $this->output->responseCode = '00';
        $this->output->responseMessage = 'Success';
        $this->output->responseDesc = 'Success Update Data';
        return response()->json($this->output);

    }


    public function deleteItems(Request $request){

        if (empty($request->all())) {
            throw new ParameterException('Parameter tidak boleh kosong');
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $error_massage = $validator->errors()->first();
            throw new ParameterException($error_massage);
        }

        $result= $this->itemsRepository->deleteItems($request->id);
        if($result != 1){
            throw new FailedDataException('Failed to delete data');
        }

        $this->output->responseCode = '00';
        $this->output->responseMessage = 'Success';
        $this->output->responseDesc = 'Success Delete Data';
        return response()->json($this->output);

    }

}
