<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Mail\SendMail;
use App\Models\Student;
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
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    protected $_studentRepository,
        $_userRepository,
        $_subjectRepository,
        $_facultyRepository,
        $_page,
        $_avg;

    public function __construct(StudentRepositoryInterface $_studentRepository,
                                FacultyRepositoryInterface $_facultyRepository,
                                UserRepositoryInterface    $_userRepository,
                                SubjectRepositoryInterface $_subjectRepository,
                                Config                     $_page,
                                Config                     $_avg
    )
    {
        $this->studentRepository = $_studentRepository;
        $this->userRepository = $_userRepository;
        $this->facultyRepository = $_facultyRepository;
        $this->subjectRepository = $_subjectRepository;
        $this->page = $_page;
        $this->avg = $_avg;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $student = $this->studentRepository->getStudents();
        $avg = $this->avg::get('constants.options.avg');
        $students = $this->studentRepository->search($request->all());
        $countSubject = $this->subjectRepository->count();
        $faculties = $this->facultyRepository->pluck('name', 'id');
        return view('admin.students.index', compact('students', 'faculties', 'countSubject', 'students', 'avg', 'student'));
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
        $student['faculty_id'] = NULL;
        $student['avatar'] = 'images/users/student.png';
        $student['phone'] = $request['phone'];
        $student['birthday'] = $dt->toDateString();
        $student['gender'] = $request['gender'];
        $student['address'] = $request['address'];
        $student['code'] = Str::uuid()->toString();
        $students = $this->studentRepository->create($student);
        $mailable = new SendMail($user);
        Mail::to($user->email)->send($mailable);
        return response()->json();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

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
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $student = $this->studentRepository->find($id)->update($request->all());
        return response()->json(['data' => $student, 'student' => $request->all(), 'studentid' => $id], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response_
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
        Student::where('id', $id)->withTrashed()->restore();
        Session::flash('success', 'Restore Student Successful');
        return redirect()->route('student-list-deleted');
    }

    public function registerSubject(Request $request)
    {
        $students = $this->studentRepository->newModel();
        $studentId = $this->studentRepository->getStudentById();
        if (!$request->subject_id) {
            Session::flash('error', 'You dont choose subjects');
            return redirect()->route('subjects.index');
        }
        $students->subjects()->attach($request->subject_id, ['student_id' => $studentId]);
        Session::flash('success', 'Register Subject Successful');
        return redirect()->route('subjects.index');
    }

    public function registerFaculty(Request $request, $id)
    {
        $student = $this->studentRepository->getStudent();
        $countSubject = $this->subjectRepository->count('id');
        $sum = 0;
        $count = 0;
        if ($student->faculty_id) {
            Session::flash('error', 'You can not register');

            return redirect()->back();
        }
        foreach ($student->subjects as $std) {
            if ($student->subjects->count() == $countSubject) {
                $count++;
                if (!$std->pivot->mark) {
                    Session::flash('error', 'You can not register');

                    return redirect()->back();
                }
                $sum += $std->pivot->mark;
            } else {
                Session::flash('error', 'You can not register');

                return redirect()->back();
            }
        }
        $avgPoint = 5;
        if ($sum) {
            $avg = $sum / $count;
            if ($avg < $avgPoint) {
                Session::flash('error', 'Your GPA Is Not Eligible To Apply For This Course ');

                return redirect()->back();
            } else {
                $data = [
                    'faculty_id' => $id
                ];
                $this->studentRepository->update($student->id, $data);
                Session::flash('success', 'Register Successfully');

                return redirect()->back();
            }
        }
        Session::flash('error', 'You can not register');

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
        $html = '';
        foreach ($students as $key) {
            $html .= '<option>' . $key->name . '</option>';
        }
        return view('admin.students.update-mark', compact('students', 'html', 'mark'));
    }

    public function export($id)
    {
        return Excel::download(new StudentExport($id), 'point-student.xlsx');
        return Excel::store(new StudentExport, 'point-student.xlsx', 'disk-name');
    }

    public function showSubject($id)
    {
        return Student::with(['subjects'])->find($id);
    }

}
