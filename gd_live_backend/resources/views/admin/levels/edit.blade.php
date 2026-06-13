@extends('layouts.admin-tailadmin')
@section('title', 'Edit Level')

@section('content')
<form method="post" action="{{ route('admin.levels.update', $level) }}" class="space-y-6">
  @csrf
  @method('PUT')
  @include('admin.levels._form', ['mode' => 'edit'])
</form>
@endsection
