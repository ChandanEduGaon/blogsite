<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
        $this->middleware('checkuserrole', [
            'only' => [
                'posts'
            ]
        ]);
    }


    public function create_post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "msg" => "Missing required fields",
                "status" => false,
                "data" => []
            ]);
        }
        $activeUser = auth()->user()->id;

        $post = [
            'title' => $request->title,
            'content' => $request->content,
            'author' => $activeUser,
        ];

        if ($request->blog_id) {

            $prevBlog = DB::table('posts')->where('id', $request->blog_id)->first();

            if (!isset($prevBlog)) {
                return response()->json([
                    "msg" => "Post not found",
                    "status" => false,
                    "data" => []
                ]);
            }

            if ($activeUser !== (int)$prevBlog->author) {
                return response()->json([
                    "msg" => "Only author can edit this post",
                    "status" => false,
                    "data" => []
                ]);
            }

            $lastId = $request->blog_id;
            DB::table('posts')->update($post);
            $msg = "updated";
        } else {
            $lastId = DB::table('posts')->insertGetId($post);
            $msg = "created";
        }

        $data = DB::table('posts')->where('id', $lastId)->first();

        return response()->json([
            "msg" => "Post " . $msg . " successfully",
            "status" => true,
            "data" => $data
        ]);
    }

    public function posts()
    {
        $posts = DB::table('posts')->where('approval_status', 'approved')->get();

        if (count($posts) > 0) {
            return response()->json([
                "msg" => "Posts fetched successfully",
                "status" => true,
                "data" => $posts
            ]);
        }

        return response()->json([
            "msg" => "Post not found",
            "status" => false,
            "data" => []
        ]);
    }

    public function post_details($id)
    {
        $post = DB::table('posts')->where('id', $id)->first();

        if (isset($post)) {
            return response()->json([
                "msg" => "Posts fetched successfully",
                "status" => true,
                "data" => $post
            ]);
        }

        return response()->json([
            "msg" => "Post not found",
            "status" => false,
            "data" => []
        ]);
    }

    public function post_delete($id)
    {
        $activeUser = auth()->user()->id;
        $post = DB::table('posts')->where('id', $id)->first();



        if (isset($post)) {
            if ($activeUser !== (int)$post->author) {
                return response()->json([
                    "msg" => "Only author can delete this post",
                    "status" => false,
                    "data" => []
                ]);
            }

            DB::table('posts')->where('id', $id)->delete();

            return response()->json([
                "msg" => "Posts deleted successfully",
                "status" => true,
                "data" => $post
            ]);
        }

        return response()->json([
            "msg" => "Post not found",
            "status" => false,
            "data" => []
        ]);
    }
    public function post_approve($id)
    {
        $activeUser = auth()->user();
        $post = DB::table('posts')->where('id', $id)->first();

        if (isset($post)) {
            if ($activeUser->role !== 'admin') {
                return response()->json([
                    "msg" => "Only admin can approve this post",
                    "status" => false,
                    "data" => []
                ]);
            }

            DB::table('posts')->where('id', $id)->update(['approval_status' => 'approved']);
            $post = DB::table('posts')->where('id', $id)->first();
            return response()->json([
                "msg" => "Posts approved successfully",
                "status" => true,
                "data" => $post
            ]);
        }

        return response()->json([
            "msg" => "Post not found",
            "status" => false,
            "data" => []
        ]);
    }
}
