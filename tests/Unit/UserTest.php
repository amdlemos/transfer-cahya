<?php

namespace Tests\Unit;

use App\Models\User;
use App\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_common_user_with_valid_cpf()
    {
        $user = User::factory()->common()->create();

        $this->assertEquals(UserType::Common, $user->type);
        $this->assertMatchesRegularExpression('/^\d{11}$/', $user->document);
    }

    #[Test]
    public function it_creates_a_merchant_user_with_valid_cnpj()
    {
        $user = User::factory()->merchant()->create();

        $this->assertEquals(UserType::Merchant, $user->type);
        $this->assertMatchesRegularExpression('/^\d{14}$/', $user->document);
    }

    #[Test]
    public function cpf_must_be_unique_for_common_users()
    {
        $cpf = '12345678901';

        User::factory()->common()->create(['document' => $cpf]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->common()->create(['document' => $cpf]);
    }

    #[Test]
    public function cnpj_must_be_unique_for_merchant_users()
    {
        $cnpj = '12345678000199';

        User::factory()->merchant()->create(['document' => $cnpj]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->merchant()->create(['document' => $cnpj]);
    }

    #[Test]
    public function it_casts_enum_correctly()
    {
        $user = User::factory()->common()->create();

        $this->assertInstanceOf(UserType::class, $user->type);
        $this->assertTrue($user->type === UserType::Common);
    }

    #[Test]
    public function email_must_be_unique()
    {
        $email = 'example@example.com';

        User::factory()->create(['email' => $email]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->create(['email' => $email]);
    }

    #[Test]
    public function it_requires_name()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['name' => null]);
    }

    #[Test]
    public function it_requires_email()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['email' => null]);
    }

    #[Test]
    public function document_cannot_be_null()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['document' => null]);
    }
}
