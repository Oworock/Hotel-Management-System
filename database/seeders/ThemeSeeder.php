<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            [
                'name' => 'Modern Minimal',
                'slug' => 'modern-minimal',
                'description' => 'Clean and contemporary design with minimalist aesthetics',
                'is_default' => true,
                'is_active' => true,
                'author' => 'StayFlow',
                'version' => '1.0.0',
                'colors' => [
                    'primary' => '#3B82F6',
                    'secondary' => '#1F2937',
                    'accent' => '#10B981',
                    'background' => '#F9FAFB',
                    'text' => '#111827',
                ],
                'settings' => [
                    'header_style' => 'modern',
                    'font_family' => 'Inter, sans-serif',
                    'border_radius' => 'rounded-lg',
                    'shadow_style' => 'soft',
                ]
            ],
            [
                'name' => 'Luxury Premium',
                'slug' => 'luxury-premium',
                'description' => 'Elegant and sophisticated design for luxury properties',
                'is_default' => false,
                'is_active' => true,
                'author' => 'StayFlow',
                'version' => '1.0.0',
                'colors' => [
                    'primary' => '#D4AF37',
                    'secondary' => '#1A1A1A',
                    'accent' => '#C9A961',
                    'background' => '#F5F5F0',
                    'text' => '#2C2C2C',
                ],
                'settings' => [
                    'header_style' => 'luxury',
                    'font_family' => 'Playfair Display, serif',
                    'border_radius' => 'rounded-none',
                    'shadow_style' => 'deep',
                ]
            ],
            [
                'name' => 'Boutique Charm',
                'slug' => 'boutique-charm',
                'description' => 'Warm and artistic design perfect for boutique hotels',
                'is_default' => false,
                'is_active' => true,
                'author' => 'StayFlow',
                'version' => '1.0.0',
                'colors' => [
                    'primary' => '#E8936D',
                    'secondary' => '#4A3728',
                    'accent' => '#F5B99A',
                    'background' => '#FFF8F3',
                    'text' => '#3D2817',
                ],
                'settings' => [
                    'header_style' => 'artistic',
                    'font_family' => 'Josefin Sans, sans-serif',
                    'border_radius' => 'rounded-xl',
                    'shadow_style' => 'warm',
                ]
            ],
            [
                'name' => 'Resort Vibrant',
                'slug' => 'resort-vibrant',
                'description' => 'Vibrant and tropical design for resort properties',
                'is_default' => false,
                'is_active' => true,
                'author' => 'StayFlow',
                'version' => '1.0.0',
                'colors' => [
                    'primary' => '#0EA5E9',
                    'secondary' => '#06B6D4',
                    'accent' => '#EC4899',
                    'background' => '#ECFDF5',
                    'text' => '#065F46',
                ],
                'settings' => [
                    'header_style' => 'tropical',
                    'font_family' => 'Poppins, sans-serif',
                    'border_radius' => 'rounded-2xl',
                    'shadow_style' => 'bright',
                ]
            ]
        ];

        foreach ($themes as $theme) {
            Theme::firstOrCreate(
                ['slug' => $theme['slug']],
                $theme
            );
        }
    }
}
