@extends('partials.form.form', ['fields' => \App\Models\Chapter::getFormFields()])
@section('card-title', __('Edit chapter'))
@section('form-action', route('admin.comics.chapters.update', ['comic' => $comic->id, 'chapter' => $chapter->id]))
@section('method', method_field('PATCH'))
@section('choose-file', __('Choose file'))
@foreach(\App\Models\Chapter::getFormFields() as $field)
    @section($field['parameters']['field'], old($field['parameters']['field'], isset($chapter->{$field['parameters']['field']}) ? $chapter->{$field['parameters']['field']} : ($field['parameters']['default'] ?? '') ))
@endforeach
