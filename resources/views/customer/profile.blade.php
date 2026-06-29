@extends('layouts.customer')
@section('title', 'Profil Saya')

@section('content')
<style>
.profile-container {
    max-width: 650px;
    margin: 2rem auto;
    padding: 0 1rem;
}
.profile-card {
    background: white;
    border-radius: 1.5rem;
    padding: 2.5rem;
    box-shadow: 0 25px 50px -12px rgba(234, 88, 12, 0.15);
    border: 1px solid var(--border-color);
    text-align: center;
    position: relative;
    overflow: hidden;
}
.profile-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 120px;
    background: linear-gradient(135deg, var(--primary), var(--primary-hover));
    z-index: 0;
}
.avatar-wrapper {
    position: relative;
    width: 110px;
    height: 110px;
    margin: 0 auto 1.5rem;
    margin-top: 2rem;
    z-index: 1;
}
.avatar-lg {
    width: 100%; height: 100%;
    border-radius: 50%;
    background: #FFF;
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 800;
    border: 4px solid #FFF;
    box-shadow: var(--shadow-md);
    object-fit: cover;
}
.profile-name {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--text-dark);
    margin-bottom: 0.25rem;
}
.profile-email {
    color: var(--text-muted);
    font-size: 1rem;
    margin-bottom: 2rem;
}
.info-list {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    text-align: left;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px dashed var(--border-color);
}
.info-item {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1rem 1.25rem;
    background: var(--bg-light);
    border-radius: 1rem;
    transition: 0.3s ease;
}
.info-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}
.info-icon {
    width: 48px; height: 48px;
    background: white;
    color: var(--primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: var(--shadow-sm);
}
.info-label {
    font-size: 0.8rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 700;
    margin-bottom: 0.25rem;
}
.info-value {
    font-weight: 700;
    color: var(--text-dark);
    font-size: 1rem;
}
.action-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-top: 2.5rem;
}
.btn-logout {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 1rem;
    background: #FEE2E2;
    color: #DC2626;
    border: none;
    border-radius: 1rem;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-logout:hover {
    background: #DC2626;
    color: #FFF;
}
.btn-edit {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 1rem;
    background: var(--primary-light);
    color: var(--primary);
    border: none;
    border-radius: 1rem;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-edit:hover {
    background: var(--primary);
    color: #FFF;
}
/* Form Styles */
.form-group {
    text-align: left;
    margin-bottom: 1.5rem;
}
.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}
.form-control {
    width: 100%;
    padding: 1rem 1.25rem;
    border: 1px solid var(--border-color);
    border-radius: 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    font-family: inherit;
    background: var(--bg-light);
}
.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px var(--primary-light);
    background: white;
}
.file-upload-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
    margin-bottom: 1rem;
}
.btn-upload {
    padding: 0.5rem 1rem;
    background: var(--bg-light);
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    color: var(--text-dark);
}
.file-upload-wrapper input[type=file] {
    font-size: 100px;
    position: absolute;
    left: 0;
    top: 0;
    opacity: 0;
    cursor: pointer;
}
</style>

<div class="profile-container animate-slide-up" x-data="{ isEditing: false }">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('menu') }}" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; font-size: 0.9rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Menu
        </a>
    </div>
    @if(session('success'))
        <div style="background: #ECFDF5; color: #047857; padding: 1rem; border-radius: 1rem; margin-bottom: 1.5rem; text-align: center; font-weight: 600; border: 1px solid #A7F3D0;">
            ✅ {{ session('success') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div style="background: #FEE2E2; color: #991B1B; padding: 1rem; border-radius: 1rem; margin-bottom: 1.5rem; font-weight: 600; border: 1px solid #FECACA;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-card">
        <!-- View Mode -->
        <div x-show="!isEditing" x-transition.opacity.duration.300ms>
            <div class="avatar-wrapper">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="avatar-lg">
                @else
                    <div class="avatar-lg">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                @endif
            </div>
            
            <div class="profile-name">{{ $user->name }}</div>
            <div class="profile-email">{{ $user->email }}</div>

            <div class="info-list">
                <div class="info-item">
                    <div class="info-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg>
                    </div>
                    <div>
                        <div class="info-label">Nomor Telepon</div>
                        <div class="info-value">{{ $user->customerProfile->phone ?? 'Belum diisi' }}</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    </div>
                    <div>
                        <div class="info-label">NIM</div>
                        <div class="info-value">{{ $user->customerProfile->nim ?? 'Belum diisi' }}</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M3 10h18"/><path d="M5 6l7-3 7 3"/><path d="M4 10v11"/><path d="M20 10v11"/><path d="M8 14v3"/><path d="M12 14v3"/><path d="M16 14v3"/></svg>
                    </div>
                    <div>
                        <div class="info-label">Fakultas / Jurusan</div>
                        <div class="info-value">{{ $user->customerProfile->faculty ?? 'Belum diisi' }}</div>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <button @click="isEditing = true" class="btn-edit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                    Edit Profil
                </button>
                <form action="{{ route('customer.logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Edit Mode -->
        <div x-show="isEditing" x-cloak style="display: none;" x-transition.opacity.duration.300ms>
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 2rem; color: var(--text-dark);">Edit Profil</h2>
            
            <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="form-group" style="text-align: center;">
                    <div class="avatar-wrapper" style="margin-top: 0; height: 90px; width: 90px;">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="avatar-lg" style="height: 100%; width: 100%;" id="avatar-preview">
                        @else
                            <div class="avatar-lg" style="height: 100%; width: 100%; font-size: 2.5rem;" id="avatar-preview-text">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                            <img src="" alt="Avatar" class="avatar-lg" style="height: 100%; width: 100%; display: none;" id="avatar-preview">
                        @endif
                    </div>
                    <div class="file-upload-wrapper">
                        <button type="button" class="btn-upload">Pilih Foto Baru</button>
                        <input type="file" name="avatar" accept="image/*" onchange="previewImage(event)">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->customerProfile->phone ?? '') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">NIM</label>
                    <input type="text" name="nim" class="form-control" value="{{ old('nim', $user->customerProfile->nim ?? '') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Fakultas / Jurusan</label>
                    <input type="text" name="faculty" class="form-control" value="{{ old('faculty', $user->customerProfile->faculty ?? '') }}">
                </div>

                <div class="action-buttons">
                    <button type="button" @click="isEditing = false" class="btn-logout" style="background: var(--bg-light); color: var(--text-dark);">
                        Batal
                    </button>
                    <button type="submit" class="btn-edit" style="background: var(--primary); color: white;">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('avatar-preview');
        output.src = reader.result;
        output.style.display = 'block';
        
        var textOutput = document.getElementById('avatar-preview-text');
        if(textOutput) {
            textOutput.style.display = 'none';
        }
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
