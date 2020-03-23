<?php

namespace App\Http\Controllers\API;

use App\Device;
use App\Exposure;
use App\HealthCareWorker;
use App\Http\Resources\GenericCollection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DevicesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function all_devices()
    {
        return new GenericCollection(Device::all());
    }

    public function facility_devices($id)
    {
        return new GenericCollection(Device::where('facility_id', $id)->get());
    }

    public function create_device(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|numeric|exists:facilities,id',
            'name' => 'required',
            'safety_designed' => 'required',

        ],[
            'facility_id.required' => 'Please select the facility'
        ]);


        $device = new Device();
        $device->created_by = \auth()->user()->id;
        $device->updated_by = \auth()->user()->id;
        $device->facility_id = $request->facility_id;
        $device->name = $request->name;
        $device->safety_designed = $request->safety_designed;

        $device->saveOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Device has been created successfully'
        ], 201);


    }


}
