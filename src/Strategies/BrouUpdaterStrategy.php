<?php

namespace Solcre\UpdatersStrategies\Strategies;

use Solcre\UpdatersStrategies\SourceUpdaterInterface;

class BrouUpdaterStrategy implements SourceUpdaterInterface {

    private $source_url;
    private $source_id;

    function __construct($source_url, $source_id) {
        $this->source_url = $source_url;
        $this->source_id = $source_id;
    }

    public function update() {

        $site = $this->getSiteString($this->source_url);
        return $this->parser($site);
    }

    private function getSiteString($url, Array $opts = array()) {
        $rs = \curl_init();
        curl_setopt($rs, CURLOPT_URL, $url);
        curl_setopt($rs, CURLOPT_RETURNTRANSFER, true);
        if (count($opts)) {
            foreach ($opts as $opt) {
                curl_setopt($rs, $opt['option'], $opt['value']);
            }
        }
        $page = curl_exec($rs);
        curl_close($rs);
        return $page;
    }

    private function parser($site) {
        $data = array();
        if (preg_match_all('/(<td class="buy">)(.*)(<\/td>)/', $site, $compras) == 15 && preg_match_all('/(<td class="sale">)(.*)(<\/td>)/', $site, $ventas) == 15) {

            $data = array(
                "arg_buy" => $compras[2][3],
                "arg_sell" => $ventas[2][3],
                "br_sell" => $ventas[2][4],
                "br_buy" => $compras[2][4],
                "eur_sell" => $ventas[2][2],
                "eur_buy" => $compras[2][2],
                "us_sell" => $ventas[2][0],
                "us_buy" => $compras[2][0],
                "source" => $this->source_id
            );
        }

        return $data;
    }

}
