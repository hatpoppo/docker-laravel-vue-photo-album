<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PhotoListApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function should_正しい構造のJSONを返却する()
    {
        Photo::factory(5)->create();
        $response = $this->json('GET', route('photo.index'));
        $photos = Photo::with(['owner'])->orderBy('created_at', 'desc')->get();

        $expected_data = $photos->map(function ($photo) {
            return [
                'id' => $photo->id,
                'url' => $photo->url,
                'owner'=>[
                    'name' => $photo->owner->name,
                ],
                'likes_count' => 0,
                'liked_by_user' => false,
                ];
        })->all();

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment([
                'data'=>$expected_data
            ]);
    }
}
