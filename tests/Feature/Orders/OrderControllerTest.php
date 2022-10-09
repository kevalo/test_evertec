<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\User;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    /**
     * Test the /orders route
     *
     * @return void
     */
    public function test_index()
    {
        // test redirection to login page
        $response = $this->get('/orders');
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // test redirection to dashboard page
        $user = User::factory()->create();
        $this->actingAs($user)->get('/orders')->assertStatus(200);
    }

    /**
     * Test the store method: POST:/orders
     * @return void
     */
    public function test_store()
    {
        // test redirection with empty data
        $data = [
            'name' => '',
            'email' => '',
            'mobile' => ''
        ];

        $response = $this->post('/orders', $data);
        $response->assertStatus(302);
        $response->assertRedirect('/buy');

        // test redirection to place to play
        $data = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'mobile' => '3153820851'
        ];

        $response = $this->post('/orders', $data);
        $response->assertStatus(302);
        $response->assertRedirectContains('https://checkout-co.placetopay.dev/session/');
    }

    /**
     * Test the show method: get:/orders/{id}
     * @return void
     */
    public function test_show()
    {
        // test redirection with fake data
        $response = $this->get('/orders/' . fake()->uuid());
        $response->assertStatus(302);
        $response->assertRedirect('/buy');

        // create an order
        $data = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'mobile' => '3153820851'
        ];

        $response = $this->post('/orders', $data);
        $response->assertStatus(302);
        $response->assertRedirectContains('https://checkout-co.placetopay.dev/session/');

        $order = order::orderBy('id', 'desc')->limit(1)->first();

        $response = $this->get('/orders/' . $order->id);
        $response->assertStatus(200);
        $response->assertSee('Orden # ' . $order->id);
    }

    /**
     * Test the newPayment method, get:/new_payment/{id}
     * @return void
     */
    public function test_new_payment()
    {
        // test error redirection
        $response = $this->get('/new_payment/' . fake()->numberBetween(700,800));
        $response->assertStatus(302);
        $response->assertRedirect('/buy');

        // test successfully redirection
        $order = order::orderBy('id', 'desc')->limit(1)->first();
        $response = $this->get('/new_payment/' . $order->id);
        $response->assertStatus(302);
        $response->assertRedirectContains('https://checkout-co.placetopay.dev/session/');
    }
}
