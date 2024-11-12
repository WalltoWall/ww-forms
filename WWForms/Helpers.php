<?php

namespace WWForms;

class Helpers {
  public static function getManifest()
    {
        $manifest_path = plugin_dir_path(__FILE__) . 'assets/.vite/manifest.json';

        if (! file_exists($manifest_path)) {
            return;
        }

        $manifest = json_decode(file_get_contents($manifest_path), true);

        return $manifest;
    }
}