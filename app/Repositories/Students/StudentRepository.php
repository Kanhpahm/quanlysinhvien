<?php

namespace App\Repositories\Students;

use App\Models\Student;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class StudentRepository extends BaseRepository implements
    StudentRepositoryInterface
{

    /**
     * @return mixed
     */
    public function getModel()
    {
        return Student::class;
    }

    public function validateAge($request)
    {
        if ($request->input('price') != 0 && $request->input('price_sale') != 0
            && $request->input('price_sale') >= $request->input('price')
        ) {
            Session::flash('error', 'Giá giảm phải nhỏ hơn giá gốc');
            return false;
        }

        if ($request->input('price_sale') != 0 && (int)$request->input('price') == 0) {
            Session::flash('error', 'Vui lòng nhập giá gốc');
            return false;
        }

        return true;
    }

    public function getStudents()
    {
        return $this->model
            ->select('id', 'name', 'faculty_id', 'email', 'avatar', 'birthday', 'phone', 'created_at', 'updated_at')
            ->with('faculty')
            ->withTrashed()
            ->orderBy('updated_at', 'DESC')->paginate();
    }

    public function search($data)
    {
        $student = $this->model->newQuery();
        $page = Config::get('constants.options.paginateStudent');
        if (isset($data['age_from'])) {
            $student->whereYear('birthday', '<=', Carbon::now()->subYear($data['age_from'])->format('Y'));
        }

        if (isset($data['age_to'])) {
            $student->whereYear('birthday', '>=', Carbon::now()->subYear($data['age_to'])->format('Y'));
        }
        return $student->orderBy('updated_at', 'DESC')->paginate($page);
    }

    public function getStudentDeleted()
    {
        return $this->model->onlyTrashed()->paginate();
    }

    public function getStudent()
    {
        return $this->model->where('user_id', Auth::user()->id)->first();
    }

    public function getStudentById()
    {
        return $this->model->where('user_id', Auth::id())->first()->id;
    }


}
