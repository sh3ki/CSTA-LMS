# CSTA-LMS — Missing Features & Broken Functionality
**Date Audited:** May 12, 2026  
**Audited By:** GitHub Copilot (full codebase analysis)  
**Scope:** Admin Portal · Teacher Portal · Student Portal

---

## LEGEND
- 🔴 **CRITICAL** — Causes crash / security vulnerability
- 🟠 **BROKEN** — Feature exists in UI but does nothing / throws error
- 🟡 **INCOMPLETE** — Feature is started but under development / placeholder only
- 🟢 **MISSING** — Feature not started at all, needs to be built from scratch

---

## ═══════════════════════════════════════════
## CRITICAL BUGS (Fix First)
## ═══════════════════════════════════════════

### 🔴 BUG-01 — ProfileController: Password Not Hashed on Change
**File:** `app/Http/Controllers/ProfileController.php` — `changePassword()` method  
**Problem:** The password is stored in **plaintext** — `Hash::make()` is never called.  
```php
// CURRENT (BROKEN — stores plaintext password):
$user->update(['password' => $request->password]);

// FIX:
$user->update(['password' => Hash::make($request->password)]);
```
**Impact:** Any user who changes their password via Profile Settings will have a plaintext password stored in the database. Logging in after this will fail because Laravel's auth hashes comparison will not match.  
**Affects:** All roles (Admin, Teacher, Student) — profile settings page.

---

### 🔴 BUG-02 — Admin: Missing Blade View `admin.classes.show`
**File:** `app/Http/Controllers/Admin/ClassController.php` — `show()` method, line 71  
**Route:** `GET /admin/classes/{class}` → `admin.classes.show`  
**Problem:** The controller references `view('admin.classes.show')` but the file `resources/views/admin/classes/show.blade.php` **does not exist**.  
**Impact:** Visiting `/admin/classes/{id}` throws a `View [admin.classes.show] not found` 500 error.  
**Fix:** Create `resources/views/admin/classes/show.blade.php` showing class details (teacher, students list, subjects list).

---

### 🔴 BUG-03 — Admin: Missing Blade View `admin.subjects.show`
**File:** `app/Http/Controllers/Admin/SubjectController.php` — `show()` method, line 69  
**Route:** `GET /admin/subjects/{subject}` → `admin.subjects.show`  
**Problem:** The controller references `view('admin.subjects.show')` but the file `resources/views/admin/subjects/show.blade.php` **does not exist**.  
**Impact:** Visiting `/admin/subjects/{id}` throws a 500 error.  
**Fix:** Create `resources/views/admin/subjects/show.blade.php` showing subject details (class, code, resources, tasks).

---

### 🔴 BUG-04 — Teacher Classes: Cannot Add Students (Chicken-and-Egg Logic)
**File:** `app/Http/Controllers/Teacher/ClassController.php` — `store()` and `update()` methods  
**Problem:** The student picker in both create and edit modals is filtered to only show students **already enrolled in this teacher's existing classes**. This means:  
- A brand-new teacher with no classes yet will see **zero students** in the picker and can never add any student.  
- The teacher portal UI even shows the message: *"No students available from your currently assigned classes."*  
**Fix:** Change the student query to show **all active students** (as the admin portal does), or at minimum all students not yet enrolled in any class.

---

## ═══════════════════════════════════════════
## ADMIN PORTAL
## ═══════════════════════════════════════════

### 🟠 ADMIN-01 — Announcements: "New Announcement" Button Does Nothing
**Page:** `/admin/announcements`  
**File:** `resources/views/admin/announcements/index.blade.php`  
**Problem:** The "New Announcement" button has no `data-bs-toggle` or modal attached. Clicking it does nothing.  
**Controller:** `AnnouncementController::index()` only returns an empty placeholder view — no CRUD logic exists.  
**Routes missing:** `POST /admin/announcements`, `PUT /admin/announcements/{id}`, `DELETE /admin/announcements/{id}`  
**Model missing:** No `Announcement` model or migration exists.  
**Fix needed:**
1. Create `Announcement` model + migration (title, body, created_by, target_role/all, published_at)
2. Build full CRUD in `AnnouncementController`
3. Add routes in `web.php`
4. Build create/edit modal in the view
5. Show announcements in a table or card list

---

