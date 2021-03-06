@extends('layout')
@section('title', 'Edit Student Research Profile')
@section('header')
	@include('nav')
@stop
@section('content')

<div class="container">

    <h1 class="mb-0">Student Research Profile</h1>
    <h2 class="mt-0 text-muted">for {{ $student->full_name }}</h2>

    <div class="alert alert-primary" role="alert">
        <p class="mb-0">Complete and submit your student research profile below. This profile will not be public, but will be made available to faculty researchers who may be looking for students. After submitting, you can always come back later to edit or withdraw your student research profile.</p>
    </div>

    {!! Form::model($student, ['route' => ['students.update', $student]]) !!}
        @include('students.form', ['editable' => true])
    {!! Form::close() !!}

</div>

@stop