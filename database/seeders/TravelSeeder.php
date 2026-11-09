<?php

namespace Database\Seeders;

use App\Models\Airline;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\UmrahPackage;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class TravelSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Airlines
        $airlines = [
            ['name' => 'Emirates', 'code' => 'EK', 'logo_path' => 'https://upload.wikimedia.org/wikipedia/commons/d/d0/Emirates_logo.svg'],
            ['name' => 'Qatar Airways', 'code' => 'QR', 'logo_path' => 'https://upload.wikimedia.org/wikipedia/en/9/9b/Qatar_Airways_Logo.svg'],
            ['name' => 'Saudia', 'code' => 'SV', 'logo_path' => 'https://upload.wikimedia.org/wikipedia/en/4/4b/Saudia_Logo.svg'],
            ['name' => 'Pakistan International Airlines', 'code' => 'PK', 'logo_path' => 'https://upload.wikimedia.org/wikipedia/commons/2/2e/PIA_logo.svg'],
        ];

        foreach ($airlines as $airline) {
            Airline::updateOrCreate(['code' => $airline['code']], $airline);
        }

        // 2. Seed Vendors
        $hotelVendor = Vendor::updateOrCreate(
            ['name' => 'Global Hospitality Partners'],
            ['type' => 'Hotel', 'contact_info' => 'contact@globalhospitality.com']
        );

        // 3. Seed Destinations
        $destinations = [
            [
                'name' => 'Paris',
                'slug' => 'paris',
                'country' => 'France',
                'description' => 'The City of Light, home to the Eiffel Tower, the Louvre, and world-class dining.',
                'image_path' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=1200&q=80',
                'is_featured' => true
            ],
            [
                'name' => 'Dubai',
                'slug' => 'dubai',
                'country' => 'UAE',
                'description' => 'A futuristic metropolis known for luxury shopping, ultramodern architecture, and a lively nightlife scene.',
                'image_path' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=1200&q=80',
                'is_featured' => true
            ],
            [
                'name' => 'Istanbul',
                'slug' => 'istanbul',
                'country' => 'Turkey',
                'description' => 'A major city in Turkey that straddles Europe and Asia across the Bosphorus Strait.',
                'image_path' => 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?w=1200&q=80',
                'is_featured' => true
            ],
            [
                'name' => 'Santorini',
                'slug' => 'santorini',
                'country' => 'Greece',
                'description' => 'One of the Cyclades islands in the Aegean Sea, known for its whitewashed houses and stunning sunsets.',
                'image_path' => 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=1200&q=80',
                'is_featured' => true
            ],
            [
                'name' => 'London',
                'slug' => 'london',
                'country' => 'UK',
                'description' => 'The capital of England and the UK, a 21st-century city with history stretching back to Roman times.',
                'image_path' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=1200&q=80',
                'is_featured' => false
            ],
            [
                'name' => 'Swiss Alps',
                'slug' => 'swiss-alps',
                'country' => 'Switzerland',
                'description' => 'The Swiss Alps are part of the Alpine mountain range and are world-renowned for skiing and hiking.',
                'image_path' => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=1200&q=80',
                'is_featured' => true
            ],
        ];

        foreach ($destinations as $dest) {
            Destination::updateOrCreate(['slug' => $dest['slug']], $dest);
        }

        // 4. Seed Umrah Packages
        $umrahPackages = [
            [
                'title' => 'Premium Ramadan Umrah Package',
                'duration_days' => 14,
                'price' => 3500.00,
                'hotel_details' => '5-Star Clock Tower Makkah, 5-Star Frontel Madinah',
                'transport_details' => 'VIP Private Transfers, Economy Class Flights Included',
                'visa_included' => true,
                'image_path' => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=1200&q=80',
                'description' => 'Our most exclusive Ramadan package with direct Haram views and premium hospitality.'
            ],
            [
                'title' => 'Economy Family Umrah',
                'duration_days' => 21,
                'price' => 1850.00,
                'hotel_details' => '4-Star Hotels with Shuttle Service',
                'transport_details' => 'Shared Bus Transfers, Flights Excluded',
                'visa_included' => true,
                'image_path' => 'https://images.unsplash.com/photo-1564769625905-50e9363df7a7?w=1200&q=80',
                'description' => 'A budget-friendly option for families seeking a longer stay in the holy cities.'
            ],
            [
                'title' => 'Standard Spiritual Journey',
                'duration_days' => 10,
                'price' => 2200.00,
                'hotel_details' => 'Walking distance hotels in Makkah and Madinah',
                'transport_details' => 'Comfortable Private Car, Economy Flights',
                'visa_included' => true,
                'image_path' => 'https://images.unsplash.com/photo-1542810634-71277d95dcbb?w=1200&q=80',
                'description' => 'A balanced package for individuals looking for comfort and convenience.'
            ],
        ];

        foreach ($umrahPackages as $pkg) {
            UmrahPackage::updateOrCreate(['title' => $pkg['title']], $pkg);
        }

        // 5. Seed Hotels
        $hotels = [
            [
                'name' => 'Burj Al Arab Jumeirah',
                'city' => 'Dubai',
                'location' => 'Jumeirah St - Umm Suqeim 3',
                'stars' => 5,
                'vendor_id' => $hotelVendor->id,
            ],
            [
                'name' => 'The Ritz Paris',
                'city' => 'Paris',
                'location' => '15 Place Vendôme',
                'stars' => 5,
                'vendor_id' => $hotelVendor->id,
            ],
            [
                'name' => 'Hilton Makkah Convention',
                'city' => 'Makkah',
                'location' => 'Jabal Omar',
                'stars' => 5,
                'vendor_id' => $hotelVendor->id,
            ],
            [
                'name' => 'Swissotel Al Maqam',
                'city' => 'Makkah',
                'location' => 'Abraj Al Bait',
                'stars' => 5,
                'vendor_id' => $hotelVendor->id,
            ],
        ];

        foreach ($hotels as $hotel) {
            Hotel::updateOrCreate(['name' => $hotel['name']], $hotel);
        }
    }
}
