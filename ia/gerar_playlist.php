<?php

function gerarPlaylistIA($humor, $quantidade, $regiao, $artista, $descricao) {
    $apiKey = "yourkey";

    if (!$apiKey) {
        return json_encode(["erro" => "API key não configurada."]);
    }

    $prompt = "
Gere uma playlist com $quantidade músicas.
Humor: $humor
Região: $regiao
Artista preferido: $artista
Descrição extra: $descricao

Responda **somente** um JSON no formato:
[
  {\"titulo\": \"...\", \"artista\": \"...\", \"link\": \"link de pesquisa do youtube\"}
]
";

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey";

    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($response, true);

    $texto = $json["candidates"][0]["content"]["parts"][0]["text"] ?? "";

    return $texto;
}
