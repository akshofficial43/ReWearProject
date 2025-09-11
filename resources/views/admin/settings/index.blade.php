@extends('admin.layouts.app')

@section('title', 'Settings')

@section('content')
<div class="card">
  <div class="card-header fw-semibold">Settings</div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.settings.update') }}">
      @csrf
      <button class="btn btn-primary" type="submit">Save</button>
    </form>
  </div>
</div>
@endsection
