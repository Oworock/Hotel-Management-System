@extends('layouts.app')

@section('title', isset($review) ? 'Edit Performance Review' : 'Create Performance Review')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem; max-width: 900px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
            <i class="fa-solid fa-{{ isset($review) ? 'edit' : 'plus' }}" style="color: var(--primary); margin-right: 0.5rem;"></i>
            {{ isset($review) ? 'Edit' : 'Create' }} Performance Review
        </h2>
    </div>

    @if($errors->any())
        <div style="padding: 1rem; background: rgba(239, 68, 68, 0.1); color: #ef4444; border-radius: var(--radius-md); margin-bottom: 1.5rem; border-left: 4px solid #ef4444;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li style="font-size: 0.85rem;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($review) ? route('admin.hr.performance.update', $review) : route('admin.hr.performance.store') }}" method="POST">
        @csrf
        @if(isset($review))
            @method('PUT')
        @endif

        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
            <div>
                <label for="employee_id" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Employee *</label>
                <select name="employee_id" id="employee_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(old('employee_id', $review->employee_id ?? '') == $emp->id)>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="reviewed_by_id" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Reviewed By *</label>
                <select name="reviewed_by_id" id="reviewed_by_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                    <option value="">Select Reviewer</option>
                    @foreach($reviewers as $reviewer)
                        <option value="{{ $reviewer->id }}" @selected(old('reviewed_by_id', $review->reviewed_by_id ?? '') == $reviewer->id)>{{ $reviewer->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="review_date" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Review Date *</label>
                <input type="date" name="review_date" id="review_date" value="{{ old('review_date', $review->review_date ?? now()->format('Y-m-d')) }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
            </div>

            <div>
                <label for="rating" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Rating (1-5) *</label>
                <select name="rating" id="rating" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);" required>
                    <option value="">Select Rating</option>
                    <option value="1" @selected(old('rating', $review->rating ?? '') == 1)>1 - Poor</option>
                    <option value="2" @selected(old('rating', $review->rating ?? '') == 2)>2 - Below Average</option>
                    <option value="3" @selected(old('rating', $review->rating ?? '') == 3)>3 - Average</option>
                    <option value="4" @selected(old('rating', $review->rating ?? '') == 4)>4 - Good</option>
                    <option value="5" @selected(old('rating', $review->rating ?? '') == 5)>5 - Excellent</option>
                </select>
            </div>

            <div>
                <label for="comments" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Comments</label>
                <textarea name="comments" id="comments" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">{{ old('comments', $review->comments ?? '') }}</textarea>
            </div>

            <div>
                <label for="strengths" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Strengths</label>
                <textarea name="strengths" id="strengths" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">{{ old('strengths', $review->strengths ?? '') }}</textarea>
            </div>

            <div>
                <label for="areas_to_improve" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Areas to Improve</label>
                <textarea name="areas_to_improve" id="areas_to_improve" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">{{ old('areas_to_improve', $review->areas_to_improve ?? '') }}</textarea>
            </div>

            <div>
                <label for="goals" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Goals</label>
                <textarea name="goals" id="goals" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">{{ old('goals', $review->goals ?? '') }}</textarea>
            </div>

            @if(isset($review))
            <div>
                <label for="status" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Status</label>
                <select name="status" id="status" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                    <option value="draft" @selected(old('status', $review->status ?? 'draft') == 'draft')>Draft</option>
                    <option value="completed" @selected(old('status', $review->status ?? '') == 'completed')>Completed</option>
                </select>
            </div>
            @endif
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-save" style="font-size: 0.85rem;"></i>
                {{ isset($review) ? 'Update Review' : 'Create Review' }}
            </button>
            <a href="{{ route('admin.hr.performance.index') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color); text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-arrow-left" style="font-size: 0.85rem;"></i>
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
