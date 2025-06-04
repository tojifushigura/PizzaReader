@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12">
                    <h3 class="mt-1 float-start">{{ isset($team) ? __('Edit team') : __('Create team') }}</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ isset($team) ? route('admin.teams.update', $team->id) : route('admin.teams.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($team)) @method('PATCH') @endif

                <div class="mb-3 row">
                    <label for="name" class="form-label col-sm-2 col-form-label required">Name</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="191" name="name" id="name" placeholder="Name"
                               class="form-control @error('name') is-invalid @enderror col-sm-12"
                               value="{{ old('name', $team->name ?? '' )}}" required>
                        @error('name')
                        @include('partials.invalid_feedback')
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="slug" class="form-label col-sm-2 col-form-label required">Slug</label>
                    <div class="col-sm-10 inline-components">
                        <div class="col-sm-10">
                            <input type="text" maxlength="191" name="slug" id="slug" placeholder="Slug"
                               class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $team->slug ?? '' )}}" disabled>
                        </div>
                        <div class="btn btn-lg btn-success col-sm-2 col" onclick="event.preventDefault();$('#slug').prop('disabled', function(i, v) { return !v; });let text =$(this).text();$(this).text(text === 'Edit' ? 'Undo' : 'Edit').toggleClass('btn-success').toggleClass('btn-danger');">Edit</div>
                        @error('slug')
                        @include('partials.invalid_feedback')
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="url" class="form-label col-sm-2 col-form-label required">URL</label>
                    <div class="col-sm-10">
                        <input type="url" maxlength="191" name="url" id="url" placeholder="URL"
                               class="form-control @error('url') is-invalid @enderror col-sm-12"
                               value="{{ old('url', $team->url ?? '' )}}" required>
                        @error('url')
                        @include('partials.invalid_feedback')
                        @enderror
                    </div>
                </div>

                <div class="d-grid gap-2 max-auto">
                    <button type="submit" id="submit" class="btn btn-lg btn-success">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
