<?php

namespace Tests\Concerns;

use App\Enums\EstatusMensaje;
use App\Models\Comunicacion;
use App\Models\Mensaje;
use App\Models\Persona;
use App\Models\TemaNews;
use App\Models\TipoComunicacion;
use Illuminate\Support\Facades\DB;

/** Mensajes del consultorio y suscriptores para las pruebas. */
trait CreaContenido
{
    protected function crearMensaje(array $datos = []): Mensaje
    {
        return Mensaje::create(array_merge([
            'mens_remitente' => 'Lectora Curiosa',
            'mens_lugar' => 'Puebla',
            'mens_correo' => 'lectora'.uniqid().'@example.com',
            'mens_asunto' => '¿Cómo registro mi marca?',
            'mens_descripcion' => 'Quisiera saber los pasos para registrar la marca de mi emprendimiento.',
            'mens_status' => EstatusMensaje::PENDIENTE,
        ], $datos));
    }

    /**
     * Suscriptor del newsletter (persona + correo + tema en una transacción,
     * como exige el trigger diferido tr_newsletter_completa).
     *
     * @param  'activo'|'sin_confirmar'|'baja'  $estado
     */
    protected function crearSuscriptor(string $estado = 'activo', array $datos = []): Persona
    {
        return DB::transaction(function () use ($estado, $datos) {
            $alta = now()->subDays(3);

            $persona = Persona::create([
                'pers_nombre' => $datos['nombre'] ?? 'Sofía',
                'pers_primer_apellido' => $datos['apellido'] ?? 'Ramírez',
                'pers_subs_newsletter' => $estado !== 'baja',
                'pers_acepta_aviso' => true,
                'pers_fecha_suscripcion' => $alta,
                'pers_token_confirmacion' => bin2hex(random_bytes(32)),
                'pers_fecha_confirmacion' => $estado === 'sin_confirmar' ? null : $alta->copy()->addHour(),
                'pers_fecha_baja' => $estado === 'baja' ? now() : null,
            ]);

            Comunicacion::create([
                'pers_id_persona' => $persona->pers_id_persona,
                'comu_id_tipo_comunicacion' => TipoComunicacion::correo()->value('comu_id_tipo_comunicacion'),
                'comu_valor_comunicacion' => $datos['correo'] ?? 'suscriptor'.uniqid().'@example.com',
                'comu_etiqueta' => 'Newsletter',
                'comu_activo' => true,
            ]);

            TemaNews::create([
                'pers_id_persona' => $persona->pers_id_persona,
                'tene_tema_interes' => $datos['tema'] ?? 'Comunicación',
            ]);

            return $persona;
        });
    }
}
