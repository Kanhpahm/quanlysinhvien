<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Mail\SendMail;
use App\Models\Faculty;
use App\Models\Student;
use App\Repositories\Faculties\FacultyRepository;
use App\Repositories\Students\StudentRepository;
use App\Repositories\Subjects\SubjectRepository;
use App\Repositories\Users\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Ramsey\Uuid\Uuid;

class StudentController extends Controller
{
    protected $studentRepository, $userRepository, $subjectRepository, $facultyRepository;


    public function __construct(StudentRepository $studentRepository, FacultyRepository $facultyRepository, UserRepository $userRepository, SubjectRepository $subjectRepository)
    {
        $this->studentRepository = $studentRepository;
        $this->userRepository = $userRepository;
        $this->facultyRepository = $facultyRepository;
        $this->subjectRepository = $subjectRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $students = $this->studentRepository->search($request->all());
        $faculties = $this->facultyRepository->pluck('name', 'id');
        return view('admin.students.index', compact('students', 'faculties'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $student = $this->studentRepository->newModel();
        $faculties = $this->facultyRepository->pluck('id', 'name')->all();
        return view('admin.students.form', compact('student', 'faculties'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StudentRequest $request)
    {
        $input = [
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make('123'),
        ];
        $user = $this->userRepository->create($input);
        $user->assignRole('student');
        $dt = Carbon::now('Asia/Ho_Chi_Minh');
        $student['user_id'] = $user->id;
        $student['name'] = $user->name;
        $student['email'] = $user->email;
        $student['faculty_id'] = $request['faculty_id'];
        $student['avatar'] = 'images/students/Phạm Văn Khánh_T5wx0WCn236nh58fHlZBAyqaR1SPlv4bduoIchwk.png';
        $student['phone'] = $request['phone'];
        $student['birthday'] = $dt->toDateString();
        $student['gender'] = $request['gender'];
        $student['address'] = $request['address'];
        $student['code'] = Uuid::generate()->string;
        $students = $this->studentRepository->create($student);
        $mailable = new SendMail($user);
        Mail::to($user->email)->send($mailable);
        return redirect()->route('students.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
//        $t = Student::find($id);
//        dd($t);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $student = $this->studentRepository->find($id);
        return response()->json([
            'student' => $student,
            'id' => $student->id,
            'faculty_id' => $student->faculty_id
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(StudentRequest $request, $id)
    {
        $data = $request->all();
//        $student = Student::find($id);
//        $student->name = $data['name'];
//        $student->address = $data['address'];
//        $student->phone = $data['phone'];
//        $student->email = $data['email'];
//        $student->gender = $data['gender'];
//        $student->faculty_id = $data['faculty_id'];
        $this->studentRepository->update($data);
        return response()->json([]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $student = $this->studentRepository->find($id);
        $student->delete();
        Session::flash('success', 'Delete Student Successful');
        return redirect()->route('students.index');
    }

    public function listDeleted()
    {
        $student = Student::all();
        dd($student);
    }

    public function restore($id)
    {
        $model = Student::withTrashed()->find($id);
        $model->restore();
        Session::flash('success', 'Restore Student Successful');
        return redirect()->route('students.index');
    }

    public function subcribe($id)
    {
        $subject = $this->subjectRepository->find($id)->id;
        $students = $this->studentRepository->newModel();
        $studentId = Student::where('user_id', Auth::id())->first();
        $students->subjects()->attach($subject, ['student_id' => $studentId]);
        Session::flash('success', ' Registation Subject Successful');
        return redirect()->route('subjects.index');
    }

    public function search_old(Request $request)
    {
        $date = date('Y');
        $formOld = $request['fromOld'];
        $toOld = $request['toOld'];
        if ($formOld > $toOld) {
            $formOld = $request['toOld'];
            $toOld = $request['fromOld'];
        }
        // dd($request);
        $stu = Student::all();
        if ($formOld == '' && $toOld == '') {
            return redirect()->route('students.index');
        } elseif ($formOld == '' | $toOld == '') {
            foreach ($stu as $item) {
                $bits = explode('-', $item->birthday);
                $age = $date - $bits[0];
                if ($age == $toOld) {
                    $_SESSION['students'] = $item;
                    $students[] = $_SESSION['students'];
                } else {
                    $students = [];
                }
            }
            return view('admin.students.index', compact('students'));
        } else {
            foreach ($stu as $item) {
                $bits = explode('-', $item->birthday);
                $age = $date - $bits[0];

                if ($formOld <= $age && $age <= $toOld) {
                    $_SESSION['students'] = $item;
                    $students[] = $_SESSION['students'];
                } else {
                    $students = [];
                }
            }
            return view('admin.students.index', compact('students'));
        }
    }

}
