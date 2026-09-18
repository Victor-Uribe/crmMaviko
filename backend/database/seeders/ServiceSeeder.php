<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'code' => 'pagina_web',
                'name' => 'Página web',
            ],
            [
                'code' => 'tienda_online',
                'name' => 'Tienda en línea',
            ],
            [
                'code' => 'catalogo_digital',
                'name' => 'Catálogo digital',
            ],
            [
                'code' => 'menu_qr',
                'name' => 'Menú QR',
            ],
            [
                'code' => 'sistema_administrativo',
                'name' => 'Sistema administrativo',
            ],
            [
                'code' => 'automatizacion_ia',
                'name' => 'Automatización con IA',
            ],
            [
                'code' => 'hosting',
                'name' => 'Hosting',
            ],
            [
                'code' => 'correo_empresarial',
                'name' => 'Correo empresarial',
            ],
            [
                'code' => 'soporte_tecnico',
                'name' => 'Soporte tecnológico',
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                [
                    'code' => $service['code'],
                ],
                $service
            );
        }
    }
}
