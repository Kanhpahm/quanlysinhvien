<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacultyRequest;
use App\Repositories\Faculties\FacultyRepositoryInterface;
use App\Repositories\Students\StudentRepositoryInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class FacultyController extends Controller
{
    protected $facultyRepository, $studentRepository;

    public function __construct(FacultyRepositoryInterface $facultyRepository,
                                StudentRepositoryInterface $studentRepository)
    {
        $this->facultyRepository = $facultyRepository;
        $this->studentRepository = $studentRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $student = Config::get('constants.options.roleStudent');
        $faculties = $this->facultyRepository->facultyList();
        return view('admin.faculties.index', compact('faculties', 'student'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $faculty = $this->facultyRepository->newModel();
        return view('admin.faculties.form', compact('faculty'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(FacultyRequest $request)
    {
        $data = $this->facultyRepository->create($request->all());
        Session::flash('success', 'Create Faculty Successful');
        return redirect()->route('faculties.index');
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
        $faculty = $this->facultyRepository->find($id);
        return view('admin.faculties.form', compact('faculty'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function update(FacultyRequest $request, $id)
    {
        $data = $request->all();
        $this->facultyRepository->update($id, $data);
        Session::flash('success', 'Update Successful');
        return redirect()->route('faculties.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->facultyRepository->delete($id);
        Session::flash('success', 'Delete Faculty Successful');
        return redirect()->route('faculties.index');
    }
}
