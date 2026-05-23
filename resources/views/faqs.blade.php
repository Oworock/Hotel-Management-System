@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <div class="container mx-auto px-4 py-16 max-w-3xl">
        <h1 class="text-4xl font-bold text-gray-900 text-center mb-4">Frequently Asked Questions</h1>
        <p class="text-xl text-gray-600 text-center mb-12">Find answers to common questions about our hotel and booking process.</p>

        <div class="space-y-4">
            @foreach($faqs as $faq)
            <details class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <summary class="px-6 py-4 cursor-pointer font-semibold text-gray-800 hover:text-blue-600 transition-colors flex justify-between items-center">
                    {{ $faq->question }}
                    <span class="text-xl">+</span>
                </summary>
                <div class="px-6 pb-4 text-gray-700 border-t border-gray-200 pt-4">
                    {{ $faq->answer }}
                </div>
            </details>
            @endforeach

            @if($faqs->isEmpty())
            <div class="bg-gray-100 rounded-lg p-12 text-center">
                <p class="text-gray-600 text-lg">No FAQs available at the moment.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
