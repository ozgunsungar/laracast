<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::truncate();
        Post::truncate();
        Category::truncate();

        $user = User::factory()->create();

        $personal = Category::create([
            'name' => 'Personal',
            'slug' => 'personal'
        ]);
        $family = Category::create([
            'name' => 'Family',
            'slug' => 'family'
        ]);
        $work = Category::create([
            'name' => 'Work',
            'slug' => 'work'
        ]);

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Post::create([
            'user_id' => $user->id,
            'category_id' => $family->id,
            'title'=>'My Family Post',
            'slug'=>'my-first-post',
            'excerpt'=>'Lorem ipsum dolar sit amet',
            'body'=>'Lorem ipsum dolar'
        ]);

        Post::create([
            'user_id' => $user->id,
            'category_id' => $personal->id,
            'title'=>'My Personal Post',
            'slug'=>'my-personal-post',
            'excerpt'=>'Lorem ipsum dolar sit amet',
            'body'=>'Lorem ipsum dolar'
        ]);
        Post::create([
            'user_id' => $user->id,
            'category_id' => $work->id,
            'title'=>'My Work Post',
            'slug'=>'my-work-post',
            'excerpt'=>'Lorem ipsum dolar sit amet',
            'body'=>'Lorem ipsum dolar'
        ]);

    }
}
