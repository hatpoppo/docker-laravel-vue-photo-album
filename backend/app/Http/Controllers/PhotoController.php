<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StorePhoto;
use App\Models\Photo;
use App\Models\User;
use App\Models\Comment;

class PhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index','download','show']);
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

        Storage::disk('public')
        ->putFileAs('', $request->photo, $photo->filename);
        DB::beginTransaction();
        try {
            Auth::user()->photos()->save($photo);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            Storage::disk('public')->delete($photo->filename);
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
    /**
     * 写真ダウンロード
     * @param Photo $photo
     * @retun Illuminate\Httep\Response
     */
    public function download(Photo $photo)
    {
        if (!Storage::disk('public')->exists($photo->filename)) {
            abort(404);
        }

        $disposition = 'attachment; filename="' . $photo->filename . '"';
        $header = [
            'Content-Type'=>'application/octet-stream',
            'Content-Disposition'=>$disposition,
        ];
        
        return response(Storage::disk('public')->get($photo->filename), 200, $header);
    }
    /**
     * 写真詳細
     * @param string $id
     * @return Photo
     */
    public function show(string $id)
    {
        $photo=Photo::where('id', $id)->with(['owner','comments.author'])->first();
        return $photo ?? abort(404);
    }
    /**
     * コメント投稿
     * @param Photo $photo
     * @param StoreComment $request
     * @return \Illumitate\Http\Response
     */
    public function addComment(Photo $photo, StoreComment $request)
    {
        $comment = new Comment();
        $comment->content = $request->get('content');
        $comment->user_id = Auth::user()->id;
        $photo->comments()->save($comment);

        $new_comment = Comment::where('id', $comment->id)->with('author')->first();
        return response($new_comment, 201);
    }
    /**
     * いいね
     * @params string $id
     * @return array
     */
    public function like(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (!$photo) {
            abort(404);
        }
        // $photo->likes()->dump();
        $photo->likes()->detach(Auth::user()->id);
        $photo->likes()->attach(Auth::user()->id);
        // $photo->likes()->dump();

        return ["photo_id"=>$id];
    }
    /**
     * いいね解除
     * @param string $id
     * @return array
     */
    public function unlike(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (!$photo) {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);

        return ["photo_id"=>$id];
    }
}
