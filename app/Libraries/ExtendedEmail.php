<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;

class ExtendedEmail extends Email
{
    /**
     * Overriding SMTPConnect to allow for custom SSL context options.
     * This is useful for local development on XAMPP where SSL certs are missing.
     */
    protected function SMTPConnect()
    {
        if (is_resource($this->SMTPConnect)) {
            return true;
        }

        $ssl = '';

        // Connection to port 465 should use implicit TLS (without STARTTLS)
        // as per RFC 8314.
        if ($this->SMTPPort === 465) {
            $ssl = 'tls://';
        }
        // But if $SMTPCrypto is set to `ssl`, SSL can be used.
        if ($this->SMTPCrypto === 'ssl') {
            $ssl = 'ssl://';
        }

        // --- CUSTOM START ---
        // Create a custom stream context to bypass SSL verification if setting is disabled
        $verifySsl = (get_setting('email_smtp_verify_ssl', '1') === '1');
        
        $contextOptions = [];
        if (!$verifySsl || ENVIRONMENT === 'development') {
            $contextOptions = [
                'ssl' => [
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
        }
        $context = stream_context_create($contextOptions);

        $this->SMTPConnect = stream_socket_client(
            $ssl . $this->SMTPHost . ':' . $this->SMTPPort,
            $errno,
            $errstr,
            $this->SMTPTimeout,
            STREAM_CLIENT_CONNECT,
            $context
        );
        // --- CUSTOM END ---

        if (! is_resource($this->SMTPConnect)) {
            $this->setErrorMessage(lang('Email.SMTPError', [$errno . ' ' . $errstr]));

            return false;
        }

        stream_set_timeout($this->SMTPConnect, $this->SMTPTimeout);
        $this->setErrorMessage($this->getSMTPData());

        if ($this->SMTPCrypto === 'tls') {
            $this->sendCommand('hello');
            $this->sendCommand('starttls');
            
            // --- CUSTOM START ---
            // We need to apply the crypto but we might need to handle the context again?
            // Actually, stream_socket_enable_crypto doesn't take a context, 
            // but it should respect the context of the stream created by stream_socket_client.
            // However, PHP's stream_socket_enable_crypto is known to be picky.
            // Under CodeIgniter 4, we follow their way but we've already set the context to skip verification for the stream.
            // --- CUSTOM END ---
            
            $crypto = stream_socket_enable_crypto(
                $this->SMTPConnect,
                true,
                STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT
                | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT
                | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
                | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT,
            );

            if ($crypto !== true) {
                $this->setErrorMessage(lang('Email.SMTPError', [$this->getSMTPData()]));

                return false;
            }
        }

        return $this->sendCommand('hello');
    }
}
