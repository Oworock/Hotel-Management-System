@extends('layouts.app')

@section('title', 'Performance Reviews')

@section('content')
<div class="glass-panel animate-fade-in" style="padding: 1.5rem 2rem 2.5rem 2rem;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                <i class="fa-solid fa-star" style="color: var(--primary); margin-right: 0.5rem;"></i> Performance Reviews
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">Track and manage employee performance</p>
        </div>
        <a href="{{ route('admin.hr.performance.create') }}" class="btn" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; border: none; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer;">
            <i class="fa-solid fa-plus"></i> Create Review
        </a>
    </div>

    @if(session('success'))
        <div style="padding: 1rem; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: var(--radius-md); margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter -->
    <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 1.5rem; border-radius: var(--radius-md);">
        <form method="GET" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem;">
            <select name="employee_id" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                <option value="">All Employees</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                @endforeach
            </select>
            <select name="status" style="padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-primary); color: var(--text-primary);">
                <option value="">All Status</option>
                <option value="draft" @selected(request('status') == 'draft')>Draft</option>
                <option value="completed" @selected(request('status') == 'completed')>Completed</option>
            </select>
            <button type="submit" class="btn btn-outline" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="table-container" style="border-radius: var(--radius-md); overflow: hidden;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Reviewed By</th>
                    <th>Date</th>
                    <th>Rating</th>
                    <th>Status</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr>
                        <td><strong>{{ $review->employee->full_name }}</strong></td>
                        <td>{{ $review->reviewedBy->full_name }}</td>
                        <td>{{ $review->review_date->format('M d, Y') }}</td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600;
                                @if($review->rating >= 4) background: rgba(16, 185, 129, 0.1); color: #10b981;
                                @elseif($review->rating >= 3) background: rgba(245, 158, 11, 0.1); color: #f59e0b;
                                @else background: rgba(239, 68, 68, 0.1); color: #ef4444;
                                @endif">
                                {{ $review->rating }}/5 <i class="fa-solid fa-star" style="font-size: 0.75rem;"></i>
                            </span>
                        </td>
                        <td>
                            <span style="padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; @if($review->status === 'completed') background: rgba(16, 185, 129, 0.1); color: #10b981; @else background: rgba(245, 158, 11, 0.1); color: #f59e0b; @endif">
                                {{ ucfirst($review->status) }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 0.5rem; justify-content: center; font-size: 0.85rem;">
                                <a href="{{ route('admin.hr.performance.edit', $review) }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">Edit</a>
                                <form action="{{ route('admin.hr.performance.destroy', $review) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: var(--danger); text-decoration: none; font-weight: 600; cursor: pointer;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No performance reviews found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 1.5rem;">
        {{ $reviews->links() }}
    </div>
</div>
@endsection
