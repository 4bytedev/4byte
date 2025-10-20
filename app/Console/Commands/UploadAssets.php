<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UploadAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:upload-assets {--c|clean : Remove old build files from storage before uploading}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload assets to storage';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $assetDirs = ['css', 'js', 'vendor'];
        $s3Path    = 'assets';

        if ($this->option('clean')) {
            $this->info('Cleaning old build files...');
            Storage::deleteDirectory($s3Path);
        }

        foreach ($assetDirs as $dir) {
            $localPath = public_path($dir);
            $files     = File::allFiles($localPath);

            foreach ($files as $file) {
                $relativePath = str_replace($localPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $s3FilePath   = $s3Path . '/' . $dir . '/' . $relativePath;

                Storage::put($s3FilePath, File::get($file->getPathname()), 'public');
                $this->line("Uploaded: {$s3FilePath}");
            }
        }

        $this->info('✅ Assets uploaded successfully!');
    }
}
