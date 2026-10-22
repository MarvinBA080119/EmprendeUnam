<?php

namespace App\Console\Commands;

use App\Mail\CorreoPrueba;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Diagnóstico del correo: muestra la configuración que está usando la
 * aplicación (sin la contraseña), revisa la cola y envía un correo de prueba
 * en el momento. Si falla, explica la causa más probable.
 *
 *   php artisan emprende:probar-correo destino@correo.com
 *   php artisan emprende:probar-correo destino@correo.com --cola   (prueba también el trabajador)
 */
class ProbarCorreo extends Command
{
    protected $signature = 'emprende:probar-correo {destino : Correo que recibirá la prueba} {--cola : Enviar por la cola, como los correos reales}';

    protected $description = 'Revisa la configuración de correo y la cola, y envía un correo de prueba';

    public function handle(): int
    {
        $destino = trim((string) $this->argument('destino'));

        if (filter_var($destino, FILTER_VALIDATE_EMAIL) === false) {
            $this->error("«{$destino}» no es un correo válido.");

            return self::FAILURE;
        }

        $this->mostrarConfiguracion();
        $this->revisarCola();

        $mailer = (string) config('mail.default');
        if (in_array($mailer, ['log', 'array'], true)) {
            $this->newLine();
            $this->error("MAIL_MAILER={$mailer}: los correos NO se envían, solo se ".($mailer === 'log' ? 'escriben en storage/logs/laravel.log' : 'guardan en memoria').'.');
            $this->line('Configura MAIL_MAILER=smtp y los datos del servidor en .env (ver README, «Correo»), y después:');
            $this->line('  php artisan config:clear   (o php artisan optimize)  y  sudo systemctl restart <servicio de la cola>');

            return self::FAILURE;
        }

        $correo = new CorreoPrueba((string) config('app.url'));

        if ($this->option('cola')) {
            Mail::to($destino)->queue($correo);
            $this->newLine();
            $this->info("Correo de prueba puesto en la cola para {$destino}.");
            $this->line('Si no llega en un minuto, el trabajador de la cola no está corriendo o falló:');
            $this->line('  sudo systemctl status <servicio de la cola>   ·   php artisan queue:failed');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->line("Enviando a {$destino}…");

        try {
            Mail::to($destino)->send($correo);
        } catch (Throwable $e) {
            $this->error('No se pudo enviar: '.strtok($e->getMessage(), "\n"));
            foreach ($this->pistas($e->getMessage()) as $pista) {
                $this->line('  · '.$pista);
            }

            return self::FAILURE;
        }

        $this->info('Enviado. Revisa la bandeja de entrada (y la de correo no deseado).');

        return self::SUCCESS;
    }

    private function mostrarConfiguracion(): void
    {
        $mailer = (string) config('mail.default');
        $smtp = (array) config('mail.mailers.smtp');
        $usuario = (string) ($smtp['username'] ?? '');
        $remitente = (string) config('mail.from.address');

        $this->table(['Parámetro', 'Valor'], [
            ['MAIL_MAILER', $mailer],
            ['MAIL_HOST', (string) ($smtp['host'] ?? '')],
            ['MAIL_PORT', (string) ($smtp['port'] ?? '')],
            ['MAIL_SCHEME', (string) ($smtp['scheme'] ?? '') ?: '(automático: STARTTLS si el servidor lo ofrece)'],
            ['MAIL_USERNAME', $usuario === '' ? '(vacío)' : $usuario],
            ['MAIL_PASSWORD', ($smtp['password'] ?? '') ? '(definida)' : '(vacía)'],
            ['MAIL_FROM_ADDRESS', $remitente],
            ['QUEUE_CONNECTION', (string) config('queue.default')],
        ]);

        if (str_ends_with($remitente, '.invalid')) {
            $this->warn('MAIL_FROM_ADDRESS es provisional (.invalid): usa una dirección real autorizada por el servidor de correo.');
        }

        if ($mailer === 'smtp' && (int) ($smtp['port'] ?? 0) === 465 && ($smtp['scheme'] ?? '') !== 'smtps') {
            $this->warn('El puerto 465 usa TLS directo: pon MAIL_SCHEME=smtps.');
        }
    }

    private function revisarCola(): void
    {
        if (config('queue.default') !== 'database' || ! Schema::hasTable('jobs')) {
            return;
        }

        $pendientes = DB::table('jobs')->count();
        $masAntiguo = DB::table('jobs')->min('available_at');
        $fallidos = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;

        $this->line("Cola: {$pendientes} correo(s) pendiente(s), {$fallidos} fallido(s).");

        if ($masAntiguo !== null && now()->getTimestamp() - (int) $masAntiguo > 120) {
            $this->warn('Hay correos esperando hace más de 2 minutos: el trabajador de la cola no está corriendo.');
            $this->line('  sudo systemctl status <servicio de la cola>   ·   journalctl -u <servicio de la cola> -n 50');
        }

        if ($fallidos > 0) {
            $ultimo = (string) DB::table('failed_jobs')->orderByDesc('id')->value('exception');
            $this->warn('Último error de la cola: '.strtok($ultimo, "\n"));
            $this->line('  Al corregirlo: php artisan queue:retry all');
        }
    }

    /** @return list<string> */
    private function pistas(string $mensaje): array
    {
        $m = mb_strtolower($mensaje);

        return match (true) {
            str_contains($m, '535') || str_contains($m, 'authentication') || str_contains($m, 'username and password') => [
                'El servidor rechazó el usuario o la contraseña.',
                'Gmail y Microsoft 365 piden una «contraseña de aplicación» (con verificación en dos pasos), no la contraseña normal.',
            ],
            str_contains($m, 'timed out') || str_contains($m, 'connection could not be established')
                || str_contains($m, 'connection refused') || str_contains($m, 'network is unreachable') => [
                    'No hubo conexión con el servidor de correo: revisa MAIL_HOST y MAIL_PORT.',
                    'Prueba la red desde el servidor: curl -v telnet://MAIL_HOST:MAIL_PORT  (Ctrl+C para salir).',
                    'Si el correo sale desde PHP-FPM con SELinux: sudo setsebool -P httpd_can_sendmail on',
                    'Algunas redes bloquean el puerto 25: usa 587 (STARTTLS) o 465 (smtps).',
                ],
            str_contains($m, 'ssl') || str_contains($m, 'tls') || str_contains($m, 'certificate') || str_contains($m, 'crypto') => [
                'Falló el cifrado: 587 va con STARTTLS (MAIL_SCHEME vacío o smtp) y 465 con TLS directo (MAIL_SCHEME=smtps).',
            ],
            str_contains($m, '550') || str_contains($m, '553') || str_contains($m, '554') || str_contains($m, 'sender') => [
                'El servidor no acepta al remitente: MAIL_FROM_ADDRESS debe ser la cuenta con la que se inicia sesión o un alias autorizado.',
            ],
            default => ['Revisa storage/logs/laravel.log y los datos MAIL_* de .env.'],
        };
    }
}
