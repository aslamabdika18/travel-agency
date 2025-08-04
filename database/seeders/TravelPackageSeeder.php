<?php

namespace Database\Seeders;

use App\Models\TravelPackage;
use App\Models\Category;
use App\Models\Itinerary;
use App\Models\TravelInclude;
use App\Models\TravelExclude;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TravelPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $adventureCategory = Category::where('name', 'Adventure')->first();
        $culturalCategory = Category::where('name', 'Cultural')->first();
        $ecoTourismCategory = Category::where('name', 'Eco-Tourism')->first();
        $photographyCategory = Category::where('name', 'Photography')->first();
        $beachIslandCategory = Category::where('name', 'Beach & Island')->first();
        $culinaryCategory = Category::where('name', 'Culinary')->first();

        $travelPackages = [
            [
                'name' => 'PT Sumatra Tour Travel Paradise Explorer',
            'description' => 'Explore the beauty of 99 islands with PT Sumatra Tour Travel, Aceh Singkil, dubbed the "Maldives of Aceh". Enjoy crystal clear seawater, white sand beaches, pristine coral reefs, and traditional fishing culture across 17 tourist-favorite islands.',
                'price' => 1850000,
                'base_person_count' => 2,
                'additional_person_price' => 750000,
                'capacity' => 12,
                'duration' => '4 Days 3 Nights',
                'tax_percentage' => 10.00,
                'category_id' => $beachIslandCategory->id
            ],
            [
                'name' => 'Panjang Island Hopping Adventure',
                'description' => 'Island hopping adventure to Panjang Island and surroundings offering the most complete tourist attractions with PT Sumatra Tour Travel. Snorkeling, diving, fishing, and enjoying spectacular sunsets in the Indian Ocean.',
                'price' => 2150000,
                'base_person_count' => 2,
                'additional_person_price' => 850000,
                'capacity' => 10,
                'duration' => '3 Days 2 Nights',
                'tax_percentage' => 11.00,
                'category_id' => $adventureCategory->id
            ],
            [
                'name' => 'Nibung Bay Turtle Conservation',
                'description' => 'Visit the turtle conservation center at Nibung Bay, Aceh Singkil. Witness turtle egg hatching, baby turtle release to the sea, and learn about turtle conservation efforts while enjoying the beauty of pristine beaches.',
                'price' => 1650000,
                'base_person_count' => 2,
                'additional_person_price' => 650000,
                'capacity' => 8,
                'duration' => '3 Days 2 Nights',
                'tax_percentage' => 10.50,
                'category_id' => $ecoTourismCategory->id
            ],
            [
                'name' => 'Tuangku Island Diving Adventure',
                'description' => 'Explore the underwater paradise of Tuangku Island with world-class diving spots. Encounter marine biodiversity, pristine coral reefs, and enjoy the stunning clarity of tropical seawater.',
                'price' => 2350000,
                'base_person_count' => 2,
                'additional_person_price' => 950000,
                'capacity' => 8,
                'duration' => '3 Days 2 Nights',
                'tax_percentage' => 12.00,
                'category_id' => $adventureCategory->id
            ],
            [
                'name' => 'Aceh Singkil Cultural Heritage',
                'description' => 'Experience authentic coastal life in Aceh Singkil. Learn traditional fishing techniques, explore local markets, enjoy fresh seafood cuisine, and discover the rich maritime heritage of this charming coastal town.',
                'price' => 1350000,
                'base_person_count' => 2,
                'additional_person_price' => 550000,
                'capacity' => 12,
                'duration' => '2 Days 1 Night',
                'tax_percentage' => 9.50,
                'category_id' => $culturalCategory->id
            ],
            [
                'name' => 'Bangkaru Island Eco-Tourism',
                'description' => 'Eco-friendly tourism to Bangkaru Island, one of Indonesia\'s most important turtle research sites. Join conservation programs, beach cleanup, and support sustainable ecotourism with local communities.',
                'price' => 2250000,
                'base_person_count' => 2,
                'additional_person_price' => 900000,
                'capacity' => 6,
                'duration' => '4 Days 3 Nights',
                'tax_percentage' => 11.50,
                'category_id' => $ecoTourismCategory->id
            ],
            [
                'name' => 'Aceh Singkil Mangrove Exploration',
                'description' => 'Navigate through pristine mangrove forests of Aceh Singkil with traditional boats. Discover unique wildlife, learn about mangrove ecosystems, and support community-based conservation initiatives.',
                'price' => 1450000,
                'base_person_count' => 2,
                'additional_person_price' => 600000,
                'capacity' => 10,
                'duration' => '2 Days 1 Night',
                'tax_percentage' => 9.00,
                'category_id' => $ecoTourismCategory->id
            ],
            [
                'name' => 'PT Sumatra Tour Travel Photography Tour',
            'description' => 'Professional photography workshop at the most photogenic locations with PT Sumatra Tour Travel. Capture stunning landscapes, underwater scenes, cultural moments, and receive guidance from expert photographers.',
                'price' => 2450000,
                'base_person_count' => 2,
                'additional_person_price' => 1000000,
                'capacity' => 6,
                'duration' => '4 Days 3 Nights',
                'tax_percentage' => 12.50,
                'category_id' => $photographyCategory->id
            ],
            [
                'name' => 'Traditional Boat Building Singkil',
                'description' => 'Learn the ancient art of traditional boat building in Aceh Singkil. Work alongside master craftsmen, understand maritime traditions, and create a miniature traditional boat as a souvenir.',
                'price' => 1550000,
                'base_person_count' => 2,
                'additional_person_price' => 575000,
                'capacity' => 8,
                'duration' => '3 Days 2 Nights',
                'tax_percentage' => 10.25,
                'category_id' => $culturalCategory->id
            ],
            [
                'name' => 'Kenduri Laut Tradition Experience',
                'description' => 'Witness and participate in the sacred Kenduri Laut (Tron U Laot) tradition of Aceh Singkil fishermen. Ritual ceremonies, prayers for fishermen\'s safety, and thanksgiving celebrations for sea harvest with coastal communities.',
                'price' => 1250000,
                'base_person_count' => 2,
                'additional_person_price' => 500000,
                'capacity' => 15,
                'duration' => '2 Days 1 Night',
                'tax_percentage' => 9.00,
                'category_id' => $culturalCategory->id
            ],
            [
                'name' => 'PT Sumatra Tour Travel Sunrise & Sunset Tour',
            'description' => 'Enjoy the best sunrise and sunset with PT Sumatra Tour Travel from strategic spots. Long bridges with coconut tree backdrops, white sand beaches, and stunning Indian Ocean panoramas.',
                'price' => 1750000,
                'base_person_count' => 2,
                'additional_person_price' => 700000,
                'capacity' => 10,
                'duration' => '3 Days 2 Nights',
                'tax_percentage' => 10.00,
                'category_id' => $photographyCategory->id
            ],
            [
                'name' => 'Aceh Singkil Culinary & Market Tour',
                'description' => 'Explore the culinary richness of Aceh Singkil and traditional markets. Taste fresh seafood, specialty salted fish, fish crackers, and learn traditional seafood processing methods from local fishermen.',
                'price' => 950000,
                'base_person_count' => 2,
                'additional_person_price' => 400000,
                'capacity' => 12,
                'duration' => '1 Day',
                'tax_percentage' => 8.00,
                'category_id' => $culinaryCategory->id
            ]
        ];

        foreach ($travelPackages as $packageData) {
            $travelPackage = TravelPackage::create($packageData);
            $this->createItinerariesForPackage($travelPackage);
            $this->createIncludesForPackage($travelPackage);
            $this->createExcludesForPackage($travelPackage);
        }
    }

    /**
     * Create itineraries for a specific travel package
     */
    private function createItinerariesForPackage(TravelPackage $travelPackage): void
    {
        $itinerariesData = $this->getItinerariesData($travelPackage->name);

        foreach ($itinerariesData as $itineraryData) {
            Itinerary::create([
                'travel_package_id' => $travelPackage->id,
                'day' => $itineraryData['day'],
                'activity' => $itineraryData['activity'],
                'note' => $itineraryData['note'],
            ]);
        }
    }

    private function createIncludesForPackage(TravelPackage $travelPackage): void
    {
        $includesData = $this->getIncludesData($travelPackage->name);

        foreach ($includesData as $includeName) {
            TravelInclude::create([
                'travel_package_id' => $travelPackage->id,
                'name' => $includeName,
            ]);
        }
    }

    private function createExcludesForPackage(TravelPackage $travelPackage): void
    {
        $excludesData = $this->getExcludesData($travelPackage->name);

        foreach ($excludesData as $excludeName) {
            TravelExclude::create([
                'travel_package_id' => $travelPackage->id,
                'name' => $excludeName,
            ]);
        }
    }

    /**
     * Get itineraries data based on package name
     */
    private function getItinerariesData(string $packageName): array
    {
        $itineraries = [
            'PT Sumatra Tour Travel Paradise Explorer' => [
                [
                    'day' => '1',
                    'activity' => 'Arrive at Medan Airport, scenic journey to Aceh Singkil (4-5 hours), hotel check-in, seafood lunch, tourism program orientation',
                    'note' => 'Comfortable air-conditioned vehicle. Bring motion sickness medication if needed for the journey.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Speedboat with PT Sumatra Tour Travel, visit 4-5 pristine islands, snorkeling in crystal clear water, explore traditional fishing village',
                    'note' => 'Complete snorkeling equipment provided. Use reef-safe sunscreen.'
                ],
                [
                    'day' => '3',
                    'activity' => 'Extended island exploration, deep sea fishing, beach camping experience, traditional cooking class with locals',
                    'note' => 'Overnight camping on pristine beach. Experience traditional island lifestyle.'
                ],
                [
                    'day' => '4',
                    'activity' => 'Visit local market, cultural interaction with fishing community, souvenir shopping, return journey to Medan',
                    'note' => 'Perfect opportunity to buy local specialties like salted fish and traditional crafts.'
                ]
            ],
            'Panjang Island Hopping Adventure' => [
                [
                    'day' => '1',
                    'activity' => 'Pick-up in Medan, comfortable journey to Aceh Singkil, accommodation check-in, equipment briefing, sunset viewing at the harbor',
                    'note' => '4-5 hour journey with mountain and coastal views. Rest stops included.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Full-day island hopping to Panjang Island and 6-7 other islands, multiple snorkeling sites, beach picnic, swimming in hidden lagoons',
                    'note' => 'Ultimate island adventure with pristine coral reefs. Underwater cameras available for rent.'
                ],
                [
                    'day' => '3',
                    'activity' => 'Visit the most photogenic island, final snorkeling session, beach relaxation, departure preparation',
                    'note' => 'Last chance for perfect photos and swimming. Check-out and return journey to Medan.'
                ]
            ],
            'Nibung Bay Turtle Conservation' => [
                [
                    'day' => '1',
                    'activity' => 'Arrive in Aceh Singkil, check-in to eco-friendly accommodation, introduction to turtle conservation program, beach cleanup activities at Nibung Bay',
                    'note' => 'Stay in eco-friendly accommodation. Participate in meaningful conservation efforts.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Turtle conservation program at Nibung Bay, witness turtle egg hatching, baby turtle release to the sea, sustainable fishing workshop',
                    'note' => 'Hands-on conservation activities with local community. Educational and impactful experience.'
                ],
                [
                    'day' => '3',
                    'activity' => 'Marine research expedition, underwater ecosystem study, data collection training, conservation technology workshop, conservation certificate',
                    'note' => 'Advanced conservation techniques and marine research methods. Scientific approach to conservation.'
                ]
            ],
            'Tuangku Island Diving Adventure' => [
                [
                    'day' => '1',
                    'activity' => 'Journey to Singkil, diving equipment check, accommodation check-in, diving theory session, shallow water practice',
                    'note' => 'Professional diving equipment provided. Certificate required for diving activities.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Multiple diving sessions at Tuangku Island, underwater photography, coral garden exploration, marine life observation',
                    'note' => 'World-class diving sites with diverse marine life. Follow dive master instructions strictly.'
                ],
                [
                    'day' => '3',
                    'activity' => 'Final diving session, equipment cleaning, logbook completion, farewell lunch, return journey',
                    'note' => 'Complete diving logbook with new experiences. Professional underwater photos available.'
                ]
            ],
            'Aceh Singkil Cultural Heritage' => [
                [
                    'day' => '1',
                    'activity' => 'Traditional welcome ceremony, homestay check-in, fishing village tour, traditional boat ride, cultural evening',
                    'note' => 'Authentic cultural immersion with local fishing families. Respect local customs and traditions.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Dawn fishing expedition, traditional fishing techniques, local market exploration, cooking class, cultural performance, farewell lunch',
                    'note' => 'Learn traditional maritime skills from experienced fishermen. Start very early in the morning.'
                ]
            ],
            'PT Sumatra Tour Travel Eco-Lodge Retreat' => [
                [
                    'day' => '1',
                    'activity' => 'Eco-friendly transfer, solar-powered lodge check-in, organic welcome lunch, sustainability tour, sunset meditation',
                    'note' => 'Sustainable accommodation with minimal environmental impact. Solar power and organic food.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Guided nature walks, sustainable snorkeling, renewable energy workshop, organic farming participation, stargazing',
                    'note' => 'Learn about sustainable tourism practices. Participate in eco-friendly activities.'
                ],
                [
                    'day' => '3',
                    'activity' => 'Permaculture workshop, sustainable fishing techniques, eco-construction project, renewable energy installation',
                    'note' => 'Advanced sustainability practices. Hands-on eco-construction and renewable energy projects.'
                ],
                [
                    'day' => '4',
                    'activity' => 'Community sustainability project, environmental impact assessment, eco-tourism planning workshop',
                    'note' => 'Contribute to long-term sustainability initiatives. Learn eco-tourism development.'
                ],
                [
                    'day' => '5',
                    'activity' => 'Farm-to-table breakfast, final eco-activity, sustainability certificate ceremony, carbon-neutral departure',
                    'note' => 'Receive sustainability participation certificate. Leave minimal environmental footprint.'
                ]
            ],
            'Pulau Bangkaru Turtle Sanctuary Tour' => [
                [
                    'day' => '1',
                    'activity' => 'Turtle sanctuary orientation, conservation team meeting, beach patrol introduction, evening turtle observation',
                    'note' => 'Important sea turtle nesting site. Follow conservation guidelines strictly.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Boat trip to Pulau Bangkaru, turtle nesting site visit, conservation activities, turtle release ceremony',
                    'note' => 'Participate in turtle conservation efforts. Emotional and educational experience.'
                ],
                [
                    'day' => '3',
                    'activity' => 'Advanced turtle research, satellite tracking workshop, marine biology study, conservation data analysis',
                    'note' => 'Scientific approach to turtle conservation. Learn research methodologies and data collection.'
                ],
                [
                    'day' => '4',
                    'activity' => 'Final conservation activity, impact assessment, adoption certificate presentation, departure',
                    'note' => 'Adopt a turtle and receive certificate. Contribute to sea turtle protection.'
                ]
            ],
            'Aceh Singkil Mangrove Exploration' => [
                [
                    'day' => '1',
                    'activity' => 'Mangrove research station visit, ecosystem briefing, traditional boat introduction, sunset mangrove cruise',
                    'note' => 'Learn about mangrove ecosystem importance. Traditional boat navigation experience.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Full-day mangrove expedition, wildlife spotting, conservation activity participation, mangrove planting, certificate presentation, departure',
                    'note' => 'Discover unique mangrove wildlife. Plant mangrove seedlings and receive conservation certificate.'
                ]
            ],
            'PT Sumatra Tour Travel Photography Workshop' => [
                [
                    'day' => '1',
                    'activity' => 'Photography equipment check, technical briefing, camera settings workshop, golden hour practice, image review',
                    'note' => 'Professional photography guidance provided. Bring your camera or rent professional equipment.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Dawn photography session, underwater photography workshop, landscape composition, sunset masterclass, editing workshop',
                    'note' => 'Learn advanced photography techniques. Capture stunning island landscapes and marine life.'
                ],
                [
                    'day' => '3',
                    'activity' => 'Advanced composition techniques, drone photography workshop, time-lapse creation, night photography session',
                    'note' => 'Master advanced photography techniques including aerial and night photography.'
                ],
                [
                    'day' => '4',
                    'activity' => 'Final sunrise shoot, portfolio review, image selection and editing, photography certificate presentation',
                    'note' => 'Create professional portfolio of your work. Receive photography workshop certificate.'
                ]
            ],
            'Singkil Traditional Boat Building Experience' => [
                [
                    'day' => '1',
                    'activity' => 'Traditional shipyard visit, master craftsman introduction, boat building history lesson, wood selection workshop',
                    'note' => 'Learn ancient maritime craftsmanship. Work with experienced traditional boat builders.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Hands-on boat building workshop, traditional tool introduction, advanced construction techniques, cultural exchange',
                    'note' => 'Participate in actual boat construction. Learn traditional joinery methods and techniques.'
                ],
                [
                    'day' => '3',
                    'activity' => 'Miniature boat completion, maritime heritage certificate, farewell lunch with craftsmen, departure with souvenir',
                    'note' => 'Complete your own miniature traditional boat. Take home handmade maritime souvenir.'
                ]
            ],
            'Sabang Diving & Marine Adventure' => [
                [
                    'day' => '1',
                    'activity' => 'Ferry ke Sabang, diving/snorkeling di Taman Laut Rubiah dengan terumbu karang terbaik',
                    'note' => 'Sertifikat diving diperlukan untuk diving. Snorkeling tersedia untuk pemula.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Diving di spot premium, underwater photography, eksplorasi Pantai Iboih',
                    'note' => 'Kamera underwater bisa disewa. Ikuti instruksi dive master dengan ketat.'
                ],
                [
                    'day' => '3',
                    'activity' => 'Kunjungi Kilometer Nol Indonesia, sunset viewing spektakuler, perjalanan kembali',
                    'note' => 'Titik paling barat Indonesia. Bawa kamera untuk foto bersejarah.'
                ]
            ],
            'Kenduri Laut Tradition Experience' => [
                [
                    'day' => '1',
                    'activity' => 'Arrive in Aceh Singkil, introduction to Kenduri Laut tradition, Tron U Laot ceremony preparation, cultural evening with fishermen',
                    'note' => 'Sacred tradition of Acehnese fishermen. Respect customs and follow traditional elder guidance.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Witness Kenduri Laut ceremony, ritual for protection, prayers for fishermen safety, thanksgiving celebration for sea harvest, community feast',
                    'note' => 'Deep spiritual and cultural experience. Participate with full respect.'
                ]
            ],
            'PT Sumatra Tour Travel Sunrise & Sunset Tour' => [
                [
                    'day' => '1',
                    'activity' => 'Journey to Aceh Singkil, speedboat with PT Sumatra Tour Travel, resort check-in, sunset viewing at long bridge with coconut tree backdrop',
                    'note' => 'Best sunset spot at iconic bridge. Bring camera for spectacular photos.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Sunrise hunting at white sand beach, island hopping to best photo spots, snorkeling, relaxation at pristine beach',
                    'note' => 'Wake up early for best sunrise. Enjoy the natural beauty that remains pristine.'
                ],
                [
                    'day' => '3',
                    'activity' => 'Final sunrise session, last island exploration, departure preparation, return journey to Medan',
                    'note' => 'Last chance for sunrise photos. Bring Aceh Singkil specialty souvenirs.'
                ]
            ],
            'Aceh Singkil Culinary & Market Tour' => [
                [
                    'day' => '1',
                    'activity' => 'Traditional market tour of Aceh Singkil, taste fresh seafood, specialty salted fish, fish crackers, learn traditional seafood processing, cooking class with fishermen',
                    'note' => 'Explore coastal culinary richness. Taste authentic food and learn traditional recipes.'
                ]
            ]
        ];

        return $itineraries[$packageName] ?? [];
    }

    private function getIncludesData(string $packageName): array
    {
        $includes = [
            'PT Sumatra Tour Travel Paradise Explorer' => [
                'AC transportation round trip Medan - Aceh Singkil',
                'Speedboat transfer with PT Sumatra Tour Travel',
                '2 nights accommodation (hotel/guesthouse)',
                'All meals during tour (breakfast, lunch, dinner)',
                'Professional tour guide Indonesian/English speaking',
                'Complete snorkeling equipment',
                'All entrance tickets and permits',
                'Underwater photography session',
                'Mineral water throughout journey',
                'Travel insurance'
            ],
            'Panjang Island Hopping Adventure' => [
                'Private AC vehicle Medan - Singkil round trip',
                'Speedboat for island hopping',
                '2 nights quality accommodation',
                'All meals with fresh seafood',
                'Professional island guide',
                'Snorkeling and safety equipment',
                'Island entrance tickets',
                'Waterproof camera rental option',
                'Cool box with fresh drinks',
                'First aid kit and safety briefing'
            ],
            'Nibung Bay Turtle Conservation' => [
                'Eco-friendly transportation',
                '2 nights eco-friendly accommodation',
                'Organic food and local cuisine',
                'Conservation program participation',
                'Marine conservation equipment',
                'Expert conservation guide',
                'Educational materials and resources',
                'Conservation certificate',
                'Turtle adoption program',
                'Environmental impact assessment'
            ],
            'Tuangku Island Diving Adventure' => [
                'Transportation Medan - Singkil round trip',
                'Diving boat to Tuangku Island',
                '2 nights diving resort accommodation',
                'All meals including diving lunch',
                'Complete diving equipment rental',
                'Certified diving instructor',
                'Multiple diving sessions',
                'Underwater photography service',
                'Diving logbook completion',
                'Safety equipment and insurance'
            ],
            'Aceh Singkil Cultural Heritage' => [
                'Cultural transportation experience',
                '2 nights traditional homestay',
                'Authentic local meals with family',
                'Traditional fishing expedition',
                'Cultural guide and translator',
                'Traditional boat experience',
                'Craft workshop materials',
                'Cultural performance tickets',
                'Traditional costume photo session',
                'Cultural exchange certificate'
            ],
            'Kenduri Laut Tradition Experience' => [
                'Cultural transportation to Aceh Singkil',
                '1 night traditional fisherman homestay',
                'Meals with fisherman community',
                'Kenduri Laut ceremony participation',
                'Cultural and spiritual guide',
                'Traditional ritual experience',
                'Maritime cultural education materials',
                'Cultural participation certificate',
                'Traditional ceremony documentation',
                'Aceh Singkil specialty souvenirs'
            ],
            'PT Sumatra Tour Travel Sunrise & Sunset Tour' => [
                'AC transportation Medan - Aceh Singkil',
                'Speedboat to best sunrise/sunset spots with PT Sumatra Tour Travel',
                '2 nights resort accommodation',
                'All meals with spectacular views',
                'Professional photography guide',
                'Basic photography equipment',
                'Access to exclusive photo spots',
                'Sunrise and sunset photography sessions',
                'Professional photo editing',
                'Digital photo album'
            ],
            'Aceh Singkil Culinary & Market Tour' => [
                'Local transportation in Aceh Singkil',
                'Experienced culinary guide',
                'Traditional market tour',
                'Cooking class with fishermen',
                'All food and beverages',
                'Traditional cooking recipes',
                'Cooking ingredients to take home',
                'Cooking class certificate',
                'Cooking process documentation',
                'Specialty culinary souvenirs'
            ]


        ];

        return $includes[$packageName] ?? [];
    }

    private function getExcludesData(string $packageName): array
    {
        $excludes = [
            'PT Sumatra Tour Travel Paradise Explorer' => [
                'Personal expenses and souvenirs',
                'Alcoholic beverages (not available)',
                'Tips for guide and crew',
                'Additional meals outside package',
                'Paid watersport activities',
                'Personal diving equipment',
                'Medical costs and medications',
                'International flight tickets'
            ],
            'Panjang Island Hopping Adventure' => [
                'Personal expenses and shopping',
                'Travel insurance upgrade',
                'Tips for boat crew and guide',
                'Additional watersport activities',
                'Personal snorkeling equipment',
                'Alcoholic beverages',
                'Medical costs',
                'Additional accommodation nights'
            ],
            'Nibung Bay Turtle Conservation' => [
                'Personal expenses',
                'Tips for conservation team',
                'Additional conservation donations',
                'Personal conservation equipment',
                'Additional educational materials',
                'Personal transportation',
                'Medical costs',
                'Additional certification fees'
            ],
            'Tuangku Island Diving Adventure' => [
                'Diving certification course fees',
                'Personal diving equipment',
                'Tips for diving instructor',
                'Additional diving sessions',
                'Underwater camera purchase',
                'Personal expenses',
                'Medical diving clearance',
                'Diving insurance upgrade'
            ],
            'Aceh Singkil Cultural Heritage' => [
                'Personal expenses and souvenirs',
                'Tips for host family',
                'Additional cultural activities',
                'Personal fishing equipment',
                'Additional craft materials',
                'Traditional costume purchase',
                'Additional cultural performances',
                'Personal transportation'
            ],
            'Kenduri Laut Tradition Experience' => [
                'Personal expenses',
                'Tips for fisherman family',
                'Additional ceremony donations',
                'Personal transportation',
                'Medical costs',
                'Additional souvenirs',
                'Additional spiritual activities',
                'Personal documentation'
            ],
            'PT Sumatra Tour Travel Sunrise & Sunset Tour' => [
                'Personal camera equipment',
                'Tips for photography guide',
                'Additional photo editing software',
                'Personal photography equipment',
                'Additional photo printing services',
                'Professional portfolio creation',
                'Additional workshop sessions',
                'Personal equipment insurance'
            ],
            'Aceh Singkil Culinary & Market Tour' => [
                'Personal expenses at market',
                'Tips for local chef',
                'Additional cooking ingredient purchases',
                'Personal cooking equipment',
                'Additional cooking workshops',
                'Traditional kitchen equipment purchase',
                'Additional culinary activities',
                'Personal transportation'
            ]
        ];

        return $excludes[$packageName] ?? [];
    }
}
