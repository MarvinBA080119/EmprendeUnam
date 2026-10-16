@extends('errors.layout')

@section('codigo', '409')
@section('titulo', 'No se pudo completar la operación')
@section('mensaje')
{{ $mensaje ?? 'La operación no cumple una regla de la revista.' }}
@endsection
