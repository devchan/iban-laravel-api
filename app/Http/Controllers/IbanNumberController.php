<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\IbanNumberRequest;
use App\Http\Resources\IbanNumber\IbanNumberCollection;
use App\Models\IbanNumber;
use App\Repositories\IbanNumber\IbanNumberRepository;

class IbanNumberController extends Controller
{

    public function __construct(IbanNumber $model)
    {
        $this->model = new IbanNumberRepository($model);
    }

    public function index()
    {
        return new IbanNumberCollection($this->model->get());
    }

    public function store(IbanNumberRequest $request)
    {

        try {
            $data = $request->validated();

            $data['user_id'] = $request->user()->id;

            if ($model = $this->model->create($data)) {

                return response()->json(['message' => "{$model->iban_number} is a valid IBAN number!."]);

            }

            return response()->json(['message' => 'Not Found!'], 404);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['message' => 'Error!'], 400);
        }
    }

}
