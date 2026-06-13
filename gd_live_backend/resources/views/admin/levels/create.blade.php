@extends('layouts.admin-tailadmin')
@section('title', 'Create Level')

@section('content')
<form method="post" action="{{ route('admin.levels.store') }}" class="space-y-6">
  @csrf
  @include('admin.levels._form', ['mode' => 'create'])
</form>
@endsection
