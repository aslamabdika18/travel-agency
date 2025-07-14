<?php

namespace Database\Seeders;

use App\Models\TravelPackage;
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
        $travelPackages = [
            [
                'name' => 'Banyak Islands Paradise Explorer',
                'description' => 'Discover the pristine beauty of 99 islands in Aceh Singkil, often called "Aceh\'s Maldives". Experience crystal-clear waters, white sandy beaches, vibrant coral reefs, and traditional fishing village culture across multiple stunning islands.',
                'price' => 1950000,
                'base_person_count' => 2,
                'additional_person_price' => 750000,
                'capacity' => 12,
                'duration' => '4 Days 3 Nights',
                'tax_percentage' => 10.00
            ],
            [
                'name' => 'Banyak Island Hopping Adventure',
                'description' => 'Ultimate island hopping experience in Banyak island archipelago. Visit 5-7 pristine islands, enjoy snorkeling in untouched coral gardens, relax on secluded beaches, and witness spectacular sunsets over the Indian Ocean.',
                'price' => 2150000,
                'base_person_count' => 2,
                'additional_person_price' => 850000,
                'capacity' => 10,
                'duration' => '3 Days 2 Nights',
                'tax_percentage' => 11.00
            ],
            [
                'name' => 'Aceh Singkil Marine Conservation Experience',
                'description' => 'Immerse yourself in marine conservation efforts while exploring Aceh Singkil\'s coastal wonders. Participate in turtle conservation programs, mangrove restoration, and sustainable fishing practices with local communities.',
                'price' => 1750000,
                'base_person_count' => 2,
                'additional_person_price' => 650000,
                'capacity' => 8,
                'duration' => '5 Days 4 Nights',
                'tax_percentage' => 10.50
            ],
            [
                'name' => 'Tuangku Island Diving & Snorkeling Safari',
                'description' => 'Explore the underwater paradise of Tuangku Island in Banyak Islands. Experience world-class diving spots, encounter diverse marine life, and enjoy pristine coral reefs in crystal-clear tropical waters.',
                'price' => 2350000,
                'base_person_count' => 2,
                'additional_person_price' => 950000,
                'capacity' => 8,
                'duration' => '3 Days 2 Nights',
                'tax_percentage' => 12.00
            ],
            [
                'name' => 'Singkil Coastal Cultural Journey',
                'description' => 'Experience authentic coastal life in Aceh Singkil. Learn traditional fishing techniques, explore local markets, enjoy fresh seafood cuisine, and discover the rich maritime heritage of this charming coastal town.',
                'price' => 1450000,
                'base_person_count' => 2,
                'additional_person_price' => 550000,
                'capacity' => 12,
                'duration' => '2 Days 1 Night',
                'tax_percentage' => 9.50
            ],
            [
                'name' => 'Banyak Islands Eco-Lodge Retreat',
                'description' => 'Sustainable island retreat in Pulau Banyak with eco-friendly accommodations. Enjoy solar-powered lodges, organic local cuisine, nature walks, and minimal-impact tourism while supporting local communities.',
                'price' => 2250000,
                'base_person_count' => 2,
                'additional_person_price' => 900000,
                'capacity' => 6,
                'duration' => '5 Days 4 Nights',
                'tax_percentage' => 11.50
            ],
            [
                'name' => 'Pulau Bangkaru Turtle Sanctuary Tour',
                'description' => 'Visit the famous turtle nesting site at Pulau Bangkaru in Banyak Islands. Witness sea turtle conservation efforts, participate in beach cleanups, and learn about marine ecosystem protection.',
                'price' => 1850000,
                'base_person_count' => 2,
                'additional_person_price' => 750000,
                'capacity' => 10,
                'duration' => '4 Days 3 Nights',
                'tax_percentage' => 10.00
            ],
            [
                'name' => 'Aceh Singkil Mangrove Exploration',
                'description' => 'Navigate through pristine mangrove forests of Aceh Singkil by traditional boat. Discover unique wildlife, learn about mangrove ecosystems, and support community-based conservation initiatives.',
                'price' => 1650000,
                'base_person_count' => 2,
                'additional_person_price' => 600000,
                'capacity' => 10,
                'duration' => '2 Days 1 Night',
                'tax_percentage' => 9.00
            ],
            [
                'name' => 'Banyak Islands Photography Workshop',
                'description' => 'Professional photography tour across Banyak Islands\' most photogenic locations. Capture stunning landscapes, underwater scenes, cultural moments, and receive guidance from expert photographers.',
                'price' => 2450000,
                'base_person_count' => 2,
                'additional_person_price' => 1000000,
                'capacity' => 6,
                'duration' => '4 Days 3 Nights',
                'tax_percentage' => 12.50
            ],
            [
                'name' => 'Singkil Traditional Boat Building Experience',
                'description' => 'Learn the ancient art of traditional boat building in Aceh Singkil. Work alongside master craftsmen, understand maritime traditions, and create your own miniature traditional boat as a souvenir.',
                'price' => 1550000,
                'base_person_count' => 2,
                'additional_person_price' => 575000,
                'capacity' => 8,
                'duration' => '3 Days 2 Nights',
                'tax_percentage' => 10.25
            ],
            [
                'name' => 'Sabang Diving & Marine Adventure',
                'description' => 'Explore the underwater paradise of Sabang Island with world-class diving spots at Rubiah Marine Park. Experience vibrant coral reefs, diverse marine life, and visit the westernmost point of Indonesia.',
                'price' => 1850000,
                'base_person_count' => 2,
                'additional_person_price' => 750000,
                'capacity' => 8,
                'duration' => '3 Days 2 Nights',
                'tax_percentage' => 11.00
            ],
            [
                'name' => 'Takengon Coffee & Lake Experience',
                'description' => 'Discover the world-famous Gayo coffee in its highland origin. Enjoy coffee plantation tours, traditional boat rides on Lake Laut Tawar, and experience the cool mountain atmosphere of Takengon.',
                'price' => 1650000,
                'base_person_count' => 2,
                'additional_person_price' => 600000,
                'capacity' => 10,
                'duration' => '3 Days 2 Nights',
                'tax_percentage' => 10.00
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
            'Banyak Islands Paradise Explorer' => [
                [
                    'day' => '1',
                    'activity' => 'Arrival at Medan airport, scenic drive to Aceh Singkil (4-5 hours), hotel check-in, welcome seafood lunch, program orientation',
                    'note' => 'Comfortable air-conditioned vehicle. Bring motion sickness medication if needed for the journey.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Speedboat to Banyak Islands, visit 4-5 pristine islands, snorkeling in crystal-clear waters, traditional fishing village exploration',
                    'note' => 'Complete snorkeling equipment provided. Use reef-safe sunscreen to protect marine life.'
                ],
                [
                    'day' => '3',
                    'activity' => 'Extended island exploration, deep sea fishing, beach camping experience, traditional cooking class with locals',
                    'note' => 'Overnight camping on pristine beach. Experience traditional island lifestyle.'
                ],
                [
                    'day' => '4',
                    'activity' => 'Local market visit, cultural interaction with fishing communities, souvenir shopping, return journey to Medan',
                    'note' => 'Perfect opportunity to buy local specialties like dried fish and traditional handicrafts.'
                ]
            ],
            'Banyak Island Hopping Adventure' => [
                [
                    'day' => '1',
                    'activity' => 'Medan pickup, comfortable drive to Aceh Singkil, accommodation check-in, equipment briefing, sunset viewing at harbor',
                    'note' => 'Journey takes 4-5 hours with scenic mountain and coastal views. Rest stops included.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Full-day island hopping to 6-7 islands, multiple snorkeling sites, beach picnic, swimming in secluded lagoons',
                    'note' => 'Ultimate island adventure with pristine coral reefs. Underwater camera available for rent.'
                ],
                [
                    'day' => '3',
                    'activity' => 'Visit most photogenic island, final snorkeling session, beachside relaxation, departure preparation',
                    'note' => 'Last chance for perfect photos and swimming. Check-out and return journey to Medan.'
                ]
            ],
            'Aceh Singkil Marine Conservation Experience' => [
                [
                    'day' => '1',
                    'activity' => 'Arrival in Singkil, eco-accommodation check-in, conservation program introduction, beach cleanup activity',
                    'note' => 'Stay at environmentally friendly accommodation. Participate in meaningful conservation efforts.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Turtle conservation program, mangrove restoration, sustainable fishing workshop, coral reef monitoring',
                    'note' => 'Hands-on conservation activities with local communities. Educational and impactful experience.'
                ],
                [
                    'day' => '3',
                    'activity' => 'Marine research expedition, underwater ecosystem study, data collection training, conservation technology workshop',
                    'note' => 'Advanced conservation techniques and marine research methods. Scientific approach to conservation.'
                ],
                [
                    'day' => '4',
                    'activity' => 'Community outreach program, environmental education workshop, local school visit, conservation presentation',
                    'note' => 'Share conservation knowledge with local communities. Educational outreach activities.'
                ],
                [
                    'day' => '5',
                    'activity' => 'Final conservation project, impact assessment, certificate presentation, departure',
                    'note' => 'Receive conservation participation certificate. Contribute to marine ecosystem protection.'
                ]
            ],
            'Tuangku Island Diving & Snorkeling Safari' => [
                [
                    'day' => '1',
                    'activity' => 'Journey to Singkil, diving equipment check, accommodation check-in, diving theory session, shallow water practice',
                    'note' => 'Professional diving equipment provided. Certification required for diving activities.'
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
            'Singkil Coastal Cultural Journey' => [
                [
                    'day' => '1',
                    'activity' => 'Traditional welcome ceremony, homestay check-in, fishing village tour, traditional boat ride, cultural evening',
                    'note' => 'Authentic cultural immersion with local fishing families. Respect local customs and traditions.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Dawn fishing expedition, traditional fishing techniques, local market exploration, cooking class, cultural performance, farewell lunch, departure',
                    'note' => 'Learn traditional maritime skills from experienced fishermen. Early morning start required.'
                ]
            ],
            'Banyak Islands Eco-Lodge Retreat' => [
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
            'Banyak Islands Photography Workshop' => [
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
            'Takengon Coffee & Lake Experience' => [
                [
                    'day' => '1',
                    'activity' => 'Kunjungi perkebunan kopi arabika terbaik dunia, coffee cupping session',
                    'note' => 'Bawa jaket karena udara pegunungan dingin. Pelajari proses dari biji hingga secangkir kopi.'
                ],
                [
                    'day' => '2',
                    'activity' => 'Aktivitas perahu di danau terbesar Aceh, menikmati pemandangan pegunungan',
                    'note' => 'Danau Laut Tawar adalah danau terbesar di Aceh. Bawa kamera untuk landscape photography.'
                ],
                [
                    'day' => '3',
                    'activity' => 'Kunjungi desa tradisional Gayo, beli kopi langsung dari petani',
                    'note' => 'Kesempatan membeli kopi premium langsung dari sumbernya dengan harga terbaik.'
                ]
            ]
        ];

        return $itineraries[$packageName] ?? [];
    }

    private function getIncludesData(string $packageName): array
    {
        $includes = [
            'Banyak Islands Paradise Explorer' => [
                'Round-trip AC transportation Medan - Aceh Singkil',
                'Speedboat transfers to Banyak Islands',
                '2 nights accommodation (hotel/guesthouse)',
                'All meals during tour (breakfast, lunch, dinner)',
                'Professional English-speaking guide',
                'Complete snorkeling equipment',
                'All entrance fees and permits',
                'Underwater photography session',
                'Mineral water throughout the journey',
                'Travel insurance coverage'
            ],
            'Banyak Island Hopping Adventure' => [
                'Private AC vehicle Medan - Singkil return',
                'Speedboat for island hopping',
                '2 nights quality accommodation',
                'All meals with fresh seafood',
                'Professional island guide',
                'Snorkeling gear and safety equipment',
                'Island entrance fees',
                'Waterproof camera rental option',
                'Cool box with refreshments',
                'First aid kit and safety briefing'
            ],
            'Aceh Singkil Marine Conservation Experience' => [
                'Eco-friendly transportation',
                '2 nights eco-accommodation',
                'Organic meals and local cuisine',
                'Conservation program participation',
                'Marine conservation equipment',
                'Expert conservation guide',
                'Educational materials and resources',
                'Conservation certificate',
                'Turtle adoption program',
                'Environmental impact assessment'
            ],
            'Tuangku Island Diving & Snorkeling Safari' => [
                'Transportation Medan - Singkil return',
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
            'Singkil Coastal Cultural Journey' => [
                'Cultural transportation experience',
                '2 nights traditional homestay',
                'Authentic local meals with families',
                'Traditional fishing expedition',
                'Cultural guide and interpreter',
                'Traditional boat experience',
                'Handicraft workshop materials',
                'Cultural performance tickets',
                'Traditional costume photo session',
                'Cultural exchange certificate'
            ],
            'Banyak Islands Eco-Lodge Retreat' => [
                'Carbon-neutral transportation',
                '2 nights solar-powered eco-lodge',
                'Organic farm-to-table meals',
                'Sustainability workshop participation',
                'Eco-activity equipment',
                'Environmental education guide',
                'Renewable energy demonstration',
                'Organic farming experience',
                'Sustainability certificate',
                'Eco-impact measurement report'
            ],
            'Pulau Bangkaru Turtle Sanctuary Tour' => [
                'Transportation to turtle sanctuary',
                '2 nights conservation accommodation',
                'Meals with conservation team',
                'Turtle conservation program',
                'Conservation equipment and tools',
                'Marine biologist guide',
                'Turtle nesting observation',
                'Beach cleanup materials',
                'Turtle adoption certificate',
                'Conservation impact documentation'
            ],
            'Aceh Singkil Mangrove Exploration' => [
                'Eco-transportation to mangrove sites',
                '2 nights eco-friendly accommodation',
                'Local cuisine with mangrove honey',
                'Traditional boat navigation',
                'Wildlife observation equipment',
                'Mangrove ecosystem guide',
                'Conservation activity participation',
                'Mangrove planting materials',
                'Ecosystem education materials',
                'Conservation participation certificate'
            ],
            'Banyak Islands Photography Workshop' => [
                'Photography equipment transportation',
                '2 nights photographer-friendly accommodation',
                'Meals at photogenic locations',
                'Professional photography guidance',
                'Camera equipment rental option',
                'Underwater photography gear',
                'Photo editing software access',
                'Professional portfolio review',
                'Photography workshop certificate',
                'High-resolution photo delivery'
            ],
            'Singkil Traditional Boat Building Experience' => [
                'Transportation to traditional shipyard',
                '2 nights cultural accommodation',
                'Meals with master craftsmen',
                'Traditional boat building workshop',
                'Traditional tools and materials',
                'Master craftsman instruction',
                'Maritime heritage education',
                'Miniature boat creation materials',
                'Cultural exchange sessions',
                'Maritime craftsmanship certificate'
            ],
            'Sabang Diving & Marine Adventure' => [
                'PADI certified dive master',
                'Complete diving equipment',
                'Ferry from Banda Aceh to Sabang',
                'Fresh seafood lunch',
                'Underwater photography service',
                'Diving safety equipment',
                'Entrance ticket to Rubiah Marine Park'
            ],
            'Takengon Coffee & Lake Experience' => [
                'Coffee expert guide',
                'Transportation to coffee plantations',
                'Coffee cupping session',
                'Traditional boat on Lake Laut Tawar',
                'Traditional Gayo lunch',
                'Premium Gayo coffee 250gr',
                'Mineral water and snacks'
            ]


        ];

        return $includes[$packageName] ?? [];
    }

    private function getExcludesData(string $packageName): array
    {
        $excludes = [
            'Banyak Islands Paradise Explorer' => [
                'Personal expenses and souvenirs',
                'Alcoholic beverages (not available)',
                'Tips for guide and crew',
                'Additional meals outside the package',
                'Paid water sports activities',
                'Personal diving equipment',
                'Medical expenses and medication',
                'International flight tickets'
            ],
            'Pulau Banyak Island Hopping Adventure' => [
                'Personal expenses and shopping',
                'Travel insurance upgrade',
                'Tips for boat crew and guide',
                'Additional water sports activities',
                'Personal snorkeling equipment',
                'Alcoholic beverages',
                'Medical expenses',
                'Extra accommodation nights'
            ],
            'Aceh Singkil Marine Conservation Experience' => [
                'Personal expenses',
                'Tips for conservation team',
                'Additional conservation donations',
                'Personal conservation equipment',
                'Extra educational materials',
                'Personal transportation',
                'Medical expenses',
                'Additional certification fees'
            ],
            'Tuangku Island Diving & Snorkeling Safari' => [
                'Diving certification course fees',
                'Personal diving equipment',
                'Tips for diving instructor',
                'Additional diving sessions',
                'Underwater camera purchase',
                'Personal expenses',
                'Medical diving clearance',
                'Diving insurance upgrade'
            ],
            'Singkil Coastal Cultural Journey' => [
                'Personal expenses and souvenirs',
                'Tips for host families',
                'Additional cultural activities',
                'Personal fishing equipment',
                'Extra handicraft materials',
                'Traditional costume purchase',
                'Additional cultural performances',
                'Personal transportation'
            ],
            'Banyak Islands Eco-Lodge Retreat' => [
                'Personal expenses',
                'Tips for eco-lodge staff',
                'Additional sustainability workshops',
                'Personal eco-equipment',
                'Extra organic products',
                'Carbon offset donations',
                'Personal environmental projects',
                'Additional certification fees'
            ],
            'Pulau Bangkaru Turtle Sanctuary Tour' => [
                'Personal expenses',
                'Tips for conservation team',
                'Additional turtle adoptions',
                'Personal conservation equipment',
                'Extra conservation donations',
                'Research participation fees',
                'Personal transportation',
                'Additional educational materials'
            ],
            'Aceh Singkil Mangrove Exploration' => [
                'Personal expenses',
                'Tips for boat operator',
                'Additional mangrove seedlings',
                'Personal wildlife equipment',
                'Extra conservation activities',
                'Research participation fees',
                'Personal transportation',
                'Additional educational workshops'
            ],
            'Banyak Islands Photography Workshop' => [
                'Personal camera equipment',
                'Tips for photography instructor',
                'Additional photo editing software',
                'Personal photography gear',
                'Extra photo printing services',
                'Professional portfolio creation',
                'Additional workshop sessions',
                'Personal equipment insurance'
            ],
            'Singkil Traditional Boat Building Experience' => [
                'Personal expenses',
                'Tips for master craftsmen',
                'Additional boat building materials',
                'Personal woodworking tools',
                'Extra workshop sessions',
                'Traditional tool purchases',
                'Additional cultural activities',
                'Personal transportation'
            ],
            'Sabang Diving & Marine Adventure' => [
                'Personal diving equipment',
                'Tips for dive master and crew',
                'Additional diving sessions',
                'Underwater camera purchase',
                'Personal expenses and souvenirs',
                'Diving certification course fees',
                'Medical diving clearance',
                'Alcoholic beverages'
            ],
            'Takengon Coffee & Lake Experience' => [
                'Personal expenses and souvenirs',
                'Tips for guide and boat crew',
                'Additional coffee purchases',
                'Personal transportation',
                'Extra accommodation nights',
                'Personal hiking equipment',
                'Medical expenses',
                'Alcoholic beverages'
            ]
        ];

        return $excludes[$packageName] ?? [];
    }
}
