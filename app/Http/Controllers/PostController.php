<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;

class PostController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $posts = Post::orderBy('id', 'desc')->get();
    
    return response()->json(['status' => true, 'data' => $posts], 200);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    // Validate user role if able to create a post
    if (auth()->user()->role === 'viewer') {
      return response()->json(['status' => false, 'message' => 'Usuario no autorizado para crear post']);
    }

    $data = array();
    $data['content'] = $request->input('content');

    // Validate data
    $validator = Validator::make($data, 
      ['content' => 'required'], 
      ['name.required'  => 'El campo POST es obligatorio.',]
    );  
    if ($validator->fails()) {
      return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
    } 
    $data['user_id'] = auth()->user()->id;

    try {
      $post = Post::create($data);

      return response()->json(['status' => true, 'data' => $post, 'message' => 'Registro correcto.']);

    } catch (Exception $e) {
      return response()->json(['status' => false, 'message' => $e->getMessage()]);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Post  $post
   * @return \Illuminate\Http\Response
   */
  public function show(Post $post)
  {
      //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Post  $post
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Post $post)
  {
    // Validate user role if able to update a post
    if (auth()->user()->role === 'viewer') {
      return response()->json(['status' => false, 'message' => 'Usuario no autorizado para actualizar post']);
    }
    // Validate if user role is standard to update own post 
    if (auth()->user()->role === 'standard' && $post->user_id !== auth()->user()->id) {
      return response()->json(['status' => false, 'message' => 'Usuario no autorizado para actualizar este post']);
    }

    $data = array();
    $data['content'] = $request->input('content');

    // Validate data
    $validator = Validator::make($data, 
      ['content' => 'required'], 
      ['name.required'  => 'El campo POST es obligatorio.',]
    );  
    if ($validator->fails()) {
      return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
    }

    try {
      $post->update($data);

      return response()->json(['status' => true, 'data' => $post, 'message' => 'Actualización correcta.']);

    } catch (Exception $e) {
      return response()->json(['status' => false, 'message' => $e->getMessage()]);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Post  $post
   * @return \Illuminate\Http\Response
   */
  public function destroy(Post $post)
  {
    // Validate user role if able to delete a post
    if (auth()->user()->role === 'viewer') {
      return response()->json(['status' => false, 'message' => 'Usuario no autorizado para eliminar post']);
    }
    // Validate if user role is standard to delete own post 
    if (auth()->user()->role === 'standard' && $post->user_id !== auth()->user()->id) {
      return response()->json(['status' => false, 'message' => 'Usuario no autorizado para eliminar este post']);
    }

    try {
      $post->delete();

      return response()->json(['status' => true, 'message' => 'Eliminación correcta.']);

    } catch (Exception $e) {
      return response()->json(['status' => false, 'message' => $e->getMessage()]);
    }
  }
}
