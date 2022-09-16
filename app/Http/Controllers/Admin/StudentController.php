<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Mail\SendMail;
use App\Repositories\Faculties\FacultyRepositoryInterface;
use App\Repositories\Students\StudentRepositoryInterface;
use App\Repositories\Subjects\SubjectRepositoryInterface;
use App\Repositories\Users\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;

class StudentController extends Controller
{
    protected $studentRepository,
        $userRepository,
        $subjectRepository,
        $facultyRepository,
        $page,
        $avg;

    public function __construct(StudentRepositoryInterface $studentRepository,
                                FacultyRepositoryInterface $facultyRepository,
                                UserRepositoryInterface    $userRepository,
                                SubjectRepositoryInterface $subjectRepository,
                                Config                     $page, Config $avg)
    {
        $this->studentRepository = $studentRepository;
        $this->userRepository = $userRepository;
        $this->facultyRepository = $facultyRepository;
        $this->subjectRepository = $subjectRepository;
        $this->page = $page;
        $this->avg = $avg;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $avg = $this->avg::get('constants.options.avg');
        $students = $this->studentRepository->search($request->all());
        $countSubject = $this->subjectRepository->count();
        $faculties = $this->facultyRepository->pluck('name', 'id');
        return view('admin.students.index', compact('students', 'faculties', 'countSubject', 'students', 'avg'));
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
        $student['avatar'] = null;
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
        dd($student);
        return view('admin.students.form', compact('student'));
//        return response()->json([
//            'student' => $student,
//            'id' => $student->id,
//            'faculty_id' => $student->faculty_id
//        ]);
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

    public function getListDeleted()
    {
        $student = $this->studentRepository->getStudentDeleted();
        return view('admin.students.list-deleted', compact('student'));
    }

    public function restore($id)
    {
        $model = $this->studentRepository::withTrashed()->find($id);
        $model->restore();
        Session::flash('success', 'Restore Student Successful');
        return redirect()->route('students.index');
    }

    public function resgistation(Request $request)
    {
        $students = $this->studentRepository->newModel();
        $studentId = $this->studentRepository->getStudentById();
        $students->subjects()->attach($request->subject_id, ['student_id' => $studentId]);
        Session::flash('success', 'Resgiste Subject Successful');
        return redirect()->route('subjects.index');
    }

    public function resgistationFaculty(Request $request)
    {
        $data = $request['faculty_id'];
        $student = $this->studentRepository->getStudentById();
        $student->update($data, $student);
        return redirect()->back();
    }

    public function updatePoint($id)
    {
        $student = $this->studentRepository->find($id);
        $students = $student->subjects;
        $mark = '';
        foreach ($students as $param) {
            $mark .= $param->pivot->mark;
        }
//        dd($mark);
        $html = '';
        foreach ($students as $key) {
            $html .= '<option>' . $key->name . '</option>';
        }
        //    "<select id='selectbox' class='form-control'>" + "<option value='##'>" + 'Subject'  +"</option>" + "</select>"
        return view('admin.students.update-mark', compact('students', 'html', 'mark'));
    }

    public function export($id)
    {
        return Excel::download(new StudentExport($id), 'point-student.xlsx');
        return Excel::store(new StudentExport, 'point-student.xlsx', 'disk-name');
    }


}
