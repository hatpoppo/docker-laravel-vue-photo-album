<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Photo;
use App\Models\User;

class LikeApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp():void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Photo::factory()->create();
        $this->photo = Photo::first();
    }
    /**
     * @test
     */
    public function should_add_like()
    {
        $response = $this->actingAs($this->user)
            ->json('PUT', route('photo.like', [
                'id' => $this->photo->id,
            ]));
        
        $response->assertStatus(200)
            ->assertJsonFragment([
                'photo_id' => $this->photo->id,
            ]);

        $this->assertEquals(1, $this->photo->likes()->count());
    }
    /**
     * @test
     */
    public function should_２回同じ写真にいいねしても１個しかいいねがつかない()
    {
        $param=['id'=>$this->photo->id];
        $this->actingAs($this->user)->json('PUT', route('photo.like', $param));
        $this->actingAs($this->user)->json('PUT', route('photo.like', $param));

        $this->assertEquals(1, $this->photo->likes()->count());
    }
    /**
     * @test
     */
    public function should_いいねを解除できる()
    {
        $this->photo->likes()->attach($this->user->id);

        $response=$this->actingAs($this->user)
            ->json('DELETE', route('photo.like', [
                'id'=>$this->photo->id,
            ]));
        
        $this->assertEquals(0, $this->photo->likes()->count());
    }
}
