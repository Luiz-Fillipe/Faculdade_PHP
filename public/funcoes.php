<?php

function mesPorExtenso($numero)
{
    $meses = [
        1 => 'janeiro',
        2 => 'fevereiro',
        3 => 'março',
        4 => 'abril',
        5 => 'maio',
        6 => 'junho',
        7 => 'julho',
        8 => 'agosto',
        9 => 'setembro',
        10 => 'outubro',
        11 => 'novembro',
        12 => 'dezembro'
    ];

    return $meses[(int) $numero] ?? '';
}

function dataCompleta($timestamp)
{
    $data = strtotime($timestamp);

    return date('j', $data)
        . ' de '
        . mesPorExtenso(date('n', $data))
        . ' de '
        . date('Y', $data)
        . ' às '
        . date('H:i', $data);
}

function resumoCurto($texto, $limite = 170)
{
    $texto = trim($texto ?? '');

    if (mb_strlen($texto) <= $limite) {
        return $texto;
    }

    $corte = mb_substr($texto, 0, $limite);
    $ultimoEspaco = mb_strrpos($corte, ' ');

    if ($ultimoEspaco !== false) {
        $corte = mb_substr($corte, 0, $ultimoEspaco);
    }

    return $corte . '…';
}

function linkValido($url)
{
    $url = trim($url ?? '');

    if ($url === '') {
        return null;
    }

    if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
        return $url;
    }

    return null;
}

function mensagemDeErro($codigo)
{
    $mensagens = [
        'categoria' => 'Essa categoria não foi encontrada.',
        'conteudo' => 'Essa publicação não foi encontrada.',
        'usuario' => 'Esse usuário não foi encontrado.',
        'evento' => 'Esse evento não foi encontrado.'
    ];

    return $mensagens[$codigo] ?? '';
}
