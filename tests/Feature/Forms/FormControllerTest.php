<?php

namespace Tests\Feature\Forms;

use Tests\TestCase;

class FormControllerTest extends TestCase
{
    /**
     * Test the / route
     * @return void
     */
    public function test_root_route()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test the /buy route
     * @return void
     */
    public function test_shopping_route()
    {
        $response = $this->get('/buy');
        $response->assertStatus(200);
        $response->assertSee('50% de descuento - Solo por $9.999');
    }

    /**
     * Test the /preview route
     * @return void
     */
    public function test_preview_route()
    {
        // test redirection with empty fields
        $data = [
            'name' => '',
            'email' => '',
            'mobile' => ''
        ];

        $response = $this->post('/preview', $data);
        $response->assertStatus(302);

        // test success render
        $data = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'mobile' => '3153820851'
        ];
        $responseOk = $this->post('/preview', $data);
        $responseOk->assertStatus(200);
        $responseOk->assertSee('Resumen de la orden');
    }
}
