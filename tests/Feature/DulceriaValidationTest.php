<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Dulce;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DulceriaValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /* ---------------------------------------------------------
     * 1. VALIDACIONES AL AGREGAR CATEGORÍA
     * --------------------------------------------------------- */

    public function test_agregar_categoria_valida_exitosamente(): void
    {
        $response = $this->actingAs($this->user)->post('/categorias', [
            'nombre'      => 'Chocolates Finos',
            'descripcion' => 'Deliciosos chocolates',
        ]);

        $response->assertRedirect(route('categorias.index'));
        $this->assertDatabaseHas('categorias', [
            'nombre' => 'Chocolates Finos',
        ]);
    }

    public function test_agregar_categoria_con_campo_vacio_falla(): void
    {
        $response = $this->actingAs($this->user)->post('/categorias', [
            'nombre' => '',
        ]);

        $response->assertSessionHasErrors([
            'nombre' => 'El nombre de la categoría es obligatorio.',
        ]);
    }

    public function test_agregar_categoria_con_numeros_falla(): void
    {
        $response = $this->actingAs($this->user)->post('/categorias', [
            'nombre' => 'Gomitas 123',
        ]);

        $response->assertSessionHasErrors([
            'nombre' => 'El nombre solo debe contener letras, no se permiten números.',
        ]);
    }

    /* ---------------------------------------------------------
     * 2. VALIDACIONES AL AGREGAR DULCES
     * --------------------------------------------------------- */

    public function test_agregar_dulce_valido_exitosamente(): void
    {
        $categoria = Categoria::create(['nombre' => 'Caramelos']);

        $response = $this->actingAs($this->user)->post('/dulces', [
            'nombre'       => 'Caramelo de Miel',
            'categoria_id' => $categoria->id,
            'precio'       => 5.50,
            'stock'        => 20,
            'descripcion'  => 'Caramelo macizo sabor miel',
        ]);

        $response->assertRedirect(route('dulces.index'));
        $this->assertDatabaseHas('dulces', [
            'nombre'       => 'Caramelo de Miel',
            'categoria_id' => $categoria->id,
            'precio'       => 5.50,
            'stock'        => 20,
        ]);
    }

    public function test_agregar_dulce_con_campos_vacios_falla(): void
    {
        $response = $this->actingAs($this->user)->post('/dulces', [
            'nombre'       => '',
            'categoria_id' => '',
            'precio'       => '',
            'stock'        => '',
        ]);

        $response->assertSessionHasErrors(['nombre', 'categoria_id', 'precio', 'stock']);
    }

    public function test_agregar_dulce_con_precio_o_stock_invalidos_falla(): void
    {
        $categoria = Categoria::create(['nombre' => 'Paletas']);

        $response = $this->actingAs($this->user)->post('/dulces', [
            'nombre'       => 'Paleta Payaso',
            'categoria_id' => $categoria->id,
            'precio'       => -2.00,
            'stock'        => -5,
        ]);

        $response->assertSessionHasErrors(['precio', 'stock']);
    }

    /* ---------------------------------------------------------
     * 3. VALIDACIONES AL EDITAR DULCE
     * --------------------------------------------------------- */

    public function test_editar_dulce_valido_exitosamente(): void
    {
        $categoria = Categoria::create(['nombre' => 'Chocolates']);
        $dulce = Dulce::create([
            'nombre'       => 'Carlos V',
            'categoria_id' => $categoria->id,
            'precio'       => 12.00,
            'stock'        => 15,
        ]);

        $response = $this->actingAs($this->user)->put("/dulces/{$dulce->id}", [
            'nombre'       => 'Carlos V Suizo',
            'categoria_id' => $categoria->id,
            'precio'       => 15.00,
            'stock'        => 30,
        ]);

        $response->assertRedirect(route('dulces.index'));
        $this->assertDatabaseHas('dulces', [
            'id'     => $dulce->id,
            'nombre' => 'Carlos V Suizo',
            'precio' => 15.00,
            'stock'  => 30,
        ]);
    }

    public function test_editar_dulce_con_campos_vacios_falla(): void
    {
        $categoria = Categoria::create(['nombre' => 'Gomitas']);
        $dulce = Dulce::create([
            'nombre'       => 'Panditas',
            'categoria_id' => $categoria->id,
            'precio'       => 10.00,
            'stock'        => 50,
        ]);

        $response = $this->actingAs($this->user)->put("/dulces/{$dulce->id}", [
            'nombre'       => '',
            'categoria_id' => '',
            'precio'       => '',
            'stock'        => '',
        ]);

        $response->assertSessionHasErrors(['nombre', 'categoria_id', 'precio', 'stock']);
    }

    /* ---------------------------------------------------------
     * 4. VALIDACIONES AL EDITAR PERFIL
     * --------------------------------------------------------- */

    public function test_editar_perfil_valido_exitosamente(): void
    {
        $response = $this->actingAs($this->user)->patch('/profile', [
            'name'  => 'Juan Pérez',
            'email' => 'juanperez@example.com',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $this->assertDatabaseHas('users', [
            'id'    => $this->user->id,
            'name'  => 'Juan Pérez',
            'email' => 'juanperez@example.com',
        ]);
    }

    public function test_editar_perfil_con_campo_vacio_falla(): void
    {
        $response = $this->actingAs($this->user)->patch('/profile', [
            'name'  => '',
            'email' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_editar_perfil_con_numeros_en_nombre_falla(): void
    {
        $response = $this->actingAs($this->user)->patch('/profile', [
            'name'  => 'Juan123',
            'email' => 'juan@example.com',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'El nombre solo debe contener letras, no se permiten números.',
        ]);
    }
}
