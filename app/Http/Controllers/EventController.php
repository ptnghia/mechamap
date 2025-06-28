<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    /**
     * Display events index
     */
    public function index(Request $request)
    {
        // Mock events data - replace with actual model when implemented
        $events = collect([
            [
                'id' => 1,
                'title' => 'Vietnam Manufacturing Summit 2024',
                'description' => 'Annual summit for manufacturing professionals in Vietnam',
                'type' => 'conference',
                'date' => '2024-03-15',
                'time' => '09:00',
                'location' => 'Ho Chi Minh City',
                'organizer' => 'Vietnam Manufacturing Association',
                'attendees' => 250,
                'max_attendees' => 300,
                'price' => 500000,
                'currency' => 'VND',
                'status' => 'upcoming',
                'image' => '/images/events/manufacturing-summit.jpg'
            ],
            [
                'id' => 2,
                'title' => 'CAD Software Workshop',
                'description' => 'Hands-on workshop for advanced CAD techniques',
                'type' => 'workshop',
                'date' => '2024-02-20',
                'time' => '14:00',
                'location' => 'Hanoi',
                'organizer' => 'MechaMap Education',
                'attendees' => 45,
                'max_attendees' => 50,
                'price' => 200000,
                'currency' => 'VND',
                'status' => 'upcoming',
                'image' => '/images/events/cad-workshop.jpg'
            ],
            [
                'id' => 3,
                'title' => 'Industry 4.0 Webinar Series',
                'description' => 'Online webinar series about Industry 4.0 technologies',
                'type' => 'webinar',
                'date' => '2024-02-10',
                'time' => '19:00',
                'location' => 'Online',
                'organizer' => 'Tech Innovation Hub',
                'attendees' => 180,
                'max_attendees' => 500,
                'price' => 0,
                'currency' => 'VND',
                'status' => 'upcoming',
                'image' => '/images/events/industry-4-webinar.jpg'
            ],
            [
                'id' => 4,
                'title' => 'Mechanical Engineering Career Fair',
                'description' => 'Connect with top employers in mechanical engineering',
                'type' => 'career_fair',
                'date' => '2024-01-25',
                'time' => '10:00',
                'location' => 'Da Nang',
                'organizer' => 'Engineering Career Network',
                'attendees' => 320,
                'max_attendees' => 400,
                'price' => 0,
                'currency' => 'VND',
                'status' => 'completed',
                'image' => '/images/events/career-fair.jpg'
            ]
        ]);
        
        // Filter by type
        if ($request->filled('type')) {
            $events = $events->where('type', $request->get('type'));
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $events = $events->where('status', $request->get('status'));
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = strtolower($request->get('search'));
            $events = $events->filter(function($event) use ($search) {
                return str_contains(strtolower($event['title']), $search) ||
                       str_contains(strtolower($event['description']), $search) ||
                       str_contains(strtolower($event['location']), $search);
            });
        }
        
        // Sort by date
        $events = $events->sortBy('date');
        
        // Pagination simulation
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedEvents = $events->slice($offset, $perPage)->values();
        
        $eventTypes = [
            'conference' => 'Conference',
            'workshop' => 'Workshop',
            'webinar' => 'Webinar',
            'career_fair' => 'Career Fair',
            'networking' => 'Networking Event'
        ];
        
        return view('community.events.index', compact('paginatedEvents', 'eventTypes'));
    }
    
    /**
     * Display event details
     */
    public function show($id)
    {
        // Mock event data - replace with actual model when implemented
        $event = [
            'id' => $id,
            'title' => 'Vietnam Manufacturing Summit 2024',
            'description' => 'The premier annual summit bringing together manufacturing professionals, industry leaders, and technology innovators from across Vietnam and Southeast Asia.',
            'full_description' => 'Join us for three days of intensive learning, networking, and innovation at the Vietnam Manufacturing Summit 2024. This event features keynote presentations from industry leaders, technical workshops, product demonstrations, and extensive networking opportunities.',
            'type' => 'conference',
            'date' => '2024-03-15',
            'end_date' => '2024-03-17',
            'time' => '09:00',
            'end_time' => '17:00',
            'location' => 'Saigon Convention Center, Ho Chi Minh City',
            'address' => '123 Nguyen Hue Boulevard, District 1, Ho Chi Minh City',
            'organizer' => 'Vietnam Manufacturing Association',
            'organizer_contact' => 'events@vma.org.vn',
            'attendees' => 250,
            'max_attendees' => 300,
            'price' => 500000,
            'currency' => 'VND',
            'status' => 'upcoming',
            'registration_deadline' => '2024-03-10',
            'image' => '/images/events/manufacturing-summit.jpg',
            'agenda' => [
                ['time' => '09:00-10:00', 'title' => 'Registration & Welcome Coffee'],
                ['time' => '10:00-11:30', 'title' => 'Keynote: Future of Manufacturing in Vietnam'],
                ['time' => '11:45-12:45', 'title' => 'Panel: Industry 4.0 Implementation'],
                ['time' => '14:00-15:30', 'title' => 'Technical Workshops'],
                ['time' => '15:45-17:00', 'title' => 'Networking Session']
            ],
            'speakers' => [
                ['name' => 'Dr. Nguyen Van A', 'title' => 'Director of Manufacturing Innovation'],
                ['name' => 'Ms. Tran Thi B', 'title' => 'Senior Engineering Manager'],
                ['name' => 'Mr. Le Van C', 'title' => 'Technology Consultant']
            ]
        ];
        
        return view('community.events.show', compact('event'));
    }
    
    /**
     * Register for event
     */
    public function register(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'dietary_requirements' => 'nullable|string',
            'special_requests' => 'nullable|string'
        ]);
        
        // Store registration (implement actual storage)
        // EventRegistration::create([...]);
        
        return back()->with('success', 'Registration successful! You will receive a confirmation email shortly.');
    }
    
    /**
     * Create new event (for organizers)
     */
    public function create()
    {
        $this->authorize('create-events'); // Implement authorization
        
        $eventTypes = [
            'conference' => 'Conference',
            'workshop' => 'Workshop',
            'webinar' => 'Webinar',
            'career_fair' => 'Career Fair',
            'networking' => 'Networking Event'
        ];
        
        return view('community.events.create', compact('eventTypes'));
    }
    
    /**
     * Store new event
     */
    public function store(Request $request)
    {
        $this->authorize('create-events'); // Implement authorization
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string',
            'date' => 'required|date|after:today',
            'time' => 'required',
            'location' => 'required|string',
            'max_attendees' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'registration_deadline' => 'required|date|before:date'
        ]);
        
        // Store event (implement actual storage)
        // Event::create($validated);
        
        return redirect()->route('events.index')
                        ->with('success', 'Event created successfully!');
    }
    
    /**
     * Get upcoming events for API
     */
    public function upcoming()
    {
        $events = collect([
            [
                'id' => 1,
                'title' => 'Vietnam Manufacturing Summit 2024',
                'date' => '2024-03-15',
                'location' => 'Ho Chi Minh City',
                'type' => 'conference'
            ],
            [
                'id' => 2,
                'title' => 'CAD Software Workshop',
                'date' => '2024-02-20',
                'location' => 'Hanoi',
                'type' => 'workshop'
            ]
        ]);
        
        return response()->json($events);
    }
    
    /**
     * Export events data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        // Mock data - replace with actual query
        $events = collect([
            [
                'title' => 'Vietnam Manufacturing Summit 2024',
                'type' => 'conference',
                'date' => '2024-03-15',
                'location' => 'Ho Chi Minh City',
                'attendees' => 250,
                'price' => 500000
            ]
        ]);
        
        if ($format === 'json') {
            return response()->json($events);
        }
        
        // CSV export
        $filename = 'events_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($events) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Title', 'Type', 'Date', 'Location', 'Attendees', 'Price']);
            
            foreach ($events as $event) {
                fputcsv($file, [
                    $event['title'],
                    $event['type'],
                    $event['date'],
                    $event['location'],
                    $event['attendees'],
                    $event['price']
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
