<?php

namespace Tests\Unit;

use App\Support\ImageStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class ImageStorageTest extends TestCase
{
    public function test_it_stores_uploaded_jpeg_as_webp_on_public_disk(): void
    {
        Storage::fake('public');

        $path = ImageStorage::storeAsWebp(
            UploadedFile::fake()->image('avatar.jpg', 80, 80),
            'profiles'
        );

        $this->assertStringStartsWith('profiles/', $path);
        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);
        $this->assertSame('image/webp', mime_content_type(Storage::disk('public')->path($path)));
    }

    public function test_it_stores_uploaded_png_as_webp_on_public_disk(): void
    {
        Storage::fake('public');

        $path = ImageStorage::storeAsWebp(
            UploadedFile::fake()->image('portfolio.png', 120, 80),
            'portofolios'
        );

        $this->assertStringStartsWith('portofolios/', $path);
        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);
        $this->assertSame('image/webp', mime_content_type(Storage::disk('public')->path($path)));
    }

    public function test_it_rejects_unsupported_file_types(): void
    {
        Storage::fake('public');

        $this->expectException(RuntimeException::class);

        ImageStorage::storeAsWebp(
            UploadedFile::fake()->create('document.pdf', 12, 'application/pdf'),
            'profiles'
        );
    }
}
