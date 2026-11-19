<?php

namespace App\Services;

use App\Models\Cliente;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class DocumentosClientes
{
    public function ejecutar(Closure $operacion, array $archivos = []): mixed
    {
        $rutasNuevas = [];
        $rutasAnteriores = [];
        try {
            foreach ($archivos as $campo => $archivo) {
                if ($archivo) {
                    $this->preparar($archivo, $rutasNuevas, $campo);
                }
            }
            $resultado = DB::transaction(function () use ($operacion, &$rutasNuevas, &$rutasAnteriores) {
                $resultado = $operacion($rutasNuevas, $rutasAnteriores);
                foreach (array_unique($rutasAnteriores) as $ruta) {
                    $this->pendiente($ruta);
                }

                return $resultado;
            }, 3);
        } catch (Throwable $error) {
            foreach ($rutasNuevas as $ruta) {
                $this->eliminarORegistrar($ruta);
            }
            throw $error;
        }
        foreach (array_unique(array_merge($rutasNuevas, $rutasAnteriores)) as $ruta) {
            $this->eliminarORegistrar($ruta);
        }

        return $resultado;
    }

    public function sustituir(Cliente $persona, UploadedFile $archivo, array &$rutasNuevas, array &$rutasAnteriores): void
    {
        $ruta = $rutasNuevas[spl_object_id($archivo)];
        if ($persona->documento_archivo) {
            $rutasAnteriores[] = $persona->documento_archivo;
        }
        $persona->update(['documento_archivo' => $ruta]);
    }

    private function preparar(UploadedFile $archivo, array &$rutasNuevas, string $campo): void
    {
        $ruta = 'documentos_clientes/'.Str::uuid().'.'.$archivo->extension();
        $this->pendiente($ruta);
        $rutasNuevas[spl_object_id($archivo)] = $ruta;
        try {
            $guardada = Storage::disk('local')->putFileAs('documentos_clientes', $archivo, basename($ruta));
            if ($guardada !== $ruta || ! Storage::disk('local')->exists($ruta)) {
                throw new \RuntimeException('El almacenamiento no confirmó el archivo.');
            }
        } catch (Throwable $error) {
            report($error);
            throw ValidationException::withMessages([
                $campo => 'No se pudo guardar el documento'.($campo === 'documento_garante' ? ' del garante' : ' del cliente').'. Se conservaron los datos y el documento anteriores; vuelve a intentarlo.',
            ]);
        }
    }

    public function reintentar(): int
    {
        foreach (DB::table('clientes_documentos_limpieza')->where('updated_at', '<=', now()->subHour())->pluck('ruta') as $ruta) {
            $this->eliminarORegistrar($ruta);
        }

        return DB::table('clientes_documentos_limpieza')->count();
    }

    private function pendiente(string $ruta): void
    {
        DB::table('clientes_documentos_limpieza')->updateOrInsert(['ruta' => $ruta], ['updated_at' => now()]);
    }

    private function eliminarORegistrar(string $ruta): void
    {
        try {
            if (Cliente::where('documento_archivo', $ruta)->exists()) {
                DB::table('clientes_documentos_limpieza')->where('ruta', $ruta)->delete();

                return;
            }
            $disco = Storage::disk('local');
            if ($disco->exists($ruta) && (! $disco->delete($ruta) || $disco->exists($ruta))) {
                throw new \RuntimeException('No se pudo eliminar el documento sustituido.');
            }
            DB::table('clientes_documentos_limpieza')->where('ruta', $ruta)->delete();
        } catch (Throwable $error) {
            $this->pendiente($ruta);
            Log::warning('Documento pendiente de limpieza.', ['ruta' => $ruta, 'error' => $error->getMessage()]);
        }
    }
}
