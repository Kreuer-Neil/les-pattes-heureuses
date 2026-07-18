<?php

namespace App\Jobs;

use File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

class HandleAnimalsImageUploads implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected string $imageName;
    protected ?string $oldImageName;
    protected string $imagePath;
    protected string $directory;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $imageName,
        ?string $oldImageName,
        string $imagePath,
        string $directory
    )
    {
        $this->imageName = $imageName;
        $this->oldImageName = $oldImageName;
        $this->imagePath = $imagePath;
        $this->directory = $directory;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $imageManager = ImageManager::usingDriver(Driver::class);
        // rewrite image sizes to adapt to the app
        $scales = ['small' => 32, 'medium' => 64, 'large' => 160];

        foreach ($scales as $key => $scale) {
            $image = $imageManager->decodePath((storage_path().'/app/public/' . $this->imagePath));
            $image->cover($scale, $scale);
            $encoded = $image->encodeUsingFormat(Format::WEBP, quality: 65);
            $encoded->save(storage_path()."/app/public/images/$this->directory/$key/$this->imageName.webp");

            unset($image, $encoded);

            if ($this->oldImageName) {
                $oldFilePath = storage_path()."/app/public/images/$this->directory/$key/$this->oldImageName.webp";
                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }
        }

        if (File::exists(storage_path()."/app/public/images/users/$this->imageName.webp")) {
            File::delete(storage_path()."/app/public/images/users/$this->imageName.webp");
        }
    }
}
