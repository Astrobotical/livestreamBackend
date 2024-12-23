<?php

namespace App\Http\Controllers\api\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Stream;
use App\Http\Resources\UserResource;
use App\Models\Tag;

class Tags extends Controller
{
        // Fetch all tags or search tags based on query
        public function searchTags(Request $request)
        {
            $query = $request->get('query', '');
            
            $tags = Tag::where('name', 'like', "%$query%")
                        ->get()
                        ->pluck('name');
            
            return response()->json(['tags' => $tags]);
        }
        public function getTags()
        {
            $tags = Tag::all(); // Fetch all tags from the database
        
            return response()->json(['tags' => $tags], 200);
        }
        // Add new tag to the catalog
        public function addTag(Request $request)
        {
            $request->validate([
                'name' => 'required|string|unique:tags,name|max:255',
            ]);
    
            $tag = Tag::create([
                'name' => $request->input('name'),
            ]);
    
            return response()->json(['tag' => $tag], 201);
        }
    
        // Add tags to a user
        public function addTagsToUser(Request $request, $userId)
        {
            $user = User::findOrFail($userId);
            
            // Validate tags
            $tags = $request->input('tags');
            
            // Ensure the tags exist in the catalog
            foreach ($tags as $tag) {
                if (!Tag::where('name', $tag)->exists()) {
                    // Optionally, add the tag if it doesn't exist
                    Tag::create(['name' => $tag]);
                }
            }
    
            // Sync tags to the user
            $user->tags()->sync(Tag::whereIn('name', $tags)->get()->pluck('id'));
    
            return response()->json(['message' => 'Tags updated successfully!']);
        }
    }
