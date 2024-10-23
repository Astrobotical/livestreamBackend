<?php

namespace App\Http\Controllers\api\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Stream;

class adminController extends Controller
{
	/**
	 * Get all users.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getAllUsers()
	{
		$users = User::all();
		return response()->json($users,200,['Content-Type' => 'application/json']);
	}

	/**
	 * Schedule a new stream.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function scheduleStream(Request $request)
	{
		$request->validate([
			'title' => 'required|string|max:255',
			'description' => 'required|string',
			'stream_date' => 'required|date',
			'stream_time'=> 'string',
			'stream_url'=>'string',
		]);
		$stream = new Stream();
		$stream->title = $request->input('title');
		$stream->description = $request->input('description');
		$stream->stream_url = $request->input('stream_url');
		$stream->stream_time = $request->input('stream_time');
		$stream->stream_date = $request->input('stream_date');
		$stream->save();

		return response()->json(['message' => 'Stream scheduled successfully', 'stream' => $stream],200,['Content-Type' => 'application/json']);
	}
}