<?php

declare(strict_types=1);

namespace App\Core\Servicos;

final class ServicoGeocodificacao
{
    public function localizar(?string $cep, ?string $cidade, ?string $estado): ?array
    {
        $query = trim(implode(', ', array_filter([
            preg_replace('/\D/', '', (string) $cep) ?: null,
            $cidade,
            $estado,
            'Brasil',
        ])));

        if ($query === '') {
            return null;
        }

        $url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' . rawurlencode($query);
        $context = stream_context_create([
            'http' => [
                'timeout' => 2,
                'header' => "User-Agent: EstagioMatch/1.0\r\n",
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);
        if (!is_array($data) || empty($data[0]['lat']) || empty($data[0]['lon'])) {
            return null;
        }

        return [
            'latitude' => round((float) $data[0]['lat'], 7),
            'longitude' => round((float) $data[0]['lon'], 7),
        ];
    }
}
