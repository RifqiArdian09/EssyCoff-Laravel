<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\CafeTable;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TableValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]);

        Product::create([
            'name' => 'Test Product',
            'price' => 10000,
            'stock' => 10,
            'category_id' => $category->id
        ]);
    }

    /** @test */
    public function it_rejects_order_with_invalid_table_code()
    {
        $orderData = [
            'customer_name' => 'Test Customer',
            'table' => 'INVALID-CODE',
            'items' => [
                [
                    'id' => 1,
                    'quantity' => 1
                ]
            ],
            'total' => 10000
        ];

        $response = $this->postJson(route('customer.order'), $orderData);

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'error_type' => 'table_not_found'
                ])
                ->assertJsonFragment([
                    'message' => 'Meja dengan kode "INVALID-CODE" tidak ditemukan. Silakan hubungi staff untuk bantuan.'
                ]);
    }

    /** @test */
    public function it_redirects_to_table_not_found_page_when_accessing_invalid_table_via_url()
    {
        $response = $this->get(route('customer.table', ['code' => 'INVALID-CODE']));

        $response->assertRedirect(route('customer.table.not-found', ['code' => 'INVALID-CODE']));
    }

    /** @test */
    public function it_shows_table_not_found_page_with_suggestions()
    {
        // Create some available tables
        CafeTable::create([
            'name' => 'Meja A1',
            'code' => 'TBL-A001',
            'status' => 'available',
            'seats' => 4
        ]);

        CafeTable::create([
            'name' => 'Meja B1',
            'code' => 'TBL-B001',
            'status' => 'available',
            'seats' => 2
        ]);

        $response = $this->get(route('customer.table.not-found', ['code' => 'INVALID-CODE']));

        $response->assertStatus(200)
                ->assertSee('Meja Tidak Ditemukan')
                ->assertSee('INVALID-CODE')
                ->assertSee('Meja yang Tersedia')
                ->assertSee('Meja A1')
                ->assertSee('TBL-A001');
    }

    /** @test */
    public function it_accepts_order_with_valid_table_code()
    {
        // Create a valid table
        $table = CafeTable::create([
            'name' => 'Test Table',
            'code' => 'TBL-TEST1',
            'status' => 'available',
            'seats' => 4
        ]);

        $orderData = [
            'customer_name' => 'Test Customer',
            'table' => 'TBL-TEST1',
            'items' => [
                [
                    'id' => 1,
                    'quantity' => 1
                ]
            ],
            'total' => 10000
        ];

        $response = $this->postJson(route('customer.order'), $orderData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ])
                ->assertJsonStructure([
                    'table_info' => [
                        'id',
                        'name',
                        'code'
                    ]
                ]);
    }

    /** @test */
    public function it_accepts_order_without_table_code()
    {
        $orderData = [
            'customer_name' => 'Walk-in Customer',
            'items' => [
                [
                    'id' => 1,
                    'quantity' => 1
                ]
            ],
            'total' => 10000
        ];

        $response = $this->postJson(route('customer.order'), $orderData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'table_info' => null
                ]);
    }
}
