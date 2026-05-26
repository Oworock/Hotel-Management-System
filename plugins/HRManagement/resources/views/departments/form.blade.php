@extends('layouts.app')

@section('title', isset($department) ? 'Edit Department' : 'Create Department')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem; max-width: 900px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
            <i class="fa-solid fa-{{ isset($department) ? 'edit' : 'plus' }}" style="color: var(--primary); margin-right: 0.5rem;"></i>
            {{ isset($department) ? 'Edit' : 'Create' }} Department
        </h2>
    </div>

    @if($errors->any())
        <div style="padding: 1rem; background: rgba(239, 68, 68, 0.1); color: #ef4444; border-radius: var(--radius-md); margin-bottom: 1.5rem; border-left: 4px solid #ef4444;">
            <p style="font-weight: 600; margin: 0 0 0.5rem 0;">Please fix the following errors:</p>
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li style="font-size: 0.85rem;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($department) ? route('admin.hr.departments.update', $department) : route('admin.hr.departments.store') }}" method="POST">
        @csrf
        @if(isset($department))
            @method('PUT')
        @endif

        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
            <div>
                <label for="name" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Department Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $department->name ?? '') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                @error('name')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Description</label>
                <textarea name="description" id="description" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">{{ old('description', $department->description ?? '') }}</textarea>
                @error('description')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="manager_id" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Department Manager</label>
                <select name="manager_id" id="manager_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                    <option value="">Select Manager</option>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" @selected(old('manager_id', $department->manager_id ?? '') == $manager->id)>{{ $manager->full_name }}</option>
                    @endforeach
                </select>
                @error('manager_id')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="budget" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Budget</label>
                <input type="number" name="budget" id="budget" value="{{ old('budget', $department->budget ?? '') }}" step="0.01" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                @error('budget')<p style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label style="display: flex; align-items: center; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" @if(old('is_active', $department->is_active ?? true)) checked @endif style="width: 1rem; height: 1rem; cursor: pointer; accent-color: var(--primary);">
                    <span style="margin-left: 0.5rem;">Active</span>
                </label>
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-save" style="font-size: 0.85rem;"></i>
                {{ isset($department) ? 'Update' : 'Create' }} Department
            </button>
            <a href="{{ route('admin.hr.departments.index') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color); text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-arrow-left" style="font-size: 0.85rem;"></i>
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
