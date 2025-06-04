<?php

namespace App\Http\Controllers\Reader;

use App\Models\ChapterDownload;
use App\Models\ChapterPdf;
use App\Models\Comic;
use App\Models\Settings;
use App\Models\Chapter;
use App\Models\Page;
use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\View;
use App\Models\VolumeDownload;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReaderController extends Controller {

    public function info(): JsonResponse {
        $response = ['socials' => []];
        $socials = Settings::getSocials();

        $n = strlen('social_');
        foreach($socials as $social) {
            $response['socials'][] = [
                'name' => str_replace('_', ' ', ucfirst(substr($social->key, $n))),
                'url' => $social->value
            ];
        }
        return response()->json(["info" => $response]);
    }

    public function comics(): JsonResponse {
        return response()->json($this->getComics('name'));
    }

    public function recommended(): JsonResponse {
        return response()->json($this->getComics('order_index'));
    }

    public function getComics($ord): array {
        $response = ['comics' => []];

        $comics = Comic::public($ord)->get();
        foreach ($comics as $comic) {
            $response['comics'][] = Comic::generateReaderArray($comic);
        }
        return $response;
    }

    public function search($search): JsonResponse {
        $response = ['comics' => []];
        $comics = Comic::publicSearch($search);
        foreach ($comics as $comic) {
            $response['comics'][] = Comic::generateReaderArray($comic);
        }
        return response()->json($response);
    }

    public function targets($target): JsonResponse {
        $response = ['comics' => []];
        $comics = Comic::publicSearch($target, 'target');
        foreach ($comics as $comic) {
            $response['comics'][] = Comic::generateReaderArray($comic);
        }
        return response()->json($response);
    }

    public function genres($genre): JsonResponse {
        $response = ['comics' => []];
        $comics = Comic::publicSearch($genre, 'genres');
        foreach ($comics as $comic) {
            $response['comics'][] = Comic::generateReaderArray($comic);
        }
        return response()->json($response);
    }

    public function comic($comic_slug): JsonResponse {
        $comic = Comic::publicSlug($comic_slug);
        $show_licensed = filter_var(request()->query('licensed'), FILTER_VALIDATE_BOOLEAN);
        return response()->json(['comic' => Comic::generateReaderArrayWithChapters($comic, $show_licensed)], $comic ? 200 : 404);
    }

    public function chapter($comic_slug, $language, $ch = null): JsonResponse {
        $response = ['comic' => null, 'chapter' => null];
        $ch = $this->explodeCh($language, $ch);
        if (!$ch) return response()->json($response, 404);

        $comic = Comic::publicSlug($comic_slug);
        if (!$comic) return response()->json($response, 404);
        $response['comic'] = Comic::generateReaderArrayWithChapters($comic);

        $chapter = Chapter::publicFilterByCh($comic, $ch);
        if (!$chapter) return response()->json($response, 404);

        if(!$chapter->licensed) {
            View::incrementIfNew($chapter, request()->ip());
        }

        $response['chapter'] = Chapter::generateReaderArray($comic, $chapter);
        $response['chapter']['pages'] = Chapter::isLicensed($chapter) ? [asset('img/404.gif')] : Page::getAllPagesForReader($comic, $chapter);

        $previous_chapter = null;
        $next_chapter = null;
        $last = null;
        $can_break = false;
        // Previous and Next chapter should be in the same language
        foreach ($comic->publicChapters()->where('language', $chapter->language)->get() as $c) {
            if ($can_break) {
                $previous_chapter = $c;
                break;
            }
            if ($c->id === $chapter->id) {
                $next_chapter = $last;
                $can_break = true;
            }
            $last = $c;
        }
        $response['chapter']['vote_id'] = $chapter->id;
        $response['chapter']['previous'] = Chapter::generateReaderArray($comic, $previous_chapter);
        $response['chapter']['next'] = Chapter::generateReaderArray($comic, $next_chapter);

        return response()->json($response);
    }

    public function download($comic_slug, $language, $ch = null): StreamedResponse {
        $ch = $this->explodeCh($language, $ch);
        if (!$ch) abort(404);

        $comic = Comic::publicSlug($comic_slug);
        if (!$comic) {
            abort(404);
        }

        $chapter = Chapter::publicFilterByCh($comic, $ch);
        if (!$chapter) {
            if ($ch['vol'] === null || $ch['ch'] !== null || $ch['sub'] !== null) {
                abort(404);
            }
            // Volume download is only for real publicChapters
            $chapters = $comic->chapters()->where([
                ['language', $language],
                ['volume', $ch['vol']],
                ['licensed', 0],
            ])->published()->get();
            if ($chapters->isEmpty()) {
                abort(404);
            }
            if (!Chapter::canVolumeDownload($comic)) {
                abort(403);
            }
            $download = VolumeDownload::getDownload($comic, $language, $ch['vol']);
            if (!$download) {
                abort(507, "A chapter is empty or the server has no space left. Try again later or report this error to us");
            }
            return Storage::download($download['path'], $download['name']);
        }
        if (!Chapter::canChapterDownload($chapter)) {
            abort(403);
        }
        $download = ChapterDownload::getDownload($comic, $chapter);
        if (!$download) {
            abort(507, "The chapter is empty or the server has no space left. Try again later or report this error to us");
        }
        return Storage::download($download['path'], $download['name']);
    }

    public function pdf($comic_slug, $language, $ch = null): StreamedResponse {
        $ch = $this->explodeCh($language, $ch);
        if (!$ch) abort(404);

        $comic = Comic::publicSlug($comic_slug);
        if (!$comic) {
            abort(404);
        }
        $chapter = Chapter::publicFilterByCh($comic, $ch);
        if (!$chapter){
            abort(404);
        }

        if(!Chapter::canChapterPdf($chapter)) {
            abort(403);
        }
        $pdf = ChapterPdf::getPdf($comic, $chapter);
        if (!$pdf) {
            abort(507, "The chapter is empty or the server has no space left. Try again later or report this error to us");
        }
        return Storage::download($pdf['path'], $pdf['name']);
    }

    /**
     * @throws \Exception
     */
    public function get_vote($chapter_id): JsonResponse {
        $your_vote = Rating::where([['chapter_id', $chapter_id], ['ip', request()->ip()]])->first();
        if (!isset($_COOKIE['vote_token'])) {
            $vote_token = bin2hex(random_bytes(32));
            setcookie('vote_token', $vote_token, array('expires' => time() + 36000, 'path' => '/', 'samesite' => 'lax'));
        } else {
            $vote_token = $_COOKIE['vote_token'];
        }
        $response = [
            'vote_id' => $chapter_id,
            'vote_token' => $vote_token,
            'your_vote' => $your_vote ? $your_vote->rating : null
        ];
        // If the cache is enabled maybe you need to use this not-cached endpoint to increment views
        if (config('settings.cache_proxy_enabled')) {
            $chapter = Chapter::find($chapter_id);
            if ($chapter) View::incrementIfNew($chapter, request()->ip());
        }
        return response()->json($response);
    }

    public function vote(Request $request, $comic_slug, $language, $ch = null): JsonResponse {
        $request->validate([
            'vote' => ['integer', 'min:1', 'max:5', 'required'],
        ]);
        if (!isset($_COOKIE['vote_token']) || $_COOKIE['vote_token'] !== $request->vote_token) response()->json(['error' => 'missing or invalid vote_token'], 403);
        $ch = $this->explodeCh($language, $ch);
        if (!$ch) response()->json(['error' => 'chapter not found'], 404);

        $comic = Comic::publicSlug($comic_slug);
        if (!$comic) response()->json(['error' => 'comic not found'], 404);

        $chapter = Chapter::publicFilterByCh($comic, $ch);
        if (!$chapter) response()->json(['error' => 'chapter not found'], 404);
        if (Chapter::isLicensed($chapter)) response()->json(['error' => 'chapter licensed'], 403);

        $sum_inc;
        $count_inc;
        $fallback = false;
        $old_rating = Rating::where([['chapter_id', $chapter->id], ['ip', request()->ip()]])->first();
        if ($old_rating === null) {
            $sum_inc = $request->vote;
            $count_inc = 1;
            Rating::create(['chapter_id' => $chapter->id, 'ip' => request()->ip(), 'rating' => $request->vote]);
        } else {
            $sum_inc = $request->vote - $old_rating->rating;
            $count_inc = 0;
            $old_rating->rating = $request->vote;
            $old_rating->save();
        }
        if ($chapter->rating_count) {
            // Don't care if "rating" is race-conditioned
            try {
                Chapter::where('id', $chapter->id)->update([
                    'rating_sum' => DB::raw('`rating_sum` + ' . $sum_inc),
                    'rating_count' => DB::raw('`rating_count` + ' . $count_inc),
                    'rating' => ($chapter->rating_sum+$sum_inc)/($chapter->rating_count+$count_inc) * 2,
                ]);
                $chapter->refresh();
            } catch (QueryException $ex) {
                $fallback = true;
            }
        } else {
            // If first view fallback
            $fallback = true;
        }
        
        if ($fallback) {
            // Calculate from rating table
            $ratings = $chapter->ratings()->selectRaw('COUNT(*) as rating_count, SUM(`rating`) as rating_sum')->first();
            if ($ratings->rating_count) {
                $chapter->timestamps = false;
                $chapter->rating_count = $ratings->rating_count;
                $chapter->rating_sum = $ratings->rating_sum;
                $chapter->rating = $ratings->rating_sum/$ratings->rating_count * 2;
                $chapter->save();
            }
            // After this it should be aligned
            // SELECT `pr_chapters`.`id` as chapter_id, `rating_sum`, `rating_count`, `pr_chapters`.`rating`, avg(`pr_ratings`.`rating`)*2 as real_avg FROM `pr_chapters` join `pr_ratings` on `pr_chapters`.`id` = `chapter_id` group by `chapter_id`
        }

        return response()->json(['rating' => $chapter->rating]);
    }

    static function explodeCh($language, $ch): ?array {
        if ($ch) {
            $ch = array_values(array_filter(explode("/", $ch), function ($value) {
                return $value !== '';
            }));
            $length = count($ch);
            if ($length % 2) {
                return null;
            }
            $temp = ['lang' => $language, 'vol' => null, 'ch' => null, 'sub' => null];
            for ($i = 0; $i < $length; $i += 2) {
                if (in_array($ch[$i], ['vol', 'ch', 'sub'], true)) $temp[$ch[$i]] = $ch[$i + 1];
            }
            $ch = $temp;
            unset($temp);
        } else {
            $ch = ['lang' => $language, 'vol' => null, 'ch' => null, 'sub' => null];
        }
        return $ch;
    }
}
