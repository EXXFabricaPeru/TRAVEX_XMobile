<?php

namespace api\traits;

trait Respuestas {

    /**
     * Códigos de errores:
     * Código: 200 => error generico no se envio el error o se desconoce el error
     * Código: 100 => código satisfactorio, no se envio el codigo o simplemente se realizo la tarea
     * Código: 60 => no se encontro resultados
     * Código: 300 => error generico al actualizar datos
     * Código 201 => usuario/contraseña incorrectos
     */
    private function correcto($datos = [], $mensaje = 'OK', $codigo = 200) {
        return array(
            "estado" => $codigo,
            "respuesta" => $datos,
            "mensaje" => $mensaje
        );
    }

    private function error($mensaje = 'Error', $codigo = 100) {
        return array(
            "estado" => $codigo,
            "mensaje" => $mensaje
        );
    }

}
