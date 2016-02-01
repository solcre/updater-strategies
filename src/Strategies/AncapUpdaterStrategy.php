<?php

namespace Solcre\UpdatersStrategies\Strategies;

use Solcre\UpdatersStrategies\SourceUpdaterInterface;

class AncapUpdaterStrategy implements SourceUpdaterInterface {

    private $source_url;
    private $source_id;

    function __construct($source_url, $source_id) {
        $this->source_url = $source_url;
        $this->source_id = $source_id;
    }

    public function update() {

        $file1 = $this->source_url;
        $lines = file($file1);
        $data = array();

        foreach ($lines as $line) {

            $fuel_type = $this->get_string_between($line);
            $value = substr($line, strpos($line, "=") + 1);
            if (!empty($fuel_type)) {
                $data[$fuel_type] = trim($value);
            }
        }
        $data["supergas"] = $this->getGasPrice();
        $data["source"] = $this->source_id;

        return $data;
    }

    private function get_string_between($string) {
        $find = preg_match('~&(.*?)=~', $string, $output);
        if ($find == 1) {
            return trim($output[1]);
        }
    }

    private function getGasPrice() {
        $site = $this->getSiteString("http://www.ducsa.com.uy/Combustibles/default.aspx?IDCAT=48&IDPROD=238");
        $string_strip = preg_replace('/\s+/', '', $site);
        preg_match_all('/<!--Precio-->\$(.*?)\+/s', $string_strip, $matches);

        if (!empty($matches[1][0])) {
            return $matches[1][0];
        } else {
            return null;
        }
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

}
