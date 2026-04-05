<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['id_number' => 'ADMIN-001'],
            [
                'full_name'      => 'System Administrator',
                'email'          => 'admin@csta-lms.local',
                'contact_number' => '09170000001',
                'role'           => 'admin',
                'status'         => true,
                'password'       => Hash::make('admin123'),
            ]
        );

        $teachers = collect();
        for ($i = 1; $i <= 5; $i++) {
            $teachers->push(User::updateOrCreate(
                ['id_number' => sprintf('TCH-%03d', $i)],
                [
                    'full_name'      => "Teacher {$i}",
                    'email'          => sprintf('teacher%02d@csta-lms.local', $i),
                    'contact_number' => sprintf('09170001%03d', $i),
                    'role'           => 'teacher',
                    'status'         => true,
                    'password'       => Hash::make('teacher123'),
                ]
            ));
        }

        $allStudents = collect();
        for ($i = 1; $i <= 25; $i++) {
            $allStudents->push(User::updateOrCreate(
                ['id_number' => sprintf('STU-%03d', $i)],
                [
                    'full_name'      => "Student {$i}",
                    'email'          => sprintf('student%02d@csta-lms.local', $i),
                    'contact_number' => sprintf('09180001%03d', $i),
                    'role'           => 'student',
                    'course'         => 'BSCS',
                    'year_level'     => ['1st Year', '2nd Year', '3rd Year', '4th Year'][($i - 1) % 4],
                    'status'         => true,
                    'password'       => Hash::make('student123'),
                ]
            ));
        }

        for ($classIndex = 1; $classIndex <= 5; $classIndex++) {
            $teacher = $teachers[$classIndex - 1];

            $class = SchoolClass::updateOrCreate(
                ['name' => sprintf('Class %d', $classIndex)],
                [
                    'teacher_id' => $teacher->id,
                    'status'     => true,
                ]
            );

            $studentOffset = ($classIndex - 1) * 5;
            $classStudents = $allStudents->slice($studentOffset, 5);
            $class->students()->sync($classStudents->pluck('id')->all());

            for ($subjectIndex = 1; $subjectIndex <= 2; $subjectIndex++) {
                $subject = Subject::firstOrNew([
                    'class_id' => $class->id,
                    'name'     => sprintf('Subject %d.%d', $classIndex, $subjectIndex),
                ]);

                if (!$subject->subject_code) {
                    $subject->subject_code = Subject::generateUniqueCode();
                }

                $subject->course_code = sprintf('CS%d%d', $classIndex, $subjectIndex);
                $subject->semester = $subjectIndex === 1 ? '1st' : '2nd';
                $subject->description = sprintf('Seeded subject %d for Class %d.', $subjectIndex, $classIndex);
                $subject->status = true;
                $subject->created_by = $teacher->id;
                $subject->save();
            }
        }

        $this->command->info("Admin seeded: {$admin->id_number} / admin123");
        $this->command->info('Seeded data: 5 teachers, 5 classes, 10 subjects, 25 students (5 per class).');
    }
}
