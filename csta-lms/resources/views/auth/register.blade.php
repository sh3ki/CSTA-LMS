<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up &mdash; CSTA-LMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * { font-family: 'Roboto', sans-serif; box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; background: linear-gradient(135deg, #fce8ec, #fff); }
        .auth-wrap { min-height: 100vh; display: grid; place-items: center; padding: 24px; }
        .auth-card { width: 100%; max-width: 720px; background: #fff; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,.12); padding: 28px; }
        .title { font-family: 'Google Sans', Roboto, sans-serif; font-size: 28px; font-weight: 700; color: #202124; }
        .subtitle { color: #5f6368; font-size: 14px; }
        .form-label { font-size: 12px; font-weight: 600; text-transform: uppercase; color: #5f6368; letter-spacing: .5px; }
        .btn-main { background: #800020; border-color: #800020; }
        .btn-main:hover { background: #5c0016; border-color: #5c0016; }
    </style>
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <div class="title">Create Account</div>
                <div class="subtitle">Register as student or teacher.</div>
            </div>
            <a href="{{ route('login') }}" class="btn btn-light rounded-pill">Back to Sign In</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Role</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" id="id_number_label">Student ID No.</label>
                    <input type="text" name="id_number" id="id_number" class="form-control" value="{{ old('id_number') }}" placeholder="Enter Student ID No." required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}">
                </div>
                <div class="col-md-6 student-only">
                    <label class="form-label">Course</label>
                    <input type="text" name="course" class="form-control" value="{{ old('course') }}">
                </div>
                <div class="col-md-6 student-only">
                    <label class="form-label">Year Level</label>
                    <select name="year_level" class="form-select">
                        <option value="">Select Year Level</option>
                        @foreach (['1st Year', '2nd Year', '3rd Year', '4th Year'] as $year)
                            <option value="{{ $year }}" {{ old('year_level') === $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="agree_terms" name="agree_terms" {{ old('agree_terms') ? 'checked' : '' }}>
                        <label class="form-check-label" for="agree_terms">
                            I agree to the terms and conditions.
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-main text-white rounded-pill px-4">
                    <span class="material-icons align-middle me-1" style="font-size:16px;">person_add</span>
                    Register
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const roleInput = document.getElementById('role');
    const idNumberLabel = document.getElementById('id_number_label');
    const idNumberInput = document.getElementById('id_number');
    const studentBlocks = document.querySelectorAll('.student-only');

    function toggleStudentFields() {
        const isStudent = roleInput.value === 'student';

        if (isStudent) {
            idNumberLabel.textContent = 'Student ID No.';
            idNumberInput.placeholder = 'Enter Student ID No.';
        } else {
            idNumberLabel.textContent = 'Teacher ID No.';
            idNumberInput.placeholder = 'Enter Teacher ID No.';
        }

        studentBlocks.forEach((block) => {
            block.style.display = isStudent ? '' : 'none';
        });
    }

    roleInput.addEventListener('change', toggleStudentFields);
    toggleStudentFields();
</script>
</body>
</html>
