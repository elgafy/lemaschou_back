<?php

namespace Tests\Unit;


use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadImageTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_can_connect(): void
    {
        $imageUrl = 'https://placehold.co/600x400/EEE/31343C';

        //fetch image from url
        $image = file_get_contents($imageUrl);

        //upload image to s3 bucket
        Storage::disk('s3')->put('aws_test.jpg', $image);

        //fetch image from s3 bucket
        $imageFromS3 = Storage::disk('s3')->get('aws_test.jpg');

        $this->assertEquals($image, $imageFromS3);
    }
}
