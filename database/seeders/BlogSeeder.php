<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $blogs = [
            [
                'slug' => 'first-time-umrah-guide-' . Str::random(4),
                'title' => "A First-Timer's Complete Guide to Umrah",
                'tag' => 'Umrah Guide',
                'excerpt' => 'Everything you need to know before stepping into Ihram for the first time — rituals, etiquette and packing tips.',
                'content' => '<h2>Preparing for Your First Umrah</h2>
<p>Embarking on your first Umrah is a deeply spiritual experience. This guide will walk you through every step, from preparation to completing the rituals with confidence.</p>

<h3>Before You Leave Home</h3>
<ul>
<li>Ensure your passport is valid for at least 6 months</li>
<li>Book your flights and accommodation well in advance</li>
<li>Pack comfortable, modest clothing and essential toiletries</li>
<li>Learn the basic Duas and rituals of Umrah</li>
</ul>

<h2>The Rituals of Umrah</h2>
<p>Umrah consists of four main rituals that must be performed in order:</p>
<ol>
<li><strong>Ihram</strong> — Enter the state of Ihram at the designated Miqat point</li>
<li><strong>Tawaf</strong> — Circumambulate the Kaaba seven times</li>
<li><strong>Sa\'i</strong> — Walk seven times between Safa and Marwa</li>
<li><strong>Taqseer</strong> — Shave or trim the hair</li>
</ol>

<h3>Etiquette and Tips</h3>
<p>Stay hydrated, be patient with crowds, and keep your intention pure throughout the journey. Remember, Umrah is about spiritual renewal — take your time and absorb every moment.</p>

<blockquote>The best Umrah is one performed with sincerity, patience, and gratitude.</blockquote>

<h2>What to Pack</h2>
<ul>
<li>Ihram clothing (for men) or modest white clothing</li>
<li>Comfortable sandals</li>
<li>Unscented toiletries</li>
<li>A small bag for Tawaf</li>
<li>Medications if needed</li>
</ul>',
                'author' => 'Sangu Travel Team',
                'is_active' => true,
                'display_order' => 1,
                'published_at' => '2026-08-12',
                'image_url' => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?q=80&w=800&auto=format&fit=crop',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'uae-visa-documents-2026-' . Str::random(4),
                'title' => 'UAE Visit Visa: Documents Checklist for 2026',
                'tag' => 'Visa Tips',
                'excerpt' => 'Avoid rejection with this up-to-date checklist of documents required for a smooth UAE visit visa approval.',
                'content' => '<h2>Updated UAE Visa Requirements</h2>
<p>The UAE frequently updates its visa requirements. Here is the latest checklist for 2026 to ensure your application is approved without hassle.</p>

<h3>Essential Documents</h3>
<ul>
<li>Original passport with at least 6 months validity</li>
<li>Passport-size photographs (white background, recent)</li>
<li>Completed visa application form</li>
<li>Proof of accommodation (hotel booking or host invitation)</li>
<li>Return flight ticket</li>
<li>Proof of sufficient funds (bank statement for last 3 months)</li>
</ul>

<h2>Common Reasons for Rejection</h2>
<ol>
<li><strong>Incomplete documentation</strong> — Ensure every document is complete and clear</li>
<li><strong>Invalid passport</strong> — Must be valid for at least 6 months from travel date</li>
<li><strong>Previous overstay</strong> — If you have overstayed before, you may need additional documentation</li>
<li><strong>Insufficient funds</strong> — Bank statements should show consistent income</li>
</ol>

<h3>Processing Time</h3>
<p>Standard processing takes 3-5 working days. Express processing is available for an additional fee and takes 1-2 working days.</p>

<blockquote>Apply at least 2 weeks before your intended travel date to avoid last-minute stress.</blockquote>',
                'author' => 'Sangu Travel Team',
                'is_active' => true,
                'display_order' => 2,
                'published_at' => '2026-08-03',
                'image_url' => 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=800&auto=format&fit=crop',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'best-time-to-book-umrah-' . Str::random(4),
                'title' => 'When Is the Best Time to Book Your Umrah Trip?',
                'tag' => 'Planning',
                'excerpt' => 'Compare Ramadan, Shawwal and off-peak seasons to find the best value and least crowded time to travel.',
                'content' => '<h2>Choosing the Right Season for Umrah</h2>
<p>Timing your Umrah trip can significantly affect your experience and budget. Here is a breakdown of each season to help you decide.</p>

<h3>Ramadan Umrah</h3>
<p>The most spiritually rewarding time to perform Umrah. However, it is also the busiest and most expensive period. Hotels near the Haram are in high demand and prices can be 2-3x higher than off-peak.</p>

<h3>Off-Peak Season (Shawwal to Dhul Qadah)</h3>
<p>Immediately after Ramadan, the crowds thin out significantly. This is an excellent time for those who want a more peaceful experience at lower prices.</p>

<h2>Price Comparison by Season</h2>
<ul>
<li><strong>Ramadan:</strong> Premium pricing, book 3-6 months in advance</li>
<li><strong>Shawwal:</strong> Moderate pricing, 30-40% less than Ramadan</li>
<li><strong>Dhul Hijjah:</strong> High demand due to Hajj proximity</li>
<li><strong>Rabi ul Awal:</strong> Good balance of price and experience</li>
<li><strong>Off-peak (other months):</strong> Best value, least crowded</li>
</ul>

<h3>Our Recommendation</h3>
<p>If budget is a concern, consider Shawwal or the months of Rajab and Sha\'ban. You will enjoy lower prices, shorter wait times at the Haram, and a more intimate spiritual experience.</p>',
                'author' => 'Sangu Travel Team',
                'is_active' => true,
                'display_order' => 3,
                'published_at' => '2026-07-27',
                'image_url' => 'https://images.unsplash.com/photo-1519817650390-64a93db51149?q=80&w=800&auto=format&fit=crop',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'ziyarat-places-madinah-' . Str::random(4),
                'title' => '7 Ziyarat Places You Must Visit in Madinah',
                'tag' => 'Ziyarat',
                'excerpt' => 'From Masjid Quba to Jabal Uhud — a guide to the historic sites every visitor to Madinah should see.',
                'content' => '<h2>Sacred Sites of Madinah</h2>
<p>Madinah Al Munawwarah is home to some of the most significant historical and spiritual sites in Islam. Here are the 7 must-visit Ziyarat places during your stay.</p>

<h2>1. Masjid Quba</h2>
<p>The first mosque built in Islam. The Prophet Muhammad (peace be upon him) laid its foundation when he arrived in Madinah. Praying two Rakat here is equivalent to performing an Umrah in reward.</p>

<h2>2. Jabal Uhud (Mount Uhud)</h2>
<p>The site of the historic Battle of Uhud. Visit the martyr\'s cemetery and pay respects to Hamza ibn Abdul-Muttalib (RA) and other companions who gave their lives here.</p>

<h2>3. Masjid Qiblatain</h2>
<p>The mosque where the direction of prayer was changed from Jerusalem to Makkah. A significant turning point in Islamic history.</p>

<h2>4. Al-Baqi Cemetery</h2>
<p>The resting place of many companions of the Prophet (PBUH) and members of his family. A place for quiet reflection and prayer.</p>

<h2>5. Date Markets of Madinah</h2>
<p>Madinah is famous for its delicious dates. Visit the local markets to sample and purchase the finest Ajwa and other varieties.</p>

<h2>6. The Prophet\'s Mosque (Al-Masjid an-Nabawi)</h2>
<p>One of the largest mosques in the world, featuring the Green Dome over the Prophet\'s (PBUH) grave. Spend time in prayer and reflection here.</p>

<h2>7. Seven Mosques (Sab\'a Masajid)</h2>
<p>A collection of small historical mosques near Mount Uhud, each marking a significant event during the Battle of the Trench.</p>

<blockquote>Madinah is a city of peace. When you enter it, you feel a tranquility that is unlike any other place on earth.</blockquote>',
                'author' => 'Sangu Travel Team',
                'is_active' => true,
                'display_order' => 4,
                'published_at' => '2026-07-14',
                'image_url' => 'https://images.unsplash.com/photo-1564769662853-9fefe8a4e0b8?q=80&w=800&auto=format&fit=crop',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($blogs as $blog) {
            DB::table('blogs')->updateOrInsert(
                ['title' => $blog['title']],
                $blog
            );
        }
    }
}
