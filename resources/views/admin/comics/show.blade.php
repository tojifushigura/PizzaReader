@extends('admin.comics.information', ['fields' => \App\Models\Comic::getFormFields(), 'is_chapter' => false])
@section('card-title', __('Information about this comic'))
@section('reader_url', asset(substr($comic->url, 1)))
@section('destroy-message', __('Do you want to delete this comic and its relative chapters?'))
@section('form-action', route('admin.comics.destroy', $comic->id))
@section('list-title', __('Chapters'))
@section('list-buttons')
    <a href="{{ route('admin.comics.chapters.create', $comic->slug) }}" class="btn btn-success ms-3">{{ __('Add chapter') }}</a>
@endsection
@section('list')
    <div class="list">
        @foreach($chapters as $chapter)
            <div class="item row">
                <div class="col">
                    <h5 class="mb-0">
                        <a href="{{ route('admin.comics.chapters.show', ['comic' => $comic->slug, 'chapter' => $chapter->id]) }}"
                            class="filter">
                            {{ \App\Models\Chapter::name($comic, $chapter) }}
                        </a>
                    </h5>
                    <span class="small">
                        @if(Auth::user()->hasPermission("manager"))
                            <a href="{{ route('admin.comics.chapters.destroy', ['comic' => $comic->id, 'chapter' => $chapter->id]) }}"
                                data-bs-toggle="modal" data-bs-target="#modal-container" data-description="{{ __('Do you want to delete this chapter and its relative pages?') }}" data-form="destroy-chapter-form-{{ $chapter->id }}">{{ __('Delete chapter') }}</a>
                            <form id="destroy-chapter-form-{{ $chapter->id }}" action="{{ route('admin.comics.chapters.destroy', ['comic' => $comic->id, 'chapter' => $chapter->id]) }}"
                                  method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                            <span class="spacer">|</span>
                        @endif
                        <a href="{{ asset(substr(\App\Models\Chapter::getUrl($comic, $chapter), 1)) }}">{{ __('Read') }}</a>
                    </span>
                </div>
                <span class="rounded flag flag-{{ $chapter->language }}"></span>
            </div>
        @endforeach
    </div>
@endsection
