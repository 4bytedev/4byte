<?php

namespace Packages\News\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $news = [
            [
                'title' => 'GitHub Announces New AI-Powered Code Review Features',
                'slug' => 'github-announces-ai-powered-code-review-features',
                'excerpt' => 'GitHub introduces revolutionary AI assistance for code reviews, promising to reduce review time by 40%.',
                'content' => 'GitHub has unveiled its latest AI-powered code review system that integrates seamlessly with existing workflows. The new features include automated vulnerability detection, code quality analysis, and intelligent suggestions for improvements. Early beta testing shows significant reduction in review cycles and improved code quality across participating organizations.',
                'image' => 'news/github-ai-features.jpg',
                'status' => 'PUBLISHED',
                'user_id' => 1,
                'published_at' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'title' => 'Laravel 11 Officially Released with Major Performance Improvements',
                'slug' => 'laravel-11-officially-released-performance-improvements',
                'excerpt' => 'The latest version of Laravel brings significant performance enhancements and new developer-friendly features.',
                'content' => 'Laravel 11 has officially launched, featuring improved query performance, streamlined directory structure, and enhanced developer experience. Key highlights include faster boot times, optimized Eloquent queries, and new Artisan commands that simplify common development tasks. The framework continues to prioritize developer happiness while maintaining enterprise-grade reliability.',
                'image' => 'news/laravel-11-release.jpg',
                'status' => 'PUBLISHED',
                'user_id' => 5,
                'published_at' => Carbon::now()->subDays(5),
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'title' => 'Major Security Vulnerability Discovered in Popular NPM Package',
                'slug' => 'major-security-vulnerability-npm-package',
                'excerpt' => 'Critical security flaw affects millions of Node.js applications worldwide, immediate updates recommended.',
                'content' => 'A critical security vulnerability has been identified in a widely-used NPM package, potentially affecting millions of applications. The vulnerability allows unauthorized access to sensitive data and has prompted immediate security advisories from major cloud providers. Developers are urged to update their dependencies and review their security practices.',
                'image' => 'news/npm-security-vulnerability.jpg',
                'status' => 'PUBLISHED',
                'user_id' => 3,
                'published_at' => Carbon::now()->subHours(6),
                'created_at' => Carbon::now()->subHours(8),
                'updated_at' => Carbon::now()->subHours(6),
            ],
            [
                'title' => 'AWS Introduces New Serverless Database Service',
                'slug' => 'aws-introduces-serverless-database-service',
                'excerpt' => 'Amazon Web Services launches a fully managed serverless database solution with automatic scaling capabilities.',
                'content' => 'AWS has announced a new serverless database service that automatically scales based on demand, eliminating the need for capacity planning. The service supports both SQL and NoSQL workloads and integrates seamlessly with existing AWS services. Early adopters report significant cost savings and improved application performance.',
                'image' => 'news/aws-serverless-database.jpg',
                'status' => 'PUBLISHED',
                'user_id' => 3,
                'published_at' => Carbon::now()->subDays(8),
                'created_at' => Carbon::now()->subDays(9),
                'updated_at' => Carbon::now()->subDays(8),
            ],
            [
                'title' => 'React 19 Beta Released with Exciting New Features',
                'slug' => 'react-19-beta-released-new-features',
                'excerpt' => 'The React team unveils React 19 beta with improved performance and developer experience enhancements.',
                'content' => 'React 19 beta introduces several groundbreaking features including enhanced server components, improved hydration, and new hooks for better state management. The release focuses on performance optimizations and developer productivity, with backwards compatibility maintained for existing applications.',
                'image' => 'news/react-19-beta.jpg',
                'status' => 'PUBLISHED',
                'user_id' => 4,
                'published_at' => Carbon::now()->subDays(12),
                'created_at' => Carbon::now()->subDays(13),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'title' => 'Google Chrome to Block Third-Party Cookies by Default',
                'slug' => 'google-chrome-blocks-third-party-cookies',
                'excerpt' => 'Major privacy update affects millions of websites and advertising platforms worldwide.',
                'content' => 'Google Chrome will begin blocking third-party cookies by default in the coming months, marking a significant shift in web privacy. This change affects advertising networks, analytics platforms, and many websites that rely on cross-site tracking. Developers are advised to implement privacy-friendly alternatives and prepare for the transition.',
                'image' => 'news/chrome-privacy-update.jpg',
                'status' => 'DRAFT',
                'user_id' => 2,
                'published_at' => null,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('news')->insert($news);

        // Create news-category relationships
        $newsCategories = [
            ['news_id' => 1, 'category_id' => 15, 'created_at' => now(), 'updated_at' => now()], // Open Source
            ['news_id' => 1, 'category_id' => 1, 'created_at' => now(), 'updated_at' => now()], // Web Development

            ['news_id' => 2, 'category_id' => 7, 'created_at' => now(), 'updated_at' => now()], // Backend Development
            ['news_id' => 2, 'category_id' => 1, 'created_at' => now(), 'updated_at' => now()], // Web Development

            ['news_id' => 3, 'category_id' => 10, 'created_at' => now(), 'updated_at' => now()], // Security

            ['news_id' => 4, 'category_id' => 11, 'created_at' => now(), 'updated_at' => now()], // Cloud Computing
            ['news_id' => 4, 'category_id' => 9, 'created_at' => now(), 'updated_at' => now()], // Database

            ['news_id' => 5, 'category_id' => 8, 'created_at' => now(), 'updated_at' => now()], // Frontend Development
            ['news_id' => 5, 'category_id' => 1, 'created_at' => now(), 'updated_at' => now()], // Web Development

            ['news_id' => 6, 'category_id' => 10, 'created_at' => now(), 'updated_at' => now()], // Security
            ['news_id' => 6, 'category_id' => 1, 'created_at' => now(), 'updated_at' => now()], // Web Development
        ];

        DB::table('news_category')->insert($newsCategories);

        // Create news-tag relationships
        $newsTags = [
            ['news_id' => 1, 'tag_id' => 17, 'created_at' => now(), 'updated_at' => now()], // Git
            ['news_id' => 1, 'tag_id' => 3, 'created_at' => now(), 'updated_at' => now()], // JavaScript

            ['news_id' => 2, 'tag_id' => 2, 'created_at' => now(), 'updated_at' => now()], // Laravel
            ['news_id' => 2, 'tag_id' => 1, 'created_at' => now(), 'updated_at' => now()], // PHP

            ['news_id' => 3, 'tag_id' => 6, 'created_at' => now(), 'updated_at' => now()], // Node.js
            ['news_id' => 3, 'tag_id' => 3, 'created_at' => now(), 'updated_at' => now()], // JavaScript

            ['news_id' => 4, 'tag_id' => 14, 'created_at' => now(), 'updated_at' => now()], // AWS

            ['news_id' => 5, 'tag_id' => 4, 'created_at' => now(), 'updated_at' => now()], // React
            ['news_id' => 5, 'tag_id' => 3, 'created_at' => now(), 'updated_at' => now()], // JavaScript

            ['news_id' => 6, 'tag_id' => 3, 'created_at' => now(), 'updated_at' => now()], // JavaScript
            ['news_id' => 6, 'tag_id' => 18, 'created_at' => now(), 'updated_at' => now()], // CSS
        ];

        DB::table('news_tag')->insert($newsTags);
    }
}
