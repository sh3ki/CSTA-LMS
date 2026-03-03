<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user();

        // Get classes assigned to this teacher
        $classIds = SchoolClass::where('teacher_id', $teacher->id)->pluck('id');

        $query = Subject::with(['schoolClass.students', 'resources', 'tasks'])
            ->whereIn('class_id', $classIds);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%$q%")
                   ->orWhere('description', 'like', "%$q%");
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $subjects = $query->orderBy('name')->paginate(10)->withQueryString();
        $classes  = SchoolClass::where('teacher_id', $teacher->id)->orderBy('name')->get();

        return view('teacher.subjects.index', compact('subjects', 'classes'));
    }

    public function show(Subject $subject)
    {
        $teacher = auth()->user();

        // Ensure this subject belongs to a class the teacher owns
        $ownsSubject = SchoolClass::where('teacher_id', $teacher->id)
            ->where('id', $subject->class_id)
            ->exists();

        if (!$ownsSubject) {
            abort(403, 'Unauthorized access.');
        }

        $subject->load(['schoolClass.students', 'resources', 'tasks']);

        return view('teacher.subjects.show', compact('subject'));
    }
}
