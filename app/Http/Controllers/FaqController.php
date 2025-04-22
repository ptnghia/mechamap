<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Display the FAQ page.
     */
    public function index(): View
    {
        $categories = [
            [
                'name' => 'General',
                'questions' => [
                    [
                        'question' => 'What is this forum about?',
                        'answer' => 'This forum is a community dedicated to discussing various topics related to our interests. It\'s a place to share ideas, ask questions, and connect with like-minded individuals.'
                    ],
                    [
                        'question' => 'How do I create an account?',
                        'answer' => 'To create an account, click on the "Register" button in the top right corner of the page. Fill out the registration form with your details and follow the instructions to complete the process.'
                    ],
                    [
                        'question' => 'Is registration free?',
                        'answer' => 'Yes, basic registration is completely free. We also offer premium subscription plans with additional features for those who want to enhance their experience.'
                    ]
                ]
            ],
            [
                'name' => 'Account',
                'questions' => [
                    [
                        'question' => 'How do I change my password?',
                        'answer' => 'To change your password, go to your Account Settings page. Under the "Security" section, you\'ll find the option to change your password.'
                    ],
                    [
                        'question' => 'How do I update my profile information?',
                        'answer' => 'You can update your profile information by going to your Account Settings page. There, you can edit your personal details, upload a profile picture, and customize your profile.'
                    ],
                    [
                        'question' => 'What happens if I forget my password?',
                        'answer' => 'If you forget your password, click on the "Forgot Password" link on the login page. Enter your email address, and we\'ll send you instructions to reset your password.'
                    ]
                ]
            ],
            [
                'name' => 'Posting',
                'questions' => [
                    [
                        'question' => 'How do I create a new thread?',
                        'answer' => 'To create a new thread, navigate to the forum where you want to post, then click on the "New Thread" button. Fill out the form with your thread title and content, then submit it.'
                    ],
                    [
                        'question' => 'Can I edit or delete my posts?',
                        'answer' => 'Yes, you can edit or delete your own posts. Look for the edit or delete options below your post. Note that there may be time limitations on editing or deleting posts.'
                    ],
                    [
                        'question' => 'How do I format my posts?',
                        'answer' => 'We support Markdown formatting for posts. You can use Markdown syntax to add formatting such as bold, italic, lists, links, and more. There\'s a formatting guide available in the post editor.'
                    ]
                ]
            ],
            [
                'name' => 'Features',
                'questions' => [
                    [
                        'question' => 'What is the Showcase feature?',
                        'answer' => 'The Showcase feature allows users to highlight their best content or projects. You can add items to your showcase from your profile page.'
                    ],
                    [
                        'question' => 'How does the Gallery work?',
                        'answer' => 'The Gallery is a place to share images and media. You can upload images, organize them into albums, and share them with the community.'
                    ],
                    [
                        'question' => 'What are the benefits of upgrading my account?',
                        'answer' => 'Upgrading to a premium account gives you access to additional features such as ad-free browsing, unlimited private messages, custom profile badges, and more.'
                    ]
                ]
            ]
        ];
        
        return view('faq.index', compact('categories'));
    }
}
