@extends('layouts.admin-tailadmin')
@section('title', 'Edit Recharge Plan')

@section('content')
<form method="post" action="{{ route('admin.recharge-plans.update', $plan) }}" class="space-y-6">
  @csrf
  @method('PUT')
  @include('admin.recharge-plans._form', ['mode' => 'edit'])
</form>
@endsection
