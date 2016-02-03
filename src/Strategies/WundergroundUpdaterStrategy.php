<?php

namespace Solcre\UpdatersStrategies\Strategies;

use Solcre\UpdatersStrategies\SourceUpdaterInterface;

class WundergroundUpdaterStrategy implements SourceUpdaterInterface {

    private $source_url;
    private $source_id;

    function __construct($source_url, $source_id) {
        $this->source_url = $source_url;
        $this->source_id = $source_id;
    }

    public function update() {

        $json_string = $this->getSiteString();
        return $this->parser($json_string);
    }

    private function getSiteString() {
        return file_get_contents($this->source_url);
    }

    private function getCodigo($condicion) {

        $condiciones = array(
            "PARTLY SUNNY",
            "SCATTERED THUNDERSTORMS",
            "SHOWERS",
            "SCATTERED SHOWERS",
            "RAIN AND SNOW",
            "OVERCAST",
            "LIGHT SNOW",
            "FREEZING DRIZZLE",
            "CHANCE OF RAIN",
            "SUNNY",
            "CLEAR",
            "MOSTLY SUNNY",
            "PARTLY CLOUDY",
            "MOSTLY CLOUDY",
            "CHANCE OF STORM",
            "RAIN",
            "CHANCE OF SNOW",
            "CLOUDY",
            "MIST",
            "STORM",
            "THUNDERSTORM",
            "CHANCE OF TSTORM",
            "CHANCE OF A THUNDERSTORM",
            "SLEET",
            "SNOW",
            "ICY",
            "DUST",
            "FOG",
            "SMOKE",
            "HAZE",
            "FLURRIES",
            "LIGHT RAIN",
            "SNOW SHOWERS",
            "ICE/SNOW",
            "WINDY",
            "SCATTERED SNOW SHOWERS"
        );
        $codigos = array_flip($condiciones);
        return $codigos[strtoupper($condicion)];
    }

    private function parser($json_string) {

        $parsed_json = json_decode($json_string, true);
        $condicion = $parsed_json['current_observation']['weather'];
        $ciudad = $parsed_json['location']['city'];
        $temperatura = $parsed_json['current_observation']['temp_c'];
        $minima = $parsed_json['forecast']['simpleforecast']['forecastday'][0]['low']['celsius'];
        $maxima = $parsed_json['forecast']['simpleforecast']['forecastday'][0]['high']['celsius'];
        $humedad = $parsed_json['current_observation']['relative_humidity'];

        $codigo = $this->getCodigo($condicion);
        $actual = array(
            'condicion' => $condicion,
            'temperatura' => $temperatura,
            'humedad' => $humedad,
            'codigo_condicion' => $codigo,
            'minima' => $minima,
            'maxima' => $maxima
        );
        $pronostico = array();
        $largo = count($parsed_json['forecast']['simpleforecast']['forecastday']);
        for ($i = 1; $i < $largo; $i++) {
            $condicion = $parsed_json['forecast']['simpleforecast']['forecastday'][$i]['conditions'];
            $minima = $parsed_json['forecast']['simpleforecast']['forecastday'][$i]['low']['celsius'];
            $maxima = $parsed_json['forecast']['simpleforecast']['forecastday'][$i]['high']['celsius'];
            $codigo = $this->getCodigo($condicion);
            $pronostico[] = array(
                'condicion' => $condicion,
                'codigo_condicion' => $codigo,
                'minima' => $minima,
                'maxima' => $maxima
            );
        }
        $respuesta = array(
            'actual' => $actual,
            'pronostico' => $pronostico,
        );

        $data = array(
            "city" => $ciudad,
            "data" => $respuesta,
            "source" => $this->source_id
        );

        return $data;
    }

}
