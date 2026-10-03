<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\PetResource;

class PetResourceController extends Controller {

    public function index(Request $request) {
        $query = PetResource::with(['author.shelterStaffProfile']);

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('category')) {
            $query->ofCategory($request->input('category'));
        }

        if ($request->filled('type')) {
            $query->ofType($request->input('type'));
        }

        $resources = $query->orderBy('created_at', 'desc')->paginate(9);

        return view('Adopter.resources.index', compact('resources'));
    }

    public function manage() {
        $resources = PetResource::orderBy('created_at', 'desc')->paginate(10);
        return view('Shelterstaff.resources.manage', compact('resources'));
    }

    public function create() {
        return view('Shelterstaff.resources.create');
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:article,video',
            'content' => 'required|string',
            'category' => 'required|in:pet_care,food_safety,training',
            'image.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        $imagePaths = [];

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $imagePaths[] = $image->store('pet_resources', 'public');
            }
        }

        PetResource::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'content' => $request->content,
            'category' => $request->category,
            'created_by' => Auth::id(),
            'image_paths' => json_encode($imagePaths),
        ]);

        return redirect()->route('Shelter.resources.manage')->with('success', 'Resource added successfully.');
    }

    public function edit($id) {
        $resource = PetResource::findOrFail($id);
        return view('Shelterstaff.resources.edit', compact('resource'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'title' => 'required|string|max:255',
        'type' => 'required|in:article,video',
        'content' => 'required|string',
        'category' => 'required|in:pet_care,food_safety,training',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
    ]);

    $resource = PetResource::findOrFail($id);
    $storedImages = is_array($resource->image_paths) 
        ? $resource->image_paths 
        : json_decode($resource->image_paths, true) ?? [];
   
    if ($request->has('delete_images')) {
        foreach ($request->delete_images as $imageToDelete) {
            Storage::delete('public/' . $imageToDelete);
            
            $storedImages = array_filter($storedImages, function($img) use ($imageToDelete) {
                return $img !== $imageToDelete;
            });
        }
    }
    
    if ($request->hasFile('image')) {
        $newImagePath = $request->file('image')->store('pet_resources', 'public');
        $storedImages[] = $newImagePath;
    }

    $resource->update([
        'title' => $request->title,
        'type' => $request->type,
        'content' => $request->content,
        'category' => $request->category,
        'image_paths' => json_encode($storedImages),
    ]);

    return redirect()->route('shelter.resources.manage')->with('success', 'Resource updated successfully.');
}

    public function destroy($id) {
        PetResource::findOrFail($id)->delete();
        return redirect()->route('shelter.resources.manage')->with('success', 'Resource deleted.');
    }

    public function show($id) {
        $resource = PetResource::findOrFail($id);
        return view('Adopter.resources.show', compact('resource'));
    }
}
