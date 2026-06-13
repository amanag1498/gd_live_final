@extends('layouts.admin-tailadmin')
@section('title', 'Create Recharge Plan')

@section('content')
<form method="post" action="{{ route('admin.recharge-plans.store') }}" class="space-y-6">
  @csrf
  @include('admin.recharge-plans._form', ['mode' => 'create'])
</form>
@endsection
