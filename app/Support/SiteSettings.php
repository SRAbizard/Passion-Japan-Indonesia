<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Single read-side surface for everything edited under Admin → Website
 * Settings. Each accessor reads the Setting model first, falls back to
 * config/passion.php so the public site keeps working before any value
 * has been overridden.
 */
class SiteSettings
{
    /**
     * Contact value (email, phone, whatsapp).
     */
    public static function contact(string $key): ?string
    {
        return Setting::get("contact.{$key}", config("passion.contact.{$key}"));
    }

    /**
     * Social media URL (instagram, facebook, tiktok).
     */
    public static function social(string $key): ?string
    {
        return Setting::get("social.{$key}", config("passion.contact.{$key}"));
    }

    /**
     * Stat value (students, workers, companies).
     */
    public static function stat(string $key): ?string
    {
        return Setting::get("stats.{$key}", config("passion.stats.{$key}"));
    }

    /**
     * Office list. Each entry: {city, country, address, maps_url}
     * If maps_url is empty, an auto-generated Google Maps search URL
     * for the address is returned in its place.
     *
     * @return array<int, array{city: ?string, country: ?string, address: ?string, maps_url: string}>
     */
    public static function offices(): array
    {
        $stored = Setting::get('contact.offices', null);
        $offices = is_array($stored) ? $stored : config('passion.contact.offices', []);

        return collect($offices)
            ->filter(fn ($o) => filled($o['city'] ?? null) || filled($o['address'] ?? null))
            ->map(fn ($o) => [
                'city'     => $o['city']     ?? null,
                'country'  => $o['country']  ?? null,
                'address'  => $o['address']  ?? null,
                'maps_url' => filled($o['maps_url'] ?? null)
                    ? $o['maps_url']
                    : static::mapsUrlFor($o['address'] ?? trim(($o['city'] ?? '').', '.($o['country'] ?? ''), ', ')),
            ])
            ->values()
            ->all();
    }

    /**
     * Google Maps search URL for an arbitrary address string.
     */
    public static function mapsUrlFor(string $address): string
    {
        return 'https://www.google.com/maps/search/?api=1&query='.urlencode($address);
    }

    /**
     * Public URL for the uploaded Company Profile document, or null if
     * none has been uploaded yet.
     */
    public static function companyProfileUrl(): ?string
    {
        $path = Setting::get('company.profile_pdf_path');
        return $path ? asset('storage/'.$path) : null;
    }

    /**
     * Public URL for the uploaded background theme song, or null if
     * none uploaded. Plays as muted-autoplay on the homepage.
     */
    public static function themeAudioUrl(): ?string
    {
        $path = Setting::get('audio.theme_path');
        return $path ? asset('storage/'.$path) : null;
    }

    /**
     * Hero slideshow slides — admin can upload up to 4 images. Each
     * entry merges admin-uploaded image (if any) with a sensible
     * fallback Unsplash photo + brand-tinted background colour so
     * there's never an empty frame.
     *
     * @return array<int, array{image: string, color: string}>
     */
    public static function heroSlides(): array
    {
        $defaults = [
            ['image' => 'https://images.unsplash.com/photo-1480796927426-f609979314bd?auto=format&fit=crop&w=1920&q=70', 'color' => '#4a0f07'],
            ['image' => 'https://images.unsplash.com/photo-1522383225653-ed111181a951?auto=format&fit=crop&w=1920&q=70', 'color' => '#6d160a'],
            ['image' => 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?auto=format&fit=crop&w=1920&q=70', 'color' => '#2a0805'],
            ['image' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=1920&q=70', 'color' => '#0e1124'],
        ];

        $stored = Setting::get('hero.slides', null);
        if (! is_array($stored) || empty($stored)) {
            return $defaults;
        }

        // Fill up to 4 slots, falling back to defaults for empty slots.
        $out = [];
        for ($i = 0; $i < 4; $i++) {
            $slide     = $stored[$i] ?? null;
            $imagePath = $slide['image_path'] ?? null;
            $out[] = [
                'image' => $imagePath
                    ? asset('storage/'.$imagePath)
                    : $defaults[$i]['image'],
                'color' => $defaults[$i]['color'],
            ];
        }
        return $out;
    }
}
