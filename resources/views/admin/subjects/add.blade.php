@extends('admin.admin-master')
@section('title', 'Add Subject')
@section('content-title', 'Add Subject')
@section('danh-muc', 'Add Subject')
@section('content')


    <form action="{{route('subjects.store')}} " method="post" enctype="multipart/form-data">
        <br>
        @csrf
        @include('admin.admin-alert')
        <div class="row mb-4">
            <div class="col">
                <div class="form-outline">
                    <label class="form-label" for="form3Example1">Name Subject</label>
                    <input type="text" id="form3Example1" value="{{ old('name') }}" name="name"
                        class="form-control" />
                    @error('name')
                        <h6 class="text-danger">{{ $message }} </h6>
                    @enderror
                </div>
            </div>

        </div>

        <button type="submit" class="btn btn-danger btn-block mb-4">Tạo mới</button>
        <!-- Register buttons -->
        <button type="reset" class="btn btn-warning btn-block mb-4">Nhập lại</button>

    </form>
@endsection
