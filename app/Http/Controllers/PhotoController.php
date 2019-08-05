<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\StoreComment;
use App\Http\Requests\StorePhoto;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function __construct()
    {
        // 登录验证，排除index方法
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index()
    {
        $photos = Photo::with(['owner'])
            ->orderBy('likes_count', 'desc')
            ->orderBy(Photo::CREATED_AT, 'desc')
            ->paginate(6);

        return $photos;
    }

    public function show(string $id)
    {
        $photo = Photo::where('id', $id)
            ->with(['owner', 'comments.author', 'likes'])->first();

        return $photo ?? abort(404);
    }

    public function create(StorePhoto $request)
    {
        $user = Auth::user();
        $max = env('USER_COUNT', '5');

        if($user->count > $max && $user->role !== 'admin'){
            $err = ['errors' => ['photo' => ['只能上传'.$max.'张图片']]];
            return response()->json($err, 422);
        }

        $extension = $request->photo->extension();

        $size = $request->photo->getSize();

        if($size > 1024*1024*2){
            $err = ['errors' => ['photo' => ['只能上传不超过2M的文件']]];
            return response()->json($err, 422);
        }

        $photo = new Photo();

        $id = $photo->id;

        $filename = $photo->id . '.' . $extension;

        $path = 'photos/';

        $photo->filename = $path .$photo->id . '.' . $extension;

        Storage::cloud()->putFileAs($path, $request->photo, $filename);

        DB::beginTransaction();

        try {
            $user->photos()->save($photo);
            $user->increment('count');
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Storage::cloud()->delete($photo->filename);
            throw $exception;
        }

        return response(['id'=> $id], 201);
    }

    public function download(Photo $photo)
    {
        // 判段文件是否存在
        if (!Storage::cloud()->exists($photo->filename)) {
            abort(404);
        }

        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $photo->filename . '"',
        ];

        return response(Storage::cloud()->get($photo->filename), 200, $headers);
    }

    public function delete(string $id)
    {
        $photo = Photo::with('owner:id,role')->find($id);
        $user = Auth::user();

        if($user->role == 'admin' || $photo->owner->id == $user->id){
            // 判段文件是否存在
            if (!Storage::cloud()->exists($photo->filename)) {
                abort(404);
            }
            DB::beginTransaction();
            try {
                if(Storage::cloud()->delete($photo->filename)){
                    $photo->delete();
                }
                if($photo->owner->count > 0){
                    $user->decrement('count');
                }
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                throw $exception;
            }
        }else{
            $result = [
                'message' => '您暂无权限删除！'
            ];

            return response()->json($result, 422);
        }

        return ["photo_id" => $photo->id];
    }

    public function addComment(Photo $photo, StoreComment $request)
    {
        $comment = new Comment();
        $comment->content = $request->get('content');
        $comment->user_id = Auth::user()->id;
        $photo->comments()->save($comment);

        $new_comment = Comment::where('id', $comment->id)->with('author')->first();

        return response($new_comment, 201);
    }

    public function like(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (! $photo) {
            abort(404);
        }

        $photo->likes()->attach(Auth::user()->id);
        $photo->increment('likes_count');

        return ["photo_id" => $id];
    }

    public function unlike(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (! $photo) {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);
        $photo->decrement('likes_count');

        return ["photo_id" => $id];
    }
}
