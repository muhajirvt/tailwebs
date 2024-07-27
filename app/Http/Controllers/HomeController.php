<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        $students = Student::get(['id','name','subject','mark']);
        $subjects = [
            'English', 'Maths', 'IT'
        ];
        return view('home',compact('students','subjects'));
    }

    public function addEditStudent(Request $request)
    {
        $validator = Validator::make($request->input(),[
            'name'    => 'required',
            'subject' => 'required',
            'mark'    => 'required'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status'  => 0,
                'message' => $validator->errors()
            ]);
        } else {
            $studentId = $request->student_id;
            $name = $request->name;
            $subject = $request->subject;
            $mark = $request->mark;
            $message = "";
            if(!$studentId) {
                $student = Student::where([
                    ['name',$name], ['subject',$subject]
                ])->first(['id','mark']);
                if(!$student) {
                    $student = new Student();
                    $student->name = $name;
                    $student->subject = $subject;
                    $student->mark = $mark;
                    $student->save();
                    $message = "Student Added Successfully";
                } else {
                    $student->mark = $student->mark + $mark;
                    $student->save();
                    $message = "Student Mark updated successfully";
                }
            } else {
                $student = Student::find($studentId);
                $student->name = $name;
                $student->subject = $subject;
                $student->mark = $mark;
                $student->save();
                $message = "Student Updated Successfully";
            }

            return response()->json([
                'status'  => 1,
                'message' => $message
            ]);
        }
    }

    public function updateStudent(Request $request) {
        $student = Student::find($request->studentId);
        if($student) {
            $fieldName = $request->fieldName;
            $student->$fieldName = $request->value;
            $student->save();
            $status = 1;
            $message = "Student updated successfully";
        } else {
            $status = 0;
            $message = "Something went wrong";
        }
        return response()->json([
            'status'  => $status,
            'message' => $message
        ]);
    }

    public function deleteStudent($studentId) {
        $student = Student::find($studentId);
        if($student) {
            $student->delete();
            $status = 1;
            $message = "Student deleted successfully";
        } else {
            $status = 0;
            $message = "Something went wrong";
        }
        return response()->json([
            'status'  => $status,
            'message' => $message
        ]);
    }
}
