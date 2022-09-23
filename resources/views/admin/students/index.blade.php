@extends('admin.admin-master')
@section('content-title', 'List Students')
@section('title', 'List Students')
@section('content')
    <div class="container">
        <form action="{{route('students.index')}}" method="get">
            <div class="row">
                <div class="col-sm-3">
                    <label class="form-label" for="form3Example1">Age From</label>
                    <input type="number" class="form-control input-sm" id="fromOld" name="age_from" require>
                </div>
                <div class="col-sm-3">
                    <label class="form-label" for="form3Example1">Age to</label>
                    <input type="number" class="form-control input-sm" id="toOld" name="age_to" require>
                </div>
                <div class="col-sm-3">
                    <button style="margin-top: 35px" type="submit" class="btn btn-outline-success btn-sm" name="search">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-2 flex-md-grow-1">
        @can('create')
            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#create-bookmark"> Add Student</button>
        @endcan
    </div>
    <div class="modal fade modal-bookmark" id="create-bookmark" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Student</h5>
                </div>
                <div class="modal-body">
                    {{ Form::model($student, ['method' => 'POST']) }}
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            {!!Form::label('', 'Name student')!!}
                            {!!Form::text('name', '',['class' => 'form-control' ,'id' => 'name_student','placeholder' => 'Enter name student'])!!}
                            <div class="invalid-feedback validate-name"></div>
                        </div>
                        <div class="form-group col-md-12">
                            {!!Form::label('', 'Email student')!!}
                            {!!Form::email('email', '',['class' => 'form-control' ,'id' => 'email_student','placeholder' => 'Enter email student'])!!}
                            <div class="invalid-feedback validate-email"></div>
                        </div>
                        <div class="form-group col-md-12">
                            {!!Form::label('', 'Phone number student')!!}
                            {!!Form::text('phone', '',['class' => 'form-control' ,'id' => 'phone_student','placeholder' => 'Enter phone student'])!!}
                            <div class="invalid-feedback validate-phone"></div>
                        </div>
                        <div class="form-group col-md-12">
                            {!!Form::label('', 'Birthday student')!!}
                            {!!Form::date('birthday', '',['class' => 'form-control' ,'id' => 'birthday_student','placeholder' => 'Enter birthday student'])!!}
                            <div class="invalid-feedback validate-birthday"></div>
                        </div>
                        <div class="form-group col-md-12">
                            {!!Form::label('', 'Address student')!!}
                            {!!Form::text('address', '',['class' => 'form-control' ,'id' => 'address_student','placeholder' => 'Enter address student'])!!}
                            <div class="invalid-feedback validate-address"></div>
                        </div>
                        <div class="form-group col-md-12">
                            {{ Form::label('', 'Gender', ['class' => 'col-form-label pt-0']) }}
                            <div class="form-group m-t-15 m-checkbox-inline mb-0 custom-radio-ml">
                                <div class="radio radio-primary">
                                    {{Form::radio('gender', '0', true, ['class' => 'form-check-input', 'id' => 'radioinline11'])}}
                                    {{ Form::label('radioinline11', 'Male', ['class' => 'mb-0']) }}
                                </div>
                                <div class="radio radio-primary">
                                    {{Form::radio('gender', '1', false, ['class' => 'form-check-input', 'id' => 'radioinline22'])}}
                                    {{ Form::label('radioinline22', 'Female', ['class' => 'mb-0']) }}
                                </div>
                                <div class="invalid-feedback validate-gender"></div>
                            </div>
                        </div>
                    </div>
                    <br>
                    {!! Form::submit('Save', ['class' => 'btn btn-secondary','id' => 'saveCreateForm'])!!}
                    {!! Form::button('Cancel', ['class' => 'btn btn-primary','data-bs-dismiss' => 'modal'])!!}
                    {!! Form::close() !!}
                </div>
                {!!Form::hidden('student_id', '',['id' => 'student_id'])!!}
                {!!Form::hidden('user_id', '',['id' => 'user_id'])!!}
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    <script type="text/javascript" src="{{asset('dist/js/add_student.js')}}"></script>
    <script type="text/javascript" src="{{asset('dist/js/show_subject.js')}}"></script>
    <div class="card-body px-0 pb-0">
        <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
                <thead>
                <tr>
                    <th class="text-uppercase text-secondary text-center text-xxs font-weight-bolder opacity-7">
                        Stt
                    </th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                    <th class="text-uppercase text-secondary text-center text-xxs font-weight-bolder opacity-7">Avatar
                    </th>
                    <th class="text-uppercase text-secondary text-center text-xxs font-weight-bolder opacity-7">Name
                        Faculty
                    </th>
                    <th class="text-uppercase text-secondary text-center text-xxs font-weight-bolder opacity-7">
                        Learned
                    </th>
                    <th class="text-uppercase text-secondary text-center text-xxs font-weight-bolder opacity-7">
                        Show Subject
                    </th>
                    <th class="text-uppercase text-secondary text-center text-xxs font-weight-bolder opacity-7">AVG
                        Point
                    </th>
                    <th class="text-uppercase text-secondary text-center text-xxs font-weight-bolder opacity-7">
                        <a href="{{ route('student-list-deleted') }}"
                           class="btn btn-secondary btn-sm"> Deleted Student </a>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach ($students as $index => $item)
                    <tr>
                        <td id="id" class="text-center">
                            {{ $index + 1 }}
                        </td>
                        <td id="name">
                            {{ $item->name }}
                        </td>
                        <td class="text-center">
                            <img title="{{$item->name}}" src="{{ asset($item->avatar) }}" class="width">
                        </td>
                        <td class="text-center">
                            @if(isset($item->faculty))
                                {{ $item->faculty->name }}
                            @else
                                <b class="text-danger">Registered Any Faculties! </b>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $item->subjects->count() .'/'. $countSubject }}
                        </td>
                        <td class="text-center">
                            <a data-toggle="modal" data-id="{{$item->id}}" data-target="#exampleModal"
                               class="btnModal gradient-button gradient-button-3">
                                <i class="fa fa-arrow-up text-white"></i>
                            </a>
                            <div class="modal fade" data-id="{{$item->id}}" id="exampleModal" tabindex="-1"
                                 role="dialog" aria-labelledby="exampleModalLabel"
                                 aria-hidden="true">
                                <div class="modal-dialog" data-id="{{$item->id}}" role="document">
                                    <div class="modal-content">
                                        <br>
                                        <h5 class="modal-title" id="exampleModalLabel"><b>
                                                List Subject
                                            </b>
                                        </h5>
                                        <form id="form_update" method="post">
                                            <div class="modal-body">
                                                <table class="table table-hover">
                                                    <thead>
                                                    <tr style=" text-align: left">
                                                        <th scope="col">#</th>
                                                        <th scope="col" class="text-center">Name</th>
                                                        <th scope="col">Mark</th>
                                                    </tr>
                                                    </thead>

                                                    <tbody id="table-subject">
                                                    <div class="col">

                                                    </div>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn-outline-danger btn btn-sm"
                                                        data-dismiss="modal">Close
                                                </button>
                                                <button type="submit" id="update"
                                                        class="btn btn-outline-success btn-sm">Update
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                        @if($item->subjects->count() == 0)
                            <td class="text-sm text-info text-sm-center"> Haven't Registered</td>
                        @elseif($item->subjects->count() < $countSubject)
                            <td class="text-sm text-success text-sm-center"> Studying</td>
                        @else
                            @for($i=0; $i < $countSubject; $i++)
                                @if(!$item->subjects[$i]->pivot->mark)
                                    <td class="text-sm text-center text-success text-sm-center"> Studying</td>
                                    @break
                                @elseif($i == $countSubject - 1)
                                    @if(round($item->subjects->avg('pivot.mark'), 2) < $avg)
                                        <td class="text-center text-danger"> {{round($item->subjects->avg('pivot.mark'), 2)}} </td>
                                    @else
                                        <td class="text-center"> {{round($item->subjects->avg('pivot.mark'), 2)}} </td>
                                    @endif
                                @endif
                            @endfor
                        @endif
                        <td class="text-center">
                            @can('edit')
{{--                                                                <a style="color: #febc06" href="{{route('students.edit', $item->id)}}">--}}
{{--                                                                    <i class="fa fa-edit"></i>--}}
{{--                                                                </a>--}}
                                <a style="color: #febc06" href="" onclick="update({{ $item->id }})"
                                   data-bs-toggle="modal"
                                   data-bs-target="#edit-bookmark" id="editStudent" data-id="{{ $item->id }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                            @endcan
                            @can('delete')
                                <a style="color: red" href="{{ route('students.destroy', $item->id) }}"
                                   class="btnDelete">
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </table>
            {{ Form::model($students, ['route' => ['mail_subjects_all'], 'method' => 'get'])}}
            <button type="submit" class="btn btn-outline-warning btn-sm"
                    onclick="return confirm('Do you want send to student?')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-arrow-right-circle" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                </svg>
            </button>
            {{ Form::close()}}
            <form action="" method="POST" id="form-delete">
                {{ method_field('DELETE') }}
                {!! csrf_field() !!}
            </form>
            <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
            <script src="{{asset('dist/js/edit_student.js')}}"></script>
        </div>
        <link href="{{asset('dist/css/btn-css.css')}}" rel="stylesheet">
        <div>
            {{ $students->links() }}
        </div>
        <div class="modal fade modal-bookmark" id="edit-bookmark" tabindex="-1" style="display: none;"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Student</h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="form-bookmark needs-validation" novalidate="">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    {!! Form::label('', 'Name')!!}
                                    {!! Form::text('name' ,'', ['class' => 'form-control' , 'id' => 'nameStudent'])!!}

                                    {!! Form::label('', 'Phone number ')!!}
                                    {!! Form::text('phone' ,'', ['class' => 'form-control' , 'id' => 'phoneStudent'])!!}

                                    {!! Form::label('', 'Address Student')!!}
                                    {!! Form::text('address' ,'', ['class' => 'form-control' , 'id' => 'addressStudent'])!!}

                                    {{ Form::label('', 'Gender', ['class' => 'col-form-label pt-0']) }}
                                    <div class="form-group m-t-15 m-checkbox-inline mb-0 custom-radio-ml">
                                        @if ($errors->first('gender'))
                                            <div class="radio radio-primary">
                                                {{Form::radio('gender', '0', true, ['class' => 'form-check-input is-invalid', 'id' => 'radioinline11'])}}
                                                {{ Form::label('radioinline11', 'Male', ['class' => 'mb-0']) }}
                                            </div>
                                            <div class="radio radio-primary">
                                                {{Form::radio('gender', '1', false, ['class' => 'form-check-input is-invalid', 'id' => 'radioinline22'])}}
                                                {{ Form::label('radioinline22', 'Female', ['class' => 'mb-0']) }}
                                            </div>
                                            <div class="invalid-feedback">{{$errors->first('gender')}}</div>
                                        @else
                                            <div class="radio radio-primary">
                                                {{Form::radio('gender', '0', true, ['class' => 'form-check-input', 'id' => 'radioinline11'])}}
                                                {{ Form::label('radioinline11', 'Male', ['class' => 'mb-0']) }}
                                            </div>
                                            <div class="radio radio-primary">
                                                {{Form::radio('gender', '1', false, ['class' => 'form-check-input', 'id' => 'radioinline22'])}}
                                                {{ Form::label('radioinline22', 'Female', ['class' => 'mb-0']) }}
                                            </div>
                                        @endif
                                    </div>
                                    {!! Form::label('', 'Birthday student')!!}
                                    {!! Form::date('birthday', '', ['class' => 'form-control' , 'id' => 'birthdayStudent'])!!}

                                    {!! Form::label('', 'Email')!!}
                                    {!! Form::text('email' ,'', ['class' => 'form-control' , 'id' => 'emailStudent', 'readonly'])!!}
                                </div>
                            </div>
                            <input type="hidden" name="student_id" id="student_id">
                            <br>
                            <button class="btn btn-secondary" type="button" id="saveUpdateForm" onclick="saveUpdate()">
                                Save
                            </button>
                            <button class="btn btn-primary" type="button">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <style>
            a {
                font-size: 20px;
            }
        </style>
    </div>
@endsection
