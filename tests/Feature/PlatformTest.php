<?php

use App\Models\Customer;
use App\Models\FileAsset;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\UploadedFile;

function staff(string $role, ?Customer $customer = null): User
{
    test()->seed(RolePermissionSeeder::class);

    return User::factory()->create([
        'role_id' => Role::query()->where('key', $role)->firstOrFail()->id,
        'customer_id' => $customer?->id,
        'password' => 'local-only-change-me',
    ]);
}

it('renders the public site', function () {
    $this->get('/')->assertOk()->assertSee('Surework')->assertSee('Your business. Reimagined.');
    $this->get('/services')->assertOk();
    $this->get('/services/business-websites')->assertOk()->assertSee('Business websites');
    $this->get('/contact')->assertOk();
    $this->get('/privacy')->assertOk()->assertSee('not legal advice');
    $this->get('/sitemap.xml')->assertOk();
    $this->get('/robots.txt')->assertOk();
});

it('records a quote enquiry and rejects an empty one', function () {
    $this->post('/quote', [])->assertSessionHasErrors(['name', 'email']);

    $this->post('/quote', [
        'name' => 'Ada North',
        'email' => 'ada@example.test',
        'service' => 'Business websites',
        'description' => 'A site for a local trade.',
        'marketing_consent' => false,
    ])->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseHas('leads', [
        'email' => 'ada@example.test',
        'source' => 'quote',
        'status' => 'new',
    ]);
});

it('keeps anonymous visitors out of the staff area', function () {
    $this->get('/admin')->assertRedirect('/login');
    $this->get('/admin/leads')->assertRedirect('/login');
    $this->get('/portal')->assertRedirect('/login');
});

it('stops a client reading staff data or another customer', function () {
    $own = Customer::query()->create(['name' => 'Own Co', 'email' => 'own@example.test']);
    $other = Customer::query()->create(['name' => 'Other Co', 'email' => 'other@example.test']);
    $client = staff('client', $own);
    $invoice = Invoice::query()->create([
        'customer_id' => $other->id,
        'number' => 'INV-2026-0099',
        'title' => 'Other invoice',
        'status' => 'sent',
        'currency' => 'GBP',
        'tax_rate_bps' => 0,
    ]);

    $this->actingAs($client)->get('/admin')->assertForbidden();
    $this->actingAs($client)->get('/admin/leads')->assertForbidden();
    $this->actingAs($client)->get('/admin/invoices/'.$invoice->id)->assertForbidden();
    $this->actingAs($client)->get('/portal')->assertOk()->assertDontSee('Other invoice');
});

it('filters leads and refuses an illegal status jump', function () {
    $owner = staff('owner');
    Lead::query()->create([
        'name' => 'Ada North',
        'email' => 'ada@example.test',
        'source' => 'quote',
        'status' => 'new',
    ]);
    Lead::query()->create([
        'name' => 'Ben South',
        'email' => 'ben@example.test',
        'source' => 'consultation',
        'status' => 'contacted',
    ]);

    $this->actingAs($owner)->get('/admin/leads?search=Ada&status=new')->assertOk()->assertSee('Ada North')->assertDontSee('Ben South');

    $lead = Lead::query()->where('email', 'ada@example.test')->firstOrFail();
    $this->actingAs($owner)->post('/admin/leads/'.$lead->id.'/status', ['status' => 'won'])->assertSessionHasErrors('status');
    expect($lead->fresh()->status)->toBe('new');
});

it('denies a marketing user the lead list', function () {
    $marketing = staff('marketing');

    $this->actingAs($marketing)->get('/admin/leads')->assertForbidden();
    $this->actingAs($marketing)->get('/admin')->assertOk();
});

it('limits a developer to assigned projects', function () {
    $developer = staff('developer');
    $customer = Customer::query()->create(['name' => 'Client', 'email' => 'client@example.test']);
    $project = Project::query()->create([
        'customer_id' => $customer->id,
        'title' => 'Unassigned build',
        'status' => 'planning',
    ]);

    $this->actingAs($developer)->get('/admin/projects/'.$project->id)->assertForbidden();
});

it('rejects a disallowed upload', function () {
    $owner = staff('owner');

    $this->actingAs($owner)->post('/admin/files', [
        'file' => UploadedFile::fake()->create('notes.exe', 12, 'application/octet-stream'),
    ])->assertSessionHasErrors('file');

    expect(FileAsset::query()->count())->toBe(0);
});

it('names the product Surework', function () {
    expect(config('brand.name'))->toBe('Surework')
        ->and(config('brand.domain'))->toBe('surework.co.uk');
});

it('does not create the local owner outside the local environment', function () {
    $this->seed(Database\Seeders\DatabaseSeeder::class);

    expect(User::query()->where('email', 'owner@surework.test')->exists())->toBeFalse()
        ->and(\App\Models\Service::query()->where('starting_price_pence', '>', 0)->exists())->toBeFalse();
});