### 🟡 ADMIN-02 — Reports & Analytics: Placeholder Only
**Page:** `/admin/reports`  
**File:** `resources/views/admin/reports/index.blade.php`  
**Problem:** The entire page is a "under development" placeholder. The "Export Report" button does nothing (it's a plain `<button>` with no form or action).  
**Controller:** `ReportController::index()` returns only the empty view — no data queries.  
**Fix needed:**
1. Add stats queries in `ReportController::index()` (enrollment counts, submission rates, grade distributions)
2. Build the reports view with charts or tables
3. Add CSV/PDF export functionality — add a `GET /admin/reports/export` route

---

### 🟡 ADMIN-03 — System Settings: Placeholder Only
**Page:** `/admin/settings`  
**File:** `resources/views/admin/settings/index.blade.php`  
**Problem:** Entire page is a placeholder. No forms, no save buttons, no routes for saving.  
**Controller:** `SettingsController::index()` only returns the empty view.  
**Fix needed:**
1. Decide what settings to configure (school name, semester, grading scale, etc.)
2. Create a `settings` table or use a config-based approach
3. Build `SettingsController::update()` with `POST /admin/settings` route
4. Build the settings form view

---

### 🟡 ADMIN-04 — Admin Dashboard: Missing Stats & Activity Feed
**Page:** `/admin/dashboard`  
**File:** `app/Http/Controllers/Admin/DashboardController.php`  
**Problem:** Dashboard only shows 4 stats (teachers, students, classes, subjects). Missing:
- Total Resources count
- Total Tasks count
- Total Submissions count
- Recent Audit Activity feed
- Recent registrations

**Fix needed:** Expand `DashboardController::index()` to include more stat counters and a recent activity list.

---

### 🟡 ADMIN-05 — Audit Logs: No Filter, No Export
**Page:** `/admin/audit-logs`  
**File:** `resources/views/admin/audit-logs/index.blade.php`  
**Problem:**
- No search/filter bar (cannot search by user name, action type, or date range)
- No export functionality (no way to download logs as CSV)
**Fix needed:**
1. Add filter form (search by user, role, action, date from/to)
2. Update `AuditLogController::index()` to accept and apply filters
3. Add `GET /admin/audit-logs/export` route and download controller method

---

### 🟡 ADMIN-06 — Admin Task Management: No Submissions View
**Page:** `/admin/tasks`  
**Problem:** Admin can create, edit, and delete tasks but **cannot view submissions**. There is no "View Submissions" button or route in the admin task section (unlike the teacher portal which has `teacher.tasks.show`).  
**Fix needed:** Add a task detail/submissions view for admin, or at minimum link to the relevant teacher view.

---

### 🟡 ADMIN-07 — Admin Dashboard: Quick Actions Missing Links
**Page:** `/admin/dashboard`  
**Problem:** The Quick Actions section only has 4 shortcuts (Manage Teachers, Students, Classes, Subjects). Missing links to: Resources, Tasks, Reports, Audit Logs, Settings.

---

## ═══════════════════════════════════════════
## TEACHER PORTAL
## ═══════════════════════════════════════════

### 🟠 TEACHER-01 — No Subject Edit or Delete for Teachers
**Routes missing:** `PUT /teacher/subjects/{subject}` and `DELETE /teacher/subjects/{subject}`  
**File:** `app/Http/Controllers/Teacher/SubjectController.php`  
**Problem:** Teachers can create subjects but **cannot edit** the subject name, description, semester, or course code, and **cannot delete** a subject they created.  
The `SubjectController` only has `index()`, `show()`, and `store()`. No `update()` or `destroy()` methods exist.  
**Fix needed:**
1. Add `update()` and `destroy()` methods to `Teacher\SubjectController`
2. Register `PUT /teacher/subjects/{subject}` and `DELETE /teacher/subjects/{subject}` routes
3. Add Edit and Delete buttons/modals in `teacher/subjects/index.blade.php`

---

### 🟠 TEACHER-02 — Quiz Task Edit Modal Missing Quiz Builder
**Page:** `/teacher/tasks`  
**File:** `resources/views/teacher/tasks/index.blade.php`  
**Problem:** When creating a task with type "Quiz", a full quiz builder UI is shown (add questions, types, multiple choice options, Google Form link). However, when **editing** a Quiz task via the edit modal, the quiz builder is completely absent — only the basic text description field is shown.  
**Impact:** Editing a Quiz task overwrites the structured quiz content with plain text, destroying the quiz item structure.  
**Fix needed:** Add the quiz builder UI to the edit modal and load existing quiz items when the modal opens.

---

### 🟡 TEACHER-03 — Performance Report: No Export
**Page:** `/teacher/performance`  
**File:** `resources/views/teacher/performance/index.blade.php`  
**Problem:** The grade sheet table is displayed correctly but there is no way to export it (no "Export CSV" or "Export PDF" button).  
**Fix needed:** Add an export button and `GET /teacher/performance/export` route that downloads the grade sheet as CSV.

---

### 🟡 TEACHER-04 — Teacher Dashboard: Potential Null Date Error
**Page:** `/teacher/dashboard`  
**File:** `resources/views/teacher/dashboard.blade.php`  
**Problem:** The recent tasks table calls `$task->due_date->isPast()` directly. If any task somehow has a null `due_date`, this will throw `Call to member function isPast() on null`.  
**Fix:** Use `$task->due_date?->isPast()` (null-safe operator) in the view.

---

### 🟡 TEACHER-05 — Teacher Resources: No Resource Type Filter
**Page:** `/teacher/resources`  
**Problem:** The resources filter bar has search and subject filter, but no filter by `resource_type` (Course Syllabus / Lesson / Others) — even though the admin resources page has this filter.  
**Fix needed:** Add a `resource_type` dropdown filter to the teacher resources filter bar.

---

## ═══════════════════════════════════════════
## STUDENT PORTAL
## ═══════════════════════════════════════════

### 🟡 STUDENT-01 — Announcements: Placeholder Only
**Page:** `/student/announcements`  
**File:** `resources/views/student/announcements/index.blade.php`  
**Problem:** The entire page is a placeholder message with no data. The `AnnouncementController::index()` returns the view with zero data. There is no announcement model yet.  
**Fix needed:** Once Admin announcements (ADMIN-01) are built, connect them here — display published announcements targeted at students.

---

### 🟡 STUDENT-02 — Submission History: No Download Button for Past Attempts
**Page:** `/student/tasks/{task}` — "Submission History" card  
**Problem:** The submission history list shows each attempt's file name and timestamp, but there is **no download button** for previous submission files. Students can see they submitted `file.pdf` on attempt #1 but cannot retrieve it.  
**Note:** The route `student.resources.download` exists. A matching route for submission history downloads would need to be added.  
**Fix needed:**
1. Add `GET /student/submission-histories/{history}/download` route
2. Create `downloadSubmissionHistory()` method in `Student\TaskController`
3. Add download link/button per attempt row in the submission history card

---

### 🟡 STUDENT-03 — Student Tasks Index: "Status" Filter Labels Not User-Friendly
**Page:** `/student/tasks`  
**Problem:** The status filter dropdown options use raw constant values (`submitted_on_time`, `submitted_late`, `missing`). The controller correctly uses these constants, but the dropdown option labels should match what students expect to see (e.g., "Submitted On Time", "Submitted Late", "Missing / Not Submitted").  
**Verify:** Ensure the `<option value="...">` values in the task filter exactly match the `Submission::STATUS_*` constants.

---

## ═══════════════════════════════════════════
## SHARED / CROSS-PORTAL
## ═══════════════════════════════════════════

### 🟡 SHARED-01 — No Announcement System (End-to-End)
**Portals affected:** Admin, Student (Teacher may post too)  
**Summary:** There is no Announcement model, migration, or CRUD anywhere. The feature is referenced in the UI (admin announcements page, student announcements page, student dashboard quick-link) but is entirely unimplemented.  
**Full implementation needed:**
- `Announcement` model + migration + factory
- Admin CRUD (create, publish, edit, delete)
- Student read-only view (filtered by target audience)
- Optional: Teacher can post class-level announcements

---

### 🟡 SHARED-02 — No Notification System
**Problem:** There is a notification bell icon (`nav-icon-btn`) with a red dot indicator in the admin navbar but no notification data or routes behind it. The bell is decorative only.  
**Fix needed (optional enhancement):** Wire up a basic in-app notification for key events (new submission received, task graded, announcement published).

---

## ═══════════════════════════════════════════
## SUMMARY TABLE
## ═══════════════════════════════════════════

| ID | Priority | Portal | Feature | Status |
|----|----------|--------|---------|--------|
| BUG-01 | 🔴 Critical | All | Profile password change — plaintext storage | Fix immediately |
| BUG-02 | 🔴 Critical | Admin | `admin.classes.show` view missing → 500 error | Create view |
| BUG-03 | 🔴 Critical | Admin | `admin.subjects.show` view missing → 500 error | Create view |
| BUG-04 | 🔴 Critical | Teacher | Cannot add students to class (chicken-and-egg) | Fix query |
| ADMIN-01 | 🟠 Broken | Admin | Announcements — no model/CRUD/routes | Build from scratch |
| ADMIN-02 | 🟡 Incomplete | Admin | Reports & Analytics — placeholder only | Build from scratch |
| ADMIN-03 | 🟡 Incomplete | Admin | System Settings — placeholder only | Build from scratch |
| ADMIN-04 | 🟡 Incomplete | Admin | Dashboard missing stats & activity feed | Expand controller |
| ADMIN-05 | 🟡 Incomplete | Admin | Audit Logs — no filter/export | Add filter + export |
| ADMIN-06 | 🟡 Incomplete | Admin | Tasks — no submissions view for admin | Add view/route |
| ADMIN-07 | 🟢 Missing | Admin | Dashboard quick actions incomplete | Add links |
| TEACHER-01 | 🟠 Broken | Teacher | No subject edit/delete for teacher | Add routes + methods |
| TEACHER-02 | 🟠 Broken | Teacher | Quiz edit modal missing quiz builder | Add quiz UI to edit modal |
| TEACHER-03 | 🟡 Incomplete | Teacher | Performance report — no export | Add CSV export |
| TEACHER-04 | 🟡 Incomplete | Teacher | Dashboard null due_date crash risk | Add null-safe operator |
| TEACHER-05 | 🟡 Incomplete | Teacher | Resources — no type filter | Add dropdown filter |
| STUDENT-01 | 🟡 Incomplete | Student | Announcements — placeholder only | Wire to Announcement model |
| STUDENT-02 | 🟡 Incomplete | Student | Submission history — no download for past attempts | Add route + button |
| STUDENT-03 | 🟡 Incomplete | Student | Tasks status filter — verify option values | Check/fix values |
| SHARED-01 | 🟢 Missing | All | Announcement system — end-to-end not built | Full feature build |
| SHARED-02 | 🟢 Missing | All | Notification bell — decorative only | Optional enhancement |

---

## ═══════════════════════════════════════════
## SUGGESTED FIX ORDER (Prioritized)
## ═══════════════════════════════════════════

### Phase 1 — Immediate Fixes (Crashes & Security)
1. **BUG-01** — Fix plaintext password storage in `ProfileController::changePassword()`
2. **BUG-02** — Create `admin/classes/show.blade.php`
3. **BUG-03** — Create `admin/subjects/show.blade.php`
4. **BUG-04** — Fix teacher class student query to show all active students
5. **TEACHER-04** — Add null-safe `?->` on `$task->due_date` in teacher dashboard view

### Phase 2 — Restore Broken Buttons & Core Features
6. **TEACHER-01** — Add subject edit/delete for teacher portal
7. **TEACHER-02** — Add quiz builder to task edit modal
8. **ADMIN-01 + SHARED-01** — Build Announcement model, CRUD, and connect to student view
9. **STUDENT-02** — Add submission history download for students

### Phase 3 — Incomplete Pages
10. **ADMIN-02** — Build Reports & Analytics page
11. **ADMIN-03** — Build System Settings page
12. **ADMIN-04** — Expand admin dashboard stats
13. **ADMIN-05** — Add filter + export to Audit Logs
14. **ADMIN-06** — Add task submissions view for admin
15. **TEACHER-03** — Add CSV export to performance report
16. **TEACHER-05** — Add resource type filter in teacher resources

### Phase 4 — Polish & Enhancements
17. **ADMIN-07** — Add more quick action links to admin dashboard
18. **STUDENT-03** — Verify/fix task status filter option values
19. **SHARED-02** — Implement notification bell system (optional)

---

*End of audit document.*
