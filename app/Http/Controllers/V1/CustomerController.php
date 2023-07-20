<?php declare(strict_types=1);

namespace App\Http\Controllers\V1;

use stdClass;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\ParameterException;
use App\Exceptions\FailedDataException;
use App\Exceptions\InvalidRuleException;
use App\Repositories\CustomerRepository;
use App\Exceptions\DataNotFoundException;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function __construct(CustomerRepository $customerRepository)
    {
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
        $this->customerRepository = $customerRepository;
        $this->output = new stdClass();
        $this->output->responseCode = '';
        $this->output->responseDesc = '';
    }

    public function inquirycustomer(Request $request)
    {
        $filter = [];

        if (empty($request->all())) {
            throw new ParameterException('Parameter tidak boleh kosong');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string',
        ]);

        if ($validator->fails()) {
            $error_massage = $validator->errors()->first();
            throw new ParameterException($error_massage);
        }

        $filter['name'] = $request->name;
        $result= $this->customerRepository->getCustomer($filter);

        if(count($result) < 1){
            throw new DataNotFoundException('Data Not Found');
        }


        $this->output->responseCode = '00';
        $this->output->responseDesc = 'Success Inquiry Customer';
        $this->output->responseData = $result;
        return response()->json($this->output);
    }

    public function createcustomer(Request $request){

        if (empty($request->all())) {
            throw new ParameterException('Parameter tidak boleh kosong');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            $error_massage = $validator->errors()->first();
            throw new ParameterException($error_massage);
        }

        $result= $this->customerRepository->createCustomer($request->name);
        if($result == false){
            throw new FailedDataException('Failed to insert data');
        }

        $this->output->responseCode = '00';
        $this->output->responseMessage = 'Success';
        $this->output->responseDesc = 'Success Insert Data';
        return response()->json($this->output);

    }

    public function updatecustomer(Request $request){

        if (empty($request->all())) {
            throw new ParameterException('Parameter tidak boleh kosong');
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            $error_massage = $validator->errors()->first();
            throw new ParameterException($error_massage);
        }

        $request = [
            "id" => $request->id,
            "name" => $request->name
        ];

        $result= $this->customerRepository->updateCustomer($request);
        if($result != 1){
            throw new FailedDataException('Failed to update data');
        }

        $this->output->responseCode = '00';
        $this->output->responseMessage = 'Success';
        $this->output->responseDesc = 'Success Update Data';
        return response()->json($this->output);

    }


    public function deletecustomer(Request $request){

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

        $result= $this->customerRepository->deleteCustomer($request->id);
        if($result != 1){
            throw new FailedDataException('Failed to delete data');
        }

        $this->output->responseCode = '00';
        $this->output->responseMessage = 'Success';
        $this->output->responseDesc = 'Success Delete Data';
        return response()->json($this->output);

    }

}
