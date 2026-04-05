<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Resource;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    /**
     * Check if the given subject belongs to the authenticated teacher via DB query.
     */
    private function ownsSubject($subjectId): bool
    {
        $teacher = Auth::user();
        if (!$teacher) {
            return false;
        }

        return Subject::whereKey($subjectId)
            ->whereHas('schoolClass', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->exists();
    }

    public function index(Request $request)
    {
        $teacher    = $request->user();
        $classIds   = SchoolClass::where('teacher_id', $teacher->id)->where('status', true)->pluck('id');
        $subjectIds = Subject::where('status', true)->whereIn('class_id', $classIds)->pluck('id');

        $query = Resource::with('subject.schoolClass')
            ->whereIn('subject_id', $subjectIds);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%$q%")
                   ->orWhere('description', 'like', "%$q%")
                   ->orWhere('file_name', 'like', "%$q%");
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $resources = $query->orderByDesc('created_at')->paginate(10)->withQueryString();
        $subjects  = Subject::where('status', true)->whereIn('class_id', $classIds)->orderBy('name')->get();

        return view('teacher.resources.index', compact('resources', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id'  => 'required|exists:subjects,id',
            'title'       => 'required|string|max:255',
            'resource_type' => 'required|in:Course Syllabus,Lesson,Others',
            'description' => 'nullable|string',
            'file'        => 'required|file|max:20480', // 20MB max
        ]);

        // Verify teacher owns this subject
        if (!$this->ownsSubject($request->subject_id)) {
            abort(403, 'Unauthorized access.');
        }

        $file     = $request->file('file');
        $path     = $file->store('resources', 'public');
        $fileName = $file->getClientOriginalName();
        $fileType = $file->getClientOriginalExtension();

        Resource::create([
            'subject_id'  => $request->subject_id,
            'title'       => $request->title,
            'resource_type' => $request->resource_type,
            'description' => $request->description,
            'file_path'   => $path,
            'file_name'   => $fileName,
            'file_type'   => strtolower($fileType),
            'uploaded_by' => $request->user()->id,
        ]);

        AuditLog::record('Upload Resource', "Uploaded resource: {$request->title}");

        if ($request->redirect_to === 'subject_show') {
            return redirect()->route('teacher.subjects.show', $request->subject_id)->with('success', 'Resource uploaded successfully.');
        }

        return redirect()->route('teacher.resources.index')->with('success', 'Resource uploaded successfully.');
    }

    public function update(Request $request, Resource $resource)
    {
        // Verify teacher owns this resource's subject
        if (!$this->ownsSubject($resource->subject_id)) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'subject_id'  => 'required|exists:subjects,id',
            'resource_type' => 'required|in:Course Syllabus,Lesson,Others',
            'description' => 'nullable|string',
            'file'        => 'nullable|file|max:20480',
        ]);

        if (!$this->ownsSubject($request->subject_id)) {
            abort(403, 'Unauthorized access.');
        }

        $data = [
            'title'       => $request->title,
            'subject_id'  => $request->subject_id,
            'resource_type' => $request->resource_type,
            'description' => $request->description,
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }
            $file = $request->file('file');
            $data['file_path'] = $file->store('resources', 'public');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = strtolower($file->getClientOriginalExtension());
        }

        $resource->update($data);

        AuditLog::record('Edit Resource', "Updated resource: {$resource->title}");

        return redirect()->route('teacher.resources.index')->with('success', 'Resource updated successfully.');
    }

    public function destroy(Request $request, Resource $resource)
    {
        if (!$this->ownsSubject($resource->subject_id)) {
            abort(403, 'Unauthorized access.');
        }

        $name = $resource->title;

        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        AuditLog::record('Delete Resource', "Deleted resource: {$name}");

        if ($request->redirect_to === 'subject_show' && $request->subject_id) {
            return redirect()->route('teacher.subjects.show', $request->subject_id)->with('success', 'Resource deleted successfully.');
        }

        return redirect()->route('teacher.resources.index')->with('success', 'Resource deleted successfully.');
    }

    public function download(Resource $resource)
    {
        if (!$this->ownsSubject($resource->subject_id)) {
            abort(403, 'Unauthorized access.');
        }

        $disk = Storage::disk('public');
        return response()->download($disk->path($resource->file_path), $resource->file_name);
    }
}
