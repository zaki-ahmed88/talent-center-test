<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\StudentInterface;
use App\Http\Interfaces\TeachersInterface;
use App\Http\Traits\ApiDesignTrait;
//use App\Models\role;
use App\Models\Role;
use App\Models\StudentGroup;
use App\Models\User;


use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class StudentRepository implements StudentInterface {

    use ApiDesignTrait;


    private $userModel;
    private $roleModel;
    private $studentGroupModel;



    public function __construct(User $user, Role $role, StudentGroup $studentGroup) {

        $this->userModel = $user;
        $this->roleModel = $role;
        $this->studentGroupModel = $studentGroup;
    }


    public function addStudent($request)
    {
        // TODO: Implement addStudent() method.


        $validation = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'phone' => 'required|min:10',
            'password' => 'required',
        ]);


        if($validation->fails()){
            return $this->ApiResponse(200, 'Validation Error', $validation->errors());
        }




        $studentRole = $this->roleModel::where([['is_teacher', 0], ['is_staff', 0]])->first();

        $student = $this->userModel::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $studentRole->id,
        ]);

        if($request->has('groups')){
            foreach ($request->groups as $group){
                $requestGroup = explode(',', $group);

                $this->studentGroupModel::create([
                    'student_id' =>$student->id,
                    'group_id' =>$requestGroup[0],
                    'count' =>$requestGroup[1],
                    'price' =>$requestGroup[2],
                ]);
            }
        }



        return $this->ApiResponse(200, 'Student Was Created', null, $student);
    }












    public function allStudents()
    {
        // TODO: Implement allStudent() method.

        $students = $this->userModel::whereHas('roleName', function ($q){
            return $q->where([['is_teacher', 0], ['is_staff', 0]]);
        })->withCount('studentGroups')->get();


        return $this->ApiResponse(200, 'Done', null, $students);
    }











    public function updateStudent($request)
    {
        // TODO: Implement updateStudent() method.
        $validation = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'phone' => 'required',
            'email' => 'required|email|unique:users,email,'.$request->student_id,
            'password' => 'required|min:8',
            'student_id' => 'required|exists:users,id',
        ]);

        if($validation->fails()){
            return $this->ApiResponse(422, 'Validation Errors', $validation->errors());
        }


        $student = $this->userModel::find($request->student_id);
        $student->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        if($request->has('groups')){
            foreach ($request->groups as $group){
                $requestGroup = explode(',', $group);

                $this->studentGroupModel::create([
                    'student_id' =>$request->student_id,
                    'group_id' =>$requestGroup[0],
                    'count' =>$requestGroup[1],
                    'price' =>$requestGroup[2],
                ]);
            }
        }

        return $this->ApiResponse(200, 'Student Was Updated', null,  $student);
    }





    public function deleteStudentT($request)
    {
        // TODO: Implement deleteStudentT() method.
    }





    public function specificStudent($request)
    {
        // TODO: Implement specificStudent() method.
    }





    public function updateStudentRequest($request)
    {
        // TODO: Implement updateStudentRequest() method.
    }

    public function deleteStudentRequest($request)
    {
        // TODO: Implement deleteStudentRequest() method.
    }
}