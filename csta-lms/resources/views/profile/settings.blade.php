@php
    $layout = match(auth()->user()->role) {
        'admin'   => 'layouts.admin',
        'teacher' => 'layouts.teacher',
        default   => 'layouts.student',
    };
@endphp
@extends($layout)
@section('title', 'Profile Settings')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <span class="material-icons align-middle me-2" style="color:#1a73e8;">manage_accounts</span>
            Profile Settings
        </h1>
        <p class="page-subtitle">Manage your account information and security.</p>
    </div>
</div>

<div class="row g-4">

    {{-- ── LEFT: Profile Picture ── --}}
    <div class="col-lg-4">
        <div class="card p-4 text-center h-100" style="display:flex;flex-direction:column;align-items:center;justify-content:flex-start;">
            <h6 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:20px;align-self:flex-start;">Profile Photo</h6>

            {{-- Avatar --}}
            <div id="avatarWrapper" style="position:relative;width:120px;height:120px;margin-bottom:16px;">
                @if(auth()->user()->profile_picture)
                    <img id="avatarPreview"
                         src="{{ asset('storage/' . auth()->user()->profile_picture) }}"
                         alt="Profile Picture"
                         style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid #e8eaed;">
                @else
                    <div id="avatarInitials"
                         style="width:120px;height:120px;border-radius:50%;background:linear-gradient(135deg,#1a73e8,#34a853);display:flex;align-items:center;justify-content:center;font-family:'Google Sans',Roboto,sans-serif;font-size:42px;font-weight:600;color:#fff;border:3px solid #e8eaed;">
                        {{ strtoupper(substr(auth()->user()->full_name, 0, 2)) }}
                    </div>
                    <img id="avatarPreview"
                         src=""
                         alt="Profile Picture"
                         style="display:none;width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid #e8eaed;">
                @endif
                <label for="profilePictureInput" title="Change photo"
                       style="position:absolute;bottom:4px;right:4px;width:32px;height:32px;background:#1a73e8;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,.25);">
                    <span class="material-icons" style="font-size:16px;color:#fff;">photo_camera</span>
                </label>
            </div>

            <div style="font-weight:600;color:#202124;font-size:15px;">{{ auth()->user()->full_name }}</div>
            <div style="font-size:12px;color:#5f6368;margin-top:2px;">
                {{ auth()->user()->id_number }} &bull; {{ ucfirst(auth()->user()->role) }}
            </div>

            <hr style="width:100%;margin:16px 0;border-color:#e8eaed;">

            {{-- Upload Form --}}
            <form action="{{ route('profile.updatePhoto') }}" method="POST" enctype="multipart/form-data" id="photoForm" style="width:100%;">
                @csrf
                @method('PATCH')
                <input type="file" id="profilePictureInput" name="profile_picture"
                       accept="image/jpeg,image/png,image/gif,image/webp"
                       style="display:none;" onchange="previewPhoto(this)">

                <button type="submit" id="uploadBtn" class="btn btn-primary w-100 rounded-pill mb-2"
                        style="background:#1a73e8;border-color:#1a73e8;display:none;">
                    <span class="material-icons align-middle me-1" style="font-size:16px;">upload</span>
                    Upload Photo
                </button>
                <label for="profilePictureInput"
                       class="btn btn-outline-secondary w-100 rounded-pill mb-2"
                       style="cursor:pointer;">
                    <span class="material-icons align-middle me-1" style="font-size:16px;">photo_library</span>
                    Choose Photo
                </label>
            </form>

            {{-- Remove Photo --}}
            @if(auth()->user()->profile_picture)
            <form action="{{ route('profile.removePhoto') }}" method="POST" style="width:100%;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger w-100 rounded-pill"
                        onclick="return confirm('Remove your profile picture?')">
                    <span class="material-icons align-middle me-1" style="font-size:16px;">delete_outline</span>
                    Remove Photo
                </button>
            </form>
            @endif

            <p style="font-size:11px;color:#80868b;margin-top:12px;margin-bottom:0;">
                JPG, PNG, GIF or WEBP &bull; Max 2 MB
            </p>
        </div>
    </div>

    {{-- ── RIGHT: Info + Password ── --}}
    <div class="col-lg-8 d-flex flex-column gap-4">

        {{-- Personal Information --}}
        <div class="card p-4">
            <h6 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:20px;">
                <span class="material-icons align-middle me-1" style="font-size:18px;color:#1a73e8;">person</span>
                Personal Information
            </h6>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12px;font-weight:600;color:#5f6368;text-transform:uppercase;letter-spacing:.5px;">ID Number</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->id_number }}"
                               readonly style="background:#f8f9fa;color:#5f6368;font-family:monospace;font-size:14px;">
                        <div style="font-size:11px;color:#80868b;margin-top:4px;">ID Number cannot be changed.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12px;font-weight:600;color:#5f6368;text-transform:uppercase;letter-spacing:.5px;">Role</label>
                        <input type="text" class="form-control" value="{{ ucfirst(auth()->user()->role) }}"
                               readonly style="background:#f8f9fa;color:#5f6368;">
                    </div>
                    <div class="col-md-12">
                        <label for="full_name" class="form-label" style="font-size:12px;font-weight:600;color:#5f6368;text-transform:uppercase;letter-spacing:.5px;">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                               id="full_name" name="full_name"
                               value="{{ old('full_name', auth()->user()->full_name) }}"
                               required>
                        @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12">
                        <label for="contact_number" class="form-label" style="font-size:12px;font-weight:600;color:#5f6368;text-transform:uppercase;letter-spacing:.5px;">Contact Number</label>
                        <input type="text" class="form-control @error('contact_number') is-invalid @enderror"
                               id="contact_number" name="contact_number"
                               value="{{ old('contact_number', auth()->user()->contact_number) }}"
                               placeholder="e.g. 09XX-XXX-XXXX">
                        @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4"
                            style="background:#1a73e8;border-color:#1a73e8;">
                        <span class="material-icons align-middle me-1" style="font-size:16px;">save</span>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="card p-4">
            <h6 style="font-family:'Google Sans',Roboto,sans-serif;font-weight:600;color:#202124;margin-bottom:20px;">
                <span class="material-icons align-middle me-1" style="font-size:18px;color:#ea4335;">lock</span>
                Change Password
            </h6>
            <form action="{{ route('profile.changePassword') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="current_password" class="form-label" style="font-size:12px;font-weight:600;color:#5f6368;text-transform:uppercase;letter-spacing:.5px;">Current Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="current_password"
                                   name="current_password" placeholder="Enter current password">
                            <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePwd('current_password', this)">
                                <span class="material-icons" style="font-size:18px;">visibility</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label" style="font-size:12px;font-weight:600;color:#5f6368;text-transform:uppercase;letter-spacing:.5px;">New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password"
                                   name="password" placeholder="Min 8 characters">
                            <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePwd('password', this)">
                                <span class="material-icons" style="font-size:18px;">visibility</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label" style="font-size:12px;font-weight:600;color:#5f6368;text-transform:uppercase;letter-spacing:.5px;">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_confirmation"
                                   name="password_confirmation" placeholder="Repeat new password">
                            <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePwd('password_confirmation', this)">
                                <span class="material-icons" style="font-size:18px;">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        <span class="material-icons align-middle me-1" style="font-size:16px;">lock_reset</span>
                        Update Password
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function previewPhoto(input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('avatarPreview');
            const initials = document.getElementById('avatarInitials');
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (initials) initials.style.display = 'none';
            document.getElementById('uploadBtn').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }

    function togglePwd(id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('.material-icons');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }
</script>
@endpush
@endsection
