<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            // Original 4 Themes
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
                'is_active' => false,
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
                'is_active' => false,
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
                'is_active' => false,
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
            ],
            // New 5 Themes
            [
                'name' => 'Urban Elite',
                'slug' => 'urban-elite',
                'description' => 'Sleek and professional design for urban business hotels',
                'is_default' => false,
                'is_active' => false,
                'author' => 'StayFlow',
                'version' => '1.0.0',
                'colors' => [
                    'primary' => '#1F2937',
                    'secondary' => '#374151',
                    'accent' => '#F59E0B',
                    'background' => '#F3F4F6',
                    'text' => '#111827',
                ],
                'settings' => [
                    'header_style' => 'corporate',
                    'font_family' => 'IBM Plex Sans, sans-serif',
                    'border_radius' => 'rounded-md',
                    'shadow_style' => 'sharp',
                ]
            ],
            [
                'name' => 'Nature Green',
                'slug' => 'nature-green',
                'description' => 'Eco-friendly design inspired by nature and sustainability',
                'is_default' => false,
                'is_active' => false,
                'author' => 'StayFlow',
                'version' => '1.0.0',
                'colors' => [
                    'primary' => '#059669',
                    'secondary' => '#065F46',
                    'accent' => '#10B981',
                    'background' => '#F0FDF4',
                    'text' => '#064E3B',
                ],
                'settings' => [
                    'header_style' => 'organic',
                    'font_family' => 'Segoe UI, sans-serif',
                    'border_radius' => 'rounded-xl',
                    'shadow_style' => 'soft',
                ]
            ],
            [
                'name' => 'Sunset Romance',
                'slug' => 'sunset-romance',
                'description' => 'Romantic and warm design with sunset color palette',
                'is_default' => false,
                'is_active' => false,
                'author' => 'StayFlow',
                'version' => '1.0.0',
                'colors' => [
                    'primary' => '#F97316',
                    'secondary' => '#92400E',
                    'accent' => '#EA580C',
                    'background' => '#FEF3C7',
                    'text' => '#7C2D12',
                ],
                'settings' => [
                    'header_style' => 'romantic',
                    'font_family' => 'Lora, serif',
                    'border_radius' => 'rounded-3xl',
                    'shadow_style' => 'warm',
                ]
            ],
            [
                'name' => 'Ocean Blue',
                'slug' => 'ocean-blue',
                'description' => 'Peaceful ocean-inspired design for coastal resorts',
                'is_default' => false,
                'is_active' => false,
                'author' => 'StayFlow',
                'version' => '1.0.0',
                'colors' => [
                    'primary' => '#0369A1',
                    'secondary' => '#082F49',
                    'accent' => '#0EA5E9',
                    'background' => '#F0F9FF',
                    'text' => '#0C2340',
                ],
                'settings' => [
                    'header_style' => 'aquatic',
                    'font_family' => 'Nunito, sans-serif',
                    'border_radius' => 'rounded-2xl',
                    'shadow_style' => 'bright',
                ]
            ],
            [
                'name' => 'Royal Purple',
                'slug' => 'royal-purple',
                'description' => 'Sophisticated purple theme for upscale properties',
                'is_default' => false,
                'is_active' => false,
                'author' => 'StayFlow',
                'version' => '1.0.0',
                'colors' => [
                    'primary' => '#7C3AED',
                    'secondary' => '#581C87',
                    'accent' => '#A78BFA',
                    'background' => '#FAF5FF',
                    'text' => '#3F0F5C',
                ],
                'settings' => [
                    'header_style' => 'regal',
                    'font_family' => 'Cinzel, serif',
                    'border_radius' => 'rounded-lg',
                    'shadow_style' => 'deep',
                ]
            ],
        ];

        foreach ($themes as $theme) {
            Theme::updateOrCreate(['slug' => $theme['slug']], $theme);
        }

        Theme::where('slug', '!=', 'modern-minimal')->update(['is_active' => false]);
        Theme::where('slug', 'modern-minimal')->update(['is_active' => true, 'is_default' => true]);
    }
}
