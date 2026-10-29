<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationAndRosterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_student_and_report_pages_to_login(): void
    {
        $this->get(route('students.index'))->assertRedirect(route('login'));
        $this->get(route('reports.failed'))->assertRedirect(route('login'));
        $this->get(route('reports.centralizer'))->assertRedirect(route('login'));
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create(['email' => 'docente@example.test']);

        $this->post(route('login.store'), [
            'email' => 'docente@example.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertOk();

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $this->post(route('login.store'), [
            'email' => 'docente@example.test',
            'password' => 'incorrecta',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
