@extends('layouts.admin')
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-9 col-6">
                    <h3 class="mt-1 float-start">{{ __('Teams') }}</h3>
                    <a href="{{ route('admin.teams.create') }}" class="btn btn-success ms-3">{{ __('Add team') }}</a>
                </div>
                <div class="col-sm-3 col-6">
                    @include('partials.card-search')
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row border-bottom py-1 d-none d-md-flex">
                <div class="col-md-3 text-start">{{ __('Name') }}</div>
                <div class="col-md-3 text-start">{{ __('Slug') }}</div>
                <div class="col-md-4 text-start">{{ __('URL') }}</div>
                <div class="col-auto text-end ps-1">{{ __('Options') }}</div>
            </div>
            @foreach($teams as $team)
                <div class="row flex-md-nowrap text-truncate border-bottom py-1 item teams">
                    <div class="col-md-3 text-start"><span class="filter">{{ $team->name }}</span></div>
                    <div class="col-md-3 text-start">{{ $team->slug }}</div>
                    <div class="col-md-4 text-start"><a href="{{ $team->url }}" target="_blank">{{ $team->url }}</a></div>
                    <div class="col-auto text-end ps-0">
                        @if(Auth::user()->hasPermission('admin'))
                            <a href="{{ route('admin.teams.edit', $team->id) }}" class="btn btn-success">{{ __('Edit') }}</a>
                            <form id="delete-{{ $team->id }}" action="{{ route('admin.teams.destroy', $team->id) }}"
                                  method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                            <a href="{{ route('admin.teams.destroy', $team->id) }}" class="btn btn-danger"
                                data-bs-toggle="modal" data-bs-target="#modal-container" data-description="{{ __('Do you want to delete') }} {{ $team->name }}?" data-form="delete-{{ $team->id }}">{{ __('Delete') }}</a>
                        @else
                            <a href="#" class="btn btn-secondary" title="You can't edit this team" onclick="event.preventDefault()">{{ __('Edit') }}</a>
                            <a href="#" class="btn btn-secondary" title="You can't delete this team" onclick="event.preventDefault()">{{ __('Delete') }}</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        {{ $teams->links() }}
    </div>
@endsection
