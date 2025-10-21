<?php

namespace App\Forms\Components;

use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SpatieMediaLibraryFileUpload extends FileUpload
{
    protected ?string $mediaCollection = 'cover';

    /**
     * Set the media collection name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function collection(string $name): static
    {
        $this->mediaCollection = $name;

        return $this;
    }

    /**
     * Create file upload component.
     *
     * @param string $name
     *
     * @return $this
     */
    public static function make(string $name = 'image'): static
    {
        /** @var $this $component */
        $component = parent::make($name)
            ->label(__('Image'))
            ->dehydrated(false);

        $component->afterStateHydrated(function ($set, $record) use ($name, $component) {
            if ($record && $record->hasMedia($component->mediaCollection)) {
                $mediaItems = $record->getMedia($component->mediaCollection);
                $paths      = $mediaItems->map(fn ($media) => $media->getPathRelativeToRoot())->toArray();
                $set($name, $paths);
            }
        });

        $component->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $state, $record = null) use ($component) {
            if ($record) {
                $media = $record->addMediaFromDisk($file->getRealPath())
                    ->toMediaCollection($component->mediaCollection);

                return $media->getPathRelativeToRoot();
            }

            Notification::make()
                ->title(__('Image can\'t upload, create record first'))
                ->warning()
                ->send();

            return '';
        });

        return $component;
    }
}
