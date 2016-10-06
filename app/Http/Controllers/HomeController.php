<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Employee;
use Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'id' => 'required|numeric',
            'status' => 'required|numeric',
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('home');
    }

    public function getData() 
    {
        $employees = Employee::all()->toArray();
        return response()->json($employees);
    }    

    public function changeStatus(Request $request) 
    {
        $validator = $this->validator($request->all());
        if($validator->fails()) {
            $errors = $validator->errors();
            $m = [];
            foreach ($errors->all() as $key => $value) {
                $m[] = $value;
            }
            $m = implode("<br>", $m);
            return response()->json(['status' => false, 'message' => $m]);
        }
        $data = Employee::find($request->id);
        if($data->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Data not found']);
        }
        $data->status = $request->status;
        if($data->save()) return response()->json(['status' => true, 'message' => 'Success']);
        return response()->json(['status' => false, 'message' => 'Failed']);
    }
}
