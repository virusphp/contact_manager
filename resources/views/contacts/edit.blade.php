@extends('layouts.main')

@section('content')

<div class="panel panel-default">
<div class="panel-heading">
  <strong>Edit Contact</strong>
</div>
{{-- form model contact di lewatkan pada model dan pada route mengidentifikasikan id --}}
{!! Form::model($contact, ['files' => true, 'route' => ['contacts.update', $contact->id], 'method' => 'PATCH']) !!}

@include("contacts.form")

{!! Form::close() !!}
</div>

@endsection