<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StorePhoto;
use App\Models\Photo;
use App\Models\User;

class PhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * 写真投稿
     * @param StorePhoto $request
     * @return \Illuminate\Http\Response
     */
    public function create(StorePhoto $request)
    {
        $extension = $request->photo->extension();
        $photo = new Photo();

        $photo->filename = $photo->id . '.' . $extension;

        Storage::disk('local')
        ->put($photo->filename, $request->photo);

        DB::beginTransaction();
        try {
            Auth::user()->photos()->save($photo);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            Storage::disk('local')->delete($photo->filename);
            throw $exception;
        }
        return response($photo, 201);
    }
    /**
     * 写真一覧
     */
    public function index()
    {
        $photos = Photo::with(['owner'])
            ->orderBy(Photo::CREATED_AT, 'desc')->paginate();
        
        return $photos;
    }
}
