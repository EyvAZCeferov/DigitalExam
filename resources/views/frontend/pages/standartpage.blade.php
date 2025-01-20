@extends('frontend.layouts.app')
@section('title')
    {{ $data->name[app()->getLocale() . '_name'] }}
@endsection
@section('content')
    @include('frontend.light_parts.about',['page'=>$data])
@endsection
