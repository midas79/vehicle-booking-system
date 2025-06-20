@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">{{ __('Profile') }}</h2>

            <!-- Update Profile Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Profile Information') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __("Update your account's profile information and email address.") }}</p>
                    
                    <form method="post" action="{{ route('profile.update') }}" class="mt-4">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Role') }}</label>
                            <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" readonly disabled>
                        </div>

                        @if($user->role === 'approver' && $user->level)
                        <div class="mb-3">
                            <label class="form-label">{{ __('Approval Level') }}</label>
                            <input type="text" class="form-control" value="Level {{ $user->level }}" readonly disabled>
                        </div>
                        @endif

                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                        @if (session('status') === 'profile-updated')
                            <span class="text-success ms-2">{{ __('Saved.') }}</span>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Update Password -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Update Password') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
                    
                    <form method="post" action="{{ route('password.update') }}" class="mt-4">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                            <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                                   id="current_password" name="current_password" autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('New Password') }}</label>
                            <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                                   id="password" name="password" autocomplete="new-password">
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                            <input type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                                   id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                            @error('password_confirmation', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                        @if (session('status') === 'password-updated')
                            <span class="text-success ms-2">{{ __('Saved.') }}</span>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Delete Account -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">{{ __('Delete Account') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>
                    
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        {{ __('Delete Account') }}
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post" action="{{ route('profile.destroy') }}">
                                    @csrf
                                    @method('delete')
                                    
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteAccountModalLabel">{{ __('Are you sure you want to delete your account?') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>
                                        
                                        <div class="mb-3">
                                            <label for="password_delete" class="form-label">{{ __('Password') }}</label>
                                            <input type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                                                   id="password_delete" name="password" placeholder="{{ __('Password') }}">
                                            @error('password', 'userDeletion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                        <button type="submit" class="btn btn-danger">{{ __('Delete Account') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($errors->userDeletion->isNotEmpty())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
        myModal.show();
    });
</script>
@endif
@endsection