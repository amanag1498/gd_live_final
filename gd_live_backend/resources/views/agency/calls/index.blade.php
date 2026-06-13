@extends('layouts.agency-tailadmin')
@section('title','Agency Calls')
@section('page_intro','Call activity, minutes, and spend across hosts attached to your agency.')

@section('content')
  @include('partials.call-report-table', ['layout' => 'agency'])
@endsection
