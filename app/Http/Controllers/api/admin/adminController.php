<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Stream;
use App\Http\Resources\UserResource;
use App\Models\Tag;
use Carbon\Carbon;
use App\Events\StreamStarted;
use App\Events\StreamEnded;

class adminController extends Controller
{
	/**
	 * Get all users.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getAllUsers()
	{
		$users = User::with('tags')->get(); // Eager load tags
		return response()->json(UserResource::collection($users), 200, ['Content-Type' => 'application/json']);
		//return response()->json($users,200,['Content-Type' => 'application/json']);
	}
	public function addTagsToUser(Request $request, $userId)
	{
		$request->validate([
			'tags' => 'required|array',  // Ensure we receive an array of tags
			'tags.*' => 'exists:tags,id', // Ensure each tag exists in the tags table
		]);

		$user = User::findOrFail($userId); // Find the user by ID

		// Attach the tags to the user (many-to-many relation)
		$user->tags()->syncWithoutDetaching($request->input('tags'));

		return response()->json(['message' => 'Tags added successfully'], 200, ['Content-Type' => 'application/json']);
	}
	public function searchTags(Request $request)
	{
		$request->validate([
			'query' => 'required|string|min:1|max:255',
		]);

		$query = $request->input('query');

		// Fetch tags that match the query
		$tags = Tag::where('name', 'like', '%' . $query . '%')->get();

		return response()->json(['tags' => $tags], 200, ['Content-Type' => 'application/json']);
	}
	/**
	 * Schedule a new stream.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function updateUser(Request $request, $userId)
{
    // Validate the incoming data
    $request->validate([
        'role' => 'required|in:admin,viewer', // You mentioned only admin or viewer roles
        'tags' => 'array', // Tags should be an array
        'tags.*' => 'string|exists:tags,name', // Tags must exist in the Tag model
    ]);

    // Find the user by userId
    $user = User::findOrFail($userId); // This will throw a 404 if user not found

    // Update the user's role
    $user->Role = $request->input('role');
    
    // Save the updated user
    $user->save();

    // Update user's tags
    $tags = $request->input('tags', []);
    
    // Ensure the tags exist in the catalog, otherwise, create them
    foreach ($tags as $tag) {
        if (!Tag::where('name', $tag)->exists()) {
            // Optionally, you can create tags that don't exist in the catalog
            Tag::create(['name' => $tag]);
        }
    }

    // Sync the tags to the user (many-to-many relationship)
    $user->tags()->sync(Tag::whereIn('name', $tags)->get()->pluck('id'));

    return response()->json(['message' => 'User updated successfully!', 'user' => $user], 200);
}

// In your StreamController.php

public function startStream($id)
{
    // Find the stream by ID
	$stream = Stream::findOrFail($id);

    if (!$stream) {
        return response()->json([
            'status' => 'error',
            'message' => 'Stream not found'
        ], 404);
    }

    // Ensure that the stream is in the future
    if (Carbon::parse($stream->stream_time)->isPast()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Stream cannot be started as it is in the past'
        ], 400);
    }

    // Mark the stream as started
    $stream->update([ 
        'is_live' => 1,  // Assuming is_live column indicates if the stream is active
    ]);
	broadcast(new StreamStarted($stream));
    return response()->json([
        'status' => 'success',
        //'stream' => $stream
    ], 200);
}

public function endStream($id)
{
	// Find the stream by ID
	$stream = Stream::find($id);

	if (!$stream) {
		return response()->json(['message' => 'Stream not found'], 404);
	}

	// Check if the stream is currently live
	if ($stream->is_live == 0) {
		return response()->json(['message' => 'Stream is not live'], 400);
	}

	// Update the stream status to "ended"
	$stream->stream_status = 'passed';
	$stream->is_live =  0;
	$stream->save();

	// Broadcast the stream ended event
	broadcast(new StreamEnded($stream));

	return response()->json(['message' => 'Stream ended successfully', 'stream' => $stream], 200);
}
	public function scheduleStream(Request $request)
	{
		$request->validate([
			'title' => 'required|string|max:255',
			'description' => 'required|string',
			'stream_date' => 'required|date',
			'stream_time' => 'string',
			'stream_url' => 'string',
		]);
		$stream = new Stream();
		$stream->title = $request->input('title');
		$stream->description = $request->input('description');
		$stream->stream_url = $request->input('stream_url');
		$stream->stream_time = $request->input('stream_time');
		$stream->stream_date = $request->input('stream_date');
		$stream->save();

		return response()->json(['message' => 'Stream scheduled successfully', 'stream' => $stream], 200, ['Content-Type' => 'application/json']);
	}
}
