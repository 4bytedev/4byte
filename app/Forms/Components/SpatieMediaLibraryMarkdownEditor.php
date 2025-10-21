<?php

namespace App\Forms\Components;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Notifications\Notification;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SpatieMediaLibraryMarkdownEditor extends MarkdownEditor
{
    protected ?string $mediaCollection = 'content';

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
     * Create markdown editor.
     *
     * @param string $name
     *
     * @return $this
     */
    public static function make(string $name = 'image'): static
    {
        /** @var $this $component */
        $component = parent::make($name);

        $component->saveUploadedFileAttachmentsUsing(function (TemporaryUploadedFile $file, $state, $record = null) use ($component) {
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
