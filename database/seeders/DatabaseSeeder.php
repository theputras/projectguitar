<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;
use App\Models\Tonewood;
use App\Models\Content;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // =============================================
        // Admin User
        // =============================================
        User::firstOrCreate(
            ['email' => 'admin@lynns.com'],
            [
                'name' => 'Admin Lynn\'s',
                'password' => Hash::make('password'),
            ]
        );

        // =============================================
        // FAQs
        // =============================================
        $faqs = [
            [
                'question' => 'How long does it take to build a custom instrument?',
                'answer' => 'Our standard build time is 3-6 months, depending on the complexity of the design and current queue. Rush orders may be available — please inquire for details.',
                'sort_order' => 1,
            ],
            [
                'question' => 'Do you ship internationally?',
                'answer' => 'Yes! We ship worldwide via DHL, FedEx, and UPS. All instruments are professionally packed with custom hardshell cases and full insurance coverage.',
                'sort_order' => 2,
            ],
            [
                'question' => 'What tonewoods do you use?',
                'answer' => 'We source premium tonewoods from sustainable suppliers worldwide such as Swamp Ash, Alder, Maple, Walnut, Wenge, Rosewood, and Ebony. Visit our Tonewoods page for full details.',
                'sort_order' => 3,
            ],
            [
                'question' => 'Can I choose my own specifications?',
                'answer' => 'Absolutely. Every build starts with a personal consultation where we discuss your preferences for wood, pickups, scale length, neck profile, finish, and any other custom requests.',
                'sort_order' => 4,
            ],
            [
                'question' => 'What is included in the price?',
                'answer' => 'Every instrument includes a premium hardshell case, setup to your preferred string gauge and action, certificate of authenticity, and care kit.',
                'sort_order' => 5,
            ],
            [
                'question' => 'Do you offer repairs or setups?',
                'answer' => 'We focus exclusively on building custom instruments. However, we can recommend trusted luthiers in your area for repairs and setups.',
                'sort_order' => 6,
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept bank transfer (wire), PayPal, and installment plans for custom orders. A 50% deposit is required to begin the build, with the remaining balance due before shipping.',
                'sort_order' => 7,
            ],
            [
                'question' => 'Is there a warranty?',
                'answer' => 'All Lynn\'s instruments come with a lifetime warranty against structural defects. Electronics and hardware carry a 2-year warranty.',
                'sort_order' => 8,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(['question' => $faq['question']], $faq);
        }

        // =============================================
        // Tonewoods
        // =============================================
        $tonewoods = [
            // Body woods
            [
                'name' => 'Swamp Ash', 'type' => 'body', 'origin' => 'North America',
                'description' => 'A lightweight tonewood with beautiful grain patterns, widely regarded for its balanced tonal characteristics.',
                'characteristics' => ['tone' => 'Bright, airy with pronounced mids', 'density' => 'Light to medium', 'workability' => 'Excellent', 'stability' => 'Very good', 'color' => 'Pale cream to tan with dramatic grain'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Alder', 'type' => 'body', 'origin' => 'North America / Europe',
                'description' => 'The classic electric guitar and bass body wood, known for its balanced, full-range tone.',
                'characteristics' => ['tone' => 'Balanced across all frequencies', 'density' => 'Medium', 'workability' => 'Excellent', 'stability' => 'Excellent', 'color' => 'Light brown with subtle grain'],
                'sort_order' => 2,
            ],
            [
                'name' => 'Black Walnut', 'type' => 'body', 'origin' => 'North America',
                'description' => 'A stunning dark wood delivering warm, rich tones with excellent sustain. Beautiful natural figure.',
                'characteristics' => ['tone' => 'Warm, rich with deep lows', 'density' => 'Medium-heavy', 'workability' => 'Good', 'stability' => 'Very good', 'color' => 'Deep chocolate brown to purple'],
                'sort_order' => 3,
            ],
            // Neck woods
            [
                'name' => 'Hard Maple', 'type' => 'neck', 'origin' => 'North America',
                'description' => 'The industry-standard neck wood providing bright, snappy tone and excellent stability.',
                'characteristics' => ['tone' => 'Bright, snappy, articulate', 'density' => 'High', 'workability' => 'Moderate', 'stability' => 'Excellent', 'color' => 'Pale cream to light amber'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Roasted Maple', 'type' => 'neck', 'origin' => 'North America',
                'description' => 'Thermally treated maple that offers enhanced stability and a gorgeous caramel color with vintage tone.',
                'characteristics' => ['tone' => 'Warm maple tone with vintage character', 'density' => 'Medium-high', 'workability' => 'Good', 'stability' => 'Superior (thermally treated)', 'color' => 'Caramel to dark amber'],
                'sort_order' => 2,
            ],
            [
                'name' => 'Wenge', 'type' => 'neck', 'origin' => 'Central Africa',
                'description' => 'A dense, exotic hardwood with a distinctive grain pattern and deep, punchy midrange tone.',
                'characteristics' => ['tone' => 'Deep, punchy mids with tight lows', 'density' => 'Very high', 'workability' => 'Difficult', 'stability' => 'Excellent', 'color' => 'Dark brown to black with striking grain'],
                'sort_order' => 3,
            ],
            // Fretboard woods
            [
                'name' => 'Indian Rosewood', 'type' => 'fretboard', 'origin' => 'India',
                'description' => 'A premium dark rosewood with rich character and warm sustain, the most popular fretboard wood worldwide.',
                'characteristics' => ['tone' => 'Warm, rich sustain', 'density' => 'High', 'workability' => 'Moderate', 'stability' => 'Excellent', 'color' => 'Dark brown with purple hues'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Ebony', 'type' => 'fretboard', 'origin' => 'Africa / Southeast Asia',
                'description' => 'The ultimate precision fretboard wood. Extremely dense, smooth, and articulate with a luxurious feel.',
                'characteristics' => ['tone' => 'Bright, articulate, fast attack', 'density' => 'Very high', 'workability' => 'Difficult', 'stability' => 'Excellent', 'color' => 'Jet black to dark brown'],
                'sort_order' => 2,
            ],
            [
                'name' => 'Pau Ferro', 'type' => 'fretboard', 'origin' => 'South America',
                'description' => 'A sustainable alternative to rosewood with similar tonal qualities and beautiful orangey-brown figure.',
                'characteristics' => ['tone' => 'Balanced between rosewood and ebony', 'density' => 'Medium-high', 'workability' => 'Good', 'stability' => 'Very good', 'color' => 'Orange-brown with dark streaks'],
                'sort_order' => 3,
            ],
        ];

        foreach ($tonewoods as $wood) {
            Tonewood::firstOrCreate(['name' => $wood['name']], $wood);
        }

        // =============================================
        // CMS Content
        // =============================================
        $contents = [
            [
                'title' => 'Our Story',
                'slug' => 'our-story',
                'section' => 'about',
                'content' => 'Lynn\'s Bass & Guitar was born from a deep passion for music and woodcraft. Based in Bali, Indonesia, we handcraft every instrument with meticulous attention to detail, combining traditional luthier techniques with modern precision. Each piece that leaves our workshop is a unique expression of artistry and engineering, built to inspire musicians around the world.',
            ],
            [
                'title' => 'Our Craftsmanship',
                'slug' => 'our-craftsmanship',
                'section' => 'about',
                'content' => 'Every instrument begins with carefully selected, sustainably sourced tonewoods. Our master luthiers shape, carve, and finish each piece entirely by hand, ensuring the highest level of quality and sonic performance. From the initial consultation to the final setup, we pour our heart and expertise into every detail — because your instrument deserves nothing less.',
            ],
            [
                'title' => 'Worldwide Shipping Information',
                'slug' => 'shipping-info',
                'section' => 'shipping',
                'content' => 'We ship worldwide via trusted carriers including DHL Express, FedEx International, and UPS. Every instrument is professionally packed in a premium hardshell case with custom foam inserts and double-boxed for maximum protection. Full insurance is included with every shipment. Typical delivery times: USA & Canada (5-7 business days), Europe (5-10 business days), Asia Pacific (3-5 business days), Rest of World (7-14 business days).',
            ],
            [
                'title' => 'Custom Order Process',
                'slug' => 'custom-order-process',
                'section' => 'custom_order',
                'content' => 'Building your dream instrument is a collaborative journey. Step 1: Consultation — We discuss your musical needs, preferences, and vision. Step 2: Design — We finalize specifications and create detailed blueprints. Step 3: Build — Our luthiers handcraft your instrument with precision and care. Step 4: Quality Check — Rigorous inspection and professional setup. Step 5: Shipping — Safely delivered to your doorstep anywhere in the world.',
            ],
        ];

        foreach ($contents as $content) {
            Content::firstOrCreate(['slug' => $content['slug']], $content);
        }
    }
}
