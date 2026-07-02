<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPosterRequest;
use App\Http\Requests\UpdateRotationRequest;
use App\Models\MentalHealthPoster;
use App\Models\PosterSetting;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DailyMentalCheckController extends Controller
{
    public function index()
    {
        return view('internal.daily-mental-check');
    }

    // Poster management — kept as JSON endpoints for Super Admin
    public function listPosters()
    {
        $posters = MentalHealthPoster::with('uploader')->latest()->get()
            ->map(fn($p) => [
                'id' => $p->id, 'url' => $p->url, 'is_active' => $p->is_active,
                'uploaded_by' => $p->uploader?->name, 'created_at' => $p->created_at->diffForHumans(),
            ]);
        return response()->json(['posters' => $posters, 'rotation' => PosterSetting::getRotation()]);
    }

    public function uploadPoster(UploadPosterRequest $request)
    {
        $path = app(ImageService::class)->compressAndStore($request->file('image'), 'posters', quality: 70);
        $poster = MentalHealthPoster::create([
            'image_path' => $path, 'is_active' => true, 'uploaded_by' => $request->user()->id,
        ]);
        return response()->json(['success' => true, 'poster' => ['id' => $poster->id, 'url' => $poster->url, 'created_at' => $poster->created_at->diffForHumans()]]);
    }

    public function deletePoster($id)
    {
        $poster = MentalHealthPoster::findOrFail($id);
        Storage::disk('public')->delete($poster->image_path);
        $poster->delete();
        return response()->json(['success' => true]);
    }

    public function updateRotation(UpdateRotationRequest $request)
    {
        PosterSetting::setRotation($request->rotation);
        return response()->json(['success' => true]);
    }
}
