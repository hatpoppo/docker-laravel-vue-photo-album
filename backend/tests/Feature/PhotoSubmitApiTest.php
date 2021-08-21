<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Photo;

class PhotoSubmitApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp():void
    {
        parent::setUp();
        $this->user=User::factory()->create();
    }
    /**
     *
     * @test
     */
    public function sould_can_upload()
    {
        Storage::fake('public');
        $response=$this->actingAs($this->user)
        ->json('POST', route('photo.create'), [
            'photo' => UploadedFile::fake()->image('photo.jpg')
        ]);
        $response->assertStatus(201);

        $photo = Photo::first();
        $this->assertMatchesRegularExpression('/^[0-9a-zA-Z-_]{12}$/', $photo->id);

        Storage::disk('public')->exists($photo->filename);
    }
    /**
     * @test
     */
    public function should_not_save_with_database_error()
    {
        Schema::drop('photos');
        Storage::fake('public');
        $response = $this->actingAs($this->user)
        ->json('POST', route('photo.create'), [
            'photo'=>UploadedFile::fake()->image('photo.jpg'),
        ]);
        $response->assertStatus(500);
        $this->assertEquals(0, count(Storage::disk('public')->files()));
    }
    /**
     * @test
     */
    public function sholud_not_insert_db_when_file_save_error()
    {
        Storage::shouldReceive('disk')->once()->andReturnNull();
        $response = $this->actingAs($this->user)
        ->json('POST', route('photo.create'), [
            'photo'=>UploadedFile::fake()->image('photo.jpg'),
        ]);
        $response->assertStatus(500);
        $this->assertEmpty(Photo::all());
    }
}
