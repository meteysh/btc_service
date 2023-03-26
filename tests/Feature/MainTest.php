<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MainTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $id = 1;
        //заходим на страницу по id юзера
        $response = $this->get('/user/' . $id);
        $response->assertStatus(200);
        $response->assertSee('id="transfer_user" class="balance">111.11100000000', false);
        $response->assertSee('class="balance_site">0.00000000000', false);
        $response->assertSee('name="balance_partner" value="0.00000000000"', false);
        $response->assertSee('name="balance_cashback" value="0.00000000000"', false);

        //списываем деньги с юзера и проверяем  распределение по счетам
        $this->post('/from_user', ['id' => $id, 'amount' => "0.00000005",]);
        $response = $this->get('/user/' . $id);
        $response->assertSee('id="transfer_user" class="balance">111.11099995000', false);
        $response->assertSee('class="balance_site">0.00000000043', false);
        $response->assertSee('name="balance_partner" value="0.00000000003"', false);
        $response->assertSee('name="balance_cashback" value="0.00000000005"', false);

        //партнер забирает деньги и проверяем что у него по нулям, у остальных счетов все на месте
        $this->post('/to_partner');
        $response = $this->get('/user/' . $id);
        $response->assertSee('id="transfer_user" class="balance">111.11099995000', false);
        $response->assertSee('class="balance_site">0.00000000043', false);
        $response->assertSee('name="balance_partner" value="0.0000000000', false);
        $response->assertSee('name="balance_cashback" value="0.00000000005"', false);
        //Пытаемся списать деньги с кэшбэка на пользовательский счет и проверяем,
        //что не достаточно сатоши по условиям логики
        $response = $this->post('/to_cashback', ['id' => $id]);
        $response->assertSessionHasErrors(['error']);
        //добавляем еще денег юзеру
        $this->post('/from_user', ['id' => $id, 'amount' => "7"]);
        //проверяем что все добавилось правильно
        $response = $this->get('/user/' . $id);
        $response->assertSee('id="transfer_user" class="balance">104.11099995000', false);
        $response->assertSee('class="balance_site">0.05950000043', false);
        $response->assertSee('name="balance_partner" value="0.00350000000"', false);
        $response->assertSee('name="balance_cashback" value="0.00700000005"', false);
        //Пытаемся списать деньги с кэшбэка на пользовательский счет и проверяем, что на этот раз
        //достаточно сатоши по условиям логики
        $this->post('/to_cashback', ['id' => $id]);
        $response = $this->get('/user/' . $id);
        $response->assertSee('id="transfer_user" class="balance">104.11799995005', false);
        $response->assertSee('class="balance_site">0.05950000043', false);
        $response->assertSee('name="balance_partner" value="0.00350000000"', false);
        $response->assertSee('name="balance_cashback" value="0.00000000000"', false);
    }
}
