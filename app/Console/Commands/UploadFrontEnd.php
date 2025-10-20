<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UploadFrontEnd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:upload-front-end {--c|clean : Remove old build files from storage before uploading}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload front-end build to storage';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $localPath = public_path('build');
        $s3Path    = 'assets/build';

        if ($this->option('clean')) {
            $this->info('Cleaning old build files from...');
            Storage::deleteDirectory($s3Path);
        }

        $this->info("Uploading build files from {$localPath}...");

        $files = File::allFiles($localPath);

        foreach ($files as $file) {
            $relativePath = str_replace($localPath . '/', '', $file->getPathname());
            $s3FilePath   = $s3Path . '/' . $relativePath;

            Storage::put($s3FilePath, File::get($file->getPathname()), 'public');
            $this->line("Uploaded: {$s3FilePath}");
        }

        $this->info('✅ Front-end build uploaded successfully!');
    }
}
