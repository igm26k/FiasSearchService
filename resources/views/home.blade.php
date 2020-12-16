@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <api-example></api-example>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row justify-content-center" style="margin-top: 30px">
            <div class="col-md-6">
                <passport-clients></passport-clients>
            </div>
            <div class="col-md-6">
                <passport-personal-access-tokens></passport-personal-access-tokens>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection
