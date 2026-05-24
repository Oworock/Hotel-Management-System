@extends('layouts.frontend')

@section('title', 'Contact Us')

@section('styles')
<style>
    .contact-hero {
        position: relative;
        padding: 5rem 2rem;
        background: linear-gradient(135deg, rgba(110, 68, 255, 0.05) 0%, rgba(244, 68, 150, 0.05) 100%), var(--surface);
        text-align: center;
        border-bottom: 1px solid var(--border-color);
    }
    
    .contact-container {
        max-width: 1200px;
        margin: 5rem auto;
        padding: 0 1.5rem;
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 4rem;
    }
    
    @media (max-width: 768px) {
        .contact-container {
            grid-template-columns: 1fr;
            gap: 2.5rem;
        }
    }
    
    .contact-sidebar {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    
    .info-card {
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 2rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
    }
    
    .info-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--primary-glow);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .info-title {
        font-weight: 700;
        font-size: 1.05rem;
        margin-bottom: 0.25rem;
    }
    
    .info-desc {
        color: var(--text-secondary);
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    .form-panel {
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 3rem;
        box-shadow: var(--shadow-md);
        position: relative;
    }

    @media (max-width: 768px) {
        .form-panel {
            padding: 2rem;
        }
    }
    
    .success-alert {
        display: none;
        background: var(--success-glow);
        border: 1px solid var(--success);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        color: var(--text-primary);
        margin-bottom: 2rem;
        align-items: center;
        gap: 1rem;
        animation: fadeIn 0.4s ease forwards;
    }

    .success-alert i {
        font-size: 1.5rem;
        color: var(--success);
    }
</style>
@endsection

@section('content')
    <header class="contact-hero animate-fade-in">
        <div class="container" style="max-width: 800px;">
            <span class="section-tag">Reach Out</span>
            <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;">Get In Touch With Us</h1>
            <p style="font-size: 1.2rem; color: var(--text-secondary); line-height: 1.6;">
                Have questions or special request considerations? We are here to help you 24/7.
            </p>
        </div>
    </header>

    <div class="contact-container animate-fade-in">
        <div class="contact-sidebar">
            <div class="info-card">
                <div class="info-icon">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <div>
                    <h3 class="info-title">Phone Number</h3>
                    <p class="info-desc">{{ \App\Models\Setting::getValue('contact_phone', '+1 (555) 123-4567') }}</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon" style="background: var(--secondary-glow); color: var(--secondary);">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div>
                    <h3 class="info-title">Email Address</h3>
                    <p class="info-desc">{{ \App\Models\Setting::getValue('contact_email', 'info@aetheriagrand.com') }}</p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-icon">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div>
                    <h3 class="info-title">Resort Location</h3>
                    <p class="info-desc">{{ \App\Models\Setting::getValue('physical_address', 'Golden Coast Beach Boulevard, Suite A, Victoria') }}</p>
                </div>
            </div>
            
            <div class="map-wrapper" style="height: 250px;">
                @php
                    $mapAddress = \App\Models\Setting::getValue('map_address');
                    preg_match('/src=["\']([^"\']+)["\']/', (string) $mapAddress, $mapMatch);
                    $mapSrc = $mapMatch[1] ?? $mapAddress;
                @endphp
                @if($mapSrc && (str_starts_with($mapSrc, 'http://') || str_starts_with($mapSrc, 'https://')))
                    <iframe src="{{ $mapSrc }}" allowfullscreen="" loading="lazy"></iframe>
                @else
                    <div style="width:100%; height:100%; background: linear-gradient(135deg, var(--background) 0%, var(--border-color) 100%); display: flex; flex-direction:column; align-items: center; justify-content: center; color: var(--text-secondary); text-align: center; padding: 1.5rem;">
                        <i class="fa-solid fa-map-location-dot" style="font-size: 2.5rem; margin-bottom: 0.75rem; color: var(--primary);"></i>
                        <h4 style="font-size: 0.95rem; margin-bottom: 0.25rem;">Map Not Configured</h4>
                        <p style="font-size: 0.75rem;">
                            {{ \App\Models\Setting::getValue('physical_address', 'Golden Coast Beach Boulevard, Suite A, Victoria') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="form-panel">
            <div class="success-alert" id="success-alert">
                <i class="fa-solid fa-circle-check"></i>
                <div>
                    <h4 style="font-weight:700; margin-bottom: 0.25rem;">Message Sent Successfully!</h4>
                    <p style="font-size: 0.85rem; color: var(--text-secondary);">Thank you for contacting Aetheria. A guest experience officer will respond to your email within 24 hours.</p>
                </div>
            </div>

            <form id="contact-form" onsubmit="handleContactSubmit(event)">
                <h2 style="font-size: 1.75rem; margin-bottom: 2rem; font-weight: 700;">Send Us a Message</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" id="name" class="form-control" placeholder="John Doe" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" class="form-control" placeholder="john@example.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" id="subject" class="form-control" placeholder="How can we assist you?" required>
                </div>

                <div class="form-group">
                    <label for="message" class="form-label">Message Details</label>
                    <textarea id="message" class="form-control" rows="5" placeholder="Tell us more about your reservation, feedback, or inquiries..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 600; margin-top: 1rem;">
                    <i class="fa-solid fa-paper-plane" style="margin-right: 0.5rem;"></i> Send Message
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function handleContactSubmit(event) {
        event.preventDefault();
        
        // Show success alert
        const alert = document.getElementById('success-alert');
        alert.style.display = 'flex';
        
        // Clear form
        document.getElementById('contact-form').reset();
        
        // Scroll to top of the form panel to see the success message
        document.querySelector('.form-panel').scrollIntoView({ behavior: 'smooth' });
    }
</script>
@endsection
