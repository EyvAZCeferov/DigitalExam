@extends('backend.layouts.main')

@push('js')

    <script>
        var elem = document.querySelector('.js-switch');
        var switchery = new Switchery(elem, {color: '#1AB394'});
    </script>
@endpush

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>İmtahanlar</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">İdarə Paneli</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('exams.index') }}">İmtahanlar</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Yenilə</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <div class="ibox-tools mb-3">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="fullscreen-link">
                                <i class="fa fa-expand"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h5>Yenilə</h5>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('exams.index') }}" class="btn btn-w-m btn-default">Geri</a>
                            </div>
                        </div>

                    </div>
                    <div class="ibox-content">
                        <form action="{{ route('exams.update', $exam->id) }}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @method('put')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Ad</label>
                                        <input type="text" value="{{ old('name', $exam->name) }}" name="name"
                                               class="form-control {{ $errors->first('name') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                               
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Bal</label>
                                        <input type="text" value="{{ old('point', $exam->point) }}" name="point" class="form-control {{ $errors->first('point') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Kateqoriya</label>
                                        <select name="category_id" class="form-control {{ $errors->first('category_id') ? 'is-invalid' : '' }}">
                                            <option hidden disabled selected>Seçim edin</option>
                                            @foreach(\App\Models\Category::whereNull('parent_id')->with('sub')->get() as $category)
                                                <optgroup label="{{ $category->name }}">
                                                    @foreach($category->sub as $sub)
                                                        <option {{ old('category_id', $exam->category_id) == $sub->id ? 'selected' : '' }} value="{{ $sub->id }}">{{ $sub->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="w-100">Status</label>
                                        <input type="checkbox" {{ $exam->status ? 'checked' : '' }} value="1"
                                               name="status"
                                               class="js-switch {{ $errors->first('status') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="w-100">Sıra nömrəsi</label>
                                    <input type="number" checked value="1" name="order_number"
                                        {{ isset($exam) && !empty($exam) && $exam->order_number ? $exam->order_number : 1 }}
                                        class="form-control {{ $errors->first('order_number') ? 'is-invalid' : '' }}">
                                </div>
                            </div>
{{--                            <div class="row">--}}
{{--                                <div class="col-12">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="">Məzmun</label>--}}
{{--                                        <textarea name="content"--}}
{{--                                                  class="form-control {{ $errors->first('content') ? 'is-invalid' : '' }}">{{ old('content', $exam->content) }}</textarea>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

                            <hr>

                            <button class="btn btn-primary">Yenilə</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
