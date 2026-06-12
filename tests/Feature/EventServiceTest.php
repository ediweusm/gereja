<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventAssignment;
use App\Models\MinistryRole;
use App\Models\Rayon;
use App\Models\Family;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_ministry_role()
    {
        $role = MinistryRole::create([
            'name' => 'Pendeta',
            'sort_order' => 1,
        ]);

        $this->assertDatabaseHas('ministry_roles', [
            'id' => $role->id,
            'name' => 'Pendeta',
            'sort_order' => 1,
        ]);
    }

    public function test_can_create_event_with_assignments()
    {
        $rayon = Rayon::create(['name' => 'Rayon A']);
        $family = Family::create([
            'family_number' => '1234567890',
            'rayon_id' => $rayon->id,
            'address' => 'Jl. Merdeka No. 1',
        ]);
        $member = Member::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'family_id' => $family->id,
        ]);

        $role = MinistryRole::create([
            'name' => 'Pengkhotbah',
            'sort_order' => 1,
        ]);

        $event = Event::create([
            'name' => 'Ibadah Raya Minggu',
            'theme' => 'Sukacita Melayani',
            'event_date' => '2026-06-14',
            'start_time' => '09:00:00',
            'event_type' => 'Ibadah Raya',
            'mode' => 'onsite',
            'rayon_id' => $rayon->id,
            'host_family_id' => $family->id,
            'location_notes' => 'Gedung Utama Lt. 1',
        ]);

        $assignment = EventAssignment::create([
            'event_id' => $event->id,
            'ministry_role_id' => $role->id,
            'member_id' => $member->id,
        ]);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'name' => 'Ibadah Raya Minggu',
        ]);

        $this->assertDatabaseHas('event_assignments', [
            'id' => $assignment->id,
            'event_id' => $event->id,
            'ministry_role_id' => $role->id,
            'member_id' => $member->id,
            'guest_name' => null,
        ]);

        // Test relations
        $this->assertEquals('Rayon A', $event->rayon->name);
        $this->assertEquals('1234567890', $event->hostFamily->family_number);
        $this->assertCount(1, $event->assignments);
        $this->assertEquals('Pengkhotbah', $event->assignments->first()->ministryRole->name);
        $this->assertEquals('John Doe', $event->assignments->first()->member->fullName);
    }

    public function test_print_by_range_requires_authentication()
    {
        $response = $this->get(route('events.print_by_range', ['start_date' => '2026-06-01', 'end_date' => '2026-06-30']));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_print_by_range()
    {
        $user = \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test@sig.test',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('events.print_by_range', ['start_date' => '2026-06-01', 'end_date' => '2026-06-30']));
        $response->assertStatus(200);
        $response->assertViewIs('reports.events-by-date');
    }

    public function test_print_admissions_by_range_requires_authentication()
    {
        $response = $this->get(route('reports.admissions_by_range', ['start_date' => '2026-06-01', 'end_date' => '2026-06-30']));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_print_admissions_by_range()
    {
        $user = \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test_admission@sig.test',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('reports.admissions_by_range', ['start_date' => '2026-06-01', 'end_date' => '2026-06-30']));
        $response->assertStatus(200);
        $response->assertViewIs('reports.admissions-by-range');
    }

    public function test_print_members_requires_authentication()
    {
        $response = $this->get(route('reports.members_list'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_print_members()
    {
        $user = \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test_members@sig.test',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('reports.members_list'));
        $response->assertStatus(200);
        $response->assertViewIs('reports.members-list');
    }

    public function test_print_journal_range_requires_authentication()
    {
        $response = $this->get(route('reports.journal_range', ['start_date' => '2026-06-01', 'end_date' => '2026-06-30']));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_print_journal_range()
    {
        $user = \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test_journals@sig.test',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('reports.journal_range', ['start_date' => '2026-06-01', 'end_date' => '2026-06-30']));
        $response->assertStatus(200);
        $response->assertViewIs('reports.journal-range');
    }
}
