<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Visitor;

class VisitorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Set the config explicitly for testing
        config(['app.dashboard_token' => 'athathi-admin-2024']);
    }

    /**
     * Test accessing dashboard without authentication redirects to login.
     */
    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Test accessing dashboard with invalid token redirects to login.
     */
    public function test_dashboard_rejects_invalid_token(): void
    {
        $response = $this->get('/dashboard?token=invalid-token');

        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
    }

    /**
     * Test accessing dashboard with valid token authenticates and redirects to clean URL.
     */
    public function test_dashboard_accepts_valid_token_and_redirects(): void
    {
        $response = $this->get('/dashboard?token=athathi-admin-2024');

        $response->assertRedirect('http://localhost/dashboard');
        $this->assertTrue(session('dashboard_authenticated'));

        // Follow redirection with active session
        $nextResponse = $this->withSession(['dashboard_authenticated' => true])->get('/dashboard');
        $nextResponse->assertStatus(200);
        $nextResponse->assertViewIs('dashboard');
    }

    /**
     * Test manual login authentication.
     */
    public function test_manual_login_with_correct_token(): void
    {
        $response = $this->post('/login', [
            'token' => 'athathi-admin-2024',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertTrue(session('dashboard_authenticated'));
    }

    /**
     * Test manual login with incorrect token.
     */
    public function test_manual_login_with_incorrect_token(): void
    {
        $response = $this->post('/login', [
            'token' => 'wrong-token',
        ]);

        $response->assertSessionHasErrors('token');
        $this->assertFalse(session()->has('dashboard_authenticated'));
    }

    /**
     * Test scanning a valid JSON QR code extracts fields.
     */
    public function test_scan_valid_json_qr_code(): void
    {
        $qrData = [
            'name' => 'Ahmad Tour',
            'email' => 'ahmad@example.com',
            'phone' => '099999999',
        ];

        $response = $this->withSession(['dashboard_authenticated' => true])
            ->postJson('/visitors/scan', [
                'qr_raw_data' => json_encode($qrData),
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'visitor' => [
                'name' => 'Ahmad Tour',
                'email' => 'ahmad@example.com',
                'phone' => '099999999',
            ],
        ]);

        $this->assertDatabaseHas('visitors', [
            'name' => 'Ahmad Tour',
            'email' => 'ahmad@example.com',
            'phone' => '099999999',
        ]);
    }

    /**
     * Test scanning a plain text QR code.
     */
    public function test_scan_plain_text_qr_code(): void
    {
        $plainText = 'My raw string badge identifier';

        $response = $this->withSession(['dashboard_authenticated' => true])
            ->postJson('/visitors/scan', [
                'qr_raw_data' => $plainText,
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'visitor' => [
                'qr_raw_data' => $plainText,
                'name' => null,
                'email' => null,
                'phone' => null,
            ],
        ]);

        $this->assertDatabaseHas('visitors', [
            'qr_raw_data' => $plainText,
            'name' => null,
            'email' => null,
            'phone' => null,
        ]);
    }

    /**
     * Test duplicate scan prevention.
     */
    public function test_prevent_duplicate_scans(): void
    {
        $plainText = 'UniqueBadgeText123';

        // Save visitor first time
        $visitor = Visitor::factory()->create([
            'qr_raw_data' => $plainText,
            'name' => 'First Visit',
        ]);

        // Attempt scanning again
        $response = $this->withSession(['dashboard_authenticated' => true])
            ->postJson('/visitors/scan', [
                'qr_raw_data' => $plainText,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'duplicate',
            'visitor' => [
                'id' => $visitor->id,
            ],
        ]);

        // Count in DB should still be 1
        $this->assertEquals(1, Visitor::where('qr_raw_data', $plainText)->count());
    }

    /**
     * Test CSV export downloads correct data.
     */
    public function test_csv_export(): void
    {
        Visitor::factory()->create([
            'name' => 'Export User',
            'email' => 'export@example.com',
            'qr_raw_data' => 'raw-data-123',
        ]);

        $response = $this->withSession(['dashboard_authenticated' => true])
            ->get('/visitors/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('Export User', $content);
        $this->assertStringContainsString('export@example.com', $content);
        $this->assertStringContainsString('raw-data-123', $content);
    }
}
