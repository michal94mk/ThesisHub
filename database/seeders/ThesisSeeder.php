<?php

namespace Database\Seeders;

use App\Models\Thesis;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThesisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users
        $supervisor1 = User::where('email', 'kowalski@thesis.pl')->first();
        $supervisor2 = User::where('email', 'nowak@thesis.pl')->first();
        $supervisor3 = User::where('email', 'wisniewski@thesis.pl')->first();

        $student1 = User::where('email', 'student1@thesis.pl')->first();
        $student2 = User::where('email', 'student2@thesis.pl')->first();
        $student3 = User::where('email', 'student3@thesis.pl')->first();
        $student4 = User::where('email', 'student4@thesis.pl')->first();
        $student5 = User::where('email', 'student5@thesis.pl')->first();

        // Thesis 1 - Pending approval
        Thesis::create([
            'student_id' => $student1->id,
            'supervisor_id' => $supervisor1->id,
            'title' => 'Machine Learning Approach to Network Traffic Analysis',
            'type' => Thesis::TYPE_BACHELOR,
            'field_of_study' => 'Computer Science',
            'specialization' => 'Artificial Intelligence',
            'abstract' => 'This thesis explores the application of machine learning algorithms for analyzing network traffic patterns to detect anomalies and potential security threats.',
            'outline' => '1. Introduction\n2. Literature Review\n3. Methodology\n4. Implementation\n5. Results\n6. Conclusion',
            'keywords' => ['machine learning', 'network security', 'traffic analysis', 'anomaly detection'],
            'status' => Thesis::STATUS_PENDING_APPROVAL,
            'academic_year' => '2024/2025',
            'submitted_at' => now()->subDays(2),
        ]);

        // Thesis 2 - Approved and in progress
        Thesis::create([
            'student_id' => $student2->id,
            'supervisor_id' => $supervisor2->id,
            'title' => 'Web Application for University Course Management',
            'type' => Thesis::TYPE_BACHELOR,
            'field_of_study' => 'Computer Science',
            'specialization' => 'Software Engineering',
            'abstract' => 'Development of a modern web application using Laravel and Vue.js for managing university courses, student enrollments, and grade tracking.',
            'outline' => '1. Analysis of existing systems\n2. Requirements specification\n3. System architecture\n4. Implementation\n5. Testing\n6. Deployment',
            'keywords' => ['web development', 'Laravel', 'Vue.js', 'university management'],
            'status' => Thesis::STATUS_APPROVED,
            'academic_year' => '2024/2025',
            'submitted_at' => now()->subDays(30),
            'approved_at' => now()->subDays(28),
        ]);

        // Thesis 3 - Under review
        Thesis::create([
            'student_id' => $student3->id,
            'supervisor_id' => $supervisor1->id,
            'title' => 'Blockchain-based Supply Chain Management System',
            'type' => Thesis::TYPE_MASTER,
            'field_of_study' => 'Computer Science',
            'specialization' => 'Distributed Systems',
            'abstract' => 'Implementation of a decentralized supply chain management system using blockchain technology to ensure transparency and traceability.',
            'outline' => '1. Introduction to blockchain\n2. Supply chain challenges\n3. Proposed solution\n4. Smart contracts\n5. Implementation\n6. Evaluation',
            'keywords' => ['blockchain', 'supply chain', 'smart contracts', 'distributed systems'],
            'status' => Thesis::STATUS_UNDER_REVIEW,
            'academic_year' => '2024/2025',
            'submission_date' => now()->subMonths(3),
            'approved_at' => now()->subMonths(4),
        ]);

        // Thesis 4 - Returned for corrections
        Thesis::create([
            'student_id' => $student4->id,
            'supervisor_id' => $supervisor3->id,
            'title' => 'Mobile Application for Health Monitoring',
            'type' => Thesis::TYPE_BACHELOR,
            'field_of_study' => 'Computer Science',
            'specialization' => 'Mobile Development',
            'abstract' => 'Development of a cross-platform mobile application for monitoring health metrics and providing personalized health recommendations.',
            'outline' => '1. Introduction\n2. Market analysis\n3. Technology stack\n4. Features implementation\n5. User testing\n6. Conclusion',
            'keywords' => ['mobile app', 'health monitoring', 'React Native', 'IoT'],
            'status' => Thesis::STATUS_RETURNED_FOR_CORRECTIONS,
            'academic_year' => '2024/2025',
            'supervisor_notes' => 'Please expand the literature review section and add more details about the data security measures implemented in the application.',
            'approved_at' => now()->subMonths(3),
        ]);

        // Thesis 5 - Draft
        Thesis::create([
            'student_id' => $student5->id,
            'supervisor_id' => $supervisor2->id,
            'title' => 'Cloud-Native Microservices Architecture for E-commerce',
            'type' => Thesis::TYPE_MASTER,
            'field_of_study' => 'Computer Science',
            'specialization' => 'Cloud Computing',
            'abstract' => 'Design and implementation of a scalable e-commerce platform using microservices architecture deployed on Kubernetes.',
            'outline' => '1. Introduction\n2. Microservices patterns\n3. Architecture design\n4. Implementation\n5. Performance testing\n6. Conclusion',
            'keywords' => ['microservices', 'cloud computing', 'Kubernetes', 'e-commerce'],
            'status' => Thesis::STATUS_DRAFT,
            'academic_year' => '2024/2025',
        ]);

        // Thesis 6 - Accepted
        Thesis::create([
            'student_id' => $student1->id,
            'supervisor_id' => $supervisor3->id,
            'title' => 'Automated Testing Framework for Web Applications',
            'type' => Thesis::TYPE_BACHELOR,
            'field_of_study' => 'Computer Science',
            'specialization' => 'Software Engineering',
            'abstract' => 'Development of a comprehensive automated testing framework for web applications using Selenium and Cypress.',
            'outline' => '1. Introduction\n2. Testing methodologies\n3. Framework design\n4. Implementation\n5. Case studies\n6. Results',
            'keywords' => ['automated testing', 'Selenium', 'Cypress', 'quality assurance'],
            'status' => Thesis::STATUS_ACCEPTED,
            'academic_year' => '2023/2024',
            'submission_date' => now()->subMonths(6),
            'approved_at' => now()->subMonths(8),
        ]);

        // Thesis 7 - Submitted for defense
        Thesis::create([
            'student_id' => $student2->id,
            'supervisor_id' => $supervisor1->id,
            'title' => 'Natural Language Processing for Sentiment Analysis',
            'type' => Thesis::TYPE_MASTER,
            'field_of_study' => 'Computer Science',
            'specialization' => 'Artificial Intelligence',
            'abstract' => 'Implementation of advanced NLP techniques for sentiment analysis in social media content using deep learning models.',
            'outline' => '1. Introduction\n2. NLP fundamentals\n3. Deep learning models\n4. Implementation\n5. Evaluation\n6. Conclusion',
            'keywords' => ['NLP', 'sentiment analysis', 'deep learning', 'social media'],
            'status' => Thesis::STATUS_SUBMITTED_FOR_DEFENSE,
            'academic_year' => '2023/2024',
            'defense_date' => now()->addWeeks(2),
            'approved_at' => now()->subMonths(10),
        ]);

        $this->command->info('✅ Sample theses created successfully!');
        $this->command->info('   - 7 theses with various statuses');
        $this->command->info('   - Distributed across supervisors and students');
    }
}
