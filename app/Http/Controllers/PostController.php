<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\ReactionRequest;

class PostController extends Controller
{
    public function list()
    {
        $posts = Post::get();
        
        $data = collect();
        foreach ($posts as $post) {
            $data->add([
                'id'          => $post->id,
                'title'       => $post->title,
                'description' => $post->description,
                'tags'        => $post->tags,
                'like_counts' => $post->likes->count(),
                'created_at'  => $post->created_at,
            ]);
        }
        return response()->json([
            'data' => $data,
        ]);
    }
    
    public function toggleReaction(ReactionRequest $request)
    {                
        $responseOwnPost = $this->checkOwnPost($request);
        $responseExist = $this->checkAlreadyExist($request);

        if($responseOwnPost || $responseExist)
        {
            return response()->json($responseOwnPost ?? $responseExist);
        }
        
        $responseCreate = $this->createReaction($request);
        return response()->json($responseCreate);
    }

    public function checkOwnPost($request)
    {
        $post = Post::find($request->post_id);
        
        if($post->author_id == auth()->id()) {
            return [
                'status' => 500,
                'message' => 'You cannot like your post'
            ];
        }
    }

    public function checkAlreadyExist($request)
    {
        $like = Like::where('post_id', $request->post_id)->where('user_id', auth()->id())->first();
        
        if($like && $request->like) {
            return [
                'status' => 500,
                'message' => 'You already liked this post'
            ];
        }elseif($like && !$request->like) {
            $like->delete();
            
            return [
                'status' => 200,
                'message' => 'You unlike this post successfully'
            ];
        }
    }

    public function createReaction($request)
    {
        Like::create([
            'post_id' => $request->post_id,
            'user_id' => auth()->id()
        ]);
        
        return [
            'status' => 200,
            'message' => 'You like this post successfully'
        ];
    }
}
