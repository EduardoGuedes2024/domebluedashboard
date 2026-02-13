<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ImageProxyController extends Controller
{
    public function produto(string $site, string $ref)
    {
        $site = strtolower($site);

        $url = match ($site) {
            'syssa'   => "https://syssaoficial.com.br/imgitens/{$ref}_0.webp",
            default   => "https://www.amissima.com.br/imgitens/{$ref}_0.webp",
        };

        $cacheKey = "img_produto_{$site}_{$ref}";

        $jpgBytes = Cache::remember($cacheKey, now()->addDays(7), function () use ($url) {
            $resp = Http::timeout(15)->get($url);
            if (!$resp->ok()) return null;

            $bin = $resp->body();

            $img = @imagecreatefromstring($bin);
            if (!$img) return null;

            // reduz um pouco pra não estourar memória
            $maxW = 380;
            $w = imagesx($img);
            $h = imagesy($img);

            if ($w > $maxW) {
                $newW = $maxW;
                $newH = (int) round($h * ($newW / $w));
                $tmp = imagecreatetruecolor($newW, $newH);
                imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
                imagedestroy($img);
                $img = $tmp;
            }

            ob_start();
            imagejpeg($img, null, 85);
            $jpg = ob_get_clean();

            imagedestroy($img);

            return $jpg;
        });

        if (!$jpgBytes) {
            return response('', 204);
        }

        return response($jpgBytes, 200)
            ->header('Content-Type', 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=604800');
    }
}
