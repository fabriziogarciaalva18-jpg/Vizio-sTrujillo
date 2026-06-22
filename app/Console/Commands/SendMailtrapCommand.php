<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapClient;
use Mailtrap\Mime\MailtrapEmail;
use Symfony\Component\Mime\Address;

class SendMailtrapCommand extends Command
{
    protected $signature = 'mailtrap:send';
    protected $description = 'Enviar correo de prueba con Mailtrap SDK';

    public function handle()
    {
        try {
            $email = (new MailtrapEmail())
                ->from(new Address('hello@demomailtrap.co', 'Mailtrap Test'))
                ->to(new Address('fabriziogarciaalva18@gmail.com'))
                ->subject('¡Prueba con Mailtrap SDK!')
                ->category('Integration Test')
                ->text('¡Correo enviado exitosamente con Mailtrap SDK en Laravel!');

            $response = MailtrapClient::initSendingEmails(
                apiKey: env('MAILTRAP_API_KEY')
            )->send($email);

            $this->info('✅ ¡Correo enviado!');
            $this->info(ResponseHelper::toArray($response));
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}
