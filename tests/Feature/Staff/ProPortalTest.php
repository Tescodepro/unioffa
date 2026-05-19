<?php

use App\Models\News;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    // 1. Create permissions
    $this->proPortalPermission = Permission::firstOrCreate(
        ['identifier' => 'access_pro_portal'],
        ['id' => (string) Str::uuid(), 'name' => 'Access PRO Portal']
    );

    $this->manageWebsitePermission = Permission::firstOrCreate(
        ['identifier' => 'manage_website'],
        ['id' => (string) Str::uuid(), 'name' => 'Manage Website']
    );

    // 2. Create PRO user type and assign permissions
    $this->proUserType = UserType::create([
        'id' => (string) Str::uuid(),
        'name' => 'public relations officer',
        'dashboard_route' => 'pro.dashboard',
    ]);

    $this->proUserType->permissions()->sync([
        $this->proPortalPermission->id,
        $this->manageWebsitePermission->id,
    ]);

    // 3. Create PRO User
    $this->proUser = User::factory()->create([
        'user_type_id' => $this->proUserType->id,
        'must_change_password' => false,
    ]);

    // 4. Create non-PRO user type and User
    $this->otherUserType = UserType::create([
        'id' => (string) Str::uuid(),
        'name' => 'lecturer',
        'dashboard_route' => 'lecturer.dashboard',
    ]);

    $this->otherUser = User::factory()->create([
        'user_type_id' => $this->otherUserType->id,
        'must_change_password' => false,
    ]);

    // 5. Populate route permissions in DB (which dynamic.permission middleware reads)
    \App\Models\RoutePermission::updateOrCreate(
        ['route_name' => 'pro.dashboard'],
        ['permission_identifier' => 'access_pro_portal']
    );

    \App\Models\RoutePermission::updateOrCreate(
        ['route_name' => 'ict.news.index'],
        ['permission_identifier' => 'manage_website']
    );
});

test('unauthenticated users cannot access pro dashboard', function () {
    $this->get(route('pro.dashboard'))
        ->assertRedirect(route('staff.login'));
});

test('non-pro staff without permission cannot access pro dashboard', function () {
    actingAs($this->otherUser)
        ->get(route('pro.dashboard'))
        ->assertRedirect();
});

test('pro user can access pro dashboard and view news statistics', function () {
    News::create([
        'title' => 'News 1',
        'short_title' => 'News 1 Short',
        'slug' => 'news-1',
        'image' => 'news/dummy.jpg',
        'content' => 'Content 1',
        'is_active' => true,
    ]);

    News::create([
        'title' => 'News 2',
        'short_title' => 'News 2 Short',
        'slug' => 'news-2',
        'image' => 'news/dummy.jpg',
        'content' => 'Content 2',
        'is_active' => true,
    ]);

    News::create([
        'title' => 'News 3',
        'short_title' => 'News 3 Short',
        'slug' => 'news-3',
        'image' => 'news/dummy.jpg',
        'content' => 'Content 3',
        'is_active' => false,
    ]);

    actingAs($this->proUser)
        ->get(route('pro.dashboard'))
        ->assertSuccessful()
        ->assertViewIs('staff.pro.dashboard')
        ->assertViewHas('totalNews', 3)
        ->assertViewHas('activeNews', 2)
        ->assertViewHas('inactiveNews', 1)
        ->assertSee('Public Relations Officer Dashboard')
        ->assertSee('News 1')
        ->assertSee('News 2')
        ->assertSee('News 3')
        ->assertSee(route('ict.news.index'));
});

test('pro user can access website news management page', function () {
    actingAs($this->proUser)
        ->get(route('ict.news.index'))
        ->assertSuccessful()
        ->assertViewIs('staff.ict.news')
        ->assertSee('Latest News');
});
