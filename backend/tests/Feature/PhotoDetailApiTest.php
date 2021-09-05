<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Comment;
use App\Models\Photo;

class PhotoDetailApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function test_return_struct_JSON()
    {
        Photo::factory()->create()->each(function ($photo) {
            $photo->comments()->saveMany(Comment::factory(1)->make());
        });
        $photo=Photo::first();

        $response=$this->json('GET', route('photo.show', [
            'id'=>$photo->id,
        ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id'=>$photo->id,
                'url'=>$photo->url,
                'owner'=>[
                    'name'=>$photo->owner->name,
                ],
                'comments' => $photo->comments
                    ->sortByDesc('id')
                    ->map(function ($comment) {
                        return[
                            'author' => [
                                "name" => $comment->author->name,
                            ],
                            'content' => $comment->content,
                        ];
                    })
                ->all(),
                'liked_by_user' => false,
                'likes_count' =>0,
            ]);
    }
}
