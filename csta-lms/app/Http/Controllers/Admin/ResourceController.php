<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Resource;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Resource::with(['subject.schoolClass', 'uploader']);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('file_name', 'like', "%{$q}%");
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('resource_type')) {
            $query->where('resource_type', $request->resource_type);
        }

        $resources = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        $subjects = Subject::where('status', true)->orderBy('name')->get();

        return view('admin.resources.index', compact('resources', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'resource_type' => 'required|in:Course Syllabus,Lesson,Others',
            'description' => 'nullable|string',
            'file' => 'required|file|max:20480',
        ]);

        $file = $request->file('file');

        $resource = Resource::create([
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'resource_type' => $request->resource_type,
            'description' => $request->description,
            'file_path' => $file->store('resources', 'public'),
            'file_name' => $file->getClientOriginalName(),
            'file_type' => strtolower($file->getClientOriginalExtension()),
            'uploaded_by' => $request->user()->id,
        ]);

        AuditLog::record('Upload Resource', "Admin uploaded resource: {$resource->title}");

        return redirect()->route('admin.resources.index')->with('success', 'Resource uploaded successfully.');
    }

    public function update(Request $request, Resource $resource)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'resource_type' => 'required|in:Course Syllabus,Lesson,Others',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:20480',
        ]);

        $data = [
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'resource_type' => $request->resource_type,
            'description' => $request->description,
        ];

        if ($request->hasFile('file')) {
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }
            $file = $request->file('file');
            $data['file_path'] = $file->store('resources', 'public');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = strtolower($file->getClientOriginalExtension());
        }

        $resource->update($data);

        AuditLog::record('Edit Resource', "Admin updated resource: {$resource->title}");

        return redirect()->route('admin.resources.index')->with('success', 'Resource updated successfully.');
    }

    public function destroy(Resource $resource)
    {
        $name = $resource->title;

        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        AuditLog::record('Delete Resource', "Admin deleted resource: {$name}");

        return redirect()->route('admin.resources.index')->with('success', 'Resource deleted successfully.');
    }

    public function download(Resource $resource)
    {
        $disk = Storage::disk('public');
        return response()->download($disk->path($resource->file_path), $resource->file_name);
    }
}
