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
}
