<?php

namespace App\Http\Controllers;

use App\Employee;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;


class Employees extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($id = null)
    {
        if ($id == null) {
            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => Employee::orderBy('id', 'asc')->get()
            );

            return response($response, 200);
        } else {

            return  $this->show($id);

        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'name' => 'required|unique:employees',
            'email' => 'required|unique:employees',
            'contact_number' => 'required',
            'position' => 'required'
        );
        $validator = Validator::make(Input::all(),$rules);
        $response = array(
          'message' => 'Failed',
          'status' => 400,
          'errors' => $validator->errors()
        );
        if($validator->fails()){
            return response($response, 400);
        }
        else {
            $employee = new Employee;
            $employee->name = $request->input('name');
            $employee->email = $request->input('email');
            $employee->contact_number = $request->input('contact_number');
            $employee->position = $request->input('position');
            $employee->save();
            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $employee
            );
            return response($response, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $response = array(
            'message' => 'Success',
            'status' => 200,
            'data' => Employee::find($id)
        );
        return response($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);
        if ($employee) {
            $rules = array(
                'name' => 'required|unique:employees,name,'.$id,
                'email' => 'required|unique:employees,email,'.$id,
                'contact_number' => 'required',
                'position' => 'required'
            );
            $validator = Validator::make(Input::all(),$rules);
            $response = array(
                'message' => 'Failed',
                'status' => 400,
                'errors' => $validator->errors()
            );
            if($validator->fails()){
                return response($response, 400);
            }
            else {
                $employee->name = $request->input('name');
                $employee->email = $request->input('email');
                $employee->contact_number = $request->input('contact_number');
                $employee->position = $request->input('position');
                $employee->save();
                $response = array(
                    'message' => 'Success',
                    'status' => 200,
                    'data' => $employee
                );
                return response($response, 200);
            }
        } else {
            $response = array(
                'message' => 'Failed',
                'status' => 400
            );
            return response($response, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $employee = Employee::find($id);
        if ($employee) {
            $employee->delete();
            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => Employee::orderBy('id', 'asc')->get()
            );
            return response($response, 200);
        } else {
            $response = array(
                'message' => 'Failed',
                'status' => 400
            );
            return response($response, 400);
        }
    }
}
