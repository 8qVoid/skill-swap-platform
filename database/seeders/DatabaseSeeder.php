<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Swap;
use App\Models\SwapMessage;
use App\Models\SwapRequest;
use App\Models\SwapSession;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = collect([
            [
                'name' => 'Ava Santos',
                'email' => 'ava@example.com',
                'password' => Hash::make('password'),
                'role' => 'teach-and-learn',
                'profile_photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=400&q=80',
                'bio' => 'Product designer helping beginners shape better mobile experiences while learning JavaScript for more interactive prototypes.',
                'location' => 'Manila, Philippines',
                'timezone' => 'Asia/Manila',
                'availability' => 'Weeknights',
                'skill_level' => 'Intermediate',
                'teach_skills' => ['UI Design', 'Figma', 'Design Systems'],
                'learn_skills' => ['JavaScript', 'React', 'Public Speaking'],
                'formats' => ['video call', 'chat'],
                'portfolio_links' => ['https://dribbble.com'],
                'saved_users' => [],
                'onboarding_completed' => true,
                'is_verified' => true,
            ],
            [
                'name' => 'Noah Reyes',
                'email' => 'noah@example.com',
                'password' => Hash::make('password'),
                'role' => 'teach-and-learn',
                'profile_photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80',
                'bio' => 'Frontend engineer who enjoys teaching React basics and wants to improve visual design thinking.',
                'location' => 'Cebu, Philippines',
                'timezone' => 'Asia/Manila',
                'availability' => 'Weeknights',
                'skill_level' => 'Intermediate',
                'teach_skills' => ['React', 'JavaScript', 'Frontend Basics'],
                'learn_skills' => ['Figma', 'Brand Design'],
                'formats' => ['video call', 'recorded lessons'],
                'portfolio_links' => ['https://github.com'],
                'saved_users' => [],
                'onboarding_completed' => true,
                'is_verified' => true,
            ],
            [
                'name' => 'Mia Cruz',
                'email' => 'mia@example.com',
                'password' => Hash::make('password'),
                'role' => 'teach-and-learn',
                'profile_photo' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=400&q=80',
                'bio' => 'Communication coach trading public speaking sessions for video editing help.',
                'location' => 'Davao, Philippines',
                'timezone' => 'Asia/Manila',
                'availability' => 'Weekends',
                'skill_level' => 'Advanced',
                'teach_skills' => ['Public Speaking', 'Presentation Coaching'],
                'learn_skills' => ['Video Editing', 'Premiere Pro'],
                'formats' => ['video call', 'in person'],
                'portfolio_links' => ['https://linkedin.com'],
                'saved_users' => [],
                'onboarding_completed' => true,
                'is_verified' => false,
            ],
            [
                'name' => 'Liam Tan',
                'email' => 'liam@example.com',
                'password' => Hash::make('password'),
                'role' => 'teach-and-learn',
                'profile_photo' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=400&q=80',
                'bio' => 'Freelance video editor looking to sharpen storytelling and speaking confidence.',
                'location' => 'Quezon City, Philippines',
                'timezone' => 'Asia/Manila',
                'availability' => 'Weekends',
                'skill_level' => 'Intermediate',
                'teach_skills' => ['Video Editing', 'Premiere Pro'],
                'learn_skills' => ['Public Speaking', 'Storytelling'],
                'formats' => ['chat', 'recorded lessons'],
                'portfolio_links' => ['https://youtube.com'],
                'saved_users' => [],
                'onboarding_completed' => true,
                'is_verified' => true,
            ],
            [
                'name' => 'Demo User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'role' => 'teach-and-learn',
                'profile_photo' => 'https://images.unsplash.com/photo-1502685104226-ee32379fefbe?auto=format&fit=crop&w=400&q=80',
                'bio' => 'Portfolio demo account for exploring every part of the app.',
                'location' => 'Makati, Philippines',
                'timezone' => 'Asia/Manila',
                'availability' => 'Flexible',
                'skill_level' => 'Beginner',
                'teach_skills' => ['HTML', 'Canva'],
                'learn_skills' => ['Python', 'UI Design', 'React'],
                'formats' => ['video call', 'chat'],
                'portfolio_links' => ['https://portfolio.example.com'],
                'saved_users' => [1, 2],
                'onboarding_completed' => true,
                'is_verified' => true,
            ],
        ])->map(fn (array $data) => User::updateOrCreate(['email' => $data['email']], $data));

        $request = SwapRequest::updateOrCreate(
            ['requester_id' => $users[4]->id, 'receiver_id' => $users[0]->id],
            [
                'skill_to_learn' => 'UI Design',
                'skill_to_offer' => 'React',
                'message' => 'Hi, I can help with React basics if you can coach me through Figma and UI principles.',
                'proposed_schedule' => 'Saturday 3:00 PM',
                'preferred_format' => 'video call',
                'status' => 'accepted',
            ]
        );

        $swap = Swap::updateOrCreate(
            ['swap_request_id' => $request->id],
            [
                'requester_id' => $request->requester_id,
                'receiver_id' => $request->receiver_id,
                'skill_to_learn' => $request->skill_to_learn,
                'skill_to_offer' => $request->skill_to_offer,
                'format' => $request->preferred_format,
                'status' => 'active',
                'progress_notes' => 'First intro session done. Next up: wireframe critique and React component walkthrough.',
                'progress_percent' => 45,
            ]
        );

        SwapMessage::updateOrCreate(
            ['swap_id' => $swap->id, 'user_id' => $users[4]->id, 'body' => 'Thanks for accepting. I shared my current landing page draft.']
        );

        SwapMessage::updateOrCreate(
            ['swap_id' => $swap->id, 'user_id' => $users[0]->id, 'body' => 'Looks good. Let’s review hierarchy and button states during our call.']
        );

        SwapSession::updateOrCreate(
            ['swap_id' => $swap->id, 'topic' => 'Landing page review'],
            [
                'scheduled_for' => now()->addDays(2),
                'meeting_link' => 'https://meet.google.com/demo-skill-swap',
                'status' => 'upcoming',
            ]
        );

        Review::updateOrCreate(
            ['swap_id' => $swap->id, 'reviewer_id' => $users[0]->id],
            [
                'reviewee_id' => $users[4]->id,
                'rating' => 5,
                'feedback' => 'Prepared, curious, and easy to coach. Great exchange partner.',
            ]
        );
    }
}
