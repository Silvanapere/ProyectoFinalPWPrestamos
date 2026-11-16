<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogosPrestamosSeeder extends Seeder
{
    public function run(): void
    {
        // Datos de configuración; se pueden volver a ejecutar sin duplicarlos.
        foreach ([
            ['nombre' => 'Cédula', 'mascara_validacion' => '000-0000000-0', 'longitud' => 13],
            ['nombre' => 'RNC', 'mascara_validacion' => '000-00000-0', 'longitud' => 11],
            ['nombre' => 'Pasaporte', 'mascara_validacion' => 'ALFANUMERICO', 'longitud' => null],
        ] as $dato) {
            DB::table('tipos_documento')->updateOrInsert(['nombre' => $dato['nombre']], $dato);
        }

        foreach (['Dominicana', 'Haitiana', 'Estadounidense', 'Venezolana'] as $nombre) {
            DB::table('nacionalidades')->updateOrInsert(['nombre' => $nombre], ['nombre' => $nombre]);
        }

        foreach ([
            ['nombre' => 'Personal', 'descripcion' => 'Préstamo de uso personal'],
            ['nombre' => 'Vehículo', 'descripcion' => 'Financiamiento de vehículo'],
            ['nombre' => 'Comercial', 'descripcion' => 'Financiamiento comercial'],
        ] as $dato) {
            DB::table('tipos_prestamo')->updateOrInsert(['nombre' => $dato['nombre']], $dato);
        }

        // Para plazos basados en meses, los días sirven como referencia de tasa.
        // Las fechas reales se calcularán con el calendario, no sumando siempre 30 días.
        foreach ([
            ['nombre' => 'Diario', 'dias_referencia' => 1],
            ['nombre' => 'Interdiario', 'dias_referencia' => 2],
            ['nombre' => 'Semanal', 'dias_referencia' => 7],
            ['nombre' => 'Intersemanal', 'dias_referencia' => 14],
            ['nombre' => 'Quincenal', 'dias_referencia' => 15],
            ['nombre' => 'Mensual', 'dias_referencia' => 30],
            ['nombre' => 'Trimestral', 'dias_referencia' => 90],
            ['nombre' => 'Semestral', 'dias_referencia' => 180],
            ['nombre' => 'Anual', 'dias_referencia' => 360],
        ] as $dato) {
            DB::table('tipos_plazo')->updateOrInsert(['nombre' => $dato['nombre']], $dato);
        }

        foreach ([
            ['nombre' => 'Saldo absoluto', 'descripcion' => 'Capital e interés iguales en cada cuota'],
            ['nombre' => 'Saldo insoluto', 'descripcion' => 'Cuota constante y cálculo de interés sobre saldo pendiente'],
            ['nombre' => 'Saldo fijo', 'descripcion' => 'Se genera una cuota a la vez'],
        ] as $dato) {
            DB::table('tipos_amortizacion')->updateOrInsert(['nombre' => $dato['nombre']], $dato);
        }
    }
}
