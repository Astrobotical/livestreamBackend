<?php
namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Stream;
use Carbon\Carbon; // 
class livestreamController extends Controller{
    // Display a listing of the livestreams
    public function index(Request $request)
    {
        $filter = $request->query('filter',null);
        switch($filter){
            case 'Future':
                $streams = Stream::where('stream_time', '>', now())->get();
               return response()->json($streams, 200,['Content-Type' => 'application/json']);
            case 'Past':
                $streams = Stream::where('stream_time', '<', now())->get();
                return response()->json($streams, 200,['Content-Type' => 'application/json']);
            default:
                    // Fetch all livestreams
        $livestreams = Stream::all();
        return response()->json($livestreams, 200,['Content-Type' => 'application/json']);
        }

    }
    public function getNextStream(Request $request){
        $today = Carbon::now()->format('Y-m-d');

    // Query the streams for today's date (assuming 'start_time' or 'date' column exists)
    $nextStream = Stream::whereDate('stream_time', $today) // Or 'date' if you use a 'date' column
        ->orderBy('stream_time', 'asc') // If there are multiple streams, get the next one
        ->first(); // Get the first stream of the day

    // If there is a stream today, return it. Otherwise, return a 'no stream' message
    if ($nextStream) {
        return response()->json([
            'status' => 'success',
            'stream' => $nextStream
        ],200,['Content-Type' => 'application/json']);
    } else {
        return response()->json([
            'status' => 'no stream found',
            'message' => 'No streams scheduled for today.'
        ],  200,['Content-Type' => 'application/json']);
    }
    }

    // Show the form for creating a new livestream
    public function create()
    {
        // You would typically return a view here for form submission (for a web app)
        // But in an API, you don't need a create method
    }

    // Store a newly created livestream in the database
    public function store(Request $request)
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

    // Display the specified livestream
    public function show($id)
    {
        // Find the livestream by ID
        $livestream = Stream::findOrFail($id);

        return response()->json($livestream, 200);
    }
    // Show the form for editing the specified livestream
    public function edit($id)
    {
        // Similar to create, typically used for web apps to show an edit form
        // In an API, you don't need this
    }

    // Update the specified livestream in the database
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'url' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:50',
        ]);

        // Find the livestream by ID
        $livestream = Stream::findOrFail($id);

        // Update the livestream
        $livestream->update($request->all());

        return response()->json($livestream, 200);
    }

    // Remove the specified livestream from the database
    public function destroy($id)
    {
        // Find the livestream by ID
        $livestream = Stream::findOrFail($id);

        // Delete the livestream
        $livestream->delete();

        return response()->json(['message' => 'Livestream deleted successfully'], 200);
    }
  
}