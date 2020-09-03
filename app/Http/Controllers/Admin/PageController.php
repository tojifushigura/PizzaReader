<?php

namespace App\Http\Controllers\Admin;

use App\Comic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Chapter;
use App\Page;

class PageController extends Controller {

    public function index($comic_slug, $chapter_id) {
        $comic = Comic::slug($comic_slug);
        if (!$comic) {
            abort(404);
        }
        $chapter = Chapter::find($chapter_id);
        if (!$chapter || $chapter->comic_id !== $comic->id) {
            abort(404);
        }

        return response()->json(Chapter::getAllPagesUrls($comic, $chapter));
    }

    public function store(Request $request, $comic_slug, $chapter_id) {
        $comic = Comic::slug($comic_slug);
        if (!$comic) {
            abort(404);
        }
        $chapter = Chapter::find($chapter_id);
        if (!$chapter || $chapter->comic_id !== $comic->id) {
            abort(404);
        }
        $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240']
        ]);

        $response = ["files" => []];
        $path = Chapter::absolutePath($comic, $chapter);
        foreach ($request->file('files') as $file) {
            $filename = $path . '/' . $file->getClientOriginalName();
            $file->storeAs(Chapter::path($comic, $chapter), $file->getClientOriginalName());
            $dimension = getimagesize($filename);
            $size = filesize($filename);

            Page::where([['chapter_id', $chapter->id], ['filename', $file->getClientOriginalName()]])->delete();
            $page = Page::create([
                'chapter_id' => $chapter->id,
                'filename' => $file->getClientOriginalName(),
                'size' => $size,
                'width' => $dimension[0],
                'height' => $dimension[1],
                'mime' => $file->getClientMimeType(),
                'hidden' => 0,
            ]);
            $page->url = Page::getUrl($comic, $chapter, $page);
            array_push($response['files'], [
                'name' => $page->filename,
                'size' => $page->size,
                'url' => $page->url,
                'thumbnailUrl' => $page->url,
                'deleteUrl' => route('admin.comics.chapters.pages.destroy', ['comic' => $comic->id, 'chapter' => $chapter->id, 'page' => $page->id]),
                'deleteType' => 'DELETE'
            ]);
        }
        return response()->json($response);
    }

    public function destroy($comic_id, $chapter_id, $page_id) {
        $comic = Comic::find($comic_id);
        if (!$comic) {
            abort(404);
        }
        $chapter = Chapter::find($chapter_id);
        if (!$chapter || $chapter->comic_id !== $comic->id) {
            abort(404);
        }
        $page = Page::find($page_id);
        if (!$page || $page->chapter_id !== $chapter->id) {
            abort(404);
        }
        Storage::delete(Chapter::path($comic, $chapter) . '/' . $page->filename);
        Page::destroy($page_id);
        return response()->json(["message" => "success"]);
    }
}
