@extends('layout')
@section('title', 'Student Research Profiles')
@section('header')
	@include('nav')
@stop
@section('content')

<div class="container">

    <h1>Student Research Profiles</h1>

    <livewire:students-table>

</div>

@stop
