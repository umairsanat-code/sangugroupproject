<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Visa;
use App\Models\UmrahPackage;
use App\Models\Hotel;

class SanguDataSeeder extends Seeder
{
    public function run(): void
    {
        $visas = [
            ["slug" => "uae-visit-visa", "country" => "United Arab Emirates", "flag" => "🇦🇪", "type" => "Visit Visa", "duration" => "30 Days", "processing" => "3-4 Working Days", "price" => 18500, "entry" => "Single Entry", "validity" => "60 Days", "docs" => ["Passport (6 months valid)","2 Passport-size photos","CNIC copy","Bank statement (3 months)"], "desc" => "Explore Dubai and Abu Dhabi with a hassle-free 30-day UAE visit visa, processed and couriered to your door."],
            ["slug" => "saudi-visit-visa", "country" => "Saudi Arabia", "flag" => "🇸🇦", "type" => "Visit Visa", "duration" => "90 Days", "processing" => "5-7 Working Days", "price" => 22000, "entry" => "Multiple Entry", "validity" => "1 Year", "docs" => ["Passport (6 months valid)","2 Passport-size photos","CNIC copy","Confirmed return ticket"], "desc" => "A multiple-entry Saudi visit visa suited for business trips, family visits, or extended Umrah stopovers."],
            ["slug" => "uk-visit-visa", "country" => "United Kingdom", "flag" => "🇬🇧", "type" => "Standard Visitor", "duration" => "6 Months", "processing" => "15-20 Working Days", "price" => 48000, "entry" => "Single Entry", "validity" => "6 Months", "docs" => ["Passport","Bank statement (6 months)","Employment letter","Travel itinerary"], "desc" => "Full documentation support and interview guidance for a UK standard visitor visa application."],
            ["slug" => "schengen-visa", "country" => "Schengen (EU)", "flag" => "🇪🇺", "type" => "Tourist Visa", "duration" => "90 Days", "processing" => "10-15 Working Days", "price" => 36000, "entry" => "Single Entry", "validity" => "90 Days", "docs" => ["Passport","Travel insurance","Hotel booking proof","Bank statement"], "desc" => "Visit up to 27 European countries on one Schengen tourist visa, with appointment booking handled for you."],
            ["slug" => "turkey-visa", "country" => "Turkey", "flag" => "🇹🇷", "type" => "e-Visa", "duration" => "30 Days", "processing" => "1-2 Working Days", "price" => 9500, "entry" => "Multiple Entry", "validity" => "180 Days", "docs" => ["Passport (6 months valid)","Digital photo"], "desc" => "Fast-track Turkey e-Visa, ideal for Istanbul city breaks and Cappadocia tours."],
            ["slug" => "malaysia-visa", "country" => "Malaysia", "flag" => "🇲🇾", "type" => "eNTRI / Tourist", "duration" => "30 Days", "processing" => "2-3 Working Days", "price" => 8500, "entry" => "Single Entry", "validity" => "30 Days", "docs" => ["Passport","Passport photo","Return ticket copy"], "desc" => "Affordable Malaysia tourist visa processing for Kuala Lumpur and Genting getaways."],
        ];

        foreach ($visas as $visa) {
            Visa::create($visa);
        }

        $umrah = [
            ["slug" => "umrah-economy-10", "title" => "Umrah Economy Package", "nights" => "10 Nights", "city" => "Makkah & Madinah", "price" => 145000, "rating" => 4.4, "group" => false, "hotel" => "3-Star Hotels (800m from Haram)", "includes" => ["Return Airfare","Visa Processing","Hotel Stay","Ziyarat Tour","Airport Transfers"], "desc" => "A budget-friendly 10-night Umrah package with comfortable 3-star stays walking distance from both Haramain.", "duration_days" => 10],
            ["slug" => "umrah-premium-14", "title" => "Umrah Premium Package", "nights" => "14 Nights", "city" => "Makkah & Madinah", "price" => 265000, "rating" => 4.8, "group" => false, "hotel" => "5-Star Hotels (Haram View)", "includes" => ["Return Airfare","Visa Processing","5-Star Hotel","Private Ziyarat","VIP Transfers","Daily Breakfast"], "desc" => "Stay at Haram-facing 5-star hotels with private ziyarat tours and a dedicated group coordinator.", "duration_days" => 14],
            ["slug" => "umrah-ramadan-special", "title" => "Ramadan Umrah Special", "nights" => "15 Nights", "city" => "Makkah & Madinah", "price" => 310000, "rating" => 4.9, "group" => true, "hotel" => "4-Star Hotels (400m from Haram)", "includes" => ["Return Airfare","Visa Processing","Iftar & Suhoor","Ziyarat Tour","24/7 Group Guide"], "desc" => "Experience the blessed nights of Ramadan in Makkah with our fully-guided group Umrah program.", "duration_days" => 15],
            ["slug" => "umrah-family-group-10", "title" => "Family Group Umrah", "nights" => "10 Nights", "city" => "Makkah & Madinah", "price" => 158000, "rating" => 4.6, "group" => true, "hotel" => "4-Star Hotels (600m from Haram)", "includes" => ["Return Airfare","Visa Processing","Family Rooms","Ziyarat Tour","Group Muallim"], "desc" => "Travel together with an organised group of families, guided by an experienced Muallim throughout.", "duration_days" => 10],
        ];

        foreach ($umrah as $u) {
            UmrahPackage::create($u);
        }

        $hotels = [
            ["slug" => "swissotel-al-maqam-makkah", "name" => "Swissôtel Al Maqam Makkah", "city" => "Makkah", "stars" => 5, "distance" => "80m from Haram", "price" => 32000, "rating" => 4.7, "amenities" => ["Haram View","Free WiFi","Breakfast Included","24hr Front Desk","Prayer Room"], "desc" => "Directly connected to the Abraj Al-Bait complex with sweeping views of the Holy Haram."],
            ["slug" => "pullman-zamzam-madinah", "name" => "Pullman Zamzam Madinah", "city" => "Madinah", "stars" => 5, "distance" => "50m from Masjid Nabawi", "price" => 27500, "rating" => 4.6, "amenities" => ["Masjid View","Free WiFi","Breakfast Included","Airport Shuttle"], "desc" => "Steps away from Masjid an-Nabawi, offering spacious rooms and dedicated family suites."],
            ["slug" => "hilton-suites-makkah", "name" => "Hilton Suites Makkah", "city" => "Makkah", "stars" => 4, "distance" => "350m from Haram", "price" => 18500, "rating" => 4.3, "amenities" => ["Free WiFi","Breakfast Included","Shuttle Service","Prayer Room"], "desc" => "Comfortable suite-style rooms with a complimentary shuttle to the Haram every 15 minutes."],
            ["slug" => "dar-al-taqwa-madinah", "name" => "Dar Al Taqwa Hotel", "city" => "Madinah", "stars" => 4, "distance" => "120m from Masjid Nabawi", "price" => 15800, "rating" => 4.2, "amenities" => ["Masjid View","Free WiFi","24hr Front Desk"], "desc" => "A well-loved budget favourite just minutes on foot from the Prophet's Mosque."],
        ];

        foreach ($hotels as $h) {
            Hotel::create($h);
        }
    }
}
